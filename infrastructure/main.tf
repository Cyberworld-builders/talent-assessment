# Configure the AWS Provider
provider "aws" {
  region  = var.aws_region
  profile = var.aws_profile
}

# Configure the Random Provider
provider "random" {}



# VPC Configuration
resource "aws_vpc" "dev_vpc" {
  cidr_block           = var.vpc_cidr
  enable_dns_support   = true
  enable_dns_hostnames = true

  tags = {
    Name = "${var.project_name}-vpc"
    Environment = var.environment
  }
}

# Public Subnet
resource "aws_subnet" "dev_subnet" {
  vpc_id                  = aws_vpc.dev_vpc.id
  cidr_block              = var.subnet_cidr
  map_public_ip_on_launch = true
  availability_zone       = var.availability_zone

  tags = {
    Name = "${var.project_name}-public-subnet"
    Environment = var.environment
  }
}

# Internet Gateway
resource "aws_internet_gateway" "dev_igw" {
  vpc_id = aws_vpc.dev_vpc.id

  tags = {
    Name = "${var.project_name}-internet-gateway"
    Environment = var.environment
  }
}

# Route Table
resource "aws_route_table" "dev_rt" {
  vpc_id = aws_vpc.dev_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.dev_igw.id
  }

  tags = {
    Name = "${var.project_name}-public-route-table"
    Environment = var.environment
  }
}

# Route Table Association
resource "aws_route_table_association" "dev_rta" {
  subnet_id      = aws_subnet.dev_subnet.id
  route_table_id = aws_route_table.dev_rt.id
}

# Security Group
resource "aws_security_group" "dev_sg" {
  name        = "${var.project_name}-security-group"
  description = "Security group for development environment"
  vpc_id      = aws_vpc.dev_vpc.id

  ingress {
    description = "Allow HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "Allow HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "Allow SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]  # TODO: Restrict to specific IP ranges for production
  }

  ingress {
    description = "Allow Traefik Dashboard"
    from_port   = 8080
    to_port     = 8080
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name = "${var.project_name}-security-group"
    Environment = var.environment
  }
}

# Key Pair
resource "aws_key_pair" "dev_key" {
  key_name   = var.ssh_key_name
  public_key = file(var.ssh_public_key_path)

  tags = {
    Name = "${var.project_name}-key-pair"
    Environment = var.environment
  }
}

# IAM Role
resource "aws_iam_role" "dev_role" {
  name = "${var.project_name}-ec2-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          Service = "ec2.amazonaws.com"
        }
        Action = "sts:AssumeRole"
      }
    ]
  })

  tags = {
    Name = "${var.project_name}-ec2-role"
    Environment = var.environment
  }
}

# IAM Role Policy
resource "aws_iam_role_policy" "dev_role_policy" {
  name = "${var.project_name}-ec2-policy"
  role = aws_iam_role.dev_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ec2-instance-connect:SendSSHPublicKey",
          "ec2:DescribeInstances",
          "ec2:DescribeTags"
        ]
        Resource = "*"
      }
    ]
  })
}

# S3 Bucket for uploads
resource "aws_s3_bucket" "uploads_bucket" {
  bucket = "${var.project_name}-${var.environment}-uploads-${random_string.bucket_suffix.result}"
  
  tags = {
    Name = "${var.project_name}-${var.environment}-uploads"
    Environment = var.environment
  }
}

# Random string for unique bucket name
resource "random_string" "bucket_suffix" {
  length  = 8
  special = false
  upper   = false
}

# S3 bucket versioning
resource "aws_s3_bucket_versioning" "uploads_bucket_versioning" {
  bucket = aws_s3_bucket.uploads_bucket.id
  versioning_configuration {
    status = "Enabled"
  }
}

