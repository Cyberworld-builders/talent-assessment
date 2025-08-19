output "public_ip" {
  description = "Public IP of the EC2 instance"
  value       = aws_instance.dev_instance.public_ip
}

output "instance_id" {
  description = "ID of the EC2 instance"
  value       = aws_instance.dev_instance.id
}

output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.dev_vpc.id
}

output "subnet_id" {
  description = "ID of the public subnet"
  value       = aws_subnet.dev_subnet.id
}

output "security_group_id" {
  description = "ID of the security group"
  value       = aws_security_group.dev_sg.id
}

output "ssh_command" {
  description = "SSH command to connect to the instance"
  value       = "ssh -i ~/.ssh/dev-key ubuntu@${aws_instance.dev_instance.public_ip}"
}

output "application_url" {
  description = "URL to access the application"
  value       = "http://${aws_instance.dev_instance.public_ip}"
}

output "traefik_dashboard_url" {
  description = "URL to access the Traefik dashboard"
  value       = "http://${aws_instance.dev_instance.public_ip}:8080"
}

output "s3_bucket_name" {
  description = "Name of the S3 bucket for uploads"
  value       = aws_s3_bucket.uploads_bucket.bucket
}

output "s3_bucket_region" {
  description = "Region of the S3 bucket"
  value       = aws_s3_bucket.uploads_bucket.region
}

output "cloudfront_domain" {
  description = "CloudFront distribution domain name"
  value       = aws_cloudfront_distribution.uploads_distribution.domain_name
}



output "cloudfront_distribution_id" {
  description = "CloudFront distribution ID"
  value       = aws_cloudfront_distribution.uploads_distribution.id
}

output "ecr_repository_url" {
  description = "ECR repository URL"
  value       = aws_ecr_repository.talent_app_repo.repository_url
}

output "staging_secrets_arn" {
  description = "Staging secrets ARN"
  value       = aws_secretsmanager_secret.staging_secrets.arn
}

output "staging_s3_bucket_name" {
  description = "Staging S3 bucket name"
  value       = aws_s3_bucket.staging_uploads_bucket.bucket
}

output "deployment_summary" {
  description = "Summary of the deployment"
  value = <<-EOF
    🚀 Talent Assessment Development Environment Deployed Successfully!
    
    📍 Instance Details:
    - Instance ID: ${aws_instance.dev_instance.id}
    - Public IP: ${aws_eip.dev_eip.public_ip}
    - Instance Type: ${aws_instance.dev_instance.instance_type}
    
    🌐 Access URLs:
    - Application: http://${aws_instance.dev_instance.public_ip}
    - Traefik Dashboard: http://${aws_instance.dev_instance.public_ip}:8080
    
    📦 S3 Storage:
    - Bucket Name: ${aws_s3_bucket.uploads_bucket.bucket}
    - Bucket Region: ${aws_s3_bucket.uploads_bucket.region}
    - Bucket URL: https://${aws_s3_bucket.uploads_bucket.bucket}.s3.${aws_s3_bucket.uploads_bucket.region}.amazonaws.com
    
    ☁️ CloudFront CDN:
    - Domain: ${aws_cloudfront_distribution.uploads_distribution.domain_name}
    - Distribution ID: ${aws_cloudfront_distribution.uploads_distribution.id}
    
    🔑 SSH Access:
    ssh -i ~/.ssh/dev-key ubuntu@${aws_instance.dev_instance.public_ip}
    
    📋 Next Steps:
    1. Update your domain DNS to point to: ${aws_instance.dev_instance.public_ip}
    2. Upload your Laravel application files to the server
    3. Configure SSL certificates for HTTPS
    4. Set up environment variables for production
    5. Update S3 bucket name in Laravel configuration
    6. Set AWS_CLOUDFRONT_DOMAIN environment variable with CloudFront domain

    
    ⚠️  Security Note: SSH is currently open to 0.0.0.0/0 for AWS console access.
    Consider restricting this to your IP address for production use.
  EOF
}
