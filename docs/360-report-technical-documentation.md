# 360 Report Technical Documentation

## Overview

The 360 Report is a developmental performance management tool that collects feedback from multiple sources (self, direct reports, peers, supervisors, and others) to provide comprehensive performance feedback across six core competencies. This document explains the technical implementation of how these reports are generated in the Involved Talent assessment platform.

## Report Structure

A typical 360 Report includes:
- **Cover Page**: Contains the user's name, overview text, and branding
- **Competency Pages**: One page per dimension showing scores and behavioral anchors
- **Feedback Pages**: Written feedback organized by relationship category
- **Multi-source Scoring**: Separate scores for Self, Direct Report, Supervisor, Peer, and Others

## Technical Architecture

### 1. Entry Point and Routing

**Controller**: `ReportsController`  
**Method**: `indexDevelopment($id, $assignmentId, $userId)`  
**Location**: `app/Http/Controllers/ReportsController.php:163`

The entry point determines which report template to use based on the assessment type:

```php
$reports = [
    (int)get_global('leader') => 'cacique',
    (int)get_global('leader-s') => 'ls',
    (int)get_global('360') => 'sixty',
    (int)get_global('leader-sr') => 'lsr',
];
$report = $reports[$assignment->assessment_id];
```

For 360 assessments, the system calls the `sixty()` method (or `sixtyctca()` for CTCA-specific clients).

### 2. Core Report Generation Method

**Method**: `ReportsController::sixty($assignmentId, $userId)`  
**Location**: `app/Http/Controllers/ReportsController.php:1888`

#### 2.1 Performance Optimization

```php
ini_set('max_execution_time', 520);
```

The method sets an extended execution time limit because:
- It processes multiple assignments
- Calculates scores across multiple dimensions
- Aggregates data from numerous raters

#### 2.2 Assignment Filtering

The method retrieves all completed assignments that rate the target user:

```php
$assignments = Assignment::where([
    'created_at' => $assignment->created_at,
    'completed' => 1
])->get()->filter(function($assignment) use ($user) {
    // Filter assignments rating this specific user
    foreach ($assignment->custom_fields['type'] as $i => $field) {
        if ($assignment->target_id == $user->id)
            return true;
        
        if ($field == 'name' && $assignment->custom_fields['value'][$i] == $user->name)
            return true;
        
        if ($field == 'email' && $assignment->custom_fields['value'][$i] == $user->email)
            return true;
    }
    return false;
});
```

**Key Points**:
- Uses `created_at` to group assignments from the same survey cycle
- Filters only completed assignments (`completed = 1`)
- Matches by `target_id`, name, or email from custom fields
- Custom fields store target information as serialized arrays

### 3. Performance Dimensions

The report evaluates six core competencies, each with behavioral anchors at three performance levels (1, 3, and 5):

```php
$dimensions = [
    'Creative Problem Solving' => [...],
    'Leadership Adaptability' => [...],
    'Collaboration' => [...],
    'Self-Development' => [...],
    'Business Mindset' => [...],
    'Performance Management' => [...]
];
```

Each dimension contains:
- **Answers**: Array to store responses grouped by rater relationship
- **Definition**: Text description of the competency
- **Expectations**: Behavioral anchors at three levels:
  - **Level 1**: Below Expectations (multiple example behaviors)
  - **Level 3**: Meets Expectations (multiple example behaviors)
  - **Level 5**: Exceeds Expectations (multiple example behaviors)

### 4. Answer Collection and Categorization

#### 4.1 Grouping by Dimension

```php
foreach ($assignments as $assignment) {
    foreach ($dimensions as $dimensionName => $dimension) {
        $answers = $assignment->answers->filter(function($answer) use ($dimensionName) {
            $question = Question::find($answer->question_id);
            
            if ($question->dimension()->name == $dimensionName) {
                $answer->question_type = $question->type;
                return true;
            }
        });
        
        // Get relation of user to the target
        $relationToTarget = $assignment->user->getUserTargetRelation($assignment->target);
        
        // Sort answers by relationship category
        if (!array_key_exists($relationToTarget, $dimensions[$dimensionName]['Answers']))
            $dimensions[$dimensionName]['Answers'][$relationToTarget] = [];
        
        array_push($dimensions[$dimensionName]['Answers'][$relationToTarget], $answers);
    }
}
```

