<?php

namespace App\Http\Controllers;

use App\Client;
use App\Language;
use App\Research;
use App\User;
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

        if ($user->client && $user->client->require_profile && !$user->completed_profile)
			return view('profile.index', compact('user', 'first_name', 'middle_name', 'last_name'));

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
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors())->withInput();

        $name = implode(' ', [$data['first_name'], $data['last_name']]);

        if ($data['middle_name'])
            $name = implode(' ', [$data['first_name'], $data['middle_name'], $data['last_name']]);

        $data['name'] = $name;
        $data['password'] = bcrypt($data['password']);

        $user = \Auth::user();
        $user->update($data);

        $user->completed_profile = true;
        $user->save();

        return redirect('/profile/research');
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

        $languages = Language::all();
        $languages_array = [
            '' => '',
        ];
        foreach ($languages as $language)
            $languages_array[$language->id] = $language->native_name;

        return view('profile.language', compact('user', 'languages_array'));
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

        return view('dashboard.users.create', compact('rolesArray', 'clientsArray'));
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
            'password' => 'required|min:4'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

		if (! array_key_exists('client_id', $data))
			$data['client_id'] = false;

        if (! $data['client_id']) {
            unset($data['client_id']);
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

        return view('dashboard.users.edit', compact('user', 'rolesArray', 'clientsArray'));
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

		if (! array_key_exists('client_id', $data))
			$data['client_id'] = false;

        if (! $data['client_id']) {
            unset($data['client_id']);
            $user->client_id = null;
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

        // For each user field
        foreach($data['username'] as $i => $username) {

            $users[$i] = false;
            $name = $data['name'][$i];
            $email = $data['email'][$i];
            $job_title = $data['job_title'][$i];
            $job_family = $data['job_family'][$i];
            $job = $data['job_id'][$i];

            // Generate new user
            $user = new User([
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'job_title' => $job_title,
                'job_family' => $job_family,
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

            // If can't save, must be a duplicate entry
            catch (\Exception $e) {
                $error = '';

                if (strpos($e, 'Duplicate entry'))
                    $error = 'Username '.$username.' is already in use.';

                array_push($errors, 'User '.$name.' could not be added. '.$error);
            }

            $users[$i] = $user;
        }

        $file = $this->download_generated_users($users);
        $download_link = '/download/'.$file['file'];

        return \Response::json(['count' => $count, 'errors' => $errors, 'users' => $users, 'download_link' => $download_link]);
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
     * Upload and parse an excel spreadsheet of users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload_from_file(Request $request)
    {
        $data = $request->all();
        $users = [];

        $validator = Validator::make($data, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => 'File must be a valid .xls or a .xlsx file format.']);

        Excel::load($data['file'], function($reader) use (&$users) {
            $results = $reader->all();

            $reader->each(function($sheet) use (&$users) {

                $sheet->each(function($row) use (&$users) {
                    $name = $row->name;
                    $email = trim($row->email);
                    $job_title = $row->job_title;
                    $job_family = $row->job_family;
                    $username = $row->username;

                    if (! $row->email && $row->e_mail)
                        $email = $row->e_mail;

                    if (! $row->username && $row->user_name)
                        $username = $row->user_name;

                    array_push($users, [
                    	'email' => $email,
						'name' => $name,
						'username' => $username,
						'job_title' => $job_title,
						'job_family' => $job_family
					]);
                });
            });
        });

        return \Response::json(['users' => $users]);
    }

	/**
     * Download an excel spreadsheet of users that have just been created.
     *
     * @param $users
     * @return mixed
     */
    public function download_generated_users($users)
    {
        //$filename = 'Generated Users ' . Carbon::now();
        $filename = 'Generated Users '.time();
        $data = Excel::create($filename, function($excel) use ($users)
        {
            $excel->setTitle('Generated User Details');

            $excel->sheet('Details', function($sheet) use ($users) {

                $sheet->loadView('excel.generated-users', compact('users'));

                $sheet->setAutoSize(true);

                $sheet->freezeFirstRow();
                $sheet->cell('A1:F1', function($cell) {

                    $cell->setFont(array(
                        'bold' => true
                    ));
                });
            });
        });

        return $data->store('csv', false, true);

        //return view('excel.assignments.show', compact('assessment', 'questions', 'answers', 'user', 'assignment'));
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
