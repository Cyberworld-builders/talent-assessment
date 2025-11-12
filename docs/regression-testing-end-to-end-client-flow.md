# End-to-End Client Flow Regression Testing Guide

## Overview
This document provides step-by-step instructions for regression testing the complete client workflow from client creation through assessment completion and result viewing.

## Test Objective
Validate the complete lifecycle of:
1. Client creation
2. User management (CSV upload)
3. Group management (CSV upload)
4. Assessment assignment to groups
5. Email notification delivery
6. User assessment completion
7. Admin result viewing

## Prerequisites
- Admin access to the talent assessment platform
- Test CSV files for users and groups (see [CSV Format Requirements](#csv-format-requirements))
- Access to email inbox for testing notifications
- A 360 assessment configured in the system

## Test Environment
- **Staging**: https://talent-staging.cyberworldbuilders.dev
- **Production**: https://talent.cyberworldbuilders.dev

**Recommendation**: Always test on staging before production deployments.

---

## Step-by-Step Testing Procedure

### 1. Create a New Client

**Login as Admin**
1. Navigate to the platform URL
2. Login with admin credentials
3. Verify successful login to dashboard

**Create Client**
1. Navigate to **Clients** from the main menu
2. Click **Create New Client** button
3. Fill in client details:
   - **Client Name**: `Test Client - [Current Date]`
   - **Logo**: Upload a test logo (optional)
   - **Background**: Upload a test background image (optional)
   - **Industry**: Select an industry
   - **Other fields**: Fill as needed
4. Click **Save** or **Create Client**
5. **Verify**: Client appears in the clients list
6. Note the client ID or name for reference

---

### 2. Upload Users from CSV

**Prepare CSV File**
1. Create or use existing user CSV file (see [User CSV Format](#user-csv-format))
2. Ensure CSV includes at least 3-5 test users
3. Include a user with your own email address for testing

**Upload Users**
1. Navigate to the newly created client
2. Go to **Users** tab
3. Click **Upload Users** or **Import from CSV**
4. Select your prepared CSV file
5. Click **Upload** or **Import**
6. **Verify**: 
   - Success message appears
   - All users appear in the user list
   - User count matches CSV row count
   - Email addresses are correct

**Expected Result**: All users from CSV are created and visible in the client's user list.

---

### 3. Upload Groups from CSV

**Prepare CSV File**
1. Create or use existing group CSV file (see [Group CSV Format](#group-csv-format))
2. Ensure CSV references users uploaded in Step 2
3. Include at least 2 groups with multiple users each

**Upload Groups**
1. From the client view, go to **Groups** tab
2. Click **Upload Groups** or **Import from CSV**
3. Select your prepared CSV file
4. Click **Upload** or **Import**
5. **Verify**:
   - Success message appears
   - All groups appear in the group list
   - Users are correctly assigned to groups
   - Group member counts are accurate

**Expected Result**: All groups from CSV are created with correct user assignments.

---

### 4. Assign 360 Assessment to Entire Group

**Select Assessment**
1. Navigate to **Assessments** from the main menu
2. Locate a 360 assessment (or create one if needed)
3. Note: 360 assessments have `target` set to "Other User"

**Assign to Group**
1. From the assessment view, click **Assign** or **Assign Assessment**
2. Or navigate to **Assignments** > **Create New Assignment**
3. Fill in assignment details:
   - **Client**: Select the test client created in Step 1
   - **Assessment**: Select the 360 assessment
   - **Expiration Date**: Set to 7-14 days from now
   - **Email Notification**: Select **Yes**
   - **Email Reminder**: Select **Yes** (optional)
   - **Reminder Frequency**: Select **1 Week** (if enabled)

**Select Recipients**
1. Click **From Groups** button
2. Select the group(s) created in Step 3
3. **Verify**: All group members appear in the assignment list

**Configure Email**
1. Review the email preview
2. Customize subject/body if needed (optional)
3. Ensure email notification is enabled

**Submit Assignment**
1. Click **Save** or **Assign**
2. **Verify**:
   - Success message appears
   - Assignment appears in assignments list
   - Assignment count matches expected number of users

**Expected Result**: Assignment created successfully for all group members.

---

### 5. Verify Email Notification Delivery

**Check Email Inbox**
1. Access the email inbox for the test user (your email)
2. **Verify email received** with:
   - Correct sender (e.g., noreply@involvedtalent.com)
   - Assessment name in subject/body
   - Login link or assessment link
   - Expiration date mentioned
   - Professional formatting

**Email Content Check**
- [ ] Subject line is clear and includes assessment name
- [ ] Recipient name is personalized
- [ ] Login credentials (if new user)
- [ ] Direct link to assessment
- [ ] Expiration date is displayed
- [ ] Instructions are clear
- [ ] No broken formatting or HTML issues

**Expected Result**: Email received within 1-2 minutes with all required information.

---

### 6. Complete Assessment as User

**Access Assessment from Email**
1. Click the assessment link in the email
2. If required, login with credentials from email
3. **Verify**: User is directed to the assessment page

**Navigate Assessment**
1. Review assessment instructions/description
2. **Verify**: Instructions are clear and well-formatted
3. For paginated assessments:
   - **Verify**: Instructions appear only on first page
   - **Verify**: Questions start on second page
   - **Verify**: Page navigation works correctly

**Complete Assessment Questions**
1. Answer all required questions:
   - Multiple choice questions
   - Text input fields
   - Slider fields (if applicable)
   - Any special field types
2. **Verify**: 
   - All field types render correctly
   - Tables with borders/styling display properly
   - Rich text content is preserved
   - No question numbers visible (clean interface)
   - Required fields are marked

**For 360 Assessments**
1. **Verify**: Target selection is available
2. Select the person being assessed
3. Complete all questions about the target person

**Submit Assessment**
1. Click **Submit** button
2. **Verify**: 
   - Confirmation message appears
   - Success page or redirect occurs
   - No error messages

**Expected Result**: Assessment completed successfully without errors.

---

### 7. View Assessment Submission as Admin

**Navigate to Submissions**
1. Login as admin
2. Navigate to **Assignments** or **Results**
3. Filter by:
   - Client: Test client
   - Assessment: 360 assessment
   - Date range: Today

**Locate Completed Assessment**
1. Find the test user's submission
2. **Verify**: Submission appears in results list with:
   - User name
   - Assessment name
   - Completion date/time
   - Status: Completed

**View Submission Details**
1. Click on the submission to view details
2. **Verify submission contains**:
   - All answered questions
   - User's responses are recorded correctly
   - Rich text/table formatting is preserved
   - Dimension assignments are correct (if applicable)
   - Target person recorded (for 360 assessments)

**Check Assessment Report (if available)**
1. Generate or view assessment report
2. **Verify report shows**:
   - Correct scores/results
   - Charts and visualizations render
   - Data matches submitted responses
   - Proper formatting

**Expected Result**: Submission visible to admin with all data intact.

---

## CSV Format Requirements

### User CSV Format

**File Name**: `users_import.csv`

**Required Columns**:
- `name`: Full name of the user
- `email`: Valid email address (must be unique)
- `username`: Login username (must be unique)
- `password`: Plain text password (will be hashed on import)

**Optional Columns**:
- `job_title`: User's job title
- `department`: Department name
- `hire_date`: Date of hire (YYYY-MM-DD format)
- `phone`: Phone number

**Example**:
```csv
name,email,username,password,job_title,department
John Doe,john.doe@example.com,jdoe,Test123!,Manager,Sales
Jane Smith,jane.smith@example.com,jsmith,Test123!,Developer,Engineering
Bob Johnson,bob.johnson@example.com,bjohnson,Test123!,Analyst,Finance
```

**Notes**:
- Ensure no duplicate emails or usernames
- Password requirements: minimum 6 characters
- Include at least one user with your email for testing

---

### Group CSV Format

**File Name**: `groups_import.csv`

**Required Columns**:
- `group_name`: Name of the group
- `user_email`: Email of user to add (must match uploaded users)

**Example**:
```csv
group_name,user_email
Sales Team,john.doe@example.com
Sales Team,jane.smith@example.com
Engineering Team,jane.smith@example.com
Engineering Team,bob.johnson@example.com
```

**Notes**:
- Users can belong to multiple groups
- User emails must match exactly with uploaded users
- Group names can repeat (adds multiple users to same group)

---

## Validation Checklist

Use this checklist to ensure comprehensive testing:

### Client Creation
- [ ] Client created successfully
- [ ] Client appears in client list
- [ ] Client details saved correctly
- [ ] Logo and background images uploaded (if applicable)

### User Upload
- [ ] CSV file accepted
- [ ] All users imported successfully
- [ ] User count matches CSV
- [ ] Email addresses correct
- [ ] Passwords set properly
- [ ] No duplicate errors

### Group Upload
- [ ] CSV file accepted
- [ ] All groups created successfully
- [ ] Users correctly assigned to groups
- [ ] Member counts accurate
- [ ] Multiple group membership works

### Assignment Creation
- [ ] Assessment assigned to group
- [ ] All group members included
- [ ] Expiration date set correctly
- [ ] Email notification configured
- [ ] Assignment saved successfully

### Email Notification
- [ ] Email received within 2 minutes
- [ ] Correct sender address
- [ ] Assessment link works
- [ ] Login credentials included (if new user)
- [ ] Formatting is professional
- [ ] No broken links or HTML

### Assessment Completion
- [ ] Link from email works
- [ ] Login successful (if required)
- [ ] Assessment loads correctly
- [ ] Instructions display on first page only
- [ ] Questions start on correct page
- [ ] All field types render properly
- [ ] Tables/rich text preserved
- [ ] No question numbers visible to user
- [ ] Required fields marked
- [ ] Target selection works (360 assessments)
- [ ] Submit button functional
- [ ] Success confirmation displayed

### Admin Viewing
- [ ] Submission appears in results
- [ ] Completion date/time recorded
- [ ] All responses captured
- [ ] Rich text/formatting preserved
- [ ] Dimension data correct
- [ ] Target person recorded (360)
- [ ] Report generates successfully (if applicable)

---

## Common Issues and Troubleshooting

### Issue: Users not imported from CSV
**Possible Causes**:
- CSV format incorrect (wrong delimiters, encoding)
- Duplicate emails or usernames
- Missing required columns

**Solution**:
- Verify CSV format matches template
- Check for duplicate entries
- Ensure UTF-8 encoding

### Issue: Groups not created
**Possible Causes**:
- User emails don't match uploaded users
- CSV format issues

**Solution**:
- Double-check email addresses match exactly
- Ensure user emails exist in system before group import

### Issue: Email not received
**Possible Causes**:
- Email in spam folder
- Email configuration issues
- Invalid email addresses

**Solution**:
- Check spam/junk folders
- Verify email configuration (check logs)
- Confirm email addresses are valid

### Issue: Assessment won't submit
**Possible Causes**:
- Required fields not completed
- JavaScript errors
- Network issues

**Solution**:
- Check browser console for errors
- Ensure all required fields filled
- Try different browser
- Check network connection

### Issue: Submission not visible to admin
**Possible Causes**:
- Submission didn't save
- Filter settings hiding result
- Permissions issue

**Solution**:
- Verify submission success message was shown
- Clear/adjust filters
- Check admin permissions

---

## Test Data Cleanup

After completing regression testing, clean up test data:

1. **Delete Test Assignments**
   - Navigate to Assignments
   - Delete all test assignments created

2. **Remove Test Users** (Optional)
   - Navigate to client users
   - Delete test users if not needed

3. **Delete Test Groups** (Optional)
   - Navigate to groups
   - Remove test groups

4. **Delete Test Client** (Optional)
   - Navigate to clients
   - Delete the test client entirely
   - **Warning**: This will cascade delete all related data

---

## Expected Time to Complete
- **Full Test**: 30-45 minutes
- **Experienced Tester**: 20-30 minutes
- **Quick Smoke Test**: 15 minutes (skip some verification steps)

---

## Sign-off Template

**Test Date**: _________________  
**Tester Name**: _________________  
**Environment**: [ ] Staging [ ] Production  
**Test Result**: [ ] Pass [ ] Fail  

**Issues Found**:
```
Issue 1: [Description]
Severity: [High/Medium/Low]
Steps to Reproduce:

Issue 2: [Description]
...
```

**Additional Notes**:
```
[Any additional observations or comments]
```

**Approved By**: _________________  
**Date**: _________________

---

## Related Documentation
- [User CSV Upload Documentation](./user-csv-upload.md)
- [Assessment Assignment Guide](./assessment-assignment-guide.md)
- [Email Configuration](./email-configuration-fix-summary.md)
- [Assessment Editor Guide](./modern-assessment-editor-guide.md)

---

## Version History
- **v1.0** (2025-10-12): Initial document created

