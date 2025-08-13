# PR 2: User Industry Integration

## Overview
Connect users to industries by adding industry assignment functionality to user registration, profiles, and management interfaces.

## Background
Building on the industries foundation from PR 1, this PR adds the ability to assign users to specific industries, which will enable industry-specific features like benchmarks and reporting.

## Objectives
- Add industry_id field to users table
- Update User model with industry relationship
- Implement industry selection in user forms
- Update user management interfaces

## Technical Requirements

### Database Changes
- Create migration to add `industry_id` to users table
  - `industry_id` (unsigned integer, nullable)
  - Foreign key constraint to industries table

### Models
- Update `app/User.php` model
  - Add `industry_id` to fillable array
  - Add `industry()` relationship method
  - Ensure proper relationship to Industry model

### User Forms
- Update user registration forms to include industry selection
- Update user profile/edit forms to include industry selection
- Update admin user management forms
- Add industry dropdown with all available industries

### Views to Update
- User registration forms
- User profile/edit forms
- Admin user management forms
- User listing views (show industry information)

### Form Integration
Add industry selection dropdown to user forms:
```php
<select name="industry_id" class="form-control">
    <option value="">Select Industry</option>
    @foreach(App\Industry::orderBy('name')->get() as $industry)
        <option value="{{ $industry->id }}" 
                {{ old('industry_id', $user->industry_id) == $industry->id ? 'selected' : '' }}>
            {{ $industry->name }}
        </option>
    @endforeach
</select>
```

### Validation
- Add validation rules for industry_id
- Ensure industry_id exists in industries table
- Handle nullable industry assignments

## Acceptance Criteria
- [ ] Users table has industry_id column with proper foreign key
- [ ] User model updated with industry relationship
- [ ] Industry selection available in user registration forms
- [ ] Industry selection available in user profile/edit forms
- [ ] Admin can assign/change user industries
- [ ] User listings show industry information
- [ ] Industry validation working properly
- [ ] Null industry assignments handled gracefully
- [ ] No breaking changes to existing user functionality
- [ ] All existing user features still work

## Testing Requirements
- [ ] Unit tests for User-Industry relationship
- [ ] Feature tests for user industry assignment
- [ ] Test user registration with industry selection
- [ ] Test user profile updates with industry changes
- [ ] Test admin user management with industry assignment
- [ ] Verify industry validation rules
- [ ] Test with null industry assignments

## Dependencies
- PR 1: Industries Foundation System (must be completed first)

## Files to Create/Modify
### New Files
- `database/migrations/2024_01_01_000002_add_industry_id_to_users_table.php`

### Modified Files
- `app/User.php` (add industry relationship and fillable)
- User registration forms (add industry dropdown)
- User profile/edit forms (add industry dropdown)
- Admin user management forms (add industry dropdown)
- User listing views (display industry information)

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Code reviewed and approved
- [ ] Tests passing
- [ ] Manual testing completed
- [ ] User registration with industry works
- [ ] User profile updates with industry work
- [ ] Admin can manage user industries
- [ ] Industry information displays correctly
- [ ] No console errors or warnings
- [ ] Responsive design verified

## Notes
- This PR builds directly on PR 1
- Focus on user experience - industry selection should be intuitive
- Consider adding industry information to user exports/reports
- Ensure industry selection is optional (nullable)
- Test with existing users who don't have industry assignments
