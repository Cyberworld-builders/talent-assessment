# Legacy PDF Download Report - Technical Documentation

## Overview

This document provides a technical deep-dive into how the PDF report download functionality works in the involved-legacy codebase (https://64.225.43.110). This system generates on-demand PDF reports from Blade templates using the DomPDF library.

## Route Structure

The PDF download functionality follows this URL pattern:
```
/dashboard/report/development/{clientId}/{assignmentId}/{userId}/download
```

**Example URL:**
```
https://64.225.43.110/dashboard/report/development/8/22/53/download
```

Where:
- `8` = Client ID
- `22` = Assignment ID  
- `53` = User ID

### Route Definition

```php:77:77:/opt/involved-legacy/app/Http/routes.php
Route::get('dashboard/report/development/{clientId}/{assignmentId}/{userId}/download', 'ReportsController@download');
```

This route is protected by the `auth` and `level:2` middleware, meaning:
1. User must be authenticated
2. User must have role level 2 or higher (above standard user)

## Controller Logic Flow

### 1. Entry Point: `download()` Method

```php:187:203:/opt/involved-legacy/app/Http/Controllers/ReportsController.php
public function download($id, $assignmentId, $userId) 
{
    $client = Client::findOrFail($id);
    $assignment = Assignment::findOrFail($assignmentId);
    $assessment = Assessment::findOrFail($assignment->assessment_id);

    // Figure out which report we're showing
    $report = $this->getDevelopmentReportName($assessment);

    if (! $report)
        throw new \Exception("No blade template specified with which to show the report.", 100);

    if (! method_exists($this, $report))
        throw new \Exception("Blade template \"".$report.".blade.php\" could not be found.", 100);

    return $this->{$report}($assignmentId, $userId, $download = true);
}
```

**Key Operations:**
1. Validates that the Client, Assignment, and Assessment exist
2. Dynamically determines which report type to generate based on the assessment name
3. Validates that the report method exists
4. Calls the specific report method with `$download = true` flag

### 2. Report Type Detection

```php:264:276:/opt/involved-legacy/app/Http/Controllers/ReportsController.php
public function getDevelopmentReportName($assessment) {
    $report = 'sixty';
    if ($assessment->name == 'Involved-Leader')
        $report = 'leader';
    if ($assessment->name == 'Involved-Me')
        $report = 'me';
    if ($assessment->name == 'Involved-Me Peak Week')
        $report = 'meppw';
    if ($assessment->name == 'Involved-Blockers')
        $report = 'blockers';

    return $report;
}
```

The system supports multiple report types:
- **sixty** - Involved-360 (360-degree feedback report) - Default
- **leader** - Involved-Leader assessment
- **me** - Involved-Me assessment  
- **meppw** - Involved-Me Peak Week
- **blockers** - Involved-Blockers assessment

### 3. Report Generation: `sixty()` Example

Let's examine the most complex report type - the 360-degree feedback report:

```php:1844:1986:/opt/involved-legacy/app/Http/Controllers/ReportsController.php
public function sixty($assignmentId, $userId, $download = false)
{
    ini_set('max_execution_time', 520);
    $assignment = Assignment::find($assignmentId);
    $s = new ScoringController();

    // Get all users in the group (only those that were rated as targets) (will need this for group scores)
    $usersInGroup = $this->getUsersInGroup($assignment);

    // Get all the scores for each user
    $groupScores = [];
    foreach ($usersInGroup as $user) {
        $groupScores[$user->id] = $this->get360ScoresForUser($assignment, $user);
    }

    // ... [Score calculation and processing logic - lines omitted for brevity] ...

    // Get the user the report is for
    $user = User::find($userId);

    // Download a PDF report
    if ($download) {
        $pdf = \PDF::setOptions(['dpi' => 100, 'defaultPaperSize' => 'a4'])->loadView('reports.360', compact('user', 'scores', 'download'));
        return $pdf->download('360 Report for ' . $user->name . '.pdf');
    }

    // View the report in HTML
    $template = '360';
    return view('reports.report', compact('user', 'scores', 'template', 'download'));
}
```

**Key Processing Steps:**

1. **Extended Execution Time**: Sets max execution to 520 seconds (8+ minutes) due to complex calculations
2. **Data Collection**: 
   - Retrieves all users in the group
   - Calculates scores for each user from 360 feedback
3. **Score Processing**:
   - Aggregates feedback from multiple raters (Self, Direct Reports, Peers, Supervisors, etc.)
   - Calculates dimension scores across multiple competencies
   - Applies industry benchmarks and norms
   - Computes group averages for comparison
   - Flags scores that fall below thresholds
4. **PDF Generation** (when `$download = true`):
   - Uses the `\PDF` facade (Laravel DomPDF wrapper)
   - Sets DPI to 100 for balanced quality/performance
   - Sets paper size to A4
   - Loads the Blade view with data
   - Returns downloadable PDF file

## PDF Library Configuration

### Composer Dependency

```json:20:20:/opt/involved-legacy/composer.json
"barryvdh/laravel-dompdf": "0.8.2",
```

The system uses **barryvdh/laravel-dompdf** version 0.8.2, which is a Laravel wrapper for the DomPDF library.

### DomPDF Configuration

```php:1:245:/opt/involved-legacy/config/dompdf.php
<?php

return array(

    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => array(
        "font_dir" => env('DOMPDF_FONT_DIR', storage_path('fonts/')),
        "font_cache" => env('DOMPDF_FONT_CACHE', storage_path('fonts/')),
        "temp_dir" => sys_get_temp_dir(),
        "chroot" => env('DOMPDF_CHROOT', realpath(base_path())),
        "enable_font_subsetting" => false,
        "pdf_backend" => "CPDF",
        "default_media_type" => "projection",
        "default_paper_size" => "letter",
        "default_font" => "serif",
        "dpi" => 300,
        "enable_php" => false,
        "enable_javascript" => true,
        "enable_remote" => true,
        "font_height_ratio" => 1.1,
        "enable_html5_parser" => false,
    ),
);
```

**Notable Settings:**
- **pdf_backend**: `"CPDF"` - Uses the bundled R&OS PDF class (not PDFLib)
- **dpi**: `300` - High quality default (overridden to 100 in controller for performance)
- **enable_remote**: `true` - Allows loading of external assets
- **enable_javascript**: `true` - Enables JS in PDFs (limited support)
- **enable_php**: `false` - Security: PHP code in templates is disabled

## Blade Template Structure

### Main Report Template

The PDF is generated from Blade templates located in `/opt/involved-legacy/resources/views/reports/`.

For the 360 report specifically: `360.blade.php`

### Template Architecture

```blade:1:100:/opt/involved-legacy/resources/views/reports/360.blade.php
{{-- Whitelabel Styles --}}
@if ($user->client->whitelabel && $user->client->id == 29)
    <link rel="stylesheet" type="text/css" href="{{ getAsset('css/angela-whitelabel.css', $download) }}">
@endif

{{-- PDF Styles --}}
@if ($download)
    <link rel="stylesheet" type="text/css" href="{{ public_path('css/pdf.css') }}">

    @if ($user->client->whitelabel && $user->client->id == 29)
        <link rel="stylesheet" type="text/css" href="{{ public_path('css/angela-pdf.css') }}">
    @endif
@endif

{{-- Cover --}}
<?php $page = 1; ?>
<div class="page-container" id="{{ $page }}">

    {{-- Cover Shapes --}}
    @if ($user->client->whitelabel && $user->client->id == 29)
        <img class="cover-shapes" src="{{ getAsset('images/angela-cover-shapes.png', $download) }}" />
    @else
        <img class="cover-shapes" src="{{ getAsset('images/report-cover-shapes.png', $download) }}" />
    @endif
    
    {{-- Container --}}
    <div class="page-wrapper">

        {{-- Header --}}
        @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

        {{-- Title --}}
        <div class="cover-title">
            <span>Involved-360</span>
            <span class="report">Report</span>
            <span class="for"><strong>for</strong> {{ $user->name }}</span>
        </div>

        {{-- Disclaimer --}}
        <div class="cover-disclaimer">
            @if ($user->client->whitelabel && $user->client->id == 29)
                <img src="{{ getAsset('images/powered-by-involved-medium-gray.png', $download) }}" />
            @else
                <img src="{{ getAsset('images/logo-tagline.png', $download) }}" />
            @endif
        </div>
    </div>
</div>
```

**Template Features:**
1. **Conditional Styling**: Different CSS for web view vs. PDF download
2. **Whitelabel Support**: Custom branding for specific clients (e.g., client ID 29)
3. **Page Structure**: Each page in a `.page-container` div with manual pagination
4. **Asset Management**: Uses `getAsset()` helper to handle file paths differently for web vs. PDF

### Asset Helper Function

```php:550:555:/opt/involved-legacy/app/Http/helpers.php
function getAsset($asset, $download) {
    if ($download)
        return public_path($asset);

    return asset($asset);
}
```

This helper ensures:
- **Web View**: Uses Laravel's `asset()` helper (returns URL)
- **PDF Generation**: Uses `public_path()` (returns absolute file system path)

This is critical because DomPDF needs absolute file paths to embed images and CSS.

## PDF Styling

### PDF-Specific CSS

```css:1:100:/opt/involved-legacy/public/css/pdf.css
@font-face {
  font-family: 'Avant Garde';
  src: url('../assets/fonts/avant_garde_book_bt-webfont.eot');
  src: url('../assets/fonts/avant_garde_book_bt-webfont.eot?#iefix') format('embedded-opentype'), 
       url('../assets/fonts/avant_garde_book_bt-webfont.woff2') format('woff2'), 
       url('../assets/fonts/avant_garde_book_bt-webfont.woff') format('woff'), 
       url('../assets/fonts/avant_garde_book_bt-webfont.ttf') format('truetype');
  font-weight: normal;
  font-style: normal;
}

@page {
  margin: 0;
}

body {
  margin: 0;
  padding: 0;
}

.page-container {
  background-color: white;
  height: 1100px;
  width: 850px;
  page-break-after: always;
  position: relative;
  color: #272842;
  overflow: hidden;
}

.page-container .page-wrapper {
  padding: 59px 73px;
  position: relative;
  height: 960px;
}
```

**CSS Considerations:**
1. **Custom Fonts**: Embeds Avant Garde font family for branded look
2. **Fixed Dimensions**: Each page is exactly 850px × 1100px
3. **Page Breaks**: Uses `page-break-after: always` for pagination
4. **Zero Margins**: `@page { margin: 0; }` removes default PDF margins

## Data Flow Summary

```
1. User clicks download button
   ↓
2. Browser requests: /dashboard/report/development/{clientId}/{assignmentId}/{userId}/download
   ↓
3. Route middleware checks authentication and authorization
   ↓
4. ReportsController@download() method:
   - Loads Client, Assignment, Assessment models
   - Determines report type (sixty, leader, me, etc.)
   ↓
5. Specific report method (e.g., sixty()):
   - Sets extended execution time
   - Queries database for all relevant assignments
   - Calculates scores across dimensions
   - Aggregates feedback from multiple raters
   - Applies benchmarks and norms
   - Flags low scores
   - Prepares data structure for view
   ↓
6. PDF Generation (when $download = true):
   - Loads Blade template (e.g., reports.360)
   - Passes compiled data (user, scores, download flag)
   - DomPDF renders HTML/CSS to PDF
   - Sets options (DPI, paper size)
   ↓
7. Browser receives PDF file download:
   - Content-Type: application/pdf
   - Filename: "360 Report for [User Name].pdf"
```

## Score Calculation Details

The 360 report specifically performs complex calculations:

### Data Collection Process

```php:2000:2029:/opt/involved-legacy/app/Http/Controllers/ReportsController.php
public function get360ScoresForUser($assignment, $user) 
{
    // Find all completed assignments that pertain to this specific leader
    $assignments = Assignment::where([
        'created_at' => $assignment->created_at,
        'assessment_id' => $assignment->assessment_id,
        'completed' => 1
    ])->get()->filter(function($assignment) use ($user)
    {
        // Filter these to make sure that this assignment was rating this specific user
        foreach ($assignment->custom_fields['type'] as $i => $field)
        {
            if ($assignment->target_id == $user->id)
                return true;

            if ($field == 'name')
            {
                $name = $assignment->custom_fields['value'][$i];
                if ($name == $user->name)
                    return true;
            }
            if ($field == 'email')
            {
                $email = $assignment->custom_fields['value'][$i];
                if ($email == $user->email)
                    return true;
            }
        }
        return false;
    });
```

**Process:**
1. Finds all completed assignments from the same batch (same `created_at`)
2. Filters to only those where the current user was the target (person being rated)
3. Matches by target_id, name, or email
4. Collects answers grouped by dimension and rater relationship

### Dimension Scoring

The system calculates scores for 9 leadership competencies:
1. Creative Problem Solving
2. Leadership Adaptability
3. Collaboration
4. Self-Development
5. Business Mindset
6. Performance Management
7. Customer Focus
8. Communication
9. Ethics & Integrity

For each dimension, it computes:
- **Individual category scores** (Self, Direct Reports, Peers, Supervisors, etc.)
- **All Raters aggregate score**
- **Industry benchmark comparison**
- **Group average comparison**
- **Flagged status** (if below threshold)
- **Percentage representation** (for bar charts)
- **Qualitative feedback** (open-ended responses)

## Performance Considerations

1. **Execution Time**: Set to 520 seconds (8.6 minutes) for complex reports
2. **Memory**: Large dataset calculations for group scores
3. **DPI Setting**: Controller overrides default 300 DPI to 100 DPI for faster rendering
4. **Caching Opportunity**: Comments suggest potential for JSON caching of scores

```php:1859:1861:/opt/involved-legacy/app/Http/Controllers/ReportsController.php
// Store scores into a JSON file and pull from the JSON file, not to have to re-calculate everything
//dd(json_encode($groupScores)); // Use this to get scores and save them to /var/www/public/uploads/scores.json
//$groupScores = $this->getStoredScores(); // Use this to pull scores from that file
```

## Security Considerations

1. **Authentication**: Route protected by `auth` middleware
2. **Authorization**: `level:2` middleware ensures only managers+ can download
3. **Model Validation**: Uses `findOrFail()` to prevent invalid ID access
4. **No Direct User Input**: All parameters validated against database
5. **PHP Execution Disabled**: DomPDF config has `enable_php => false`

## Alternative Report Formats

The system supports both:
1. **PDF Download**: `$download = true` triggers PDF generation
2. **HTML Web View**: `$download = false` returns HTML view

The same calculation logic is used for both formats, ensuring consistency.

## Potential Migration Considerations

When implementing similar functionality in the talent-assessment codebase:

1. **Library Choice**: 
   - barryvdh/laravel-dompdf is Laravel 5.1 compatible
   - Consider same library or alternatives (wkhtmltopdf, Puppeteer/Chromium)

2. **Performance**: 
   - Consider implementing score caching
   - Use queued jobs for large reports
   - Implement progress indicators

3. **Asset Management**:
   - Ensure proper path handling for PDF generation
   - Store fonts in accessible location
   - Use absolute paths for images in PDF context

4. **Styling**:
   - Create PDF-specific CSS
   - Test pagination carefully
   - Consider responsive dimensions

5. **Scalability**:
   - Implement background job processing
   - Add progress tracking
   - Consider CDN for generated PDFs

## Related Files Reference

### Controllers
- `/opt/involved-legacy/app/Http/Controllers/ReportsController.php` - Main report generation logic
- `/opt/involved-legacy/app/Http/Controllers/ScoringController.php` - Score calculation utilities

### Routes
- `/opt/involved-legacy/app/Http/routes.php` - Route definitions

### Views
- `/opt/involved-legacy/resources/views/reports/360.blade.php` - 360 report template
- `/opt/involved-legacy/resources/views/reports/leader.blade.php` - Leader report template
- `/opt/involved-legacy/resources/views/reports/me.blade.php` - Me report template
- `/opt/involved-legacy/resources/views/reports/partials/_header.blade.php` - Report header partial
- `/opt/involved-legacy/resources/views/reports/partials/_footer.blade.php` - Report footer partial

### Configuration
- `/opt/involved-legacy/config/dompdf.php` - DomPDF configuration

### Assets
- `/opt/involved-legacy/public/css/pdf.css` - PDF-specific styles
- `/opt/involved-legacy/public/css/angela-pdf.css` - Whitelabel PDF styles
- `/opt/involved-legacy/public/assets/fonts/` - Custom font files
- `/opt/involved-legacy/public/assets/images/` - Report images and logos

### Helpers
- `/opt/involved-legacy/app/Http/helpers.php` - Contains `getAsset()` helper function

### Models
- `/opt/involved-legacy/app/Report.php` - Report model
- `/opt/involved-legacy/app/Assignment.php` - Assignment model
- `/opt/involved-legacy/app/User.php` - User model
- `/opt/involved-legacy/app/Assessment.php` - Assessment model
- `/opt/involved-legacy/app/Client.php` - Client model

---

**Document Version:** 1.0  
**Last Updated:** October 30, 2025  
**Author:** AI Technical Documentation  
**Related Legacy URL:** https://64.225.43.110/dashboard/report/development/8/22/53/download

