# Phase 1 Implementation Plan: Industries, Benchmarks, and Feedback

## Overview

This document outlines the implementation plan for adding the three core features from the Involved Talent codebase to the talent-assessment project:

1. **Industries** - Industry classification system with user association
2. **Benchmarks** - Industry-specific benchmark scoring system  
3. **Feedback** - Feedback library system for assessment results

## Current State Analysis

### Missing Models in talent-assessment:
- `Industry.php` - Industry classification model
- `Benchmark.php` - Benchmark scoring model  
- `FeedbackLibrary.php` - Feedback library model

### Missing Controllers in talent-assessment:
- `IndustriesController.php` - Industry management
- `BenchmarksController.php` - Benchmark management
- `FeedbackController.php` - Feedback library management

### Missing Database Tables:
- `industries` - Industry classifications
- `benchmarks` - Industry-specific benchmarks
- `feedback_libraries` - Feedback library content
- `users.industry_id` - User industry association

### Missing UI Components:
- Industries tab in admin sidebar
- Benchmarks tab in admin sidebar  
- Feedback tab in admin sidebar
- Industry selection in user registration/profile

## Implementation Plan

### 1. Database Migrations

#### 1.1 Create Industries Table
```php
// database/migrations/2024_01_01_000001_create_industries_table.php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndustriesTable extends Migration
{
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('industries');
    }
}
```

#### 1.2 Create Benchmarks Table
```php
// database/migrations/2024_01_01_000002_create_benchmarks_table.php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBenchmarksTable extends Migration
{
    public function up()
    {
        Schema::create('benchmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dimension_id')->unsigned();
            $table->integer('industry_id')->unsigned();
            $table->text('value');
            $table->timestamps();

            $table->foreign('dimension_id')
                ->references('id')
                ->on('dimensions')
                ->onDelete('cascade');

            $table->foreign('industry_id')
                ->references('id')
                ->on('industries')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('benchmarks');
    }
}
```

#### 1.3 Create Feedback Libraries Table
```php
// database/migrations/2024_01_01_000003_create_feedback_libraries_table.php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackLibrariesTable extends Migration
{
    public function up()
    {
        Schema::create('feedback_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients');

            $table->string('name');
            $table->json('feedback');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('feedback_libraries', function (Blueprint $table) {
            $table->dropForeign('feedback_libraries_client_id_foreign');
        });
        Schema::drop('feedback_libraries');
    }
}
```

#### 1.4 Add Industry ID to Users Table
```php
// database/migrations/2024_01_01_000004_add_industry_id_to_users_table.php
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndustryIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('industry_id')->unsigned()->nullable();

            $table->foreign('industry_id')
                ->references('id')
                ->on('industries');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_industry_id_foreign');
            $table->dropColumn('industry_id');
        });
    }
}
```

### 2. Models

#### 2.1 Industry Model
```php
// app/Industry.php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all benchmarks for this industry.
     */
    public function benchmarks()
    {
        return $this->hasMany('App\Benchmark');
    }

    /**
     * Get all users in this industry.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
```

#### 2.2 Benchmark Model
```php
// app/Benchmark.php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    protected $fillable = ['industry_id', 'dimension_id', 'value'];

    /**
     * Get the industry to which this benchmark belongs.
     */
    public function industry()
    {
        return $this->belongsTo('App\Industry');
    }

    /**
     * Get the dimension to which this benchmark belongs.
     */
    public function dimension()
    {
        return $this->belongsTo('App\Dimension');
    }
}
```

#### 2.3 FeedbackLibrary Model
```php
// app/FeedbackLibrary.php
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
        return json_decode($this->attributes['feedback']);
    }
}
```

#### 2.4 Update User Model
```php
// Add to app/User.php fillable array:
protected $fillable = [
    'username', 'name', 'email', 'password', 'client_id', 
    'job_title', 'job_family', 'industry_id'
];

// Add relationship method:
/**
 * Get the industry to which this user belongs.
 */
public function industry()
{
    return $this->belongsTo('App\Industry');
}
```

#### 2.5 Update Client Model
```php
// Add to app/Client.php:
/**
 * Get all feedback libraries belonging to this client.
 */
public function feedbackLibraries()
{
    return $this->hasMany('App\FeedbackLibrary');
}
```

### 3. Controllers

