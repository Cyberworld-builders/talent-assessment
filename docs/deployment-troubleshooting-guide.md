# Deployment Troubleshooting Guide

## Overview
This document details the issues encountered during the deployment of the talent assessment application using Docker Compose with Traefik reverse proxy, and the solutions implemented to resolve them.

## Issues Encountered and Solutions

### 1. User Data Script Deadlock

**Issue**: The cloud-init user data script was deadlocking itself by calling `cloud-init status --wait` from within the cloud-init process.

**Symptoms**:
- No files or processes created by the user data script
- Cloud-init appears to hang or not complete
- Missing `/opt/talent-assessment` directory
- No Docker containers running

**Root Cause**: 
```bash
# This line in user_data.sh caused the deadlock:
cloud-init status --wait
```

**Solution**: 
- Removed the `cloud-init status --wait` call from `infrastructure/user_data.sh`
- Added proper error handling with `set -euo pipefail`

**Fixed Code**:
```bash
#!/bin/bash
set -euo pipefail

# NOTE: Do NOT call 'cloud-init status --wait' from within a cloud-init
# user-data script, as it will deadlock (cloud-init waits on itself).
```

### 2. Docker Not Installed

**Issue**: Docker and Docker Compose were not installed on the server.

**Symptoms**:
- `docker` command not found
- `docker-compose` command not found

**Solution**:
```bash
# Install Docker and Docker Compose
sudo apt update && sudo apt install -y docker.io docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### 3. Missing Traefik Network

**Issue**: The `traefik-net` network required by the docker-compose configuration was not created.

**Solution**:
```bash
# Create the traefik-net network
sudo docker network create traefik-net
```

### 4. Missing ACME Storage for SSL Certificates

**Issue**: Traefik needs persistent storage for Let's Encrypt certificates.

**Solution**:
```bash
# Create ACME storage directory and file
sudo mkdir -p /etc/traefik
sudo touch /etc/traefik/acme.json
sudo chmod 600 /etc/traefik/acme.json
```

### 5. Traefik Not Running

**Issue**: Traefik container was not started with proper configuration.

**Solution**:
```bash
# Start Traefik with proper configuration
sudo docker run -d --name traefik --restart unless-stopped \
  --network traefik-net \
  -p 80:80 -p 443:443 -p 8080:8080 \
  -v /var/run/docker.sock:/var/run/docker.sock:ro \
  -v /etc/traefik/acme.json:/acme.json \
  traefik:v2.10 \
  --providers.docker=true \
  --entrypoints.web.address=:80 \
  --entrypoints.websecure.address=:443 \
  --certificatesresolvers.letsencrypt.acme.email=admin@cyberworldbuilders.dev \
  --certificatesresolvers.letsencrypt.acme.storage=/acme.json \
  --certificatesresolvers.letsencrypt.acme.httpchallenge=true \
  --certificatesresolvers.letsencrypt.acme.httpchallenge.entrypoint=web \
  --api.dashboard=true \
  --api.insecure=true
```

### 6. Missing Docker Configuration Files

**Issue**: Required Docker configuration files were missing.

**Missing Files**:
- `docker/apache.conf` - Apache virtual host configuration
- `composer.lock` - Composer lock file

**Solutions**:

**Apache Configuration** (`docker/apache.conf`):
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

**Dockerfile Updates**:
```dockerfile
# Remove composer.lock requirement
COPY composer.json ./

# Add Composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_PLUGINS=1

# Disable plugins during install and autoload
RUN composer install --no-plugins --no-scripts --no-autoloader
RUN composer dump-autoload --no-plugins --optimize
```

### 7. Laravel Application Issues

**Issue**: Laravel application was returning 500 errors due to missing configuration.

**Symptoms**:
- HTTP 500 errors when accessing the application
- Missing `.env` file in container
- Missing APP_KEY
- Permission issues with storage directories

**Solutions**:

**Copy .env File**:
```bash
sudo docker cp .env talent-assessment-app:/var/www/.env
```

**Generate APP_KEY**:
```bash
sudo docker exec -i talent-assessment-app bash -lc \
  "cd /var/www && php artisan key:generate && chown www-data:www-data .env && chmod 644 .env"
