#!/bin/bash

# 🚀 GitHub OIDC Setup Script for Talent Assessment
# This script helps you configure GitHub repository variables for AWS OIDC authentication

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
    
    # Check if gh CLI is installed
    if ! command_exists gh; then
        print_warning "GitHub CLI (gh) is not installed. You'll need to manually set repository variables."
        print_warning "Install with: brew install gh (macOS) or visit: https://cli.github.com/"
    fi
    
    print_success "Prerequisites check completed."
}

# Function to check if OIDC infrastructure exists
check_oidc_infrastructure() {
    print_status "Checking existing OIDC infrastructure..."
    
    cd infrastructure
    
    # Check if OIDC provider exists
    if terraform output -raw github_oidc_provider_arn >/dev/null 2>&1; then
        print_success "OIDC provider already exists"
        OIDC_EXISTS=true
    else
        print_status "OIDC provider not found, will create it"
        OIDC_EXISTS=false
    fi
    
    # Check if IAM role exists
    if terraform output -raw github_actions_role_arn >/dev/null 2>&1; then
        print_success "IAM role already exists"
        ROLE_EXISTS=true
    else
        print_status "IAM role not found, will create it"
        ROLE_EXISTS=false
    fi
    
    # Get EC2 instance details
    if terraform output -raw public_ip >/dev/null 2>&1; then
        EC2_HOST=$(terraform output -raw public_ip)
        print_success "EC2 instance found: $EC2_HOST"
    else
        print_error "EC2 instance not found. Please deploy the main infrastructure first."
        exit 1
    fi
    
    # Get S3 bucket name
    if terraform output -raw staging_s3_bucket_name >/dev/null 2>&1; then
        S3_BUCKET=$(terraform output -raw staging_s3_bucket_name)
        print_success "S3 bucket found: $S3_BUCKET"
    else
        print_error "S3 bucket not found. Please deploy the main infrastructure first."
        exit 1
    fi
    
    cd ..
}

# Function to deploy OIDC infrastructure (idempotent)
deploy_oidc_infrastructure() {
    print_status "Deploying OIDC infrastructure..."
    
    # Navigate to infrastructure directory
    cd infrastructure
    
    # Initialize Terraform if needed
    if [ ! -d ".terraform" ]; then
        print_status "Initializing Terraform..."
        terraform init
    fi
    
    # Check if we need to deploy anything
    if [ "$OIDC_EXISTS" = true ] && [ "$ROLE_EXISTS" = true ]; then
        print_success "OIDC infrastructure already exists, skipping deployment"
        cd ..
        return
    fi
    
    # Plan the deployment
    print_status "Planning OIDC infrastructure deployment..."
    terraform plan -target=aws_iam_openid_connect_provider.github -target=aws_iam_role.github_actions -out=oidc-plan
    
    # Ask for confirmation
    echo
    print_warning "This will create/update the following OIDC resources:"
    echo "  - GitHub OIDC Provider"
    echo "  - IAM Role for GitHub Actions"
    echo "  - IAM Policies for ECR, Secrets Manager, S3, and EC2 access"
    echo
    read -p "Do you want to proceed with the OIDC deployment? (y/n): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Applying OIDC infrastructure..."
        terraform apply oidc-plan
        
        # Clean up plan file
        rm -f oidc-plan
        
        print_success "OIDC infrastructure deployed successfully!"
    else
        print_warning "OIDC deployment cancelled."
        cd ..
        exit 0
    fi
    
    cd ..
}

# Function to get SSH key content
get_ssh_key_content() {
    if [ -f ~/.ssh/dev-key ]; then
        SSH_KEY_CONTENT=$(cat ~/.ssh/dev-key)
        print_success "SSH key found"
    else
        print_error "SSH key ~/.ssh/dev-key not found. Please create it first."
        exit 1
    fi
}