#### 3.1 IndustriesController
```php
// app/Http/Controllers/IndustriesController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Industry;
use Illuminate\Support\Facades\Validator;

class IndustriesController extends Controller
{
    public function index()
    {
        $industries = Industry::all()->sortBy('name');
        return view('dashboard.industries.index', compact('industries'));
    }

    public function create()
    {
        return view('dashboard.industries.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $industry = new Industry($data);
        $industry->save();

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' created successfully!');
    }

    public function edit($id)
    {
        $industry = Industry::findOrFail($id);
        return view('dashboard.industries.edit', compact('industry'));
    }

    public function update(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|unique:industries,name,'.$industry->id,
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $industry->update($data);

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' updated successfully!');
    }

    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
        $industry->delete();

        return redirect('dashboard/industries')
            ->with('success', 'Industry '.$industry->name.' deleted successfully!');
    }
}
```

#### 3.2 BenchmarksController
```php
// app/Http/Controllers/BenchmarksController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Benchmark;
use App\Assessment;
use App\Industry;
use App\Dimension;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BenchmarksController extends Controller
{
    public function selectAssessment()
    {
        $assessments = Assessment::all();
        return view('dashboard.benchmarks.index', compact('assessments'));
    }

    public function selectIndustry($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industries = Industry::all()->sortBy('name');
        return view('dashboard.benchmarks.index', compact('assessment', 'industries'));
    }

    public function index($assessmentId, $industryId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $industry = Industry::findOrFail($industryId);
        $dimensions = $assessment->dimensions->sortBy('name');

        return view('dashboard.benchmarks.index', compact('assessment', 'industry', 'dimensions'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        foreach ($data['benchmarks'] as $benchmarkData) {
            if (!array_key_exists('industry', $benchmarkData))
                continue;

            $benchmark = Benchmark::where([
                'industry_id' => $benchmarkData['industry'],
                'dimension_id' => $benchmarkData['dimension']
            ])->first();

            if ($benchmark && $benchmarkData['value']) {
                $benchmark->value = $benchmarkData['value'];
                $benchmark->save();
            } elseif ($benchmark && !$benchmarkData['value']) {
                $benchmark->delete();
            } elseif (!$benchmark && $benchmarkData['value']) {
                $benchmark = new Benchmark([
                    'industry_id' => $benchmarkData['industry'],
                    'dimension_id' => $benchmarkData['dimension'],
                    'value' => $benchmarkData['value'],
                ]);
                $benchmark->save();
            }
        }

        return 1;
    }

    public function upload($assessmentId, Request $request)
    {
        $data = $request->all();
        $assessment = Assessment::findOrFail($assessmentId);
        $errors = [];
        $updated = 0;
        $created = 0;

        $validator = Validator::make($data, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => 'File must be a valid .xls or a .xlsx file format.']);

        Excel::load($data['file'], function($reader) use (&$errors, &$updated, &$created, $assessmentId) {
            $results = $reader->all();
            $reader->each(function($sheet) use (&$errors, &$updated, &$created, $assessmentId) {
                $sheet->each(function($row) use (&$errors, &$updated, &$created, $assessmentId) {
                    $dimensionName = $row[0];
                    $error = false;

                    foreach ($row as $industryName => $value) {
                        if (!$industryName)
                            continue;

                        $dimension = Dimension::where([
                            'name' => $dimensionName,
                            'assessment_id' => $assessmentId,
                        ])->first();

                        if (! $dimension) {
                            $errors[] = 'Dimension "'.$dimensionName.'" was not found.';
                            $error = true;
                            break;
                        }

                        $industry = Industry::all()->filter(function($indy) use ($industryName) {
                            if (clean_string($indy->name) == $industryName)
                                return true;
                        })->first();

                        if (! $industry) {
                            $errors[] = 'Industry "'.$industry->name.'" was not found.';
                            $error = true;
                            break;
                        }

                        $benchmark = Benchmark::where([
                            'industry_id' => $industry->id,
                            'dimension_id' => $dimension->id,
                        ])->first();

                        if ($benchmark) {
                            $benchmark->value = $value;
                            $benchmark->save();
                            $updated++;
                        } else {
                            $benchmark = new Benchmark([
                                'industry_id' => $industry->id,
                                'dimension_id' => $dimension->id,
                                'value' => $value
                            ]);
                            $benchmark->save();
                            $created++;
                        }
                    }
                });
            });
        });

        $success = [];
        $success[] = $created . ' new entries added.';
        $success[] = $updated . ' entries updated.';
        return \Response::json([
            'success' => $success,
            'errors' => $errors,
        ]);
    }
}
```