# S3 bucket public access block - BLOCK ALL PUBLIC ACCESS
resource "aws_s3_bucket_public_access_block" "uploads_bucket_public_access" {
  bucket = aws_s3_bucket.uploads_bucket.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

# CloudFront Origin Access Identity
resource "aws_cloudfront_origin_access_identity" "uploads_oai" {
  comment = "OAI for ${var.project_name}-${var.environment} uploads bucket"
}

# S3 bucket policy for CloudFront access only
resource "aws_s3_bucket_policy" "uploads_bucket_policy" {
  bucket = aws_s3_bucket.uploads_bucket.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "CloudFrontAccess"
        Effect    = "Allow"
        Principal = {
          AWS = aws_cloudfront_origin_access_identity.uploads_oai.iam_arn
        }
        Action    = "s3:GetObject"
        Resource  = "${aws_s3_bucket.uploads_bucket.arn}/*"
      }
    ]
  })
}



# CloudFront Distribution
resource "aws_cloudfront_distribution" "uploads_distribution" {
  enabled             = true
  is_ipv6_enabled     = true
  default_root_object = "index.html"
  
  # Use default CloudFront domain (no custom domain for now)
  aliases = []
  
  origin {
    domain_name = aws_s3_bucket.uploads_bucket.bucket_regional_domain_name
    origin_id   = "S3-${aws_s3_bucket.uploads_bucket.bucket}"

    s3_origin_config {
      origin_access_identity = aws_cloudfront_origin_access_identity.uploads_oai.cloudfront_access_identity_path
    }
  }

  default_cache_behavior {
    allowed_methods  = ["GET", "HEAD", "OPTIONS"]
    cached_methods   = ["GET", "HEAD"]
    target_origin_id = "S3-${aws_s3_bucket.uploads_bucket.bucket}"

    forwarded_values {
      query_string = false
      cookies {
        forward = "none"
      }
    }

    viewer_protocol_policy = "redirect-to-https"
    min_ttl                = 0
    default_ttl            = 3600
    max_ttl                = 86400
    
    # Cache images for 1 day
    compress = true
  }

  # Cache images for longer
  ordered_cache_behavior {
    path_pattern     = "*.jpg"
    allowed_methods  = ["GET", "HEAD", "OPTIONS"]
    cached_methods   = ["GET", "HEAD"]
    target_origin_id = "S3-${aws_s3_bucket.uploads_bucket.bucket}"

    forwarded_values {
      query_string = false
      cookies {
        forward = "none"
      }
    }

    viewer_protocol_policy = "redirect-to-https"
    min_ttl                = 0
    default_ttl            = 86400  # 1 day
    max_ttl                = 31536000  # 1 year
    compress               = true
  }

  ordered_cache_behavior {
    path_pattern     = "*.png"
    allowed_methods  = ["GET", "HEAD", "OPTIONS"]
    cached_methods   = ["GET", "HEAD"]
    target_origin_id = "S3-${aws_s3_bucket.uploads_bucket.bucket}"

    forwarded_values {
      query_string = false
      cookies {
        forward = "none"
      }
    }

    viewer_protocol_policy = "redirect-to-https"
    min_ttl                = 0
    default_ttl            = 86400  # 1 day
    max_ttl                = 31536000  # 1 year
    compress               = true
  }

  ordered_cache_behavior {
    path_pattern     = "*.gif"
    allowed_methods  = ["GET", "HEAD", "OPTIONS"]
    cached_methods   = ["GET", "HEAD"]
    target_origin_id = "S3-${aws_s3_bucket.uploads_bucket.bucket}"

    forwarded_values {
      query_string = false
      cookies {
        forward = "none"
      }
    }

    viewer_protocol_policy = "redirect-to-https"
    min_ttl                = 0
    default_ttl            = 86400  # 1 day
    max_ttl                = 31536000  # 1 year
    compress               = true
  }

  price_class = "PriceClass_100"  # Use only North America and Europe

  restrictions {
    geo_restriction {
      restriction_type = "none"
    }
  }

  viewer_certificate {
    cloudfront_default_certificate = true
  }

  tags = {
    Name = "${var.project_name}-${var.environment}-uploads-cdn"
    Environment = var.environment
  }
}

