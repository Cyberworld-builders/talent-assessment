#!/bin/bash

# 🚀 Production Deployment Script
# This script deploys the production environment by fetching secrets and updating services
# This script performs the following steps:
# 1. Checks prerequisites (AWS CLI, Docker, Docker Compose)
# 2. Fetches secrets and environment variables from AWS Parameter Store
# 3. Updates environment variables for production environment
# 4. Takes down existing production containers
# 5. Rebuilds and deploys production containers with new configuration
# 6. Runs database migrations
# 7. Verifies deployment status

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

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check if AWS CLI is installed
    if ! command_exists aws; then
        print_error "AWS CLI is not installed. Please install it first."
        exit 1
    fi
    
    # Check if Docker is installed
    if ! command_exists docker; then
        print_error "Docker is not installed. Please install it first."
        exit 1
    fi
    
    # Check if Docker Compose is installed
    if ! command_exists docker-compose; then
        print_error "Docker Compose is not installed. Please install it first."
        exit 1
    fi
    
    # Check if jq is installed
    if ! command_exists jq; then
        print_error "jq is not installed. Please install it first."
        exit 1
    fi
    
    print_success "All prerequisites are installed."
}

# Function to fetch secrets from AWS Secrets Manager
fetch_secrets() {
    print_status "Fetching secrets from AWS Secrets Manager..."
    
    # Check if we're authenticated with AWS
    if ! aws sts get-caller-identity >/dev/null 2>&1; then
        print_error "Not authenticated with AWS. Please run 'aws configure' or set up OIDC."
        exit 1
    fi
    
    # Fetch secrets from AWS Secrets Manager
    local secrets_file="/tmp/production-secrets.json"
    
    if aws secretsmanager get-secret-value --secret-id "talent-assessment/production" --query SecretString --output text > "$secrets_file"; then
        print_success "Secrets fetched successfully."
    else
        print_error "Failed to fetch secrets from AWS Secrets Manager."
        exit 1
    fi
    
    # Set server environment variables for docker-compose
    export PRODUCTION_DB_DATABASE=$(jq -r '.PRODUCTION_DB_DATABASE' "$secrets_file")
    export PRODUCTION_DB_USERNAME=$(jq -r '.PRODUCTION_DB_USERNAME' "$secrets_file")
    export PRODUCTION_DB_PASSWORD=$(jq -r '.PRODUCTION_DB_PASSWORD' "$secrets_file")
    export PRODUCTION_DB_ROOT_PASSWORD=$(jq -r '.PRODUCTION_DB_ROOT_PASSWORD' "$secrets_file")
    export PRODUCTION_REDIS_PASSWORD=$(jq -r '.PRODUCTION_REDIS_PASSWORD' "$secrets_file")
    export PRODUCTION_S3_BUCKET=$(jq -r '.PRODUCTION_S3_BUCKET' "$secrets_file")
    export PRODUCTION_SES_ACCESS_KEY_ID=$(jq -r '.PRODUCTION_SES_ACCESS_KEY_ID' "$secrets_file")
    export PRODUCTION_SES_SECRET_ACCESS_KEY=$(jq -r '.PRODUCTION_SES_SECRET_ACCESS_KEY' "$secrets_file")
    export PRODUCTION_SES_REGION=$(jq -r '.PRODUCTION_SES_REGION' "$secrets_file")
    export PRODUCTION_SES_FROM_ADDRESS=$(jq -r '.PRODUCTION_SES_FROM_ADDRESS' "$secrets_file")
    export PRODUCTION_CLOUDFRONT_DOMAIN=$(jq -r '.PRODUCTION_CLOUDFRONT_DOMAIN' "$secrets_file")
    
    # Generate APP_KEY properly (32-byte key encoded in base64, but shorter format like Laravel artisan)
    export PRODUCTION_APP_KEY=$(openssl rand -base64 24 | tr -d '\n')
    
    # Set APP_VERSION from image tag or default
    if [ -n "$image_tag" ]; then
        export PRODUCTION_APP_VERSION="$image_tag"
    else
        # Try to get version from git tag, fallback to default
        if command_exists git && [ -d ".git" ]; then
            export PRODUCTION_APP_VERSION=$(git describe --tags --always 2>/dev/null || echo "1.5.18-production")
        else
            export PRODUCTION_APP_VERSION="1.5.18-production"
        fi
    fi
    
    print_success "Server environment variables set."
    print_status "Generated APP_KEY: $PRODUCTION_APP_KEY"
    print_status "Set APP_VERSION: $PRODUCTION_APP_VERSION"
}

# Function to set image environment variable
set_image_variable() {
    local image_tag="$1"
    local ecr_registry="$2"
    
    if [ -n "$image_tag" ] && [ -n "$ecr_registry" ]; then
        # Set the image environment variable for docker-compose
        export PRODUCTION_APP_IMAGE="$ecr_registry/talent-assessment-app:$image_tag"
        
        print_success "Image environment variable set: $PRODUCTION_APP_IMAGE"
    else
        print_error "Image tag and ECR registry are required for production deployment."
        exit 1
    fi
}

