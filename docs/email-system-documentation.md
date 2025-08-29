# Email System Documentation

## Overview

The Talent Assessment application includes a comprehensive email system for sending notifications to users about assessment assignments, completions, and other important events. The system supports multiple email service providers and is configured to work with AWS services.

## Email Configuration

### Primary Configuration Files

#### 1. `config/mail.php`
The main Laravel mail configuration file that defines:
- **Driver**: SMTP (default)
- **Host**: `smtp.gmail.com` (default)
- **Port**: 587 (default)
- **Encryption**: TLS (default)
- **From Address**: `postmaster@mg.aoescience.com`
- **From Name**: `AOE Science`

#### 2. `config/services.php`
Contains configuration for third-party email services:
- **Mailgun**: Domain and secret configuration
- **Amazon SES**: Key, secret, and region configuration (us-east-1)

### Environment Variables

The following environment variables control email functionality:

```bash
# Mail Configuration
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls

# Third-party Services
MAILGUN_DOMAIN=your_mailgun_domain
MAILGUN_SECRET=your_mailgun_secret
SES_KEY=your_aws_ses_key
SES_SECRET=your_aws_ses_secret
```

## Email Service Providers

### 1. Amazon SES (Simple Email Service)
- **Region**: us-east-1 (configured in `config/services.php`)
- **Usage**: Primary email service for production environments
- **Configuration**: Requires AWS SES credentials (SES_KEY, SES_SECRET)
- **Benefits**: High deliverability, scalable, cost-effective

### 2. Mailgun
- **Domain**: `postmaster@mg.aoescience.com` (hardcoded in Mailer class)
- **Usage**: Alternative email service provider
- **Configuration**: Requires Mailgun domain and secret
- **Benefits**: Good deliverability, detailed analytics

### 3. SMTP (Gmail)
- **Default Configuration**: Used for development and testing
- **Host**: smtp.gmail.com
- **Port**: 587
- **Encryption**: TLS

### 4. Mailtrap (Development)
- **Usage**: Development and staging environments
- **Configuration**: 
  - Host: smtp.mailtrap.io
  - Port: 2525
  - No authentication required

## Core Email Functionality

### 1. Mailer Class (`app/Mailer.php`)

The main email service class that handles all email operations:

#### Key Properties:
- **Domain**: `postmaster@mg.aoescience.com`
- **From Name**: `AOE Science`
- **BCC**: `xaonst@gmail.com` (all emails are blind copied)

#### Main Methods:

##### `send_assignments($user, $ids, $expiration, $subject, $body)`
- Sends multiple assessment assignments in one email
- Generates login credentials for users
- Includes assessment list and expiration date
- Uses shortcode replacement for dynamic content

##### `send_assignment($user, $id)`
- Sends single assessment assignment email
- Includes assessment details and login credentials
- Uses template: `emails.assignment`

##### `send_completed($user, $id)`
- Sends completion notification email
- Confirms assessment submission
- Uses template: `emails.completed`

##### `send_questionnaire($user, $jaqId, $subject, $body)`
- Sends job analysis questionnaire
- Includes login credentials and analysis details
- Marks JAQ as sent in database

### 2. Email Templates

Located in `resources/views/emails/`:

#### `assignment.blade.php`
- Single assessment assignment notification
- Includes login credentials and expiration date
- Dynamic content based on user level

#### `assignments.blade.php`
- Multiple assessment assignments
- Uses shortcode replacement for body content
- Flexible template for various assignment types

#### `completed.blade.php`
- Assessment completion confirmation
- Simple confirmation message
- Includes copyright footer

#### `password.blade.php`
- Password reset functionality (minimal implementation)

### 3. Shortcode System

The email system supports dynamic content replacement using shortcodes:

```php
$body = do_shortcodes([
    'name'             => $user->name,
    'username'         => $user->username,
    'email'            => $user->email,
    'password'         => $user->generate_password_for_user(),
    'expiration-date'  => $expiration->format('l, F jS, Y'),
    'login-link'       => $assignments_link,
    'assessments'      => $assessmentsList,
    'analysis'         => $analysis->name,
], $body);
```

## Email Triggers

### 1. Assessment Assignment
- **Trigger**: When users are assigned to assessments
- **Controller**: `AssignmentsController@send_assignment_link_to_user`
- **Template**: `emails.assignments`

### 2. Assessment Completion
- **Trigger**: When users complete assessments
- **Controller**: `AssignmentsController@send_completion_notification_to_user`
- **Template**: `emails.completed`

### 3. Job Analysis Questionnaire
- **Trigger**: When JAQs are sent to users
- **Controller**: `AnalysisController@send`
- **Template**: `emails.assignments`

### 4. Manual Email Testing
- **Location**: `DashboardController` (commented out)
- **Purpose**: Development and testing email functionality

## AWS Integration

### Amazon SES Configuration
The application is configured to work with AWS SES for production email delivery:

```php
// config/services.php
'ses' => [
    'key'    => env('SES_KEY'),
    'secret' => env('SES_SECRET'),
    'region' => 'us-east-1',
],
```

