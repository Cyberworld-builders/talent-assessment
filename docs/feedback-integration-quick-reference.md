# Feedback System Integration Quick Reference

## 🚀 Quick Start Integration

### 1. Basic Assessment Report Integration

```php
// In your ReportsController
public function generateReport($userId, $assessmentId)
{
    $user = User::findOrFail($userId);
    $assessment = Assessment::findOrFail($assessmentId);
    $scores = $this->calculateScores($userId, $assessmentId);
    
    // Get feedback library and generate feedback
    $feedbackLibrary = FeedbackLibrary::where('client_id', $user->client_id)
        ->orWhere('client_id', null)
        ->first();
    
    $feedback = [];
    if ($feedbackLibrary) {
        foreach ($scores as $dimension => $score) {
            $level = ($score >= 80) ? 'high' : (($score >= 60) ? 'medium' : 'low');
            $feedback[$dimension] = [
                'score' => $score,
                'level' => $level,
                'feedback' => $feedbackLibrary->feedback['dimensions'][$dimension][$level] ?? ''
            ];
        }
    }
    
    return view('reports.assessment', compact('user', 'assessment', 'scores', 'feedback'));
}
```

### 2. Email Feedback on Assessment Completion

```php
// In your AssignmentsController
public function completeAssessment($assignmentId)
{
    $assignment = Assignment::findOrFail($assignmentId);
    $user = Auth::user();
    
    // Calculate scores and generate feedback
    $scores = $this->calculateScores($user->id, $assignment->assessment_id);
    $feedbackLibrary = FeedbackLibrary::first();
    
    $feedback = [];
    foreach ($scores as $dimension => $score) {
        $level = ($score >= 80) ? 'high' : (($score >= 60) ? 'medium' : 'low');
        $feedback[$dimension] = [
            'score' => $score,
            'level' => $level,
            'feedback' => $feedbackLibrary->feedback['dimensions'][$dimension][$level] ?? ''
        ];
    }
    
    // Send email
    Mail::send('emails.assessment_feedback', [
        'user' => $user,
        'assessment' => $assignment->assessment,
        'feedback' => $feedback
    ], function($message) use ($user, $assignment) {
        $message->to($user->email)
                ->subject('Your Assessment Results: ' . $assignment->assessment->name);
    });
    
    return response()->json(['success' => true]);
}
```

### 3. Dashboard Feedback Widget

```php
// In your DashboardController
public function userDashboard()
{
    $user = Auth::user();
    $recentAssessments = $user->assignments()->where('completed', 1)->latest()->take(3)->get();
    
    $feedbackData = [];
    foreach ($recentAssessments as $assignment) {
        $scores = $this->calculateScores($user->id, $assignment->assessment_id);
        $feedbackLibrary = FeedbackLibrary::first();
        
        $feedback = [];
        foreach ($scores as $dimension => $score) {
            $level = ($score >= 80) ? 'high' : (($score >= 60) ? 'medium' : 'low');
            $feedback[$dimension] = [
                'score' => $score,
                'level' => $level,
                'feedback' => $feedbackLibrary->feedback['dimensions'][$dimension][$level] ?? ''
            ];
        }
        
        $feedbackData[$assignment->id] = $feedback;
    }
    
    return view('dashboard.user', compact('user', 'recentAssessments', 'feedbackData'));
}
```

## 📊 Common Integration Patterns

### Pattern 1: Simple Score-to-Feedback Mapping

```php
function getFeedbackForScore($score, $dimension, $feedbackLibrary)
{
    $level = ($score >= 80) ? 'high' : (($score >= 60) ? 'medium' : 'low');
    
    return [
        'score' => $score,
        'level' => $level,
        'feedback' => $feedbackLibrary->feedback['dimensions'][$dimension][$level] ?? '',
        'color' => $level === 'high' ? 'success' : ($level === 'medium' ? 'warning' : 'danger')
    ];
}
```

### Pattern 2: Client-Specific Feedback Selection

```php
function getFeedbackLibrary($user)
{
    // Priority: client-specific > industry-specific > general
    return FeedbackLibrary::where('client_id', $user->client_id)
        ->orWhere('name', 'like', '%' . $user->industry->name . '%')
        ->orWhere('name', 'General Assessment Feedback')
        ->orderByRaw('CASE WHEN client_id IS NOT NULL THEN 0 WHEN name LIKE "%' . $user->industry->name . '%" THEN 1 ELSE 2 END')
        ->first();
}
```

### Pattern 3: Feedback with Action Items

