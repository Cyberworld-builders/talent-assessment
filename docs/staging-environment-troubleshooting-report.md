# Staging Environment Troubleshooting Report

## Executive Summary

The staging environment for the Talent Assessment application was experiencing critical failures, including 500 errors on the home page and login functionality. Through systematic troubleshooting, we identified and resolved multiple infrastructure and configuration issues, ultimately achieving a working staging environment.

## Initial Problem Statement

- **Primary Issue**: Staging site returning "whoops" error page (HTTP 500) on home page
- **Secondary Issue**: Login submission failures
- **Environment**: Laravel 5.1 application deployed via Docker containers
- **URL**: https://talent-staging.cyberworldbuilders.dev

## Root Cause Analysis

### 1. Infrastructure Configuration Issues

#### Problem: Wrong Docker Images
- **Issue**: `docker-compose.staging.yml` was using the PHP application image for MySQL and Redis services
- **Evidence**: 
  - MySQL container was running Apache instead of MySQL
  - Redis container couldn't find `redis-server` and kept restarting
- **Impact**: Database and cache services were non-functional

#### Problem: Missing Environment Variables
- **Issue**: Staging environment variables not being passed to containers
- **Evidence**: Docker Compose warnings about missing `STAGING_*` variables
- **Impact**: Services couldn't connect to databases or authenticate

### 2. Application Configuration Issues

#### Problem: Hardcoded Redis Configuration
- **Issue**: `config/database.php` had hardcoded `127.0.0.1` for Redis host
- **Evidence**: Laravel trying to connect to localhost instead of staging Redis container
- **Impact**: Cache operations failing

#### Problem: Encryption Key Format
- **Issue**: Laravel 5.1 encryption key using `base64:` prefix
- **Evidence**: "No supported encrypter found" errors
- **Impact**: Application couldn't initialize encryption service

#### Problem: Database Middleware Logic
- **Issue**: `SetDatabase` middleware only configured database when reseller session existed
- **Evidence**: Database connection failing during initial login (no reseller session)
- **Impact**: Login functionality broken

## Troubleshooting Methodology

### 1. Log Analysis
- Examined Laravel application logs (`/var/www/storage/logs/laravel.log`)
- Analyzed Docker container logs for each service
- Identified specific error messages and stack traces

### 2. Environment Comparison
- Compared working development environment with broken staging environment
- Identified key differences in configuration and setup
- Used development environment as reference for correct configuration

### 3. Infrastructure Validation
- Verified container status and networking
- Tested hostname resolution between containers
- Validated environment variable propagation

## Solutions Implemented

### 1. Fixed Docker Compose Configuration

**Updated `docker-compose.staging.yml`:**
```yaml
# Before (incorrect)
mysql-staging:
  image: talent-assessment-app  # Wrong image
redis-staging:
  image: talent-assessment-app  # Wrong image

# After (correct)
mysql-staging:
  image: mysql:8.0
redis-staging:
  image: redis:7-alpine
```

### 2. Implemented Proper Environment Variable Handling

**Rule Established**: When updating environment variables in Docker containers, ALWAYS use `docker-compose down` followed by `docker-compose up -d`. NEVER use `restart` as it doesn't pick up new environment variables.

**Environment Variables Set:**
```bash
export STAGING_DB_DATABASE=talent_assessment_staging
export STAGING_DB_USERNAME=talent_user_staging
export STAGING_DB_PASSWORD=strong_staging_db_pass_ntcneex7
export STAGING_DB_ROOT_PASSWORD=strong_staging_root_pass_ntcneex7
export STAGING_REDIS_PASSWORD=strong_staging_redis_pass_ntcneex7
```

### 3. Updated Application Configuration

**Fixed Redis Configuration (`config/database.php`):**
```php
// Before (hardcoded)
'default' => [
    'host'     => '127.0.0.1',
    'port'     => 6379,
    'database' => 0,
],

// After (environment-based)
'default' => [
    'host'     => env('REDIS_HOST', '127.0.0.1'),
    'port'     => env('REDIS_PORT', 6379),
    'password' => env('REDIS_PASSWORD', null),
    'database' => 0,
],
```

