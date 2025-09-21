#!/bin/bash

# 🧪 Local Staging Test Script
# This script allows you to test staging builds locally without deploying to the actual staging server
# It builds the application locally and runs it with staging configuration

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    if ! command_exists docker; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    if ! command_exists docker-compose; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    
    print_success "All prerequisites are met."
}

# Function to build local staging image
build_staging_image() {
    print_status "Building local staging image..."
    
    # Build the image with staging tag
    if docker build -f Dockerfile -t talent-assessment-app:local-staging .; then
        print_success "Local staging image built successfully."
    else
        print_error "Failed to build local staging image."
        exit 1
    fi
}

# Function to create local staging environment file
create_local_staging_env() {
    print_status "Creating local staging environment file..."
    
    # Create a local staging environment file based on .env.staging
    cat > .env.local-staging << EOF
# Local Staging Environment Configuration
APP_NAME="Talent Assessment (Local Staging)"
APP_ENV=staging
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=true
APP_URL=http://localhost:8003

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql-staging
DB_PORT=3306
DB_DATABASE=talent_assessment_staging_local
DB_USERNAME=talent_user_staging
DB_PASSWORD=local_staging_password

# Redis Configuration
REDIS_HOST=redis-staging
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=database
QUEUE_DRIVER=redis

# Mail Configuration (using log driver for local testing)
MAIL_DRIVER=log
MAIL_FROM_ADDRESS=test@localhost
MAIL_FROM_NAME="Local Staging Test"

# AWS Configuration (not used in local testing)
AWS_REGION=us-east-2
AWS_S3_BUCKET=local-test-bucket
AWS_CLOUDFRONT_DOMAIN=localhost

# Docker user mapping for development
USER_ID=1000
GROUP_ID=1000

# Local staging image
STAGING_APP_IMAGE=talent-assessment-app:local-staging
EOF
    
    print_success "Local staging environment file created."
}

# Function to start local staging services
start_staging_services() {
    print_status "Starting local staging services..."
    
    # Set the image environment variable
    export STAGING_APP_IMAGE=talent-assessment-app:local-staging
    
    # Start services using staging docker-compose file
    if docker-compose -f docker-compose.staging.yml up -d; then
        print_success "Local staging services started successfully."
    else
        print_error "Failed to start local staging services."
        exit 1
    fi
}

# Function to wait for services to be ready
wait_for_services() {
    print_status "Waiting for services to be ready..."
    
    # Wait for MySQL to be ready
    print_status "Waiting for MySQL to be ready..."
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if docker-compose -f docker-compose.staging.yml exec -T mysql-staging mysqladmin ping -h localhost -u root -p"local_staging_password" --silent; then
            print_success "MySQL is ready."
            break
        fi
        
        if [ $attempt -eq $max_attempts ]; then
            print_error "MySQL failed to start after $max_attempts attempts."
            exit 1
        fi
        
        print_status "Waiting for MySQL... (attempt $attempt/$max_attempts)"
        sleep 2
        attempt=$((attempt + 1))
    done
    
    # Wait for Redis to be ready
    print_status "Waiting for Redis to be ready..."
    attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if docker-compose -f docker-compose.staging.yml exec -T redis-staging redis-cli ping | grep -q "PONG"; then
            print_success "Redis is ready."
            break
        fi
        
        if [ $attempt -eq $max_attempts ]; then
            print_error "Redis failed to start after $max_attempts attempts."
            exit 1
        fi
        
        print_status "Waiting for Redis... (attempt $attempt/$max_attempts)"
        sleep 2
        attempt=$((attempt + 1))
    done
}

# Function to setup Laravel application
setup_laravel() {
    print_status "Setting up Laravel application..."
    
    # Generate application key
    print_status "Generating application key..."
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan key:generate
    
    # Run migrations
    print_status "Running database migrations..."
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan migrate --force
    
    # Seed database
    print_status "Seeding database..."
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan db:seed --force
    
    # Clear caches
    print_status "Clearing caches..."
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan cache:clear
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan config:clear
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan route:clear
    docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan view:clear
    
    print_success "Laravel application setup completed."
}

# Function to run tests
run_tests() {
    print_status "Running test suite..."
    
    if docker-compose -f docker-compose.staging.yml exec -T app-staging php -d memory_limit=512M vendor/bin/phpunit; then
        print_success "All tests passed."
    else
        print_warning "Some tests failed, but continuing with local staging setup."
    fi
}

# Function to show service status
show_status() {
    print_status "Service Status:"
    docker-compose -f docker-compose.staging.yml ps
    
    print_status "Application URLs:"
    echo "  - Application: http://localhost:8003"
    echo "  - MySQL: localhost:3306"
    echo "  - Redis: localhost:6379"
    
    print_status "Useful Commands:"
    echo "  - View logs: docker-compose -f docker-compose.staging.yml logs -f"
    echo "  - Access app container: docker-compose -f docker-compose.staging.yml exec app-staging bash"
    echo "  - Stop services: docker-compose -f docker-compose.staging.yml down"
    echo "  - Rebuild: docker-compose -f docker-compose.staging.yml up -d --build"
}

# Function to cleanup
cleanup() {
    print_status "Cleaning up..."
    
    # Remove local staging environment file
    if [ -f ".env.local-staging" ]; then
        rm .env.local-staging
        print_success "Local staging environment file cleaned up."
    fi
}

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --no-tests        Skip running tests"
    echo "  --no-build        Skip building image (use existing)"
    echo "  --cleanup         Clean up and exit"
    echo "  -h, --help        Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                # Full local staging setup with tests"
    echo "  $0 --no-tests     # Setup without running tests"
    echo "  $0 --cleanup      # Clean up and stop services"
}

# Main function
main() {
    local skip_tests=false
    local skip_build=false
    local cleanup_only=false
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --no-tests)
                skip_tests=true
                shift
                ;;
            --no-build)
                skip_build=true
                shift
                ;;
            --cleanup)
                cleanup_only=true
                shift
                ;;
            -h|--help)
                show_usage
                exit 0
                ;;
            *)
                print_error "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
    
    if [ "$cleanup_only" = true ]; then
        print_status "Cleaning up local staging environment..."
        docker-compose -f docker-compose.staging.yml down
        cleanup
        print_success "Cleanup completed."
        exit 0
    fi
    
    print_status "Starting local staging environment setup..."
    
    # Check prerequisites
    check_prerequisites
    
    # Build image if not skipping
    if [ "$skip_build" = false ]; then
        build_staging_image
    else
        print_warning "Skipping image build (using existing image)."
    fi
    
    # Create local staging environment
    create_local_staging_env
    
    # Start services
    start_staging_services
    
    # Wait for services
    wait_for_services
    
    # Setup Laravel
    setup_laravel
    
    # Run tests if not skipping
    if [ "$skip_tests" = false ]; then
        run_tests
    else
        print_warning "Skipping tests."
    fi
    
    # Show status
    show_status
    
    print_success "Local staging environment is ready!"
    print_status "You can now test your changes locally before deploying to actual staging."
}

# Run main function with all arguments
main "$@"