**Process Flow**:
1. Iterate through each completed assignment
2. For each dimension, filter answers related to that dimension
3. Determine the rater's relationship to the target (`getUserTargetRelation()`)
4. Group answers by relationship category (Self, Direct Report, Supervisor, Peer, etc.)

#### 4.2 Relationship Categorization

**Method**: `User::getUserTargetRelation($target)`  
**Location**: `app/User.php:456`

The method determines the relationship between the rater and the target:

```php
public function getUserTargetRelation($target) {
    // Check if rating self
    if ($this->id == $target->id)
        return 'Self';
    
    // Find the group where target is the subject
    $group = $this->groups()->filter(function($group) use ($target) {
        return $group->target_id == $target->id;
    })->first();
    
    if ($group) {
        $groupUsers = $group->users;
        // Determine relationship based on position in group
        // Returns: 'Direct Report', 'Supervisor', 'Peer', etc.
    }
    
    return 'Subordinate'; // Default
}
```

### 5. Score Calculation

#### 5.1 Rating Scale

The system uses a 0-4 point scale internally, converted to 1-5 for display:
- Answer values stored: 0, 1, 2, 3, 4
- Display scores: Add 1 to get 1-5 scale

#### 5.2 Category Scores

For each dimension and relationship category:

```php
foreach ($dimensionData['Answers'] as $categoryName => $answersCollection) {
    $score = 0;
    $count = 0;
    
    foreach ($answersCollection as $answers) {
        foreach ($answers as $answer) {
            if ($answer->question_type != 1) // Skip non-rating questions
                continue;
            
            $score += $answer->value;
            $count++;
        }
    }
    
    // Calculate average and convert to 1-5 scale
    $scores[$dimensionName]['Score'][$categoryName] = 0;
    if ($count)
        $scores[$dimensionName]['Score'][$categoryName] = ($score / $count) + 1;
    
    $totalScore += $score;
    $totalCount += $count;
}
```

**Calculation Steps**:
1. Sum all rating answers for the category
2. Count total number of ratings
3. Calculate average: `score / count`
4. Convert to 1-5 scale: `average + 1`

#### 5.3 Total Score

```php
$scores[$dimensionName]['Score']['Total'] = ($totalScore / $totalCount) + 1;
```

The total score is the average across all raters for that dimension.

### 6. Feedback Collection

#### 6.1 Open-Ended Responses

```php
foreach ($dimensionData['Answers'] as $categoryName => $answersCollection) {
    $responses = [];
    
    foreach ($answersCollection as $answers) {
        foreach ($answers as $answer) {
            // Question type 3 = free-text response
            if ($answer->question_type != 3 || !$answer->value)
                continue;
            
            array_push($responses, $answer->value);
        }
    }
    
    $scores[$dimensionName]['Feedback'][$categoryName] = $responses;
}
```

**Question Types**:
- **Type 1**: Rating scale (numeric)
- **Type 3**: Free-text feedback

#### 6.2 Feedback Reorganization

The system consolidates feedback into three main categories:

```php
foreach ($scores as $dimension => $data) {
    $otherFeedback = [];
    
    foreach ($data['Feedback'] as $category => $feedback) {
        // Keep Self and Direct Report separate
        if ($category == 'Self' || $category == 'Direct Report')
            continue;
        
        // Consolidate all other categories
        foreach ($scores[$dimension]['Feedback'][$category] as $response)
            array_push($otherFeedback, $response);
        
        unset($scores[$dimension]['Feedback'][$category]);
    }
    
    $scores[$dimension]['Feedback']['Others'] = $otherFeedback;
}
```

**Final Categories**:
- **Self**: The target's self-assessment
- **Direct Report**: Feedback from subordinates
- **Others**: Combined feedback from supervisors, peers, and other raters

### 7. Data Structure

The final `$scores` array passed to the view:

```php
$scores = [
    'Creative Problem Solving' => [
        'Score' => [
            'Self' => 3.45,
            'Direct Report' => 4.12,
            'Supervisor' => 3.89,
            'Peer' => 3.67,
            'Total' => 3.78
        ],
        'Feedback' => [
            'Self' => ['I believe I...'],
            'Direct Report' => ['Grace consistently...', 'She demonstrates...'],
            'Others' => ['Very collaborative...', 'Good problem solver...']
        ],
        'Definition' => 'Creative Problem Solving is defined as...',
        'Expectations' => [
            '1' => ['Behavior 1', 'Behavior 2', ...],
            '3' => ['Behavior 1', 'Behavior 2', ...],
            '5' => ['Behavior 1', 'Behavior 2', ...]
        ]
    ],
    // ... other dimensions
]
```

