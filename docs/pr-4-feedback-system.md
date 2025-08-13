# PR 4: Feedback System

## Overview
Implement feedback library system that allows admins to create and manage feedback content for assessment results, enabling personalized feedback delivery to users based on their assessment performance.

## Background
Building on the previous PRs, this PR adds a comprehensive feedback system that allows for the creation, management, and delivery of personalized feedback to users based on their assessment results and industry context.

## Objectives
- Create feedback libraries database table and model
- Implement feedback library management interface
- Add JSON-based feedback content management
- Integrate feedback with client relationships
- Add feedback navigation to admin sidebar

## Technical Requirements

### Database Changes
- Create `feedback_libraries` table migration
  - `id` (increments)
  - `client_id` (unsigned integer, nullable, foreign key to clients)
  - `name` (string)
  - `feedback` (json)
  - `timestamps`
  - Foreign key constraint to clients table

### Models
- Create `app/FeedbackLibrary.php` model
  - Fillable: `['name', 'feedback', 'client_id']`
  - Table: `feedback_libraries`
  - Relationships: `client()`
  - JSON handling for feedback attribute

### Controllers
- Create `app/Http/Controllers/FeedbackController.php`
  - `index()` - List feedback libraries
  - `create()` - Show create form
  - `store()` - Save new feedback library
  - `edit()` - Show edit form
  - `update()` - Update feedback library
  - `destroy()` - Delete feedback library

### Views
- Create `resources/views/dashboard/feedback/` directory
- Copy feedback views from original codebase:
  - `index.blade.php` - Feedback library listing
  - `create.blade.php` - Create form
  - `edit.blade.php` - Edit form
  - `show.blade.php` - Feedback library details

### Routes
Add to `app/Http/routes.php` in admin middleware group:
```php
Route::get('dashboard/feedback', 'FeedbackController@index');
Route::get('dashboard/feedback/create', 'FeedbackController@create');
Route::post('dashboard/feedback', 'FeedbackController@store');
Route::get('dashboard/feedback/{id}/edit', 'FeedbackController@edit');
Route::patch('dashboard/feedback/{id}', 'FeedbackController@update');
Route::delete('dashboard/feedback/{id}', 'FeedbackController@destroy');
```

### Navigation
- Update `resources/views/dashboard/partials/_sidebar.blade.php`
- Add Feedback menu item in admin section:
```php
<li>
    <a href="{{ url('dashboard/feedback') }}">
        <i class="fa-file-text-o"></i>
        <span class="title">Feedback</span>
    </a>
</li>
```

### Client Model Updates
- Update `app/Client.php` model
  - Add `feedbackLibraries()` relationship method
  - Ensure proper relationship to FeedbackLibrary model

### JSON Feedback Handling
- Implement JSON encoding/decoding for feedback content
- Add proper JSON validation
- Handle feedback content structure
- Support complex feedback data structures

### Integration Points
- Connect feedback libraries to clients
- Prepare for integration with assessment reports
- Support both global and client-specific feedback libraries

## Acceptance Criteria
- [ ] Feedback libraries table created with proper schema
- [ ] FeedbackLibrary model with JSON handling implemented
- [ ] FeedbackController with full CRUD operations
- [ ] Feedback management views copied and functional
- [ ] Feedback menu item appears in admin sidebar
- [ ] Admin can create, edit, and delete feedback libraries
- [ ] JSON feedback content handled properly
- [ ] Client relationships working correctly
- [ ] Feedback library names are unique and validated
- [ ] No breaking changes to existing functionality
- [ ] All routes accessible and functional

## Testing Requirements
- [ ] Unit tests for FeedbackLibrary model relationships
- [ ] Unit tests for JSON feedback handling
- [ ] Feature tests for feedback library CRUD operations
- [ ] Test JSON validation and error handling
- [ ] Test client relationship functionality
- [ ] Manual testing of all feedback management functions
- [ ] Verify feedback validation (unique names)
- [ ] Test feedback library deletion with proper error handling
- [ ] Test JSON content encoding/decoding

## Dependencies
- PR 1: Industries Foundation System (must be completed first)
- PR 2: User Industry Integration (must be completed first)
- PR 3: Benchmarks System (must be completed first)

## Files to Create/Modify
### New Files
- `database/migrations/2024_01_01_000004_create_feedback_libraries_table.php`
- `app/FeedbackLibrary.php`
- `app/Http/Controllers/FeedbackController.php`
- `resources/views/dashboard/feedback/index.blade.php`
- `resources/views/dashboard/feedback/create.blade.php`
- `resources/views/dashboard/feedback/edit.blade.php`
- `resources/views/dashboard/feedback/show.blade.php`

### Modified Files
- `app/Http/routes.php` (add feedback routes)
- `resources/views/dashboard/partials/_sidebar.blade.php` (add menu item)
- `app/Client.php` (add feedbackLibraries relationship)

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Code reviewed and approved
- [ ] Tests passing
- [ ] Manual testing completed
- [ ] JSON feedback handling verified
- [ ] Client relationships working
- [ ] No console errors or warnings
- [ ] Responsive design verified
- [ ] Admin permissions working correctly
- [ ] Error handling tested and working

## Notes
- This is the final PR in the Phase 1 feature set
- Focus on robust JSON handling for feedback content
- Consider feedback content structure and validation
- Ensure proper client relationship handling
- Test with various JSON content structures
- Prepare for future integration with assessment reports
- Feedback libraries can be global (client_id = null) or client-specific