```

**Fix Storage Permissions**:
```bash
sudo docker exec -i talent-assessment-app bash -lc \
  "mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache; \
   chown -R www-data:www-data storage bootstrap/cache; \
   chmod -R 775 storage bootstrap/cache"
```

### 8. Environment Variable Configuration

**Issue**: The `APP_URL` environment variable was not set for docker-compose substitution.

**Solution**:
```bash
# Set APP_URL for docker-compose
export APP_URL=talent-aws.cyberworldbuilders.dev

# Start with environment variable
sudo -E docker-compose up -d
```

### 9. Container Network Issues

**Issue**: Application containers were not properly connected to the Traefik network.

**Solution**: Use docker-compose with proper Traefik labels as defined in `docker-compose.yml`:

```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.talent-assessment.rule=Host(`${APP_URL}`)"
  - "traefik.http.routers.talent-assessment.entrypoints=websecure"
  - "traefik.http.routers.talent-assessment.tls.certresolver=letsencrypt"
  - "traefik.http.services.talent-assessment.loadbalancer.server.port=8000"
```

## Verification Steps

### 1. Check Traefik Status
```bash
# Check if Traefik is running
sudo docker ps | grep traefik

# Check Traefik API
curl -s http://localhost:8080/api/http/routers
```

### 2. Check Application Status
```bash
# Check if application container is running
sudo docker ps | grep talent-assessment

# Test direct access
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8001
```

### 3. Check Traefik Routing
```bash
# Test through Traefik with proper Host header
curl -s -I -H "Host: talent-aws.cyberworldbuilders.dev" -k https://localhost
```

### 4. Check Laravel Logs
```bash
# Check Laravel logs for errors
sudo docker exec -i talent-assessment-app bash -lc \
  "tail -n 50 /var/www/storage/logs/laravel.log"
```

## Final Working Configuration

### Docker Compose Command
```bash
export APP_URL=talent-aws.cyberworldbuilders.dev
sudo -E docker-compose up -d
```

### Required Files
- `docker-compose.yml` - Main orchestration file
- `Dockerfile` - Application container definition
- `docker/apache.conf` - Apache configuration
- `.env` - Laravel environment file

### Network Configuration
- `traefik-net` network for Traefik communication
- Application containers connected to `traefik-net`
- Traefik listening on ports 80, 443, 8080

### SSL Configuration
- Let's Encrypt certificates via HTTP challenge
- ACME storage in `/etc/traefik/acme.json`
- Automatic certificate renewal

## Lessons Learned

1. **Never call `cloud-init status --wait` from within a cloud-init user data script**
2. **Always verify required files exist before building Docker images**
3. **Use docker-compose with proper Traefik labels for reverse proxy setup**
4. **Ensure Laravel has proper permissions and APP_KEY before starting**
5. **Set environment variables for docker-compose substitution**
6. **Verify network connectivity between Traefik and application containers**

## Troubleshooting Checklist

- [ ] Docker and Docker Compose installed
- [ ] Traefik network created
- [ ] ACME storage configured
- [ ] Traefik container running
- [ ] Required files present (apache.conf, .env)
- [ ] APP_URL environment variable set
- [ ] Application container running
- [ ] Laravel APP_KEY generated
- [ ] Storage permissions correct
- [ ] Traefik routing configured
- [ ] SSL certificates working

## Next Steps for User Data Script

1. Remove the `cloud-init status --wait` call
2. Add proper error handling with `set -euo pipefail`
3. Ensure all required files are copied to the instance
4. Add verification steps for each component
5. Include proper logging for troubleshooting
6. Add health checks for Traefik and application containers
