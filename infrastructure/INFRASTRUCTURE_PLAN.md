# Infrastructure Plan: Dedicated Production Environment

## 📋 Overview

This document outlines the planned infrastructure changes to create a dedicated, highly available production environment for the Talent Assessment application. The new infrastructure will replace the current Docker Compose-based production setup with AWS managed services.

## 🎯 Goals

- **High Availability**: Multi-AZ deployments for critical services
- **Enhanced Security**: Private subnets and strict security groups
- **Scalability**: ECS Fargate with auto-scaling capabilities
- **Cost Optimization**: Pay-as-you-go model with managed services
- **Operational Efficiency**: Reduced maintenance overhead

## 🏗️ Infrastructure Components

### 1. Database Infrastructure (`database.tf`)

#### RDS MySQL Instance
```hcl
resource "aws_db_instance" "talent_assessment_db" {
  identifier              = "talent-assessment-production-db"
  engine                  = "mysql"
  engine_version          = "8.0.35"
  instance_class          = "db.t3.medium"
  allocated_storage       = 100
  max_allocated_storage   = 1000
  db_name                 = "talent_assessment_production"
  username                = "talent_user_production"
  password                = random_password.rds_password.result
  port                    = 3306
  vpc_security_group_ids  = [aws_security_group.rds_sg.id]
  db_subnet_group_name    = aws_db_subnet_group.talent_assessment_db_subnet_group.name
  multi_az                = true
  storage_encrypted       = true
  publicly_accessible     = false
  skip_final_snapshot     = false
  backup_retention_period = 7
  deletion_protection     = true
  monitoring_interval     = 60
  monitoring_role_arn     = aws_iam_role.rds_monitoring_role.arn
  apply_immediately       = false
}
```

**Why**: Replaces the current MySQL container with a managed RDS instance for better reliability, automated backups, and Multi-AZ high availability.

#### RDS Subnet Group
```hcl
resource "aws_db_subnet_group" "talent_assessment_db_subnet_group" {
  name       = "talent-assessment-db-subnet-group"
  subnet_ids = [aws_subnet.private_subnet_1.id, aws_subnet.private_subnet_2.id]
}
```

**Why**: Ensures the database is deployed across multiple availability zones for high availability.

#### RDS Security Group
```hcl
resource "aws_security_group" "rds_sg" {
  name_prefix = "talent-assessment-rds-"
  description = "Allow MySQL access from ECS tasks"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.ecs_sg.id]
    description     = "MySQL access from ECS tasks"
  }
}
```

**Why**: Restricts database access to only ECS tasks, enhancing security by preventing direct internet access.

#### Secrets Management
```hcl
resource "aws_secretsmanager_secret" "rds_credentials" {
  name        = "talent-assessment-rds-credentials"
  description = "RDS database credentials for talent assessment"
}

resource "aws_secretsmanager_secret_version" "rds_credentials" {
  secret_id     = aws_secretsmanager_secret.rds_credentials.id
  secret_string = jsonencode({
    username = aws_db_instance.talent_assessment_db.username,
    password = random_password.rds_password.result,
    engine   = aws_db_instance.talent_assessment_db.engine,
    host     = aws_db_instance.talent_assessment_db.address,
    port     = aws_db_instance.talent_assessment_db.port,
    db_name  = aws_db_instance.talent_assessment_db.db_name
  })
}
```

**Why**: Centralizes credential management and eliminates hardcoded passwords in application code.

### 2. Network Infrastructure (`network.tf`)

#### Private Subnets
```hcl
resource "aws_subnet" "private_subnet_1" {
  vpc_id            = aws_vpc.dev_vpc.id
  cidr_block        = "10.0.10.0/24"
  availability_zone = data.aws_availability_zones.available.names[0]
}

resource "aws_subnet" "private_subnet_2" {
  vpc_id            = aws_vpc.dev_vpc.id
  cidr_block        = "10.0.20.0/24"
  availability_zone = data.aws_availability_zones.available.names[1]
}
```

**Why**: Creates isolated network segments for database and application components, preventing direct internet access.

#### NAT Gateway
```hcl
resource "aws_eip" "nat_gateway_eip" {
  vpc        = true
  depends_on = [aws_internet_gateway.dev_igw]
}

resource "aws_nat_gateway" "talent_assessment_nat" {
  allocation_id = aws_eip.nat_gateway_eip.id
  subnet_id     = aws_subnet.dev_subnet.id
  depends_on    = [aws_internet_gateway.dev_igw]
}
```

**Why**: Enables private subnets to access the internet for updates and external API calls while maintaining security.