# Function to generate passwords
generate_passwords() {
    print_status "Generating passwords for staging environment..."
    
    # Generate DB root password
    DB_ROOT_PASSWORD="StagingDB$(date +%Y%m%d)!"
    
    # Generate Redis password
    REDIS_PASSWORD="StagingRedis$(date +%Y%m%d)!"
    
    print_success "Passwords generated"
}

# Function to setup GitHub repository variables and secrets
setup_github_variables() {
    print_status "Setting up GitHub repository variables and secrets..."
    
    # Get the role ARN
    cd infrastructure
    ROLE_ARN=$(terraform output -raw github_actions_role_arn)
    cd ..
    
    # Check if gh CLI is available
    if ! command_exists gh; then
        print_warning "GitHub CLI not available. Please manually set these repository variables and secrets:"
        echo
        print_status "Go to: https://github.com/Cyberworld-builders/talent-assessment/settings/variables/actions"
        echo
        print_status "Add these variables:"
        echo "  - AWS_ROLE_ARN: $ROLE_ARN"
        echo "  - AWS_REGION: us-east-2"
        echo
        print_status "Go to: https://github.com/Cyberworld-builders/talent-assessment/settings/secrets/actions"
        echo
        print_status "Add these secrets:"
        echo "  - EC2_HOST: $EC2_HOST"
        echo "  - EC2_USER: ubuntu"
        echo "  - EC2_SSH_KEY: [Your SSH private key content]"
        echo "  - STAGING_DB_ROOT_PASSWORD: $DB_ROOT_PASSWORD"
        echo "  - STAGING_REDIS_PASSWORD: $REDIS_PASSWORD"
        echo "  - STAGING_S3_BUCKET: $S3_BUCKET"
        echo
        return
    fi
    
    # Check if user is authenticated with GitHub
    if ! gh auth status >/dev/null 2>&1; then
        print_warning "Not authenticated with GitHub CLI. Please run: gh auth login"
        print_warning "Then manually set the repository variables and secrets as shown above."
        return
    fi
    
    # Set repository variables (idempotent - will update if exists)
    print_status "Setting AWS_ROLE_ARN variable..."
    gh variable set AWS_ROLE_ARN --body "$ROLE_ARN" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting AWS_REGION variable..."
    gh variable set AWS_REGION --body "us-east-2" --repo Cyberworld-builders/talent-assessment
    
    # Set repository secrets (idempotent - will update if exists)
    print_status "Setting EC2_HOST secret..."
    gh secret set EC2_HOST --body "$EC2_HOST" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting EC2_USER secret..."
    gh secret set EC2_USER --body "ubuntu" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting EC2_SSH_KEY secret..."
    gh secret set EC2_SSH_KEY --body "$SSH_KEY_CONTENT" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting STAGING_DB_ROOT_PASSWORD secret..."
    gh secret set STAGING_DB_ROOT_PASSWORD --body "$DB_ROOT_PASSWORD" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting STAGING_REDIS_PASSWORD secret..."
    gh secret set STAGING_REDIS_PASSWORD --body "$REDIS_PASSWORD" --repo Cyberworld-builders/talent-assessment
    
    print_status "Setting STAGING_S3_BUCKET secret..."
    gh secret set STAGING_S3_BUCKET --body "$S3_BUCKET" --repo Cyberworld-builders/talent-assessment
    
    print_success "GitHub repository variables and secrets set successfully!"
}

# Function to show help
show_help() {
    echo "🚀 GitHub OIDC Setup Script for Talent Assessment"
    echo
    echo "Usage: $0 [COMMAND]"
    echo
    echo "Commands:"
    echo "  setup     Deploy OIDC infrastructure and setup GitHub variables/secrets (default)"
    echo "  deploy    Deploy only the OIDC infrastructure"
    echo "  github    Setup only GitHub repository variables and secrets"
    echo "  verify    Verify the current setup"
    echo "  help      Show this help message"
    echo
    echo "Examples:"
    echo "  $0              # Full setup (idempotent)"
    echo "  $0 setup        # Full setup (idempotent)"
    echo "  $0 deploy       # Deploy OIDC infrastructure only"
    echo "  $0 github       # Setup GitHub variables and secrets only"
    echo "  $0 verify       # Verify current setup"
}

