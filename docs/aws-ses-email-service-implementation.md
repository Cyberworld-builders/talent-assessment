# AWS SES Email Service Implementation Plan

## Overview

This document outlines the implementation of AWS Simple Email Service (SES) to replace the current Mailtrap email testing setup with a production-ready email service for the Talent Assessment application.

## Why AWS SES?

- **Cost-effective**: Pay only for emails sent (first 62,000 emails/month free from EC2)
- **High deliverability**: Built-in reputation management and bounce handling
- **Scalable**: Handles millions of emails per day
- **Integrated**: Works seamlessly with other AWS services
- **Compliant**: Supports SPF, DKIM, and DMARC authentication

## Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Laravel App   │───▶│   AWS SES       │───▶│   Recipients    │
│                 │    │                 │    │                 │
│ - Assessment    │    │ - Email Sending │    │ - Users         │
│   Assignments   │    │ - Bounce/Compl. │    │ - Admins        │
│ - Password      │    │   Handling      │    │ - Support       │
│   Resets        │    │ - Analytics     │    │                 │
│ - Notifications │    │                 │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Implementation Phases

### Phase 1: AWS SES Setup
- Create SES domain verification
- Configure DKIM authentication
- Set up IAM roles and policies
- Create SNS topics for bounce/complaint handling

### Phase 2: Application Integration
- Update Laravel mail configuration
- Modify Mailer class for SES
- Implement bounce/complaint handling
- Add email analytics

### Phase 3: Testing & Monitoring
- Test email delivery
- Monitor bounce rates
- Set up CloudWatch alarms
- Implement retry logic

## Terraform Implementation

### 1. SES Domain Configuration

```hcl
# ses.tf

# SES Domain Identity
resource "aws_ses_domain_identity" "main" {
  domain = var.domain_name
}

# SES Domain DKIM
resource "aws_ses_domain_dkim" "main" {
  domain = aws_ses_domain_identity.main.domain
}

# SES Domain Mail From
resource "aws_ses_domain_mail_from" "main" {
  domain           = aws_ses_domain_identity.main.domain
  mail_from_domain = "mail.${aws_ses_domain_identity.main.domain}"
}

# Route53 MX Record for mail subdomain
resource "aws_route53_record" "ses_mx" {
  count   = var.create_route53_records ? 1 : 0
  zone_id = var.route53_zone_id
  name    = aws_ses_domain_mail_from.main.mail_from_domain
  type    = "MX"
  ttl     = "600"
  records = ["10 feedback-smtp.${data.aws_region.current.name}.amazonses.com"]
}

# Route53 TXT Record for SPF
resource "aws_route53_record" "ses_txt" {
  count   = var.create_route53_records ? 1 : 0
  zone_id = var.route53_zone_id
  name    = aws_ses_domain_mail_from.main.mail_from_domain
  type    = "TXT"
  ttl     = "600"
  records = ["v=spf1 include:amazonses.com ~all"]
}

# Route53 CNAME Records for DKIM
resource "aws_route53_record" "ses_dkim" {
  count   = var.create_route53_records ? 3 : 0
  zone_id = var.route53_zone_id
  name    = "${element(aws_ses_domain_dkim.main.dkim_tokens, count.index)}._domainkey.${aws_ses_domain_identity.main.domain}"
  type    = "CNAME"
  ttl     = "600"
  records = ["${element(aws_ses_domain_dkim.main.dkim_tokens, count.index)}.dkim.amazonses.com"]
}

# Route53 TXT Record for domain verification
resource "aws_route53_record" "ses_verification" {
  count   = var.create_route53_records ? 1 : 0
  zone_id = var.route53_zone_id
  name    = "_amazonses.${aws_ses_domain_identity.main.domain}"
  type    = "TXT"
  ttl     = "600"
  records = [aws_ses_domain_identity.main.verification_token]
}
```

### 2. SNS Topics for Bounce/Complaint Handling

```hcl
# sns.tf

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
```

### 3. IAM Roles and Policies

