# Feedback System Integration Guide

## Overview

This guide provides detailed instructions on how to integrate the feedback system with assessment reports and other parts of the talent assessment platform. You'll learn how to deliver personalized feedback to users based on their assessment performance.

## Integration Points

### 1. Assessment Reports Integration
### 2. User Dashboard Integration  
### 3. Email Notifications
### 4. API Integration
### 5. Client-Specific Feedback

---

## 1. Assessment Reports Integration

### Overview
Integrate personalized feedback into assessment reports to provide users with actionable insights based on their performance across different dimensions.

### Step-by-Step Implementation

#### Step 1: Modify Report Generation Logic

**File**: `app/Http/Controllers/ReportsController.php`

Add feedback integration to your existing report generation method:

```php
public function generateAssessmentReport($userId, $assessmentId)
{
    $user = User::findOrFail($userId);
    $assessment = Assessment::findOrFail($assessmentId);
    
    // Get user's performance scores
    $scores = $this->calculateDimensionScores($user, $assessmentId);
    
    // Get appropriate feedback library
    $feedbackLibrary = $this->getFeedbackLibrary($user, $assessment);
    
    // Generate personalized feedback
    $personalizedFeedback = $this->generatePersonalizedFeedback($scores, $feedbackLibrary);
    
    // Prepare report data
    $reportData = [
        'user' => $user,
        'assessment' => $assessment,
        'scores' => $scores,
        'feedback' => $personalizedFeedback,
        'benchmarks' => $this->getBenchmarks($user, $assessment),
        'recommendations' => $this->generateRecommendations($personalizedFeedback)
    ];
    
    return view('reports.assessment', compact('reportData'));
}

private function getFeedbackLibrary($user, $assessment)
{
    // Priority: client-specific library, then global library
    $library = FeedbackLibrary::where('client_id', $user->client_id)
        ->orWhere('client_id', null)
        ->orderByRaw('CASE WHEN client_id IS NOT NULL THEN 0 ELSE 1 END')
        ->first();
    
    return $library;
}

private function generatePersonalizedFeedback($scores, $feedbackLibrary)
{
    if (!$feedbackLibrary) {
        return [];
    }
    
    $feedback = [];
    $feedbackData = $feedbackLibrary->feedback;
    
    foreach ($scores as $dimension => $score) {
        if (isset($feedbackData['dimensions'][$dimension])) {
            $level = $this->determinePerformanceLevel($score);
            $feedback[$dimension] = [
                'score' => $score,
                'level' => $level,
                'feedback' => $feedbackData['dimensions'][$dimension][$level] ?? '',
                'strengths' => $this->identifyStrengths($score, $level),
                'development_areas' => $this->identifyDevelopmentAreas($score, $level)
            ];
        }
    }
    
    return $feedback;
}

private function determinePerformanceLevel($score)
{
    if ($score >= 80) return 'high';
    if ($score >= 60) return 'medium';
    return 'low';
}

private function identifyStrengths($score, $level)
{
    if ($level === 'high') {
        return ['Strong performance', 'Excellent capabilities', 'Outstanding skills'];
    } elseif ($level === 'medium') {
        return ['Solid foundation', 'Good potential', 'Reasonable abilities'];
    }
    return ['Development opportunity', 'Growth potential', 'Learning mindset'];
}

private function identifyDevelopmentAreas($score, $level)
{
    if ($level === 'high') {
        return ['Advanced development', 'Leadership opportunities', 'Mentoring others'];
    } elseif ($level === 'medium') {
        return ['Skill enhancement', 'Practice and refinement', 'Further development'];
    }
    return ['Core skill building', 'Fundamental development', 'Basic training'];
}
```

#### Step 2: Create Feedback Service Class

**File**: `app/Services/FeedbackService.php`

