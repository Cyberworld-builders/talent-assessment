# S3 IAM Role Implementation for Talent Assessment

## Overview

This document describes the implementation of S3 file uploads using AWS IAM roles instead of long-lived access keys, following AWS security best practices.

## Architecture

### Infrastructure Components

1. **S3 Bucket**: `talent-assessment-{environment}-uploads-{random-suffix}`
   - **Private bucket** - no public access (security best practice)
   - Versioning enabled for file recovery
   - Located in the same region as the EC2 instance

2. **CloudFront Distribution**: CDN for serving images
   - **Public access** through CloudFront for image serving
   - Optimized caching for images (1 day default, 1 year max)
   - HTTPS enforcement and compression
   - Origin Access Identity for secure S3 access

3. **IAM Role**: Attached to EC2 instance profile
   - Permissions for S3 operations (Get, Put, Delete, List)
   - No long-lived credentials required

4. **Laravel Configuration**: Updated to use IAM roles and CloudFront
   - AWS SDK automatically loads credentials from instance metadata
   - Environment variables for bucket name, region, and CloudFront domain
   - Automatic S3 to CloudFront URL conversion

## Implementation Details

### Terraform Infrastructure

```hcl
# S3 Bucket with unique name (private)
resource "aws_s3_bucket" "uploads_bucket" {
  bucket = "${var.project_name}-${var.environment}-uploads-${random_string.bucket_suffix.result}"
}

# Block all public access to S3 bucket
resource "aws_s3_bucket_public_access_block" "uploads_bucket_public_access" {
  bucket = aws_s3_bucket.uploads_bucket.id
  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

# CloudFront Origin Access Identity
resource "aws_cloudfront_origin_access_identity" "uploads_oai" {
  comment = "OAI for ${var.project_name}-${var.environment} uploads bucket"
}

# S3 bucket policy for CloudFront access only
resource "aws_s3_bucket_policy" "uploads_bucket_policy" {
  bucket = aws_s3_bucket.uploads_bucket.id
  policy = jsonencode({
    Statement = [
      {
        Effect = "Allow"
        Principal = {
          AWS = aws_cloudfront_origin_access_identity.uploads_oai.iam_arn
        }
        Action = "s3:GetObject"
        Resource = "${aws_s3_bucket.uploads_bucket.arn}/*"
      }
    ]
  })
}

# CloudFront Distribution
resource "aws_cloudfront_distribution" "uploads_distribution" {
  enabled = true
  origin {
    domain_name = aws_s3_bucket.uploads_bucket.bucket_regional_domain_name
    origin_id   = "S3-${aws_s3_bucket.uploads_bucket.bucket}"
    s3_origin_config {
      origin_access_identity = aws_cloudfront_origin_access_identity.uploads_oai.cloudfront_access_identity_path
    }
  }
  default_cache_behavior {
    allowed_methods  = ["GET", "HEAD", "OPTIONS"]
    cached_methods   = ["GET", "HEAD"]
    target_origin_id = "S3-${aws_s3_bucket.uploads_bucket.bucket}"
    viewer_protocol_policy = "redirect-to-https"
    compress = true
  }
}

# IAM Policy for S3 access
resource "aws_iam_role_policy" "s3_uploads_policy" {
  policy = jsonencode({
    Statement = [
      {
        Effect = "Allow"
        Action = [
          "s3:GetObject",
          "s3:PutObject", 
          "s3:DeleteObject",
          "s3:ListBucket"
        ]
        Resource = [
          aws_s3_bucket.uploads_bucket.arn,
          "${aws_s3_bucket.uploads_bucket.arn}/*"
        ]
      }
    ]
  })
}
```

### Laravel Configuration Changes

#### AWS Config (`config/aws.php`)
```php
// IAM Role credentials will be automatically loaded from instance metadata
// No need for long-lived access keys when using IAM roles
// 'credentials' => [
//     'key'    => env('AWS_ACCESS_KEY_ID'),
//     'secret' => env('AWS_SECRET_ACCESS_KEY'),
// ],
```

