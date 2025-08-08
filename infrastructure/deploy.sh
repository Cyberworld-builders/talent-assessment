#!/bin/bash

# 🚀 Talent Assessment Infrastructure Deployment Script
# This script automates the deployment of the AWS infrastructure

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
    
    # Check if Terraform is installed
    if ! command_exists terraform; then
        print_error "Terraform is not installed. Please install Terraform first."
        exit 1
    fi
    
    # Check if AWS CLI is installed
    if ! command_exists aws; then
        print_error "AWS CLI is not installed. Please install AWS CLI first."
        exit 1
    fi
    
    # Check if SSH key exists
    if [ ! -f ~/.ssh/dev-key.pub ]; then
        print_warning "SSH key ~/.ssh/dev-key.pub not found."
        read -p "Do you want to create a new SSH key pair? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            print_status "Creating SSH key pair..."
            ssh-keygen -t rsa -b 4096 -f ~/.ssh/dev-key -N ""
            print_success "SSH key pair created successfully."
        else
            print_error "SSH key pair is required for deployment."
            exit 1
        fi
    fi
    
    # Check AWS credentials
    # Extract AWS profile from terraform.tfvars
    AWS_PROFILE=$(grep "^aws_profile" terraform.tfvars | cut -d'=' -f2 | cut -d'#' -f1 | tr -d ' "')
    if [ -z "$AWS_PROFILE" ]; then
        AWS_PROFILE="default"
    fi
    
    if ! aws sts get-caller-identity --profile "$AWS_PROFILE" >/dev/null 2>&1; then
        print_error "AWS credentials not configured for profile '$AWS_PROFILE'. Please run 'aws configure' or 'aws configure sso' first."
        exit 1
    fi
    
    # Export AWS profile for Terraform
    export AWS_PROFILE="$AWS_PROFILE"
    
    print_success "All prerequisites are met."
}

# Function to validate configuration
validate_config() {
    print_status "Validating configuration..."
    
    # Check if terraform.tfvars exists
    if [ ! -f terraform.tfvars ]; then
        print_error "terraform.tfvars file not found."
        exit 1
    fi
    
    # Check if main.tf exists
    if [ ! -f main.tf ]; then
        print_error "main.tf file not found."
        exit 1
    fi
    
    print_success "Configuration files are valid."
}

# Function to deploy infrastructure
deploy_infrastructure() {
    print_status "Starting infrastructure deployment..."
    
    # Initialize Terraform
    print_status "Initializing Terraform..."
    terraform init
    
    # Plan the deployment
    print_status "Planning deployment..."
    terraform plan -out=tfplan
    
    # Ask for confirmation
    echo
    print_warning "This will create the following resources:"
    echo "  - VPC with public subnet"
    echo "  - Internet Gateway"
    echo "  - Security Group (HTTP, HTTPS, SSH)"
    echo "  - EC2 Instance (t3.small)"
    echo "  - IAM Role and Instance Profile"
    echo "  - SSH Key Pair"
    echo
    read -p "Do you want to proceed with the deployment? (y/n): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Applying Terraform configuration..."
        terraform apply tfplan
        
        print_success "Infrastructure deployed successfully!"
        
        # Display outputs
        echo
        print_status "Deployment Summary:"
        terraform output deployment_summary
        
        echo
        print_status "Next Steps:"
        echo "1. Update your domain DNS to point to the public IP"
        echo "2. SSH into the instance to upload your Laravel application"
        echo "3. Configure SSL certificates for HTTPS"
        echo "4. Set up environment variables for production"
        
    else
        print_warning "Deployment cancelled."
        exit 0
    fi
}

# Function to destroy infrastructure
destroy_infrastructure() {
    print_status "Starting infrastructure destruction..."
    
    print_warning "This will permanently delete all resources and data."
    read -p "Are you sure you want to destroy the infrastructure? (y/n): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Destroying infrastructure..."
        terraform destroy -auto-approve
        print_success "Infrastructure destroyed successfully."
    else
        print_warning "Destruction cancelled."
        exit 0
    fi
}

# Function to show status
show_status() {
    print_status "Current infrastructure status:"
    terraform show
}

# Function to show outputs
show_outputs() {
    print_status "Infrastructure outputs:"
    terraform output
}

# Function to show help
show_help() {
    echo "🚀 Talent Assessment Infrastructure Deployment Script"
    echo
    echo "Usage: $0 [COMMAND]"
    echo
    echo "Commands:"
    echo "  deploy    Deploy the infrastructure (default)"
    echo "  destroy   Destroy the infrastructure"
    echo "  status    Show current infrastructure status"
    echo "  outputs   Show infrastructure outputs"
    echo "  help      Show this help message"
    echo
    echo "Examples:"
    echo "  $0              # Deploy infrastructure"
    echo "  $0 deploy       # Deploy infrastructure"
    echo "  $0 destroy      # Destroy infrastructure"
    echo "  $0 status       # Show status"
    echo "  $0 outputs      # Show outputs"
}

# Main script logic
main() {
    # Change to script directory
    cd "$(dirname "$0")"
    
    # Parse command line arguments
    case "${1:-deploy}" in
        "deploy")
            check_prerequisites
            validate_config
            deploy_infrastructure
            ;;
        "destroy")
            check_prerequisites
            validate_config
            destroy_infrastructure
            ;;
        "status")
            validate_config
            show_status
            ;;
        "outputs")
            validate_config
            show_outputs
            ;;
        "help"|"-h"|"--help")
            show_help
            ;;
        *)
            print_error "Unknown command: $1"
            show_help
            exit 1
            ;;
    esac
}

# Run main function with all arguments
main "$@"
