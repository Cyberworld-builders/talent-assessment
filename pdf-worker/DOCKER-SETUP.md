# PDF Worker - Docker Architecture

## Overview

The PDF worker runs as a **separate Docker service** alongside the main PHP application, with proper environment separation.

## Why Separate Service?

✅ **Environment Separation** - Dev/staging/production use different configs  
✅ **Clean Architecture** - PHP app doesn't need Node.js  
✅ **Independent Scaling** - Can run multiple workers if needed  
✅ **No Path Issues** - Container paths are consistent  
✅ **Auto-restart** - Docker manages the process  

## Architecture

```
┌──────────────────┐
│  app container   │  (PHP/Laravel)
│  - Generates HTML│
│  - Saves to DB   │
└────────┬─────────┘
         │
         │ (same network)
         │
┌────────▼─────────┐         ┌──────────────┐
│  mysql container │◄────────┤ pdf-worker   │
│  - report_data   │         │  container   │
│  - html_url      │         │  - Polls DB  │
│  - pdf_url       │         │  - Gen PDF   │
└──────────────────┘         │  - Upload S3 │
                             └──────────────┘
```

## Service Configuration

### docker-compose.yml
```yaml
pdf-worker:
  build:
    context: ./pdf-worker
    dockerfile: Dockerfile
  container_name: talent-assessment-pdf-worker
  restart: unless-stopped
  env_file:
    - .env.dev
  depends_on:
    - mysql
  networks:
    - talent-network
  command: sh -c "while true; do node generate-pdf.js --batch; sleep 30; done"
```

### Key Points:

1. **Build Context**: `./pdf-worker` (separate from main app)
2. **Container Name**: Environment-specific (dev/staging/production)
3. **Env File**: Uses environment-specific `.env` files
4. **Network**: Same network as MySQL (`talent-network`)
5. **Command**: Infinite loop that runs batch processing every 30 seconds

## Dockerfile

```dockerfile
FROM node:20-alpine

# Install Chromium (small Alpine package)
RUN apk add --no-cache chromium nss freetype harfbuzz ca-certificates ttf-freefont font-noto-emoji

# Tell Puppeteer to use system Chromium (no 200MB download)
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY generate-pdf.js ./

# Run as non-root
USER nodejs

CMD ["node", "generate-pdf.js", "--batch"]
```

## Environment Variables

The worker automatically reads from the environment file:

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | MySQL hostname | `mysql` |
| `DB_USERNAME` | Database user | From env file |
| `DB_PASSWORD` | Database password | From env file |
| `DB_DATABASE` | Database name | From env file |
| `AWS_REGION` | S3 region | `us-east-2` |
| `AWS_S3_BUCKET` | S3 bucket name | From env file |
| `AWS_CLOUDFRONT_DOMAIN` | CDN domain | From env file |

## How It Works

### 1. User Generates Report (PHP)
```php
// ReportsController@generateDevelopment
$reportData = $this->generateStaticReport($assignmentId, $userId, $report);
// Creates HTML, uploads to S3, saves html_url to database
// No need to trigger Node - worker will pick it up automatically
```

### 2. Worker Polls Database (Node.js)
```javascript
// Every 30 seconds, query for pending reports
SELECT id FROM report_data 
WHERE html_url IS NOT NULL 
  AND pdf_url IS NULL 
LIMIT 10
```

### 3. Worker Processes Report
```javascript
// For each pending report:
// 1. Fetch HTML from CloudFront
// 2. Render with Puppeteer/Chromium
// 3. Generate PDF
// 4. Upload to S3
// 5. Update pdf_url in database
```

### 4. User Downloads PDF (PHP)
```php
// ReportsController@downloadDevelopment
if ($reportData->pdf_url) {
    return redirect($reportData->pdf_url);  // From CloudFront
}
```

## Development Workflow

```bash
# Start all services
docker-compose up -d

# View worker logs
docker-compose logs -f pdf-worker

# Generate a report in the UI
# (Creates HTML, worker picks it up within 30 seconds)

# Check worker processed it
docker-compose logs pdf-worker | grep "Processing report"

# Download PDF from UI
# (Should work if 30+ seconds have passed)
```

## Staging/Production

Each environment has its own docker-compose file:

### Staging
```bash
# Start staging worker
docker-compose -f docker-compose.staging.yml up -d pdf-worker

# View logs
docker-compose -f docker-compose.staging.yml logs -f pdf-worker
```

### Production
```bash
# Start production worker
docker-compose -f docker-compose.production.yml up -d pdf-worker

# View logs
docker-compose -f docker-compose.production.yml logs -f pdf-worker
```

## Advantages of This Approach

| Feature | Benefit |
|---------|---------|
| **No exec() calls** | PHP doesn't spawn processes |
| **No path issues** | All paths are container-relative |
| **Auto-recovery** | Docker restarts worker if it crashes |
| **Environment isolation** | Dev/staging/production separated |
| **Scalable** | Can run multiple workers easily |
| **Simple monitoring** | `docker-compose logs` shows everything |
| **IAM role auth** | EC2 instance role works for all containers |

## Monitoring

```bash
# Check if worker is running
docker-compose ps pdf-worker

# View logs (all output)
docker-compose logs pdf-worker

# View logs (tail last 50 lines)
docker-compose logs --tail=50 pdf-worker

# View logs (follow real-time)
docker-compose logs -f pdf-worker

# Check resource usage
docker stats pdf-worker
```

## Troubleshooting

### Worker not running
```bash
docker-compose ps pdf-worker
# If not running:
docker-compose up -d pdf-worker
```

### Worker crashing on startup
```bash
docker-compose logs pdf-worker
# Look for errors, common issues:
# - Missing environment variables
# - Cannot connect to MySQL
# - Cannot connect to S3
```

### PDFs not generating
```bash
# Check if reports are pending
docker-compose exec mysql mysql -u talent_user -p talent_assessment \
  -e "SELECT id, html_url IS NOT NULL, pdf_url IS NOT NULL FROM report_data;"

# Manually trigger worker
docker-compose exec pdf-worker node generate-pdf.js --batch
```

### Rebuild after code changes
```bash
docker-compose up -d --build pdf-worker
```

## Performance Tuning

### Faster Processing (Shorter Interval)
```yaml
# In docker-compose.yml
command: sh -c "while true; do node generate-pdf.js --batch; sleep 10; done"  # Every 10 seconds
```

### Multiple Workers (Future)
```yaml
pdf-worker:
  # ... existing config ...
  deploy:
    replicas: 3  # Run 3 workers in parallel
```

### Process More Reports Per Batch
```javascript
// In generate-pdf.js, change LIMIT
LIMIT 10  // -> LIMIT 50
```

## Summary

**Before** (Host-based):
- ❌ Path issues (`/opt/talent-assessment/...`)
- ❌ Environment mixing (dev/staging/production)
- ❌ Manual Node.js installation
- ❌ Cron job management

**After** (Docker service):
- ✅ Container-relative paths (`/app/...`)
- ✅ Environment separation (different compose files)
- ✅ Automatic installation (Dockerfile)
- ✅ Docker manages restarts

This is production-ready and follows Docker best practices! 🎉

