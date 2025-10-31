# PDF Worker - Quick Start Guide

## 1. Start the Worker

The PDF worker runs automatically as a Docker service:

```bash
# Start all services (including PDF worker)
docker-compose up -d

# Or just start the PDF worker
docker-compose up -d pdf-worker
```

The worker automatically checks for pending reports every 30 seconds.

---

## 2. View Worker Logs

```bash
# Watch worker logs in real-time
docker-compose logs -f pdf-worker
```

**Expected output:**
```
pdf-worker | Updating report URLs to CloudFront...
pdf-worker | No pending reports. Exiting.
pdf-worker | [30 seconds later...]
pdf-worker | 🔄 Processing report ID: 1
pdf-worker | 📄 HTML URL: https://dhbhjqoqdk3yk.cloudfront.net/reports/2/2-cyclops-14-20251031-070845.html
pdf-worker | 🌐 Launching browser...
pdf-worker | ⬇️  Loading HTML...
pdf-worker | 📄 Generating PDF...
pdf-worker | ✅ PDF generated (245KB)
pdf-worker | ⬆️  Uploading to S3: reports/2/2-cyclops-14-20251031-070845.pdf
pdf-worker | ✅ PDF uploaded
pdf-worker | ✅ Database updated
```

---

## 3. Manual Trigger (Optional)

If you need to process a specific report immediately:

```bash
# Process specific report
docker-compose exec pdf-worker node generate-pdf.js 1

# Process all pending reports now
docker-compose exec pdf-worker node generate-pdf.js --batch
```

---

## 4. Check Worker Status

```bash
# Check if worker is running
docker-compose ps pdf-worker

# Restart worker
docker-compose restart pdf-worker

# Rebuild worker (after code changes)
docker-compose up -d --build pdf-worker
```

---

## Environment-Specific Commands

### Development
```bash
docker-compose up -d pdf-worker
docker-compose logs -f pdf-worker
```

### Staging
```bash
docker-compose -f docker-compose.staging.yml up -d pdf-worker
docker-compose -f docker-compose.staging.yml logs -f pdf-worker
```

### Production
```bash
docker-compose -f docker-compose.production.yml up -d pdf-worker
docker-compose -f docker-compose.production.yml logs -f pdf-worker
```

---

## Troubleshooting

### Worker not starting
```bash
# Rebuild the container
docker-compose up -d --build pdf-worker

# Check logs for errors
docker-compose logs pdf-worker
```

### PDFs not generating
```bash
# Check if reports are pending in database
docker-compose exec mysql mysql -u talent_user -p talent_assessment \
  -e "SELECT id, user_id, slug, html_url IS NOT NULL as has_html, pdf_url IS NOT NULL as has_pdf FROM report_data;"

# Manually trigger processing
docker-compose exec pdf-worker node generate-pdf.js --batch
```

### Check report status
```bash
# View all reports
docker-compose exec mysql mysql -u talent_user -p talent_assessment \
  -e "SELECT * FROM report_data ORDER BY created_at DESC LIMIT 5;"
```

---

## That's It!

The workflow is now completely automated:

1. **User clicks "Generate Report"** → PHP creates HTML and uploads to S3
2. **PDF worker picks it up** (within 30 seconds) → Generates PDF automatically  
3. **User clicks "Download PDF"** → PDF ready from CloudFront!

No manual intervention needed!

