# AWS SES Email Service Configuration

# SES Domain Identity
resource "aws_ses_domain_identity" "main" {
  domain = "cyberworldbuilders.dev"
}

# SES Domain DKIM
resource "aws_ses_domain_dkim" "main" {
  domain = aws_ses_domain_identity.main.domain
}

# SES Domain Mail From
resource "aws_ses_domain_mail_from" "main" {
  domain           = aws_ses_domain_identity.main.domain
  mail_from_domain = "mail.cyberworldbuilders.dev"
}

# SNS Topic for Bounces
resource "aws_sns_topic" "ses_bounces" {
  name = "${var.project_name}-ses-bounces"
}

# SNS Topic for Complaints
resource "aws_sns_topic" "ses_complaints" {
  name = "${var.project_name}-ses-complaints"
}

# SNS Topic for Deliveries (optional)
resource "aws_sns_topic" "ses_deliveries" {
  name = "${var.project_name}-ses-deliveries"
}

# SES Configuration Set
resource "aws_ses_configuration_set" "main" {
  name = "${var.project_name}-ses-config"
}

# SES Event Destination for Bounces
resource "aws_ses_event_destination" "bounces" {
  name                   = "bounces"
  configuration_set_name = aws_ses_configuration_set.main.name
  enabled                = true
  matching_types         = ["bounce"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_bounces.arn
  }
}

# SES Event Destination for Complaints
resource "aws_ses_event_destination" "complaints" {
  name                   = "complaints"
  configuration_set_name = aws_ses_configuration_set.main.name
  enabled                = true
  matching_types         = ["complaint"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_complaints.arn
  }
}

# SES Event Destination for Deliveries
resource "aws_ses_event_destination" "deliveries" {
  name                   = "deliveries"
  configuration_set_name = aws_ses_configuration_set.main.name
  enabled                = true
  matching_types         = ["delivery"]

  sns_destination {
    topic_arn = aws_sns_topic.ses_deliveries.arn
  }
}

# IAM Policy for SES Sending
resource "aws_iam_policy" "ses_send_policy" {
  name        = "${var.project_name}-ses-send-policy"
  description = "Policy for sending emails via SES"

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

# IAM Policy for SES Configuration
resource "aws_iam_policy" "ses_config_policy" {
  name        = "${var.project_name}-ses-config-policy"
  description = "Policy for SES configuration management"

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

# Note: SES permissions are now handled via IAM roles attached to EC2 instances and GitHub Actions
# No IAM users or access keys are needed for SES authentication

# Add SES permissions to existing EC2 role
resource "aws_iam_role_policy" "ec2_ses_policy" {
  name = "${var.project_name}-ec2-ses-policy"
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

# Add SES permissions to GitHub Actions role
resource "aws_iam_role_policy" "github_actions_ses" {
  name = "${var.project_name}-github-actions-ses-policy"
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

# Email Address Verification for Testing
# These email addresses will be verified programmatically for testing purposes
resource "aws_ses_email_identity" "test_emails" {
  for_each = toset(var.test_email_addresses)
  email     = each.value
}

# Variable for test email addresses
# Add this to your variables.tf file
# variable "test_email_addresses" {
#   description = "List of email addresses to verify for testing"
#   type        = list(string)
#   default     = ["your-email@example.com", "admin@cyberworldbuilders.dev"]
# }