```php
<?php

namespace App\Services;

use App\FeedbackLibrary;
use App\User;
use App\Assessment;

class FeedbackService
{
    public function generateFeedback(User $user, Assessment $assessment, $scores)
    {
        $library = $this->getBestFeedbackLibrary($user, $assessment);
        
        if (!$library) {
            return $this->generateDefaultFeedback($scores);
        }
        
        return $this->buildPersonalizedFeedback($scores, $library);
    }
    
    private function getBestFeedbackLibrary(User $user, Assessment $assessment)
    {
        // Check for client-specific library first
        $clientLibrary = FeedbackLibrary::where('client_id', $user->client_id)->first();
        
        if ($clientLibrary) {
            return $clientLibrary;
        }
        
        // Fall back to global library
        return FeedbackLibrary::where('client_id', null)->first();
    }
    
    private function buildPersonalizedFeedback($scores, FeedbackLibrary $library)
    {
        $feedback = [];
        $feedbackData = $library->feedback;
        
        foreach ($scores as $dimension => $score) {
            if (isset($feedbackData['dimensions'][$dimension])) {
                $level = $this->getPerformanceLevel($score);
                $feedback[$dimension] = [
                    'score' => $score,
                    'level' => $level,
                    'feedback' => $feedbackData['dimensions'][$dimension][$level] ?? '',
                    'color' => $this->getLevelColor($level),
                    'icon' => $this->getLevelIcon($level),
                    'action_items' => $this->generateActionItems($level, $dimension)
                ];
            }
        }
        
        return $feedback;
    }
    
    private function getPerformanceLevel($score)
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        return 'low';
    }
    
    private function getLevelColor($level)
    {
        switch ($level) {
            case 'high': return 'success';
            case 'medium': return 'warning';
            case 'low': return 'danger';
            default: return 'info';
        }
    }
    
    private function getLevelIcon($level)
    {
        switch ($level) {
            case 'high': return 'fa-star';
            case 'medium': return 'fa-star-half-o';
            case 'low': return 'fa-star-o';
            default: return 'fa-question';
        }
    }
    
    private function generateActionItems($level, $dimension)
    {
        $actions = [];
        
        switch ($level) {
            case 'high':
                $actions = [
                    'Continue developing advanced skills',
                    'Mentor others in this area',
                    'Take on leadership opportunities'
                ];
                break;
            case 'medium':
                $actions = [
                    'Practice and refine current skills',
                    'Seek feedback from experts',
                    'Take on challenging projects'
                ];
                break;
            case 'low':
                $actions = [
                    'Focus on fundamental development',
                    'Seek training and resources',
                    'Practice regularly in this area'
                ];
                break;
        }
        
        return $actions;
    }
    
    private function generateDefaultFeedback($scores)
    {
        $feedback = [];
        
        foreach ($scores as $dimension => $score) {
            $level = $this->getPerformanceLevel($score);
            $feedback[$dimension] = [
                'score' => $score,
                'level' => $level,
                'feedback' => $this->getDefaultFeedbackMessage($level),
                'color' => $this->getLevelColor($level),
                'icon' => $this->getLevelIcon($level)
            ];
        }
        
        return $feedback;
    }
    
    private function getDefaultFeedbackMessage($level)
    {
        switch ($level) {
            case 'high':
                return 'Excellent performance in this area. Continue building on your strengths.';
            case 'medium':
                return 'Good performance with room for improvement. Focus on development areas.';
            case 'low':
                return 'Development opportunity identified. Consider additional training and practice.';
            default:
                return 'Performance level not determined.';
        }
    }
}
```

#### Step 3: Update Assessment Report View

**File**: `resources/views/reports/assessment.blade.php`

Add the feedback section to your existing report template:

