# Data source to get availability zones
data "aws_availability_zones" "available" {
  state = "available"
}

# Private subnets for RDS (Multi-AZ)
resource "aws_subnet" "private_subnet_1" {
  vpc_id            = aws_vpc.dev_vpc.id
  cidr_block        = "10.0.10.0/24"
  availability_zone = data.aws_availability_zones.available.names[0]

  tags = {
    Name        = "${var.project_name}-private-subnet-1"
    Environment = var.environment
    Type        = "private"
  }
}

resource "aws_subnet" "private_subnet_2" {
  vpc_id            = aws_vpc.dev_vpc.id
  cidr_block        = "10.0.20.0/24"
  availability_zone = data.aws_availability_zones.available.names[1]

  tags = {
    Name        = "${var.project_name}-private-subnet-2"
    Environment = var.environment
    Type        = "private"
  }
}

# Database subnet group for RDS
resource "aws_db_subnet_group" "talent_assessment_db_subnet_group" {
  name       = "${var.project_name}-db-subnet-group"
  subnet_ids = [aws_subnet.private_subnet_1.id, aws_subnet.private_subnet_2.id]

  tags = {
    Name        = "${var.project_name}-db-subnet-group"
    Environment = var.environment
  }
}

# Security group for RDS
resource "aws_security_group" "rds_sg" {
  name_prefix = "${var.project_name}-rds-"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.ecs_sg.id]
    description     = "MySQL access from ECS tasks"
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "All outbound traffic"
  }

  tags = {
    Name        = "${var.project_name}-rds-security-group"
    Environment = var.environment
  }
}

# Security group for ECS tasks (referenced by RDS SG)
resource "aws_security_group" "ecs_sg" {
  name_prefix = "${var.project_name}-ecs-"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb_sg.id]
    description     = "HTTP access from ALB"
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "All outbound traffic"
  }

  tags = {
    Name        = "${var.project_name}-ecs-security-group"
    Environment = var.environment
  }
}

# Security group for ALB (referenced by ECS SG)
resource "aws_security_group" "alb_sg" {
  name_prefix = "${var.project_name}-alb-"
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

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
    description = "All outbound traffic"
  }

  tags = {
    Name        = "${var.project_name}-alb-security-group"
    Environment = var.environment
  }
}

# Random password for RDS
resource "random_password" "rds_password" {
  length  = 16
  special = true
}

# AWS Secrets Manager secret for RDS credentials
resource "aws_secretsmanager_secret" "rds_credentials" {
  name                    = "${var.project_name}-rds-credentials"
  description             = "RDS database credentials for talent assessment"
  recovery_window_in_days = 7

  tags = {
    Name        = "${var.project_name}-rds-credentials"
    Environment = var.environment
  }
}

resource "aws_secretsmanager_secret_version" "rds_credentials" {
  secret_id = aws_secretsmanager_secret.rds_credentials.id
  secret_string = jsonencode({
    username = "talent_user_production"
    password = random_password.rds_password.result
    engine   = "mysql"
    host     = aws_db_instance.talent_assessment_db.endpoint
    port     = 3306
    dbname   = "talent_assessment_production"
  })
}

# RDS MySQL instance
resource "aws_db_instance" "talent_assessment_db" {
  identifier = "${var.project_name}-production-db"
  
  # Engine configuration
  engine         = "mysql"
  engine_version = "8.0.35"
  instance_class = "db.t3.medium"
  
  # Database configuration
  db_name  = "talent_assessment_production"
  username = "talent_user_production"
  password = random_password.rds_password.result
  
  # Storage configuration
  allocated_storage     = 100
  max_allocated_storage = 1000
  storage_type          = "gp3"
  storage_encrypted     = true
  
  # Network configuration
  db_subnet_group_name   = aws_db_subnet_group.talent_assessment_db_subnet_group.name
  vpc_security_group_ids = [aws_security_group.rds_sg.id]
  publicly_accessible    = false
  
  # Backup configuration
  backup_retention_period = 7
  backup_window          = "03:00-04:00"
  maintenance_window     = "sun:04:00-sun:05:00"
  
  # Monitoring
  monitoring_interval = 60
  monitoring_role_arn = aws_iam_role.rds_monitoring_role.arn
  
  # High availability
  multi_az = true
  
  # Deletion protection
  deletion_protection = true
  skip_final_snapshot = false
  final_snapshot_identifier = "${var.project_name}-production-db-final-snapshot-${formatdate("YYYY-MM-DD-hhmm", timestamp())}"
  
  tags = {
    Name        = "${var.project_name}-production-database"
    Environment = var.environment
  }
}

# IAM role for RDS monitoring
resource "aws_iam_role" "rds_monitoring_role" {
  name = "${var.project_name}-rds-monitoring-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "monitoring.rds.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "${var.project_name}-rds-monitoring-role"
    Environment = var.environment
  }
}

resource "aws_iam_role_policy_attachment" "rds_monitoring_policy" {
  role       = aws_iam_role.rds_monitoring_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonRDSEnhancedMonitoringRole"
}

# IAM role for ECS tasks
resource "aws_iam_role" "ecs_task_role" {
  name = "${var.project_name}-ecs-task-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "${var.project_name}-ecs-task-role"
    Environment = var.environment
  }
}

# IAM role for ECS task execution
resource "aws_iam_role" "ecs_execution_role" {
  name = "${var.project_name}-ecs-execution-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "${var.project_name}-ecs-execution-role"
    Environment = var.environment
  }
}

# Attach AWS managed policies to ECS execution role
resource "aws_iam_role_policy_attachment" "ecs_execution_role_policy" {
  role       = aws_iam_role.ecs_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

# Custom policy for ECS tasks to access Secrets Manager
resource "aws_iam_policy" "ecs_secrets_policy" {
  name        = "${var.project_name}-ecs-secrets-policy"
  description = "Policy for ECS tasks to access Secrets Manager"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "secretsmanager:GetSecretValue"
        ]
        Resource = [
          aws_secretsmanager_secret.rds_credentials.arn,
          aws_secretsmanager_secret.production_secrets.arn
        ]
      }
    ]
  })

  tags = {
    Name        = "${var.project_name}-ecs-secrets-policy"
    Environment = var.environment
  }
}

resource "aws_iam_role_policy_attachment" "ecs_task_secrets_policy" {
  role       = aws_iam_role.ecs_task_role.name
  policy_arn = aws_iam_policy.ecs_secrets_policy.arn
}
