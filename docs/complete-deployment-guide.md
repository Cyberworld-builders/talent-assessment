# Complete Deployment Guide

This guide provides comprehensive instructions for building and deploying the Talent Assessment application across all environments: **Development**, **Staging**, and **Production**.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Development Environment](#development-environment)
3. [Staging Environment](#staging-environment)
4. [Production Environment](#production-environment)
5. [Troubleshooting](#troubleshooting)
6. [Environment Comparison](#environment-comparison)

## Prerequisites

### Required Software
- **Docker** (20.10+)
- **Docker Compose** (2.0+)
- **Git** (2.0+)
- **AWS CLI** (2.0+) - for staging/production
- **jq** - for JSON processing

### Installation Commands
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y docker.io docker-compose git awscli jq

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### AWS Setup (Staging/Production)
```bash
# Configure AWS credentials
aws configure

# Or use OIDC (recommended for CI/CD)
# See: docs/aws-oidc-setup.md
```

## Development Environment

### Quick Start
```bash
# Clone repository
git clone <repository-url>
cd talent-assessment

# Start development environment
docker-compose up -d

# Install dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Start development server
docker-compose exec -d app php artisan serve --host=0.0.0.0 --port=8000
```

### Development Services
- **Application**: http://localhost:8000
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

### Development Commands
```bash
# View logs
docker-compose logs -f

# Execute commands in container
docker-compose exec app php artisan <command>

# Run tests
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit

# Rebuild containers
docker-compose down
docker-compose up -d --build

# Access container shell
docker-compose exec app bash
```

### Development Configuration
The development environment uses:
- **File**: `docker-compose.yml`
- **Environment**: `.env` (local development)
- **Database**: `talent_assessment`
- **Debug**: `true`
- **Hot Reloading**: Enabled via volume mounts

## Staging Environment

### Automated Deployment (Recommended)
```bash
# Create and push staging tag
git tag v1.x.x-staging
git push origin v1.x.x-staging

# GitHub Actions will automatically:
# 1. Run tests
# 2. Build Docker image
# 3. Push to ECR
# 4. Deploy to staging server
# 5. Run health checks
```

### Manual Deployment
```bash
# 1. Build and push image
docker build -f Dockerfile -t talent-assessment-app:staging .
aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 068732175988.dkr.ecr.us-east-2.amazonaws.com
docker tag talent-assessment-app:staging 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-staging
docker push 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-staging

# 2. Deploy to server
ssh ubuntu@<staging-server-ip>
cd /opt/talent-assessment

# 3. Update environment variables
export STAGING_APP_IMAGE="068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-staging"

# 4. Deploy services
docker-compose -f docker-compose.staging.yml pull
docker-compose -f docker-compose.staging.yml up -d --force-recreate

# 5. Run migrations and clear cache
docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan migrate --force
docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan cache:clear
docker-compose -f docker-compose.staging.yml exec -T app-staging php artisan config:clear
```

### Staging Services
- **Application**: https://talent-staging.cyberworldbuilders.dev
- **Database**: `talent_assessment_staging`
- **Redis**: With password authentication
- **S3**: Staging bucket for file uploads

### Staging Configuration
- **File**: `docker-compose.staging.yml`
- **Environment**: `.env.staging`
- **Image**: ECR-hosted Docker image
- **Domain**: talent-staging.cyberworldbuilders.dev

## Production Environment

### Automated Deployment (Recommended)
```bash
# Create and push production release tag
git tag v1.x.x-release
git push origin v1.x.x-release

# GitHub Actions will automatically:
# 1. Run comprehensive tests
# 2. Build production Docker image
# 3. Push to ECR
# 4. Deploy to production server
# 5. Run health checks
# 6. Clean up resources
```

### Manual Deployment
```bash
# 1. Build and push image
docker build -f Dockerfile -t talent-assessment-app:production .
aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 068732175988.dkr.ecr.us-east-2.amazonaws.com
docker tag talent-assessment-app:production 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-release
docker push 068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-release

# 2. Deploy to server
ssh ubuntu@<production-server-ip>
cd /opt/talent-assessment

# 3. Update environment variables
export PRODUCTION_APP_IMAGE="068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-release"

# 4. Deploy services
docker-compose -f docker-compose.production.yml pull
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d

# 5. Run migrations and clear cache
docker-compose -f docker-compose.production.yml exec -T app-production php artisan migrate --force
docker-compose -f docker-compose.production.yml exec -T app-production php artisan cache:clear
docker-compose -f docker-compose.production.yml exec -T app-production php artisan config:clear
```

### Production Services
- **Application**: https://my.involvedtalent.com
- **Database**: `talent_assessment_production`
- **Redis**: Without password (internal network)
- **S3**: Production bucket with CloudFront CDN
- **SES**: Email service for notifications

### Production Configuration
- **File**: `docker-compose.production.yml`
- **Environment**: `.env.production`
- **Image**: ECR-hosted Docker image
- **Domain**: my.involvedtalent.com
- **SSL**: Let's Encrypt via Traefik

## Troubleshooting

### Common Issues

#### 1. Container Won't Start
```bash
# Check logs
docker-compose logs <service-name>

# Check container status
docker-compose ps

# Restart specific service
docker-compose restart <service-name>
```

#### 2. Database Connection Issues
```bash
# Check database container
docker-compose exec mysql mysql -u root -p

# Verify environment variables
docker-compose exec app env | grep DB_

# Test connection
docker-compose exec app php artisan tinker
# Then run: DB::connection()->getPdo();
```

#### 3. Permission Issues
```bash
# Fix Laravel permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# Fix file permissions
sudo chown -R $USER:$USER .
```

#### 4. Memory Issues
```bash
# Increase PHP memory limit
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit

# Check container memory usage
docker stats
```

#### 5. Image Pull Issues
```bash
# Re-authenticate with ECR
aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 068732175988.dkr.ecr.us-east-2.amazonaws.com

# Pull latest images
docker-compose -f docker-compose.staging.yml pull
```

### Health Checks

#### Development
```bash
# Check application
curl -I http://localhost:8000

# Check database
docker-compose exec mysql mysqladmin ping -h localhost -u root -p

# Check Redis
docker-compose exec redis redis-cli ping
```

#### Staging/Production
```bash
# Check application
curl -I https://talent-staging.cyberworldbuilders.dev
curl -I https://my.involvedtalent.com

# Check services
docker-compose -f docker-compose.staging.yml ps
docker-compose -f docker-compose.production.yml ps

# Check logs
docker-compose -f docker-compose.staging.yml logs --tail=50 app-staging
```

## Environment Comparison

| Feature | Development | Staging | Production |
|---------|-------------|---------|------------|
| **Docker Compose File** | `docker-compose.yml` | `docker-compose.staging.yml` | `docker-compose.production.yml` |
| **Environment File** | `.env` | `.env.staging` | `.env.production` |
| **Image Source** | Local build | ECR | ECR |
| **Domain** | localhost:8000 | talent-staging.cyberworldbuilders.dev | my.involvedtalent.com |
| **Database** | talent_assessment | talent_assessment_staging | talent_assessment_production |
| **Debug Mode** | true | false | false |
| **SSL** | No | Yes (Let's Encrypt) | Yes (Let's Encrypt) |
| **Redis Auth** | No | Yes | No |
| **S3 Bucket** | N/A | Staging bucket | Production bucket |
| **Email Service** | Log | SES | SES |
| **Deployment** | Manual | Automated (GitHub Actions) | Automated (GitHub Actions) |

## Quick Reference Commands

### Development
```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Rebuild
docker-compose up -d --build

# Run tests
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit
```

### Staging
```bash
# Deploy
git tag v1.x.x-staging && git push origin v1.x.x-staging

# Manual deploy
docker-compose -f docker-compose.staging.yml up -d --force-recreate
```

### Production
```bash
# Deploy
git tag v1.x.x-release && git push origin v1.x.x-release

# Manual deploy
docker-compose -f docker-compose.production.yml up -d
```

## Support

For additional help:
- **Documentation**: Check the `docs/` directory
- **Issues**: Create GitHub issues for bugs
- **Deployment Scripts**: Use `deploy-staging.sh` and `deploy-production.sh`
- **Health Monitoring**: Check application logs and service status

---

**Last Updated**: September 2024
**Version**: 1.0
