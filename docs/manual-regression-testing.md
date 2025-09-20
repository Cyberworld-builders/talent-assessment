# Manual Regression Testing Guide

This document provides step-by-step instructions for manually testing the talent assessment platform to ensure all features work correctly after recent updates.

## Prerequisites

- Access to the staging/production environment
- Valid user credentials for testing
- Browser developer tools access
- Basic understanding of web application testing

## Test Environment Setup

1. **Clear Browser Data**
   - Clear cookies, cache, and local storage
   - Use incognito/private browsing mode for clean testing

2. **Access the Application**
   - Navigate to the application URL
   - Ensure you're testing the correct environment (staging/production)

## 1. CSRF Token Mismatch Error Handling

### Test Case 1.1: Login with Expired Session
**Objective:** Verify that CSRF token mismatches during login are handled gracefully.

**Steps:**
1. Navigate to the login page
2. Open browser developer tools (F12)
3. Go to Application/Storage tab and clear all cookies
4. Attempt to log in with valid credentials
5. **Expected Result:** 
   - Should redirect back to login page
   - Should display error message: "Your session has expired. Please try again."
   - Should preserve username in the form field
   - Should NOT show a 500 error page

### Test Case 1.2: Form Submission with Invalid CSRF Token
**Objective:** Verify that form submissions with invalid CSRF tokens are handled gracefully.

**Steps:**
1. Log in successfully to the application
2. Navigate to any form (e.g., user creation, assessment creation)
3. Open browser developer tools
4. In the Console, run: `document.querySelector('input[name="_token"]').value = 'invalid-token'`
5. Submit the form
6. **Expected Result:**
   - Should redirect back to the previous page
   - Should display error message: "Your session has expired. Please try again."
   - Should preserve form input data (except _token)
   - Should NOT show a 500 error page

### Test Case 1.3: AJAX Request with Invalid CSRF Token
**Objective:** Verify that AJAX requests with invalid CSRF tokens are handled gracefully.

**Steps:**
1. Log in successfully to the application
2. Navigate to a page with AJAX functionality (e.g., feedback submission)
3. Open browser developer tools
4. In the Console, run: `document.querySelector('meta[name="csrf-token"]').setAttribute('content', 'invalid-token')`
5. Trigger an AJAX request (e.g., submit feedback)
6. **Expected Result:**
   - Should handle the error gracefully
   - Should NOT show a 500 error in the console
   - Should display appropriate error message to user

## 2. Industry Validation in User Creation

### Test Case 2.1: User Creation Without Industry
**Objective:** Verify that industry selection is required when creating users.

**Steps:**
1. Log in as an admin user
2. Navigate to User Management → Create User
3. Fill in all required fields EXCEPT industry
4. Submit the form
5. **Expected Result:**
   - Should display validation error for industry field
   - Should NOT allow form submission
   - Should highlight the industry field as required

### Test Case 2.2: User Creation With Industry
**Objective:** Verify that user creation works with valid industry selection.

**Steps:**
1. Log in as an admin user
2. Navigate to User Management → Create User
3. Fill in all required fields INCLUDING industry
4. Submit the form
5. **Expected Result:**
   - Should successfully create the user
   - Should redirect to user list or show success message
   - User should be able to log in with new credentials

### Test Case 2.3: Industry Field UI Indication
**Objective:** Verify that the industry field clearly indicates it's required.

**Steps:**
1. Navigate to User Management → Create User
2. Locate the industry dropdown field
3. **Expected Result:**
   - Should see "*Required" text next to the industry label
   - Field should be visually marked as required

## 3. Dimension Management with Definition Field

### Test Case 3.1: Create Dimension with Definition
**Objective:** Verify that dimensions can be created with definition text.

**Steps:**
1. Log in as an admin user
2. Navigate to Dimensions Management → Create Dimension
3. Fill in name, code, and definition fields
4. Submit the form
5. **Expected Result:**
   - Should successfully create the dimension
   - Definition should be saved and displayed

### Test Case 3.2: Edit Dimension Definition
**Objective:** Verify that dimension definitions can be edited.

**Steps:**
1. Navigate to an existing dimension
2. Click Edit
3. Modify the definition text
4. Save changes
5. **Expected Result:**
   - Should successfully update the dimension
   - New definition should be displayed

### Test Case 3.3: Dimension Parent Selection
**Objective:** Verify that dimensions can have no parent (deselect parent).