# Function to verify setup
verify_setup() {
    print_status "Verifying OIDC setup..."
    
    # Navigate to infrastructure directory
    cd infrastructure
    
    # Check if OIDC provider exists
    if terraform output -raw github_oidc_provider_arn >/dev/null 2>&1; then
        print_success "OIDC provider is deployed"
        OIDC_PROVIDER_ARN=$(terraform output -raw github_oidc_provider_arn)
        echo "  - Provider ARN: $OIDC_PROVIDER_ARN"
    else
        print_error "OIDC provider not found. Run the setup first."
        exit 1
    fi
    
    # Check if IAM role exists
    if terraform output -raw github_actions_role_arn >/dev/null 2>&1; then
        print_success "IAM role is deployed"
        ROLE_ARN=$(terraform output -raw github_actions_role_arn)
        echo "  - Role ARN: $ROLE_ARN"
    else
        print_error "IAM role not found. Run the setup first."
        exit 1
    fi
    
    # Check EC2 instance
    if terraform output -raw public_ip >/dev/null 2>&1; then
        EC2_HOST=$(terraform output -raw public_ip)
        print_success "EC2 instance is deployed"
        echo "  - Public IP: $EC2_HOST"
    else
        print_error "EC2 instance not found. Deploy main infrastructure first."
        exit 1
    fi
    
    # Check S3 bucket
    if terraform output -raw staging_s3_bucket_name >/dev/null 2>&1; then
        S3_BUCKET=$(terraform output -raw staging_s3_bucket_name)
        print_success "S3 bucket is deployed"
        echo "  - Bucket name: $S3_BUCKET"
    else
        print_error "S3 bucket not found. Deploy main infrastructure first."
        exit 1
    fi
    
    cd ..
    
    # Check GitHub CLI if available
    if command_exists gh; then
        print_status "Checking GitHub repository configuration..."
        
        if gh auth status >/dev/null 2>&1; then
            print_success "GitHub CLI is authenticated"
            
            # Try to get variables (this might fail if they don't exist, which is okay)
            if gh variable list --repo Cyberworld-builders/talent-assessment | grep -q "AWS_ROLE_ARN"; then
                print_success "AWS_ROLE_ARN variable is set"
            else
                print_warning "AWS_ROLE_ARN variable not found"
            fi
            
            if gh variable list --repo Cyberworld-builders/talent-assessment | grep -q "AWS_REGION"; then
                print_success "AWS_REGION variable is set"
            else
                print_warning "AWS_REGION variable not found"
            fi
        else
            print_warning "GitHub CLI not authenticated. Run: gh auth login"
        fi
    else
        print_warning "GitHub CLI not available. Cannot verify repository configuration."
    fi
    
    print_success "OIDC setup verification completed!"
    echo
    print_status "Next steps:"
    echo "1. Push to the 'staging' branch to test the deployment"
    echo "2. Monitor the GitHub Actions workflow logs"
    echo "3. Verify the application deploys successfully to EC2"
}

# Main script logic
main() {
    # Parse command line arguments
    case "${1:-setup}" in
        "setup")
            check_prerequisites
            check_oidc_infrastructure
            deploy_oidc_infrastructure
            get_ssh_key_content
            generate_passwords
            setup_github_variables
            verify_setup
            ;;
        "deploy")
            check_prerequisites
            check_oidc_infrastructure
            deploy_oidc_infrastructure
            ;;
        "github")
            check_oidc_infrastructure
            get_ssh_key_content
            generate_passwords
            setup_github_variables
            ;;
        "verify")
            verify_setup
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
