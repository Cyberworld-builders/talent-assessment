# Feedback System Developer Guide

## Technical Overview

The Feedback System is built on Laravel 5.x and provides a RESTful API for managing feedback libraries. This guide covers the technical implementation, database schema, and integration points for developers.

## Database Schema

### feedback_libraries Table

```sql
CREATE TABLE `feedback_libraries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `feedback` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feedback_libraries_name_unique` (`name`),
  KEY `feedback_libraries_client_id_foreign` (`client_id`),
  CONSTRAINT `feedback_libraries_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Key Fields

- **id**: Primary key, auto-incrementing
- **client_id**: Foreign key to clients table (nullable for global libraries)
- **name**: Unique library name
- **feedback**: JSON field containing structured feedback content
- **timestamps**: Standard Laravel created_at/updated_at fields

## Models

### FeedbackLibrary Model

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackLibrary extends Model
{
    protected $fillable = [
        'name',
        'feedback',
        'client_id'
    ];

    protected $table = 'feedback_libraries';

    /**
     * Get the client to which this feedback library belongs.
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * JSON encode feedback when saved in storage.
     */
    public function setFeedbackAttribute($value)
    {
        $this->attributes['feedback'] = json_encode($value);
    }

    /**
     * JSON decode feedback when retrieved from storage.
     */
    public function getFeedbackAttribute()
    {
        return json_decode($this->attributes['feedback'], true);
    }
}
```

### Client Model Relationship

```php
/**
 * Get all feedback libraries belonging to this client.
 */
public function feedbackLibraries()
{
    return $this->hasMany('App\FeedbackLibrary');
}
```

## Controllers

### FeedbackController

The controller provides full CRUD operations with JSON responses for AJAX interactions:

```php
class FeedbackController extends Controller
{
    public function index()
    {
        $libraries = FeedbackLibrary::where('client_id', null)->get();
        return view('dashboard.feedback.index', compact('libraries'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:feedback_libraries'
        ]);

        if ($validator->fails()) {
            return \Response::json(['errors' => ['Name must be unique.']]);
        }

        $library = new FeedbackLibrary([
            'name' => $request->name,
            'feedback' => $request->feedback,
        ]);

        $library->save();
        return \Response::json(['success' => 'Saved successfully!']);
    }

    // Additional CRUD methods...
}
```

## Routes

### Admin Routes

```php
// Feedback routes (admin middleware group)
Route::get('dashboard/feedback', 'FeedbackController@index');
Route::get('dashboard/feedback/create', 'FeedbackController@create');
Route::post('dashboard/feedback', 'FeedbackController@store');
Route::get('dashboard/feedback/{id}/edit', 'FeedbackController@edit');
Route::patch('dashboard/feedback/{id}', 'FeedbackController@update');
Route::delete('dashboard/feedback/{id}', 'FeedbackController@destroy');
```

## Integration Points

### 1. Assessment Report Integration

To integrate feedback into assessment reports:

```php
// In your report generation logic
public function generateReport($userId, $assessmentId)
{
    $user = User::find($userId);
    $assessment = Assessment::find($assessmentId);
    
    // Get user's performance scores
    $scores = $this->calculateScores($userId, $assessmentId);
    
    // Get appropriate feedback library
    $feedbackLibrary = $this->getFeedbackLibrary($user, $assessment);
    
    // Generate personalized feedback
    $feedback = $this->generatePersonalizedFeedback($scores, $feedbackLibrary);
    
    return view('reports.assessment', compact('user', 'assessment', 'scores', 'feedback'));
}

private function getFeedbackLibrary($user, $assessment)
{
    // Priority: client-specific library, then global library
    $library = FeedbackLibrary::where('client_id', $user->client_id)
        ->orWhere('client_id', null)
        ->first();
    
    return $library;
}

