#!/bin/bash

# 🚀 Development Deployment Script
# This script deploys the development environment with version management
# This script performs the following steps:
# 1. Sets up development environment variables including APP_VERSION
# 2. Takes down existing development containers
# 3. Rebuilds and deploys development containers with new configuration
# 4. Runs database migrations
# 5. Verifies deployment status

set -e  # Exit on any error

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

# Function to set development environment variables
set_dev_environment() {
    print_status "Setting development environment variables..."
    
    # Set APP_VERSION from git tag or default
    if command_exists git && [ -d ".git" ]; then
        export DEV_APP_VERSION=$(git describe --tags --always 2>/dev/null || echo "1.5.18-dev")
    else
        export DEV_APP_VERSION="1.5.18-dev"
    fi
    
    # Generate APP_KEY for development
    export DEV_APP_KEY=$(openssl rand -base64 24 | tr -d '\n')
    
    print_success "Development environment variables set."
    print_status "Set APP_VERSION: $DEV_APP_VERSION"
    print_status "Generated APP_KEY: $DEV_APP_KEY"
}

# Function to update development environment file
update_dev_environment_file() {
    print_status "Updating .env.dev file for development..."
    
    # Create .env.dev file with development values
    cat > .env.dev << EOF
# Development Environment Configuration
APP_NAME="Involved Talent Assessment"
APP_ENV=local
APP_KEY=$DEV_APP_KEY
APP_DEBUG=true
APP_URL=http://localhost:8001
APP_VERSION=$DEV_APP_VERSION

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=talent_assessment
DB_USERNAME=talent_user
DB_PASSWORD=talent_password

# Redis Configuration
REDIS_HOST=redis
REDIS_PASSWORD=
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

# Mail Configuration (for development)
MAIL_DRIVER=log
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=dev@involvedtalent.com
MAIL_FROM_NAME="Involved Talent Assessment (Dev)"

# AWS Configuration (for development)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_S3_BUCKET=

# Security
BCRYPT_ROUNDS=10

# Logging
LOG_LEVEL=debug
EOF
    
    print_success ".env.dev file updated for development."
}

# Function to deploy development environment
deploy_dev() {
    print_status "Deploying development environment..."
    
    # Take down existing development containers
    print_status "Taking down existing development containers..."
    docker-compose down || true
    
    # Start development services
    print_status "Starting development services..."
    docker-compose up -d --build
    
    # Wait for services to be ready
    print_status "Waiting for services to be ready..."
    sleep 30
    
    # Run database migrations
    print_status "Running database migrations..."
    docker-compose exec app php artisan migrate --force
    
    # Clear cache
    print_status "Clearing application cache..."
    docker-compose exec app php artisan cache:clear
    docker-compose exec app php artisan config:clear
    docker-compose exec app php artisan route:clear
    docker-compose exec app php artisan view:clear
    
    print_success "Development deployment completed."
}

# Function to verify deployment
verify_dev_deployment() {
    print_status "Verifying development deployment..."
    
    # Check if containers are running
    if docker-compose ps | grep -q "Up"; then
        print_success "Development containers are running."
    else
        print_error "Some development containers are not running."
        exit 1
    fi
    
    # Test application health
    print_status "Testing application health..."
    if curl -f -s http://localhost:8001 >/dev/null; then
        print_success "Application is responding."
    else
        print_warning "Application health check failed, but deployment may still be successful."
    fi
    
    # Test version display
    print_status "Testing version display..."
    if docker-compose exec app php -r "echo 'App Version: ' . config('app.version') . PHP_EOL;"; then
        print_success "Version configuration is working."
    else
        print_warning "Version configuration test failed."
    fi
    
    print_success "Development deployment verification completed."
}

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -h, --help               Show this help message"
    echo ""
    echo "This script deploys the development environment with version management."
}

# Main execution
main() {
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
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
    
    print_status "Starting development deployment..."
    
    set_dev_environment
    update_dev_environment_file
    deploy_dev
    verify_dev_deployment
    
    print_success "Development deployment completed successfully!"
    print_status "Application is available at: http://localhost:8001"
    print_status "Version: $DEV_APP_VERSION"
}

# Run main function with all arguments
main "$@"
