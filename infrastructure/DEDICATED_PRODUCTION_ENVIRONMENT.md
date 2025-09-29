# Dedicated Production Environment Infrastructure Plan

## Overview
This document outlines the infrastructure design for a dedicated production environment that will replace the current Docker Compose-based production setup. The new environment will use AWS managed services for better scalability, reliability, and operational efficiency.

## Current State
- **Current Production**: Docker Compose on single EC2 instance
- **Database**: MySQL in Docker container
- **Cache**: Redis in Docker container
- **Application**: Laravel 5.1 in Docker container
- **Load Balancing**: None (single instance)

## Target Architecture

### Core Services
1. **Amazon RDS for MySQL** - Managed database service
2. **Amazon ElastiCache for Redis** - Managed caching service
3. **Amazon ECS (Fargate)** - Container orchestration
4. **Application Load Balancer** - Traffic distribution
5. **VPC with Public/Private Subnets** - Network isolation

## Infrastructure Components

### 1. Networking
```
VPC (10.0.0.0/16)
├── Public Subnets (10.0.1.0/24, 10.0.2.0/24) - AZ-a, AZ-b
│   ├── Internet Gateway
│   ├── NAT Gateway (for private subnet outbound)
│   └── Application Load Balancer
└── Private Subnets (10.0.10.0/24, 10.0.20.0/24) - AZ-a, AZ-b
    ├── ECS Fargate Tasks
    ├── RDS MySQL (Multi-AZ)
    └── ElastiCache Redis (Multi-AZ)
```

### 2. Database Layer
- **RDS MySQL 8.0** (Multi-AZ for high availability)
- **Instance Class**: db.t3.medium (2 vCPU, 4 GB RAM)
- **Storage**: 100 GB GP3 with auto-scaling
- **Backup**: 7-day retention, automated snapshots
- **Security**: VPC security groups, encryption at rest

### 3. Caching Layer
- **ElastiCache Redis 7.x** (Multi-AZ cluster mode)
- **Node Type**: cache.t3.micro (1 vCPU, 0.5 GB RAM)
- **Cluster**: 2 nodes (1 primary, 1 replica)
- **Security**: VPC security groups, encryption in transit

### 4. Application Layer
- **ECS Fargate** (Serverless containers)
- **Task Definition**: 1 vCPU, 2 GB RAM per task
- **Auto Scaling**: 2-10 tasks based on CPU/memory
- **Container Registry**: ECR (existing)
- **Image**: talent-assessment-app:latest

### 5. Load Balancing
- **Application Load Balancer (ALB)**
- **Target Groups**: ECS Fargate tasks
- **Health Checks**: HTTP 200 on /health endpoint
- **SSL/TLS**: ACM certificate for HTTPS
- **Routing**: Path-based routing for different services

## Security Groups

### ALB Security Group
```hcl
resource "aws_security_group" "alb_sg" {
  name_prefix = "talent-assessment-alb-"
  
  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
  
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}
```

### ECS Security Group
```hcl
resource "aws_security_group" "ecs_sg" {
  name_prefix = "talent-assessment-ecs-"
  
  ingress {
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb_sg.id]
  }
  
  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}
```

### RDS Security Group
```hcl
resource "aws_security_group" "rds_sg" {
  name_prefix = "talent-assessment-rds-"
  
  ingress {
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.ecs_sg.id]
  }
}
```

### ElastiCache Security Group
```hcl
resource "aws_security_group" "redis_sg" {
  name_prefix = "talent-assessment-redis-"
  
  ingress {
    from_port       = 6379
    to_port         = 6379
    protocol        = "tcp"
    security_groups = [aws_security_group.ecs_sg.id]
  }
}
```

## Environment Variables & Secrets

### ECS Task Definition Environment
```json
{
  "environment": [
    {
      "name": "APP_ENV",
      "value": "production"
    },
    {
      "name": "DB_HOST",
      "value": "${rds_endpoint}"
    },
    {
      "name": "REDIS_HOST",
      "value": "${redis_endpoint}"
    }
  ],
  "secrets": [
    {
      "name": "DB_PASSWORD",
      "valueFrom": "${rds_secret_arn}"
    },
    {
      "name": "APP_KEY",
      "valueFrom": "${app_key_secret_arn}"
    }
  ]
}
```

## Migration Strategy

### Phase 1: Infrastructure Setup
1. Create VPC and networking components
2. Deploy RDS MySQL instance
3. Deploy ElastiCache Redis cluster
4. Set up ECS cluster and task definitions
5. Configure Application Load Balancer

### Phase 2: Data Migration
1. Create database backup from current production
2. Restore data to new RDS instance
3. Test database connectivity and data integrity
4. Migrate Redis cache (if needed)

### Phase 3: Application Deployment
1. Update ECR with latest application image
2. Deploy ECS tasks with new environment variables
3. Configure ALB target groups and health checks
4. Update DNS to point to new ALB

### Phase 4: Cutover
1. Schedule maintenance window
2. Final data sync (if needed)
3. DNS cutover to new infrastructure
4. Monitor application health and performance
5. Decommission old infrastructure

## Cost Estimation

### Monthly Costs (US East 2)
- **RDS MySQL (db.t3.medium)**: ~$45/month
- **ElastiCache Redis (cache.t3.micro)**: ~$15/month
- **ECS Fargate (2 tasks, 1vCPU, 2GB)**: ~$30/month
- **Application Load Balancer**: ~$20/month
- **NAT Gateway**: ~$45/month
- **Data Transfer**: ~$10/month
- **Total Estimated**: ~$165/month

## Benefits

### Reliability
- Multi-AZ deployment for high availability
- Automated backups and point-in-time recovery
- Health checks and auto-scaling

### Scalability
- Auto-scaling based on demand
- Load balancer distributes traffic
- Managed services handle scaling

### Security
- Network isolation with private subnets
- Security groups for fine-grained access control
- Encryption at rest and in transit
- AWS Secrets Manager for sensitive data

### Operational Excellence
- Managed services reduce operational overhead
- CloudWatch monitoring and logging
- Automated patching and updates
- Infrastructure as Code with Terraform

## Monitoring & Logging

### CloudWatch Metrics
- ECS: CPU, Memory, Task count
- RDS: CPU, Memory, Connections, Storage
- ElastiCache: CPU, Memory, Cache hits/misses
- ALB: Request count, response time, error rates

### Logging
- ECS: Application logs to CloudWatch Logs
- RDS: Database logs to CloudWatch Logs
- ALB: Access logs to S3

## Next Steps

1. **Review and approve** this infrastructure plan
2. **Create Terraform modules** for each component
3. **Set up development environment** for testing
4. **Plan migration timeline** and communication
5. **Execute migration** in phases

## Questions for Discussion

1. **Database sizing**: Is db.t3.medium appropriate for current load?
2. **Redis configuration**: Do we need cluster mode or single node?
3. **ECS scaling**: What are the expected traffic patterns?
4. **Monitoring**: What specific metrics are most important?
5. **Backup strategy**: Are 7-day RDS backups sufficient?
6. **SSL certificates**: Should we use ACM or external certificates?

---

*This document will be updated as we refine the architecture and gather more requirements.*