#### 3.3 FeedbackController
```php
// app/Http/Controllers/FeedbackController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Assessment;
use App\FeedbackLibrary;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index()
    {
        $libraries = FeedbackLibrary::where('client_id', null)->get();
        return view('dashboard.feedback.index', compact('libraries'));
    }

    public function create()
    {
        $assessments = Assessment::all();
        return view('dashboard.feedback.create', compact('assessments'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $errors = [];

        $validator = Validator::make($data, [
            'name' => 'required|unique:feedback_libraries'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => ['Name must be unique.']]);

        if (!array_key_exists('feedback', $data))
            return \Response::json(['errors' => ['No feedback specified.']]);

        $library = new FeedbackLibrary([
            'name' => $data['name'],
            'feedback' => $data['feedback'],
        ]);

        try {
            $library->save();
        } catch (\Exception $e) {
            return \Response::json(['errors' => [$e->getMessage()]]);
        }

        return \Response::json(['success' => 'Saved successfully!']);
    }

    public function edit($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $assessments = Assessment::all();
        return view('dashboard.feedback.edit', compact('library', 'assessments'));
    }

    public function update(Request $request, $id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $data = $request->all();
        $errors = [];

        $validator = Validator::make($data, [
            'name' => 'required|unique:feedback_libraries,name,'.$library->id,
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => ['Name must be unique.']]);

        try {
            $library->update([
                'name' => $data['name'],
                'feedback' => $data['feedback'],
            ]);
        } catch (\Exception $e) {
            return \Response::json(['errors' => [$e->getMessage()]]);
        }

        return \Response::json(['success' => 'Updated successfully!']);
    }

    public function destroy($id)
    {
        $library = FeedbackLibrary::findOrFail($id);
        $library->delete();

        return redirect()->back()
            ->with('success', 'Feedback library "'.$library->name.'" deleted successfully!');
    }
}
```

### 4. Routes

Add the following routes to `app/Http/routes.php` in the admin middleware group:

```php
// Industries routes
Route::get('dashboard/industries', 'IndustriesController@index');
Route::get('dashboard/industries/create', 'IndustriesController@create');
Route::post('dashboard/industries', 'IndustriesController@store');
Route::get('dashboard/industries/{id}/edit', 'IndustriesController@edit');
Route::patch('dashboard/industries/{id}', 'IndustriesController@update');
Route::delete('dashboard/industries/{id}', 'IndustriesController@destroy');

// Benchmarks routes
Route::get('dashboard/benchmarks', 'BenchmarksController@selectAssessment');
Route::get('dashboard/benchmarks/{assessmentId}', 'BenchmarksController@selectIndustry');
Route::get('dashboard/benchmarks/{assessmentId}/{industryId}', 'BenchmarksController@index');
Route::get('dashboard/benchmarks/create', 'BenchmarksController@create');
Route::post('dashboard/benchmarks', 'BenchmarksController@store');
Route::post('dashboard/benchmarks/{assessmentId}/upload', 'BenchmarksController@upload');

// Feedback routes
Route::get('dashboard/feedback', 'FeedbackController@index');
Route::get('dashboard/feedback/create', 'FeedbackController@create');
Route::post('dashboard/feedback', 'FeedbackController@store');
Route::get('dashboard/feedback/{id}/edit', 'FeedbackController@edit');
Route::patch('dashboard/feedback/{id}', 'FeedbackController@update');
Route::delete('dashboard/feedback/{id}', 'FeedbackController@destroy');
```

### 5. Views

#### 5.1 Update Sidebar Navigation
Add the missing menu items to `resources/views/dashboard/partials/_sidebar.blade.php` in the admin section:

```php
@role('admin')
    <!-- ... existing menu items ... -->
    <li>
        <a href="{{ url('dashboard/industries') }}">
            <i class="fa-flask"></i>
            <span class="title">Industries</span>
        </a>
    </li>
    <li>
        <a href="{{ url('dashboard/benchmarks') }}">
            <i class="fa-signal"></i>
            <span class="title">Benchmarks</span>
        </a>
    </li>
    <li>
        <a href="{{ url('dashboard/feedback') }}">
            <i class="fa-file-text-o"></i>
            <span class="title">Feedback</span>
        </a>
    </li>
@endrole
```

#### 5.2 Copy View Files
Copy the following view directories from the original codebase:
- `resources/views/dashboard/industries/`
- `resources/views/dashboard/benchmarks/`
- `resources/views/dashboard/feedback/`

### 6. Dependencies

#### 6.1 Composer Dependencies
Add to `composer.json`:
```json
{
    "require": {
        "maatwebsite/excel": "^2.1",
        "barryvdh/laravel-dompdf": "0.8.2",
        "league/flysystem-aws-s3-v3": "^1.0",
        "s-ichikawa/laravel-sendgrid-driver": "1.2.6"
    }
}
```

#### 6.2 PHP Extensions
Ensure the following PHP extensions are installed in the Docker container:
- `pdo_mysql`
- `mbstring`
- `exif`
- `pcntl`
- `bcmath`
- `gd`
- `json` (usually included by default)

