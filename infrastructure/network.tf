# NAT Gateway for private subnet internet access
resource "aws_eip" "nat_gateway_eip" {
  domain = "vpc"
  
  tags = {
    Name        = "${var.project_name}-nat-gateway-eip"
    Environment = var.environment
  }
}

resource "aws_nat_gateway" "talent_assessment_nat" {
  allocation_id = aws_eip.nat_gateway_eip.id
  subnet_id     = aws_subnet.dev_subnet.id

  tags = {
    Name        = "${var.project_name}-nat-gateway"
    Environment = var.environment
  }

  depends_on = [aws_internet_gateway.dev_igw]
}

# Route table for private subnets
resource "aws_route_table" "private_rt" {
  vpc_id = aws_vpc.dev_vpc.id

  route {
    cidr_block     = "0.0.0.0/0"
    nat_gateway_id = aws_nat_gateway.talent_assessment_nat.id
  }

  tags = {
    Name        = "${var.project_name}-private-route-table"
    Environment = var.environment
  }
}

# Associate private subnets with private route table
resource "aws_route_table_association" "private_subnet_1_association" {
  subnet_id      = aws_subnet.private_subnet_1.id
  route_table_id = aws_route_table.private_rt.id
}

resource "aws_route_table_association" "private_subnet_2_association" {
  subnet_id      = aws_subnet.private_subnet_2.id
  route_table_id = aws_route_table.private_rt.id
}

# Application Load Balancer
resource "aws_lb" "talent_assessment_alb" {
  name               = "${var.project_name}-alb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.alb_sg.id]
  subnets            = [aws_subnet.dev_subnet.id]

  enable_deletion_protection = false

  tags = {
    Name        = "${var.project_name}-application-load-balancer"
    Environment = var.environment
  }
}

# Target group for ECS tasks
resource "aws_lb_target_group" "talent_assessment_tg" {
  name        = "${var.project_name}-tg"
  port        = 80
  protocol    = "HTTP"
  vpc_id      = aws_vpc.dev_vpc.id
  target_type = "ip"

  health_check {
    enabled             = true
    healthy_threshold   = 2
    interval            = 30
    matcher             = "200"
    path                = "/health"
    port                = "traffic-port"
    protocol            = "HTTP"
    timeout             = 5
    unhealthy_threshold = 2
  }

  tags = {
    Name        = "${var.project_name}-target-group"
    Environment = var.environment
  }
}

# ALB listener for HTTP (redirect to HTTPS)
resource "aws_lb_listener" "talent_assessment_http" {
  load_balancer_arn = aws_lb.talent_assessment_alb.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {
    type = "redirect"

    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}

# ALB listener for HTTPS (commented out until certificate is validated)
# resource "aws_lb_listener" "talent_assessment_https" {
#   load_balancer_arn = aws_lb.talent_assessment_alb.arn
#   port              = "443"
#   protocol          = "HTTPS"
#   ssl_policy        = "ELBSecurityPolicy-TLS-1-2-2017-01"
#   certificate_arn   = aws_acm_certificate_validation.talent_assessment_cert_validation.certificate_arn
#
#   default_action {
#     type             = "forward"
#     target_group_arn = aws_lb_target_group.talent_assessment_tg.arn
#   }
# }

# SSL Certificate for HTTPS (without Route53 validation for now)
resource "aws_acm_certificate" "talent_assessment_cert" {
  domain_name       = "my.involvedtalent.com"
  validation_method = "DNS"

  subject_alternative_names = [
    "*.involvedtalent.com"
  ]

  lifecycle {
    create_before_destroy = true
  }

  tags = {
    Name        = "${var.project_name}-ssl-certificate"
    Environment = var.environment
  }
}

# Note: Certificate validation and Route53 records will be added later
# when the hosted zone is available or created