#### Application Load Balancer
```hcl
resource "aws_lb" "talent_assessment_alb" {
  name               = "talent-assessment-alb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.alb_sg.id]
  subnets            = [aws_subnet.dev_subnet.id]
}
```

**Why**: Distributes incoming traffic across multiple ECS tasks and provides SSL termination.

#### ALB Target Group
```hcl
resource "aws_lb_target_group" "talent_assessment_tg" {
  name     = "talent-assessment-tg"
  port     = 80
  protocol = "HTTP"
  vpc_id   = aws_vpc.dev_vpc.id
  target_type = "ip"

  health_check {
    enabled  = true
    path     = "/health"
    protocol = "HTTP"
    matcher  = "200"
    interval = 30
    timeout  = 5
    healthy_threshold   = 2
    unhealthy_threshold = 2
  }
}
```

**Why**: Manages health checks and traffic distribution to ECS tasks.

#### SSL Certificate
```hcl
resource "aws_acm_certificate" "talent_assessment_cert" {
  domain_name       = "my.involvedtalent.com"
  validation_method = "DNS"
  subject_alternative_names = ["*.involvedtalent.com"]
}
```

**Why**: Provides SSL/TLS encryption for secure HTTPS communication.

### 3. Security Groups

#### ALB Security Group
```hcl
resource "aws_security_group" "alb_sg" {
  name_prefix = "talent-assessment-alb-"
  description = "Allow HTTP/HTTPS access to ALB"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
    description = "HTTP access from internet"
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
    description = "HTTPS access from internet"
  }
}
```

**Why**: Allows public internet access to the load balancer on standard HTTP/HTTPS ports.

#### ECS Security Group
```hcl
resource "aws_security_group" "ecs_sg" {
  name_prefix = "talent-assessment-ecs-"
  description = "Allow HTTP access from ALB to ECS tasks"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb_sg.id]
    description     = "HTTP access from ALB"
  }
}
```

**Why**: Restricts ECS task access to only the load balancer, preventing direct internet access.

### 4. IAM Roles and Policies

#### ECS Task Role
```hcl
resource "aws_iam_role" "ecs_task_role" {
  name = "talent-assessment-ecs-task-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Action = "sts:AssumeRole",
        Effect = "Allow",
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}
```

**Why**: Provides ECS tasks with permissions to access AWS services like Secrets Manager.

#### ECS Execution Role
```hcl
resource "aws_iam_role" "ecs_execution_role" {
  name = "talent-assessment-ecs-execution-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Action = "sts:AssumeRole",
        Effect = "Allow",
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })
}
```

**Why**: Allows Fargate to pull container images and send logs to CloudWatch.

#### RDS Monitoring Role
```hcl
resource "aws_iam_role" "rds_monitoring_role" {
  name = "talent-assessment-rds-monitoring-role"
  assume_role_policy = jsonencode({
    Version = "2012-10-17",
    Statement = [
      {
        Action = "sts:AssumeRole",
        Effect = "Allow",
        Principal = {
          Service = "monitoring.rds.amazonaws.com"
        }
      }
    ]
  })
}
```

**Why**: Enables enhanced monitoring for RDS instances.

### 5. Configuration Variables (`variables.tf`)

#### Database Configuration
```hcl
variable "db_instance_class" {
  description = "RDS instance class"
  type        = string
  default     = "db.t3.medium"
}

variable "db_allocated_storage" {
  description = "RDS allocated storage in GB"
  type        = number
  default     = 100
}

variable "db_max_allocated_storage" {
  description = "RDS maximum allocated storage in GB"
  type        = number
  default     = 1000
}

variable "db_backup_retention_period" {
  description = "RDS backup retention period in days"
  type        = number
  default     = 7
}
```

**Why**: Provides flexibility to adjust database resources based on requirements.

#### ECS Configuration
```hcl
variable "ecs_cpu" {
  description = "ECS task CPU units (256 = 0.25 vCPU)"
  type        = number
  default     = 1024
}

variable "ecs_memory" {
  description = "ECS task memory in MB"
  type        = number
  default     = 2048
}

variable "ecs_desired_count" {
  description = "Desired number of ECS tasks"
  type        = number
  default     = 2
}
```

**Why**: Allows scaling of application resources based on demand.

### 6. Outputs (`outputs.tf`)

#### Database Outputs
```hcl
output "rds_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.talent_assessment_db.endpoint
}

output "rds_credentials_secret_arn" {
  description = "ARN of the RDS credentials secret"
  value       = aws_secretsmanager_secret.rds_credentials.arn
}
```

**Why**: Provides connection information for application configuration.