```php
@extends('app')

@section('title')
    Assessment Report - {{ $reportData['user']->name }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Assessment Report</h1>
            <h3>{{ $reportData['assessment']->name }}</h3>
            <p><strong>Participant:</strong> {{ $reportData['user']->name }}</p>
            <p><strong>Date:</strong> {{ now()->format('F j, Y') }}</p>
        </div>
    </div>

    <!-- Scores Overview -->
    <div class="row">
        <div class="col-md-12">
            <h2>Performance Overview</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Dimension</th>
                            <th>Score</th>
                            <th>Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['scores'] as $dimension => $score)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $dimension)) }}</td>
                            <td>{{ $score }}%</td>
                            <td>
                                <span class="label label-{{ $reportData['feedback'][$dimension]['color'] }}">
                                    {{ ucfirst($reportData['feedback'][$dimension]['level']) }}
                                </span>
                            </td>
                            <td>
                                <i class="fa {{ $reportData['feedback'][$dimension]['icon'] }}"></i>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Feedback -->
    <div class="row">
        <div class="col-md-12">
            <h2>Personalized Feedback</h2>
            
            @foreach($reportData['feedback'] as $dimension => $feedbackData)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa {{ $feedbackData['icon'] }}"></i>
                        {{ ucfirst(str_replace('_', ' ', $dimension)) }}
                        <span class="label label-{{ $feedbackData['color'] }} pull-right">
                            {{ $feedbackData['score'] }}%
                        </span>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Feedback</h4>
                            <p>{{ $feedbackData['feedback'] }}</p>
                            
                            @if(isset($feedbackData['action_items']))
                            <h4>Recommended Actions</h4>
                            <ul>
                                @foreach($feedbackData['action_items'] as $action)
                                <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h4>Performance Level</h4>
                            <div class="text-center">
                                <div class="huge">
                                    <i class="fa {{ $feedbackData['icon'] }} text-{{ $feedbackData['color'] }}"></i>
                                </div>
                                <div class="text-{{ $feedbackData['color'] }}">
                                    <strong>{{ ucfirst($feedbackData['level']) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Development Recommendations -->
    <div class="row">
        <div class="col-md-12">
            <h2>Development Recommendations</h2>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Next Steps</h3>
                </div>
                <div class="panel-body">
                    @foreach($reportData['recommendations'] as $recommendation)
                    <div class="media">
                        <div class="media-left">
                            <i class="fa fa-lightbulb-o fa-2x text-info"></i>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">{{ $recommendation['title'] }}</h4>
                            <p>{{ $recommendation['description'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Complete User Experience Walkthrough

#### For Assessment Administrators:

1. **Create Assessment**
   - Set up assessment with dimensions
   - Configure scoring criteria
   - Select appropriate feedback library

2. **Assign Assessment**
   - Assign to users or groups
   - Set completion deadlines
   - Configure notification settings

3. **Monitor Progress**
   - Track completion rates
   - Review preliminary results
   - Adjust feedback libraries if needed

#### For Assessment Participants:

1. **Take Assessment**
   - Complete questions across dimensions
   - Submit responses
   - Receive immediate confirmation

2. **Receive Report**
   - Get email notification with report link
   - Access personalized feedback
   - Review performance by dimension

3. **Review Feedback**
   - Read personalized feedback for each dimension
   - See performance levels (High/Medium/Low)
   - Review recommended actions
   - Access development resources

#### For Managers/HR:

1. **Access Team Reports**
   - View individual and team performance
   - Compare against benchmarks
   - Identify development needs

2. **Plan Development**
   - Use feedback to inform training decisions
   - Create individual development plans
   - Track progress over time

---

## 2. User Dashboard Integration

### Overview
Display personalized feedback directly in user dashboards for ongoing development tracking.

### Implementation

#### Step 1: Add Feedback to User Dashboard

**File**: `app/Http/Controllers/DashboardController.php`

```php
public function userDashboard()
{
    $user = Auth::user();
    $recentAssessments = $user->assignments()
        ->where('completed', 1)
        ->with('assessment')
        ->latest()
        ->take(5)
        ->get();
    
    $feedbackData = [];
    foreach ($recentAssessments as $assignment) {
        $scores = $this->calculateScores($user->id, $assignment->assessment_id);
        $feedbackService = app('App\Services\FeedbackService');
        $feedback = $feedbackService->generateFeedback($user, $assignment->assessment, $scores);
        
        $feedbackData[$assignment->id] = [
            'assessment' => $assignment->assessment,
            'scores' => $scores,
            'feedback' => $feedback,
            'completed_at' => $assignment->updated_at
        ];
    }
    
    return view('dashboard.user', compact('user', 'recentAssessments', 'feedbackData'));
}
```

#### Step 2: Create Dashboard Feedback Widget

**File**: `resources/views/dashboard/partials/_feedback_widget.blade.php`

```php
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="fa fa-comments"></i>
            Recent Feedback
        </h3>
    </div>
    <div class="panel-body">
        @if(count($feedbackData) > 0)
            @foreach($feedbackData as $assignmentId => $data)
            <div class="feedback-item">
                <h4>{{ $data['assessment']->name }}</h4>
                <p class="text-muted">Completed: {{ $data['completed_at']->format('M j, Y') }}</p>
                
                <div class="dimension-summary">
                    @foreach($data['feedback'] as $dimension => $feedback)
                    <div class="dimension-item">
                        <span class="dimension-name">{{ ucfirst(str_replace('_', ' ', $dimension)) }}</span>
                        <span class="label label-{{ $feedback['color'] }}">
                            {{ ucfirst($feedback['level']) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                
                <a href="{{ url('dashboard/reports/' . $assignmentId) }}" class="btn btn-sm btn-primary">
                    View Full Report
                </a>
            </div>
            @endforeach
        @else
            <p class="text-muted">No recent assessments completed.</p>
        @endif
    </div>
</div>
```

---

## 3. Email Notifications with Feedback

### Overview
Send personalized feedback via email when assessments are completed.

### Implementation

#### Step 1: Create Feedback Email Template

**File**: `resources/views/emails/assessment_feedback.blade.php`

```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Assessment Results</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; }
        .feedback-section { margin: 20px 0; padding: 15px; border-left: 4px solid #007bff; }
        .score-high { color: #28a745; }
        .score-medium { color: #ffc107; }
        .score-low { color: #dc3545; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Assessment Results</h1>
            <p>Hi {{ $user->name }},</p>
            <p>Your assessment "{{ $assessment->name }}" has been completed and analyzed. Here's your personalized feedback:</p>
        </div>

        <div class="performance-summary">
            <h2>Performance Summary</h2>
            @foreach($feedback as $dimension => $data)
            <div class="feedback-section">
                <h3>{{ ucfirst(str_replace('_', ' ', $dimension)) }}</h3>
                <p><strong>Score:</strong> <span class="score-{{ $data['level'] }}">{{ $data['score'] }}%</span></p>
                <p><strong>Level:</strong> {{ ucfirst($data['level']) }}</p>
                <p>{{ $data['feedback'] }}</p>
                
                @if(isset($data['action_items']))
                <h4>Recommended Actions:</h4>
                <ul>
                    @foreach($data['action_items'] as $action)
                    <li>{{ $action }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endforeach
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('dashboard/reports/' . $assignmentId) }}" class="btn">
                View Full Report
            </a>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <h3>Next Steps</h3>
            <p>Use this feedback to guide your professional development. Consider:</p>
            <ul>
                <li>Discussing results with your manager</li>
                <li>Creating a development plan</li>
                <li>Seeking additional training in areas for improvement</li>
                <li>Practicing skills in your daily work</li>
            </ul>
        </div>
    </div>
</body>
</html>
```

#### Step 2: Create Email Service

**File**: `app/Services/FeedbackEmailService.php`

```php
<?php

namespace App\Services;

use App\User;
use App\Assignment;
use Mail;

class FeedbackEmailService
{
    public function sendFeedbackEmail(User $user, Assignment $assignment, $feedback)
    {
        $data = [
            'user' => $user,
            'assessment' => $assignment->assessment,
            'feedback' => $feedback,
            'assignmentId' => $assignment->id
        ];
        
        Mail::send('emails.assessment_feedback', $data, function($message) use ($user, $assignment) {
            $message->to($user->email, $user->name)
                    ->subject('Your Assessment Results: ' . $assignment->assessment->name);
        });
    }
}
```

#### Step 3: Trigger Email on Assessment Completion

**File**: `app/Http/Controllers/AssignmentsController.php`

```php
public function completeAssessment(Request $request, $assignmentId)
{
    $assignment = Assignment::findOrFail($assignmentId);
    $user = Auth::user();
    
    // Mark assignment as completed
    $assignment->update(['completed' => 1]);
    
    // Calculate scores
    $scores = $this->calculateScores($user->id, $assignment->assessment_id);
    
    // Generate feedback
    $feedbackService = app('App\Services\FeedbackService');
    $feedback = $feedbackService->generateFeedback($user, $assignment->assessment, $scores);
    
    // Send feedback email
    $emailService = app('App\Services\FeedbackEmailService');
    $emailService->sendFeedbackEmail($user, $assignment, $feedback);
    
    return response()->json([
        'success' => true,
        'message' => 'Assessment completed successfully. Check your email for detailed feedback.'
    ]);
}
```

---

## 4. API Integration

### Overview
Provide API endpoints for external systems to access feedback data.

### Implementation

#### Step 1: Create API Routes

**File**: `app/Http/routes.php`

```php
// API Routes for Feedback
Route::group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function() {
    Route::get('feedback/{userId}/{assessmentId}', 'Api\FeedbackController@getFeedback');
    Route::get('feedback-libraries', 'Api\FeedbackController@getLibraries');
    Route::post('feedback/generate', 'Api\FeedbackController@generateFeedback');
});
```

#### Step 2: Create API Controller

**File**: `app/Http/Controllers/Api/FeedbackController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Assessment;
use App\FeedbackLibrary;
use App\Services\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function getFeedback($userId, $assessmentId)
    {
        $user = User::findOrFail($userId);
        $assessment = Assessment::findOrFail($assessmentId);
        
        $feedbackService = app('App\Services\FeedbackService');
        $scores = $this->calculateScores($userId, $assessmentId);
        $feedback = $feedbackService->generateFeedback($user, $assessment, $scores);
        
        return response()->json([
            'user_id' => $userId,
            'assessment_id' => $assessmentId,
            'feedback' => $feedback,
            'generated_at' => now()->toISOString()
        ]);
    }
    
    public function getLibraries()
    {
        $libraries = FeedbackLibrary::select('id', 'name', 'client_id')
            ->with('client:id,name')
            ->get();
        
        return response()->json([
            'libraries' => $libraries
        ]);
    }
    
    public function generateFeedback(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'assessment_id' => 'required|exists:assessments,id',
            'scores' => 'required|array'
        ]);
        
        $user = User::findOrFail($request->user_id);
        $assessment = Assessment::findOrFail($request->assessment_id);
        
        $feedbackService = app('App\Services\FeedbackService');
        $feedback = $feedbackService->generateFeedback($user, $assessment, $request->scores);
        
        return response()->json([
            'feedback' => $feedback,
            'generated_at' => now()->toISOString()
        ]);
    }
}
```

---

## 5. Client-Specific Feedback

### Overview
Provide different feedback libraries for different clients to ensure relevance and customization.

### Implementation

#### Step 1: Create Client-Specific Feedback Libraries

```php
// Example: Create feedback library for a specific client
$client = Client::find(1); // Technology company

