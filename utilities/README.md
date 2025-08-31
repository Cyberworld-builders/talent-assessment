# SES Testing Utilities

This folder contains utilities for testing and debugging AWS SES (Simple Email Service) configuration in the Talent Assessment application.

## Files

### `test-ses-config.php`
A simple configuration test script that displays the current mail configuration and environment variables.

**Usage:**
```bash
# From the project root
docker exec talent-assessment-app php utilities/test-ses-config.php

# Or from within a container
php utilities/test-ses-config.php
```

**What it tests:**
- Mail driver configuration
- Mail from address and name
- SES region configuration
- Environment variable loading

### `test-ses-email.php`
A comprehensive email test script that attempts to send a test email through SES.

**Usage:**
```bash
# From the project root
docker exec talent-assessment-app php utilities/test-ses-email.php

# Or from within a container
php utilities/test-ses-email.php
```

**What it tests:**
- Full SES email sending functionality
- IAM role authentication
- Mail configuration
- Error handling

### `debug-mail-config.php`
A detailed debugging script for investigating mail configuration issues.

**Usage:**
```bash
# From the project root
docker exec talent-assessment-app php utilities/debug-mail-config.php

# Or from within a container
php utilities/debug-mail-config.php
```

**What it investigates:**
- Environment variable loading
- Laravel configuration values
- Config cache status
- .env file contents and status

## When to Use

- **`test-ses-config.php`**: Quick check of mail configuration
- **`test-ses-email.php`**: Verify SES email functionality is working
- **`debug-mail-config.php`**: Troubleshoot configuration issues

## Prerequisites

- Laravel application must be running in a container
- AWS SES must be configured
- IAM roles must have proper SES permissions
- Email addresses must be verified in SES (for actual sending)

## Notes

- These scripts are designed for development and staging environments
- They require Laravel to be properly bootstrapped
- Environment variables must be set correctly
- For production, use the integrated SES test in the deployment script