```hcl
# iam.tf

# IAM Role for SES
resource "aws_iam_role" "ses_role" {
  name = "${var.project_name}-ses-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ses.amazonaws.com"
        }
      }
    ]
  })
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

# Attach policies to role
resource "aws_iam_role_policy_attachment" "ses_send" {
  role       = aws_iam_role.ses_role.name
  policy_arn = aws_iam_policy.ses_send_policy.arn
}

resource "aws_iam_role_policy_attachment" "ses_config" {
  role       = aws_iam_role.ses_role.name
  policy_arn = aws_iam_policy.ses_config_policy.arn
}

# Note: IAM users and access keys are deprecated in favor of IAM roles
# The following resources are kept for reference but should not be used in production

# IAM User for SES API access (DEPRECATED)
resource "aws_iam_user" "ses_user" {
  name = "${var.project_name}-ses-user"
  count = 0  # Disabled - use IAM roles instead
}

# IAM Access Key for SES User (DEPRECATED)
resource "aws_iam_access_key" "ses_user" {
  user = aws_iam_user.ses_user[0].name
  count = 0  # Disabled - use IAM roles instead
}

# Attach SES policies to user (DEPRECATED)
resource "aws_iam_user_policy_attachment" "ses_user_send" {
  user       = aws_iam_user.ses_user[0].name
  policy_arn = aws_iam_policy.ses_send_policy.arn
  count = 0  # Disabled - use IAM roles instead
}

resource "aws_iam_user_policy_attachment" "ses_user_config" {
  user       = aws_iam_user.ses_user[0].name
  policy_arn = aws_iam_policy.ses_config_policy.arn
  count = 0  # Disabled - use IAM roles instead
}
```

### 4. IAM Role-Based Authentication (Recommended Approach)

**Note**: This approach eliminates the need for long-lived AWS access keys. The EC2 instance and GitHub Actions will authenticate with AWS using their respective IAM roles.

```hcl
# Attach SES policy to EC2 instance role
resource "aws_iam_role_policy" "ec2_ses_policy" {
  name = "${var.project_name}-ec2-ses-policy"
  role = aws_iam_role.ec2_role.id

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

# Attach SES policy to GitHub Actions role
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
```

### 5. Lambda Functions for Bounce/Complaint Handling

```hcl
# lambda.tf

# Lambda function for handling bounces
resource "aws_lambda_function" "ses_bounce_handler" {
  filename         = "lambda/ses_bounce_handler.zip"
  function_name    = "${var.project_name}-ses-bounce-handler"
  role            = aws_iam_role.lambda_role.arn
  handler         = "index.handler"
  runtime         = "nodejs18.x"
  timeout         = 30

  environment {
    variables = {
      DATABASE_URL = var.database_url
    }
  }
}

# Lambda function for handling complaints
resource "aws_lambda_function" "ses_complaint_handler" {
  filename         = "lambda/ses_complaint_handler.zip"
  function_name    = "${var.project_name}-ses-complaint-handler"
  role            = aws_iam_role.lambda_role.arn
  handler         = "index.handler"
  runtime         = "nodejs18.x"
  timeout         = 30

  environment {
    variables = {
      DATABASE_URL = var.database_url
    }
  }
}

# SNS Subscription for bounces
resource "aws_sns_topic_subscription" "bounce_lambda" {
  topic_arn = aws_sns_topic.ses_bounces.arn
  protocol  = "lambda"
  endpoint  = aws_lambda_function.ses_bounce_handler.arn
}

# SNS Subscription for complaints
resource "aws_sns_topic_subscription" "complaint_lambda" {
  topic_arn = aws_sns_topic.ses_complaints.arn
  protocol  = "lambda"
  endpoint  = aws_lambda_function.ses_complaint_handler.arn
}

# Lambda permission for SNS
resource "aws_lambda_permission" "bounce_sns" {
  statement_id  = "AllowExecutionFromSNS"
  action        = "lambda:InvokeFunction"
  function_name = aws_lambda_function.ses_bounce_handler.function_name
  principal     = "sns.amazonaws.com"
  source_arn    = aws_sns_topic.ses_bounces.arn
}

resource "aws_lambda_permission" "complaint_sns" {
  statement_id  = "AllowExecutionFromSNS"
  action        = "lambda:InvokeFunction"
  function_name = aws_lambda_function.ses_complaint_handler.function_name
  principal     = "sns.amazonaws.com"
  source_arn    = aws_sns_topic.ses_complaints.arn
}
```

### 5. Variables and Outputs