# S3 IAM Policy for EC2 instance
resource "aws_iam_role_policy" "s3_uploads_policy" {
  name = "${var.project_name}-s3-uploads-policy"
  role = aws_iam_role.dev_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:PutObject",
          "s3:DeleteObject",
          "s3:ListBucket"
        ]
        Resource = [
          aws_s3_bucket.uploads_bucket.arn,
          "${aws_s3_bucket.uploads_bucket.arn}/*"
        ]
      }
    ]
  })
}

# ECR Repository for app images
resource "aws_ecr_repository" "talent_app_repo" {
  name                 = "talent-assessment-app"
  image_tag_mutability = "MUTABLE"
  
  image_scanning_configuration {
    scan_on_push = true
  }
}

# ECR Lifecycle Policy to expire old images
resource "aws_ecr_lifecycle_policy" "talent_app_lifecycle" {
  repository = aws_ecr_repository.talent_app_repo.name

  policy = jsonencode({
    rules = [
      {
        rulePriority = 1
        description  = "Expire images older than 10"
        selection = {
          tagStatus   = "any"
          countType   = "imageCountMoreThan"
          countNumber = 10
        }
        action = {
          type = "expire"
        }
      }
    ]
  })
}

# Secrets Manager for Staging Environment
resource "aws_secretsmanager_secret" "staging_secrets" {
  name = "talent-assessment-staging-secrets"
  
  tags = {
    Name = "${var.project_name}-staging-secrets"
    Environment = "staging"
  }
}

resource "aws_secretsmanager_secret_version" "staging_secrets_version" {
  secret_id     = aws_secretsmanager_secret.staging_secrets.id
  secret_string = jsonencode({
    STAGING_DB_PASSWORD    = "strong_staging_db_pass_${random_string.secret_suffix.result}"
    STAGING_REDIS_PASSWORD = "strong_staging_redis_pass_${random_string.secret_suffix.result}"
    STAGING_S3_BUCKET      = aws_s3_bucket.staging_uploads_bucket.bucket
    STAGING_DB_DATABASE    = "talent_assessment_staging"
    STAGING_DB_USERNAME    = "talent_user_staging"
    STAGING_DB_ROOT_PASSWORD = "strong_staging_root_pass_${random_string.secret_suffix.result}"
  })
}

# Secrets Manager for Production Environment
resource "aws_secretsmanager_secret" "production_secrets" {
  name = "talent-assessment-production-secrets"
  
  tags = {
    Name = "${var.project_name}-production-secrets"
    Environment = "production"
  }
}

resource "aws_secretsmanager_secret_version" "production_secrets_version" {
  secret_id     = aws_secretsmanager_secret.production_secrets.id
  secret_string = jsonencode({
    PRODUCTION_DB_PASSWORD    = "strong_production_db_pass_${random_string.secret_suffix.result}"
    PRODUCTION_REDIS_PASSWORD = "strong_production_redis_pass_${random_string.secret_suffix.result}"
    PRODUCTION_S3_BUCKET      = aws_s3_bucket.uploads_bucket.bucket
    PRODUCTION_DB_DATABASE    = "talent_assessment_production"
    PRODUCTION_DB_USERNAME    = "talent_user_production"
    PRODUCTION_DB_ROOT_PASSWORD = "strong_production_root_pass_${random_string.secret_suffix.result}"
    PRODUCTION_APP_KEY        = "base64:${base64encode(random_string.app_key.result)}"
    PRODUCTION_SES_CONFIG_SET = aws_ses_configuration_set.production.name
    PRODUCTION_SES_REGION     = "us-east-2"
  })
}

# Random string for Laravel APP_KEY
resource "random_string" "app_key" {
  length  = 32
  special = false
  upper   = false
}

# Random string for unique secret values
resource "random_string" "secret_suffix" {
  length  = 8
  special = false
  upper   = false
}

