# Deployment Quick Reference

## Pre-Deployment Checklist

- [ ] All required files are in the repository:
  - `docker-compose.yml`
  - `Dockerfile`
  - `docker/apache.conf`
  - `.env`
  - `composer.json`
- [ ] User data script has been updated (remove `cloud-init status --wait`)
- [ ] Domain DNS is configured to point to the server
- [ ] Security groups allow ports 80, 443, 8080

## Manual Deployment Steps (if user data fails)

### 1. Install Docker
```bash
sudo apt update && sudo apt install -y docker.io docker-compose
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Set up Traefik
```bash
# Create network
sudo docker network create traefik-net

# Set up ACME storage
sudo mkdir -p /etc/traefik
sudo touch /etc/traefik/acme.json
sudo chmod 600 /etc/traefik/acme.json

# Start Traefik
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

### 3. Deploy Application
```bash
# Set environment variable
export APP_URL=talent-aws.cyberworldbuilders.dev

# Build and start
sudo -E docker-compose build --no-cache
sudo -E docker-compose up -d
```

### 4. Configure Laravel
```bash
# Copy .env file
sudo docker cp .env talent-assessment-app:/var/www/.env

# Generate APP_KEY
sudo docker exec -i talent-assessment-app bash -c \
  "cd /var/www && php artisan key:generate && chown www-data:www-data .env && chmod 644 .env"

# Fix permissions
sudo docker exec -i talent-assessment-app bash -c \
  "mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache && \
   chown -R www-data:www-data storage bootstrap/cache && \
   chmod -R 775 storage bootstrap/cache"
```

## Verification Commands

### Check Traefik
```bash
# Check if running
sudo docker ps | grep traefik

# Check API
curl -s http://localhost:8080/api/http/routers
```

### Check Application
```bash
# Check if running
sudo docker ps | grep talent-assessment

# Test direct access
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8001

# Test through Traefik
curl -s -I -H "Host: talent-aws.cyberworldbuilders.dev" -k https://localhost
```

### Check Logs
```bash
# Traefik logs
sudo docker logs traefik

# Application logs
sudo docker logs talent-assessment-app

# Laravel logs
sudo docker exec -i talent-assessment-app bash -c \
  "tail -n 50 /var/www/storage/logs/laravel.log"
```

## Common Issues and Solutions

### Issue: 500 Error from Laravel
**Solution**: Check APP_KEY and permissions
```bash
sudo docker exec -i talent-assessment-app bash -c \
  "cd /var/www && php artisan key:generate"
```

### Issue: Traefik not routing
**Solution**: Check network and labels
```bash
sudo docker network inspect traefik-net
curl -s http://localhost:8080/api/http/routers
```

### Issue: SSL certificate not working
**Solution**: Check ACME storage and domain DNS
```bash
sudo ls -la /etc/traefik/acme.json
nslookup talent-aws.cyberworldbuilders.dev
```

### Issue: Container not starting
**Solution**: Check logs and required files
```bash
sudo docker logs talent-assessment-app
ls -la docker/apache.conf
```

## Environment Variables

| Variable | Value | Purpose |
|----------|-------|---------|
| `APP_URL` | `talent-aws.cyberworldbuilders.dev` | Domain for Traefik routing |
| `COMPOSER_ALLOW_SUPERUSER` | `1` | Allow Composer as root |
| `COMPOSER_NO_PLUGINS` | `1` | Disable Composer plugins |

## Ports Used

| Port | Service | Purpose |
|------|---------|---------|
| 80 | Traefik | HTTP traffic |
| 443 | Traefik | HTTPS traffic |
| 8080 | Traefik | Dashboard |
| 8001 | Application | Direct access |

## Files to Monitor

- `/var/log/user-data.log` - User data script logs
- `/var/log/cloud-init-output.log` - Cloud-init logs
- `/etc/traefik/acme.json` - SSL certificates
- `/opt/talent-assessment/deployment-status.txt` - Deployment status