```hcl
# variables.tf

variable "domain_name" {
  description = "Domain name for SES configuration"
  type        = string
}

variable "project_name" {
  description = "Project name for resource naming"
  type        = string
  default     = "talent-assessment"
}

variable "create_route53_records" {
  description = "Whether to create Route53 records"
  type        = bool
  default     = true
}

variable "route53_zone_id" {
  description = "Route53 hosted zone ID"
  type        = string
  default     = ""
}

variable "database_url" {
  description = "Database connection URL for Lambda functions"
  type        = string
  sensitive   = true
}

# outputs.tf

output "ses_domain_identity" {
  description = "SES domain identity"
  value       = aws_ses_domain_identity.main.domain
}

output "ses_dkim_tokens" {
  description = "DKIM tokens for domain verification"
  value       = aws_ses_domain_dkim.main.dkim_tokens
}

output "ses_access_key_id" {
  description = "SES access key ID"
  value       = aws_iam_access_key.ses_user.id
}

output "ses_secret_access_key" {
  description = "SES secret access key"
  value       = aws_iam_access_key.ses_user.secret
  sensitive   = true
}

output "ses_configuration_set" {
  description = "SES configuration set name"
  value       = aws_ses_configuration_set.main.name
}
```

## Application Changes

### 1. Update Laravel Mail Configuration

```php
// config/mail.php

return [
    'default' => env('MAIL_MAILER', 'ses'),
    
    'mailers' => [
        'ses' => [
            'transport' => 'ses',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'options' => [
                'ConfigurationSetName' => env('SES_CONFIGURATION_SET'),
            ],
        ],
        // ... other mailers
    ],
    
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@yourdomain.com'),
        'name' => env('MAIL_FROM_NAME', 'Talent Assessment'),
    ],
];
```

### 2. Update Environment Variables

#### Option A: IAM Role-Based Authentication (Recommended)

```bash
# .env.production

# Mail Configuration
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Talent Assessment

# AWS Configuration (only region needed - authentication via IAM role)
AWS_DEFAULT_REGION=us-east-1

# SES Configuration
SES_CONFIGURATION_SET=talent-assessment-ses-config

# Remove Mailtrap configuration
# MAIL_HOST=sandbox.smtp.mailtrap.io
# MAIL_USERNAME=cd877c53a7d010
# MAIL_PASSWORD=718d08c34c9cba
# MAIL_ENCRYPTION=tls
```

#### Option B: Access Key Authentication (Legacy - Not Recommended)

```bash
# .env.production

# AWS Configuration
AWS_ACCESS_KEY_ID=your_ses_access_key_id
AWS_SECRET_ACCESS_KEY=your_ses_secret_access_key
AWS_DEFAULT_REGION=us-east-1

# SES Configuration
MAIL_MAILER=ses
SES_CONFIGURATION_SET=talent-assessment-ses-config
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Talent Assessment
```

**Note**: Option A is preferred as it eliminates the need for long-lived access keys and leverages AWS IAM roles for secure authentication.

### 3. Update Mailer Class

```php
// app/Mailer.php

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class Mailer
{
    private $sesClient;
    private $configurationSet;
    
    public function __construct()
    {
        $this->sesClient = new SesClient([
            'version' => 'latest',
            'region'  => config('mail.mailers.ses.region'),
            // No credentials needed - AWS SDK will use IAM role automatically
        ]);
        
        $this->configurationSet = config('mail.mailers.ses.options.ConfigurationSetName');
    }
    
    public function send_assignments($user, $ids, $expiration, $subject, $body)
    {
        try {
            $result = $this->sesClient->sendEmail([
                'Source' => config('mail.from.address'),
                'Destination' => [
                    'ToAddresses' => [$user->email],
                ],
                'Message' => [
                    'Subject' => [
                        'Data' => $subject,
                        'Charset' => 'UTF-8',
                    ],
                    'Body' => [
                        'Html' => [
                            'Data' => $this->processEmailBody($body, $user, $ids, $expiration),
                            'Charset' => 'UTF-8',
                        ],
                    ],
                ],
                'ConfigurationSetName' => $this->configurationSet,
            ]);
            
            // Log successful email
            \Log::info('Email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message_id' => $result['MessageId'],
            ]);
            
            return true;
            
        } catch (AwsException $e) {
            \Log::error('SES email sending failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    // ... other methods with similar SES integration
}
```

