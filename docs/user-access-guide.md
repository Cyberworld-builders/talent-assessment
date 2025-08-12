# User Access Guide

## Overview

This guide provides instructions for accessing the Talent Assessment application with the seeded test data. The application includes multiple user accounts with different roles and permissions for testing various features.

## Application Access

### Local Development Environment

If running locally with Docker:
- **URL**: `http://localhost:8001`
- **Direct Container Access**: `http://localhost:8001`

### Production/Staging Environment

- **URL**: `https://talent-aws.cyberworldbuilders.dev`

## Available User Accounts

### System Administrator (AOE Admin)
- **Email**: `admin@example.com`
- **Password**: `password`
- **Role**: AOE Admin (Level 4)
- **Permissions**: Full system access, can manage all clients, users, and assessments

### Regular User
- **Email**: `user@example.com`
- **Password**: `password`
- **Role**: User (Level 1)
- **Permissions**: Basic user access, can complete assessments

### Client Administrators

#### TechCorp Solutions
- **Email**: `techadmin@techcorp.com`
- **Password**: `password`
- **Role**: Client Admin (Level 2)
- **Client**: TechCorp Solutions
- **Permissions**: Manage TechCorp users, jobs, and assessments

#### Manufacturing Inc
- **Email**: `mfgadmin@manufacturing.com`
- **Password**: `password`
- **Role**: Client Admin (Level 2)
- **Client**: Manufacturing Inc
- **Permissions**: Manage Manufacturing Inc users, jobs, and assessments

#### Consulting Partners
- **Email**: `consultadmin@consulting.com`
- **Password**: `password`
- **Role**: Client Admin (Level 2)
- **Client**: Consulting Partners
- **Permissions**: Manage Consulting Partners users, jobs, and assessments

## Seeded Data Overview

### Clients Created
1. **TechCorp Solutions**
   - Technology company with software engineering focus
   - Primary color: Blue (#007bff)
   - Jobs: Software Engineer, Product Manager, Data Scientist

2. **Manufacturing Inc**
   - Manufacturing company with operations focus
   - Primary color: Red (#dc3545)
   - Jobs: Production Supervisor, Quality Control Specialist, Maintenance Technician

3. **Consulting Partners**
   - Consulting firm with business focus
   - Primary color: Purple (#6f42c1)
   - Jobs: Senior Consultant, Business Analyst, Project Manager

### Assessments Available
1. **Personality Assessment**
   - Comprehensive personality evaluation for workplace compatibility
   - 10 questions per page, not timed
   - Available to all clients

2. **Cognitive Ability Test**
   - Problem-solving and analytical thinking assessment
   - 15 questions per page, 45-minute time limit
   - Available to TechCorp and Manufacturing Inc

3. **Leadership Potential**
   - Assessment of leadership skills and potential
   - 12 questions per page, not timed
   - Available to Manufacturing Inc and Consulting Partners

### Applicant Users
Each client has 8-15 applicant users created for testing:
- Random names and email addresses
- All use password: `password`
- Assigned to various jobs within their respective clients

## Login Instructions

### Step 1: Access the Application
1. Navigate to the application URL
2. You should be redirected to the login page

### Step 2: Login
1. Enter one of the email addresses listed above
2. Enter the password: `password`
3. Click "Login" or press Enter

### Step 3: Dashboard Access
After successful login, you'll be redirected to the appropriate dashboard based on your role:

- **AOE Admin**: System-wide dashboard with access to all clients
- **Client Admin**: Client-specific dashboard with access to their organization's data
- **Regular User**: Basic user dashboard

## Dashboard Navigation

### AOE Admin Dashboard (`admin@example.com`)
- **Clients**: View and manage all client organizations
- **Users**: View and manage all users across all clients
- **Assessments**: View and manage all assessments
- **Reports**: Access system-wide reports and analytics

### Client Admin Dashboard (e.g., `techadmin@techcorp.com`)
- **Users**: Manage users within your client organization
- **Jobs**: Manage job postings and requirements
- **Assignments**: Assign assessments to users
- **Reports**: View reports for your organization
- **Settings**: Configure client-specific settings and branding

### User Dashboard (`user@example.com`)
- **My Assessments**: View assigned assessments
- **Profile**: Update personal information
- **Results**: View completed assessment results

## Testing Scenarios

### Scenario 1: Client Admin Workflow
1. Login as `techadmin@techcorp.com`
2. Navigate to "Jobs" to view the three created jobs
3. Navigate to "Users" to see the applicant users
4. Navigate to "Assignments" to assign assessments to users
5. Navigate to "Reports" to view assessment results

### Scenario 2: Assessment Assignment
1. Login as a client admin
2. Go to "Assignments" section
3. Select a job (e.g., "Software Engineer")
4. Choose assessments to assign (Personality, Cognitive)
5. Select applicant users to assign to
6. Set expiration dates and send invitations

### Scenario 3: User Assessment Completion
1. Login as an applicant user (any of the generated users)
2. View assigned assessments
3. Complete assessments
4. View results and feedback

### Scenario 4: System Administration
1. Login as `admin@example.com`
2. Navigate to "Clients" to view all three client organizations
3. Navigate to "Users" to see all users across all clients
4. Navigate to "Assessments" to view the three created assessments
5. Access system-wide reports and analytics

## Troubleshooting

### Common Issues

#### Login Fails
- Verify the email address is correct
- Ensure you're using `password` as the password
- Check that the application is running and accessible

#### Dashboard Not Loading
- Clear browser cache and cookies
- Try accessing in an incognito/private browser window
- Check browser console for JavaScript errors

#### Missing Data
- Ensure the seeder was run successfully
- Check the database connection
- Verify the user has the correct role and client association

### Database Verification
To verify the seeded data exists, you can run:
```bash
docker compose exec app php artisan tinker
```
Then check for data:
```php
App\Client::count(); // Should return 3
App\User::count(); // Should return 40+
App\Assessment::count(); // Should return 3
App\Job::count(); // Should return 9
```

## Security Notes

- **Test Environment**: These credentials are for testing only
- **Password**: All users use the same password (`password`) for convenience
- **Production**: Use strong, unique passwords in production environments
- **Access Control**: Different roles have different access levels as designed

## Next Steps

After familiarizing yourself with the basic functionality:
1. Explore the assessment creation process
2. Test the assignment workflow
3. Review the reporting features
4. Experiment with different user roles and permissions
5. Test the multi-tenant isolation between clients
