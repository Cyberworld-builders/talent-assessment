# PDF Generation Service Documentation

## Overview

This document describes the Node.js background service that converts HTML reports stored in S3 to PDF format.

## System Architecture

### Flow Diagram

```
1. User clicks "Download PDF" button
   ↓
2. Laravel generates static HTML with:
   - Inline CSS styles
   - Inline JavaScript
   - Absolute URLs for images
   ↓
3. HTML uploaded to S3: reports/{client_id}/{slug}.html
   ↓
4. HTML URL saved to report_data table (html_url column)
   ↓
5. Node.js service monitors S3 bucket for new HTML files
   ↓
6. Service converts HTML to PDF using Puppeteer/wkhtmltopdf
   ↓
7. PDF uploaded to S3: reports/{client_id}/{slug}.pdf
   ↓
8. PDF URL saved to report_data table (pdf_url column)
```

## Database Schema

### report_data Table

| Column | Type | Description |
|--------|------|-------------|
| id | integer | Primary key |
| user_id | integer | User the report is for |
| assignment_id | integer | Assignment the report is based on |
| slug | string | Unique identifier for the report (used in filenames) |
| html_url | string | S3 URL for the HTML version |
| pdf_url | string | S3 URL for the PDF version |
| score | text | JSON data with report scores |
| created_at | timestamp | When the report was created |
| updated_at | timestamp | Last update timestamp |

## File Naming Convention

All reports follow this naming convention:

### HTML Files
```
reports/{client_id}/{slug}.html
```

### PDF Files
```
reports/{client_id}/{slug}.pdf
```

### Slug Format
The slug is automatically generated using:
```
{client_id}-{user_name_slug}-{assignment_id}-{timestamp}
```

Example: `2-john-doe-22-20251031-123045`

## Node.js Service Requirements

### Dependencies

```json
{
  "name": "report-pdf-generator",
  "version": "1.0.0",
  "dependencies": {
    "aws-sdk": "^2.1000.0",
    "puppeteer": "^21.0.0",
    "mysql2": "^3.0.0",
    "dotenv": "^16.0.0"
  }
}
```

### Environment Variables

```bash
# AWS Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=talent
DB_USERNAME=root
DB_PASSWORD=root

# Service Configuration
POLL_INTERVAL=30000  # Check for new reports every 30 seconds
LOG_LEVEL=info
```

## Pseudocode for Node.js Service

```javascript
const AWS = require('aws-sdk');
const puppeteer = require('puppeteer');
const mysql = require('mysql2/promise');

// Initialize AWS S3
const s3 = new AWS.S3({
  region: process.env.AWS_DEFAULT_REGION
});

// Initialize MySQL connection
const dbConfig = {
  host: process.env.DB_HOST,
  user: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE
};

async function processReports() {
  const connection = await mysql.createConnection(dbConfig);
  
  // Find reports that have HTML but no PDF
  const [reports] = await connection.execute(
    `SELECT id, user_id, assignment_id, slug, html_url, pdf_url 
     FROM report_data 
     WHERE html_url IS NOT NULL 
     AND (pdf_url IS NULL OR pdf_url = '')
     LIMIT 10`
  );

  for (const report of reports) {
    try {
      console.log(`Processing report: ${report.slug}`);
      
      // Download HTML from S3
      const htmlKey = `reports/${getClientId(report.user_id)}/${report.slug}.html`;
      const htmlContent = await s3.getObject({
        Bucket: process.env.AWS_BUCKET,
        Key: htmlKey
      }).promise();

      // Convert HTML to PDF using Puppeteer
      const browser = await puppeteer.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
      });
      const page = await browser.newPage();
      
      // Set the HTML content
      await page.setContent(htmlContent.Body.toString('utf8'), {
        waitUntil: 'networkidle0'
      });

      // Generate PDF
      const pdfBuffer = await page.pdf({
        format: 'A4',
        printBackground: true,
        margin: {
          top: '0px',
          right: '0px',
          bottom: '0px',
          left: '0px'
        }
      });

      await browser.close();

      // Upload PDF to S3
      const pdfKey = `reports/${getClientId(report.user_id)}/${report.slug}.pdf`;
      await s3.putObject({
        Bucket: process.env.AWS_BUCKET,
        Key: pdfKey,
        Body: pdfBuffer,
        ContentType: 'application/pdf',
        ACL: 'public-read'
      }).promise();

      // Get the PDF URL
      const pdfUrl = `https://${process.env.AWS_BUCKET}.s3.amazonaws.com/${pdfKey}`;

      // Update database with PDF URL
      await connection.execute(
        'UPDATE report_data SET pdf_url = ? WHERE id = ?',
        [pdfUrl, report.id]
      );

      console.log(`Successfully generated PDF: ${pdfUrl}`);

    } catch (error) {
      console.error(`Error processing report ${report.slug}:`, error);
      // Continue with next report
    }
  }

  await connection.end();
}

