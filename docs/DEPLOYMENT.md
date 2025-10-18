# Deployment Guide

This document provides comprehensive instructions for deploying the Talent Assessment application across different environments.

## Table of Contents
- [Environment Overview](#environment-overview)
- [Prerequisites](#prerequisites)
- [Development Environment](#development-environment)
- [Staging Environment](#staging-environment)
- [Production Environment](#production-environment)
- [Secret Management](#secret-management)
- [Troubleshooting](#troubleshooting)
- [Monitoring and Logs](#monitoring-and-logs)

## Environment Overview

### Development Environment
- **Domain**: `talent-aws.cyberworldbuilders.dev`
- **Purpose**: Local development with hot reloading
- **Deployment**: Manual via SSH/IDE edits
- **Resources**: Shared with staging on same EC2 instance

### Staging Environment
- **Domain**: `talent-staging.cyberworldbuilders.dev`
- **Purpose**: Pre-production testing and validation
- **Deployment**: Automated via GitHub Actions
- **Resources**: Separate containers, databases, and S3 bucket

### Production Environment
- **Domain**: `my.involvedtalent.com`
- **Purpose**: Live production environment
- **Deployment**: Automated via GitHub Actions (tag-based)
- **Resources**: Dedicated infrastructure with ECR images

## Prerequisites

### Required Tools
- Docker and Docker Compose
- AWS CLI configured with appropriate credentials
- SSH access to EC2 instance
- GitHub repository access

### AWS Resources
- EC2 instance with IAM role
- ECR repository for container images
- Secrets Manager for environment secrets
- S3 buckets for file uploads
- CloudFront distributions for CDN

### GitHub Secrets & Variables
The following secrets and variables must be configured in your GitHub repository:

#### GitHub Variables
- `AWS_ROLE_ARN`: IAM role ARN for OIDC authentication
- `AWS_REGION`: AWS region (e.g., `us-east-2`)

#### GitHub Secrets
- `EC2_HOST`: EC2 instance public IP or domain
- `EC2_USER`: SSH username (usually `ubuntu`)
- `EC2_SSH_KEY`: Private SSH key for EC2 access
- `MAILTRAP_USERNAME`: Mailtrap username for testing
- `MAILTRAP_PASSWORD`: Mailtrap password for testing

#### AWS Secrets Manager
All environment-specific secrets are stored in AWS Secrets Manager:
- **Staging**: `talent-assessment-staging-secrets`
- **Production**: `talent-assessment-production-secrets`

## Development Environment

### Local Setup
1. Clone the repository
2. Copy `.env.example` to `.env` and configure
3. Run development environment:
   ```bash
   docker-compose up -d
   ```

### Development Workflow
- Code changes are reflected immediately via volume mounts
- Database migrations run automatically
- Hot reloading enabled for rapid iteration

### Accessing Development
- Application: http://localhost:8001
- Traefik Dashboard: http://localhost:8080

## Staging Environment

### Initial Setup

#### 1. Deploy Infrastructure
```bash
cd infrastructure
terraform init
terraform plan
terraform apply
```

#### 2. Configure DNS
Add A record for `talent-staging.cyberworldbuilders.dev` pointing to the EC2 instance public IP.

#### 3. Set Up GitHub Secrets
Configure all required secrets in your GitHub repository settings.

### Staging Deployment Process

#### Automatic Deployment (Recommended)
1. Create a feature branch from `main`
2. Make your changes and commit
3. Create a pull request to the `main` branch
4. GitHub Actions will run tests on the PR
5. Merge the PR to `main`
6. Create a staging tag: `git tag v1.x.x-staging && git push origin v1.x.x-staging`
7. GitHub Actions will automatically deploy to staging
8. Monitor the deployment in GitHub Actions

#### Manual Deployment
```bash
# SSH to EC2 instance
ssh -i ~/.ssh/dev-key ubuntu@<EC2_IP>

# Navigate to project directory
cd /opt/talent-assessment

# Fetch secrets and generate .env.staging
aws secretsmanager get-secret-value --secret-id talent-assessment-staging-secrets --query SecretString --output text | jq -r > secrets.json

# Generate .env.staging file
cat > .env.staging << EOF
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://talent-staging.cyberworldbuilders.dev
APP_KEY=base64:$(openssl rand -base64 32)

DB_CONNECTION=mysql
DB_HOST=mysql-staging
DB_PORT=3306
DB_DATABASE=$(jq -r '.STAGING_DB_DATABASE' secrets.json)
DB_USERNAME=$(jq -r '.STAGING_DB_USERNAME' secrets.json)
DB_PASSWORD=$(jq -r '.STAGING_DB_PASSWORD' secrets.json)

REDIS_HOST=redis-staging
REDIS_PORT=6379
REDIS_PASSWORD=$(jq -r '.STAGING_REDIS_PASSWORD' secrets.json)

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

AWS_REGION=us-east-1
AWS_S3_BUCKET=$(jq -r '.STAGING_S3_BUCKET' secrets.json)
EOF

# Set environment variables
export STAGING_DB_DATABASE=$(jq -r '.STAGING_DB_DATABASE' secrets.json)
export STAGING_DB_USERNAME=$(jq -r '.STAGING_DB_USERNAME' secrets.json)
export STAGING_DB_PASSWORD=$(jq -r '.STAGING_DB_PASSWORD' secrets.json)
export STAGING_DB_ROOT_PASSWORD=$(jq -r '.STAGING_DB_ROOT_PASSWORD' secrets.json)
export STAGING_REDIS_PASSWORD=$(jq -r '.STAGING_REDIS_PASSWORD' secrets.json)

# Clean up
rm secrets.json

# Build and deploy
docker-compose -f docker-compose.staging.yml up -d --build
```

### Staging Environment Management

#### Viewing Logs
```bash
# Application logs
docker-compose -f docker-compose.staging.yml logs app-staging

# Database logs
docker-compose -f docker-compose.staging.yml logs mysql-staging

# Redis logs
docker-compose -f docker-compose.staging.yml logs redis-staging

# All logs
docker-compose -f docker-compose.staging.yml logs -f
```

#### Restarting Services
```bash
# Restart specific service
docker-compose -f docker-compose.staging.yml restart app-staging

# Restart all staging services
docker-compose -f docker-compose.staging.yml restart
```

#### Scaling Services
```bash
# Scale application (if needed)
docker-compose -f docker-compose.staging.yml up -d --scale app-staging=2
```

## Production Environment

### Production Deployment Process

#### Automatic Deployment (Recommended)
1. Ensure all changes are merged to `main` branch
2. Create a production release tag: `git tag v1.x.x-release && git push origin v1.x.x-release`
3. GitHub Actions will automatically:
   - Run tests
   - Build and push Docker image to ECR
   - Deploy to production with the new image
4. Monitor the deployment in GitHub Actions

#### Manual Deployment
```bash
# SSH to EC2 instance
ssh -i ~/.ssh/dev-key ubuntu@<EC2_IP>

# Navigate to project directory
cd /opt/talent-assessment

# Update image tag (if needed)
export PRODUCTION_APP_IMAGE=068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-release

# Deploy production
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d
```

### Image Tag Management
Production uses Docker images from ECR with specific tags:
- **Image Format**: `068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.x.x-release`
- **Environment Variable**: `PRODUCTION_APP_IMAGE`
- **Update Script**: `./scripts/update-image-tags.sh production v1.x.x-release`

#### Managing Docker Images
```bash
# Update production image tag
./scripts/update-image-tags.sh production v1.3.6-release

# Update staging image tag
./scripts/update-image-tags.sh staging v1.3.6-staging

# Check current image tags
grep -E "(PRODUCTION_APP_IMAGE|STAGING_APP_IMAGE)" .env.production .env.staging
```

#### Image Tag Workflow
1. **Development**: Code changes are made and tested locally
2. **CI/CD**: GitHub Actions builds and pushes images with specific tags
3. **Deployment**: Workflows automatically update environment files with new image tags
4. **Verification**: Containers use the correct image with all fixes included

## Secret Management

### AWS Secrets Manager

#### Current Secrets Structure
```json
{
  "STAGING_DB_PASSWORD": "strong_staging_db_pass_<random>",
  "STAGING_REDIS_PASSWORD": "strong_staging_redis_pass_<random>",
  "STAGING_S3_BUCKET": "talent-assessment-staging-uploads-<random>",
  "STAGING_DB_DATABASE": "talent_assessment_staging",
  "STAGING_DB_USERNAME": "talent_user_staging",
  "STAGING_DB_ROOT_PASSWORD": "strong_staging_root_pass_<random>"
}
```

#### Rotating Secrets

##### Database Passwords
1. Generate new passwords:
   ```bash
   openssl rand -base64 32
   ```

2. Update Secrets Manager:
   ```bash
   aws secretsmanager update-secret \
     --secret-id talent-assessment-staging-secrets \
     --secret-string '{
       "STAGING_DB_PASSWORD": "new_password_here",
       "STAGING_REDIS_PASSWORD": "new_redis_password",
       "STAGING_S3_BUCKET": "talent-assessment-staging-uploads-<random>",
       "STAGING_DB_DATABASE": "talent_assessment_staging",
       "STAGING_DB_USERNAME": "talent_user_staging",
       "STAGING_DB_ROOT_PASSWORD": "new_root_password"
     }'
   ```

3. Redeploy staging environment to pick up new secrets

##### S3 Bucket Rotation
1. Create new S3 bucket via Terraform
2. Update Secrets Manager with new bucket name
3. Migrate data from old bucket to new bucket
4. Update application configuration

#### Adding New Secrets
1. Add secret to AWS Secrets Manager
2. Update deployment scripts to fetch new secret
3. Update `.env.staging` generation in GitHub Actions
4. Test deployment with new secret

### GitHub Secrets Rotation

#### Rotating SSH Keys
1. Generate new SSH key pair
2. Update EC2 instance with new public key
3. Update GitHub secret `EC2_SSH_KEY` with new private key
4. Test deployment

#### Rotating AWS Credentials
1. Create new IAM user with required permissions
2. Generate new access keys
3. Update GitHub secrets `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY`
4. Test deployment
5. Delete old access keys

## Troubleshooting

### Common Issues

#### Wrong Docker Image Tag
**Symptoms**: Application behaves unexpectedly, missing recent fixes
**Solution**:
```bash
# Check current image tag
docker-compose -f docker-compose.production.yml exec app-production env | grep PRODUCTION_APP_IMAGE

# Update to correct image tag
./scripts/update-image-tags.sh production v1.3.5-release
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d
```

#### ECR Authentication Issues
**Symptoms**: Docker pull fails with authentication error
**Solution**:
```bash
# Re-authenticate with ECR
aws ecr get-login-password --region us-east-2 | docker login --username AWS --password-stdin 068732175988.dkr.ecr.us-east-2.amazonaws.com
```

#### Secrets Manager Access Issues
**Symptoms**: Deployment fails when fetching secrets
**Solution**:
1. Verify EC2 IAM role has `secretsmanager:GetSecretValue` permission
2. Check secret ARN in IAM policy
3. Verify secret exists in correct region

#### Database Connection Issues
**Symptoms**: Application can't connect to database
**Solution**:
1. Check database container is running:
   ```bash
   docker-compose -f docker-compose.staging.yml ps mysql-staging
   ```
2. Verify database credentials in `.env.staging`
3. Check network connectivity between containers

#### Traefik Routing Issues
**Symptoms**: Domain not accessible or SSL certificate issues
**Solution**:
1. Check Traefik is running:
   ```bash
   docker ps | grep traefik
   ```
2. Verify domain DNS points to correct IP
3. Check Traefik logs:
   ```bash
   docker logs traefik
   ```
4. Verify Let's Encrypt certificate generation

#### Container Resource Issues
**Symptoms**: Containers failing to start or running slowly
**Solution**:
1. Check available disk space:
   ```bash
   df -h
   ```
2. Check available memory:
   ```bash
   free -h
   ```
3. Clean up unused Docker resources:
   ```bash
   docker system prune -a
   ```

#### Session/Login Issues
**Symptoms**: Users redirected to login page repeatedly, session not persisting
**Solution**:
1. Check session configuration in container:
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production cat /var/www/config/session.php | grep -A 2 -B 2 "domain\|secure"
   ```
2. Verify correct image is being used:
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production env | grep PRODUCTION_APP_IMAGE
   ```
3. Copy updated config files to container:
   ```bash
   docker cp config/session.php talent-assessment-app-production:/var/www/config/session.php
   docker cp bootstrap/app.php talent-assessment-app-production:/var/www/bootstrap/app.php
   docker-compose -f docker-compose.production.yml exec app-production php artisan config:clear
   ```

#### Email Sending Issues
**Symptoms**: "Whoops" error when sending emails, email notifications not working
**Solution**:
1. Check AWS region configuration:
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production env | grep AWS_REGION
   ```
2. Verify MAIL_FROM_ADDRESS is set:
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production env | grep MAIL_FROM_ADDRESS
   ```
3. Check AWS deprecation warning suppression:
   ```bash
   docker-compose -f docker-compose.production.yml exec app-production grep -n "AWS_SUPPRESS_PHP_DEPRECATION_WARNING" /var/www/bootstrap/app.php
   ```

### Debugging Commands

#### Check Service Status
```bash
# All containers
docker ps -a

# Staging services
docker-compose -f docker-compose.staging.yml ps

# Service logs
docker-compose -f docker-compose.staging.yml logs <service-name>
```

#### Check Network Connectivity
```bash
# Test database connectivity
docker exec talent-assessment-app-staging mysql -h mysql-staging -u talent_user_staging -p

# Test Redis connectivity
docker exec talent-assessment-app-staging redis-cli -h redis-staging -a <password>
```

#### Check Application Health
```bash
# Application logs
docker logs talent-assessment-app-staging

# Check application response
curl -I https://talent-staging.cyberworldbuilders.dev
```

### Rollback Procedures

#### Rollback to Previous Image
```bash
# SSH to EC2
ssh -i ~/.ssh/dev-key ubuntu@<EC2_IP>

# Navigate to project
cd /opt/talent-assessment

# Update image tag to previous version
./scripts/update-image-tags.sh production v1.3.4-release  # or previous working version

# Redeploy
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d
```

#### Quick Rollback (Emergency)
```bash
# Stop services immediately
docker-compose -f docker-compose.production.yml down

# Start with previous image tag
export PRODUCTION_APP_IMAGE=068732175988.dkr.ecr.us-east-2.amazonaws.com/talent-assessment-app:v1.3.4-release
docker-compose -f docker-compose.production.yml up -d
```

#### Emergency Rollback
```bash
# Stop staging services
docker-compose -f docker-compose.staging.yml down

# Start with previous configuration
docker-compose -f docker-compose.staging.yml up -d
```

## Monitoring and Logs

### Application Monitoring
- **Traefik Dashboard**: http://<EC2_IP>:8080
- **Application Logs**: `docker logs talent-assessment-app-staging`
- **Database Logs**: `docker logs talent-assessment-mysql-staging`

### Health Checks
- **Application**: https://talent-staging.cyberworldbuilders.dev
- **Database**: Check container status and connectivity
- **Redis**: Check container status and connectivity

### Log Management
- Application logs are stored in Docker containers
- Consider implementing log aggregation for production
- Monitor disk space for log files

### Performance Monitoring
- Monitor EC2 instance CPU and memory usage
- Monitor Docker container resource usage
- Monitor database performance and connection pool
- Monitor Redis memory usage and hit rates

## Security Best Practices

### Network Security
- SSH access is restricted to specific IP ranges
- All external traffic goes through Traefik with SSL
- Internal services communicate over Docker networks

### Secret Security
- All secrets stored in AWS Secrets Manager
- No secrets committed to version control
- Regular secret rotation procedures

### Container Security
- Images scanned on push to ECR
- Regular security updates for base images
- Minimal attack surface with Alpine-based images

### Access Control
- IAM roles with least privilege principle
- Separate credentials for different environments
- Regular access key rotation

## Support and Maintenance

### Regular Maintenance Tasks
1. **Weekly**: Check for security updates
2. **Monthly**: Review and rotate secrets
3. **Quarterly**: Review and update documentation
4. **As needed**: Scale resources based on usage

### Emergency Contacts
- **Infrastructure Issues**: AWS Support
- **Application Issues**: Development Team
- **Security Issues**: Security Team

### Documentation Updates
- Update this document when procedures change
- Keep runbooks current with actual deployment steps
- Document any custom configurations or workarounds
