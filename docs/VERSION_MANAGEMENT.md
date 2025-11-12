# Version Management and Deployment Guide

This document outlines the version management and deployment process for the Involved Talent application.

## 📋 Version Numbering Convention

### Version Format
- **Development**: `v1.5.28-dev`
- **Staging**: `v1.5.28-staging` 
- **Production**: `v1.5.28-release`

### Version Increment Rules
- **Major** (1.x.x): Breaking changes, major feature releases
- **Minor** (x.5.x): New features, significant improvements
- **Patch** (x.x.28): Bug fixes, minor improvements, maintenance

## 🚀 Deployment Process

### 1. Development Environment

#### Create Development Version
```bash
# 1. Update changelog in dashboard/index.blade.php
# 2. Update .env.dev with new version
# 3. Create and push dev tag
git tag v1.5.28-dev
git push origin v1.5.28-dev

# 4. Build and tag Docker image
docker build -t talent-assessment-app:v1.5.28-dev .
docker tag talent-assessment-app:v1.5.28-dev 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.5.28-dev
docker push 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.5.28-dev

# 5. Update .env.dev with new version
# 6. Restart development environment
docker-compose down
docker-compose up -d
```

### 2. Staging Environment

#### Create Staging Version
```bash
# 1. Copy dev tag to staging
git tag v1.5.28-staging v1.5.28-dev
git push origin v1.5.28-staging

# 2. Deploy to staging (automated via GitHub Actions)
# The staging deployment will be triggered by the tag push
```

### 3. Production Environment

#### Create Production Version
```bash
# 1. Copy dev tag to release
git tag v1.5.28-release v1.5.28-dev
git push origin v1.5.28-release

# 2. Deploy to production (automated via GitHub Actions)
# The production deployment will be triggered by the tag push
```

## 📝 Changelog Management

### Dashboard Changelog Format
The changelog is displayed in `resources/views/dashboard/index.blade.php` with the following format:

```html
<ul class="list-unstyled changelog">
    <li>
        <i class="fa-asterisk text-success"></i>
        <p><strong>v1.5.28 - Version Title:</strong> Brief description of the update.</p>
    </li>
    <li>
        <i class="fa-asterisk text-info"></i>
        <p>Specific feature or fix description.</p>
    </li>
    <!-- Additional items... -->
</ul>
```

### Changelog Icons
- **text-success** (green): Major features, important updates
- **text-info** (blue): Minor features, improvements
- **text-warning** (yellow): Bug fixes, maintenance
- **text-danger** (red): Critical fixes, security updates

## 🔧 Environment Configuration

### Development Environment
- **File**: `.env.dev`
- **Version Variable**: `APP_VERSION=v1.5.28-dev`
- **Docker Compose**: `docker-compose.yml`
- **Restart Command**: `docker-compose down && docker-compose up -d`

### Staging Environment
- **File**: `.env.staging`
- **Version Variable**: `APP_VERSION=v1.5.28-staging`
- **Docker Compose**: `docker-compose.staging.yml`
- **Deployment**: Automated via GitHub Actions

### Production Environment
- **File**: `.env.production`
- **Version Variable**: `APP_VERSION=v1.5.28-release`
- **Docker Compose**: `docker-compose.production.yml`
- **Deployment**: Automated via GitHub Actions

## 📊 Version History

### v1.5.41 - Assessment Editor UI/UX Improvements & Anchor Management
- **Anchor Persistence**: Fixed anchor value persistence in database by properly handling JSON serialization/deserialization
- **Error Resolution**: Resolved "Array to string conversion" error in assessment editor by using model accessors
- **UI Improvements**: Updated field preview to display anchors as UI elements instead of raw Blade code
- **Modal Fixes**: Fixed modal positioning and z-index issues to prevent backdrop from covering modal content
- **Field Styling**: Improved field numbering styling from toggle-like circular badges to clean rectangular question numbers
- **Interface Cleanup**: Removed unnecessary field type badge that served no functional purpose
- **Layout Enhancement**: Enhanced field header layout with better spacing, alignment, and non-interactive styling

### v1.5.28 - Infrastructure Planning & UI Fixes
- **Infrastructure Planning**: Created comprehensive infrastructure plan for dedicated production environment
- **Login Page Fixes**: Fixed missing logo.png path and added Docker container rules
- **Asset Management**: Updated image paths from `public/images/` to `public/assets/images/`
- **Environment Management**: Improved environment variable handling and Docker container usage
- **Documentation**: Added version management and deployment documentation

### v1.5.27 - Environment Configuration & Bug Fixes
- **Environment Files**: Fixed production and staging environment configurations
- **GitHub Actions**: Fixed MySQL client installation issues in CI/CD pipelines
- **Seeder Fixes**: Corrected Involved360AssessmentSeeder question numbering
- **Security**: Removed sensitive environment files from git tracking

