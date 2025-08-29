# 🔐 AWS SSO Setup for Server

This guide explains how to set up AWS SSO authentication on the server for the Talent Assessment project.

## 📋 Overview

The project uses AWS SSO with the profile name `sandbox-profile` as configured in `infrastructure/variables.tf`.

## 🚀 Setup Instructions

### 1. Install AWS CLI (if not already installed)

```bash
# Check if AWS CLI is installed
aws --version

# If not installed, install it
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
```

### 2. Configure AWS SSO

```bash
# Configure AWS SSO
aws configure sso

# You'll be prompted for the following information:
# SSO start URL: [Your SSO portal URL]
# SSO Region: [Your SSO region, e.g., us-east-1]
# Profile name: sandbox-profile
```

### 3. Get SSO Start URL

You'll need the SSO start URL from your AWS administrator. This is typically something like:
- `https://your-portal.awsapps.com/start`
- `https://d-xxxxxxxxxx.awsapps.com/start`

### 4. Login to AWS SSO

```bash
# Login using the configured profile
aws sso login --profile sandbox-profile
```

This will open a browser window (or provide a URL) where you can authenticate.

### 5. Verify Authentication

```bash
# Test the authentication
aws sts get-caller-identity --profile sandbox-profile
```

You should see output like:
```json
{
    "UserId": "AROA...",
    "Account": "123456789012",
    "Arn": "arn:aws:sts::123456789012:assumed-role/..."
}
```

## 🔧 Configuration Details

### Profile Configuration

The profile `sandbox-profile` is configured in:
- `infrastructure/variables.tf` (line 9)
- Used by Terraform for AWS resource management

### Required Permissions

The SSO profile needs permissions for:
- **EC2**: Create and manage instances
- **VPC**: Create and manage networking
- **S3**: Create and manage buckets
- **IAM**: Create and manage roles
- **Secrets Manager**: Access secrets
- **Route 53**: Domain management (for SES setup)

## 🧪 Testing the Setup

### 1. Test Terraform Access

```bash
cd infrastructure
terraform init
terraform plan
```

### 2. Test AWS CLI Commands

```bash
# List S3 buckets
aws s3 ls --profile sandbox-profile

# List EC2 instances
aws ec2 describe-instances --profile sandbox-profile

# Check SES configuration
aws ses get-send-quota --profile sandbox-profile
```

## 🚨 Troubleshooting

### Common Issues

#### 1. "Profile not found" Error
```bash
# Check if profile exists
aws configure list-profiles

# If not found, reconfigure SSO
aws configure sso
```

#### 2. "Token expired" Error
```bash
# Re-login to refresh token
aws sso login --profile sandbox-profile
```

#### 3. "Access denied" Error
- Verify your SSO user has the required permissions
- Contact your AWS administrator
- Check if you're in the correct AWS account

#### 4. "SSO session expired" Error
```bash
# Clear SSO cache and re-login
aws sso logout --profile sandbox-profile
aws sso login --profile sandbox-profile
```

### Debug Commands

```bash
# Check SSO configuration
cat ~/.aws/config

# Check current identity
aws sts get-caller-identity --profile sandbox-profile

# List available profiles
aws configure list-profiles

# Check SSO session status
aws sts get-caller-identity --profile sandbox-profile
```

## 🔄 Switching Between Profiles

If you have multiple AWS profiles:

```bash
# Use specific profile for commands
aws s3 ls --profile sandbox-profile

# Set default profile for current session
export AWS_PROFILE=sandbox-profile

# Use default profile
aws s3 ls
```

## 📚 Additional Resources

- [AWS CLI SSO Configuration](https://docs.aws.amazon.com/cli/latest/userguide/cli-configure-sso.html)
- [AWS SSO User Guide](https://docs.aws.amazon.com/singlesignon/latest/userguide/)
- [AWS CLI Configuration](https://docs.aws.amazon.com/cli/latest/userguide/cli-configure-files.html)

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Verify your SSO portal URL is correct
3. Ensure you have the required permissions
4. Contact your AWS administrator for SSO access
5. Check AWS CloudTrail logs for authentication errors

## 🎯 Next Steps

Once AWS SSO is configured:

1. **Test Terraform**: Run `terraform plan` to verify access
2. **Deploy Infrastructure**: Use `terraform apply` to create resources
3. **Configure SES**: Follow the SES implementation guide
4. **Set up Domain**: Register and configure your staging domain
