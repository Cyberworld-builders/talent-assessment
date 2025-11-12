# Production Email Configuration Analysis

## Problem Statement

Production email configuration keeps getting reset to Mailtrap (development SMTP) with null credentials instead of using AWS SES, which causes emails to fail with "Authentication required" errors.

## Root Cause Analysis

### Issue #1: Deployment Script Overwrites Email Configuration

**File**: `.github/workflows/production-deploy-tag.yml` (Lines 188-213)

The deployment script creates `.env.production` from `.env.example` if it doesn't exist, and then updates it with secrets. However, the script has several issues:

1. **Missing SES Configuration in Secrets Manager**
   - Secrets Manager contains `PRODUCTION_SES_REGION` but is missing:
     - `PRODUCTION_SES_FROM_ADDRESS` 
     - `PRODUCTION_SES_ACCESS_KEY_ID` (not needed with IAM role)
     - `PRODUCTION_SES_SECRET_ACCESS_KEY` (not needed with IAM role)

2. **Hardcoded SMTP Configuration**
   - Line 189-196 in production-deploy-tag.yml shows that when `.env.production` is created, it defaults to generic settings
   - There's no explicit SES configuration being set

3. **Email Driver Not Set**
   - The deployment script never sets `MAIL_DRIVER=ses`
   - It only updates `MAIL_FROM_ADDRESS` (line 213) but doesn't set the driver or other required SES settings

### Issue #2: IAM Role-Based Auth Already Available

**Discovery**: The EC2 instance role `talent-assessment-ec2-role` ALREADY has SES permissions!

Test result:
```bash
$ aws ses send-email --from noreply@cyberworldbuilders.dev \
  --destination "ToAddresses=user-apone@cyberworldbuilders.com" \
  --message "Subject={Data='Test'},Body={Text={Data='Test'}}" \
  --region us-east-2

{
    "MessageId": "010f0199d98d585b-7f73c705-7a8e-4205-a379-28390525d860-000000"
}
```

✅ **This means we can use IAM role-based authentication and don't need SES credentials!**

## Current Configuration Issues

### What's in Secrets Manager
```json
{
  "PRODUCTION_APP_KEY": "...",
  "PRODUCTION_DB_DATABASE": "talent_assessment_production",
  "PRODUCTION_DB_PASSWORD": "...",
  "PRODUCTION_DB_ROOT_PASSWORD": "...",
  "PRODUCTION_DB_USERNAME": "talent_user_production",
  "PRODUCTION_REDIS_PASSWORD": "null",
  "PRODUCTION_S3_BUCKET": "talent-assessment-production-uploads-l474uk2d",
  "PRODUCTION_SES_CONFIG_SET": "...",
  "PRODUCTION_SES_REGION": "us-east-2"
}
```

**Missing**:
- `PRODUCTION_SES_FROM_ADDRESS` - should be `noreply@cyberworldbuilders.dev` or similar

### Current `.env.production` (After Deployment)
```bash
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

### What It SHOULD Be
```bash
MAIL_DRIVER=ses
MAIL_FROM_ADDRESS=noreply@cyberworldbuilders.dev
MAIL_FROM_NAME="Involved Talent Assessment"

# AWS Configuration (uses IAM role, no credentials needed)
AWS_DEFAULT_REGION=us-east-2
```

## Comparison with Staging (Working Configuration)

### Staging `.env` (Working)
```bash
MAIL_DRIVER=ses
MAIL_FROM_ADDRESS=noreply@cyberworldbuilders.dev
MAIL_FROM_NAME="Talent Assessment Staging"
```

Staging works because it's properly configured to use SES!

## Recommended Solution

### Option 1: Use IAM Role-Based Auth (RECOMMENDED)

**Advantages**:
- ✅ No credentials to manage or rotate
- ✅ More secure (no hardcoded secrets)
- ✅ Already working on EC2 instance
- ✅ Simpler configuration
- ✅ Follows AWS best practices

**Implementation**:
1. Update Secrets Manager to add `PRODUCTION_SES_FROM_ADDRESS`
2. Modify deployment script to set SES configuration
3. Remove Mailtrap configuration entirely from production

### Option 2: Use Access Keys (NOT RECOMMENDED)

**Disadvantages**:
- ❌ Requires managing and rotating credentials
- ❌ Less secure
- ❌ More complex
- ❌ Not following AWS best practices

## Implementation Plan

### Step 1: Update AWS Secrets Manager
```bash
# Add PRODUCTION_SES_FROM_ADDRESS to secrets
aws secretsmanager update-secret \
  --secret-id talent-assessment-production-secrets \
  --secret-string '{
    "PRODUCTION_APP_KEY": "...",
    "PRODUCTION_DB_DATABASE": "talent_assessment_production",
    "PRODUCTION_DB_PASSWORD": "...",
    "PRODUCTION_DB_ROOT_PASSWORD": "...",
    "PRODUCTION_DB_USERNAME": "talent_user_production",
    "PRODUCTION_REDIS_PASSWORD": "null",
    "PRODUCTION_S3_BUCKET": "talent-assessment-production-uploads-l474uk2d",
    "PRODUCTION_SES_CONFIG_SET": "...",
    "PRODUCTION_SES_REGION": "us-east-2",
    "PRODUCTION_SES_FROM_ADDRESS": "noreply@cyberworldbuilders.dev"
  }' \
  --region us-east-2
```

### Step 2: Update Deployment Script

Modify `.github/workflows/production-deploy-tag.yml` to:
1. Set `MAIL_DRIVER=ses` instead of `smtp`
2. Remove Mailtrap configuration
3. Set SES-specific environment variables
4. Use IAM role for authentication (no credentials needed)

### Step 3: Update `.env.production` Template

Create a production-specific environment template that includes SES configuration.

## Benefits of This Approach

1. **Security**: No credentials in environment variables or files
2. **Simplicity**: Fewer configuration values to manage
3. **Consistency**: Same approach as staging
4. **Reliability**: IAM roles are more reliable than access keys
5. **Compliance**: Follows AWS security best practices
6. **Cost**: No additional cost for IAM role usage

## Testing Plan

1. Update Secrets Manager with `PRODUCTION_SES_FROM_ADDRESS`
2. Update deployment scripts
3. Deploy to production
4. Run email test script
5. Verify emails are sent successfully
6. Monitor production logs for any issues

## Rollback Plan

If issues occur:
1. Revert deployment script changes
2. Manually update `.env.production` to use SES
3. Restart containers: `docker-compose -f docker-compose.production.yml down && docker-compose -f docker-compose.production.yml up -d`

## Additional Notes

- The EC2 instance role `talent-assessment-ec2-role` already has all necessary SES permissions
- No changes to IAM policies are needed
- This configuration will persist across deployments once fixed
- Staging is already using this approach successfully

