# PR 1: Industries Foundation System

## Overview
Implement the core industries system that will serve as the foundation for industry-specific features including benchmarks and user industry assignments.

## Background
The talent-assessment project needs industry classification functionality to support industry-specific benchmarks and user industry assignments. This PR establishes the foundational industries system.

## Objectives
- Create industries database table and model
- Implement industry management interface for admins
- Add industry navigation to admin sidebar
- Establish industry data seeding

## Technical Requirements

### Database Changes
- Create `industries` table migration
  - `id` (increments)
  - `name` (string, unique)
  - `timestamps`

### Models
- Create `app/Industry.php` model
  - Fillable: `['name']`
  - Relationships: `benchmarks()`, `users()`

### Controllers
- Create `app/Http/Controllers/IndustriesController.php`
  - `index()` - List all industries
  - `create()` - Show create form
  - `store()` - Save new industry
  - `edit()` - Show edit form
  - `update()` - Update industry
  - `destroy()` - Delete industry

### Views
- Create `resources/views/dashboard/industries/` directory
- Copy industry views from original codebase:
  - `index.blade.php` - Industry listing
  - `create.blade.php` - Create form
  - `edit.blade.php` - Edit form
  - `show.blade.php` - Industry details

### Routes
Add to `app/Http/routes.php` in admin middleware group:
```php
Route::get('dashboard/industries', 'IndustriesController@index');
Route::get('dashboard/industries/create', 'IndustriesController@create');
Route::post('dashboard/industries', 'IndustriesController@store');
Route::get('dashboard/industries/{id}/edit', 'IndustriesController@edit');
Route::patch('dashboard/industries/{id}', 'IndustriesController@update');
Route::delete('dashboard/industries/{id}', 'IndustriesController@destroy');
```

### Navigation
- Update `resources/views/dashboard/partials/_sidebar.blade.php`
- Add Industries menu item in admin section:
```php
<li>
    <a href="{{ url('dashboard/industries') }}">
        <i class="fa-flask"></i>
        <span class="title">Industries</span>
    </a>
</li>
```

### Database Seeding
- Create `database/seeds/IndustriesTableSeeder.php`
- Add default industries: Technology, Healthcare, Finance, Education, Manufacturing, Retail, Consulting, Government, Non-Profit, Real Estate, Transportation, Energy, Media, Legal, Hospitality
- Update `DatabaseSeeder.php` to call `IndustriesTableSeeder`

## Acceptance Criteria
- [ ] Industries table created with proper schema
- [ ] Industry model with relationships implemented
- [ ] IndustriesController with full CRUD operations
- [ ] Industry management views copied and functional
- [ ] Industries menu item appears in admin sidebar
- [ ] Admin can create, edit, and delete industries
- [ ] Industry names are unique and validated
- [ ] Default industries seeded in database
- [ ] All routes accessible and functional
- [ ] No breaking changes to existing functionality

## Testing Requirements
- [ ] Unit tests for Industry model relationships
- [ ] Feature tests for industry CRUD operations
- [ ] Manual testing of all industry management functions
- [ ] Verify industry validation (unique names)
- [ ] Test industry deletion with proper error handling

## Dependencies
- None (foundational PR)

## Files to Create/Modify
### New Files
- `database/migrations/2024_01_01_000001_create_industries_table.php`
- `app/Industry.php`
- `app/Http/Controllers/IndustriesController.php`
- `database/seeds/IndustriesTableSeeder.php`
- `resources/views/dashboard/industries/index.blade.php`
- `resources/views/dashboard/industries/create.blade.php`
- `resources/views/dashboard/industries/edit.blade.php`
- `resources/views/dashboard/industries/show.blade.php`

### Modified Files
- `app/Http/routes.php` (add industry routes)
- `resources/views/dashboard/partials/_sidebar.blade.php` (add menu item)
- `database/seeds/DatabaseSeeder.php` (add seeder call)

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Code reviewed and approved
- [ ] Tests passing
- [ ] Manual testing completed
- [ ] Documentation updated
- [ ] No console errors or warnings
- [ ] Responsive design verified
- [ ] Admin permissions working correctly

## Notes
- This is a foundational PR that other features will depend on
- Focus on clean, maintainable code
- Ensure proper error handling and validation
- Follow existing code style and patterns
- Industry names should be user-friendly and professional