```php
function generateFeedbackWithActions($scores, $feedbackLibrary)
{
    $feedback = [];
    
    foreach ($scores as $dimension => $score) {
        $level = ($score >= 80) ? 'high' : (($score >= 60) ? 'medium' : 'low');
        
        $feedback[$dimension] = [
            'score' => $score,
            'level' => $level,
            'feedback' => $feedbackLibrary->feedback['dimensions'][$dimension][$level] ?? '',
            'actions' => getActionItems($level, $dimension)
        ];
    }
    
    return $feedback;
}

function getActionItems($level, $dimension)
{
    switch ($level) {
        case 'high':
            return ['Mentor others', 'Take on leadership roles', 'Continue advanced development'];
        case 'medium':
            return ['Practice regularly', 'Seek feedback', 'Take on challenging projects'];
        case 'low':
            return ['Focus on fundamentals', 'Seek training', 'Practice consistently'];
    }
}
```

## 🎯 Integration Checklist

### ✅ Assessment Reports
- [ ] Add feedback generation to report controller
- [ ] Update report view to display feedback
- [ ] Test with different performance levels
- [ ] Verify client-specific feedback selection

### ✅ Email Notifications
- [ ] Create email template
- [ ] Add feedback to email content
- [ ] Test email delivery
- [ ] Verify feedback formatting

### ✅ User Dashboard
- [ ] Add feedback widget to dashboard
- [ ] Display recent assessment feedback
- [ ] Link to full reports
- [ ] Test with multiple assessments

### ✅ API Integration
- [ ] Create feedback API endpoints
- [ ] Add authentication
- [ ] Test API responses
- [ ] Document API usage

## 🔧 Common Code Snippets

### Get Performance Level
```php
function getPerformanceLevel($score)
{
    if ($score >= 80) return 'high';
    if ($score >= 60) return 'medium';
    return 'low';
}
```

### Get Level Color
```php
function getLevelColor($level)
{
    switch ($level) {
        case 'high': return 'success';
        case 'medium': return 'warning';
        case 'low': return 'danger';
        default: return 'info';
    }
}
```

### Get Level Icon
```php
function getLevelIcon($level)
{
    switch ($level) {
        case 'high': return 'fa-star';
        case 'medium': return 'fa-star-half-o';
        case 'low': return 'fa-star-o';
        default: return 'fa-question';
    }
}
```

### Validate Feedback Library
```php
function validateFeedbackLibrary($feedbackLibrary, $dimensions)
{
    if (!$feedbackLibrary) return false;
    
    $feedbackData = $feedbackLibrary->feedback;
    foreach ($dimensions as $dimension) {
        if (!isset($feedbackData['dimensions'][$dimension])) {
            return false;
        }
    }
    
    return true;
}
```

## 🚨 Troubleshooting Quick Fixes

### Issue: Feedback Not Appearing
```php
// Check if feedback library exists
$library = FeedbackLibrary::first();
if (!$library) {
    // Run seeder or create default library
    \Artisan::call('db:seed', ['--class' => 'FeedbackLibrariesTableSeeder']);
}
```

### Issue: Dimension Names Don't Match
```php
// Ensure dimension names match exactly
$dimension = strtolower(str_replace(' ', '_', $dimensionName));
```

### Issue: JSON Decoding Errors
```php
// Check JSON structure
$feedback = json_decode($feedbackLibrary->feedback, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON error
    Log::error('Feedback JSON error: ' . json_last_error_msg());
}
```

### Issue: Performance Issues
```php
// Cache feedback libraries
$feedbackLibrary = Cache::remember('feedback_library_' . $clientId, 3600, function() use ($clientId) {
    return FeedbackLibrary::where('client_id', $clientId)->first();
});
```

## 📞 Quick Support

### Common Questions:

**Q: How do I add feedback to existing reports?**
A: Modify your report generation method to include feedback calculation and pass it to the view.

**Q: How do I create client-specific feedback?**
A: Create a new FeedbackLibrary with the client_id set to the specific client.

**Q: How do I test feedback generation?**
A: Use the test commands in the main integration guide or create a simple test script.

**Q: How do I customize feedback content?**
A: Edit the feedback libraries through the admin interface or modify the seeder.

### Emergency Commands:
```bash
# Reset feedback libraries
php artisan db:seed --class=FeedbackLibrariesTableSeeder

# Check feedback system status
php artisan tinker
>>> App\FeedbackLibrary::count()

# Test feedback generation
php artisan tinker
>>> $user = App\User::first();
>>> $library = App\FeedbackLibrary::first();
>>> $scores = ['leadership' => 85, 'communication' => 72];
>>> $level = ($scores['leadership'] >= 80) ? 'high' : 'medium';
>>> echo $library->feedback['dimensions']['leadership'][$level];
```











