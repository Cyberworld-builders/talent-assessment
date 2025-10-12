# Email Testing Guide

## Overview

This guide explains how to test email functionality on the staging and production environments.

## Staging Environment

### Email Configuration

- **Driver**: SES (Amazon Simple Email Service)
- **From Address**: `noreply@cyberworldbuilders.dev`
- **From Name**: Talent Assessment Staging
- **BCC**: All emails are automatically BCC'd to `xaonst@gmail.com`

### Quick Test

Use the provided test script to send test emails:

```bash
# Copy the test script to the staging container
docker cp test-email-staging.php talent-assessment-app-staging:/var/www/test-email-staging.php

# Run the test script
docker exec talent-assessment-app-staging php /var/www/test-email-staging.php
```

### Test Results

The script will:
1. Send a single assignment email to User Apone (`user-apone@cyberworldbuilders.com`)
2. Send a multiple assignments email to the same user

### Manual Testing

To manually test emails, you can:

1. **Log in to staging**: `https://talent-staging.cyberworldbuilders.dev`
2. **Navigate to a client**: Dashboard → Clients → Select a client
3. **Assign an assessment**: Click "Assign Assessments" button
4. **Select user(s) and assessment(s)**
5. **Check "Send email"** checkbox
6. **Submit the form**

### Checking Email Delivery

Emails are sent to:
- The user's email address (e.g., `user-apone@cyberworldbuilders.com`)
- BCC copy to `xaonst@gmail.com`

**Note**: Check spam/junk folders if emails don't appear in the inbox.

## Production Environment

### Email Configuration

- **Driver**: SES (Amazon Simple Email Service)
- **From Address**: `noreply@cyberworldbuilders.dev`
- **From Name**: Talent Assessment
- **BCC**: All emails are automatically BCC'd to `xaonst@gmail.com`

### Testing

**⚠️ Warning**: Be careful when testing on production. Only send test emails to known test accounts, not to real clients.

To test on production:

```bash
# Copy the test script to the production container
docker cp test-email-production.php talent-assessment-app-production:/var/www/test-email-production.php

# Run the test script
docker exec talent-assessment-app-production php /var/www/test-email-production.php
```

## Email Types

### 1. Assignment Email (`send_assignment`)

Sent when a single assessment is assigned to a user.

- **Template**: `emails.assignment`
- **Includes**: Assessment details, login credentials, expiration date
- **Triggered by**: Assignment creation

### 2. Multiple Assignments Email (`send_assignments`)

Sent when multiple assessments are assigned to a user in one batch.

- **Template**: `emails.assignments`
- **Includes**: List of assessments, login credentials, expiration date
- **Uses**: Shortcode replacement for dynamic content
- **Triggered by**: Bulk assignment

### 3. Completion Email (`send_completed`)

Sent when a user completes an assessment.

- **Template**: `emails.completed`
- **Includes**: Confirmation message
- **Triggered by**: Assessment submission

## Troubleshooting

### Emails Not Being Sent

1. **Check SES credentials**: Verify AWS SES is configured correctly
2. **Check logs**: `docker exec [container] tail -f storage/logs/laravel.log`
3. **Verify email address**: SES may be in sandbox mode, limiting recipient addresses
4. **Check spam folder**: Emails might be filtered as spam

### Common Issues

**Issue**: Emails go to spam
- **Solution**: Verify SPF, DKIM, and DMARC records are configured for the domain

**Issue**: SES sandbox limitations
- **Solution**: Request production access from AWS or add test email addresses to verified identities

**Issue**: Date formatting errors in `send_assignments`
- **Solution**: Ensure expiration date is in `'D, d M Y'` format (e.g., "Sun, 19 Oct 2025")

## Email Shortcodes

The following shortcodes can be used in email bodies:

- `{name}` - User's full name
- `{username}` - User's username
- `{email}` - User's email address
- `{password}` - User's generated password
- `{expiration-date}` - Assignment expiration date (formatted)
- `{login-link}` - Link to the assignments page
- `{assessments}` - List of assigned assessments (HTML)

## Best Practices

1. **Always test on staging first** before making email changes to production
2. **Use test accounts** for email testing (e.g., `user-apone@cyberworldbuilders.com`)
3. **Check spam folders** when testing
4. **Monitor BCC email** (`xaonst@gmail.com`) to verify all emails are being sent
5. **Review logs** after sending test emails to catch any errors
6. **Test all email types** (single assignment, multiple assignments, completion)

## Additional Resources

- Main documentation: `/docs/email-system-documentation.md`
- Mailer class: `/app/Mailer.php`
- Email templates: `/resources/views/emails/`

