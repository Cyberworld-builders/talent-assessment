# AWS SES Email Service Configuration for Production
# Domain: involvedtalent.com

# SES Domain Identity for Production
resource "aws_ses_domain_identity" "production" {
  domain = "involvedtalent.com"
}

# SES Domain DKIM for Production
resource "aws_ses_domain_dkim" "production" {
  domain = aws_ses_domain_identity.production.domain
}

# SES Domain Mail From for Production
resource "aws_ses_domain_mail_from" "production" {
  domain           = aws_ses_domain_identity.production.domain
  mail_from_domain = "mail.involvedtalent.com"
}

# SNS Topic for Production Bounces
resource "aws_sns_topic" "ses_production_bounces" {
  name = "${var.project_name}-ses-production-bounces"
}

# SNS Topic for Production Complaints
resource "aws_sns_topic" "ses_production_complaints" {
  name = "${var.project_name}-ses-production-complaints"
}

# SNS Topic for Production Deliveries (optional)
resource "aws_sns_topic" "ses_production_deliveries" {
  name = "${var.project_name}-ses-production-deliveries"
}

# SES Configuration Set for Production
resource "aws_ses_configuration_set" "production" {
  name = "${var.project_name}-ses-production-config"
}

# SES Event Destination for Production Bounces
resource "aws_ses_event_destination" "production_bounces" {
  name                   = "production-bounces"
  configuration_set_name = aws_ses_configuration_set.production.name
  enabled                = true
  matching_types         = ["bounce"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_production_bounces.arn
  }
}

# SES Event Destination for Production Complaints
resource "aws_ses_event_destination" "production_complaints" {
  name                   = "production-complaints"
  configuration_set_name = aws_ses_configuration_set.production.name
  enabled                = true
  matching_types         = ["complaint"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_production_complaints.arn
  }
}

# SES Event Destination for Production Deliveries
resource "aws_ses_event_destination" "production_deliveries" {
  name                   = "production-deliveries"
  configuration_set_name = aws_ses_configuration_set.production.name
  enabled                = true
  matching_types         = ["delivery"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_production_deliveries.arn
  }
}

# IAM Policy for Production SES Sending
resource "aws_iam_policy" "ses_production_send_policy" {
  name        = "${var.project_name}-ses-production-send-policy"
  description = "Policy for sending emails via SES in production"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ses:SendEmail",
          "ses:SendRawEmail"
        ]
        Resource = "*"
      }
    ]
  })
}

# IAM Policy for Production SES Configuration
resource "aws_iam_policy" "ses_production_config_policy" {
  name        = "${var.project_name}-ses-production-config-policy"
  description = "Policy for SES configuration management in production"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ses:GetSendQuota",
          "ses:GetSendStatistics",
          "ses:ListConfigurationSets",
          "ses:DescribeConfigurationSet"
        ]
        Resource = "*"
      }
    ]
  })
}

# Add Production SES permissions to existing EC2 role
resource "aws_iam_role_policy" "ec2_ses_production_policy" {
  name = "${var.project_name}-ec2-ses-production-policy"
  role = aws_iam_role.dev_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ses:SendEmail",
          "ses:SendRawEmail",
          "ses:GetSendQuota",
          "ses:GetSendStatistics"
        ]
        Resource = "*"
      }
    ]
  })
}

# Add Production SES permissions to GitHub Actions role
resource "aws_iam_role_policy" "github_actions_ses_production" {
  name = "${var.project_name}-github-actions-ses-production-policy"
  role = aws_iam_role.github_actions.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "ses:SendEmail",
          "ses:SendRawEmail",
          "ses:GetSendQuota",
          "ses:GetSendStatistics"
        ]
        Resource = "*"
      }
    ]
  })
}

# Email Address Verification for Production Testing
# These email addresses will be verified programmatically for testing purposes
resource "aws_ses_email_identity" "production_test_emails" {
  for_each = toset(var.production_test_email_addresses)
  email     = each.value
}

# NOTE: Vercel IAM User and Access Keys removed in favor of OIDC authentication
# See vercel-oidc.tf for the OIDC-based approach (follows AWS best practices)
# The OIDC approach uses short-lived credentials instead of long-lived access keys

# Variable for production test email addresses
# Add this to your variables.tf file
# variable "production_test_email_addresses" {
#   description = "List of email addresses to verify for production testing"
#   type        = list(string)
#   default     = ["admin@my.involvedtalent.com", "support@my.involvedtalent.com"]
# }