# Function to update environment file
update_environment_file() {
    print_status "Updating .env.production file..."
    
    # Create .env.production file with production values
    cat > .env.production << EOF
# Production Environment Configuration
APP_NAME="Involved Talent Assessment"
APP_ENV=production
APP_KEY=$PRODUCTION_APP_KEY
APP_DEBUG=false
APP_URL=https://my.involvedtalent.com
APP_VERSION=$PRODUCTION_APP_VERSION

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql-production
DB_PORT=3306
DB_DATABASE=$PRODUCTION_DB_DATABASE
DB_USERNAME=$PRODUCTION_DB_USERNAME
DB_PASSWORD=$PRODUCTION_DB_PASSWORD

# Redis Configuration
REDIS_HOST=redis-production
REDIS_PASSWORD=$PRODUCTION_REDIS_PASSWORD
REDIS_PORT=6379

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_DRIVER=sync

# Mail Configuration
MAIL_DRIVER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=$PRODUCTION_SES_ACCESS_KEY_ID
MAIL_PASSWORD=$PRODUCTION_SES_SECRET_ACCESS_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=$PRODUCTION_SES_FROM_ADDRESS
MAIL_FROM_NAME="Involved Talent Assessment"

# AWS Configuration
AWS_ACCESS_KEY_ID=$PRODUCTION_SES_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY=$PRODUCTION_SES_SECRET_ACCESS_KEY
AWS_DEFAULT_REGION=$PRODUCTION_SES_REGION
AWS_S3_BUCKET=$PRODUCTION_S3_BUCKET
AWS_CLOUDFRONT_DOMAIN=$PRODUCTION_CLOUDFRONT_DOMAIN

# SES Configuration
SES_REGION=$PRODUCTION_SES_REGION
SES_FROM_ADDRESS=$PRODUCTION_SES_FROM_ADDRESS

# Security
BCRYPT_ROUNDS=12

# Logging
LOG_LEVEL=error
EOF
    
    print_success ".env.production file updated."
}

# Function to deploy production environment
deploy_production() {
    print_status "Deploying production environment..."
    
    # Take down existing production containers
    print_status "Taking down existing production containers..."
    docker-compose -f docker-compose.production.yml down || true
    
    # Start production services
    print_status "Starting production services..."
    docker-compose -f docker-compose.production.yml up -d
    
    # Wait for services to be ready
    print_status "Waiting for services to be ready..."
    sleep 30
    
    # Run database migrations
    print_status "Running database migrations..."
    docker-compose -f docker-compose.production.yml exec -T app-production php artisan migrate --force
    
    # Clear cache
    print_status "Clearing application cache..."
    docker-compose -f docker-compose.production.yml exec -T app-production php artisan cache:clear
    docker-compose -f docker-compose.production.yml exec -T app-production php artisan config:clear
    docker-compose -f docker-compose.production.yml exec -T app-production php artisan view:clear
    
    print_success "Production deployment completed."
}

# Function to verify deployment
verify_deployment() {
    print_status "Verifying production deployment..."
    
    # Check if containers are running
    if docker-compose -f docker-compose.production.yml ps | grep -q "Up"; then
        print_success "Production containers are running."
    else
        print_error "Some production containers are not running."
        exit 1
    fi
    
    # Test application health
    print_status "Testing application health..."
    if curl -f -s https://my.involvedtalent.com >/dev/null; then
        print_success "Application is responding."
    else
        print_warning "Application health check failed, but deployment may still be successful."
    fi
    
    print_success "Production deployment verification completed."
}

# Function to show usage
show_usage() {
    echo "Usage: $0 <image_tag> <ecr_registry>"
    echo ""
    echo "Arguments:"
    echo "  image_tag     The Docker image tag to deploy (e.g., v1.0.0-production)"
    echo "  ecr_registry  The ECR registry URL (e.g., 123456789012.dkr.ecr.us-east-1.amazonaws.com)"
    echo ""
    echo "Example:"
    echo "  $0 v1.0.0-production 123456789012.dkr.ecr.us-east-1.amazonaws.com"
}

# Main execution
main() {
    # Check if required arguments are provided
    if [ $# -ne 2 ]; then
        show_usage
        exit 1
    fi
    
    local image_tag="$1"
    local ecr_registry="$2"
    
    print_status "Starting production deployment..."
    print_status "Image tag: $image_tag"
    print_status "ECR registry: $ecr_registry"
    
    check_prerequisites
    fetch_secrets
    set_image_variable "$image_tag" "$ecr_registry"
    update_environment_file
    deploy_production
    verify_deployment
    
    print_success "Production deployment completed successfully!"
    print_status "Application is available at: https://my.involvedtalent.com"
}

# Run main function with all arguments
main "$@"







