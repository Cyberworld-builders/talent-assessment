# Supabase IAM Role for AWS SES Access
# This allows Supabase Edge Functions to assume AWS IAM roles for SES email sending
# Note: Supabase Edge Functions don't have a built-in OIDC provider like Vercel,
# so we use AWS STS AssumeRole with External ID for security

locals {
  # Supabase project reference (set in terraform.tfvars, optional)
  supabase_project_ref = var.supabase_project_ref != null && var.supabase_project_ref != "" ? var.supabase_project_ref : ""

  # External ID for additional security (required)
  # This should be a secure random string stored in terraform.tfvars
  supabase_external_id = var.supabase_external_id != null && var.supabase_external_id != "" ? var.supabase_external_id : "supabase-edge-functions-${var.project_name}"
}

# IAM Role for Supabase Edge Functions (SES Access)
# This role can be assumed by Supabase Edge Functions using AWS STS
resource "aws_iam_role" "supabase_ses_role" {
  name = "${var.project_name}-supabase-ses-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          # Allow Supabase Edge Functions to assume this role
          # Using AWS account root as principal with conditions for security
          AWS = "arn:aws:iam::${data.aws_caller_identity.current.account_id}:root"
        }
        Action = "sts:AssumeRole"
        Condition = {
          # Require external ID for additional security
          # This should match the external ID configured in Supabase
          StringEquals = {
            "sts:ExternalId" = local.supabase_external_id
          }
          # Optional: Restrict to specific IP ranges if Supabase publishes them
          # IpAddress = {
          #   "aws:SourceIp" = [
          #     "SUPABASE_IP_RANGE_1",
          #     "SUPABASE_IP_RANGE_2"
          #   ]
          # }
        }
      }
    ]
  })

  tags = {
    Name        = "${var.project_name}-supabase-ses-role"
    Environment = "Production"
    Purpose     = "Supabase Edge Functions SES Email Sending"
  }
}

# Alternative: OIDC-based approach if Supabase provides OIDC endpoint
# Uncomment and configure if Supabase adds OIDC support
# resource "aws_iam_openid_connect_provider" "supabase" {
#   url = "https://api.supabase.com/v1/oidc"
# 
#   client_id_list = [
#     "supabase-edge-functions"
#   ]
# 
#   thumbprint_list = [
#     "SUPABASE_OIDC_THUMBPRINT" # Update with actual thumbprint
#   ]
# 
#   tags = {
#     Name        = "${var.project_name}-supabase-oidc-provider"
#     Environment = "Production"
#   }
# }

# Attach SES send policy to Supabase role
resource "aws_iam_role_policy_attachment" "supabase_ses_send_policy" {
  role       = aws_iam_role.supabase_ses_role.name
  policy_arn = aws_iam_policy.ses_production_send_policy.arn
}

# Attach SES config policy to Supabase role (for quota/statistics)
resource "aws_iam_role_policy_attachment" "supabase_ses_config_policy" {
  role       = aws_iam_role.supabase_ses_role.name
  policy_arn = aws_iam_policy.ses_production_config_policy.arn
}

# IAM User for Supabase Edge Functions
# This user has minimal permissions - only to assume the role
# The actual SES permissions are on the role, not the user
resource "aws_iam_user" "supabase_edge_functions" {
  name = "${var.project_name}-supabase-edge-functions"
  path = "/supabase/"

  tags = {
    Name        = "${var.project_name}-supabase-edge-functions"
    Environment = "Production"
    Purpose     = "Supabase Edge Functions - Role Assumption Only"
  }
}

# Policy allowing the user to assume the Supabase SES role
resource "aws_iam_policy" "supabase_assume_role_policy" {
  name        = "${var.project_name}-supabase-assume-role-policy"
  description = "Allows Supabase Edge Functions user to assume the SES role"

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect   = "Allow"
        Action   = "sts:AssumeRole"
        Resource = aws_iam_role.supabase_ses_role.arn
        Condition = {
          StringEquals = {
            "sts:ExternalId" = local.supabase_external_id
          }
        }
      }
    ]
  })
}

# Attach assume role policy to the user
resource "aws_iam_user_policy_attachment" "supabase_assume_role" {
  user       = aws_iam_user.supabase_edge_functions.name
  policy_arn = aws_iam_policy.supabase_assume_role_policy.arn
}

# Create access keys for the user (to be stored in Supabase Vault)
# Note: These keys only allow assuming the role, not direct SES access
resource "aws_iam_access_key" "supabase_edge_functions" {
  user = aws_iam_user.supabase_edge_functions.name
}

