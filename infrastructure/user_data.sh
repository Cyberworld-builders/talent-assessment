#!/bin/bash
set -euo pipefail

# NOTE: Do NOT call 'cloud-init status --wait' from within a cloud-init
# user-data script, as it will deadlock (cloud-init waits on itself).

# Update system packages
apt-get update
apt-get upgrade -y

# Install required packages
apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    software-properties-common

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io

# Start and enable Docker
systemctl enable docker
systemctl start docker

# Install Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Add ubuntu user to docker group
usermod -aG docker ubuntu

# Create application directory
mkdir -p /opt/talent-assessment
cd /opt/talent-assessment

# Set proper ownership
chown -R ubuntu:ubuntu /opt/talent-assessment

# Create docker-compose.yml for the application
cat > docker-compose.yml << 'EOF'
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
    ports:
      - "80:80"
      - "443:443"
      - "8080:8080"  # Traefik dashboard
    volumes:
      - "/var/run/docker.sock:/var/run/docker.sock:ro"
    networks:
      - traefik-net

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
      - APP_URL=https://${domain}
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
      - "traefik.http.routers.talent-assessment.entrypoints=web"
      - "traefik.http.routers.talent-assessment.rule=Host(`${domain}`)"
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

volumes:
  mysql_data:
  redis_data:

networks:
  talent-network:
    driver: bridge
  traefik-net:
    driver: bridge
EOF

# Create a simple test page
cat > index.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Talent Assessment - Development Environment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .status { padding: 20px; background: #f0f0f0; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Talent Assessment - Development Environment</h1>
        
        <div class="status success">
            <h2>✅ Infrastructure Deployed Successfully!</h2>
            <p>Your Laravel application is now running on AWS EC2 with Docker Compose and Traefik.</p>
        </div>
        
        <div class="status info">
            <h3>📋 Next Steps:</h3>
            <ul>
                <li>Upload your Laravel application files to the EC2 instance</li>
                <li>Configure your domain DNS to point to this server</li>
                <li>Set up SSL certificates for HTTPS</li>
                <li>Configure environment variables for production</li>
            </ul>
        </div>
        
        <div class="status">
            <h3>🔧 Available Services:</h3>
            <ul>
                <li><strong>Traefik Dashboard:</strong> <a href="http://${domain}:8080" target="_blank">http://${domain}:8080</a></li>
                <li><strong>Application:</strong> <a href="http://${domain}" target="_blank">http://${domain}</a></li>
            </ul>
        </div>
        
        <div class="status">
            <h3>📊 Server Information:</h3>
            <p><strong>Instance ID:</strong> $(curl -s http://169.254.169.254/latest/meta-data/instance-id)</p>
            <p><strong>Public IP:</strong> $(curl -s http://169.254.169.254/latest/meta-data/public-ipv4)</p>
            <p><strong>Region:</strong> $(curl -s http://169.254.169.254/latest/meta-data/placement/region)</p>
        </div>
    </div>
</body>
</html>
EOF

# Create a simple Dockerfile for testing
cat > Dockerfile << 'EOF'
FROM nginx:alpine

COPY index.html /usr/share/nginx/html/index.html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
EOF

# Start the Docker Compose services
cd /opt/talent-assessment
docker-compose up -d

# Create a simple health check script
cat > /opt/health-check.sh << 'EOF'
#!/bin/bash
echo "Health check at $(date)"
echo "Docker containers:"
docker ps
echo "Disk usage:"
df -h
echo "Memory usage:"
free -h
EOF

chmod +x /opt/health-check.sh

# Add to crontab for periodic health checks
echo "*/5 * * * * /opt/health-check.sh >> /var/log/health-check.log 2>&1" | crontab -

# Log the completion
echo "User data script completed at $(date)" > /var/log/user-data.log