# Staging S3 Bucket for uploads
resource "aws_s3_bucket" "staging_uploads_bucket" {
  bucket = "${var.project_name}-staging-uploads-${random_string.bucket_suffix.result}"
  
  tags = {
    Name = "${var.project_name}-staging-uploads"
    Environment = "staging"
  }
}

# S3 bucket versioning for staging
resource "aws_s3_bucket_versioning" "staging_uploads_bucket_versioning" {
  bucket = aws_s3_bucket.staging_uploads_bucket.id
  versioning_configuration {
    status = "Enabled"
  }
}

# S3 bucket public access block for staging
resource "aws_s3_bucket_public_access_block" "staging_uploads_bucket_public_access" {
  bucket = aws_s3_bucket.staging_uploads_bucket.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

# S3 bucket policy for staging (CloudFront access only)
resource "aws_s3_bucket_policy" "staging_uploads_bucket_policy" {
  bucket = aws_s3_bucket.staging_uploads_bucket.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "CloudFrontAccess"
        Effect    = "Allow"
        Principal = {
          AWS = aws_cloudfront_origin_access_identity.uploads_oai.iam_arn
        }
        Action    = "s3:GetObject"
        Resource  = "${aws_s3_bucket.staging_uploads_bucket.arn}/*"
      }
    ]
  })
}

# Extended IAM Policy for EC2 (ECR and Secrets Manager access)
resource "aws_iam_role_policy" "ec2_extended_policy" {
  name = "${var.project_name}-ec2-ecr-secrets-policy"
  role = aws_iam_role.dev_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ecr:GetAuthorizationToken",
          "ecr:BatchCheckLayerAvailability",
          "ecr:GetDownloadUrlForLayer",
          "ecr:BatchGetImage"
        ]
        Resource = "*"
      },
      {
        Effect = "Allow"
        Action = "secretsmanager:GetSecretValue"
        Resource = [
          aws_secretsmanager_secret.staging_secrets.arn,
          aws_secretsmanager_secret.production_secrets.arn
        ]
      },
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:PutObject",
          "s3:DeleteObject",
          "s3:ListBucket"
        ]
        Resource = [
          aws_s3_bucket.staging_uploads_bucket.arn,
          "${aws_s3_bucket.staging_uploads_bucket.arn}/*"
        ]
      }
    ]
  })
}

# IAM Instance Profile
resource "aws_iam_instance_profile" "dev_profile" {
  name = "${var.project_name}-instance-profile"
  role = aws_iam_role.dev_role.name
}

# Data source for Ubuntu 22.04 LTS AMI
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical's AWS account ID

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-jammy-22.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
  
  # Use specific AMI to prevent instance replacement
  # Current instance uses: ami-08a98e6d1b5aa6099
  # Uncomment the line below to pin to a specific AMI
  # ami_ids = ["ami-08a98e6d1b5aa6099"]
}

# Elastic IP
resource "aws_eip" "dev_eip" {
  domain = "vpc"
  
  tags = {
    Name = "${var.project_name}-${var.environment}-eip"
    Environment = var.environment
  }
}

# EC2 Instance
resource "aws_instance" "dev_instance" {
  ami                    = "ami-08a98e6d1b5aa6099"  # Pinned to current AMI to prevent replacement
  instance_type          = var.instance_type
  subnet_id              = aws_subnet.dev_subnet.id
  vpc_security_group_ids = [aws_security_group.dev_sg.id]
  associate_public_ip_address = true
  key_name               = aws_key_pair.dev_key.key_name
  iam_instance_profile   = aws_iam_instance_profile.dev_profile.name

  user_data_base64 = base64encode(templatefile("${path.module}/user_data.sh", {
    domain = var.domain_name
  }))

  root_block_device {
    volume_size = 20
    volume_type = "gp3"
  }

  tags = {
    Name = "${var.project_name}-${var.environment}"
    Environment = var.environment
  }
}

# Associate Elastic IP with EC2 Instance
resource "aws_eip_association" "dev_eip_assoc" {
  instance_id   = aws_instance.dev_instance.id
  allocation_id = aws_eip.dev_eip.id
}


