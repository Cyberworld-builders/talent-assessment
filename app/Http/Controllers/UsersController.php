<?php

namespace App\Http\Controllers;

use App\Client;
use App\Language;
use App\Research;
use App\User;
use App\Industry;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    /**
     * Show all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $roles = Role::all();

		if (session('reseller'))
			$roles = session('reseller')->roles();

        return view('dashboard.users.index', compact('users', 'roles'));
    }


	/**
     * Show the form for filling out profile info.
     *
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function profile()
    {
        $user = \Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect('/login');
        }

		$name = explode(' ', $user->name);

		$first_name = $name[0];
		$middle_name = '';
		$last_name = '';

		if (count($name) > 1) {
			$last_name = $name[1];
		}

		if (count($name) > 2) {
			$middle_name = $name[1];
			$last_name = $name[2];
		}

        if (! $user->language_id)
            return redirect('/language');

//		if (! $user->accepted_terms)
//			return redirect('/terms');

        if ($user->completed_profile && $user->completed_research)
            return redirect('/assignments');

        // Get industry options for the form
        $industries = Industry::orderBy('name')->get();
        $industryOptions = ['' => 'Select Industry'];
        if ($industries->count() > 0) {
            foreach($industries as $industry) {
                $industryOptions[$industry->id] = $industry->name;
            }
        }

        if ($user->client && $user->client->require_profile && !$user->completed_profile)
			return view('profile.index', compact('user', 'first_name', 'middle_name', 'last_name', 'industryOptions'));

        if ($user->client && $user->client->require_research && !$user->completed_research)
            return redirect('/profile/research');

		return redirect('/assignments');
    }

	/**
     * Store a user's profile information.
     *
     * @param Request $request
     * @return $this|RedirectResponse|Redirector
     */
    public function update_profile(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            //'organization_id' => 'required',
            'password' => 'required|min:6|confirmed',
            'industry_id' => 'sometimes|exists:industries,id'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors())->withInput();

        $name = implode(' ', [$data['first_name'], $data['last_name']]);

        if (isset($data['middle_name']) && !empty(trim($data['middle_name'])))
            $name = implode(' ', [$data['first_name'], trim($data['middle_name']), $data['last_name']]);

        $data['name'] = $name;
        $data['password'] = bcrypt($data['password']);

        // Handle empty industry_id
        if (isset($data['industry_id']) && empty($data['industry_id'])) {
            $data['industry_id'] = null;
        }

        $user = \Auth::user();
        $user->update($data);
        $user->completed_profile = true;
        $user->save();

        // Check if client requires research
        if ($user->client && $user->client->require_research) {
            return redirect('/profile/research');
        } else {
            return redirect('/assignments');
        }
    }

	/**
     * Show the form for the optional research questions.
     *
     * @return RedirectResponse|Redirector|View
     */
    public function research()
    {
        $user = \Auth::user();

        if (! $user->language_id)
            return redirect('/language');

//		if (! $user->accepted_terms)
//			return redirect('/terms');

        if ($user->completed_profile && $user->completed_research)
            return redirect('/assignments');

        return view('profile.research', compact('user'));
    }

	/**
     * Store the user's optional research questions.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function store_research(Request $request)
    {
        $data = $request->all();

        $research = new Research($data);

        $user = \Auth::user();
        $user->research()->save($research);
        $user->completed_research = true;
        $user->save();

        return redirect('/assignments');
    }

	/**
	 * Show the form for choosing a language.
	 *
	 * @return RedirectResponse|Redirector|View
	 */
    public function language()
    {
        $user = \Auth::user();

        // If user already has a language set, redirect to profile
        if ($user->language_id) {
            return redirect('/profile');
        }

        // Auto-set English as default language (ID 1) and skip selection
        $user->language_id = 1; // English
        $user->save();

        return redirect('/profile');
    }

	/**
	 * Store a user's language preferences
	 *
	 * @param Request $request
	 * @return RedirectResponse|Redirector|View
	 */
    public function update_language(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required',
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        $user = \Auth::user();
        $user->language_id = $request->language_id;
        $user->save();

        return redirect('/profile');
    }

	/**
	 * Show the terms and conditions form.
	 *
	 * @return RedirectResponse|Redirector|View
	 */
	public function terms()
	{
		$user = \Auth::user();

		return view('profile.terms', compact('user'));
    }

	/**
	 * Store the terms information in the database.
	 *
	 * @param Request $request
	 * @return RedirectResponse|Redirector|View
	 */
	public function update_terms(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'signature' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		$user = \Auth::user();
		$user->accepted_signature = $request->signature;
		$user->accepted_at = Carbon::now();
		$user->accepted_terms = 1;
		$user->save();

		return redirect('/profile');
    }

    /**
     * Show all users for a specific client.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show_users_for_client($id)
    {
        $client = Client::findOrFail($id);
        $users = $client->users()->paginate(10);

        return view('dashboard.clients.users', compact('client', 'users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clients = Client::all();
        $roles = Role::all();
        $industries = Industry::orderBy('name')->get();

		// If Reseller, don't include AOE Admin role
		if (Auth::user()->isReseller())
			$roles = Role::all()->except(1);

        $rolesArray = [];
        foreach ($roles as $role)
            $rolesArray[$role->id] = $role->name;

		// If Reseller, make reseller role the admin role
		if (Auth::user()->isReseller())
			$rolesArray[2] = 'Administrator';

        $clientsArray = [null => '---'];
        foreach ($clients as $client)
            $clientsArray[$client->id] = $client->name;

        $industriesArray = [null => 'Select Industry'];
        foreach ($industries as $industry)
            $industriesArray[$industry->id] = $industry->name;

        return view('dashboard.users.create', compact('rolesArray', 'clientsArray', 'industriesArray'));
    }

    /**
     * Show the form for creating or importing multiple users form a spreadsheet for a specific client
     *
     * @return \Illuminate\Http\Response
     */
    public function add_users_to_client($id)
    {
        $client = Client::findOrFail($id);
        $jobs = $client->jobs;

        $jobsArray = [0 => 'No'];
        if (! $jobs->isEmpty())
        {
            foreach ($jobs as $job)
                $jobsArray[$job->id] = 'Add to ' . $job->name;
        }

        return view('dashboard.users.create-multiple', compact('client', 'jobsArray'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        if ($data['role'] == 4)
            $data['password'] = \Auth::user()->generate_password($data['name'], $data['username']);

        $validator = Validator::make($data, [
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:4',
            'industry_id' => 'required|exists:industries,id'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors())->withInput($request->except('password', 'password_confirmation'));

		if (! array_key_exists('client_id', $data))
			$data['client_id'] = false;

        if (! $data['client_id']) {
            unset($data['client_id']);
        }

        // Ensure industry_id is properly set
        if (empty($data['industry_id'])) {
            return redirect()->back()->withErrors(['industry_id' => 'The industry field is required.'])->withInput($request->except('password', 'password_confirmation'));
        }

        $user = new User($data);
        $user->password = bcrypt($data['password']);
        $user->save();

        $role = Role::find($data['role']);
        $user->attachRole($role);

        return redirect()->back()->with('success', 'User '.$user->name.' created successfully!');
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$user = User::findOrFail($id);

        return view('dashboard.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $clients = Client::all();
        $industries = Industry::orderBy('name')->get();

		$rolesArray = [
			1 => 'AOE Admin',
			3 => 'Client Admin',
			4 => 'User'
		];

		// If Reseller, don't include AOE Admin role
		if (Auth::user()->isReseller())
			$rolesArray = [
				2 => 'Administrator',
				3 => 'Client Admin',
				4 => 'User'
			];

		// If self, don't allow role change at all
		if (Auth::user()->id == $user->id)
			if (Auth::user()->isReseller())
				$rolesArray = [$user->role()->id => 'Administrator'];
			else
				$rolesArray = [$user->role()->id => $user->role()->name];

        $clientsArray = [null => '---'];
        foreach ($clients as $client)
            $clientsArray[$client->id] = $client->name;

        $industriesArray = [null => 'Select Industry'];
        foreach ($industries as $industry)
            $industriesArray[$industry->id] = $industry->name;

        return view('dashboard.users.edit', compact('user', 'rolesArray', 'clientsArray', 'industriesArray'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all();

        if ($data['role'] == 4)
            $data['password'] = $user->generate_password($data['name'], $user->username);

        if (! array_key_exists('password', $data))
            $data['password'] = false;

        $validator = Validator::make($data, [
            'name' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        if ($data['password'])
            $data['password'] = bcrypt($data['password']);
        else
            unset($data['password']);

		// Handle client_id - only update if it's explicitly provided and not empty
		if (! array_key_exists('client_id', $data) || empty($data['client_id'])) {
			unset($data['client_id']); // Don't update client_id if not provided or empty
		}

        $user->update($data);

        $role = Role::find($data['role']);
        $user->detachAllRoles();
        $user->attachRole($role);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

		if ($user->id == \Auth::user()->id)
			return redirect()->back()->with('error', 'Cannot delete self.');

        $user->detachAllRoles();
        $user->delete();

        return redirect()->back()->with('success', 'User '.$user->name.' deleted successfully!');
    }

    /**
     * Generate a new password for the specified user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate_password(Request $request)
    {
        if (! \Auth::check())
            return false;

        $data = $request->all();
        $password = \Auth::user()->generate_password($data['name'], $data['username']);

        return \Response::json($password);
    }

	/**
     * Generate arbitrary usernames.
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function generate_username(Request $request)
    {
        if (! \Auth::check())
            return false;

        $data = $request->all();
        $usernames = [];
        $prefix = $data['prefix'];

        for ($i = 0; $i < $data['number']; $i++) {
            $usernames[$i] = \Auth::user()->generate_username($prefix);
        }

        return \Response::json($usernames);
    }

	/**
     * Store multiple new users.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_multiple(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $data = $request->all();
        $count = 0;
        $errors = [];
        $users = [];

        // Debug: Log the received data
        \Log::info('Store Multiple Debug', [
            'data_keys' => array_keys($data),
            'username_count' => count($data['username']),
            'name_count' => count($data['name']),
            'email_count' => count($data['email']),
            'industry_count' => isset($data['industry']) ? count($data['industry']) : 'NOT SET',
            'industry_data' => isset($data['industry']) ? $data['industry'] : 'NOT SET'
        ]);

        // For each user field
        foreach($data['username'] as $i => $username) {

            $users[$i] = false;
            $name = $data['name'][$i];
            $email = $data['email'][$i];
            $industry = isset($data['industry'][$i]) ? $data['industry'][$i] : '';
            $job = $data['job_id'][$i];

            // Find industry by name (case-insensitive)
            $industryRecord = \App\Industry::whereRaw('LOWER(name) = ?', [strtolower($industry)])->first();
            if (!$industryRecord) {
                $availableIndustries = \App\Industry::pluck('name')->toArray();
                \Log::error("Industry not found for user $name", [
                    'industry' => $industry, 
                    'available_industries' => $availableIndustries
                ]);
                array_push($errors, 'User "'.$name.'" could not be added. Industry "'.$industry.'" not found. Available industries: ' . implode(', ', $availableIndustries));
                continue;
            }

            // Generate new user
            $user = new User([
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'industry_id' => $industryRecord->id,
                'password' => bcrypt(\Auth::user()->generate_password($name, $username)),
                'client_id' => $client->id
            ]);

            // Attempt to save
            try {
                $user->save();
                $role = Role::find(4);
                $user->attachRole($role);
                $count += 1;

                // Add as applicant of job, if set
                if ($job)
                {
                    DB::table('job_users')->insert([
                        'user_id' => $user->id,
                        'job_id' => $job,
                        'viable' => true,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            // Handle specific database errors
            catch (\Illuminate\Database\QueryException $e) {
                $error = '';
                
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    if (strpos($e->getMessage(), 'username') !== false) {
                        $error = 'Username "'.$username.'" is already in use.';
                    } elseif (strpos($e->getMessage(), 'email') !== false) {
                        $error = 'Email "'.$email.'" is already in use.';
                    } else {
                        $error = 'Duplicate entry found.';
                    }
                } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                    $error = 'Invalid industry or client reference.';
                } else {
                    $error = 'Database error: ' . $e->getMessage();
                }
                
                array_push($errors, 'User "'.$name.'" could not be added. '.$error);
                \Log::error("User creation failed for $name", [
                    'error' => $e->getMessage(),
                    'user_data' => $user ? $user->toArray() : 'User object not created'
                ]);
            }
            
            // Handle other exceptions
            catch (\Exception $e) {
                $error = 'Unexpected error: ' . $e->getMessage();
                array_push($errors, 'User "'.$name.'" could not be added. '.$error);
                \Log::error("Unexpected error creating user $name", [
                    'error' => $e->getMessage(),
                    'user_data' => $user ? $user->toArray() : 'User object not created'
                ]);
            }

            $users[$i] = $user;
        }

        // Only generate download file if we have successful users
        $download_link = null;
        if ($count > 0) {
            try {
                $file = $this->download_generated_users($users);
                $download_link = '/download/'.$file['file'];
            } catch (\Exception $e) {
                \Log::error("Failed to generate download file", ['error' => $e->getMessage()]);
                array_push($errors, 'Users were created but download file could not be generated.');
            }
        }

        return \Response::json([
            'count' => $count, 
            'errors' => $errors, 
            'users' => $users, 
            'download_link' => $download_link,
            'success' => $count > 0
        ]);
    }

	/**
	 * Store multiple new users from a list of names and emails.
	 *
	 * @param Request $request
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store_multiple_from_list(Request $request, $id)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$users = [];
		$errors = [];

		// For each user field
		foreach($data['users'] as $i => $user)
		{
			if (! $user['name'])
				continue;

			$users[$i] = false;
			$name = $user['name'];
			$email = $user['email'];
			$username = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','', $name));

			// Generate new user
			$user = new User([
				'username' => $username,
				'name' => $name,
				'email' => $email,
				'password' => bcrypt(\Auth::user()->generate_password($name, $username)),
				'client_id' => $client->id
			]);

			// Attempt to save
			try {
				$user->save();
				$role = Role::find(4);
				$user->attachRole($role);
			}

			// If can't save, must be a duplicate entry
			catch (\Exception $e) {
				$error = '';

				if (strpos($e, 'Duplicate entry'))
					$error = 'Username '.$username.' is already in use.';

				array_push($errors, 'User '.$name.' could not be added. '.$error);
			}

			$users[$i] = $user;
		}

		return \Response::json(['errors' => $errors, 'users' => $users]);
	}

	/**
     * Upload and parse a CSV file of users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload_from_file(Request $request)
    {
        $data = $request->all();
        $users = [];

        $validator = Validator::make($data, [
            'file' => 'required|mimes:csv,txt'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => 'File must be a valid .csv file format.']);

        $file = $data['file'];
        
        // Debug: Log file information
        \Log::info('CSV Upload Debug', [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'temp_path' => $file->getPathname()
        ]);
        
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle === false) {
            \Log::error('Could not open uploaded file');
            return \Response::json(['errors' => 'Could not read the uploaded file.']);
        }

        // Read the header row
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            \Log::error('Could not read header row from CSV');
            return \Response::json(['errors' => 'Could not read the header row from the CSV file.']);
        }
        
        // Debug: Log header information
        \Log::info('CSV Header', ['header' => $header]);

        // Process each data row
        $rowCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            \Log::info("Processing row $rowCount", ['row' => $row]);
            
            // Skip empty rows (rows with all empty values)
            if (empty(array_filter($row, function($value) { return !empty(trim($value)); }))) {
                \Log::info("Skipping empty row $rowCount");
                continue;
            }
            
            // Create an associative array from header and row data
            $rowData = array_combine($header, $row);
            
            // Handle different CSV formats
            $name = '';
            $email = '';
            $industry = '';
            $username = '';
            
            // Check if this is the standard format (Name, Email, Industry)
            if (isset($rowData['Name']) || isset($rowData['name'])) {
                $name = isset($rowData['Name']) ? $rowData['Name'] : (isset($rowData['name']) ? $rowData['name'] : '');
                $email = isset($rowData['Email']) ? trim($rowData['Email']) : (isset($rowData['email']) ? trim($rowData['email']) : '');
                $industry = isset($rowData['Industry']) ? $rowData['Industry'] : (isset($rowData['industry']) ? $rowData['industry'] : '');
                $username = isset($rowData['Username']) ? $rowData['Username'] : (isset($rowData['username']) ? $rowData['username'] : '');
            }
            // Handle the Involved-360 format (positional columns)
            else {
                // Based on the log data: [username, name, email, industry, ...]
                $username = isset($row[0]) ? trim($row[0]) : '';
                $name = isset($row[1]) ? trim($row[1]) : '';
                $email = isset($row[2]) ? trim($row[2]) : '';
                $industry = isset($row[3]) ? trim($row[3]) : '';
            }

            // Skip rows that don't have at least a name or email
            if (empty(trim($name)) && empty(trim($email))) {
                \Log::info("Skipping row $rowCount - no name or email");
                continue;
            }

            // Validate required fields with specific error messages
            if (empty(trim($name))) {
                \Log::error("Row $rowCount - Name is required");
                return \Response::json(['errors' => "Row $rowCount: Name is required. Please provide a name for each user."]);
            }
            
            if (empty(trim($email))) {
                \Log::error("Row $rowCount - Email is required");
                return \Response::json(['errors' => "Row $rowCount: Email is required. Please provide an email address for each user."]);
            }
            
            if (empty(trim($industry))) {
                \Log::error("Row $rowCount - Industry is required");
                return \Response::json(['errors' => "Row $rowCount: Industry is required. Please provide an industry for each user."]);
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                \Log::error("Row $rowCount - Invalid email format: $email");
                return \Response::json(['errors' => "Row $rowCount: Invalid email format for '$email'. Please provide a valid email address."]);
            }

            // Generate username if not provided
            if (empty(trim($username)) && !empty(trim($name))) {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/', '', $name));
                // Ensure username is not empty and add a number if needed
                if (empty($username)) {
                    $username = 'user' . $rowCount;
                }
            }

            // Handle alternative column names
            if (empty($email) && isset($rowData['e_mail'])) {
                $email = trim($rowData['e_mail']);
            }

            if (empty($username) && isset($rowData['user_name'])) {
                $username = $rowData['user_name'];
            }

            array_push($users, [
                'email' => $email,
                'name' => $name,
                'username' => $username,
                'industry' => $industry
            ]);
        }

        fclose($handle);

        \Log::info('CSV Upload Complete', [
            'total_rows_processed' => $rowCount,
            'users_found' => count($users),
            'users' => $users
        ]);

        return \Response::json(['users' => $users]);
    }

    /**
     * Download a CSV template for user bulk upload.
     *
     * @return \Illuminate\Http\Response
     */
    public function download_template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_upload_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Write header row
            fputcsv($file, ['Name', 'Email', 'Industry', 'Username']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

	/**
     * Download a CSV file of users that have just been created.
     *
     * @param $users
     * @return mixed
     */
    public function download_generated_users($users)
    {
        $filename = 'Generated Users '.time().'.csv';
        $filepath = storage_path('app/exports/'.$filename);
        
        // Create exports directory if it doesn't exist
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        // Create CSV file
        $handle = fopen($filepath, 'w');
        
        // Write CSV header
        fputcsv($handle, ['Name', 'Email', 'Username', 'Industry']);
        
        // Write user data
        foreach ($users as $user) {
            if ($user && $user->id) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->username,
                    $user->industry ? $user->industry->name : 'N/A'
                ]);
            }
        }
        
        fclose($handle);
        
        return [
            'file' => $filename,
            'path' => $filepath
        ];
    }

	/**
     * Event to fire as users logs in.
     *
     * @param $event
     */
    public function onUserLogin($event)
    {
        $user = \Auth::user();
        $user->last_login_at = Carbon::now();
        $user->save();
    }

    /**
     * Get and return users from an array of user ids
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_users_from_ids(Request $request)
    {
        $data = $request->all();
        $ids = $data['ids'];

        $users = User::all()->filter(function($user) use ($ids) {
            return in_array($user->id, $ids);
        })->toArray();

        return \Response::json(['users' => $users]);
    }

	/**
	 * Log in as this user.
	 *
	 * @param $id
	 * @return RedirectResponse
	 */
	public function auth($id)
	{
		$user = User::findOrFail($id);

		if (Auth::user()->is('admin') || (session('reseller') && Auth::user()->is('reseller')))
		{
			Auth::login($user);
			return redirect('/dashboard');
		}
		else
			abort(404, 'You do not have permission to do that.');
    }
}
