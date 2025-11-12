# PDF Generator Worker

Converts static HTML reports from CloudFront to PDF using Puppeteer (Chromium).

Runs as a separate Docker service that polls the database for pending reports.

## Architecture

This worker runs as a separate container in docker-compose that:
1. Checks database every 30 seconds for reports with `html_url` but no `pdf_url`
2. Fetches HTML from CloudFront
3. Renders to PDF using Puppeteer/Chromium
4. Uploads PDF to S3
5. Updates database with CloudFront PDF URL

## Quick Start

The worker is already configured in `docker-compose.yml` and runs automatically:

```bash
# Start all services (including PDF worker)
docker-compose up -d

# View PDF worker logs
docker-compose logs -f pdf-worker

# Check worker status
docker-compose ps pdf-worker

# Restart worker
docker-compose restart pdf-worker
```

## How It Works

1. **Triggered**: When user clicks "Generate Report", HTML is created and uploaded to S3
2. **Background**: Node script fetches HTML from CloudFront
3. **Render**: Puppeteer loads HTML in headless Chrome
4. **PDF**: Renders to PDF with print styles
5. **Upload**: Uploads PDF to S3 via IAM role
6. **Update**: Sets `pdf_url` in database

## Environment Variables

Reads from `../.env`:

- `DB_HOST` - MySQL host (default: mysql)
- `DB_USERNAME` - Database user
- `DB_PASSWORD` - Database password
- `DB_DATABASE` - Database name
- `AWS_REGION` - S3 region
- `AWS_S3_BUCKET` - S3 bucket name
- `AWS_CLOUDFRONT_DOMAIN` - CloudFront distribution domain

## Manual Trigger (Development)

If you need to manually process a specific report:

```bash
# Execute inside the pdf-worker container
docker-compose exec pdf-worker node generate-pdf.js 1

# Or process all pending
docker-compose exec pdf-worker node generate-pdf.js --batch
```

## Configuration

The worker reads environment variables from `.env.dev` (or `.env.staging`, `.env.production`):

- `DB_HOST` - MySQL host (default: mysql)
- `DB_USERNAME` - Database user
- `DB_PASSWORD` - Database password
- `DB_DATABASE` - Database name
- `AWS_REGION` - S3 region
- `AWS_S3_BUCKET` - S3 bucket name
- `AWS_CLOUDFRONT_DOMAIN` - CloudFront distribution domain

## Polling Interval

The worker checks for new reports every **30 seconds** by default. To change this, edit `docker-compose.yml`:

```yaml
pdf-worker:
  # ... other config ...
  command: sh -c "while true; do node generate-pdf.js --batch; sleep 60; done"  # 60 seconds
```

## Troubleshooting

### Worker not processing reports

```bash
# Check if worker is running
docker-compose ps pdf-worker

# View worker logs
docker-compose logs -f pdf-worker

# Restart worker
docker-compose restart pdf-worker

# Rebuild worker (if Dockerfile changed)
docker-compose up -d --build pdf-worker
```

### "Connection refused" to MySQL

Ensure the worker is on the same network as MySQL:
```bash
docker-compose exec pdf-worker ping mysql
```

### PDF rendering issues

- Ensure HTML has absolute URLs for all assets (CloudFront)
- Check CloudFront is serving HTML correctly
- Verify print styles in HTML with `@media print`

### Check pending reports manually

```bash
docker-compose exec mysql mysql -u talent_user -p talent_assessment \
  -e "SELECT id, user_id, slug, html_url IS NOT NULL as has_html, pdf_url IS NOT NULL as has_pdf FROM report_data;"
```

## Performance

- Single PDF: ~3-5 seconds
- Batch of 10: ~30-50 seconds
- Memory: ~200MB per Chromium instance

## Security

- Uses IAM role for S3 (no keys in code)
- Reads database credentials from environment
- PDFs stored in same bucket as HTML
- CloudFront serves both publicly

