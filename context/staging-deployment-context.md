# Staging Deployment Context

This document provides a comprehensive overview of the current codebase infrastructure and configuration to support implementing staging deployment.

## Table of Contents
- [Docker Configuration](#docker-configuration)
- [Docker Compose Setup](#docker-compose-setup)
- [Traefik Configuration](#traefik-configuration)
- [Infrastructure Stack](#infrastructure-stack)
- [Terraform Infrastructure](#terraform-infrastructure)
- [GitHub Actions Workflows](#github-actions-workflows)
- [AWS Configuration](#aws-configuration)
- [Development vs Staging Separation](#development-vs-staging-separation)
- [Deployment Scripts](#deployment-scripts)
- [Security Considerations](#security-considerations)

## Docker Configuration

### Production Dockerfile (`Dockerfile`)
```dockerfile
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    wget \
    build-essential \
    python

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Node.js 6.17.1
RUN wget https://nodejs.org/dist/v6.17.1/node-v6.17.1-linux-x64.tar.xz \
    && tar -xf node-v6.17.1-linux-x64.tar.xz \
    && mv node-v6.17.1-linux-x64 /opt/nodejs \
    && ln -sf /opt/nodejs/bin/node /usr/local/bin/node \
    && ln -sf /opt/nodejs/bin/npm /usr/local/bin/npm \
    && rm node-v6.17.1-linux-x64.tar.xz

# Set working directory
WORKDIR /var/www

# Disable Composer plugins when running as root in CI/containers
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_PLUGINS=1

# Copy composer files
COPY composer.json ./

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-plugins --no-scripts --no-autoloader

# Copy application files
COPY . .

# Create storage directories and set permissions
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

RUN touch /var/www/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/storage/logs/laravel.log \
    && chmod 666 /var/www/storage/logs/laravel.log

# Install Node.js dependencies and build frontend assets
RUN npm install && npm run gulp

# Generate autoloader and optimize
RUN composer dump-autoload --no-plugins --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www

# Configure Apache
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 8000
EXPOSE 8000

# Start Apache
CMD ["apache2-foreground"]
```

### Development Dockerfile (`Dockerfile.dev`)
```dockerfile
FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    wget \
    build-essential \
    python

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js 6.17.1
RUN wget https://nodejs.org/dist/v6.17.1/node-v6.17.1-linux-x64.tar.xz \
    && tar -xf node-v6.17.1-linux-x64.tar.xz \
    && mv node-v6.17.1-linux-x64 /opt/nodejs \
    && ln -sf /opt/nodejs/bin/node /usr/local/bin/node \
    && ln -sf /opt/nodejs/bin/npm /usr/local/bin/npm \
    && rm node-v6.17.1-linux-x64.tar.xz

# Set working directory
WORKDIR /var/www

# Disable Composer plugins when running as root in CI/containers
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_PLUGINS=1

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create storage directories and set permissions
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

RUN touch /var/www/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/storage/logs/laravel.log \
    && chmod 666 /var/www/storage/logs/laravel.log

# Configure Apache
RUN a2enmod rewrite

# Copy Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 8000
EXPOSE 8000

# Start Apache
CMD ["apache2-foreground"]
```

**Key Differences:**
- Development Dockerfile doesn't copy application files or install dependencies
- Development Dockerfile doesn't build frontend assets
- Development Dockerfile doesn't generate autoloader
- Development Dockerfile uses volume mounts for hot reloading

### Apache Configuration (`docker/apache.conf`)
```apache
<VirtualHost *:8000>
    DocumentRoot /var/www/public
    <Directory /var/www/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog /var/log/apache2/error.log
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>

Listen 8000
```

## Docker Compose Setup

### Development Compose (`docker-compose.yml`)
```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.dev
    container_name: talent-assessment-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - .:/var/www
      - ./storage:/var/www/storage
    ports:
      - "8001:8000"
    env_file:
      - .env
    depends_on:
      - mysql
      - redis
    networks:
      - talent-network
      - traefik-net
    labels:
      - "traefik.enable=true"
      - "traefik.docker.network=traefik-net"
      - "traefik.http.routers.talent-assessment.entrypoints=websecure"
      - "traefik.http.routers.talent-assessment.rule=Host(`talent-aws.cyberworldbuilders.dev`)"
      - "traefik.http.routers.talent-assessment.tls=true"
      - "traefik.http.routers.talent-assessment.tls.certresolver=letsencrypt"
      - "traefik.http.services.talent-assessment.loadbalancer.server.port=8000"

  mysql:
    image: mysql:8.0
    container_name: talent-assessment-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: talent_assessment
      MYSQL_USER: talent_user
      MYSQL_PASSWORD: talent_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database/migrations:/docker-entrypoint-initdb.d
    networks:
      - talent-network

  redis:
    image: redis:7-alpine
    container_name: talent-assessment-redis
    restart: unless-stopped
    volumes:
      - redis_data:/data
    networks:
      - talent-network

volumes:
  mysql_data:
  redis_data:

networks:
  talent-network:
    driver: bridge
  traefik-net:
    external: true
```

### Production Compose (`infrastructure/docker-compose.prod.yml`)
```yaml
version: "3.9"

services:
  traefik:
    image: traefik:v2.10
    container_name: traefik
    restart: unless-stopped
    command:
      - "--providers.docker"
      - "--entrypoints.web.address=:80"
      - "--entrypoints.websecure.address=:443"
      - "--api.dashboard=true"
      - "--api.insecure=true"
      - "--certificatesresolvers.letsencrypt.acme.email=admin@cyberworldbuilders.dev"
      - "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"
      - "--certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web"
    ports:
      - "80:80"
      - "443:443"
      - "8080:8080"  # Traefik dashboard
    volumes:
      - "/var/run/docker.sock:/var/run/docker.sock:ro"
      - "letsencrypt:/letsencrypt"
    networks:
      - traefik-net
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.traefik.entrypoints=web"
      - "traefik.http.routers.traefik.rule=Host(`traefik.cyberworldbuilders.dev`)"
      - "traefik.http.routers.traefik.service=api@internal"

  app:
    build:
      context: /opt/talent-assessment
      dockerfile: Dockerfile
    container_name: talent-assessment-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./storage:/var/www/storage
      - ./public:/var/www/public
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_URL=https://talent.cyberworldbuilders.dev
      - DB_HOST=mysql
      - DB_DATABASE=talent_assessment
      - DB_USERNAME=talent_user
      - DB_PASSWORD=talent_password
      - REDIS_HOST=redis
      - REDIS_PORT=6379
      - CACHE_DRIVER=redis
      - SESSION_DRIVER=redis
      - QUEUE_CONNECTION=redis
    depends_on:
      - mysql
      - redis
    networks:
      - talent-network
      - traefik-net
    labels:
      - "traefik.enable=true"
      - "traefik.docker.network=traefik-net"
      - "traefik.http.routers.talent-assessment.entrypoints=websecure"
      - "traefik.http.routers.talent-assessment.rule=Host(`talent.cyberworldbuilders.dev`)"
      - "traefik.http.routers.talent-assessment.tls=true"
      - "traefik.http.routers.talent-assessment.tls.certresolver=letsencrypt"
      - "traefik.http.services.talent-assessment.loadbalancer.server.port=8000"
      - "traefik.http.middlewares.talent-assessment-redirect.redirectscheme.scheme=https"
      - "traefik.http.routers.talent-assessment-http.entrypoints=web"
      - "traefik.http.routers.talent-assessment-http.rule=Host(`talent.cyberworldbuilders.dev`)"
      - "traefik.http.routers.talent-assessment-http.middlewares=talent-assessment-redirect"

  mysql:
    image: mysql:8.0
    container_name: talent-assessment-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: talent_assessment
      MYSQL_USER: talent_user
      MYSQL_PASSWORD: talent_password
      MYSQL_ROOT_PASSWORD: root_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database/migrations:/docker-entrypoint-initdb.d
    networks:
      - talent-network
    command: --default-authentication-plugin=mysql_native_password

  redis:
    image: redis:7-alpine
    container_name: talent-assessment-redis
    restart: unless-stopped
    volumes:
      - redis_data:/data
    networks:
      - talent-network
    command: redis-server --appendonly yes

  # Optional: Add a backup service
  backup:
    image: alpine:latest
    container_name: talent-assessment-backup
    restart: "no"
    volumes:
      - mysql_data:/backup/mysql
      - ./backups:/backup/local
    networks:
      - talent-network
    command: |
      sh -c "
        apk add --no-cache mysql-client
        mysqldump -h mysql -u talent_user -ptalent_password talent_assessment > /backup/local/mysql_backup_$(date +%Y%m%d_%H%M%S).sql
        echo 'Backup completed at $(date)'
      "

volumes:
  mysql_data:
  redis_data:
  letsencrypt:

networks:
  talent-network:
    driver: bridge
  traefik-net:
    driver: bridge
```

## Traefik Configuration

### Current Setup
- **Version**: Traefik v2.10
- **Dashboard**: Enabled on port 8080
- **SSL**: Let's Encrypt with HTTP challenge
- **Email**: admin@cyberworldbuilders.dev
- **Storage**: `/letsencrypt/acme.json`

### Labels Configuration
**Development:**
- Domain: `talent-aws.cyberworldbuilders.dev`
- Entrypoint: `websecure`
- TLS: Enabled with Let's Encrypt resolver

**Production:**
- Domain: `talent.cyberworldbuilders.dev`
- Entrypoint: `websecure`
- TLS: Enabled with Let's Encrypt resolver
- HTTP to HTTPS redirect: Enabled

### Networks
- `talent-network`: Internal bridge network for app services
- `traefik-net`: External network for Traefik communication

## Infrastructure Stack

### Services
1. **App Service** (Laravel PHP 7.4 + Apache)
   - Port: 8000 (internal), 8001 (dev external)
   - Environment: Development/Production
   - Dependencies: MySQL, Redis

2. **MySQL Database** (MySQL 8.0)
   - Database: `talent_assessment`
   - User: `talent_user`
   - Password: `talent_password`
   - Root Password: `root_password`
   - Volume: `mysql_data`

3. **Redis Cache** (Redis 7-alpine)
   - Port: 6379
   - Volume: `redis_data`
   - Persistence: Append-only file

4. **Traefik Reverse Proxy**
   - Ports: 80, 443, 8080 (dashboard)
   - SSL: Let's Encrypt
   - Load Balancing: Docker provider

5. **Backup Service** (Optional)
   - MySQL backup automation
   - Local storage in `./backups`

### Resource Separation
**Current State:**
- Single environment setup (development)
- Shared database and Redis instances
- No environment-specific resource separation

**Staging Requirements:**
- Separate database instances
- Separate Redis instances
- Environment-specific S3 buckets
- Different domain names
- Separate CloudFront distributions

## Terraform Infrastructure

### Current Infrastructure (`infrastructure/main.tf`)

#### VPC and Networking
```hcl
# VPC Configuration
resource "aws_vpc" "dev_vpc" {
  cidr_block           = var.vpc_cidr  # 10.0.0.0/16
  enable_dns_support   = true
  enable_dns_hostnames = true
}

# Public Subnet
resource "aws_subnet" "dev_subnet" {
  vpc_id                  = aws_vpc.dev_vpc.id
  cidr_block              = var.subnet_cidr  # 10.0.1.0/24
  map_public_ip_on_launch = true
  availability_zone       = var.availability_zone  # us-east-1a
}
```

#### Security Groups
```hcl
resource "aws_security_group" "dev_sg" {
  # HTTP (80)
  # HTTPS (443)
  # SSH (22) - Currently open to 0.0.0.0/0
  # Traefik Dashboard (8080)
  # All outbound traffic allowed
}
```

#### EC2 Instance
```hcl
resource "aws_instance" "dev_instance" {
  ami                    = data.aws_ami.ubuntu.id  # Ubuntu 22.04 LTS
  instance_type          = var.instance_type  # t3.small
  subnet_id              = aws_subnet.dev_subnet.id
  vpc_security_group_ids = [aws_security_group.dev_sg.id]
  associate_public_ip_address = true
  key_name               = aws_key_pair.dev_key.key_name
  iam_instance_profile   = aws_iam_instance_profile.dev_profile.name
  user_data_base64       = base64encode(templatefile("user_data.sh", { domain = var.domain_name }))
}
```

#### S3 and CloudFront
```hcl
# S3 Bucket for uploads
resource "aws_s3_bucket" "uploads_bucket" {
  bucket = "${var.project_name}-${var.environment}-uploads-${random_string.bucket_suffix.result}"
}

# CloudFront Distribution
resource "aws_cloudfront_distribution" "uploads_distribution" {
  enabled             = true
  is_ipv6_enabled     = true
  default_root_object = "index.html"
  price_class         = "PriceClass_100"  # North America and Europe
}
```

#### IAM Configuration
```hcl
# EC2 Role with S3 permissions
resource "aws_iam_role" "dev_role" {
  # Assume role policy for EC2
}

resource "aws_iam_role_policy" "s3_uploads_policy" {
  # S3: GetObject, PutObject, DeleteObject, ListBucket
  # Resource: S3 bucket ARN
}
```

### Variables (`infrastructure/variables.tf`)
```hcl
variable "aws_region" {
  default = "us-east-1"
}

variable "instance_type" {
  default = "t3.small"
}

variable "environment" {
  default = "development"
}

variable "project_name" {
  default = "talent-assessment"
}

variable "domain_name" {
  default = "talent.cyberworldbuilders.dev"
}
```

### Outputs (`infrastructure/outputs.tf`)
- Public IP address
- Instance ID
- VPC and subnet IDs
- Security group ID
- SSH command
- Application URL
- Traefik dashboard URL
- S3 bucket name and region
- CloudFront domain and distribution ID
- Deployment summary

## GitHub Actions Workflows

### Test Workflow (`.github/workflows/tests.yml`)
```yaml
name: Tests

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - uses: actions/checkout@v4
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, mysqlnd, zip, gd, curl, fileinfo, openssl, tokenizer, json, bcmath, dom, filter, hash, libxml, session, simplexml, spl, xmlreader, xmlwriter, phar, iconv, curl, fileinfo, openssl, tokenizer, json, bcmath, dom, filter, hash, libxml, session, simplexml, spl, xmlreader, xmlwriter, phar
        coverage: none

    - name: Install MySQL Client
      run: sudo apt-get update && sudo apt-get install -y mysql-client

    - name: Setup Testing Environment
      run: |
        cp .env.example .env.testing
        sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql_testing/' .env.testing
        sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env.testing
        sed -i 's/DB_DATABASE=.*/DB_DATABASE=testing/' .env.testing
        sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env.testing
        sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=password/' .env.testing

    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache

    - name: Clear All Caches
      run: |
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        composer dump-autoload

    - name: Wait for MySQL
      run: |
        echo "Waiting for MySQL to be ready..."
        while ! mysqladmin ping -h"127.0.0.1" -P"3306" -uroot -ppassword --silent; do
          sleep 1
        done
        echo "MySQL is ready!"

    - name: Setup Environment for Artisan
      run: cp .env.testing .env

    - name: Generate key
      run: php artisan key:generate

    - name: Run Migrations
      run: php artisan migrate --force

    - name: Execute tests (via phpunit)
      run: vendor/bin/phpunit --colors=never
```

## AWS Configuration

### Laravel AWS Configuration (`config/aws.php`)
```php
<?php

use Aws\Laravel\AwsServiceProvider;

return [
    // IAM Role credentials will be automatically loaded from instance metadata
    // No need for long-lived access keys when using IAM roles
    'region' => env('AWS_REGION', 'us-west-2'),
    'version' => 'latest',
    'ua_append' => [
        'L5MOD/' . AwsServiceProvider::VERSION,
    ],
];
```

### Filesystem Configuration (`config/filesystems.php`)
```php
's3' => [
    'driver' => 's3',
    // IAM Role credentials will be automatically loaded
    'region' => env('AWS_REGION', 'us-east-2'),
    'bucket' => env('AWS_S3_BUCKET', 'talent-assessment-development-uploads'),
],
```

### Environment Variables Required
- `AWS_REGION`: AWS region (default: us-east-2)
- `AWS_S3_BUCKET`: S3 bucket name for uploads
- `AWS_CLOUDFRONT_DOMAIN`: CloudFront distribution domain

## Development vs Staging Separation

### Current State
- Single environment setup
- Shared resources between dev and staging
- No environment-specific configurations

### Staging Requirements
1. **Separate Infrastructure**
   - Different VPC/subnet (optional)
   - Separate EC2 instances
   - Different security groups
   - Separate S3 buckets
   - Different CloudFront distributions

2. **Database Separation**
   - Separate MySQL instances
   - Different database names
   - Separate Redis instances

3. **Domain Separation**
   - Development: `talent-aws.cyberworldbuilders.dev`
   - Staging: `talent-staging.cyberworldbuilders.dev`
   - Production: `talent.cyberworldbuilders.dev`

4. **Environment Variables**
   - `APP_ENV`: staging
   - `APP_DEBUG`: false
   - `APP_URL`: https://talent-staging.cyberworldbuilders.dev
   - Separate database credentials
   - Separate S3 bucket names

## Deployment Scripts

### Infrastructure Deployment (`infrastructure/deploy.sh`)
- Prerequisites checking (Terraform, AWS CLI, SSH keys)
- Configuration validation
- Terraform initialization and planning
- Infrastructure deployment
- Output summary

### User Data Script (`infrastructure/user_data.sh`)
- EC2 instance initialization
- Docker and Docker Compose installation
- Application deployment
- Service startup

### Key Features
- Error handling and colored output
- Prerequisites validation
- AWS credentials verification
- SSH key management
- Terraform state management

## Security Considerations

### Current Security Setup
1. **SSH Access**
   - Currently open to 0.0.0.0/0 (needs restriction)
   - Uses key-based authentication
   - IAM role for EC2 instance

2. **Network Security**
   - HTTP/HTTPS ports open
   - Traefik dashboard on port 8080
   - All outbound traffic allowed

3. **S3 Security**
   - Public access blocked
   - CloudFront-only access via OAI
   - Versioning enabled

4. **Database Security**
   - Default passwords (needs improvement)
   - No SSL/TLS encryption
   - Network isolation via Docker

### Staging Security Requirements
1. **SSH Access Restriction**
   - Limit to specific IP ranges
   - Use AWS Systems Manager Session Manager
   - Implement bastion host if needed

2. **Database Security**
   - Strong, unique passwords
   - SSL/TLS encryption
   - Network access controls

3. **Application Security**
   - Environment-specific secrets
   - HTTPS enforcement
   - Security headers

4. **Monitoring and Logging**
   - CloudWatch integration
   - Application logging
   - Security event monitoring

## Next Steps for Staging Implementation

1. **Create Staging Terraform Configuration**
   - Copy and modify existing infrastructure
   - Add environment-specific variables
   - Create separate resource naming

2. **Update Docker Compose for Staging**
   - Create `docker-compose.staging.yml`
   - Environment-specific configurations
   - Separate service names

3. **Implement Environment Separation**
   - Separate database instances
   - Different S3 buckets
   - Environment-specific domains

4. **Add Staging GitHub Actions**
   - Staging deployment workflow
   - Environment-specific testing
   - Automated staging updates

5. **Security Hardening**
   - Restrict SSH access
   - Implement proper secrets management
   - Add monitoring and alerting

6. **Documentation Updates**
   - Staging deployment guide
   - Environment management procedures
   - Troubleshooting documentation
