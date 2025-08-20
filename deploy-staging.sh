#!/bin/bash

# 🚀 Staging Deployment Script
# This script deploys the staging environment by fetching secrets and updating services

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
        print_error "AWS CLI is not installed. Please install AWS CLI first."
        exit 1
    fi
    
    # Check if Docker is installed
    if ! command_exists docker; then
        print_error "Docker is not installed. Please install Docker first."
        exit 1
    fi
    
    # Check if Docker Compose is installed
    if ! command_exists docker-compose; then
        print_error "Docker Compose is not installed. Please install Docker Compose first."
        exit 1
    fi
    
    # Check if jq is installed
    if ! command_exists jq; then
        print_error "jq is not installed. Please install jq first."
        exit 1
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity >/dev/null 2>&1; then
        print_error "AWS credentials not configured. Please run 'aws configure' first."
        exit 1
    fi
    
    print_success "All prerequisites are met."
}

# Function to fetch secrets from AWS Secrets Manager
fetch_secrets() {
    print_status "Fetching secrets from AWS Secrets Manager..."
    
    local secret_id="talent-assessment-staging-secrets"
    local secrets_file="secrets.json"
    
    # Fetch secrets
    if aws secretsmanager get-secret-value --secret-id "$secret_id" --query SecretString --output text > "$secrets_file" 2>/dev/null; then
        print_success "Secrets fetched successfully."
    else
        print_error "Failed to fetch secrets from AWS Secrets Manager."
        exit 1
    fi
}

# Function to set server environment variables
set_server_environment() {
    print_status "Setting server environment variables..."
    
    local secrets_file="secrets.json"
    
    # Set server environment variables for docker-compose
    export STAGING_DB_DATABASE=$(jq -r '.STAGING_DB_DATABASE' "$secrets_file")
    export STAGING_DB_USERNAME=$(jq -r '.STAGING_DB_USERNAME' "$secrets_file")
    export STAGING_DB_PASSWORD=$(jq -r '.STAGING_DB_PASSWORD' "$secrets_file")
    export STAGING_DB_ROOT_PASSWORD=$(jq -r '.STAGING_DB_ROOT_PASSWORD' "$secrets_file")
    export STAGING_REDIS_PASSWORD=$(jq -r '.STAGING_REDIS_PASSWORD' "$secrets_file")
    export STAGING_S3_BUCKET=$(jq -r '.STAGING_S3_BUCKET' "$secrets_file")
    export STAGING_APP_KEY=$(jq -r '.STAGING_APP_KEY' "$secrets_file")
    
    # Generate APP_KEY if not exists
    if [ -z "$STAGING_APP_KEY" ] || [ "$STAGING_APP_KEY" = "null" ]; then
        export STAGING_APP_KEY="base64:$(openssl rand -base64 32)"
        print_warning "Generated new APP_KEY. Consider storing it in Secrets Manager."
    fi
    
    print_success "Server environment variables set."
}



# Function to set image environment variable
set_image_environment() {
    local image_tag="$1"
    local ecr_registry="$2"
    
    if [ -n "$image_tag" ] && [ -n "$ecr_registry" ]; then
        print_status "Setting image environment variable..."
        
        # Set the image environment variable for docker-compose
        export STAGING_APP_IMAGE="$ecr_registry/talent-assessment-app:$image_tag"
        
        print_success "Image environment variable set: $STAGING_APP_IMAGE"
    else
        print_warning "No image tag or ECR registry provided, using default image."
    fi
}

# Function to authenticate with ECR
authenticate_ecr() {
    local ecr_registry="$1"
    
    if [ -n "$ecr_registry" ]; then
        print_status "Authenticating with ECR..."
        
        if aws ecr get-login-password | docker login --username AWS --password-stdin "$ecr_registry"; then
            print_success "ECR authentication successful."
        else
            print_error "ECR authentication failed."
            exit 1
        fi
    else
        print_warning "No ECR registry provided, skipping authentication."
    fi
}

# Function to deploy staging services
deploy_services() {
    print_status "Deploying staging services..."
    
    # Pull new images
    if docker-compose -f docker-compose.staging.yml pull; then
        print_success "Images pulled successfully."
    else
        print_error "Failed to pull images."
        exit 1
    fi
    
    # Start services
    if docker-compose -f docker-compose.staging.yml up -d --force-recreate; then
        print_success "Services started successfully."
    else
        print_error "Failed to start services."
        exit 1
    fi
}

