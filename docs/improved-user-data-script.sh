#!/bin/bash
set -euo pipefail

# Improved User Data Script for Talent Assessment Deployment
# Based on lessons learned from troubleshooting deployment issues

# Logging function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a /var/log/user-data.log
}

# Error handling function
error_exit() {
    log "ERROR: $1"
    exit 1
}

# Start logging
log "Starting user data script execution"

# Update system packages
log "Updating system packages"
apt-get update || error_exit "Failed to update packages"

# Install Docker and Docker Compose
log "Installing Docker and Docker Compose"
apt-get install -y docker.io docker-compose || error_exit "Failed to install Docker"

# Add ubuntu user to docker group
log "Adding ubuntu user to docker group"
usermod -aG docker ubuntu || error_exit "Failed to add user to docker group"

# Create required directories
log "Creating required directories"
mkdir -p /opt/talent-assessment /etc/traefik || error_exit "Failed to create directories"

# Copy application files (assuming they're in the user data or will be copied later)
log "Setting up application directory"
cd /opt/talent-assessment || error_exit "Failed to change to application directory"

# Create Traefik network
log "Creating Traefik network"
docker network create traefik-net || log "Traefik network may already exist"

# Set up ACME storage for SSL certificates
log "Setting up ACME storage for SSL certificates"
touch /etc/traefik/acme.json || error_exit "Failed to create acme.json"
chmod 600 /etc/traefik/acme.json || error_exit "Failed to set acme.json permissions"

# Start Traefik container
log "Starting Traefik container"
docker run -d --name traefik --restart unless-stopped \
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
  --api.insecure=true || error_exit "Failed to start Traefik"

# Wait for Traefik to be ready
log "Waiting for Traefik to be ready"
sleep 10

# Verify Traefik is running
if ! docker ps | grep -q traefik; then
    error_exit "Traefik container is not running"
fi

log "Traefik is running successfully"

# Set environment variable for docker-compose
export APP_URL=talent-aws.cyberworldbuilders.dev
log "Set APP_URL to $APP_URL"

# Verify required files exist before starting application
log "Verifying required files"
if [ ! -f "docker-compose.yml" ]; then
    error_exit "docker-compose.yml not found"
fi

if [ ! -f "Dockerfile" ]; then
    error_exit "Dockerfile not found"
fi

if [ ! -f ".env" ]; then
    error_exit ".env file not found"
fi

# Create docker/apache.conf if it doesn't exist
if [ ! -f "docker/apache.conf" ]; then
    log "Creating docker/apache.conf"
    mkdir -p docker
    cat > docker/apache.conf << 'EOF'
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
EOF
fi

# Build and start the application
log "Building and starting application with docker-compose"
docker-compose build --no-cache || error_exit "Failed to build application"
docker-compose up -d || error_exit "Failed to start application"

# Wait for application to be ready
log "Waiting for application to be ready"
sleep 30

# Verify application container is running
if ! docker ps | grep -q talent-assessment; then
    error_exit "Application container is not running"
fi

# Copy .env file to container if needed
log "Setting up Laravel environment"
docker cp .env talent-assessment-app:/var/www/.env || log "Warning: Could not copy .env file"

# Generate APP_KEY and set permissions
log "Generating Laravel APP_KEY"
docker exec talent-assessment-app bash -c "
    cd /var/www && 
    php artisan key:generate && 
    chown www-data:www-data .env && 
    chmod 644 .env
" || log "Warning: Could not generate APP_KEY"

# Fix storage permissions
log "Setting up Laravel storage permissions"
docker exec talent-assessment-app bash -c "
    mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache &&
    chown -R www-data:www-data storage bootstrap/cache &&
    chmod -R 775 storage bootstrap/cache
" || log "Warning: Could not set storage permissions"

# Test application health
log "Testing application health"
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8001 | grep -q "200\|302"; then
    log "Application is responding correctly"
else
    log "Warning: Application may not be responding correctly"
fi

# Test Traefik routing
log "Testing Traefik routing"
if curl -s -I -H "Host: $APP_URL" -k https://localhost | grep -q "HTTP"; then
    log "Traefik routing is working"
else
    log "Warning: Traefik routing may not be working"
fi

# Final verification
log "Final verification of deployment"
docker ps | grep -E "(traefik|talent-assessment)" || error_exit "Not all containers are running"

log "Deployment completed successfully!"
log "Traefik dashboard available at: http://localhost:8080"
log "Application should be available at: https://$APP_URL"

# Create a status file for external monitoring
cat > /opt/talent-assessment/deployment-status.txt << EOF
Deployment completed: $(date)
Traefik: $(docker ps | grep traefik | wc -l) containers running
Application: $(docker ps | grep talent-assessment | wc -l) containers running
APP_URL: $APP_URL
EOF

log "User data script execution completed"
