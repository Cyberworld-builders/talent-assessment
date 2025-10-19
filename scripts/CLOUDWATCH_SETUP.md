# AWS CloudWatch Agent Setup

This document describes how to set up AWS CloudWatch logging for the Talent Assessment application.

## Overview

The CloudWatch agent sends application logs, system logs, and metrics to AWS CloudWatch for centralized monitoring and alerting.

## Prerequisites

1. EC2 instance with IAM role that has CloudWatch permissions (already configured in Terraform)
2. Terraform applied with `cloudwatch.tf` resources
3. Root/sudo access to the EC2 instance

## Installation

### 1. Apply Terraform Configuration

First, ensure the CloudWatch resources are created:

```bash
cd /opt/talent-assessment/infrastructure
terraform init
terraform apply
```

This creates:
- `/talent-assessment/production/laravel-scheduler` - Log group for Laravel scheduler
- `/talent-assessment/production/application` - Log group for application logs
- `/talent-assessment/production/system` - Log group for system logs
- IAM policies for CloudWatch access

### 2. Install CloudWatch Agent

SSH into your EC2 instance and run:

```bash
cd /opt/talent-assessment/scripts
sudo ./setup-cloudwatch-agent.sh production
```

For staging environment:
```bash
sudo ./setup-cloudwatch-agent.sh staging
```

The script will:
- ✅ Download and install the CloudWatch agent
- ✅ Create configuration file
- ✅ Set up log file permissions
- ✅ Start the CloudWatch agent
- ✅ Enable agent to start on boot

## What Gets Logged

### Laravel Scheduler Logs
- **File**: `/var/log/laravel-scheduler.log`
- **Log Group**: `/talent-assessment/{environment}/laravel-scheduler`
- **Log Stream**: `{instance-id}-scheduler`
- **Contains**: Output from the Laravel scheduler cron job

### Application Logs
- **File**: `/opt/talent-assessment/storage/logs/laravel.log`
- **Log Group**: `/talent-assessment/{environment}/application`
- **Log Stream**: `{instance-id}-laravel`
- **Contains**: Laravel application logs (errors, info, debug)

### System Logs
- **Files**: `/var/log/syslog`, `/var/log/auth.log`
- **Log Group**: `/talent-assessment/{environment}/system`
- **Log Streams**: `{instance-id}-syslog`, `{instance-id}-auth`
- **Contains**: System logs, authentication logs

## Metrics

The CloudWatch agent also sends system metrics:

- **Disk Usage** - Percentage of disk space used
- **Memory Usage** - Percentage of memory used
- **Namespace**: `TalentAssessment/{environment}`

## Viewing Logs

### AWS Console

1. Go to AWS CloudWatch Console
2. Navigate to **Logs > Log groups**
3. Select your log group (e.g., `/talent-assessment/production/laravel-scheduler`)
4. Click on a log stream to view logs

### AWS CLI

```bash
# Tail scheduler logs (live)
aws logs tail /talent-assessment/production/laravel-scheduler --follow

# Tail application logs
aws logs tail /talent-assessment/production/application --follow

# View recent logs
aws logs tail /talent-assessment/production/laravel-scheduler --since 1h

# Filter logs
aws logs tail /talent-assessment/production/application --follow --filter-pattern "ERROR"
```

### CloudWatch Insights

Run queries across your logs:

```sql
# Find all errors in the last hour
fields @timestamp, @message
| filter @message like /ERROR/
| sort @timestamp desc
| limit 100

# Count errors by type
fields @timestamp, @message
| filter @message like /ERROR/
| stats count() by @message
| sort count desc
```

## Monitoring & Alerts

### Set Up Alarms

You can create CloudWatch alarms for critical events:

```bash
# Example: Alert on high disk usage
aws cloudwatch put-metric-alarm \
    --alarm-name talent-assessment-high-disk-usage \
    --alarm-description "Alert when disk usage exceeds 80%" \
    --metric-name DiskUsedPercent \
    --namespace TalentAssessment/production \
    --statistic Average \
    --period 300 \
    --threshold 80 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 2
```

### Log Metric Filters

Create metric filters to track specific events:

```bash
# Example: Track failed logins
aws logs put-metric-filter \
    --log-group-name /talent-assessment/production/application \
    --filter-name FailedLogins \
    --filter-pattern "[time, level=ERROR, message=*login*failed*]" \
    --metric-transformations \
        metricName=FailedLoginAttempts,metricNamespace=TalentAssessment/Security,metricValue=1
```

## Troubleshooting

### Check Agent Status

```bash
sudo /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl -a status
```

### Restart Agent

```bash
sudo systemctl restart amazon-cloudwatch-agent
```

### View Agent Logs

```bash
sudo tail -f /opt/aws/amazon-cloudwatch-agent/logs/amazon-cloudwatch-agent.log
```

### Verify IAM Permissions

Ensure the EC2 instance has the correct IAM role attached:

```bash
aws sts get-caller-identity
```

### Test Log Sending

```bash
# Write a test log entry
echo "$(date): Test log entry" >> /var/log/laravel-scheduler.log

# Wait 30 seconds and check CloudWatch
aws logs tail /talent-assessment/production/laravel-scheduler --since 5m
```

## Cost Considerations

### Log Ingestion
- First 5 GB/month: Free
- After 5 GB: $0.50 per GB

### Log Storage
- First 5 GB/month: Free
- After 5 GB: $0.03 per GB per month

### Retention Settings
- Scheduler logs: 30 days
- Application logs: 30 days  
- System logs: 14 days

Adjust retention in `cloudwatch.tf` to manage costs.

## Log Rotation

The CloudWatch agent automatically handles log rotation. Local log files are kept for:
- `/var/log/laravel-scheduler.log` - Rotated daily by CloudWatch agent
- Laravel logs - Managed by Laravel (daily rotation)
- System logs - Managed by logrotate

## Security

- All logs are encrypted at rest using AWS KMS
- Only EC2 instances with the correct IAM role can write logs
- Log groups are created with appropriate retention policies
- Access to CloudWatch Logs requires IAM permissions

## Maintenance

### Update Agent Configuration

1. Edit `/opt/aws/amazon-cloudwatch-agent/etc/cloudwatch-config.json`
2. Restart the agent:
   ```bash
   sudo systemctl restart amazon-cloudwatch-agent
   ```

### Upgrade Agent

```bash
# Download latest version
wget https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb

# Install update
sudo dpkg -i -E ./amazon-cloudwatch-agent.deb

# Restart agent
sudo systemctl restart amazon-cloudwatch-agent
```

## References

- [AWS CloudWatch Agent Documentation](https://docs.aws.amazon.com/AmazonCloudWatch/latest/monitoring/Install-CloudWatch-Agent.html)
- [CloudWatch Logs Insights Query Syntax](https://docs.aws.amazon.com/AmazonCloudWatch/latest/logs/CWL_QuerySyntax.html)
- [CloudWatch Pricing](https://aws.amazon.com/cloudwatch/pricing/)