// Run the processor in a loop
async function main() {
  console.log('PDF Generation Service started');
  
  while (true) {
    try {
      await processReports();
    } catch (error) {
      console.error('Error in main loop:', error);
    }
    
    // Wait before next poll
    await new Promise(resolve => setTimeout(resolve, process.env.POLL_INTERVAL || 30000));
  }
}

// Helper function to get client ID from user ID
async function getClientId(userId) {
  const connection = await mysql.createConnection(dbConfig);
  const [users] = await connection.execute(
    'SELECT client_id FROM users WHERE id = ?',
    [userId]
  );
  await connection.end();
  
  return users[0]?.client_id || 'unknown';
}

main().catch(console.error);
```

## Deployment Options

### Option 1: Docker Container

```dockerfile
FROM node:18-alpine

# Install Chromium for Puppeteer
RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    freetype-dev \
    harfbuzz \
    ca-certificates \
    ttf-freefont

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .

CMD ["node", "index.js"]
```

### Option 2: AWS Lambda (with layers)

For AWS Lambda, you'll need:
- Puppeteer Lambda layer
- Chromium Lambda layer
- Triggered by S3 events or EventBridge schedule

### Option 3: PM2 Process Manager

```json
{
  "apps": [{
    "name": "pdf-generator",
    "script": "./index.js",
    "instances": 1,
    "autorestart": true,
    "watch": false,
    "max_memory_restart": "1G",
    "env": {
      "NODE_ENV": "production"
    }
  }]
}
```

## Monitoring & Logging

### Key Metrics to Track

1. **Processing Time**: How long each PDF takes to generate
2. **Success Rate**: Percentage of successful conversions
3. **Queue Depth**: Number of pending HTML files
4. **Error Rate**: Failed conversions per hour
5. **S3 Storage**: Total size of generated PDFs

### Logging Best Practices

```javascript
// Log structure
{
  "timestamp": "2025-10-31T12:30:45Z",
  "level": "info",
  "report_id": 123,
  "slug": "2-john-doe-22-20251031-123045",
  "action": "pdf_generated",
  "duration_ms": 2345,
  "file_size_bytes": 524288
}
```

## Error Handling

### Common Issues and Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Timeout errors | Large HTML files | Increase Puppeteer timeout, optimize images |
| Memory issues | Too many concurrent conversions | Limit concurrency, increase memory |
| Missing images | Relative paths | Ensure all image URLs are absolute |
| Broken styling | External CSS not loaded | Ensure all CSS is inlined |
| Font issues | Custom fonts not available | Include font files or use web-safe fonts |

## Performance Optimization

### Tips for Faster PDF Generation

1. **Parallel Processing**: Process multiple reports concurrently
2. **Browser Pooling**: Reuse Puppeteer browser instances
3. **Image Optimization**: Compress images before upload
4. **CDN for Images**: Use CloudFront for faster image loading
5. **Caching**: Cache commonly used assets

### Example with Browser Pool

```javascript
const browserPool = [];
const MAX_BROWSERS = 3;

