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

output "production_secrets_arn" {
  description = "Production secrets ARN"
  value       = aws_secretsmanager_secret.production_secrets.arn
}

output "staging_s3_bucket_name" {
  description = "Staging S3 bucket name"
  value       = aws_s3_bucket.staging_uploads_bucket.bucket
}

output "staging_cloudfront_domain" {
  description = "CloudFront domain for staging environment"
  value       = aws_cloudfront_distribution.staging_uploads_distribution.domain_name
}

output "production_cloudfront_domain" {
  description = "CloudFront domain for production environment"
  value       = aws_cloudfront_distribution.production_uploads_distribution.domain_name
}

output "production_s3_bucket_name" {
  description = "S3 bucket name for production environment"
  value       = aws_s3_bucket.production_uploads_bucket.bucket
}

# SES Outputs
output "ses_domain_identity" {
  description = "SES domain identity"
  value       = aws_ses_domain_identity.main.domain
}

output "ses_verification_token" {
  description = "SES domain verification token"
  value       = aws_ses_domain_identity.main.verification_token
}

output "ses_dkim_tokens" {
  description = "SES DKIM tokens"
  value       = aws_ses_domain_dkim.main.dkim_tokens
}

output "ses_mail_from_domain" {
  description = "SES mail from domain"
  value       = aws_ses_domain_mail_from.main.mail_from_domain
}

# Note: SES user access keys removed - using IAM role-based authentication instead
# output "ses_user_access_key" {
#   description = "SES user access key"
#   value       = aws_iam_access_key.ses_user.id
#   sensitive   = true
# }

# output "ses_user_secret_key" {
#   description = "SES user secret key"
#   value       = aws_iam_access_key.ses_user.secret
#   sensitive   = true
# }

output "ses_configuration_set" {
  description = "SES configuration set name"
  value       = aws_ses_configuration_set.main.name
}

output "ses_bounces_topic_arn" {
  description = "SNS topic ARN for SES bounces"
  value       = aws_sns_topic.ses_bounces.arn
}

output "ses_complaints_topic_arn" {
  description = "SNS topic ARN for SES complaints"
  value       = aws_sns_topic.ses_complaints.arn
}

output "ses_deliveries_topic_arn" {
  description = "SNS topic ARN for SES deliveries"
  value       = aws_sns_topic.ses_deliveries.arn
}

output "test_email_addresses" {
  description = "List of verified test email addresses"
  value       = [for email in aws_ses_email_identity.test_emails : email.email]
}

# Production SES Outputs
output "ses_production_domain_identity" {
  description = "Production SES domain identity"
  value       = aws_ses_domain_identity.production.domain
}

output "ses_production_verification_token" {
  description = "Production SES domain verification token"
  value       = aws_ses_domain_identity.production.verification_token
}

output "ses_production_dkim_tokens" {
  description = "Production SES DKIM tokens"
  value       = aws_ses_domain_dkim.production.dkim_tokens
}

output "ses_production_mail_from_domain" {
  description = "Production SES mail from domain"
  value       = aws_ses_domain_mail_from.production.mail_from_domain
}

output "ses_production_configuration_set" {
  description = "Production SES configuration set name"
  value       = aws_ses_configuration_set.production.name
}

output "ses_production_bounces_topic_arn" {
  description = "SNS topic ARN for production SES bounces"
  value       = aws_sns_topic.ses_production_bounces.arn
}

output "ses_production_complaints_topic_arn" {
  description = "SNS topic ARN for production SES complaints"
  value       = aws_sns_topic.ses_production_complaints.arn
}

output "ses_production_deliveries_topic_arn" {
  description = "SNS topic ARN for production SES deliveries"
  value       = aws_sns_topic.ses_production_deliveries.arn
}

output "production_test_email_addresses" {
  description = "List of verified production test email addresses"
  value       = [for email in aws_ses_email_identity.production_test_emails : email.email]
}

# Vercel OIDC Outputs (see vercel-oidc.tf for OIDC-based authentication)
# OIDC is the recommended approach - no long-lived credentials needed!
# The vercel_ses_role_arn and vercel_oidc_setup_instructions are defined in vercel-oidc.tf

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
    
    📧 Email Service (SES):
    - Domain: ${aws_ses_domain_identity.main.domain}
    - Mail From: ${aws_ses_domain_mail_from.main.mail_from_domain}
    - Configuration Set: ${aws_ses_configuration_set.main.name}
    - Bounces Topic: ${aws_sns_topic.ses_bounces.arn}
    - Complaints Topic: ${aws_sns_topic.ses_complaints.arn}
    
    📧 Production Email Service (SES):
    - Domain: ${aws_ses_domain_identity.production.domain}
    - Mail From: ${aws_ses_domain_mail_from.production.mail_from_domain}
    - Configuration Set: ${aws_ses_configuration_set.production.name}
    - Bounces Topic: ${aws_sns_topic.ses_production_bounces.arn}
    - Complaints Topic: ${aws_sns_topic.ses_production_complaints.arn}
    
    🔐 Secrets Management:
    - Staging Secrets: ${aws_secretsmanager_secret.staging_secrets.arn}
    - Production Secrets: ${aws_secretsmanager_secret.production_secrets.arn}
    

    
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