private function generatePersonalizedFeedback($scores, $feedbackLibrary)
{
    $feedback = [];
    $feedbackData = $feedbackLibrary->feedback;
    
    foreach ($scores as $dimension => $score) {
        if (isset($feedbackData['dimensions'][$dimension])) {
            $level = $this->determinePerformanceLevel($score);
            $feedback[$dimension] = $feedbackData['dimensions'][$dimension][$level] ?? '';
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
```

### 2. User Dashboard Integration

To display feedback in user dashboards:

```php
// In your dashboard controller
public function userDashboard($userId)
{
    $user = User::find($userId);
    $recentAssessments = $user->assignments()->where('completed', 1)->latest()->take(5)->get();
    
    $feedbackData = [];
    foreach ($recentAssessments as $assignment) {
        $feedback = $this->getAssessmentFeedback($user, $assignment);
        $feedbackData[$assignment->id] = $feedback;
    }
    
    return view('dashboard.user', compact('user', 'recentAssessments', 'feedbackData'));
}
```

### 3. API Integration

For external system integration:

```php
// API endpoint for getting feedback
Route::get('api/feedback/{userId}/{assessmentId}', function($userId, $assessmentId) {
    $user = User::findOrFail($userId);
    $assessment = Assessment::findOrFail($assessmentId);
    
    $feedback = app('App\Services\FeedbackService')->generateFeedback($user, $assessment);
    
    return response()->json([
        'user_id' => $userId,
        'assessment_id' => $assessmentId,
        'feedback' => $feedback,
        'generated_at' => now()
    ]);
});
```

## Service Layer

### FeedbackService

For complex feedback logic, consider creating a service class:

```php
<?php

namespace App\Services;

use App\FeedbackLibrary;
use App\User;
use App\Assessment;

class FeedbackService
{
    public function generateFeedback(User $user, Assessment $assessment)
    {
        $library = $this->getBestFeedbackLibrary($user, $assessment);
        $scores = $this->calculateDimensionScores($user, $assessment);
        
        return $this->buildPersonalizedFeedback($scores, $library);
    }
    
    private function getBestFeedbackLibrary(User $user, Assessment $assessment)
    {
        // Logic to select the most appropriate feedback library
        return FeedbackLibrary::where('client_id', $user->client_id)
            ->orWhere('client_id', null)
            ->first();
    }
    
    private function calculateDimensionScores(User $user, Assessment $assessment)
    {
        // Calculate performance scores for each dimension
        // Implementation depends on your scoring logic
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
                    'feedback' => $feedbackData['dimensions'][$dimension][$level] ?? ''
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
}
```

## Testing

### Unit Tests

```php
class FeedbackLibraryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_feedback_library()
    {
        $library = new FeedbackLibrary([
            'name' => 'Test Library',
            'feedback' => [
                'dimensions' => [
                    'leadership' => [
                        'high' => 'Excellent leadership skills',
                        'medium' => 'Good leadership potential',
                        'low' => 'Leadership development needed'
                    ]
                ]
            ]
        ]);

        $this->assertEquals('Test Library', $library->name);
        $this->assertTrue(is_array($library->feedback));
    }

    public function test_json_encoding_and_decoding()
    {
        $feedbackData = [
            'dimensions' => [
                'communication' => [
                    'high' => 'Outstanding communication skills',
                    'medium' => 'Good communication abilities',
                    'low' => 'Communication skills need improvement'
                ]
            ]
        ];

        $library = new FeedbackLibrary([
            'name' => 'JSON Test Library',
            'feedback' => $feedbackData
        ]);

        $library->save();
        $retrievedLibrary = FeedbackLibrary::find($library->id);
        
        $this->assertEquals($feedbackData, $retrievedLibrary->feedback);
    }
}
```

### Feature Tests

```php
class FeedbackControllerTest extends TestCase
{
    public function test_admin_can_create_feedback_library()
    {
        $admin = factory(User::class)->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post('/dashboard/feedback', [
                'name' => 'Test Library',
                'feedback' => [
                    'dimensions' => [
                        'leadership' => [
                            'high' => 'Excellent',
                            'medium' => 'Good',
                            'low' => 'Needs improvement'
                        ]
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('feedback_libraries', ['name' => 'Test Library']);
    }
}
```

## Performance Considerations

### Database Optimization

1. **Indexing**: The `name` field has a unique index for fast lookups
2. **JSON Queries**: MySQL 5.7+ supports JSON field queries for complex filtering
3. **Caching**: Consider caching frequently accessed feedback libraries

### Caching Strategy

```php
// Cache feedback libraries for performance
public function getFeedbackLibrary($clientId = null)
{
    $cacheKey = "feedback_library_{$clientId}";
    
    return Cache::remember($cacheKey, 3600, function() use ($clientId) {
        return FeedbackLibrary::where('client_id', $clientId)
            ->orWhere('client_id', null)
            ->first();
    });
}
```

## Security Considerations

### Input Validation

1. **JSON Validation**: Always validate JSON structure before saving
2. **XSS Prevention**: Sanitize feedback content to prevent XSS attacks
3. **Access Control**: Ensure only authorized users can manage feedback libraries

### Validation Rules

```php
$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255|unique:feedback_libraries,name,' . $id,
    'feedback' => 'required|json',
    'client_id' => 'nullable|exists:clients,id'
]);
```

## Deployment Notes

### Migration Order

Ensure migrations are run in the correct order:

1. `2024_01_01_000004_create_feedback_libraries_table.php`
2. `2025_08_13_214433_add_unique_constraint_to_feedback_libraries_name.php`

### Environment Variables

No additional environment variables are required for the feedback system.

### Dependencies

The feedback system has no additional dependencies beyond the core Laravel framework.

## Troubleshooting

### Common Issues

1. **JSON Decoding Errors**: Ensure JSON is properly formatted
2. **Unique Constraint Violations**: Check for duplicate library names
3. **Foreign Key Errors**: Verify client_id references exist
4. **Permission Errors**: Check user role and middleware configuration

### Debugging

```php
// Enable query logging for debugging
DB::enableQueryLog();
$library = FeedbackLibrary::find(1);
dd(DB::getQueryLog());

// Check JSON structure
$library = FeedbackLibrary::find(1);
dd($library->feedback);
```

## Future Enhancements

### Potential Improvements

1. **Version Control**: Add versioning to feedback libraries
2. **Templates**: Pre-built feedback templates for common scenarios
3. **Analytics**: Track feedback usage and effectiveness
4. **Multi-language**: Support for multiple languages
5. **Rich Text**: Support for formatted feedback content
6. **Conditional Logic**: Advanced conditional feedback based on multiple factors

### API Extensions

Consider adding these API endpoints for external integration:

- `GET /api/feedback-libraries` - List all libraries
- `POST /api/feedback-libraries` - Create new library
- `GET /api/feedback-libraries/{id}` - Get specific library
- `PUT /api/feedback-libraries/{id}` - Update library
- `DELETE /api/feedback-libraries/{id}` - Delete library
- `POST /api/feedback/generate` - Generate personalized feedback
