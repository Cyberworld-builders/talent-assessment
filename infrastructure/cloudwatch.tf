# CloudWatch Log Group for Laravel Scheduler
resource "aws_cloudwatch_log_group" "laravel_scheduler" {
  name              = "/talent-assessment/${var.environment}/laravel-scheduler"
  retention_in_days = 30

  tags = {
    Name        = "${var.project_name}-${var.environment}-scheduler-logs"
    Environment = var.environment
    Application = "laravel-scheduler"
  }
}

# CloudWatch Log Group for Application Logs
resource "aws_cloudwatch_log_group" "application" {
  name              = "/talent-assessment/${var.environment}/application"
  retention_in_days = 30

  tags = {
    Name        = "${var.project_name}-${var.environment}-application-logs"
    Environment = var.environment
    Application = "talent-assessment"
  }
}

# CloudWatch Log Group for System Logs
resource "aws_cloudwatch_log_group" "system" {
  name              = "/talent-assessment/${var.environment}/system"
  retention_in_days = 14

  tags = {
    Name        = "${var.project_name}-${var.environment}-system-logs"
    Environment = var.environment
  }
}

# IAM Policy for CloudWatch Logs
resource "aws_iam_role_policy" "cloudwatch_logs_policy" {
  name = "${var.project_name}-cloudwatch-logs-policy"
  role = aws_iam_role.dev_role.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "logs:CreateLogGroup",
          "logs:CreateLogStream",
          "logs:PutLogEvents",
          "logs:DescribeLogStreams"
        ]
        Resource = [
          "arn:aws:logs:*:*:log-group:/talent-assessment/*",
          "arn:aws:logs:*:*:log-group:/talent-assessment/*:log-stream:*"
        ]
      },
      {
        Effect = "Allow"
        Action = [
          "cloudwatch:PutMetricData"
        ]
        Resource = "*"
      }
    ]
  })
}