**Fixed SetDatabase Middleware (`app/Http/Middleware/SetDatabase.php`):**
```php
// Before (only handled reseller sessions)
if (session('reseller')) {
    // Set reseller database
}

// After (handles both cases)
if (session('reseller')) {
    // Set reseller database
} else {
    // Use default database configuration
    \Config::set('database.connections.mysql.host', env('DB_HOST', 'mysql-staging'));
    \Config::set('database.connections.mysql.database', env('DB_DATABASE', 'talent_assessment_staging'));
    \Config::set('database.connections.mysql.username', env('DB_USERNAME', 'talent_user_staging'));
    \Config::set('database.connections.mysql.password', env('DB_PASSWORD', 'strong_staging_db_pass_ntcneex7'));
    DB::reconnect('mysql');
}
```

### 4. Fixed Encryption Key Format

**Updated APP_KEY in `.env.staging`:**
```bash
# Before (base64 format - not supported in Laravel 5.1)
APP_KEY=base64:Bg5Rerk9c9klImU1J/+ic8doq67Sth4l+ZPgFwsGvrE=

# After (32-character format - Laravel 5.1 compatible)
APP_KEY=Cg5Jku0iO4SBrbeX4eBp5FppkWnnb1Vf
```

### 5. Implemented File Override Strategy

**Used Volume Mounts in `docker-compose.staging.yml`:**
```yaml
volumes:
  - ./app/Http/Middleware/SetDatabase.php:/var/www/app/Http/Middleware/SetDatabase.php
  - ./config/database.php:/var/www/config/database.php
  - ./config/app.php:/var/www/config/app.php
  - ./.env.staging:/var/www/.env
```

This approach allows overriding files in the prebuilt Docker image without rebuilding.

## Key Discoveries

### 1. Laravel 5.1 Encryption Requirements
- Laravel 5.1 requires plain 32-character encryption keys
- Base64-encoded keys (introduced in later Laravel versions) are not supported
- This was a critical blocker that prevented application initialization

### 2. Docker Environment Variable Behavior
- `docker-compose restart` does NOT pick up new environment variables
- Must use `docker-compose down` followed by `docker-compose up -d`
- This is a common pitfall that affects all Docker Compose deployments

### 3. Middleware Execution Order
- Artisan commands don't execute the full middleware stack
- Database connection issues in middleware affect web requests but not CLI commands
- This explains why some operations worked via web but failed via CLI

### 4. Prebuilt Image Limitations
- Prebuilt Docker images don't include local configuration changes
- Volume mounts provide a clean way to override files without rebuilding
- This approach is essential for staging/production deployments

## Current Status

### ✅ Resolved Issues
- Staging home page loads successfully (HTTP 200)
- Login page loads successfully (HTTP 200)
- Laravel session cookies are being set properly
- Database and Redis containers are running with correct images
- Environment variables are properly configured
- Encryption service is working

### 🔄 Remaining Tasks
- Database migrations need to be run
- Database seeding needs to be completed
- Login functionality testing needed
- Production deployment strategy needs to be defined

## Lessons Learned

### 1. Environment Comparison is Critical
Comparing working environments (development) with broken environments (staging) revealed key differences that weren't obvious from logs alone.

### 2. Laravel Version Compatibility
Laravel 5.1 has specific requirements that differ from newer versions, particularly around encryption key formats.

### 3. Docker Best Practices
- Always use `down`/`up` for environment variable changes
- Use volume mounts for configuration overrides
- Verify container networking and hostname resolution

### 4. Systematic Troubleshooting
- Start with infrastructure issues before application issues
- Use logs to identify specific error messages
- Test each fix incrementally

## Recommendations for Production

1. **Documentation**: Create deployment checklist based on lessons learned
2. **Automation**: Implement CI/CD pipeline that handles environment variable management
3. **Testing**: Create staging environment tests that validate all critical paths
4. **Monitoring**: Implement proper logging and monitoring for production deployment
5. **Backup Strategy**: Ensure database backups are configured before production deployment

## Technical Debt

1. **Laravel Version**: Consider upgrading from Laravel 5.1 to a supported version
2. **Docker Configuration**: Standardize Docker Compose configurations across environments
3. **Environment Management**: Implement proper secrets management for production
4. **Testing**: Add automated tests for infrastructure and application functionality

---

**Report Generated**: August 19, 2025  
**Environment**: Staging  
**Status**: Infrastructure Fixed, Application Loading Successfully