### 8. View Rendering

**Template**: `resources/views/reports/360.blade.php`

#### 8.1 Dynamic CSS Generation

The blade template generates CSS dynamically for score bar positioning:

```php
@foreach ($scores as $dimensionName => $dimension)
    #score-360 .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }} { 
        left: {{ ($dimension['Score']['Total'] - 1) * 25 }}%; 
    }
    #score-360 .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }}:before { 
        content: "{{ number_format($dimension['Score']['Total'], 2) }}"; 
    }
@endforeach
```

**Formula**: `left = (score - 1) * 25%`
- Score 1.0 → 0% (left edge)
- Score 3.0 → 50% (middle)
- Score 5.0 → 100% (right edge)

#### 8.2 Report Pagination

Each dimension gets its own page:

```php
@foreach ($scores as $dimensionName => $dimension)
    <div class="page-container" id="2">
        <!-- Dimension score visualization -->
        <div id="score-360">
            <img class="score-bar-background" src="/assets/images/360-gradient.jpg" />
            <div class="score-bar {{ strtolower(str_replace(' ', '-', $dimensionName)) }}"></div>
        </div>
        
        <!-- Behavioral anchors -->
        <div class="col-xs-4 expectations">
            <h3>1) Below Expectations</h3>
            <?php shuffle($dimension['Expectations']['1']) ?>
            <small>{{ $dimension['Expectations']['1'][0] }}</small>
        </div>
        
        <!-- Category subscores -->
        @foreach ($dimension['Score'] as $category => $score)
            <div id="score-360-sm">
                <!-- Subscore visualization -->
            </div>
        @endforeach
    </div>
    
    <!-- Feedback page if feedback exists -->
    @if ($feedback)
        <div class="page-container" id="3">
            @foreach ($dimension['Feedback'] as $feedbackCategory => $feedbacks)
                <h4>{{ $feedbackCategory }}</h4>
                @foreach ($feedbacks as $feedback)
                    <p>{{ $num }}) {{ $feedback }}</p>
                @endforeach
            @endforeach
        </div>
    @endif
@endforeach
```

#### 8.3 Randomized Behavioral Anchors

To prevent repetition, the system randomly selects one behavioral example from each level:

```php
<?php shuffle($dimension['Expectations']['1']) ?>
<small>{{ $dimension['Expectations']['1'][0] }}</small>
```

This ensures variety when the same dimension appears on multiple pages or reports.

## Database Schema

### Key Tables

#### assignments
- `id`: Primary key
- `user_id`: The person completing the assessment
- `assessment_id`: Type of assessment (360, leader, etc.)
- `target_id`: The person being rated
- `completed`: Boolean flag
- `custom_fields`: Serialized array with target info
- `created_at`: Survey cycle timestamp

#### answers
- `id`: Primary key
- `assignment_id`: Foreign key to assignments
- `question_id`: Foreign key to questions
- `value`: Answer value (numeric or text)

#### questions
- `id`: Primary key
- `dimension_id`: Foreign key to dimensions
- `type`: Question type (1=rating, 3=text)

#### dimensions
- `id`: Primary key
- `name`: Dimension name
- `definition`: Text description

#### users
- `id`: Primary key
- `name`: User name
- `email`: User email

#### groups
- `id`: Primary key
- `target_id`: The subject of the group
- User pivot table tracks group membership and roles

## Custom Fields Structure

Assignments for 360 assessments store target information:

```php
$custom_fields = [
    'type' => ['name', 'email', 'role'],
    'value' => ['Grace Guo', 'grace@example.com', 'Peer']
];
```

This allows the system to:
1. Identify which assignments rate the same target
2. Categorize raters by their relationship
3. Handle cases where target isn't a registered user

## Client-Specific Variations

### CTCA-Specific Report

**Method**: `sixtyctca($assignmentId, $userId)`  
**Location**: `app/Http/Controllers/ReportsController.php:2193`

Some clients have customized dimension definitions and behavioral anchors:

```php
// In indexDevelopment()
if ($report == 'sixty' && $client->id == 22)
    $report = 'sixtyctca';
```

The `sixtyctca` method uses identical logic but with CTCA-specific:
- Dimension definitions
- Behavioral anchor descriptions
- Branding elements

## Performance Considerations

