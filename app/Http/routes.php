<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Temp
//Route::get('generate', 'DashboardController@generateDefaults');
//Route::get('generate_new', 'DashboardController@generate_new');
//Route::get('test', 'DashboardController@test');

// Authenticated
Route::group(['middleware' => ['auth']], function()
{
	// Must Be Any Role Above Standard User
	Route::group(['middleware' => ['level:2']], function()
	{
		// Dashboard
		Route::get('dashboard', 'DashboardController@index');
		Route::post('dashboard/check_applicant', 'DashboardController@check_if_applicant_exists');
		Route::get('download/{file}', 'DashboardController@download')->where('filename', '[A-Za-z0-9\-\_\.]+');

		// Client Dashboard
		Route::get('dashboard/selection', 'ClientDashboardController@selection');
		Route::get('dashboard/development', 'ClientDashboardController@development');
		Route::get('dashboard/development/{id}', 'ClientDashboardController@specificDev');
		Route::get('dashboard/jobs/{id}', 'ClientDashboardController@job');
		Route::get('dashboard/jobs/{id}/assign', 'ClientDashboardController@assign');
		Route::post('dashboard/assign', 'AssignmentsController@assignAssessment');
		Route::get('dashboard/add-applicants', 'ClientDashboardController@addApplicants');
		Route::post('dashboard/add-applicants', 'ClientDashboardController@storeApplicants');
		Route::get('dashboard/all-users', 'ClientDashboardController@users');
		Route::get('dashboard/all-users/{id}', 'ClientDashboardController@user');
		Route::get('dashboard/all-users/{id}/edit', 'ClientDashboardController@editUser');
		Route::patch('dashboard/all-users/{id}', 'ClientDashboardController@updateUser');
		Route::get('dashboard/jobs/{id}/download', 'ClientDashboardController@download_job_data');
		Route::get('dashboard/jobs/{id}/export-users', 'ClientDashboardController@exportApplicants');
		Route::post('dashboard/assign/add-users', 'AssignmentsController@add_users_to_assignment_from_client');
		Route::post('dashboard/assign/add-user', 'AssignmentsController@add_user_to_assignment');
		Route::post('dashboard/assessments/{id}/assign', 'AssessmentsController@generate_assignment_for_assessment');

		// Client Job Applicants
		Route::get('dashboard/clients/{id}/jobs/{jobId}/applicants', 'JobsController@applicants');
		Route::get('dashboard/clients/{id}/jobs/{jobId}/applicants/add', 'JobsController@addApplicants');
		Route::post('dashboard/clients/{id}/jobs/{jobId}/applicants/{userId}/reject', 'JobsController@rejectApplicant');
		Route::post('dashboard/clients/{id}/jobs/{jobId}/applicants/{userId}/unreject', 'JobsController@unrejectApplicant');
		Route::get('dashboard/clients/{id}/jobs/{jobId}/download', 'JobsController@download_job_data');

		// Client Assignments
		Route::post('dashboard/assign/add-applicants', 'AssignmentsController@add_users_to_assignment_from_job');
		Route::get('dashboard/assignments', 'ClientDashboardController@assignments');
		Route::get('dashboard/assignments/{id}/edit', 'ClientDashboardController@editAssignment');
		Route::patch('dashboard/assignments/{id}', 'AssignmentsController@update');
		Route::get('dashboard/assignments/bulk-edit', 'ClientDashboardController@bulk');
		Route::get('dashboard/assignments/jobs/{id}/bulk-edit', 'ClientDashboardController@bulk');
		Route::post('dashboard/assignments/bulk', 'AssignmentsController@bulk_update');
		Route::get('dashboard/assignments/{date}', 'ClientDashboardController@assignmentsForDate');
		Route::delete('dashboard/assignments/{id}', 'AssignmentsController@destroy');
		Route::post('dashboard/assignments/send-assignment-email', 'AssignmentsController@sendAssignmentEmail');

		// Reports
		Route::get('dashboard/report/{clientId}/{jobId}/{userId}', 'ReportsController@index');
		Route::get('dashboard/report/development/{clientId}/{assignmentId}/{userId}', 'ReportsController@indexDevelopment');
		Route::get('dashboard/model/{clientId}/{jobId}/{userId}/{modelId}', 'ReportsController@model');
		Route::get('dashboard/report/cacique', 'ReportsController@caciquetest');
		Route::get('dashboard/users/{id}/report/{clientReportId}', 'ReportsController@clientReport');
		Route::get('dashboard/report/{id}/{userId}', 'ReportsController@show');
		Route::get('dashboard/report/{id}/{userId}/download', 'ReportsController@downloadReport');
		Route::get('dashboard/report/{clientId}/{jobId}/{userId}/download', 'ReportsController@download');
		Route::get('dashboard/model/{clientId}/{jobId}/{userId}/{modelId}/download', 'ReportsController@downloadModel');

		// Users
		Route::post('dashboard/users/upload', 'UsersController@upload_from_file');

		// My Account
		Route::get('account', 'DashboardController@account');
		Route::patch('account', 'ClientDashboardController@updateAccount');
	});

	// Must Be Reseller Role or Higher
	Route::group(['middleware' => ['level:3']], function()
	{
		// Dashboard
		Route::post('dashboard/check_user', 'DashboardController@check_if_user_exists');
		Route::get('dashboard/get-user', 'DashboardController@getUser');
		Route::post('dashboard/sort', 'DashboardController@sort');

		// Users
		Route::get('dashboard/users/{id}/auth', 'UsersController@auth');
		Route::resource('dashboard/users', 'UsersController');
		Route::get('dashboard/users/client/{id}', 'UsersController@show_users_for_client');
		Route::get('dashboard/users/create/{id}', 'UsersController@add_users_to_client');
		Route::post('dashboard/users/create/{id}', 'UsersController@store_multiple');
		Route::post('dashboard/users/create-from-list/{id}', 'UsersController@store_multiple_from_list');
		Route::post('dashboard/users/generate_password', 'UsersController@generate_password');
		Route::post('dashboard/users/generate_username', 'UsersController@generate_username');
		Route::delete('dashboard/users/{id}', 'UsersController@destroy');
		Route::post('dashboard/users/get_users_from_ids', 'UsersController@get_users_from_ids');
		Route::get('dashboard/users/{id}/auth', 'UsersController@auth');

		// Clients
		Route::resource('dashboard/clients', 'ClientsController');
		Route::get('dashboard/clients/{id}/export-users', 'ClientsController@export_users');

		// Client Users
		Route::get('dashboard/clients/{id}/users', 'UsersController@show_users_for_client');

		// Client Assignments
		Route::get('dashboard/clients/{id}/assignments', 'AssignmentsController@assignments');
		Route::get('dashboard/clients/{id}/assignments/bulk-edit', 'AssignmentsController@bulk');
		Route::get('dashboard/clients/{id}/assignments/{date}', 'AssignmentsController@assignmentsForDate');
		Route::get('dashboard/clients/{id}/assignments/{assignmentId}/edit', 'AssignmentsController@edit');
		Route::get('dashboard/clients/{id}/assign', 'AssignmentsController@assignToClient');
		Route::post('dashboard/clients/{id}/assign', 'AssignmentsController@assignAssessment');
		Route::post('dashboard/clients/{id}/assign/verify', 'AssignmentsController@verifyAssessments');
		Route::post('dashboard/clients/{id}/add-from-groups', 'AssignmentsController@addFromGroups');
		Route::post('dashboard/clients/{id}/add-from-job-family', 'AssignmentsController@addFromJobFamily');
		Route::post('dashboard/clients/{id}/add-from-job', 'AssignmentsController@addFromJob');
		Route::post('dashboard/clients/{id}/assignments/send-assignment-email', 'AssignmentsController@sendAssignmentEmail');
		Route::post('dashboard/assessments/upload', 'AssessmentsController@upload_from_file');

		// Client Groups
		Route::get('dashboard/clients/{id}/groups', 'GroupsController@index');
		Route::get('dashboard/clients/{id}/groups/create', 'GroupsController@create');
		Route::post('dashboard/clients/{id}/groups', 'GroupsController@store');
		Route::get('dashboard/clients/{id}/groups/{groupId}/edit', 'GroupsController@edit');
		Route::patch('dashboard/clients/{id}/groups/{groupId}', 'GroupsController@update');
		Route::delete('dashboard/clients/{id}/groups/{groupId}', 'GroupsController@destroy');
		Route::post('dashboard/clients/{id}/generate-groups', 'GroupsController@autoGenerateGroups');
		Route::post('dashboard/clients/{id}/upload-groups', 'GroupsController@uploadGroups');

		// Client Jobs
		Route::get('dashboard/clients/{id}/jobs', 'JobsController@index');
		Route::get('dashboard/clients/{id}/jobs/create/{jobTemplateId}', 'JobsController@createFromTemplate');
		Route::post('dashboard/clients/{id}/jobs/{jobTemplateId}', 'JobsController@storeFromTemplate');
		Route::get('dashboard/clients/{id}/jobs/{jobId}/edit', 'JobsController@edit');
		Route::patch('dashboard/clients/{id}/jobs/{jobId}', 'JobsController@update');
		Route::delete('dashboard/clients/{id}/jobs/{jobId}', 'JobsController@destroy');

		// Client Surveys
		Route::get('dashboard/clients/{id}/surveys', 'SurveysController@index');
		Route::get('dashboard/clients/{id}/surveys/{date}', 'SurveysController@show');
		Route::get('dashboard/clients/{id}/surveys/{date}/generate', 'SurveysController@generate');

		// Assignments
		Route::get('dashboard/assignments/{id}/edit', 'AssignmentsController@edit');
		Route::patch('dashboard/assignments/{id}', 'AssignmentsController@update');
		Route::get('dashboard/assignments/user/{user_id}', 'AssignmentsController@show_assignments_for_user');
		Route::post('dashboard/assignments/upload', 'AssignmentsController@upload_from_file');
	});

	// Must Be An AOE Admin
	Route::group(['middleware' => ['level:4']], function()
	{
		// Dashboard
		Route::get('dashboard/test', 'DashboardController@test');
		Route::get('dashboard/config', 'DashboardController@config');
		Route::post('dashboard/config', 'DashboardController@config');
		Route::get('dashboard/config/databases', 'DashboardController@databases');
		Route::get('dashboard/config/databases/{resellerId}/update', 'ResellersController@updateDatabase');

		// Client Jobs
		Route::get('dashboard/clients/{id}/jobs/create', 'JobsController@create');
		Route::post('dashboard/clients/{id}/jobs', 'JobsController@store');

		// Client Job Applicants
		Route::get('dashboard/clients/{id}/jobs/{jobId}/applicants', 'JobsController@applicants');
		Route::get('dashboard/clients/{id}/jobs/{jobId}/applicants/add', 'JobsController@addApplicants');
		Route::post('dashboard/clients/{id}/jobs/{jobId}/applicants', 'JobsController@storeApplicants');

		// Client Weighting
		Route::get('dashboard/clients/{id}/weights', 'WeightsController@index');
		Route::get('dashboard/clients/{id}/weights/create/{jobId}/{assessmentId}', 'WeightsController@create');
		Route::post('dashboard/clients/{id}/weights/{jobId}/{assessmentId}', 'WeightsController@store');
		Route::get('dashboard/clients/{id}/weights/{weightId}/edit', 'WeightsController@edit');
		Route::patch('dashboard/clients/{id}/weights/{weightId}', 'WeightsController@update');
		Route::delete('dashboard/clients/{id}/weights/{weightId}', 'WeightsController@destroy');

		// Client Predictive Models
		Route::get('dashboard/clients/{id}/models', 'PredictiveModelsController@index');
		Route::get('dashboard/clients/{id}/models/create', 'PredictiveModelsController@create');
		Route::post('dashboard/clients/{id}/models', 'PredictiveModelsController@store');
		Route::get('dashboard/clients/{id}/models/{modelId}/edit', 'PredictiveModelsController@edit');
		Route::patch('dashboard/clients/{id}/models/{modelId}', 'PredictiveModelsController@update');
		Route::delete('dashboard/clients/{id}/models/{modelId}', 'PredictiveModelsController@destroy');

		// Client Job Analysis
		Route::get('dashboard/clients/{id}/analysis', 'AnalysisController@index');
		Route::get('dashboard/clients/{id}/analysis/create', 'AnalysisController@create');
		Route::get('dashboard/clients/{id}/analysis/{analysisId}', 'AnalysisController@show');
		Route::post('dashboard/clients/{id}/analysis', 'AnalysisController@store');
		Route::get('dashboard/clients/{id}/analysis/{analysisId}/edit', 'AnalysisController@edit');
		Route::patch('dashboard/clients/{id}/analysis/{analysisId}', 'AnalysisController@update');
		Route::delete('dashboard/clients/{id}/analysis/{analysisId}', 'AnalysisController@destroy');
		Route::get('dashboard/clients/{id}/analysis/{analysisId}/send', 'AnalysisController@send');

		// Client JAQ
		Route::get('dashboard/clients/{id}/analysis/{analysisId}/jaqs/{jaqId}', 'JaqsController@show');
		Route::post('dashboard/clients/{id}/analysis/{analysisId}/jaqs/{jaqId}', 'JaqsController@adminStore');
		Route::get('dashboard/clients/{id}/analysis/{analysisId}/jaqs/{jaqId}/reset', 'JaqsController@reset');
		Route::delete('dashboard/clients/{id}/analysis/{analysisId}/jaqs/{jaqId}', 'JaqsController@destroy');

		// Client Reports
		Route::get('dashboard/clients/{id}/reports', 'ReportsController@reportsIndex');
		Route::get('dashboard/clients/{id}/reports/create', 'ReportsController@create');
		Route::get('dashboard/clients/{id}/reports/{reportId}/edit', 'ReportsController@edit');
		Route::patch('dashboard/clients/{id}/reports/{reportId}', 'ReportsController@update');
		Route::post('dashboard/clients/{id}/reports', 'ReportsController@store');
		Route::delete('dashboard/clients/{id}/reports/{reportId}', 'ReportsController@destroy');
		Route::post('dashboard/clients/{id}/reports/{reportsId}/toggle', 'ReportsController@toggleVisibility');
		Route::get('dashboard/clients/{id}/reports/{reportId}/customize', 'ReportsController@customize');
		Route::patch('dashboard/clients/{id}/reports/{reportId}/customize/update', 'ReportsController@updateCustomizations');
		Route::patch('dashboard/clients/{id}/reports/{reportId}/customize/reset', 'ReportsController@resetCustomizations');
		Route::get('dashboard/clients/{id}/reports/{reportId}/weighting', 'ReportsController@weighting');
		Route::patch('dashboard/clients/{id}/reports/{reportId}/weighting/update', 'ReportsController@updateWeighting');
		Route::get('dashboard/clients/{id}/reports/{reportId}/modeling', 'ReportsController@modeling');
		Route::patch('dashboard/clients/{id}/reports/{reportId}/modeling/update', 'ReportsController@updateModeling');

		// Client Job Reports
		Route::get('dashboard/clients/{id}/jobs/{jobId}/reports', 'ClientReportsController@index');
		Route::get('dashboard/clients/{id}/jobs/{jobId}/reports/{reportId}/edit', 'ClientReportsController@edit');
		Route::patch('dashboard/clients/{id}/jobs/{jobId}/reports/{clientReportId}/update', 'ClientReportsController@update');
		Route::post('dashboard/clients/{id}/jobs/{jobId}/reports/{reportId}', 'ClientReportsController@store');
		Route::post('dashboard/clients/{id}/jobs/{jobId}/reports/{reportId}/toggle', 'ClientReportsController@toggleVisibility');
		Route::delete('dashboard/clients/{id}/jobs/{jobId}/reports/{clientReportId}', 'ClientReportsController@destroy');

		// Resellers
		Route::resource('dashboard/resellers', 'ResellersController');
		Route::get('dashboard/resellers/{id}/logout', 'ResellersController@logout');

		// Reseller Clients
		Route::get('dashboard/resellers/{id}/clients', 'ResellersController@clients');
		Route::get('dashboard/resellers/{id}/clients/create', 'ResellersController@createClient');
		Route::post('dashboard/resellers/{id}/clients', 'ResellersController@storeClient');
		Route::get('dashboard/resellers/{id}/clients/{clientId}/edit', 'ResellersController@editClient');
		Route::patch('dashboard/resellers/{id}/clients/{clientId}', 'ResellersController@updateClient');
		Route::get('dashboard/resellers/{id}/clients/{clientId}', 'ResellersController@showClient');
		Route::delete('dashboard/resellers/{id}/clients/{clientId}', 'ResellersController@destroyClient');

		// Reseller Client Users
		Route::get('dashboard/resellers/{id}/clients/{clientId}/users', 'ResellersController@clientUsers');

		// Reseller Users
		Route::get('dashboard/resellers/{id}/users', 'ResellersController@users');
		Route::get('dashboard/resellers/{id}/users/create', 'ResellersController@createUser');
		Route::post('dashboard/resellers/{id}/users', 'ResellersController@storeUser');
		Route::get('dashboard/resellers/{id}/users/{userId}/edit', 'ResellersController@editUser');
		Route::patch('dashboard/resellers/{id}/users/{userId}', 'ResellersController@updateUser');
		Route::get('dashboard/resellers/{id}/users/{userId}', 'ResellersController@showUser');
		Route::delete('dashboard/resellers/{id}/users/{userId}', 'ResellersController@destroyUser');
		Route::get('dashboard/resellers/{id}/users/{userId}/auth', 'ResellersController@authUser');

		// Reseller Jobs
		Route::get('dashboard/resellers/{id}/jobs', 'ResellersController@jobs');
		Route::get('dashboard/resellers/{id}/jobs/create', 'ResellersController@createJob');
		Route::post('dashboard/resellers/{id}/jobs', 'ResellersController@storeJob');
		Route::get('dashboard/resellers/{id}/jobs/{jobId}/edit', 'ResellersController@editJob');
		Route::patch('dashboard/resellers/{id}/jobs/{jobId}', 'ResellersController@updateJob');
		Route::get('dashboard/resellers/{id}/jobs/{jobId}', 'ResellersController@showJob');
		Route::delete('dashboard/resellers/{id}/jobs/{jobId}', 'ResellersController@destroyJob');

		// Reseller Weighting
		Route::get('dashboard/resellers/{id}/weights', 'ResellersController@weights');
		Route::get('dashboard/resellers/{id}/weights/create/{jobId}/{assessmentId}', 'ResellersController@createWeights');
		Route::post('dashboard/resellers/{id}/weights/{jobId}/{assessmentId}', 'ResellersController@storeWeights');
		Route::get('dashboard/resellers/{id}/weights/{weightId}/edit', 'ResellersController@editWeights');
		Route::patch('dashboard/resellers/{id}/weights/{weightId}', 'ResellersController@updateWeights');
		Route::get('dashboard/resellers/{id}/weights/{weightId}', 'ResellersController@showWeights');
		Route::delete('dashboard/resellers/{id}/weights/{weightId}', 'ResellersController@destroyWeights');

		// Reseller Predictive Models
		Route::get('dashboard/resellers/{id}/models', 'ResellersController@models');
		Route::get('dashboard/resellers/{id}/models/create', 'ResellersController@createModels');
		Route::post('dashboard/resellers/{id}/models', 'ResellersController@storeModels');
		Route::get('dashboard/resellers/{id}/models/{modelId}/edit', 'ResellersController@editModels');
		Route::patch('dashboard/resellers/{id}/models/{modelId}', 'ResellersController@updateModels');
		Route::delete('dashboard/resellers/{id}/models/{modelId}', 'ResellersController@destroyModels');

		// Assessments
		Route::resource('dashboard/assessments', 'AssessmentsController');
		Route::get('dashboard/assessments/{id}/assign', 'AssessmentsController@assign');
		Route::post('dashboard/assessments/{id}/assign', 'AssessmentsController@assign_assessment');

		// Assessment Dimensions
		Route::get('dashboard/assessments/{id}/dimensions', 'DimensionsController@index');
		Route::get('dashboard/assessments/{id}/dimensions/create', 'DimensionsController@create');
		Route::post('dashboard/assessments/{id}/dimensions', 'DimensionsController@store');
		Route::get('dashboard/assessments/{id}/dimensions/{dimensionId}/edit', 'DimensionsController@edit');
		Route::patch('dashboard/assessments/{id}/dimensions/{dimensionId}', 'DimensionsController@update');
		Route::delete('dashboard/assessments/{id}/dimensions/{dimensionId}', 'DimensionsController@destroy');

		// Assessment Translations
		Route::get('dashboard/assessments/{id}/translations', 'TranslationsController@index');
		Route::get('dashboard/assessments/{id}/translations/create', 'TranslationsController@create');
		Route::post('dashboard/assessments/{id}/translations', 'TranslationsController@store');
		Route::get('dashboard/assessments/{id}/translations/{translationId}/edit', 'TranslationsController@edit');
		Route::patch('dashboard/assessments/{id}/translations/{translationId}', 'TranslationsController@update');
		Route::delete('dashboard/assessments/{id}/translations/{translationId}', 'TranslationsController@destroy');

		// Assignments
		Route::get('dashboard/assignments/{id}/details', 'AssignmentsController@show_assignment_details');
		Route::get('dashboard/assignment/{id}/download', 'AssignmentsController@download_assignment');
		Route::get('dashboard/assignments/download/{client_id}/{type}', 'AssignmentsController@download_all_assignments_for_client');
	});

	// Assignments
	Route::get('assignments', 'AssignmentsController@index');
	Route::get('assignments/stage/{id}', 'AssignmentsController@stage');
	Route::get('assignment/{id}', 'AssignmentsController@show');
	Route::post('assignment/{id}', 'AssignmentsController@store_answers');
	Route::post('assignment/{id}/update_time', 'AssignmentsController@update_time_limit');
	Route::post('assignment/wm/save', 'AssignmentsController@wm_save');

	// JAQs
	Route::get('jaq/{id}', 'JaqsController@showForUser');
	Route::post('jaq/{id}', 'JaqsController@store');

	// Users
	Route::get('language', 'UsersController@language');
	Route::post('language', 'UsersController@update_language');
	Route::get('profile', 'UsersController@profile');
	Route::post('profile', 'UsersController@update_profile');
	Route::get('terms', 'UsersController@terms');
	Route::post('terms', 'UsersController@update_terms');
	Route::get('profile/research', 'UsersController@research');
	Route::post('profile/research', 'UsersController@store_research');
});

// Authentication
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');
Route::get('password', 'Auth\PasswordController@getEmail');
Route::post('password', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset/{token}', 'Auth\PasswordController@postReset');
Route::get('sendreminder', 'AssignmentsController@send_reminder');
//Route::post('getservertime', 'DashboardController@getServerTime');

// Reseller Authentication
Route::get('resellers', 'ResellersController@chooseLogin');
Route::get('resellers/{id}/login', 'Auth\AuthController@getResellerLogin');
Route::post('resellers/{id}/login', 'Auth\AuthController@postResellerLogin');
Route::get('r/{id}/assignments', 'AssignmentsController@indexResellers');
//Route::post('login', 'Auth\AuthController@postLogin');

Route::get('/', 'DashboardController@home');

// Sample Assessment
Route::get('assessment/sample/{name}', 'AssignmentsController@stageWithoutAuth');
Route::post('assessment/sample/{name}/complete', 'AssignmentsController@completeSample');
Route::post('assessment/sample/{name}/report', 'ReportsController@showSample');
Route::post('assessment/sample/{name}/take/{code}', 'AssignmentsController@showWithoutAuth');

// Registration
//Route::get('register', 'Auth\AuthController@getRegister');
//Route::post('register', 'Auth\AuthController@postRegister');