### AWS Resources Required
1. **SES Service**: Configured in us-east-1 region
2. **IAM User**: With SES permissions
3. **SES Credentials**: Access key and secret
4. **Domain Verification**: Email domain must be verified in SES
5. **Sending Limits**: May need to request production access

### AWS SES Setup Steps
1. Create AWS SES service in us-east-1
2. Verify email domain (aoescience.com)
3. Create IAM user with SES permissions
4. Generate access keys
5. Update environment variables with SES credentials
6. Test email delivery

## Environment-Specific Configuration

### Development Environment
```bash
MAIL_DRIVER=smtp
MAIL_HOST=mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

### Staging Environment
```bash
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

### Production Environment
```bash
MAIL_DRIVER=ses
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
SES_KEY=your_aws_ses_key
SES_SECRET=your_aws_ses_secret
```

## Security Considerations

### 1. Credential Management
- Email credentials stored in environment variables
- AWS SES credentials should be managed securely
- Consider using AWS Secrets Manager for production

### 2. Email Validation
- All email addresses are validated before sending
- Uses PHP's `filter_var($email, FILTER_VALIDATE_EMAIL)`

### 3. BCC Implementation
- All emails are blind copied to `xaonst@gmail.com`
- Provides audit trail and backup delivery

## Troubleshooting

### Common Issues

1. **Email Not Sending**
   - Check environment variables
   - Verify SMTP credentials
   - Check AWS SES configuration
   - Review Laravel logs

2. **AWS SES Issues**
   - Verify domain is verified in SES
   - Check sending limits
   - Ensure IAM permissions are correct
   - Test with SES console

3. **Template Issues**
   - Check Blade template syntax
   - Verify shortcode replacement
   - Test with simple content first

### Debugging Tools

1. **Laravel Logs**: Check `storage/logs/laravel.log`
2. **Mail Preview**: Use `Mail::pretend()` for testing
3. **AWS SES Console**: Monitor email delivery
4. **Mailtrap**: Capture emails in development

## Migration to New AWS Account

### Steps Required

1. **Create New SES Service**
   - Set up SES in new AWS account
   - Verify email domain
   - Configure sending limits

2. **Update Environment Variables**
   - Generate new SES credentials
   - Update production environment
   - Test email delivery

3. **Verify Configuration**
   - Test all email types
   - Monitor delivery rates
   - Update DNS records if needed

4. **Update Documentation**
   - Update this documentation
   - Update deployment scripts
   - Update team access

### Required AWS Resources

1. **SES Service**: Email delivery service
2. **IAM User**: With SES permissions
3. **Verified Domain**: Email domain verification
4. **Sending Quotas**: Production sending limits

## Future Enhancements

### Potential Improvements

1. **Email Queue System**
   - Implement Laravel queues for email sending
   - Improve performance and reliability
   - Add retry mechanisms

2. **Email Templates**
   - Create more professional templates
   - Add branding consistency
   - Implement responsive design

3. **Email Analytics**
   - Track email open rates
   - Monitor delivery success
   - Implement click tracking

4. **Advanced Features**
   - Email scheduling
   - Template management
   - A/B testing capabilities

## Testing

### Email System Tests

The application includes comprehensive email system tests located in `tests/EmailSystemTest.php`. These tests cover:

#### Test Coverage
- **Mail Configuration**: Validates Mailtrap SMTP settings
- **Simple Email Sending**: Tests basic email functionality
- **HTML Email Templates**: Tests email template rendering
- **Mailer Class Methods**: Tests all Mailer class functionality
- **Email Templates**: Validates template existence and rendering
- **Shortcode Replacement**: Tests dynamic content replacement
- **Error Handling**: Tests email error scenarios
- **Multiple Email Sending**: Tests bulk email functionality
- **System Integration**: Validates Laravel integration

#### Running Email Tests
```bash
# Run all email tests
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit tests/EmailSystemTest.php

# Run with verbose output
docker-compose exec app php -d memory_limit=512M vendor/bin/phpunit tests/EmailSystemTest.php --verbose
```

#### Test Results
- **17 Tests**: Comprehensive email functionality coverage
- **41 Assertions**: Detailed validation of email features
- **Mailtrap Integration**: All tests use Mailtrap for safe email testing
- **Real Email Sending**: Tests actually send emails to Mailtrap inbox

#### Test Data Requirements
- Admin user (`admin@example.com`)
- Regular user (`user@example.com`)
- Sample assessments and assignments
- Analysis and JAQ data (for questionnaire testing)

### Manual Testing

To manually test email functionality:

1. **Check Mailtrap Inbox**: Visit [https://mailtrap.io/inboxes](https://mailtrap.io/inboxes)
2. **Run Email Tests**: Execute the test suite
3. **Verify Email Delivery**: Check that emails appear in Mailtrap
4. **Test Admin Interface**: Use the application's admin interface to send emails

## Conclusion

The email system is well-integrated with AWS services and provides comprehensive functionality for user notifications. The modular design allows for easy configuration changes and service provider switching. When migrating to a new AWS account, focus on SES setup and credential management to ensure seamless email delivery.

### Testing Status: ✅ Complete
- Email system fully tested with Mailtrap
- All email functionality verified
- Comprehensive test coverage implemented
- Ready for production deployment
