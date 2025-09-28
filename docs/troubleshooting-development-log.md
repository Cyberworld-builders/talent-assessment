# Troubleshooting & Development Log

## Overview
This document chronicles major troubleshooting sessions, development challenges, and solutions implemented for the Involved Talent Assessment Platform. It serves as a historical record of critical issues resolved and development patterns established.

---

## Session 1: Docker Production Optimization & CI/CD Enhancement (v1.5.8-release)

### Date: September 2025
### Issue: Production deployment optimization and CI/CD workflow improvements

### Problem Statement
- Need to separate npm and composer builds into distinct stages for optimized production containers
- Development environment needed build tools and hot reloading capabilities
- CI/CD workflows required manual triggering and test skipping options

### Root Cause Analysis
- Single-stage Docker builds included unnecessary build tools in production
- Development environment lacked proper hot reloading setup
- GitHub Actions workflows lacked flexibility for manual deployments

### Solution Implemented

#### 1. Multi-Stage Docker Build Architecture
**Files Modified:**
- `Dockerfile.production` (created)
- `Dockerfile` (refactored for development)

**Key Changes:**
```dockerfile
# Production: Multi-stage build with separate npm and composer stages
FROM node:6.17.1-alpine AS node-build
# ... npm build process

FROM php:7.4-cli AS composer-build  
# ... composer install --no-dev

FROM php:7.4-apache
# ... final minimal production image
```

**Development Environment:**
```dockerfile
# Development: Single stage with build tools and artisan serve
FROM php:7.4-apache
# ... install build tools, Node.js, composer
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

#### 2. CI/CD Workflow Enhancements
**Files Modified:**
- `.github/workflows/staging-deploy-tag.yml`
- `.github/workflows/production-deploy-tag.yml`

**Key Features Added:**
- `workflow_dispatch` triggers for manual deployment
- `skip_tests` option for faster deployments
- Consistent `IMAGE_TAG` handling
- Production builds using `Dockerfile.production`

#### 3. Dependency Management
**Files Modified:**
- `.gitignore` (commented out `*.lock`)
- `composer.lock` (added to repository)

### Results
- ✅ Production images reduced in size by excluding build tools
- ✅ Development environment supports hot reloading
- ✅ Manual deployment triggers available
- ✅ Consistent dependency versions across environments

---

## Session 2: Critical User Loading & Error Handling Fixes (v1.5.9-release)

### Date: September 2025
### Issue: Users unable to load myinvolvedtalent.com due to critical errors

### Problem Statement
- CSRF token mismatch errors causing session invalidation
- Missing industry field validation causing user creation failures
- Poor error handling leading to user experience issues

### Root Cause Analysis
- `TokenMismatchException` not properly handled
- Industry field validation missing in user creation controllers
- Authentication errors not gracefully handled

### Solution Implemented

#### 1. Enhanced Exception Handling
**Files Modified:**
- `app/Exceptions/Handler.php`

**Key Changes:**
```php
protected $dontReport = [
    TokenMismatchException::class,
    UnauthorizedException::class,
    ValidationException::class,
];

public function render($request, Exception $e)
{
    if ($e instanceof TokenMismatchException) {
        \Session::flush();
        \Auth::logout();
        return redirect()->to('/login')
            ->withInput($request->except('_token', 'password', 'password_confirmation'))
            ->withErrors(['_token' => 'Your session has expired. Please log in again.']);
    }
    // ... additional error handling
}
```

#### 2. Industry Field Validation
**Files Modified:**
- `app/Http/Controllers/UsersController.php`
- `app/Http/Controllers/ResellersController.php`

**Key Changes:**
```php
$validator = Validator::make($data, [
    'name' => 'required',
    'username' => 'required|unique:users',
    'password' => 'required|min:4',
    'industry_id' => 'required|exists:industries,id'
]);