### 4. Create Email Tracking Model

```php
// app/Models/EmailLog.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'subject',
        'message_id',
        'status',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'complained_at',
    ];
    
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
        'complained_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 5. Create Email Tracking Migration

```php
// database/migrations/2025_08_29_create_email_logs_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailLogsTable extends Migration
{
    public function up()
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('subject');
            $table->string('message_id')->unique();
            $table->enum('status', ['sent', 'delivered', 'bounced', 'complained'])->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->text('bounce_reason')->nullable();
            $table->text('complaint_reason')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('message_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('email_logs');
    }
}
```

## Lambda Functions

### 1. Bounce Handler

```javascript
// lambda/ses_bounce_handler.js

const mysql = require('mysql2/promise');

exports.handler = async (event) => {
    const connection = await mysql.createConnection(process.env.DATABASE_URL);
    
    try {
        for (const record of event.Records) {
            const message = JSON.parse(record.Sns.Message);
            
            if (message.bounce) {
                // Handle bounce
                await handleBounce(connection, message);
            }
        }
        
        return { statusCode: 200, body: 'Success' };
    } catch (error) {
        console.error('Error processing bounce:', error);
        return { statusCode: 500, body: 'Error' };
    } finally {
        await connection.end();
    }
};

async function handleBounce(connection, message) {
    const bounce = message.bounce;
    
    for (const recipient of bounce.bouncedRecipients) {
        await connection.execute(
            'UPDATE email_logs SET status = ?, bounced_at = ?, bounce_reason = ? WHERE message_id = ?',
            ['bounced', new Date(), bounce.bounceType, message.mail.messageId]
        );
        
        // Optionally mark user as bounced
        await connection.execute(
            'UPDATE users SET email_bounced = 1 WHERE email = ?',
            [recipient.emailAddress]
        );
    }
}
```

### 2. Complaint Handler

```javascript
// lambda/ses_complaint_handler.js

const mysql = require('mysql2/promise');

exports.handler = async (event) => {
    const connection = await mysql.createConnection(process.env.DATABASE_URL);
    
    try {
        for (const record of event.Records) {
            const message = JSON.parse(record.Sns.Message);
            
            if (message.complaint) {
                // Handle complaint
                await handleComplaint(connection, message);
            }
        }
        
        return { statusCode: 200, body: 'Success' };
    } catch (error) {
        console.error('Error processing complaint:', error);
        return { statusCode: 500, body: 'Error' };
    } finally {
        await connection.end();
    }
};

async function handleComplaint(connection, message) {
    const complaint = message.complaint;
    
    for (const recipient of complaint.complainedRecipients) {
        await connection.execute(
            'UPDATE email_logs SET status = ?, complained_at = ?, complaint_reason = ? WHERE message_id = ?',
            ['complained', new Date(), complaint.complaintFeedbackType, message.mail.messageId]
        );
        
        // Optionally mark user as complained
        await connection.execute(
            'UPDATE users SET email_complained = 1 WHERE email = ?',
            [recipient.emailAddress]
        );
    }
}
```

## Deployment Steps

### 1. Terraform Deployment

```bash
# Initialize Terraform
terraform init

# Plan the deployment
terraform plan -var="domain_name=yourdomain.com" -var="route53_zone_id=Z1234567890ABC"

# Apply the configuration
terraform apply -var="domain_name=yourdomain.com" -var="route53_zone_id=Z1234567890ABC"

# Get outputs
terraform output ses_access_key_id
terraform output ses_secret_access_key
```

### 2. Domain Verification

1. After deployment, verify your domain in SES console
2. Add DKIM records to your DNS
3. Wait for verification (can take up to 72 hours)

### 3. Application Deployment

1. Update environment variables with SES credentials
2. Deploy application changes
3. Test email functionality
4. Monitor bounce rates and complaints

## Monitoring and Alerts

### CloudWatch Alarms

```hcl
# monitoring.tf

# Bounce Rate Alarm
resource "aws_cloudwatch_metric_alarm" "bounce_rate" {
  alarm_name          = "${var.project_name}-bounce-rate"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = "2"
  metric_name         = "Bounce"
  namespace           = "AWS/SES"
  period              = "300"
  statistic           = "Sum"
  threshold           = "5"
  alarm_description   = "Bounce rate is too high"
  
  dimensions = {
    ConfigurationSet = aws_ses_configuration_set.main.name
  }
}

