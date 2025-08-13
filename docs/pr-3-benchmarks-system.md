# PR 3: Benchmarks System

## Overview
Implement industry-specific benchmark scoring system that allows admins to set and manage benchmark values for different assessment dimensions across industries.

## Background
Building on the industries and user industry integration from PRs 1 and 2, this PR adds the ability to set industry-specific benchmarks for assessment dimensions, enabling more accurate scoring and reporting based on industry standards.

## Objectives
- Create benchmarks database table and model
- Implement benchmark management interface
- Add Excel upload functionality for bulk benchmark data
- Integrate benchmarks with assessment scoring
- Add benchmark navigation to admin sidebar

## Technical Requirements

### Database Changes
- Create `benchmarks` table migration
  - `id` (increments)
  - `dimension_id` (unsigned integer, foreign key to dimensions)
  - `industry_id` (unsigned integer, foreign key to industries)
  - `value` (text)
  - `timestamps`
  - Foreign key constraints with cascade delete

### Models
- Create `app/Benchmark.php` model
  - Fillable: `['industry_id', 'dimension_id', 'value']`
  - Relationships: `industry()`, `dimension()`

### Controllers
- Create `app/Http/Controllers/BenchmarksController.php`
  - `selectAssessment()` - Choose assessment for benchmarks
  - `selectIndustry()` - Choose industry for benchmarks
  - `index()` - Display benchmarks for assessment/industry
  - `store()` - Save benchmark data
  - `upload()` - Handle Excel file uploads

### Views
- Create `resources/views/dashboard/benchmarks/` directory
- Copy benchmark views from original codebase:
  - `index.blade.php` - Benchmark management interface
  - Excel upload interface
  - Assessment/industry selection interface

### Routes
Add to `app/Http/routes.php` in admin middleware group:
```php
Route::get('dashboard/benchmarks', 'BenchmarksController@selectAssessment');
Route::get('dashboard/benchmarks/{assessmentId}', 'BenchmarksController@selectIndustry');
Route::get('dashboard/benchmarks/{assessmentId}/{industryId}', 'BenchmarksController@index');
Route::post('dashboard/benchmarks', 'BenchmarksController@store');
Route::post('dashboard/benchmarks/{assessmentId}/upload', 'BenchmarksController@upload');
```

### Navigation
- Update `resources/views/dashboard/partials/_sidebar.blade.php`
- Add Benchmarks menu item in admin section:
```php
<li>
    <a href="{{ url('dashboard/benchmarks') }}">
        <i class="fa-signal"></i>
        <span class="title">Benchmarks</span>
    </a>
</li>
```

### Excel Upload Functionality
- Implement Excel file processing for bulk benchmark data
- Support .xls and .xlsx file formats
- Validate dimension and industry names from Excel
- Handle errors gracefully with user feedback
- Support both creation and updates of benchmark data

### Integration with Assessment Scoring
- Update assessment scoring logic to include benchmark comparisons
- Add benchmark lookup functionality
- Implement benchmark-based scoring calculations

## Acceptance Criteria
- [ ] Benchmarks table created with proper schema and constraints
- [ ] Benchmark model with relationships implemented
- [ ] BenchmarksController with all required methods
- [ ] Benchmark management views copied and functional
- [ ] Benchmarks menu item appears in admin sidebar
- [ ] Admin can select assessment and industry for benchmarks
- [ ] Admin can manually enter benchmark values
- [ ] Excel upload functionality works correctly
- [ ] Benchmark validation and error handling working
- [ ] Benchmark data integrates with assessment scoring
- [ ] No breaking changes to existing functionality

## Testing Requirements
- [ ] Unit tests for Benchmark model relationships
- [ ] Feature tests for benchmark CRUD operations
- [ ] Test Excel upload functionality
- [ ] Test benchmark validation rules
- [ ] Test benchmark integration with assessment scoring
- [ ] Manual testing of all benchmark management functions
- [ ] Test error handling for invalid Excel files
- [ ] Test benchmark calculations with sample data

## Dependencies
- PR 1: Industries Foundation System (must be completed first)
- PR 2: User Industry Integration (must be completed first)

## Dependencies to Install
Add to `composer.json`:
```json
{
    "require": {
        "maatwebsite/excel": "^2.1"
    }
}
```

## Files to Create/Modify
### New Files
- `database/migrations/2024_01_01_000003_create_benchmarks_table.php`
- `app/Benchmark.php`
- `app/Http/Controllers/BenchmarksController.php`
- `resources/views/dashboard/benchmarks/index.blade.php`

### Modified Files
- `app/Http/routes.php` (add benchmark routes)
- `resources/views/dashboard/partials/_sidebar.blade.php` (add menu item)
- Assessment scoring logic (integrate benchmarks)
- `composer.json` (add Excel dependency)

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Code reviewed and approved
- [ ] Tests passing
- [ ] Manual testing completed
- [ ] Excel upload functionality tested
- [ ] Benchmark calculations verified
- [ ] No console errors or warnings
- [ ] Responsive design verified
- [ ] Admin permissions working correctly
- [ ] Error handling tested and working

## Notes
- This is a complex feature with Excel uploads - thorough testing required
- Focus on user experience for benchmark data entry
- Ensure proper validation of Excel data
- Consider performance implications of benchmark lookups
- Test with various Excel file formats and data structures
- Benchmark values should be validated for appropriate ranges
