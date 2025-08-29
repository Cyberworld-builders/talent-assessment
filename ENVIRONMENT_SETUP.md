# Environment Setup Guide

## Overview

This guide explains how to set up the development environment for the Talent Assessment application, including secure credential management.

## Environment Files

### Required Files

1. **`.env.dev`** - Development environment configuration (not in version control)
2. **`.env.dev.example`** - Template file with placeholder credentials
3. **`.env.example`** - General environment template

### Security Notice

⚠️ **IMPORTANT**: Never commit real credentials to version control!

- `.env.dev` contains sensitive credentials and is excluded from git
- Use `.env.dev.example` as a template for your local setup
- Keep your credentials secure and never share them

## Setup Instructions

### 1. Copy Environment Template

```bash
# Copy the development environment template
cp .env.dev.example .env.dev
```

### 2. Configure Mailtrap Credentials

Edit `.env.dev` and update the Mailtrap credentials:

```bash
# Mail Configuration
MAIL_DRIVER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_actual_mailtrap_username
MAIL_PASSWORD=your_actual_mailtrap_password
MAIL_ENCRYPTION=tls
```

### 3. Get Mailtrap Credentials

1. Go to [https://mailtrap.io/inboxes](https://mailtrap.io/inboxes)
2. Create a free account or sign in
3. Get your SMTP credentials from your inbox
4. Update `.env.dev` with your actual credentials

### 4. Restart Containers

After updating credentials, restart the containers:

```bash
docker-compose down
docker-compose up -d
```

## Testing Email Functionality

### Run Email Tests

```bash
# Test email system with Mailtrap
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit tests/EmailSystemTest.php
```

### Check Email Delivery

1. Visit your Mailtrap inbox: [https://mailtrap.io/inboxes](https://mailtrap.io/inboxes)
2. Look for test emails from the application
3. Verify email content and formatting

## Environment Variables Reference

### Mail Configuration

| Variable | Description | Example |
|----------|-------------|---------|
| `MAIL_DRIVER` | Email driver | `smtp` |
| `MAIL_HOST` | SMTP host | `sandbox.smtp.mailtrap.io` |
| `MAIL_PORT` | SMTP port | `2525` |
| `MAIL_USERNAME` | SMTP username | `your_mailtrap_username` |
| `MAIL_PASSWORD` | SMTP password | `your_mailtrap_password` |
| `MAIL_ENCRYPTION` | Encryption type | `tls` |

### Database Configuration

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_HOST` | Database host | `mysql` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `talent_assessment` |
| `DB_USERNAME` | Database username | `talent_user` |
| `DB_PASSWORD` | Database password | `talent_password` |

### Application Configuration

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_NAME` | Application name | `"Talent Assessment"` |
| `APP_ENV` | Environment | `local` |
| `APP_KEY` | Encryption key | `base64:...` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_URL` | Application URL | `http://localhost:8001` |

## Troubleshooting

### Common Issues

1. **Email not sending**: Check Mailtrap credentials in `.env.dev`
2. **Database connection failed**: Verify database credentials
3. **Environment variables not loading**: Restart containers after changes

### Security Best Practices

1. **Never commit `.env.dev`**: It's excluded from version control
2. **Use strong passwords**: Generate secure credentials
3. **Rotate credentials regularly**: Update passwords periodically
4. **Limit access**: Only share credentials with authorized team members

## Production Deployment

For production environments:

1. Use AWS SES or other production email service
2. Set up proper SSL certificates
3. Configure production database credentials
4. Use environment-specific configuration files
5. Implement proper logging and monitoring

## Support

If you encounter issues:

1. Check the [Email System Documentation](docs/email-system-documentation.md)
2. Verify your Mailtrap credentials
3. Review the test results
4. Check container logs: `docker-compose logs app`
