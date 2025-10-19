#!/bin/bash

# Setup AWS CloudWatch Agent for Talent Assessment
# This script installs and configures the CloudWatch agent to send logs to CloudWatch

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== AWS CloudWatch Agent Setup ===${NC}"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root or with sudo${NC}"
    exit 1
fi

# Determine environment (production or staging)
if [ -z "$1" ]; then
    echo -e "${YELLOW}Usage: $0 [production|staging]${NC}"
    echo "Example: sudo ./setup-cloudwatch-agent.sh production"
    exit 1
fi

ENVIRONMENT=$1

if [ "$ENVIRONMENT" != "production" ] && [ "$ENVIRONMENT" != "staging" ]; then
    echo -e "${RED}Environment must be 'production' or 'staging'${NC}"
    exit 1
fi

echo -e "${GREEN}Setting up CloudWatch agent for ${ENVIRONMENT} environment...${NC}"
echo ""

# Install CloudWatch agent
echo -e "${YELLOW}Installing CloudWatch agent...${NC}"
wget -q https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb
dpkg -i -E ./amazon-cloudwatch-agent.deb
rm amazon-cloudwatch-agent.deb

echo -e "${GREEN}✓ CloudWatch agent installed${NC}"
echo ""

# Create CloudWatch agent configuration
echo -e "${YELLOW}Creating CloudWatch agent configuration...${NC}"

cat > /opt/aws/amazon-cloudwatch-agent/etc/cloudwatch-config.json <<EOF
{
  "agent": {
    "metrics_collection_interval": 60,
    "run_as_user": "root"
  },
  "logs": {
    "logs_collected": {
      "files": {
        "collect_list": [
          {
            "file_path": "/var/log/laravel-scheduler.log",
            "log_group_name": "/talent-assessment/${ENVIRONMENT}/laravel-scheduler",
            "log_stream_name": "{instance_id}-scheduler",
            "timezone": "UTC",
            "timestamp_format": "%Y-%m-%d %H:%M:%S"
          },
          {
            "file_path": "/var/log/syslog",
            "log_group_name": "/talent-assessment/${ENVIRONMENT}/system",
            "log_stream_name": "{instance_id}-syslog",
            "timezone": "UTC"
          },
          {
            "file_path": "/var/log/auth.log",
            "log_group_name": "/talent-assessment/${ENVIRONMENT}/system",
            "log_stream_name": "{instance_id}-auth",
            "timezone": "UTC"
          },
          {
            "file_path": "/opt/talent-assessment/storage/logs/laravel.log",
            "log_group_name": "/talent-assessment/${ENVIRONMENT}/application",
            "log_stream_name": "{instance_id}-laravel",
            "timezone": "UTC",
            "timestamp_format": "[%Y-%m-%d %H:%M:%S]"
          }
        ]
      }
    }
  },
  "metrics": {
    "namespace": "TalentAssessment/${ENVIRONMENT}",
    "metrics_collected": {
      "disk": {
        "measurement": [
          {
            "name": "used_percent",
            "rename": "DiskUsedPercent",
            "unit": "Percent"
          }
        ],
        "metrics_collection_interval": 60,
        "resources": [
          "*"
        ]
      },
      "mem": {
        "measurement": [
          {
            "name": "mem_used_percent",
            "rename": "MemoryUsedPercent",
            "unit": "Percent"
          }
        ],
        "metrics_collection_interval": 60
      }
    }
  }
}
EOF

echo -e "${GREEN}✓ Configuration file created${NC}"
echo ""

# Create log file if it doesn't exist
touch /var/log/laravel-scheduler.log
chmod 666 /var/log/laravel-scheduler.log

# Create Laravel log file if it doesn't exist
mkdir -p /opt/talent-assessment/storage/logs
touch /opt/talent-assessment/storage/logs/laravel.log
chmod 666 /opt/talent-assessment/storage/logs/laravel.log
chown -R ubuntu:ubuntu /opt/talent-assessment/storage

echo -e "${GREEN}✓ Log files created${NC}"
echo ""

# Start CloudWatch agent
echo -e "${YELLOW}Starting CloudWatch agent...${NC}"
/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
    -a fetch-config \
    -m ec2 \
    -s \
    -c file:/opt/aws/amazon-cloudwatch-agent/etc/cloudwatch-config.json

echo -e "${GREEN}✓ CloudWatch agent started${NC}"
echo ""

# Enable CloudWatch agent to start on boot
systemctl enable amazon-cloudwatch-agent

echo -e "${GREEN}✓ CloudWatch agent enabled on boot${NC}"
echo ""

# Verify CloudWatch agent status
echo -e "${YELLOW}Checking CloudWatch agent status...${NC}"
/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
    -a status

echo ""
echo -e "${GREEN}=== CloudWatch Agent Setup Complete! ===${NC}"
echo ""
echo "Logs will be sent to the following CloudWatch Log Groups:"
echo "  - /talent-assessment/${ENVIRONMENT}/laravel-scheduler (Laravel Scheduler)"
echo "  - /talent-assessment/${ENVIRONMENT}/application (Application Logs)"
echo "  - /talent-assessment/${ENVIRONMENT}/system (System Logs)"
echo ""
echo "Metrics will be sent to namespace: TalentAssessment/${ENVIRONMENT}"
echo ""
echo "To view logs in CloudWatch:"
echo "  aws logs tail /talent-assessment/${ENVIRONMENT}/laravel-scheduler --follow"
echo ""
echo "To check agent status:"
echo "  sudo /opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl -a status"
echo ""
echo "To restart agent:"
echo "  sudo systemctl restart amazon-cloudwatch-agent"

