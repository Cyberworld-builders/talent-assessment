# Workspace Context for AI Assistants

This document provides essential context about the Talent Assessment application workspace to help AI assistants understand the project structure, constraints, and development environment.

## Project Overview

**Application**: Talent Assessment Platform (AOE Science Platform)  
**Framework**: Laravel 5.1 (Legacy Version)  
**PHP Version**: 7.4  
**Database**: MySQL 8.0  
**Cache**: Redis 7  
**Environment**: Docker-based development with staging/production deployments

## Critical Framework Constraints

### Laravel 5.1 Limitations
- **No `php artisan test` command**: This version predates Laravel's built-in testing command
- **Testing**: Use `vendor/bin/phpunit` directly instead
- **No `UploadedFile` class**: Use alternative file upload testing methods
- **Limited modern Laravel features**: Many modern Laravel features are not available

### PHP Version Constraints
- **PHP 7.4**: Some modern PHP features may not be available
- **Legacy syntax**: Code may use older PHP patterns

## Development Environment

### Docker Setup
- **Primary container**: `talent-assessment-app` (PHP 7.4 + Apache)
- **Database**: `talent-assessment-mysql` (MySQL 8.0)
- **Cache**: `talent-assessment-redis` (Redis 7)
- **Access**: Application runs on `http://localhost:8001`

### Key Commands

#### Development Environment
```bash
# Run tests (NOT php artisan test)
docker-compose exec app vendor/bin/phpunit

# Run specific test file
docker-compose exec app vendor/bin/phpunit tests/UserCsvUploadTest.php

# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan <command>

# View logs
docker-compose logs app

# Check container status
docker-compose ps
```

#### Staging Environment
```bash
# Run tests
docker-compose -f docker-compose.staging.yml exec app-staging vendor/bin/phpunit

# Access container shell
docker-compose -f docker-compose.staging.yml exec app-staging bash

# Run artisan commands
docker-compose -f docker-compose.staging.yml exec app-staging php artisan <command>

# View logs
docker-compose -f docker-compose.staging.yml logs app-staging

# Check container status
docker-compose -f docker-compose.staging.yml ps
```

#### Production Environment
```bash
# Access container shell (BE VERY CAREFUL!)
docker-compose -f docker-compose.production.yml exec app-production bash

# Run artisan commands (BE VERY CAREFUL!)
docker-compose -f docker-compose.production.yml exec app-production php artisan <command>

# View logs
docker-compose -f docker-compose.production.yml logs app-production

# Check container status
docker-compose -f docker-compose.production.yml ps
```

## File Upload System

### Current Implementation
- **User bulk uploads**: Now uses CSV format (converted from Excel)
- **Excel library**: `maatwebsite/excel` v2.1 (legacy version)
- **File validation**: Accepts `.csv` and `.txt` files
- **Processing**: Uses native PHP `fgetcsv()` function

### Upload Endpoints
- **User uploads**: `POST /dashboard/users/upload`
- **Controller**: `UsersController@upload_from_file`
- **Views**: 
  - `/resources/views/dashboard/users/create-multiple.blade.php`
  - `/resources/views/clientdashboard/addapplicants.blade.php`

## Testing Guidelines

### Test Structure
- **Test directory**: `/tests/`
- **Test base class**: `TestCase` (Laravel 5.1 style)
- **Available traits**: `DatabaseTransactions`, `WithoutMiddleware`
- **Test runner**: PHPUnit 4.8.36

### Testing Best Practices
- Use `DatabaseTransactions` for database tests
- Disable CSRF middleware for API tests
- Test CSV parsing logic directly rather than file uploads
- Use temporary files for file-based tests

## Codebase Structure

### Key Directories
- **Controllers**: `/app/Http/Controllers/`
- **Models**: `/app/` (Eloquent models)
- **Views**: `/resources/views/`
- **Routes**: `/app/Http/routes.php`
- **Tests**: `/tests/`
- **Config**: `/config/`

### Important Files
- **Composer**: `composer.json` (Laravel 5.1, PHP 7.4)
- **Docker**: `docker-compose.yml`
- **Environment**: `.env` files for different environments
- **Documentation**: Multiple README files for different aspects

## Environment-Specific Information

### Development Environment
- **URL**: `http://localhost:8001`
- **Domain**: `talent-aws.cyberworldbuilders.dev`
- **Compose file**: `docker-compose.yml`
- **Container prefix**: `talent-assessment-*`
- **Database**: Local MySQL container (`talent-assessment-mysql`)
- **Cache**: Local Redis container (`talent-assessment-redis`)
- **File storage**: Local filesystem
- **Commands**: `docker-compose exec app <command>`

### Staging Environment
- **URL**: `https://talent-staging.cyberworldbuilders.dev`
- **Compose file**: `docker-compose.staging.yml`
- **Container prefix**: `talent-assessment-*-staging`
- **Deployment**: Automated via GitHub Actions
- **Database**: Separate MySQL instance (`talent-assessment-mysql-staging`)
- **Cache**: Separate Redis instance (`talent-assessment-redis-staging`)
- **File storage**: AWS S3
- **Commands**: `docker-compose -f docker-compose.staging.yml exec app-staging <command>`