#### Load Balancer Outputs
```hcl
output "alb_dns_name" {
  description = "Application Load Balancer DNS name"
  value       = aws_lb.talent_assessment_alb.dns_name
}

output "alb_arn" {
  description = "Application Load Balancer ARN"
  value       = aws_lb.talent_assessment_alb.arn
}
```

**Why**: Provides DNS information for domain configuration.

#### SSL Certificate Outputs
```hcl
output "ssl_certificate_arn" {
  description = "SSL certificate ARN (pending validation)"
  value       = aws_acm_certificate.talent_assessment_cert.arn
}

output "ssl_certificate_validation_records" {
  description = "DNS validation records for SSL certificate"
  value       = aws_acm_certificate.talent_assessment_cert.domain_validation_options
}
```

**Why**: Provides SSL certificate information for DNS validation setup.

## 📊 Cost Estimation

### Monthly Costs (Estimated)
- **RDS MySQL (db.t3.medium, Multi-AZ, 100GB gp3 storage)**: ~$80-100
- **ElastiCache Redis (cache.t3.medium, Multi-AZ)**: ~$30-40
- **ECS Fargate (2 tasks, 1 vCPU, 2GB RAM each, running 24/7)**: ~$20-30
- **Application Load Balancer (ALB)**: ~$20-25
- **NAT Gateway**: ~$30-40 (including data processing)
- **Other (Route53, S3, CloudWatch, etc.)**: ~$5-10

**Total Estimated Monthly Cost: ~$165-245**

## 🔒 Security Benefits

### Network Isolation
- **Private Subnets**: Database and cache in private subnets
- **Security Groups**: Restrictive inbound/outbound rules
- **NAT Gateway**: Controlled internet access for private resources

### Data Security
- **Encryption at Rest**: RDS and S3 encryption enabled
- **Encryption in Transit**: SSL/TLS for all communications
- **Secrets Management**: Database credentials in Secrets Manager

### Access Control
- **IAM Roles**: Least-privilege access for services
- **Resource Isolation**: VPC-based network isolation
- **Monitoring**: Enhanced monitoring for RDS

## 🚀 Deployment Strategy

### Phase 1: Infrastructure Setup
1. Deploy RDS instance in private subnets
2. Create NAT Gateway for private subnet internet access
3. Deploy Application Load Balancer
4. Create security groups and IAM roles
5. Request SSL certificate

### Phase 2: Application Migration
1. Create ECS cluster and task definitions
2. Deploy application containers
3. Configure load balancer target groups
4. Test application connectivity

### Phase 3: DNS and SSL Setup
1. Create DNS records in GoDaddy pointing to ALB
2. Add SSL certificate validation records
3. Test HTTPS functionality
4. Update application configuration

### Phase 4: Cutover
1. Update DNS records to point to new infrastructure
2. Monitor application performance
3. Decommission old Docker Compose environment

## 📋 Prerequisites

- [ ] AWS CLI configured with appropriate permissions
- [ ] Terraform installed and configured
- [ ] S3 bucket for state storage exists
- [ ] DynamoDB table for state locking exists
- [ ] GoDaddy DNS access for domain configuration

## ⚠️ Important Notes

### Route53 Dependencies
- **Domain Management**: `involvedtalent.com` managed by GoDaddy
- **DNS Records**: Must be created manually in GoDaddy
- **SSL Validation**: Requires DNS record creation in GoDaddy

### State Management
- **Backend**: S3 backend with DynamoDB locking
- **Permissions**: EC2 instance role needs S3 and DynamoDB access
- **Locking**: Prevents concurrent modifications

### Resource Dependencies
- **VPC**: Uses existing VPC (`10.0.0.0/16`)
- **Public Subnet**: Uses existing public subnet for ALB
- **Internet Gateway**: Depends on existing IGW

## 🎯 Success Criteria

- [ ] RDS instance accessible from ECS tasks
- [ ] Load balancer distributing traffic correctly
- [ ] SSL certificate validated and working
- [ ] Application responding on HTTPS
- [ ] Database backups configured and tested
- [ ] Monitoring and alerting set up
- [ ] Cost within estimated range

## 📚 Next Steps

1. **Review and Approve**: Review this plan with stakeholders
2. **Prepare Environment**: Ensure all prerequisites are met
3. **Deploy Infrastructure**: Run `terraform apply` when ready
4. **Configure DNS**: Set up DNS records in GoDaddy
5. **Test Application**: Verify all functionality works
6. **Monitor Performance**: Set up monitoring and alerting
7. **Documentation**: Update operational documentation

---

**Note**: This infrastructure plan is designed to be implemented when ready. All code changes will be reverted to maintain a clean state until deployment is approved.
