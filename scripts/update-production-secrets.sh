#!/bin/bash

# Script to update production secrets in AWS Secrets Manager
# This should be run from a machine with AWS credentials that have secretsmanager:UpdateSecret permissions
# For example: run this from your local machine or from a GitHub Actions workflow with proper IAM role

set -e

echo "=== Updating Production Secrets in AWS Secrets Manager ==="
echo ""
echo "This script will add PRODUCTION_SES_FROM_ADDRESS to the secrets."
echo ""

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    echo "Error: AWS CLI is not installed. Please install it first."
    exit 1
fi

# Check if jq is installed
if ! command -v jq &> /dev/null; then
    echo "Error: jq is not installed. Please install it first."
    exit 1
fi

# Check AWS credentials
echo "Checking AWS credentials..."
if ! aws sts get-caller-identity &> /dev/null; then
    echo "Error: Not authenticated with AWS. Please configure AWS credentials."
    exit 1
fi

echo "✓ AWS credentials verified"
echo ""

# Fetch current secrets
echo "Fetching current secrets..."
aws secretsmanager get-secret-value \
    --secret-id talent-assessment-production-secrets \
    --region us-east-2 \
    --query SecretString \
    --output text > /tmp/current-secrets.json

echo "✓ Current secrets fetched"
echo ""

# Update secrets with SES from address
echo "Adding PRODUCTION_SES_FROM_ADDRESS to secrets..."
cat /tmp/current-secrets.json | jq '. + {"PRODUCTION_SES_FROM_ADDRESS": "noreply@cyberworldbuilders.dev"}' > /tmp/updated-secrets.json

echo "Updated secrets:"
cat /tmp/updated-secrets.json | jq 'keys'
echo ""

# Confirm update
read -p "Do you want to update the secrets in AWS Secrets Manager? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "Aborted. No changes made."
    rm /tmp/current-secrets.json /tmp/updated-secrets.json
    exit 0
fi

# Update secrets in AWS
echo ""
echo "Updating secrets in AWS Secrets Manager..."
aws secretsmanager update-secret \
    --secret-id talent-assessment-production-secrets \
    --region us-east-2 \
    --secret-string file:///tmp/updated-secrets.json

echo "✓ Secrets updated successfully!"
echo ""

# Clean up
rm /tmp/current-secrets.json /tmp/updated-secrets.json

echo "=== Update Complete ==="
echo ""
echo "Next steps:"
echo "1. Redeploy production to apply the new email configuration"
echo "2. Test emails using the test-email-production.php script"
echo ""