# Get current AWS account ID
data "aws_caller_identity" "current" {}

# Output the role ARN for Supabase configuration
output "supabase_ses_role_arn" {
  description = "ARN of the IAM role for Supabase Edge Functions SES access"
  value       = aws_iam_role.supabase_ses_role.arn
}

# Output the IAM user access keys (store securely in Supabase Vault)
output "supabase_access_key_id" {
  description = "Access Key ID for Supabase Edge Functions user - Store in Supabase Vault"
  value       = aws_iam_access_key.supabase_edge_functions.id
  sensitive   = false
}

output "supabase_secret_access_key" {
  description = "Secret Access Key for Supabase Edge Functions user - Store in Supabase Vault (SECRET!)"
  value       = aws_iam_access_key.supabase_edge_functions.secret
  sensitive   = true
}

# Output setup instructions
output "supabase_oidc_setup_instructions" {
  description = "Instructions for setting up Supabase Edge Functions with AWS SES"
  value       = <<-EOF
    🔐 Supabase Edge Functions AWS SES Setup Instructions:
    
    1. After running 'terraform apply', copy the Role ARN:
       ${aws_iam_role.supabase_ses_role.arn}
    
    2. In Supabase Dashboard, go to your project Settings > Edge Functions > Environment Variables
    
    3. Store access keys in Supabase Vault (Dashboard > Settings > Vault):
       - Access Key ID: ${aws_iam_access_key.supabase_edge_functions.id}
       - Secret Access Key: ${aws_iam_access_key.supabase_edge_functions.secret} (SECRET - copy from terraform output)
    
    4. Add the following environment variables to Edge Functions:
       - AWS_ROLE_ARN = ${aws_iam_role.supabase_ses_role.arn}
       - AWS_REGION = us-east-1 (or your SES region)
       - AWS_EXTERNAL_ID = ${local.supabase_external_id}
       - AWS_ACCESS_KEY_ID = (retrieve from Vault)
       - AWS_SECRET_ACCESS_KEY = (retrieve from Vault)
       - EMAIL_FROM = noreply@involvedtalent.com
    
    5. In your Supabase Edge Function, use AWS SDK with AssumeRole:
       
       ```typescript
       import { STSClient, AssumeRoleCommand } from 'https://esm.sh/@aws-sdk/client-sts@3';
       import { SESClient, SendEmailCommand } from 'https://esm.sh/@aws-sdk/client-ses@3';
       
       // Assume the role
       const stsClient = new STSClient({ region: Deno.env.get('AWS_REGION') });
       const assumeRoleResponse = await stsClient.send(new AssumeRoleCommand({
         RoleArn: Deno.env.get('AWS_ROLE_ARN')!,
         RoleSessionName: 'supabase-edge-function',
         ExternalId: Deno.env.get('AWS_EXTERNAL_ID'),
         DurationSeconds: 3600, // 1 hour
       }));
       
       // Use temporary credentials for SES
       const sesClient = new SESClient({
         region: Deno.env.get('AWS_REGION'),
         credentials: {
           accessKeyId: assumeRoleResponse.Credentials!.AccessKeyId!,
           secretAccessKey: assumeRoleResponse.Credentials!.SecretAccessKey!,
           sessionToken: assumeRoleResponse.Credentials!.SessionToken!,
         },
       });
       ```
    
    ✅ Benefits of this Role-based approach:
    - Access keys only allow assuming the role (not direct SES access)
    - Actual SES operations use short-lived credentials (1 hour)
    - Follows AWS security best practices (principle of least privilege)
    - If access keys are compromised, attacker can only assume role (still limited)
    - Credentials are automatically rotated for SES operations
    
    🔒 Security Notes:
    - Access keys stored in Supabase Vault (encrypted at rest)
    - External ID provides additional security layer
    - Keep the external ID secret (stored in terraform.tfvars)
    - Rotate access keys periodically
    - The External ID is: ${local.supabase_external_id}
    
    📋 Architecture:
    1. IAM User (minimal permissions) → Can assume role
    2. IAM Role (SES permissions) → Used for actual email sending
    3. Edge Function uses user credentials to assume role
    4. Edge Function uses role credentials (temporary) for SES
    
    📝 Alternative: If Supabase adds OIDC support in the future:
    - Uncomment the OIDC provider section in this file
    - Update the trust policy to use Federated principal
    - Follow similar pattern to vercel-oidc.tf
  EOF
}