if (empty($data['industry_id'])) {
    return redirect()->back()
        ->withErrors(['industry_id' => 'The industry field is required.'])
        ->withInput($request->except('password', 'password_confirmation'));
}
```

### Results
- ✅ Graceful CSRF token error handling
- ✅ Comprehensive industry field validation
- ✅ Improved user experience with clear error messages
- ✅ Form data preservation on validation errors

---

## Session 3: Calendar/Datepicker Functionality Fix (v1.5.10-release → v1.5.12-release)

### Date: September 2025
### Issue: Calendar popup not appearing on assignment forms

### Problem Statement
- Datepicker calendar not appearing when clicking date input fields
- 404 errors for missing JavaScript and CSS assets
- Inconsistent behavior between development and production environments

### Root Cause Analysis
- Missing JavaScript initialization for bootstrap datepicker
- Gulp build process not copying datepicker assets
- Staging/production deployments using incorrect commit references

### Solution Implemented

#### 1. JavaScript Initialization
**Files Modified:**
- `resources/views/dashboard/assignments/assign.blade.php`
- `resources/views/dashboard/assignments/assign2.blade.php`
- `resources/views/clientdashboard/assign.blade.php`

**Key Changes:**
```javascript
$(document).ready(function() {
    // Initialize datepicker
    $('.datepicker').datepicker({
        format: 'D, dd M yyyy',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date()
    });
});
```

#### 2. Gulp Build Process Fix
**Files Modified:**
- `gulpfile.js`

**Key Changes:**
```javascript
.copy('resources/assets/xenon/js/datepicker', 'public/assets/js/datepicker')
.copy('resources/assets/xenon/js/daterangepicker', 'public/assets/js/daterangepicker')
```

#### 3. Deployment Process Correction
**Issue Discovered:** Staging deployments using main branch instead of specific tag commits

**Solution:**
```bash
# Correct deployment command with ref parameter
gh workflow run staging-deploy-tag.yml --ref v1.5.13-staging --field tag=v1.5.13-staging --field skip_tests=true
```

### Results
- ✅ Calendar popup functionality restored
- ✅ All datepicker assets properly built and deployed
- ✅ Consistent behavior across all environments
- ✅ Proper deployment process with correct commit references

---

## Session 4: Assessment Editor Persistence Issue (Issue #65)

### Date: September 2025
### Issue: Assessment editor changes not persisting

### Problem Statement
- Users can edit assessment configurations (move questions, reorder, WYSIWYG editor)
- UI shows success when saving
- Changes don't persist when viewing the assessment

### Investigation Findings

#### 1. Code Analysis
**Key Files Identified:**
- `resources/views/dashboard/assessments/edit.blade.php`
- `app/Http/Controllers/AssessmentsController.php` (update method)
- `resources/assets/js/create-assessment-form.js`
- `app/Question.php` (model)

#### 2. Potential Issues Identified
**JavaScript Issue:**
```javascript
// Line 838 in create-assessment-form.js
$("body").prepend(data); // This seems incorrect
```

**Data Processing:**
- Question data properly processed in `update_questions()` method
- Database operations appear correct in `update()` method
- Question model has proper fillable attributes

#### 3. Current Status
- Investigation in progress
- Branch: `65-assessment-editor-changes-are-not-persisting`
- Latest main branch merged successfully

### Next Steps
1. Test current functionality to reproduce the issue
2. Debug JavaScript form submission process
3. Verify database operations are executing correctly
4. Implement fix and test persistence

---

## Session 5: Email Date Accuracy & Branding Fix (Issue #73)

### Date: September 2025
### Issue: Email dates showing inaccurate dates and incorrect branding

### Problem Statement
- Email dates were showing "weird dates" that didn't match user expectations
- Email footers still displayed "AOE Science" instead of "Involved Talent"
- Users reported confusion about assignment expiration dates

### Root Cause Analysis
- **Date Format Issue**: The date format `'D, d M Y'` (e.g., "Mon, 23 Sep 2025") was causing validation problems
- **Day-of-Week Validation**: When users selected invalid day-of-week combinations (like "Mon, 23 Sep 2025" when Sep 23, 2025 is actually Tuesday), Carbon would "fix" it by finding the next matching date
- **Branding Inconsistency**: Email templates and configuration still contained old "AOE Science" branding

### Solution Implemented

#### 1. Date Format Standardization
**Files Modified:**
- `app/Http/Controllers/AssignmentsController.php`
- `resources/views/dashboard/assignments/partials/_assignform.blade.php`
- `resources/views/dashboard/assignments/assign.blade.php`
- `resources/views/dashboard/assignments/assign2.blade.php`
- `resources/views/clientdashboard/assign.blade.php`

**Key Changes:**
```php
// Before: Problematic format with day-of-week
$expires = Carbon::createFromFormat('D, d M Y', $data['expiration']);

// After: Clean format without day-of-week
$expires = Carbon::createFromFormat('d M Y', $data['expiration']);
```

**JavaScript Datepicker Updates:**
```javascript
// Before: Format included day-of-week
format: 'D, dd M yyyy'