### Production Environment
- **URL**: `https://talent.cyberworldbuilders.dev`
- **Compose file**: `docker-compose.production.yml`
- **Container prefix**: `talent-assessment-*-production`
- **Deployment**: Manual via SSH
- **Database**: Production MySQL instance (`talent-assessment-mysql-production`)
- **Cache**: Production Redis instance (`talent-assessment-redis-production`)
- **File storage**: AWS S3 with CloudFront
- **Commands**: `docker-compose -f docker-compose.production.yml exec app-production <command>`
- **⚠️ WARNING**: Be extremely careful with production commands!

## Common Issues and Solutions

### Testing Issues
- **Problem**: `php artisan test` not found
- **Solution**: Use `vendor/bin/phpunit` directly

### File Upload Issues
- **Problem**: Excel file processing errors
- **Solution**: Use CSV format instead (current implementation)

### Docker Issues
- **Problem**: Container not responding
- **Solution**: Check container status with `docker-compose ps`

### Environment-Specific Issues
- **Problem**: Wrong environment commands being used
- **Solution**: Always specify the correct compose file with `-f` flag for staging/production
- **Problem**: Production commands executed accidentally
- **Solution**: Always double-check environment before running production commands

### Database Issues
- **Problem**: Connection failures
- **Solution**: Ensure MySQL container is running and accessible

## Docker Compose Environment Management

### Environment-Specific Compose Files
- **Development**: `docker-compose.yml` (default)
- **Staging**: `docker-compose.staging.yml`
- **Production**: `docker-compose.production.yml`

### Container Naming Conventions
- **Development**: `talent-assessment-app`, `talent-assessment-mysql`, `talent-assessment-redis`
- **Staging**: `talent-assessment-app-staging`, `talent-assessment-mysql-staging`, `talent-assessment-redis-staging`
- **Production**: `talent-assessment-app-production`, `talent-assessment-mysql-production`, `talent-assessment-redis-production`

### Environment Switching Best Practices
1. **Always specify compose file** for staging/production: `-f docker-compose.staging.yml`
2. **Check current environment** before running commands
3. **Use environment-specific container names** in commands
4. **Be extra careful with production** - always double-check commands
5. **Test in development first** before applying to staging/production

### Environment Isolation
- Each environment has separate containers and databases
- Staging and production use different S3 buckets
- Environment-specific environment variables and configurations
- Separate deployment processes for each environment

## Development Workflow

### Making Changes
1. Make code changes in the workspace
2. Test using `docker-compose exec app vendor/bin/phpunit`
3. Test manually in browser at `http://localhost:8001`
4. Commit changes to git

### Environment Variables
- **Development**: Use `.env` file
- **Staging**: Environment variables in GitHub Actions
- **Production**: Environment variables in Docker containers

## Security Considerations

### File Uploads
- **Validation**: File type and size validation
- **Processing**: Server-side CSV parsing
- **Storage**: Local filesystem or AWS S3

### Database
- **Credentials**: Stored in environment variables
- **Access**: Containerized with network isolation
- **Backups**: Automated backup system available

## Documentation References

- **Main README**: `/readme.md`
- **Environment Setup**: `/ENVIRONMENT_SETUP.md`
- **Deployment**: `/DEPLOYMENT.md`
- **Staging Context**: `/context/staging-deployment-context.md`
- **Docker Setup**: `/README-Docker.md`

## Recent Changes

### CSV Upload Implementation (Current Session)
- **Changed**: User bulk uploads from Excel to CSV format
- **Files Modified**:
  - `app/Http/Controllers/UsersController.php`
  - `resources/views/dashboard/users/create-multiple.blade.php`
  - `resources/views/clientdashboard/addapplicants.blade.php`
- **Reason**: Excel library requires updated PHP/Laravel versions
- **Testing**: Created `tests/UserCsvUploadTest.php`

## AI Assistant Guidelines

### When Working on This Project
1. **Always check Laravel version** before suggesting modern Laravel features
2. **Use Docker commands** for running tests and artisan commands
3. **Test CSV functionality** using the provided test file
4. **Check existing documentation** before making assumptions
5. **Consider legacy constraints** when suggesting solutions
6. **Always specify the correct environment** when providing Docker commands
7. **Be extra cautious with production commands** - always warn users
8. **Use environment-specific compose files** for staging/production

### Testing Recommendations
- Test CSV parsing logic directly rather than file uploads
- Use `DatabaseTransactions` for database tests
- Create temporary files for file-based tests
- Verify functionality in browser after code changes

### Environment-Specific Testing
- **Development**: Use `docker-compose exec app vendor/bin/phpunit`
- **Staging**: Use `docker-compose -f docker-compose.staging.yml exec app-staging vendor/bin/phpunit`
- **Production**: Avoid running tests in production unless absolutely necessary
- **Always test in development first** before deploying to other environments

### Code Style
- Follow Laravel 5.1 conventions
- Use existing patterns in the codebase
- Maintain backward compatibility
- Document any breaking changes

---

**Last Updated**: January 2025  
**Maintained By**: Development Team  
**Version**: 1.0