**Steps:**
1. Navigate to Dimensions Management → Create Dimension
2. Set "Is Subdimension" to Yes
3. In the parent dropdown, select "---" (first option)
4. Submit the form
5. **Expected Result:**
   - Should successfully create the dimension
   - Dimension should not have a parent

## 4. Feedback Library Dynamic Tabs

### Test Case 4.1: Feedback Library Tabs Population
**Objective:** Verify that feedback library tabs are populated from assessments.

**Steps:**
1. Log in as an admin user
2. Navigate to Feedback Libraries
3. Check the left sidebar tabs
4. **Expected Result:**
   - Should see tabs for: Involved-360, Involved-Leader, Involved-Blockers
   - Tabs should be clickable and show relevant content
   - Should NOT see tabs for removed assessments (Involved-Me, Involved-Me Peak Week, David Codes Custom)

### Test Case 4.2: Feedback Library Content
**Objective:** Verify that feedback library content is properly displayed.

**Steps:**
1. Click on each assessment tab in the feedback library
2. Verify content loads for each tab
3. **Expected Result:**
   - Each tab should show relevant feedback content
   - No JavaScript errors in console
   - Content should be properly formatted

## 5. Database Permission Handling

### Test Case 5.1: Reseller Database Creation
**Objective:** Verify that reseller database creation works with proper permissions.

**Steps:**
1. Log in as an admin user
2. Navigate to Reseller Management
3. Attempt to create a new reseller with database
4. **Expected Result:**
   - Should successfully create the reseller
   - Should successfully create the associated database
   - Should NOT show permission errors

### Test Case 5.2: Database Creation Error Handling
**Objective:** Verify graceful error handling for database permission issues.

**Steps:**
1. If database permissions are not properly set up
2. Attempt to create a reseller with database
3. **Expected Result:**
   - Should display user-friendly error message
   - Should NOT show technical database errors
   - Should provide guidance on contacting system administrator

## 6. General Application Health

### Test Case 6.1: Login Functionality
**Objective:** Verify that login works correctly.

**Steps:**
1. Navigate to the login page
2. Enter valid credentials
3. Submit the form
4. **Expected Result:**
   - Should successfully log in
   - Should redirect to dashboard
   - Should maintain session

### Test Case 6.2: Navigation and Routing
**Objective:** Verify that all navigation links work correctly.

**Steps:**
1. Log in successfully
2. Test all main navigation links
3. Test breadcrumb navigation
4. **Expected Result:**
   - All links should work without errors
   - Pages should load correctly
   - No 404 or 500 errors

### Test Case 6.3: Form Submissions
**Objective:** Verify that all forms submit correctly.

**Steps:**
1. Test user creation form
2. Test assessment creation form
3. Test dimension creation form
4. Test feedback submission forms
5. **Expected Result:**
   - All forms should submit successfully
   - Validation should work correctly
   - Success/error messages should display appropriately

## 7. Error Logging and Monitoring

### Test Case 7.1: Error Log Verification
**Objective:** Verify that errors are logged appropriately.

**Steps:**
1. Check application logs for any 500 errors
2. Verify that CSRF token mismatches are logged as info, not errors
3. **Expected Result:**
   - No 500 errors in logs
   - CSRF issues logged as info level
   - Authentication issues logged appropriately

## Test Results Documentation

For each test case, document:
- **Test Date:** [Date of testing]
- **Tester:** [Name of person conducting test]
- **Environment:** [Staging/Production]
- **Result:** [Pass/Fail]
- **Notes:** [Any observations or issues found]
- **Screenshots:** [If applicable]

## Common Issues and Troubleshooting

### Issue: CSRF Token Mismatch Still Shows 500 Error
**Solution:** 
- Clear browser cache and cookies
- Verify the exception handler changes are deployed
- Check that the application is using the updated code

### Issue: Industry Validation Not Working
**Solution:**
- Verify that the validation rules are properly set
- Check that the form includes the industry field
- Ensure the database has industry data

### Issue: Feedback Library Tabs Not Loading
**Solution:**
- Check that assessments are properly seeded
- Verify JavaScript console for errors
- Ensure the controller is returning assessment data

## Contact Information

For issues or questions regarding this testing process, contact:
- **Development Team:** [Contact information]
- **QA Team:** [Contact information]
- **System Administrator:** [Contact information]

---

**Last Updated:** [Current Date]
**Version:** 1.0
**Tested Against:** Laravel 5.1.46, PHP 7.4



