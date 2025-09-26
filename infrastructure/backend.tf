terraform {
  backend "s3" {
    bucket         = "talent-assessment-terraform-state-1758896259"
    key            = "infrastructure/terraform.tfstate"
    region         = "us-east-2"
    dynamodb_table = "talent-assessment-terraform-locks"
    encrypt        = true
    
    # Optional: Add versioning and lifecycle management
    # This ensures state files are versioned and can be recovered
  }
}
