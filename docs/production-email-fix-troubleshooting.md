# Production Email Fix Troubleshooting Guide

## Problem Summary
The assessment assignment form at `https://my.involvedtalent.com/dashboard/assessments/1/assign` was throwing a "whoops" error when the "email notification" checkbox was checked, preventing successful form submission.

## Root Cause Analysis
The issue was caused by multiple interconnected problems that created a cascade of failures:

### 1. AWS SDK Deprecation Warning (Primary Cause)
- **Problem**: AWS SDK for PHP was emitting deprecation warnings for PHP 7.4
- **Error**: `This installation of the SDK is using PHP version 7.4.33, which will be deprecated...`
- **Impact**: In production environment, this warning was being treated as a fatal error
- **Solution**: Added `putenv('AWS_SUPPRESS_PHP_DEPRECATION_WARNING=true');` to `bootstrap/app.php`

### 2. Email Link Generation Issue
- **Problem**: `$_SERVER['SERVER_NAME']` was not set in Docker container
- **Impact**: Email links were generated as `https://localhost:8000/assignments` instead of proper domain
- **Solution**: Modified `app/Mailer.php` to use `parse_url(env('APP_URL'), PHP_URL_HOST)` as fallback
- **Code Change**:
  ```php
  $server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
  ```

### 3. AWS SES Region Mismatch
- **Problem**: Production SES was configured in `us-east-2` but application used `us-east-1`
- **Error**: `MessageRejected (client): Email address is not verified`
- **Solution**: Updated `.env.production` to set `AWS_REGION=us-east-2`

### 4. Session Configuration Issues
- **Problem**: Session domain mismatch between `.involvedtalent.com` and `my.involvedtalent.com`
- **Impact**: CSRF token validation failures
- **Solution**: Updated `config/session.php` to use `my.involvedtalent.com` for production

### 5. Redis Configuration
- **Problem**: Redis password configuration conflicts
- **Impact**: Session storage failures
- **Solution**: Removed Redis password requirements from all environments

## Step-by-Step Fixes Applied

### Fix 1: Suppress AWS SDK Deprecation Warning
**File**: `bootstrap/app.php`
**Change**: Added at the top of the file
```php
<?php
putenv('AWS_SUPPRESS_PHP_DEPRECATION_WARNING=true');
// ... rest of file
```

### Fix 2: Fix Email Link Generation
**File**: `app/Mailer.php`
**Method**: `send_assignments`, `send_assignment`, `send_questionnaire`
**Change**: Updated server name detection
```php
// Before
$server = $_SERVER['SERVER_NAME'];

// After
$server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
if ($server == 'localhost')
    $server .= ':8000';
if (session('reseller'))
    $server .= '/r/'.session('reseller')->id;
$assignments_link = 'https://'.$server.'/assignments';
```

### Fix 3: Update Session Domain
**File**: `config/session.php`
**Change**: Updated domain configuration
```php
'domain' => env('APP_ENV') === 'production' ? 'my.involvedtalent.com' : null,
```

### Fix 4: Remove Redis Password Requirements
**File**: `docker-compose.staging.yml`
**Change**: Removed password configuration from Redis service
```yaml
redis-staging:
  image: redis:7-alpine
  container_name: talent-assessment-redis-staging
  # Removed: command: redis-server --requirepass ${REDIS_PASSWORD}
  # Removed: env_file: .env.staging
```

### Fix 5: Environment Variable Updates
**File**: `.env.production`
**Change**: Added correct AWS region
```
AWS_REGION=us-east-2
```

## Testing Process

### 1. Initial Debugging
- Added comprehensive logging to `AssessmentsController.php`
- Used `\Log::info()` and `\Log::error()` to trace execution flow
- Identified AWS SDK warning as the root cause

### 2. Email Testing
- Created test scripts to verify email functionality
- Tested both `App\Mailer` methods and direct `Mail` facade
- Confirmed email sending worked after region fix

### 3. Form Submission Testing
- Tested with email notifications enabled
- Verified CSRF token handling
- Confirmed session management

## Key Learnings

### 1. Environment-Specific Issues
- Production environment treats warnings as fatal errors
- Docker containers may not have all expected `$_SERVER` variables
- Environment variables must be consistent across all services

### 2. AWS Configuration
- IAM roles handle authentication (no explicit keys needed)
- Region configuration must match across all AWS services
- SES email addresses must be verified in the correct region

### 3. Laravel Configuration
- Session domain must match the actual application domain
- Redis configuration affects both cache and session storage
- Environment variable changes require service restarts

### 4. Debugging Strategy
- Start with comprehensive logging
- Test individual components in isolation
- Use temporary test scripts for complex functionality
- Check both application logs and container logs

## Prevention Measures

### 1. Environment Consistency
- Ensure all environments use the same Redis configuration
- Verify AWS region consistency across all services
- Use consistent domain naming conventions

### 2. Error Handling
- Implement proper error handling for email operations
- Add fallbacks for missing environment variables
- Use try-catch blocks around critical operations

### 3. Testing
- Test email functionality in all environments
- Verify form submissions with all options enabled
- Test both authenticated and unauthenticated flows

## Files Modified

### Core Application Files
- `bootstrap/app.php` - AWS deprecation warning suppression
- `app/Mailer.php` - Email link generation fix
- `config/session.php` - Session domain configuration
- `app/Http/Controllers/AssessmentsController.php` - Debugging (temporarily)

### Environment Files
- `.env.production` - AWS region configuration
- `.env.staging` - Redis password removal

### Docker Configuration
- `docker-compose.staging.yml` - Redis service configuration
- `docker-compose.production.yml` - (referenced for comparison)

### Frontend Files
- `resources/views/dashboard/assessments/partials/_assignform.blade.php` - JavaScript improvements

## Deployment Process

### 1. Build New Image
```bash
docker build -f Dockerfile -t talent-assessment-app:latest .
```

### 2. Deploy to Environment
```bash
# Production
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d

# Staging
export STAGING_APP_IMAGE=talent-assessment-app:latest
docker-compose -f docker-compose.staging.yml down
docker-compose -f docker-compose.staging.yml up -d
```

### 3. Clear Caches
```bash
docker-compose -f docker-compose.production.yml exec app-production php artisan cache:clear
docker-compose -f docker-compose.production.yml exec app-production php artisan config:clear
```

## Verification Steps

### 1. Check Service Status
```bash
docker-compose -f docker-compose.production.yml ps
```

### 2. Test Form Submission
- Navigate to assessment assignment form
- Enable email notifications
- Submit form
- Verify success message appears
- Check email delivery

### 3. Check Logs
```bash
docker-compose -f docker-compose.production.yml logs app-production --tail=50
```

## Troubleshooting Checklist

When encountering similar issues:

1. **Check AWS SDK warnings** - Look for deprecation warnings in logs
2. **Verify email link generation** - Check if links use correct domain
3. **Confirm AWS region consistency** - Ensure all services use same region
4. **Validate session configuration** - Check domain and security settings
5. **Test Redis connectivity** - Verify no password conflicts
6. **Clear all caches** - Application, config, and framework caches
7. **Restart services** - Full container restart may be required

## Related Issues

- CSRF token mismatches
- Session storage failures
- Email delivery failures
- AWS service authentication errors
- Docker environment variable issues