FeedbackLibrary::create([
    'name' => 'TechCorp Leadership Feedback',
    'client_id' => $client->id,
    'feedback' => [
        'dimensions' => [
            'technical_leadership' => [
                'high' => 'Exceptional technical leadership demonstrated. You excel at guiding technical teams and making architectural decisions.',
                'medium' => 'Good technical leadership potential. Focus on developing your technical decision-making skills.',
                'low' => 'Technical leadership development needed. Start by building technical expertise and confidence.'
            ],
            'agile_management' => [
                'high' => 'Outstanding agile management skills. You effectively lead agile teams and drive continuous improvement.',
                'medium' => 'Solid agile management abilities. Continue developing your agile practices and team facilitation.',
                'low' => 'Agile management development needed. Focus on understanding agile principles and practices.'
            ]
        ]
    ]
]);
```

#### Step 2: Update Feedback Selection Logic

```php
private function getBestFeedbackLibrary(User $user, Assessment $assessment)
{
    // Check for client-specific library first
    if ($user->client_id) {
        $clientLibrary = FeedbackLibrary::where('client_id', $user->client_id)->first();
        if ($clientLibrary) {
            return $clientLibrary;
        }
    }
    
    // Check for industry-specific library
    if ($user->industry_id) {
        $industryLibrary = FeedbackLibrary::where('name', 'like', '%' . $user->industry->name . '%')
            ->where('client_id', null)
            ->first();
        if ($industryLibrary) {
            return $industryLibrary;
        }
    }
    
    // Fall back to general library
    return FeedbackLibrary::where('name', 'General Assessment Feedback')
        ->where('client_id', null)
        ->first();
}
```

---

## Testing the Integration

### 1. Test Assessment Report Generation

```bash
# Access assessment report
curl -X GET "http://your-domain.com/dashboard/report/1/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Test Email Delivery

