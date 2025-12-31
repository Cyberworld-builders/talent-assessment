# Vercel OIDC Provider for AWS
# This allows Vercel deployments to assume AWS IAM roles without long-lived credentials
# Documentation: https://vercel.com/docs/security/secure-backend-access/oidc/aws

# OIDC Identity Provider for Vercel
# Supports both Team Mode and Global Mode
# Team Mode URL: https://oidc.vercel.com/[TEAM_SLUG]
# Global Mode URL: https://oidc.vercel.com
locals {
  vercel_oidc_url = var.vercel_team_slug != null && var.vercel_team_slug != "" ? "https://oidc.vercel.com/${var.vercel_team_slug}" : "https://oidc.vercel.com"
  vercel_audience = var.vercel_team_slug != null && var.vercel_team_slug != "" ? "https://vercel.com/${var.vercel_team_slug}" : "https://vercel.com"
  vercel_aud_key  = var.vercel_team_slug != null && var.vercel_team_slug != "" ? "oidc.vercel.com/${var.vercel_team_slug}:aud" : "oidc.vercel.com:aud"
}

resource "aws_iam_openid_connect_provider" "vercel" {
  url = local.vercel_oidc_url

  client_id_list = [
    local.vercel_audience
  ]

  thumbprint_list = [
    "9e99a48a9960b14926bb7f3b02e22da2b0ab7280" # Vercel's OIDC thumbprint
  ]

  tags = {
    Name = "${var.project_name}-vercel-oidc-provider"
    Environment = "Production"
  }
}

# IAM Role for Vercel Deployments (SES Access)
resource "aws_iam_role" "vercel_ses_role" {
  name = "${var.project_name}-vercel-ses-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          Federated = aws_iam_openid_connect_provider.vercel.arn
        }
        Action = "sts:AssumeRoleWithWebIdentity"
        Condition = {
          StringEquals = {
            # Audience validation - must match client_id_list
            (local.vercel_aud_key) = local.vercel_audience
          }
          # Optional: Restrict to specific projects/environments
          # Uncomment and modify as needed for stricter security
          # StringLike = {
          #   (var.vercel_team_slug != null && var.vercel_team_slug != "" 
          #     ? "oidc.vercel.com/${var.vercel_team_slug}:sub" 
          #     : "oidc.vercel.com:sub") = [
          #     "owner:${var.vercel_team_slug}:project:*:environment:production",
          #     "owner:${var.vercel_team_slug}:project:*:environment:preview"
          #   ]
          # }
        }
      }
    ]
  })

  tags = {
    Name = "${var.project_name}-vercel-ses-role"
    Environment = "Production"
    Purpose = "Vercel SES Email Sending via OIDC"
  }
}

# Attach SES send policy to Vercel role
resource "aws_iam_role_policy_attachment" "vercel_ses_send_policy" {
  role       = aws_iam_role.vercel_ses_role.name
  policy_arn = aws_iam_policy.ses_production_send_policy.arn
}

# Attach SES config policy to Vercel role (for quota/statistics)
resource "aws_iam_role_policy_attachment" "vercel_ses_config_policy" {
  role       = aws_iam_role.vercel_ses_role.name
  policy_arn = aws_iam_policy.ses_production_config_policy.arn
}

# Output the role ARN for Vercel configuration
output "vercel_ses_role_arn" {
  description = "ARN of the IAM role for Vercel SES access - Add to Vercel as AWS_ROLE_ARN"
  value       = aws_iam_role.vercel_ses_role.arn
}

# Output the OIDC provider ARN
output "vercel_oidc_provider_arn" {
  description = "ARN of the Vercel OIDC provider"
  value       = aws_iam_openid_connect_provider.vercel.arn
}

# Output setup instructions
output "vercel_oidc_setup_instructions" {
  description = "Instructions for setting up Vercel OIDC with AWS"
  value = <<-EOF
    🔐 Vercel OIDC Setup Instructions (No Long-Lived Credentials!):
    
    1. After running 'terraform apply', copy the Role ARN:
       ${aws_iam_role.vercel_ses_role.arn}
    
    2. In Vercel Dashboard, go to your project Settings > Environment Variables
    
    3. Add the following environment variable:
       - AWS_ROLE_ARN = ${aws_iam_role.vercel_ses_role.arn}
       - AWS_REGION = us-east-1 (or your SES region)
       - SMTP_FROM = staging@involvedtalent.com (for staging)
       - SMTP_FROM = noreply@involvedtalent.com (for production)
    
    4. Apply to: Production, Preview, and Development environments as needed
    
    5. In your Next.js API routes, use @vercel/functions to get AWS credentials:
       
       ```typescript
       import { awsCredentialsProvider } from '@vercel/functions/oidc';
       import { SESClient } from '@aws-sdk/client-ses';
       
       const sesClient = new SESClient({
         region: process.env.AWS_REGION || 'us-east-1',
         credentials: awsCredentialsProvider({
           roleArn: process.env.AWS_ROLE_ARN!,
         }),
       });
       ```
    
    ✅ Benefits:
    - No long-lived credentials stored in Vercel
    - Short-lived tokens (automatically rotated)
    - Follows AWS security best practices
    - Credentials are automatically refreshed
    
    📚 Documentation: https://vercel.com/docs/security/secure-backend-access/oidc/aws
  EOF
}