#### Filesystems Config (`config/filesystems.php`)
```php
's3' => [
    'driver' => 's3',
    // IAM Role credentials will be automatically loaded
    'region' => env('AWS_REGION', 'us-east-2'),
    'bucket' => env('AWS_S3_BUCKET', 'talent-assessment-development-uploads'),
],
```

### Environment Variables

```bash
# AWS Configuration
AWS_REGION=us-east-2
AWS_S3_BUCKET=talent-assessment-development-uploads
AWS_CLOUDFRONT_DOMAIN=d1234567890abc.cloudfront.net
# Note: AWS credentials are handled via IAM roles when deployed on EC2
```

## Security Benefits

1. **No Long-Lived Keys**: Eliminates the risk of exposed access keys
2. **Automatic Rotation**: IAM role credentials are automatically rotated
3. **Principle of Least Privilege**: EC2 instance only has necessary S3 permissions
4. **Audit Trail**: All S3 operations are logged in CloudTrail
5. **Private S3 Bucket**: S3 bucket is completely private, no direct public access
6. **CloudFront Security**: Images served through CloudFront with HTTPS enforcement
7. **Origin Access Identity**: CloudFront uses OAI to securely access private S3 bucket

## Current Usage in Application

### Upload Controllers
- `AssessmentsController`: Logo and background image uploads
- `ResellersController`: Logo and background image uploads  
- `ClientsController`: Logo and background image uploads
- `DashboardController`: General file uploads

### Upload Pattern
```php
$s3 = new S3Client(config('aws'));
$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
$assessment_data['logo'] = s3_to_cloudfront_url($result->get('ObjectURL'));
```

### Display Pattern
```php
// Automatically converts S3 URLs to CloudFront URLs
<img src="{{ show_image($assessment->logo) }}" alt="Logo" />
```

## Deployment Steps

1. **Deploy Infrastructure**: Run `terraform apply` to create S3 bucket, CloudFront distribution, and IAM policies
2. **Update Environment**: Set environment variables:
   - `AWS_S3_BUCKET` with the created bucket name
   - `AWS_CLOUDFRONT_DOMAIN` with the CloudFront domain name
3. **Test Uploads**: Verify file uploads work with IAM role authentication
4. **Test Display**: Verify images load through CloudFront CDN

## Troubleshooting

### Common Issues

1. **403 Forbidden**: Check IAM role permissions and bucket policy
2. **Bucket Not Found**: Verify bucket name in environment variables
3. **Region Mismatch**: Ensure AWS_REGION matches bucket region
4. **Images Not Loading**: Check CloudFront distribution status and domain configuration
5. **S3 Direct Access**: Ensure bucket is private and only accessible via CloudFront

### Debug Commands

```bash
# Check IAM role on EC2 instance
curl http://169.254.169.254/latest/meta-data/iam/security-credentials/

# Test S3 access from EC2
aws s3 ls s3://your-bucket-name

# Test CloudFront distribution
curl -I https://your-cloudfront-domain.cloudfront.net/images/test.jpg

# Check Laravel logs for S3/CloudFront errors
tail -f storage/logs/laravel.log

# Verify CloudFront distribution status
aws cloudfront get-distribution --id YOUR_DISTRIBUTION_ID
```

## Future Improvements

1. **Bucket Lifecycle**: Implement automatic cleanup of old files
2. **Custom Domain**: Add custom domain with SSL certificate for CloudFront
3. **File Validation**: Add server-side file type and size validation
4. **Backup Strategy**: Implement cross-region replication for critical files
5. **Image Optimization**: Add CloudFront Lambda@Edge for image resizing/optimization
6. **Cache Invalidation**: Implement automatic cache invalidation for updated images

## Migration from Long-Lived Keys

If migrating from existing long-lived keys:

1. Deploy new infrastructure with IAM roles
2. Update application configuration
3. Test uploads with new bucket
4. Migrate existing files if needed
5. Remove old access keys from environment

## Notes

- The AWS SDK v3.1+ automatically supports IAM role credentials
- Instance metadata service provides temporary credentials
- No code changes required in upload controllers
- Bucket name includes random suffix to ensure uniqueness