# Function to wait for services to be healthy
wait_for_services() {
    print_status "Waiting for services to be healthy..."
    
    # Wait for services to start
    sleep 30
    
    # Check service status
    print_status "Checking service status..."
    docker-compose -f docker-compose.staging.yml ps
    
    print_success "Services are running."
}

# Function to clean up Docker resources
cleanup_docker() {
    print_status "Cleaning up Docker resources..."
    
    # Remove unused containers, networks, images, and build cache
    if docker system prune -f; then
        print_success "Docker system cleanup completed."
    else
        print_warning "Docker system cleanup failed, continuing..."
    fi
    
    # Remove dangling images
    if docker image prune -f; then
        print_success "Docker image cleanup completed."
    else
        print_warning "Docker image cleanup failed, continuing..."
    fi
    
    # Check disk usage after cleanup
    local disk_usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
    print_status "Disk usage after cleanup: ${disk_usage}%"
    
    if [ "$disk_usage" -gt 85 ]; then
        print_warning "Disk usage is still high (${disk_usage}%). Consider manual cleanup or instance resize."
    fi
}

# Function to check application health
check_application_health() {
    print_status "Checking application health..."
    
    local max_attempts=30
    local attempt=1
    local health_url="https://talent-staging.cyberworldbuilders.dev"
    
    print_status "Waiting for application to be healthy at: $health_url"
    
    while [ $attempt -le $max_attempts ]; do
        print_status "Health check attempt $attempt/$max_attempts..."
        
        # Check if the application responds with HTTP 200
        if curl -f -s -o /dev/null -w "%{http_code}" "$health_url" | grep -q "200"; then
            print_success "Application is healthy! HTTP 200 received."
            return 0
        fi
        
        # Check if the application responds at all (even with error codes)
        if curl -f -s -o /dev/null "$health_url" 2>/dev/null; then
            local http_code=$(curl -f -s -o /dev/null -w "%{http_code}" "$health_url" 2>/dev/null)
            print_warning "Application responded with HTTP $http_code, but not 200. Continuing to check..."
        else
            print_status "Application not responding yet, waiting..."
        fi
        
        sleep 10
        attempt=$((attempt + 1))
    done
    
    print_error "Application health check failed after $max_attempts attempts."
    print_error "Application may not be working properly."
    
    # Show service status for debugging
    print_status "Current service status:"
    docker-compose -f docker-compose.staging.yml ps
    
    # Show recent logs for debugging
    print_status "Recent application logs:"
    docker-compose -f docker-compose.staging.yml logs --tail=50 app-staging
    
    return 1
}

# Function to clean up
cleanup() {
    print_status "Cleaning up temporary files..."
    
    # Remove secrets file
    if [ -f "secrets.json" ]; then
        rm secrets.json
        print_success "Temporary files cleaned up."
    fi
}

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -t, --tag IMAGE_TAG       Docker image tag to deploy"
    echo "  -r, --registry ECR_REGISTRY  ECR registry URL"
    echo "  -h, --help               Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                                    # Deploy with current image"
    echo "  $0 -t abc123 -r 123456789.dkr.ecr.us-east-1.amazonaws.com  # Deploy specific image"
}

# Main function
main() {
    local image_tag=""
    local ecr_registry=""
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -t|--tag)
                image_tag="$2"
                shift 2
                ;;
            -r|--registry)
                ecr_registry="$2"
                shift 2
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
    
    print_status "Starting staging deployment..."
    
    # Check prerequisites
    check_prerequisites
    
    # Fetch secrets
    fetch_secrets
    
    # Set server environment variables
    set_server_environment
    
    # Set image environment variable if image tag provided
    set_image_environment "$image_tag" "$ecr_registry"
    
    # Authenticate with ECR if registry provided
    authenticate_ecr "$ecr_registry"
    
    # Deploy services
    deploy_services
    
    # Wait for services to be healthy
    wait_for_services
    
    # Clean up Docker resources
    cleanup_docker

    # Check application health
    check_application_health
    
    # Clean up
    cleanup
    
    print_success "Staging deployment completed successfully!"
    print_success "Application URL: https://talent-staging.cyberworldbuilders.dev"
}

# Run main function with all arguments
main "$@"
