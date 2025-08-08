# 🚀 Quick Start Guide

This guide will help you deploy the Talent Assessment application on AWS EC2 with Docker Compose and Traefik in under 10 minutes.

## ⚡ Prerequisites (5 minutes)

### 1. Install Required Tools

**macOS:**
```bash
# Install Homebrew if you don't have it
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Terraform
brew install terraform

# Install AWS CLI
brew install awscli
```

**Ubuntu/Debian:**
```bash
# Install Terraform
curl -fsSL https://apt.releases.hashicorp.com/gpg | sudo apt-key add -
sudo apt-add-repository "deb [arch=amd64] https://apt.releases.hashicorp.com $(lsb_release -cs) main"
sudo apt-get update && sudo apt-get install terraform

# Install AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
```

### 2. Configure AWS Credentials

```bash
# Configure AWS CLI with your sandbox profile
aws configure sso

# Test your credentials
aws sts get-caller-identity
```

### 3. Create SSH Key Pair

```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -f ~/.ssh/dev-key -N ""
```

## 🚀 Deploy Infrastructure (5 minutes)

### 1. Navigate to Infrastructure Directory

```bash
cd infrastructure
```

### 2. Update Configuration

Edit `terraform.tfvars` and update:
- `aws_profile`: Your AWS CLI profile name
- `domain_name`: Your domain name

### 3. Run Deployment Script

```bash
# Deploy infrastructure
./deploy.sh

# Or manually:
terraform init
terraform plan
terraform apply
```

### 4. Verify Deployment

After successful deployment, you'll see:
- Public IP address
- SSH access command
- Application URLs

## 🌐 Access Your Application

### Web Application
- **URL**: `http://<public-ip>`
- **Domain**: Update your DNS to point to the public IP

### Traefik Dashboard
- **URL**: `http://<public-ip>:8080`

### SSH Access
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
```

## 📦 Deploy Your Laravel Application

### 1. SSH into the Instance

```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
```

### 2. Upload Your Application

**Option A: Using SCP**
```bash
# From your local machine
scp -r -i ~/.ssh/dev-key /path/to/your/laravel/app ec2-user@<public-ip>:/opt/talent-assessment/
```

**Option B: Using Git**
```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Navigate to app directory
cd /opt/talent-assessment

# Clone your repository
git clone https://github.com/your-username/talent-assessment.git .

# Or pull latest changes
git pull origin main
```

### 3. Configure Environment

```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Navigate to app directory
cd /opt/talent-assessment

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Start Application

```bash
# Navigate to app directory
cd /opt/talent-assessment

# Start Docker Compose
docker-compose up -d

# Check status
docker-compose ps
```

## 🔧 Post-Deployment Configuration

### 1. Update DNS Records

Point your domain to the EC2 public IP:
- **A Record**: `talent.cyberworldbuilders.dev` → `<public-ip>`

### 2. Configure SSL (Optional)

The production docker-compose file includes Let's Encrypt SSL configuration. To use it:

```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Navigate to app directory
cd /opt/talent-assessment

# Use production docker-compose file
cp docker-compose.prod.yml docker-compose.yml

# Restart services
docker-compose down
docker-compose up -d
```

### 3. Set Up Monitoring

```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Check application logs
docker-compose logs -f app

# Check system resources
df -h
free -h
```

## 🛠️ Management Commands

### View Logs
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
cd /opt/talent-assessment
docker-compose logs -f
```

### Restart Services
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
cd /opt/talent-assessment
docker-compose restart
```

### Update Application
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
cd /opt/talent-assessment
git pull
docker-compose up -d --build
```

### Backup Database
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
cd /opt/talent-assessment
docker-compose run --rm backup
```

## 🗑️ Cleanup

To destroy the infrastructure:

```bash
cd infrastructure
./deploy.sh destroy
```

**⚠️ Warning**: This will permanently delete all resources and data.

## 🆘 Troubleshooting

### Common Issues

1. **SSH Connection Failed**
   ```bash
   # Check if key exists
   ls -la ~/.ssh/dev-key*
   
   # Check instance status
   aws ec2 describe-instances --instance-ids <instance-id>
   ```

2. **Application Not Loading**
   ```bash
   # SSH into instance
   ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
   
   # Check Docker containers
   docker ps
   
   # Check application logs
   docker-compose logs app
   ```

3. **Terraform Errors**
   ```bash
   # Check AWS credentials
   aws sts get-caller-identity
   
   # Check Terraform state
   terraform show
   ```

### Get Help

- Check the full [README.md](README.md) for detailed documentation
- Review AWS CloudTrail logs for authentication issues
- Check Terraform state: `terraform show`

## 🎉 Success!

Your Laravel application is now running on AWS EC2 with:
- ✅ Docker Compose orchestration
- ✅ Traefik reverse proxy
- ✅ MySQL database
- ✅ Redis cache
- ✅ SSL support (when configured)
- ✅ Automatic health checks

**Next Steps:**
1. Configure your domain DNS
2. Set up SSL certificates
3. Configure monitoring and alerts
4. Implement backup strategies
5. Set up CI/CD pipeline
