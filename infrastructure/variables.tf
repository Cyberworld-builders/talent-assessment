variable "aws_region" {
  description = "AWS region to deploy resources"
  type        = string
  default     = "us-east-1"
}

variable "aws_profile" {
  description = "AWS CLI profile to use"
  type        = string
  default     = "sandbox-profile"
}

variable "instance_type" {
  description = "EC2 instance type"
  type        = string
  default     = "t3.small"
}

variable "vpc_cidr" {
  description = "CIDR block for VPC"
  type        = string
  default     = "10.0.0.0/16"
}

variable "subnet_cidr" {
  description = "CIDR block for subnet"
  type        = string
  default     = "10.0.1.0/24"
}

variable "availability_zone" {
  description = "Availability zone for subnet"
  type        = string
  default     = "us-east-1a"
}

variable "domain_name" {
  description = "Domain name for the application"
  type        = string
  default     = "talent.cyberworldbuilders.dev"
}



variable "ssh_key_name" {
  description = "Name of the SSH key pair"
  type        = string
  default     = "dev-key"
}

variable "ssh_public_key_path" {
  description = "Path to the SSH public key file"
  type        = string
  default     = "~/.ssh/dev-key.pub"
}

variable "environment" {
  description = "Environment name"
  type        = string
  default     = "development"
}

variable "project_name" {
  description = "Project name for resource naming"
  type        = string
  default     = "talent-assessment"
}

variable "test_email_addresses" {
  description = "List of email addresses to verify for testing"
  type        = list(string)
  default     = [
    "admin-goreman@cyberworldbuilders.com",
    "user-apone@cyberworldbuilders.com",
  ]
}

variable "production_test_email_addresses" {
  description = "List of email addresses to verify for production testing"
  type        = list(string)
  default     = [
    "admin@involvedtalent.com",
    "support@involvedtalent.com",
    "noreply@involvedtalent.com",
    "assessment@involvedtalent.com"
  ]
}

# Database variables
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

# ECS variables
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

variable "ecs_min_capacity" {
  description = "Minimum number of ECS tasks"
  type        = number
  default     = 1
}

variable "ecs_max_capacity" {
  description = "Maximum number of ECS tasks"
  type        = number
  default     = 10
}
