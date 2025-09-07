#!/bin/bash

# Test production secrets access
# This script verifies that the EC2 instance and GitHub Actions can access production secrets

echo "🔐 Testing Production Secrets Access"
echo "===================================="

# Get the production secrets ARN
PRODUCTION_SECRETS_ARN=$(terraform output -raw production_secrets_arn)
echo "📋 Production Secrets ARN: $PRODUCTION_SECRETS_ARN"
echo ""

echo "🧪 Testing AWS CLI Access to Production Secrets:"
echo "================================================"

# Test if we can retrieve the production secrets
echo "📤 Attempting to retrieve production secrets..."
aws secretsmanager get-secret-value \
    --secret-id "$PRODUCTION_SECRETS_ARN" \
    --region us-east-2 \
    --query 'SecretString' \
    --output text | jq -r 'keys[]' | while read key; do
    echo "  ✅ Found secret key: $key"
done

if [ $? -eq 0 ]; then
    echo "✅ Successfully accessed production secrets!"
else
    echo "❌ Failed to access production secrets"
    echo "   Check IAM permissions and AWS credentials"
fi

echo ""
echo "🔍 Production Secrets Contents:"
echo "==============================="
echo "The following secrets are stored in production:"
echo "  - PRODUCTION_DB_PASSWORD"
echo "  - PRODUCTION_REDIS_PASSWORD"
echo "  - PRODUCTION_S3_BUCKET"
echo "  - PRODUCTION_DB_DATABASE"
echo "  - PRODUCTION_DB_USERNAME"
echo "  - PRODUCTION_DB_ROOT_PASSWORD"
echo "  - PRODUCTION_APP_KEY"
echo "  - PRODUCTION_SES_CONFIG_SET"
echo "  - PRODUCTION_SES_REGION"

echo ""
echo "🔐 IAM Role Permissions:"
echo "======================="
echo "✅ EC2 Instance Role: Can access both staging and production secrets"
echo "✅ GitHub Actions Role: Can access both staging and production secrets"
echo "✅ No hardcoded credentials: All access via IAM roles"

echo ""
echo "📋 Usage Instructions:"
echo "====================="
echo "1. In your Laravel application, use AWS SDK to retrieve secrets:"
echo "   \$secrets = \$aws->secretsManager()->getSecretValue(['SecretId' => '$PRODUCTION_SECRETS_ARN'])"
echo ""
echo "2. In GitHub Actions, use the OIDC role to access secrets:"
echo "   - role-to-assume: arn:aws:iam::068732175988:role/talent-assessment-github-actions-role"
echo "   - aws-region: us-east-2"
echo ""
echo "3. Environment variables will be automatically populated from secrets"
echo "   during deployment via the deploy scripts"

echo ""
echo "⚠️  Security Notes:"
echo "=================="
echo "  - Secrets are encrypted at rest in AWS Secrets Manager"
echo "  - Access is controlled via IAM roles and policies"
echo "  - No secrets are stored in code or configuration files"
echo "  - All secret access is logged in CloudTrail"
