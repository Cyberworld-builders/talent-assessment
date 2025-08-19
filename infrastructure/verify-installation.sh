#!/bin/bash

# 🧪 Verification Script for EC2 Instance Installation
# This script verifies that all required packages are installed on the EC2 instance

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
check_command() {
    local cmd=$1
    local name=$2
    
    if command -v "$cmd" >/dev/null 2>&1; then
        print_success "$name is installed: $(which $cmd)"
        return 0
    else
        print_error "$name is NOT installed"
        return 1
    fi
}

# Function to check version
check_version() {
    local cmd=$1
    local name=$2
    
    if command -v "$cmd" >/dev/null 2>&1; then
        local version=$($cmd --version 2>/dev/null | head -1)
        print_success "$name version: $version"
    else
        print_error "$name version check failed"
    fi
}

# Main verification
main() {
    print_status "Verifying EC2 instance installation..."
    echo
    
    # Check required commands
    print_status "Checking required packages..."
    
    local all_good=true
    
    # Core packages
    check_command "docker" "Docker" || all_good=false
    check_command "docker-compose" "Docker Compose" || all_good=false
    check_command "aws" "AWS CLI" || all_good=false
    check_command "jq" "jq" || all_good=false
    
    # System packages
    check_command "curl" "curl" || all_good=false
    check_command "git" "git" || all_good=false
    
    echo
    print_status "Checking versions..."
    
    # Check versions
    check_version "docker" "Docker"
    check_version "docker-compose" "Docker Compose"
    check_version "aws" "AWS CLI"
    check_version "jq" "jq"
    
    echo
    print_status "Checking AWS authentication..."
    
    # Check AWS authentication
    if aws sts get-caller-identity >/dev/null 2>&1; then
        print_success "AWS authentication working"
        aws sts get-caller-identity --query 'Arn' --output text
    else
        print_error "AWS authentication failed"
        all_good=false
    fi
    
    echo
    print_status "Checking Docker service..."
    
    # Check Docker service
    if systemctl is-active --quiet docker; then
        print_success "Docker service is running"
    else
        print_error "Docker service is not running"
        all_good=false
    fi
    
    echo
    print_status "Checking application directory..."
    
    # Check application directory
    if [ -d "/opt/talent-assessment" ]; then
        print_success "Application directory exists: /opt/talent-assessment"
    else
        print_warning "Application directory does not exist: /opt/talent-assessment"
    fi
    
    echo
    if [ "$all_good" = true ]; then
        print_success "✅ All verifications passed! EC2 instance is properly configured."
        echo
        print_status "Next steps:"
        echo "1. Upload your Laravel application files"
        echo "2. Configure environment variables"
        echo "3. Start the application with docker-compose"
    else
        print_error "❌ Some verifications failed. Please check the installation."
        exit 1
    fi
}

# Run main function
main "$@"
