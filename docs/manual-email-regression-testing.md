# Manual Email Regression Testing Guide

## Overview

This guide provides step-by-step instructions for manually testing email-related features through the Talent Assessment application's user interface. These tests should be performed after any changes to the email system, user management, or assessment workflows.

## Prerequisites

### Environment Setup
- ✅ **Mailtrap configured** with valid credentials
- ✅ **Application running** in development environment
- ✅ **Database seeded** with test data
- ✅ **Test users created** (admin and regular users)

### Test Data Requirements
- Admin user: `admin@example.com` / `password`
- Regular user: `user@example.com` / `password`
- Test client: TechCorp with admin user
- Sample assessments and assignments

## Test Scenarios

### 1. User Registration Email Testing

#### Test Case: New User Registration
**Objective**: Verify that new users receive welcome emails upon registration

**Steps**:
1. **Navigate to Registration Page**
   - Go to: `http://localhost:8001/register`
   - Or: `http://localhost:8001/auth/register`

2. **Create New Test User**
   - Fill in registration form:
     - Name: `Test User Email`
     - Email: `test-email-{timestamp}@example.com`
     - Username: `testemail{timestamp}`
     - Password: `testpassword123`
   - Click "Register"

3. **Verify Email Sent**
   - Check Mailtrap inbox: [https://mailtrap.io/inboxes](https://mailtrap.io/inboxes)
   - Look for welcome email to new user
   - Verify email content includes:
     - User's name
     - Welcome message
     - Login instructions

**Expected Results**:
- ✅ Email appears in Mailtrap inbox
- ✅ Email contains correct user information
- ✅ Email has proper formatting and branding

---

### 2. Password Reset Email Testing

#### Test Case: Password Reset Request
**Objective**: Verify password reset emails are sent correctly

**Steps**:
1. **Navigate to Password Reset Page**
   - Go to: `http://localhost:8001/password/reset`
   - Or: `http://localhost:8001/auth/password`

2. **Request Password Reset**
   - Enter email: `admin@example.com`
   - Click "Send Password Reset Link"

3. **Verify Reset Email**
   - Check Mailtrap inbox
   - Look for password reset email
   - Verify email contains:
     - Reset link
     - Security instructions
     - Proper branding

4. **Test Reset Link** (Optional)
   - Click reset link in email
   - Verify it leads to password reset form
   - Set new password
   - Verify login works with new password

**Expected Results**:
- ✅ Password reset email sent
- ✅ Email contains valid reset link
- ✅ Reset link works correctly

---

### 3. Assessment Assignment Email Testing

#### Test Case: Assign Assessment to User
**Objective**: Verify assessment assignment emails are sent to users

**Steps**:
1. **Login as Admin**
   - Go to: `http://localhost:8001/login`
   - Login: `admin@example.com` / `password`

2. **Navigate to Assessment Management**
   - Go to: `http://localhost:8001/assessments`
   - Or: Admin Dashboard → Assessments

3. **Create/Select Assessment**
   - Create new assessment or select existing one
   - Ensure assessment is active and configured

4. **Assign to User**
   - Find "Assign Users" or "Send Assignment" option
   - Select user: `user@example.com`
   - Set expiration date (e.g., 7 days from now)
   - Click "Send Assignment" or "Assign"

5. **Verify Assignment Email**
   - Check Mailtrap inbox
   - Look for assessment assignment email
   - Verify email contains:
     - Assessment name and description
     - Login credentials or link
     - Expiration date
     - Instructions for completion

**Expected Results**:
- ✅ Assignment email sent to user
- ✅ Email contains assessment details
- ✅ Login credentials/links work
- ✅ Expiration date clearly stated

---

### 4. Assessment Completion Email Testing

#### Test Case: User Completes Assessment
**Objective**: Verify completion notification emails are sent

**Steps**:
1. **Login as Regular User**
   - Go to: `http://localhost:8001/login`
   - Login: `user@example.com` / `password`

2. **Access Assigned Assessment**
   - Go to: `http://localhost:8001/assignments`
   - Or: Check email for assignment link
   - Click on assigned assessment

3. **Complete Assessment**
   - Answer all questions in the assessment
   - Submit the assessment
   - Confirm completion

4. **Verify Completion Email**
   - Check Mailtrap inbox
   - Look for completion notification
   - Verify email contains:
     - Confirmation of completion
     - Assessment name
     - Completion date/time
     - Next steps (if any)

**Expected Results**:
- ✅ Completion email sent
- ✅ Email confirms successful submission
- ✅ Proper completion details included

---

### 5. Bulk Email Testing

#### Test Case: Send Multiple Assignments
**Objective**: Verify bulk assignment emails work correctly

**Steps**:
1. **Login as Admin**
   - Go to: `http://localhost:8001/login`
   - Login: `admin@example.com` / `password`

2. **Navigate to Bulk Operations**
   - Go to: `http://localhost:8001/assignments/bulk`
   - Or: Admin Dashboard → Bulk Assignments

3. **Select Multiple Users**
   - Select multiple users from the list
   - Choose assessment to assign
   - Set expiration date

4. **Send Bulk Assignment**
   - Click "Send Bulk Assignment"
   - Confirm the action

5. **Verify Bulk Emails**
   - Check Mailtrap inbox
   - Verify emails sent to all selected users
   - Check email content consistency

**Expected Results**:
- ✅ Emails sent to all selected users
- ✅ Consistent email content across all emails
- ✅ Proper personalization for each user

---

### 6. Email Template Testing

#### Test Case: Verify Email Templates
**Objective**: Ensure all email templates render correctly

**Steps**:
1. **Test Assignment Template**
   - Follow assessment assignment steps above
   - Verify email template includes:
     - Company branding
     - User's name
     - Assessment details
     - Login instructions
     - Footer with contact information

2. **Test Completion Template**
   - Follow assessment completion steps above
   - Verify completion email template includes:
     - Confirmation message
     - Assessment details
     - Completion timestamp
     - Professional formatting

3. **Test Password Reset Template**
   - Follow password reset steps above
   - Verify reset email template includes:
     - Security notice
     - Reset link
     - Expiration information
     - Support contact

**Expected Results**:
- ✅ All templates render correctly
- ✅ Consistent branding across templates
- ✅ Proper formatting and styling
- ✅ All links and buttons work

---

### 7. Email Error Handling Testing

#### Test Case: Invalid Email Addresses
**Objective**: Verify system handles invalid email addresses gracefully

**Steps**:
1. **Test Registration with Invalid Email**
   - Try to register with: `invalid-email-address`
   - Verify validation error message
   - Confirm no email is sent

2. **Test Assignment to Invalid Email**
   - Try to assign assessment to: `nonexistent@example.com`
   - Verify system handles gracefully
   - Check for appropriate error messages

3. **Test Password Reset with Invalid Email**
   - Try password reset with: `invalid@email`
   - Verify validation works
   - Confirm no email is sent

**Expected Results**:
- ✅ Proper validation error messages
- ✅ No emails sent to invalid addresses
- ✅ System remains stable

---

### 8. Email Queue Testing (If Implemented)

#### Test Case: High Volume Email Sending
**Objective**: Test system performance with multiple emails

**Steps**:
1. **Send Multiple Assignments**
   - Assign assessments to 10+ users
   - Monitor system performance
   - Check email delivery

2. **Monitor Queue Performance**
   - Check queue status (if using Laravel queues)
   - Monitor email delivery timing
   - Verify all emails are sent

**Expected Results**:
- ✅ All emails sent successfully
- ✅ System performance remains stable
- ✅ No email delivery failures

---

## Email Content Verification Checklist

### Required Elements in All Emails
- [ ] **From Address**: `postmaster@mg.aoescience.com` or configured sender
- [ ] **From Name**: "AOE Science" or configured name
- [ ] **Subject Line**: Clear, descriptive subject
- [ ] **User's Name**: Personalized greeting
- [ ] **Company Branding**: Logo, colors, styling
- [ ] **Clear Call-to-Action**: What user should do next
- [ ] **Contact Information**: Support email/phone
- [ ] **Unsubscribe Option**: If applicable

### Email-Specific Requirements

#### Assignment Emails
- [ ] Assessment name and description
- [ ] Login credentials or secure link
- [ ] Expiration date and time
- [ ] Instructions for completion
- [ ] Technical support contact

#### Completion Emails
- [ ] Confirmation of successful submission
- [ ] Assessment name
- [ ] Completion timestamp
- [ ] Next steps or follow-up information

#### Password Reset Emails
- [ ] Security notice
- [ ] Reset link with expiration
- [ ] Instructions for password creation
- [ ] Contact information for issues

---

## Troubleshooting Common Issues

### Email Not Appearing in Mailtrap
1. **Check Mailtrap Configuration**
   - Verify credentials in `.env.dev`
   - Check Mailtrap inbox settings
   - Ensure inbox is active

2. **Check Application Logs**
   ```bash
   docker-compose logs app
   # Look for email-related errors
   ```

3. **Verify Email Configuration**
   ```bash
   docker-compose exec app php artisan config:show mail
   ```

### Email Content Issues
1. **Check Template Files**
   - Verify `resources/views/emails/` templates
   - Check for syntax errors
   - Ensure variables are passed correctly

2. **Test Email Templates**
   ```bash
   docker-compose exec app php artisan tinker
   # Test template rendering
   ```

### Performance Issues
1. **Check Queue Status** (if using queues)
   ```bash
   docker-compose exec app php artisan queue:work
   ```

2. **Monitor System Resources**
   ```bash
   docker stats
   ```

---

## Test Reporting

### Create Test Report
After completing all tests, document:

1. **Test Date and Environment**
   - Date: `YYYY-MM-DD`
   - Environment: Development/Staging
   - Mailtrap Inbox: [Link to inbox]

2. **Test Results Summary**
   - Total Tests: X
   - Passed: X
   - Failed: X
   - Skipped: X

3. **Issues Found**
   - Description of each issue
   - Steps to reproduce
   - Screenshots if applicable
   - Priority level

4. **Recommendations**
   - Suggested improvements
   - Performance optimizations
   - User experience enhancements

### Sample Test Report Template

```markdown
# Email Regression Test Report

**Date**: 2025-08-29
**Environment**: Development
**Tester**: [Your Name]

## Test Results

| Test Scenario | Status | Notes |
|---------------|--------|-------|
| User Registration | ✅ PASS | Welcome email sent correctly |
| Password Reset | ✅ PASS | Reset link works |
| Assessment Assignment | ✅ PASS | Assignment email delivered |
| Assessment Completion | ✅ PASS | Completion notification sent |
| Bulk Email | ✅ PASS | All users received emails |
| Template Rendering | ✅ PASS | All templates display correctly |
| Error Handling | ✅ PASS | Invalid emails handled properly |

## Issues Found

None

## Recommendations

- Consider adding email tracking for better analytics
- Implement email templates for additional scenarios
```

---

## Conclusion

This manual regression testing guide ensures comprehensive testing of all email-related features in the Talent Assessment application. Regular testing using this guide helps maintain email functionality and user experience quality.

### Key Success Metrics
- ✅ All email types sent successfully
- ✅ Email content is accurate and professional
- ✅ User experience is smooth and intuitive
- ✅ System handles errors gracefully
- ✅ Performance remains stable under load

### Maintenance
- Update this guide when new email features are added
- Review and revise test scenarios based on user feedback
- Keep test data current and relevant
- Monitor email delivery rates and user engagement
