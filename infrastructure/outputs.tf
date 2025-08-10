output "public_ip" {
  description = "Public IP of the EC2 instance"
  value       = aws_eip.dev_eip.public_ip
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
  value       = "ssh -i ~/.ssh/dev-key ubuntu@${aws_eip.dev_eip.public_ip}"
}

output "application_url" {
  description = "URL to access the application"
  value       = "http://${aws_eip.dev_eip.public_ip}"
}

output "traefik_dashboard_url" {
  description = "URL to access the Traefik dashboard"
  value       = "http://${aws_eip.dev_eip.public_ip}:8080"
}

output "deployment_summary" {
  description = "Summary of the deployment"
  value = <<-EOF
    🚀 Talent Assessment Development Environment Deployed Successfully!
    
    📍 Instance Details:
    - Instance ID: ${aws_instance.dev_instance.id}
    - Public IP: ${aws_eip.dev_eip.public_ip}
    - Instance Type: ${aws_instance.dev_instance.instance_type}
    
    🌐 Access URLs:
    - Application: http://${aws_eip.dev_eip.public_ip}
    - Traefik Dashboard: http://${aws_eip.dev_eip.public_ip}:8080
    
    🔑 SSH Access:
    ssh -i ~/.ssh/dev-key ubuntu@${aws_eip.dev_eip.public_ip}
    
    📋 Next Steps:
    1. Update your domain DNS to point to: ${aws_eip.dev_eip.public_ip}
    2. Upload your Laravel application files to the server
    3. Configure SSL certificates for HTTPS
    4. Set up environment variables for production
    
    ⚠️  Security Note: SSH is currently open to 0.0.0.0/0 for AWS console access.
    Consider restricting this to your IP address for production use.
  EOF
}