```bash
# Complete an assessment to trigger email
curl -X POST "http://your-domain.com/assignment/1/complete" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Test API Endpoints

```bash
# Get feedback via API
curl -X GET "http://your-domain.com/api/v1/feedback/1/1" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Generate custom feedback
curl -X POST "http://your-domain.com/api/v1/feedback/generate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "assessment_id": 1,
    "scores": {
      "leadership": 85,
      "communication": 72,
      "problem_solving": 68
    }
  }'
```

---

## Best Practices

### 1. Performance Optimization
- Cache frequently accessed feedback libraries
- Use database indexes for fast lookups
- Implement lazy loading for large feedback content

### 2. Security Considerations
- Validate all input data
- Sanitize feedback content to prevent XSS
- Implement proper access controls

### 3. User Experience
- Provide clear, actionable feedback
- Use consistent language and tone
- Include specific development recommendations

### 4. Maintenance
- Regularly review and update feedback content
- Monitor feedback effectiveness
- Collect user feedback on feedback quality

---

## Troubleshooting

### Common Issues

1. **Feedback Not Appearing**
   - Check if feedback library exists
   - Verify dimension names match
   - Ensure JSON structure is correct

2. **Email Not Sending**
   - Check email configuration
   - Verify user email addresses
   - Review email service logs

3. **API Errors**
   - Validate authentication tokens
   - Check request format
   - Review API response codes

### Debug Commands

```bash
# Check feedback libraries
php artisan tinker
>>> App\FeedbackLibrary::all()->pluck('name');

# Test feedback generation
php artisan tinker
>>> $service = app('App\Services\FeedbackService');
>>> $user = App\User::first();
>>> $assessment = App\Assessment::first();
>>> $scores = ['leadership' => 85, 'communication' => 72];
>>> $feedback = $service->generateFeedback($user, $assessment, $scores);
>>> dd($feedback);
```

This comprehensive integration guide provides everything needed to successfully integrate the feedback system with assessment reports and other parts of your talent assessment platform.