### v1.5.26 - UI/UX Improvements & Asset Management
- **Login Form**: Fixed placeholder text behavior and styling
- **Image Assets**: Moved images to `public/assets/images/` directory
- **Password Reset**: Fixed logo placement and link centering
- **Asset Paths**: Updated all asset references to use correct paths

### v1.5.25 - S3 Configuration & Minor Fixes
- **S3 Integration**: Standardized S3 file uploads across all environments
- **Assessment Forms**: Fixed 500 errors and MathJax CDN warnings
- **Assignment URLs**: Fixed URL generation for consistent link behavior
- **Password Reset**: Improved logo placement and link centering

## 🛠️ Development Workflow

### Before Creating New Version
1. **Review Commits**: Check all commits since last version
2. **Update Changelog**: Add new entries to dashboard changelog
3. **Test Changes**: Verify all changes work in development
4. **Update Documentation**: Update relevant documentation

### Creating New Version
1. **Update Version**: Increment version number in appropriate files
2. **Create Tags**: Create dev, staging, and release tags
3. **Build Images**: Build and push Docker images
4. **Deploy**: Deploy to appropriate environments
5. **Verify**: Test deployment and functionality

### After Deployment
1. **Monitor**: Check application logs and functionality
2. **Test**: Verify all features work correctly
3. **Document**: Update any necessary documentation
4. **Communicate**: Notify team of deployment status

## ⚠️ Important Notes

### Docker Container Usage
- **CRITICAL**: All commands (npm, gulp, composer, etc.) must be run INSIDE Docker containers
- **Development**: Use `docker-compose exec app <command>`
- **Staging**: Use `docker-compose -f docker-compose.staging.yml exec app-staging <command>`
- **Production**: Use `docker-compose -f docker-compose.production.yml exec app-production <command>`

### Environment Variable Changes
- **CRITICAL**: When changing environment variables, ALWAYS take containers down and back up
- **NEVER use `restart`** for environment variable changes - it doesn't work
- **Use `docker-compose down` then `docker-compose up -d`** for environment changes

### Version Tagging
- **Development**: Always create `-dev` tag first
- **Staging**: Copy dev tag to `-staging` tag
- **Production**: Copy dev tag to `-release` tag
- **Never create production tags directly** - always copy from dev

## 📚 Related Documentation

- **Main README**: `/readme.md`
- **Environment Setup**: `/ENVIRONMENT_SETUP.md`
- **Cursor Rules**: `/.cursorrules`
- **Infrastructure Plan**: `/infrastructure/INFRASTRUCTURE_PLAN.md`
- **Development Log**: `/DEV_LOG.md`

## 🔗 Useful Commands

### Version Management
```bash
# Check current version
git describe --tags --always

# List all tags
git tag --sort=-version:refname

# Create new version tag
git tag v1.5.28-dev
git push origin v1.5.28-dev

# Copy tag to staging
git tag v1.5.28-staging v1.5.28-dev
git push origin v1.5.28-staging

# Copy tag to production
git tag v1.5.28-release v1.5.28-dev
git push origin v1.5.28-release
```

### Docker Commands
```bash
# Development
docker-compose down && docker-compose up -d

# Staging
docker-compose -f docker-compose.staging.yml down
docker-compose -f docker-compose.staging.yml up -d

# Production
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d
```

### Environment Updates
```bash
# Update development version
sed -i 's/APP_VERSION=.*/APP_VERSION=v1.5.28-dev/' .env.dev

# Update staging version
sed -i 's/APP_VERSION=.*/APP_VERSION=v1.5.28-staging/' .env.staging

# Update production version
sed -i 's/APP_VERSION=.*/APP_VERSION=v1.5.28-release/' .env.production
```

## ⚡ Deployment Optimization

### Image Existence Check
The GitHub Actions workflows for staging and production deployments have been optimized to check if the Docker image already exists in ECR before building. This provides significant performance benefits:

- **Faster Deployments**: If you've already built and pushed the image locally, the CI/CD pipeline will skip the build step
- **Resource Efficiency**: Avoids redundant Docker builds in CI/CD when the image already exists
- **Cost Savings**: Reduces compute time and resource usage in GitHub Actions

### How It Works
1. The workflow checks if the image with the specified tag exists in ECR
2. If the image exists, the build step is skipped and the existing image is used
3. If the image doesn't exist, a new build is performed as usual
4. This optimization applies to both staging (`*-staging` tags) and production (`*-release` tags) deployments

### Best Practices
- Build and push your Docker images locally before creating deployment tags
- This ensures the fastest possible deployment times
- The optimization is automatic - no additional configuration required
