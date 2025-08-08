# 🚀 Talent Assessment - AWS Infrastructure

This Terraform configuration deploys a complete development environment for the Talent Assessment application on AWS EC2 with Docker Compose and Traefik.

## 📋 Prerequisites

1. **AWS CLI configured** with your sandbox profile
2. **Terraform installed** (version >= 1.0)
3. **SSH key pair** created at `~/.ssh/dev-key.pub`
4. **AWS credentials** properly configured for your sandbox account

## 🔧 Configuration

### 1. Update AWS Profile

Edit `terraform.tfvars` and update the `aws_profile` variable to match your AWS CLI profile:

```hcl
aws_profile = "your-sandbox-profile"
```

### 2. Create SSH Key Pair

If you don't have an SSH key pair, create one:

```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/dev-key -N ""
```

### 3. Update Domain Name

Edit `terraform.tfvars` and update the domain name:

```hcl
domain_name = "your-domain.com"
```

## 🚀 Deployment

### 1. Initialize Terraform

```bash
cd infrastructure
terraform init
```

### 2. Plan the Deployment

```bash
terraform plan
```

### 3. Apply the Configuration

```bash
terraform apply
```

### 4. Verify Deployment

After successful deployment, you'll see output similar to:

```
🚀 Talent Assessment Development Environment Deployed Successfully!

📍 Instance Details:
- Instance ID: i-xxxxxxxxxxxxxxxxx
- Public IP: 52.xx.xx.xx
- Instance Type: t3.small

🌐 Access URLs:
- Application: http://52.xx.xx.xx
- Traefik Dashboard: http://52.xx.xx.xx:8080

🔑 SSH Access:
ssh -i ~/.ssh/dev-key ec2-user@52.xx.xx.xx
```

## 🌐 Accessing Your Application

### Web Application
- **URL**: `http://<public-ip>`
- **Domain**: Update your DNS to point to the public IP

### Traefik Dashboard
- **URL**: `http://<public-ip>:8080`
- **Purpose**: Monitor and manage your application routing

### SSH Access
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
```

## 📁 Infrastructure Components

### Networking
- **VPC**: `10.0.0.0/16` with public subnet `10.0.1.0/24`
- **Internet Gateway**: Enables internet access
- **Route Table**: Routes traffic to internet gateway
- **Security Group**: Allows HTTP (80), HTTPS (443), SSH (22)

### Compute
- **EC2 Instance**: t3.small with Amazon Linux 2
- **Key Pair**: SSH access for administration
- **IAM Role**: Permissions for AWS console access

### Application Stack
- **Docker**: Container runtime
- **Docker Compose**: Multi-container orchestration
- **Traefik**: Reverse proxy and load balancer
- **Laravel**: PHP application (to be deployed)

## 🔧 Post-Deployment Steps

### 1. Upload Your Laravel Application

SSH into the instance and upload your application files:

```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Navigate to application directory
cd /opt/talent-assessment

# Upload your Laravel application files
# (You can use scp, rsync, or git clone)
```

### 2. Configure Domain DNS

Update your domain's DNS settings to point to the EC2 public IP:
- **A Record**: `your-domain.com` → `<public-ip>`

### 3. Set Up SSL Certificates

For HTTPS, configure SSL certificates in Traefik:

```yaml
# In docker-compose.yml, add Let's Encrypt configuration
- "--certificatesresolvers.letsencrypt.acme.email=your-email@domain.com"
- "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"
```

### 4. Configure Environment Variables

Update the application environment variables for production:

```bash
# SSH into the instance
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>

# Edit environment variables
cd /opt/talent-assessment
nano .env
```

## 🛠️ Management Commands

### View Application Logs
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

### Health Check
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
/opt/health-check.sh
```

## 🗑️ Cleanup

To destroy the infrastructure:

```bash
terraform destroy
```

**⚠️ Warning**: This will permanently delete all resources and data.

## 🔒 Security Considerations

### Current Security Settings
- SSH is open to `0.0.0.0/0` for AWS console access
- HTTP and HTTPS are open to all IPs
- No SSL certificates configured by default

### Recommended Security Improvements
1. **Restrict SSH access** to your IP address
2. **Configure SSL certificates** for HTTPS
3. **Set up proper firewall rules**
4. **Use AWS Secrets Manager** for sensitive data
5. **Enable CloudWatch monitoring**

### Update Security Group for Production

Edit the security group in `main.tf`:

```hcl
ingress {
  description = "Allow SSH from your IP only"
  from_port   = 22
  to_port     = 22
  protocol    = "tcp"
  cidr_blocks = ["YOUR.IP.ADDRESS.HERE/32"]
}
```

## 📊 Monitoring

### Health Checks
- Automatic health checks run every 5 minutes
- Logs stored in `/var/log/health-check.log`

### Docker Container Status
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
docker ps
```

### System Resources
```bash
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
df -h  # Disk usage
free -h  # Memory usage
top  # Process monitoring
```

## 🆘 Troubleshooting

### Common Issues

1. **SSH Connection Failed**
   - Verify the key pair exists: `ls -la ~/.ssh/dev-key*`
   - Check security group allows SSH (port 22)
   - Ensure instance is running

2. **Application Not Accessible**
   - Check if Docker containers are running: `docker ps`
   - Verify Traefik is working: `docker logs traefik`
   - Check application logs: `docker logs talent-assessment-app`

3. **Terraform Apply Fails**
   - Verify AWS credentials: `aws sts get-caller-identity`
   - Check if resources already exist
   - Ensure you have sufficient permissions

### Useful Commands

```bash
# Check instance status
aws ec2 describe-instances --instance-ids <instance-id>

# View security group rules
aws ec2 describe-security-groups --group-ids <security-group-id>

# Check user data execution
ssh -i ~/.ssh/dev-key ec2-user@<public-ip>
cat /var/log/user-data.log
```

## 📝 Notes

- This is a **development environment** with minimal security
- For production, implement proper security measures
- Consider using AWS RDS for database instead of Docker MySQL
- Implement proper backup strategies
- Set up monitoring and alerting

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review AWS CloudTrail logs
3. Check Terraform state: `terraform show`
4. Verify resource status in AWS Console