// After: Clean format without day-of-week
format: 'dd M yyyy'
```

#### 2. Email Template Branding Updates
**Files Modified:**
- `resources/views/emails/assignment.blade.php`
- `resources/views/emails/completed.blade.php`
- `config/mail.php`

**Key Changes:**
```php
// Email templates updated
<div class="footer-text">&copy; {{ date('Y') }} Involved Talent</div>

// Mail configuration updated
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'postmaster@mg.involvedtalent.com'), 
    'name' => env('MAIL_FROM_NAME', 'Involved Talent')
],
```

#### 3. User-Friendly Email Date Display
**Email Template Enhancement:**
```php
// Email shows full day name for clarity
You have been assigned to complete the {{ $assessment->name }} assessment. 
This assessment will expire on {{ $expire_date->format('l, F j, Y') }}.
// Output: "Tuesday, September 23, 2025"
```

#### 4. Comprehensive Testing
**Files Created:**
- `tests/EmailDateAndBrandingTest.php`

**Test Coverage:**
- Date parsing accuracy with new format
- Email branding verification
- Mail configuration validation
- Date format validation
- Email date formatting
- Form date generation

### Results
- ✅ **Email dates are now accurate** - No more "weird dates" caused by day-of-week validation issues
- ✅ **Email branding updated** - All emails now show "Involved Talent" instead of "AOE Science"
- ✅ **Date picker improved** - Users can no longer select invalid date combinations
- ✅ **Email format user-friendly** - Emails show full day names for clarity
- ✅ **All tests passing** - 6 tests with 18 assertions all pass successfully

### Technical Details
**Before**: 
- Date format: `'D, d M Y'` (e.g., "Mon, 23 Sep 2025")
- Problem: "Mon, 23 Sep 2025" would parse to "2025-09-29" (next Monday) because Sep 23, 2025 is actually Tuesday
- Branding: "AOE Science" and "The AOE Group"

**After**:
- Date format: `'d M Y'` (e.g., "23 Sep 2025") 
- Solution: "23 Sep 2025" parses correctly to "2025-09-23" (Tuesday)
- Email display: "Tuesday, September 23, 2025" (user-friendly format)
- Branding: "Involved Talent"

---

## Development Patterns & Best Practices Established

### 1. Docker Development
- **Development**: Single-stage builds with build tools and `artisan serve`
- **Production**: Multi-stage builds with minimal final images
- **Hot Reloading**: Use `artisan serve` instead of Apache for development

### 2. CI/CD Workflows
- Always use `--ref` parameter when triggering manual deployments
- Include `skip_tests` option for faster deployments
- Use consistent `IMAGE_TAG` handling across workflows

### 3. Error Handling
- Graceful handling of CSRF token mismatches
- Comprehensive form validation with user-friendly messages
- Proper session management and cleanup

### 4. Frontend Asset Management
- Ensure Gulp build process copies all necessary assets
- Verify assets are available in production builds
- Test functionality across all environments

### 5. Database Operations
- Proper mass assignment protection with `$fillable` arrays
- Comprehensive validation rules
- Graceful error handling and user feedback

---

## Troubleshooting Methodology

### 1. Problem Identification
- Reproduce the issue in development environment
- Check browser console for JavaScript errors
- Verify database operations are executing
- Test across different environments

### 2. Root Cause Analysis
- Examine code flow from frontend to backend
- Check for missing dependencies or assets
- Verify configuration and environment settings
- Review recent changes and deployments

### 3. Solution Implementation
- Implement fixes incrementally
- Test thoroughly in development
- Deploy to staging for validation
- Monitor production deployment

### 4. Documentation
- Document all changes and reasoning
- Update changelog with user-friendly descriptions
- Maintain troubleshooting records for future reference

---

## Key Learnings

1. **Docker Build Context**: Files must exist in build context, not just locally
2. **GitHub Actions Ref Parameter**: Critical for using correct commit in manual deployments
3. **Frontend Asset Pipeline**: Gulp must explicitly copy all required assets
4. **Error Handling**: Graceful error handling significantly improves user experience
5. **Environment Consistency**: Test across all environments to ensure functionality

---

## Future Improvements

1. **Automated Testing**: Implement comprehensive test suite for critical functionality
2. **Monitoring**: Add application monitoring for production issues
3. **Documentation**: Maintain up-to-date API and deployment documentation
4. **Code Review**: Establish systematic code review process
5. **Performance**: Regular performance audits and optimizations

---

*Last Updated: September 23, 2025*
*Document Version: 1.1*