#### 6.3 Server Software
The current Docker setup already includes:
- Apache 2.4
- PHP 7.4
- MySQL (via docker-compose)
- Node.js 6.17.1

### 7. Database Seeders

#### 7.1 Industries Seeder
```php
// database/seeds/IndustriesTableSeeder.php
<?php

use Illuminate\Database\Seeder;
use App\Industry;

class IndustriesTableSeeder extends Seeder
{
    public function run()
    {
        $industries = [
            'Technology',
            'Healthcare',
            'Finance',
            'Education',
            'Manufacturing',
            'Retail',
            'Consulting',
            'Government',
            'Non-Profit',
            'Real Estate',
            'Transportation',
            'Energy',
            'Media',
            'Legal',
            'Hospitality'
        ];

        foreach ($industries as $industry) {
            Industry::create(['name' => $industry]);
        }
    }
}
```

#### 7.2 Update DatabaseSeeder
```php
// database/seeds/DatabaseSeeder.php
public function run()
{
    // ... existing seeders ...
    $this->call(IndustriesTableSeeder::class);
}
```

### 8. Integration Points

#### 8.1 User Registration/Profile
Update user registration and profile forms to include industry selection:

```php
// In user registration/profile forms
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

#### 8.2 Assessment Scoring
Update assessment scoring to include benchmark comparisons:

```php
// In assessment scoring logic
public function getBenchmarkScore($user, $dimension)
{
    if (!$user->industry_id) {
        return null;
    }
    
    return Benchmark::where('industry_id', $user->industry_id)
                   ->where('dimension_id', $dimension->id)
                   ->value('value');
}
```

#### 8.3 Report Generation
Update report generation to include industry benchmarks and feedback:

```php
// In report generation
$benchmarks = [];
if ($user->industry_id) {
    $benchmarks = Benchmark::where('industry_id', $user->industry_id)
                          ->whereIn('dimension_id', $dimensionIds)
                          ->get()
                          ->keyBy('dimension_id');
}

$feedback = FeedbackLibrary::where('client_id', $client->id)
                          ->orWhere('client_id', null)
                          ->get();
```

### 9. Testing Strategy

#### 9.1 Unit Tests
- Test Industry model relationships
- Test Benchmark calculations
- Test FeedbackLibrary JSON handling
- Test User industry association

#### 9.2 Feature Tests
- Test industry CRUD operations
- Test benchmark management
- Test feedback library management
- Test user industry assignment

#### 9.3 Integration Tests
- Test assessment scoring with benchmarks
- Test report generation with industry data
- Test feedback integration in reports

### 10. Deployment Checklist

#### 10.1 Database
- [ ] Run migrations in order
- [ ] Seed industries data
- [ ] Verify foreign key constraints
- [ ] Test data integrity

#### 10.2 Application
- [ ] Install new dependencies
- [ ] Clear application cache
- [ ] Regenerate autoloader
- [ ] Test all new routes

#### 10.3 UI/UX
- [ ] Verify sidebar navigation
- [ ] Test all CRUD operations
- [ ] Verify form validations
- [ ] Test file uploads (benchmarks)

#### 10.4 Integration
- [ ] Test user industry assignment
- [ ] Verify benchmark calculations
- [ ] Test feedback in reports
- [ ] Verify data relationships

### 11. Rollback Plan

#### 11.1 Database Rollback
```bash
php artisan migrate:rollback --step=4
```

#### 11.2 Code Rollback
- Remove new models, controllers, and views
- Remove new routes
- Revert sidebar changes
- Remove dependencies

### 12. Performance Considerations

#### 12.1 Database Indexing
Add indexes for frequently queried fields:
```sql
ALTER TABLE benchmarks ADD INDEX idx_industry_dimension (industry_id, dimension_id);
ALTER TABLE users ADD INDEX idx_industry (industry_id);
```

#### 12.2 Caching
Consider caching industry and benchmark data:
```php
$industries = Cache::remember('industries', 60, function() {
    return Industry::orderBy('name')->get();
});
```

### 13. Security Considerations

#### 13.1 Authorization
- Ensure only admin users can manage industries, benchmarks, and feedback
- Validate user permissions for industry assignment
- Sanitize feedback library content

#### 13.2 Data Validation
- Validate industry names (no duplicates)
- Validate benchmark values (numeric ranges)
- Sanitize JSON feedback content

### 14. Monitoring and Maintenance

#### 14.1 Logging
- Log industry assignments
- Log benchmark updates
- Log feedback library changes

#### 14.2 Data Integrity
- Regular checks for orphaned benchmarks
- Validation of industry-user relationships
- Backup of feedback library content

This implementation plan provides a comprehensive roadmap for adding the three core features while maintaining system stability and performance.
