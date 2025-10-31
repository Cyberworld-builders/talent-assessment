#!/usr/bin/env node

/**
 * PDF Generator Worker
 * 
 * Converts static HTML reports from CloudFront to PDF and uploads to S3
 * 
 * Usage:
 *   node generate-pdf.js <report_data_id>
 *   node generate-pdf.js --batch   (processes all pending reports)
 */

import puppeteer from 'puppeteer';
import { S3Client, PutObjectCommand } from '@aws-sdk/client-s3';
import { createConnection } from 'mysql2/promise';

// Configuration
const DB_CONFIG = {
  host: process.env.DB_HOST || 'mysql',
  user: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_DATABASE || 'talent_assessment',
  port: parseInt(process.env.DB_PORT || '3306')
};

const S3_CONFIG = {
  region: process.env.AWS_REGION || 'us-east-2',
  bucket: process.env.AWS_S3_BUCKET
};

const CLOUDFRONT_DOMAIN = process.env.AWS_CLOUDFRONT_DOMAIN;

// Initialize S3 Client (will use IAM role from EC2 instance)
const s3Client = new S3Client({ region: S3_CONFIG.region });

/**
 * Generate PDF from HTML report
 */
async function generatePDF(reportId) {
  const connection = await createConnection(DB_CONFIG);
  let browser;
  
  try {
    console.log(`\n🔄 Processing report ID: ${reportId}`);
    
    // Get report data
    const [rows] = await connection.execute(
      `SELECT id, user_id, assignment_id, slug, html_url, pdf_url 
       FROM report_data 
       WHERE id = ?`,
      [reportId]
    );
    
    if (rows.length === 0) {
      throw new Error(`Report ${reportId} not found`);
    }
    
    const report = rows[0];
    
    if (!report.html_url) {
      throw new Error(`Report ${reportId} has no HTML URL. Generate HTML first.`);
    }
    
    console.log(`📄 HTML URL: ${report.html_url}`);
    
    // Launch browser
    console.log(`🌐 Launching browser...`);
    browser = await puppeteer.launch({
      headless: 'new',
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-gpu',
        '--font-render-hinting=medium'
      ]
    });
    
    const page = await browser.newPage();
    
    // Load HTML from CloudFront
    console.log(`⬇️  Loading HTML...`);
    await page.goto(report.html_url, {
      waitUntil: 'networkidle0',
      timeout: 60000
    });
    
    // Emulate print media for correct CSS
    await page.emulateMediaType('print');
    
    // Wait a bit for any JavaScript to finish (using Promise instead of deprecated waitForTimeout)
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Generate PDF
    console.log(`📄 Generating PDF...`);
    const pdfBuffer = await page.pdf({
      format: 'A4',
      printBackground: true,
      preferCSSPageSize: false,
      margin: { top: 0, bottom: 0, left: 0, right: 0 }
    });
    
    await browser.close();
    browser = null;
    
    console.log(`✅ PDF generated (${Math.round(pdfBuffer.length / 1024)}KB)`);
    
    // Upload to S3
    const s3Key = `reports/${report.user_id}/${report.slug}.pdf`;
    console.log(`⬆️  Uploading to S3: ${s3Key}`);
    
    await s3Client.send(new PutObjectCommand({
      Bucket: S3_CONFIG.bucket,
      Key: s3Key,
      Body: pdfBuffer,
      ContentType: 'application/pdf'
    }));
    
    // Construct CloudFront URL
    const pdfUrl = `https://${CLOUDFRONT_DOMAIN}/${s3Key}`;
    console.log(`✅ PDF uploaded: ${pdfUrl}`);
    
    // Update database
    await connection.execute(
      `UPDATE report_data 
       SET pdf_url = ?, updated_at = NOW() 
       WHERE id = ?`,
      [pdfUrl, reportId]
    );
    
    console.log(`✅ Database updated`);
    console.log(`\n✨ Success! PDF is ready at: ${pdfUrl}\n`);
    
    return { success: true, pdfUrl };
    
  } catch (error) {
    console.error(`\n❌ Error generating PDF for report ${reportId}:`, error.message);
    
    // Log error to database
    try {
      await connection.execute(
        `UPDATE report_data 
         SET updated_at = NOW() 
         WHERE id = ?`,
        [reportId]
      );
    } catch (e) {
      // Ignore
    }
    
    throw error;
    
  } finally {
    if (browser) {
      await browser.close();
    }
    await connection.end();
  }
}

/**
 * Process all pending reports (no PDF yet)
 */
async function processBatch() {
  const connection = await createConnection(DB_CONFIG);
  
  try {
    const [rows] = await connection.execute(
      `SELECT id FROM report_data 
       WHERE html_url IS NOT NULL 
         AND pdf_url IS NULL 
       ORDER BY created_at DESC 
       LIMIT 10`
    );
    
    if (rows.length === 0) {
      console.log('✅ No pending reports to process');
      return;
    }
    
    console.log(`📋 Found ${rows.length} pending reports\n`);
    
    for (const row of rows) {
      try {
        await generatePDF(row.id);
      } catch (error) {
        console.error(`Failed to process report ${row.id}, continuing...`);
      }
    }
    
    console.log('\n✨ Batch processing complete\n');
    
  } finally {
    await connection.end();
  }
}

// Main execution
const args = process.argv.slice(2);

if (args.length === 0) {
  console.error('Usage: node generate-pdf.js <report_id>');
  console.error('       node generate-pdf.js --batch');
  process.exit(1);
}

if (args[0] === '--batch') {
  processBatch().catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
  });
} else {
  const reportId = parseInt(args[0]);
  if (isNaN(reportId)) {
    console.error('Invalid report ID');
    process.exit(1);
  }
  
  generatePDF(reportId).catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
  });
}

