# 🔐 AWS OIDC Setup for GitHub Actions

This document explains how to set up AWS OpenID Connect (OIDC) for GitHub Actions to use short-lived tokens instead of long-lived credentials.

## 📋 Overview

### What is OIDC?
OpenID Connect (OIDC) allows GitHub Actions to authenticate with AWS using short-lived tokens instead of storing long-lived access keys. This is more secure and follows AWS security best practices.

### Benefits
- ✅ **No long-lived credentials** stored in GitHub secrets
- ✅ **Automatic token rotation** - tokens expire after each workflow run
- ✅ **Principle of least privilege** - only specific repositories can assume the role
- ✅ **Audit trail** - all authentication is logged in AWS CloudTrail
- ✅ **Compliance** - meets security requirements for production environments

## 🚀 Quick Setup

### 1. Deploy OIDC Infrastructure

```bash
# Run the automated setup script
./setup-github-oidc.sh

# Or manually:
cd infrastructure
terraform init
terraform apply
```

### 2. Configure GitHub Repository Variables

The setup script will automatically configure these variables, or you can set them manually:

**Repository Variables** (Settings → Secrets and variables → Actions → Variables):
- `AWS_ROLE_ARN`: The ARN of the IAM role created by Terraform
- `AWS_REGION`: `us-east-2`

**Repository Secrets** (Settings → Secrets and variables → Actions → Secrets):
- `EC2_HOST`: Your EC2 instance public IP
- `EC2_USER`: `ubuntu`
- `EC2_SSH_KEY`: Your SSH private key
- `STAGING_DB_ROOT_PASSWORD`: Your staging DB root password
- `STAGING_REDIS_PASSWORD`: Your staging Redis password
- `STAGING_S3_BUCKET`: Your staging S3 bucket name

## 🏗️ Infrastructure Components

### 1. GitHub OIDC Provider
```hcl
resource "aws_iam_openid_connect_provider" "github" {
  url = "https://token.actions.githubusercontent.com"
  client_id_list = ["sts.amazonaws.com"]
  thumbprint_list = [
    "6938fd4d98bab03faadb97b34396831e3780aea1",
    "1c58a3a8518e8759bf075b76b750d4f2df264fcd"
  ]
}
```

### 2. IAM Role for GitHub Actions
```hcl
resource "aws_iam_role" "github_actions" {
  name = "talent-assessment-github-actions-role"
  
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          Federated = aws_iam_openid_connect_provider.github.arn
        }
        Action = "sts:AssumeRoleWithWebIdentity"
        Condition = {
          StringEquals = {
            "token.actions.githubusercontent.com:aud" = "sts.amazonaws.com"
          }
          StringLike = {
            "token.actions.githubusercontent.com:sub" = "repo:Cyberworld-builders/talent-assessment:*"
          }
        }
      }
    ]
  })
}
```

### 3. IAM Policies
The role includes policies for:
- **ECR Access**: Push/pull Docker images
- **Secrets Manager**: Access staging secrets
- **S3 Access**: Upload/download files
- **EC2 Access**: Describe instances for deployment

## 🔧 GitHub Actions Configuration

### Before (Long-lived credentials):
```yaml
- name: Configure AWS Credentials
  uses: aws-actions/configure-aws-credentials@v4
  with:
    aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
    aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
    aws-region: ${{ vars.AWS_REGION }}
```

### After (OIDC):
```yaml
permissions:
  id-token: write
  contents: read

- name: Configure AWS Credentials
  uses: aws-actions/configure-aws-credentials@v4
  with:
    role-to-assume: ${{ vars.AWS_ROLE_ARN }}
    aws-region: ${{ vars.AWS_REGION }}
```

## 🔍 How It Works

### 1. Workflow Trigger
When a GitHub Actions workflow runs, GitHub generates a short-lived JWT token.

### 2. Token Exchange
The workflow uses the JWT token to assume the AWS IAM role via the OIDC provider.

### 3. AWS Authentication
AWS validates the JWT token against the GitHub OIDC provider and grants temporary credentials.

### 4. Resource Access
The workflow can now access AWS resources (ECR, S3, Secrets Manager, etc.) using the temporary credentials.

### 5. Token Expiration
The temporary credentials automatically expire after the workflow completes.

## 🛡️ Security Features

### Repository Restriction
Only workflows from the `Cyberworld-builders/talent-assessment` repository can assume the role:

```hcl
StringLike = {
  "token.actions.githubusercontent.com:sub" = "repo:Cyberworld-builders/talent-assessment:*"
}
```

### Minimal Permissions
The IAM role has only the necessary permissions:
- ECR: Push/pull images
- Secrets Manager: Read staging secrets
- S3: Access staging bucket
- EC2: Describe instances

### Audit Logging
All authentication attempts are logged in AWS CloudTrail for security monitoring.

## 🔧 Manual Setup

If you prefer to set up manually instead of using the script:

### 1. Deploy Infrastructure
```bash
cd infrastructure
terraform init
terraform apply
```

### 2. Get the Role ARN
```bash
terraform output github_actions_role_arn
```

### 3. Set GitHub Variables
Go to your repository settings and add:
- Variable: `AWS_ROLE_ARN` = [Role ARN from step 2]
- Variable: `AWS_REGION` = `us-east-2`

## 🧪 Testing the Setup

### 1. Trigger a Workflow
Push to the `staging` branch to trigger the deployment workflow.

### 2. Check Authentication
In the workflow logs, you should see:
```
✅ Successfully configured AWS credentials
```

### 3. Verify Access
The workflow should be able to:
- Push images to ECR
- Access Secrets Manager
- Deploy to EC2

## 🚨 Troubleshooting

### Common Issues

#### 1. "Access Denied" Error
**Cause**: Repository name mismatch or role not properly configured
**Solution**: Verify the repository name in the IAM role trust policy

#### 2. "Token has expired" Error
**Cause**: JWT token expired during long-running workflow
**Solution**: This is normal for long workflows. Consider breaking them into smaller jobs.

#### 3. "Role not found" Error
**Cause**: Role ARN is incorrect or role doesn't exist
**Solution**: Verify the role ARN in GitHub variables

#### 4. "Insufficient permissions" Error
**Cause**: IAM role doesn't have required permissions
**Solution**: Check the IAM policies attached to the role

### Debug Commands

```bash
# Check if OIDC provider exists
aws iam list-open-id-connect-providers

# Check role trust policy
aws iam get-role --role-name talent-assessment-github-actions-role

# Check role policies
aws iam list-attached-role-policies --role-name talent-assessment-github-actions-role
```

## 🔄 Migration from Long-lived Credentials

### 1. Deploy OIDC Infrastructure
```bash
./setup-github-oidc.sh
```

### 2. Update GitHub Actions Workflow
Replace the AWS credentials step with the OIDC version.

### 3. Test the Workflow
Trigger a deployment to verify everything works.

### 4. Remove Old Secrets
Once confirmed working, remove the old AWS access keys from GitHub secrets.

## 📚 Additional Resources

- [AWS OIDC Documentation](https://docs.aws.amazon.com/IAM/latest/UserGuide/id_roles_providers_create_oidc.html)
- [GitHub Actions OIDC Documentation](https://docs.github.com/en/actions/deployment/security-hardening-your-deployments/configuring-openid-connect-in-amazon-web-services)
- [AWS Security Best Practices](https://docs.aws.amazon.com/wellarchitected/latest/security-pillar/welcome.html)

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review AWS CloudTrail logs for authentication errors
3. Verify GitHub repository variables are set correctly
4. Check the workflow logs for detailed error messages
5. Ensure the repository name matches exactly in the IAM role trust policy