# Complaint Rate Alarm
resource "aws_cloudwatch_metric_alarm" "complaint_rate" {
  alarm_name          = "${var.project_name}-complaint-rate"
  comparison_operator = "GreaterThanThreshold"
  evaluation_periods  = "2"
  metric_name         = "Complaint"
  namespace           = "AWS/SES"
  period              = "300"
  statistic           = "Sum"
  threshold           = "1"
  alarm_description   = "Complaint rate is too high"
  
  dimensions = {
    ConfigurationSet = aws_ses_configuration_set.main.name
  }
}
```

## Cost Estimation

### SES Pricing (us-east-1)
- **First 62,000 emails/month**: Free (from EC2)
- **Additional emails**: $0.10 per 1,000 emails
- **Data transfer**: $0.09 per GB

### Estimated Monthly Costs
- **1,000 emails/month**: $0.00 (free tier)
- **10,000 emails/month**: $0.00 (free tier)
- **100,000 emails/month**: $3.80
- **1,000,000 emails/month**: $93.80

## Security Considerations

1. **IAM Roles**: Use least privilege principle
2. **Encryption**: All data in transit is encrypted
3. **Access Keys**: Rotate access keys regularly
4. **Monitoring**: Set up CloudTrail for API logging
5. **Compliance**: SES supports GDPR and other compliance frameworks

## Testing Strategy

### 1. Sandbox Mode
- Start in SES sandbox mode for testing
- Request production access when ready

### 2. Email Testing
- Test all email types (assignments, password resets, etc.)
- Verify bounce/complaint handling
- Test with various email providers

### 3. Load Testing
- Test with high email volumes
- Monitor performance and costs

### 4. IAM Role-Based Authentication Testing

#### Testing SES Configuration
```bash
# Create a test script to verify SES functionality
cat > test-ses-email.php << 'EOF'
<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "Starting SES email test...\n";

try {
    // Display current mail configuration
    echo "Mail driver: " . Config::get('mail.default') . "\n";
    echo "Mail from address: " . Config::get('mail.from.address') . "\n";
    echo "Mail from name: " . Config::get('mail.from.name') . "\n";
    echo "SES region: " . Config::get('services.ses.region') . "\n";
    
    // Test sending a simple email
    echo "Attempting to send test email...\n";
    
    Mail::raw('This is a test email from the staging environment to verify SES configuration is working properly.', function($message) {
        $message->to('test@example.com')
                ->subject('SES Test - Staging Environment')
                ->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
    });
    
    echo "SUCCESS: Test email sent successfully!\n";
    echo "SES configuration is working properly.\n";
    
} catch (Exception $e) {
    echo "ERROR: Failed to send test email\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    exit(1);
}

echo "SES email test completed successfully!\n";
EOF

# Run the test
php test-ses-email.php
```

#### Integration with Deployment Scripts
The SES test is now integrated into the deployment script (`deploy-staging.sh`) and will run automatically after deployment to verify email functionality.

#### Testing in CI/CD
- SES testing is removed from GitHub Actions workflows
- Tests run locally on the server after deployment
- No need for AWS credentials in CI/CD environment

## Migration Plan

### Phase 1: Setup (Week 1)
1. Deploy Terraform infrastructure
2. Verify domain and DKIM
3. Update application configuration
4. Test basic email sending

### Phase 2: Integration (Week 2)
1. Deploy Lambda functions
2. Implement email tracking
3. Test bounce/complaint handling
4. Set up monitoring

### Phase 3: Production (Week 3)
1. Request SES production access
2. Deploy to production
3. Monitor and optimize
4. Document procedures

## Rollback Plan

If issues arise:
1. Revert to Mailtrap configuration
2. Update environment variables
3. Restart application services
4. Investigate and fix issues

## Success Metrics

- **Email Delivery Rate**: > 95%
- **Bounce Rate**: < 5%
- **Complaint Rate**: < 0.1%
- **Cost per Email**: < $0.001
- **Delivery Time**: < 5 minutes

## Next Steps

1. Review this implementation plan
2. Provide feedback and approval
3. Deploy Terraform infrastructure
4. Configure application changes
5. Test and monitor email delivery