### Execution Time
- Default PHP max execution: 30 seconds
- 360 reports: 520 seconds (8.7 minutes)
- Reason: Processing 10-30 assignments with 6 dimensions each

### Optimization Opportunities
1. **Caching**: Store calculated scores in `report_data` table
2. **Eager Loading**: Preload relationships to reduce queries
3. **Indexing**: Add indexes on `created_at` and `target_id`
4. **Queue Processing**: Generate reports asynchronously

### Current Caching Implementation

Some report methods check for cached data:

```php
$reportData = DB::table('report_data')->where([
    'user_id' => $userId,
    'assignment_id' => $assignmentId
])->value('score');

if ($reportData) {
    $data = json_decode($reportData, true);
    $scores = $data['scores'];
    // Use cached data
} else {
    // Calculate scores
    // Store in report_data table
}
```

**Note**: The standard `sixty()` method doesn't implement caching, but other report types (like `lsr()`) do.

## PDF Generation

### Download Functionality

**Method**: `download($clientId, $jobId, $userId)`  
**Location**: `app/Http/Controllers/ReportsController.php:4644`

```php
public function download($clientId, $jobId, $userId) {
    $user = User::findOrFail($userId);
    $job = Job::findOrFail($jobId);
    
    $headers = ['Content-Type: application/pdf'];
    $filename = "Report for " . $user->name . " - " . $job->name . ".pdf";
    $dir = $_SERVER['DOCUMENT_ROOT'].'/../storage/exports';
    
    // Initialize PDF generator
    $pdf = new PDF($_SERVER['DOCUMENT_ROOT'].'/../wkhtmltox/bin/wkhtmltopdf');
    
    // Generate HTML
    $reportsController = new ReportsController();
    $html = $reportsController->index($clientId, $jobId, $userId, true)->render();
    
    // Convert to PDF
    $pdf->loadHTML($html)->save($filename, new Local($dir), true);
    
    return response()->download($dir.'/'.$filename, $filename, $headers);
}
```

**PDF Library**: wkhtmltopdf
- Converts HTML/CSS to PDF
- Maintains visual fidelity
- Handles multi-page layouts

## Error Handling

### Missing Report Template

```php
if (!$report)
    return view('error', ['message' => "A report template could not be found..."]);

if (!method_exists($this, $report))
    return view('error', ['message' => "Looks like the method has not been configured..."]);
```

### Data Validation
- User must exist: `User::findOrFail($userId)`
- Assignment must exist: `Assignment::find($assignmentId)`
- At least one completed assignment required
- Questions must be linked to dimensions

## API Endpoints

Based on routing conventions:

```
GET /development/{clientId}/assignments/{assignmentId}/users/{userId}
    → ReportsController@indexDevelopment
```

This endpoint:
1. Determines report type from assignment
2. Calls appropriate report generation method
3. Returns rendered HTML view

## Testing Considerations

### Test Data Requirements
1. **Users**: Multiple users in different roles
2. **Assignments**: Completed assignments with `target_id` set
3. **Answers**: Mix of rating (type 1) and text (type 3) responses
4. **Groups**: Users organized with target relationships
5. **Questions**: Linked to appropriate dimensions

### Test Scenarios
1. Self-assessment only
2. Multiple raters from different categories
3. Missing feedback (scores only)
4. Client-specific variations (CTCA)
5. Large datasets (performance testing)

## Future Enhancements

### Potential Improvements
1. **Real-time Reports**: Generate on-the-fly instead of static HTML
2. **Interactive Charts**: Use JavaScript charting libraries
3. **Comparative Analytics**: Show trends over multiple survey cycles
4. **Custom Dimensions**: Allow clients to define their own competencies
5. **Multi-language Support**: Translate reports based on user preferences
6. **API Access**: RESTful endpoints for programmatic report access

### Technical Debt
1. Hardcoded dimensions in controller (should be database-driven)
2. No automated caching strategy
3. Limited error recovery
4. Tight coupling between scoring and rendering
5. Inconsistent naming (`sixty` instead of `three_sixty`)

## Conclusion

The 360 Report generation system is a sophisticated multi-source feedback tool that:
- Aggregates data from multiple raters
- Categorizes feedback by relationship
- Calculates nuanced scores across competencies
- Presents results in a professional, actionable format

The architecture separates data collection, score calculation, and presentation layers effectively, making it maintainable despite complexity. The main opportunities for improvement lie in caching, asynchronous processing, and reducing hardcoded configuration.