async function getBrowser() {
  if (browserPool.length < MAX_BROWSERS) {
    const browser = await puppeteer.launch({...});
    browserPool.push(browser);
    return browser;
  }
  return browserPool[Math.floor(Math.random() * browserPool.length)];
}
```

## Testing

### Unit Tests

```javascript
describe('PDF Generation', () => {
  it('should convert HTML to PDF', async () => {
    const html = '<html><body><h1>Test Report</h1></body></html>';
    const pdf = await generatePDF(html);
    expect(pdf).toBeInstanceOf(Buffer);
    expect(pdf.length).toBeGreaterThan(0);
  });

  it('should upload PDF to S3', async () => {
    const pdfBuffer = Buffer.from('test');
    const url = await uploadToS3(pdfBuffer, 'test.pdf');
    expect(url).toContain('.s3.amazonaws.com/');
  });
});
```

### Integration Tests

```javascript
describe('End-to-End PDF Generation', () => {
  it('should process a complete report', async () => {
    // Create test report in database
    const report = await createTestReport();
    
    // Upload test HTML
    await uploadTestHTML(report.slug);
    
    // Run processor
    await processReports();
    
    // Verify PDF was generated
    const updatedReport = await getReport(report.id);
    expect(updatedReport.pdf_url).toBeTruthy();
    
    // Verify PDF exists in S3
    const pdfExists = await s3ObjectExists(updatedReport.pdf_url);
    expect(pdfExists).toBe(true);
  });
});
```

## API Endpoints for Report Access

The Laravel application should expose these endpoints:

### Get Report HTML
```
GET /api/reports/{slug}/html
Returns: Redirect to S3 HTML URL or 404
```

### Get Report PDF
```
GET /api/reports/{slug}/pdf
Returns: Redirect to S3 PDF URL, or 202 (Accepted) if still processing
```

### Check Report Status
```
GET /api/reports/{slug}/status
Returns: 
{
  "slug": "2-john-doe-22-20251031-123045",
  "html_ready": true,
  "pdf_ready": false,
  "html_url": "https://...",
  "pdf_url": null,
  "created_at": "2025-10-31T12:30:45Z"
}
```

## Maintenance & Operations

### Daily Tasks
- Monitor error logs
- Check queue depth
- Verify S3 storage costs

### Weekly Tasks
- Review performance metrics
- Clean up old reports (optional)
- Update dependencies

### Monthly Tasks
- Audit S3 bucket for orphaned files
- Review and optimize costs
- Performance tuning based on metrics

## Cost Estimation

### AWS Costs (approximate)

- **S3 Storage**: $0.023/GB/month
  - Average report: 500KB HTML + 300KB PDF = ~800KB
  - 1000 reports/month: ~800MB = $0.02/month

- **S3 Requests**: 
  - PUT: $0.005 per 1000 requests
  - GET: $0.0004 per 1000 requests

- **EC2/Lambda**: 
  - t3.micro: $0.0104/hour = ~$7.50/month
  - Lambda: $0.20 per 1M requests + compute time

### Recommended Setup
- Start with EC2 t3.micro running Node.js service
- Scale to Lambda if volume exceeds 10,000 reports/month

## Support & Troubleshooting

### Debug Mode

Enable verbose logging:
```bash
LOG_LEVEL=debug node index.js
```

### Common Commands

```bash
# Check service status
pm2 status pdf-generator

# View logs
pm2 logs pdf-generator

# Restart service
pm2 restart pdf-generator

# Monitor resource usage
pm2 monit
```

## Security Considerations

1. **IAM Permissions**: Limit S3 access to specific bucket/prefix
2. **Database Credentials**: Use environment variables, never commit
3. **S3 ACLs**: Use signed URLs instead of public-read for sensitive reports
4. **Input Validation**: Sanitize HTML content before processing
5. **Rate Limiting**: Prevent abuse of PDF generation

## Future Enhancements

1. **Webhook Notifications**: Notify when PDF is ready
2. **Priority Queue**: VIP clients get faster processing
3. **Batch Processing**: Generate multiple PDFs at once
4. **Report Templates**: Customizable PDF layouts
5. **Watermarking**: Add dynamic watermarks to PDFs
6. **Email Delivery**: Automatically email PDFs when ready

