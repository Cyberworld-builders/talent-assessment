<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Client;
use App\DBConnection;
use App\Job;
use App\PredictiveModel;
use App\Reseller;
use App\ResellerUser;
use App\User;
use App\Weight;
use Artisan;
use Auth;
use Aws\Rds\RdsClient;
use Aws\S3\S3Client;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Route;
use Session;

class ResellersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$resellers = Reseller::all();

		foreach ($resellers as $i => $reseller)
			$resellers[$i]->db_status = $reseller->checkDbStatus();

        return view('dashboard.resellers.index', compact('resellers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$assessments = Assessment::all();
		$assessmentsArray = [];
		foreach ($assessments as $assessment)
			$assessmentsArray[$assessment->id] = $assessment->name;

        return view('dashboard.resellers.create', compact('assessmentsArray'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required|unique:resellers',
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Store the logo
		if ($request->file('logo'))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$data['logo'] = $imageName;
			$data['logo'] = $result->get('ObjectURL');
		}

		// Store the background
		if ($request->file('background'))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$data['background'] = $imageName;
			$data['background'] = $result->get('ObjectURL');
		}

		// Setup new database credentials
		$data['db_host'] = null;
		$data['db_name'] = 'aoe_' . clean_string($data['name']);
		$data['db_user'] = null;
		$data['db_pass'] = null;
		$data['db_instance'] = null;

		// If on the live server, fill in more specific credentials
		if (env('APP_ENV') == 'staging')
		{
			$data['db_host'] = '';
			// $data['db_user'] = clean_string($data['name']);
			// $data['db_pass'] = strrev(base64_encode(strrev($data['db_name'])));
			$data['db_instance'] = clean_string($data['name'], '-') . '-' . rand(10000, 99999);
		}

		// Create the reseller
		$reseller = new Reseller($data);
		$reseller->save();

		// Create the new database
		$this->createDatabase($data);

		return redirect('dashboard/resellers')->with('success', 'Reseller '.$reseller->name.' created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();

		return view('dashboard.resellers.show', compact('reseller'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();

		$assessments = Assessment::all();
		$assessmentsArray = [];
		foreach ($assessments as $assessment)
			$assessmentsArray[$assessment->id] = $assessment->name;

        return view('dashboard.resellers.edit', compact('reseller', 'assessmentsArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$reseller = Reseller::findOrFail($id);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required|unique:resellers,name,'.$reseller->id,
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Update the logo
		if (array_key_exists('logo', $data))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$data['logo'] = $imageName;
			$data['logo'] = $result->get('ObjectURL');
		}

		// Update the background
		if (array_key_exists('background', $data))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$data['background'] = $imageName;
			$data['background'] = $result->get('ObjectURL');
		}

		$reseller->update($data);

		return redirect('dashboard/resellers')->with('success', 'Reseller '.$reseller->name.' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		$reseller = Reseller::findOrFail($id);

		$this->removeDatabase($reseller);
		$reseller->delete();

		return redirect('dashboard/resellers')->with('success', 'Reseller '.$reseller->name.' deleted successfully!');
    }

	/**
	 * Creates a new database.
	 *
	 * @param $data
	 * @return bool
	 */
	function createDatabase($data)
	{
		// Check if we're on the live server
		if (env('APP_ENV') == 'staging')
		{
			// Create the database on aws
			$rds = new RdsClient(config('aws'));
			$db = $rds->createDBInstance([
				'DBInstanceClass' => 'db.t2.micro',
				'DBInstanceIdentifier' => $data['db_instance'],
				'DBName' => $data['db_name'],
				'Engine' => 'mysql',
				'MasterUserPassword' => $_SERVER['RDS_PASSWORD'],
				'MasterUsername' => $_SERVER['RDS_USERNAME'],
				'AllocatedStorage' => 5,
			]);

			if (!$db)
				abort(403, 'Could not create new database on the server. Please make sure the Client name does not contain any invalid characters.');
		}

		// Otherwise, we're on Localhost
		else
		{
			// Create the database
			DB::connection()->statement('CREATE DATABASE ' . $data['db_name']);

			// Migrate the tables into the new database
			$db = new DBConnection(['database' => $data['db_name']]);
			Artisan::call('migrate', [
				'--database' => $db->getConnection()->getName()
			]);

			// Seed the database
			Artisan::call('db:seed', [
				'--database' => $db->getConnection()->getName(),
				'--class' => 'DatabaseSeeder'
			]);

			// Drop any unnecessary tables
			$reseller = new Reseller();
			foreach ($reseller->getBlacklistedTables() as $tableName)
				$db->getConnection()->statement('DROP TABLE IF EXISTS '.$tableName);
		}
	}

	/**
	 * Removes a database with the specified name.
	 *
	 * @param $reseller
	 * @return bool
	 */
	function removeDatabase($reseller)
	{
		// Check if we're on the live server
		if (env('APP_ENV') == 'staging')
		{
			// Delete a database from aws
			$rds = new RdsClient(config('aws'));
			$db = $rds->deleteDBInstance([
				'DBInstanceIdentifier' => $reseller->db_instance,
				'SkipFinalSnapshot' => true,
			]);
		}

		// Otherwise, we're on Localhost
		else
			DB::connection()->statement('DROP DATABASE ' . $reseller->db_name);

		return true;
	}

	/**
	 * Display a listing of all the clients for this reseller.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function clients($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$clients = $reseller->clients();

		return view('dashboard.clients.index', compact('reseller', 'clients'));
	}

	/**
	 * Show the view for creating a client for this reseller.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function createClient($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();

		return view('dashboard.clients.create', compact('reseller'));
	}

	/**
	 * Store a new client in storage for this reseller.
	 *
	 * @param $id
	 * @param Request $request
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
	public function storeClient($id, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$data = $request->all();

		$db = $reseller->connectToDatabase();
		if (!$db)
			return redirect('dashboard/resellers/'.$reseller->id.'/users')->withInput()->with('error', 'Error saving. Database is unavailable at the moment.');

		$validator = Validator::make($data, [
			'name' => 'required|unique:'.$reseller->db_name.'.clients',
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Store the logo
		if ($request->file('logo'))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$request->file('logo')->move(uploads_path(), $imageName);
			$data['logo'] = $imageName;
		}

		// Store the background
		if ($request->file('background'))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$request->file('background')->move(uploads_path(), $imageName);
			$data['background'] = $imageName;
		}

		// Prep the data
		unset($data['_token']);
		unset($data['role']);
		$data['created_at'] = Carbon::now();
		$data['updated_at'] = Carbon::now();

		$db->getConnection()->table('clients')->insert($data);

		return redirect('dashboard/resellers/'.$reseller->id.'/clients')->with('success', 'Client '.$data['name'].' created successfully!');
	}

	/**
	 * Show the form for editing a specific client for this reseller.
	 *
	 * @param $id
	 * @param $clientId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editClient($id, $clientId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$client = $reseller->client($clientId);

		return view('dashboard.clients.edit', compact('reseller', 'client'));
	}

	/**
	 * Update a specific client.
	 *
	 * @param $id
	 * @param $clientId
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updateClient($id, $clientId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$client = $reseller->client($clientId);
		$data = $request->all();

		$db = $reseller->connectToDatabase();
		if (!$db)
			return redirect('dashboard/resellers/'.$reseller->id.'/clients')->withInput()->with('error', 'Error saving. Database is unavailable at the moment.');

		$validator = Validator::make($data, [
			'name' => 'required|unique:'.$reseller->db_name.'.clients,name,'.$client->id,
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Store the logo
		if (array_key_exists('logo', $data))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$request->file('logo')->move(uploads_path(), $imageName);
			$data['logo'] = $imageName;
		}

		// Store the background
		if (array_key_exists('background', $data))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$request->file('background')->move(uploads_path(), $imageName);
			$data['background'] = $imageName;
		}

		// Prep the data
		unset($data['_token']);
		unset($data['_method']);
		unset($data['role']);
		$data['updated_at'] = Carbon::now();

		$db->getConnection()->table('clients')->update($data);

		return redirect('dashboard/resellers/'.$reseller->id.'/clients')->with('success', 'Client '.$data['name'].' updated successfully!');
	}

	/**
	 * Display the specified client.
	 *
	 * @param  int $id
	 * @param $clientId
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function showClient($id, $clientId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$client = $reseller->client($clientId);
		$users = $client->users->sortBy('name');

		// Paginate the results
		$paginator = new LengthAwarePaginator($users, $users->count(), 10);
		$users = collect(array_slice($paginator->items(), 0, $paginator->perPage()));

		// If different page, offset the results
		if ($request->page && $request->page > 1 && $request->page <= $paginator->lastPage())
		{
			$offset = ($paginator->currentPage() - 1) * $paginator->perPage();
			$users = collect(array_slice($paginator->items(), $offset, $paginator->perPage()));
		}

		return view('dashboard.clients.show', compact('client', 'users', 'reseller', 'paginator'));
	}

	/**
	 * Display all the users for a specific client for a specific reseller.
	 *
	 * @param $id
	 * @param $clientId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function clientUsers($id, $clientId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$client = $reseller->client($clientId);
		$users = $reseller->users()->where('client_id', $client->id);;
		$roles = $reseller->roles();

		return view('dashboard.clients.users', compact('reseller', 'client', 'users', 'roles'));
	}

	/**
	 * Display a listing of all the users for this reseller.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function users($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		Auth::user()->reseller_id = $reseller->id;
		$users = $reseller->users();
		$roles = $reseller->roles();

		return view('dashboard.users.index', compact('reseller', 'users', 'roles'));
	}

	/**
	 * Show the form for creating a new user.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function createUser($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$clients = $reseller->clients();
		$roles = Role::all()->except(1);

		$rolesArray = [];
		foreach ($roles as $i => $role)
			$rolesArray[$role->id] = $role->name;

		$clientsArray = [null => '---'];
		foreach ($clients as $client)
			$clientsArray[$client->id] = $client->name;

		return view('dashboard.users.create', compact('reseller', 'rolesArray', 'clientsArray'));
	}

	/**
	 * Store a new user for this reseller.
	 *
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function storeUser($id, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$data = $request->all();

		if ($data['role'] == 4)
			$data['password'] = \Auth::user()->generate_password($data['name'], $data['username']);

		$db = $reseller->connectToDatabase();
		if (!$db)
			return redirect('dashboard/resellers/'.$reseller->id.'/users')->withInput()->with('error', 'Error saving. Database is unavailable at the moment.');

		$validator = Validator::make($data, [
			'name' => 'required',
			'username' => 'required|unique:'.$reseller->db_name.'.users',
			'password' => 'required|min:4'
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		if (! array_key_exists('client_id', $data))
			$data['client_id'] = false;

		if (! $data['client_id']) {
			unset($data['client_id']);
		}

		$data['password'] = bcrypt($data['password']);
		$role = Role::find($data['role']);

		// Prep the data
		unset($data['_token']);
		unset($data['role']);
		$data['created_at'] = Carbon::now();
		$data['updated_at'] = Carbon::now();

		$userId = $db->getConnection()->table('users')->insertGetId($data);
		$db->getConnection()->table('role_user')->insert([
			'role_id' => $role->id,
			'user_id' => $userId
		]);

		return redirect('dashboard/resellers/'.$reseller->id.'/users')->with('success', 'User '.$data['name'].' created successfully!');
	}

	/**
	 * Edit the specified user.
	 *
	 * @param $id
	 * @param $userId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editUser($id, $userId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$user = $reseller->user($userId);
		$clients = $reseller->clients();
		$roles = Role::all()->except(1);

		$rolesArray = [];
		foreach ($roles as $i => $role)
			$rolesArray[$role->id] = $role->name;

		$clientsArray = [null => '---'];
		foreach ($clients as $client)
			$clientsArray[$client->id] = $client->name;

		return view('dashboard.users.edit', compact('reseller', 'user', 'rolesArray', 'clientsArray'));
	}

	/**
	 * Update a specific user.
	 *
	 * @param $id
	 * @param $userId
	 * @param Request $request
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
	public function updateUser($id, $userId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$user = $reseller->user($userId);
		$data = $request->all();
		$db = $reseller->connectToDatabase();
		if (!$db)
			return redirect('dashboard/resellers/'.$reseller->id.'/users')->withInput()->with('error', 'Error saving. Database is unavailable at the moment.');

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

		$role = Role::find($data['role']);
		$db->getConnection()->table('role_user')->where('user_id', $user->id)->update(['role_id' => $role->id]);

		unset($data['_token']);
		unset($data['_method']);
		unset($data['role']);
		$data['updated_at'] = Carbon::now();
		$db->getConnection()->table('users')->where('id', $user->id)->update($data);

		return redirect('dashboard/resellers/'.$reseller->id.'/users')->with('success', 'User '.$data['name'].' updated successfully!');
	}

	/**
	 * Display the specified user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showUser($id, $userId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$user = $reseller->user($userId);

		return view('dashboard.users.show', compact('reseller', 'user'));
	}

	/**
	 * Log in as this user.
	 *
	 * @param $id
	 * @return RedirectResponse
	 */
	public function authUser($id, $userId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$user = $reseller->user($userId);

		if (Auth::user()->is('admin'))
		{
			$user = new User((Array)$user);

			// Change the connection to the Reseller's database
			\Config::set('database.connections.mysql.database', $reseller->db_name);
			DB::reconnect('mysql');

			// Store the reseller in the session
//			session(['reseller' => $reseller]);

			if (Auth::login($user))
			{
				session(['reseller' => $reseller]);
				return redirect('/dashboard')->with('success', 'Logged in as '.$user->name);
			}
			else
			{
				\Config::set('database.connections.mysql.database', 'aoe');
				DB::reconnect('mysql');
				return redirect()->back()->with('error', 'Failed to log in as '.$user->name);
			}
		}
	}

	/**
	 * Log out and redirect to the Reseller's login view.
	 *
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
	 */
	public function logout($id)
	{
		$reseller = Reseller::findOrFail($id);
		Session::flush();
		Auth::logout();

		return redirect('resellers/'.$reseller->id.'/login');
	}

	/**
	 * Show view for choosing a Reseller to view their login page.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function chooseLogin()
	{
		$resellers = Reseller::all();

		$resellersArray = [null => 'Choose Reseller..'];
		foreach ($resellers as $reseller)
			$resellersArray[$reseller->id] = $reseller->name;

		return view('auth.reseller', compact('resellersArray'));
	}

	/**
	 * Re-migrate the database for a specific reseller.
	 *
	 * @param $resellerId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updateDatabase($resellerId)
	{
		$reseller = Reseller::findOrFail($resellerId);

		$reseller->updateDatabase();

		return redirect('dashboard/config/databases')->with('success', 'Database updated for '.$reseller->name);
	}

	/**
	 * Display a listing of all the users for this reseller.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function jobs($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		Auth::user()->reseller_id = $reseller->id;
		$jobs = $reseller->jobs();

		return view('dashboard.jobs.index', compact('reseller', 'jobs'));
	}

	/**
	 * Show the form for creating a new job.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function createJob($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();

		$assessments = $reseller->assessments;
		$assessmentsArray = [];
		foreach ($assessments as $assessmentId)
		{
			$assessment = Assessment::find($assessmentId);

			if (! $assessment)
				continue;

			$assessmentsArray[$assessment->id] = $assessment->name;
		}

		return view('dashboard.jobs.create', compact('reseller', 'assessmentsArray'));
	}

	/**
	 * Store a new job for this reseller.
	 *
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function storeJob($id, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'slug' => 'required|unique:jobs,slug',
			'assessments' => 'required',
		],
		[
			'slug.required' => 'The Job ID field is required.',
			'slug.unique' => 'A job with the specified ID already exists. The Job ID field must be unique.'
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		$job = new Job($data);
		$job->reseller_id = $reseller->id;
		$job->save();

		return redirect('dashboard/resellers/'.$reseller->id.'/jobs')->with('success', 'Job '.$job->name.' created successfully!');
	}

	/**
	 * Edit the specified job.
	 *
	 * @param $id
	 * @param $jobId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function editJob($id, $jobId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$job = Job::findOrFail($jobId);

		$assessments = $reseller->assessments;
		$assessmentsArray = [];
		foreach ($assessments as $assessmentId)
		{
			$assessment = Assessment::find($assessmentId);

			if (! $assessment)
				continue;

			$assessmentsArray[$assessment->id] = $assessment->name;
		}

		return view('dashboard.jobs.edit', compact('reseller', 'job', 'assessmentsArray'));
	}

	/**
	 * Update a specific job.
	 *
	 * @param $id
	 * @param $jobId
	 * @param Request $request
	 * @return $this|\Illuminate\Http\RedirectResponse
	 */
	public function updateJob($id, $jobId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$job = Job::findOrFail($jobId);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'slug' => 'required|unique:jobs,slug,'.$job->id,
			'assessments' => 'required',
		],
		[
			'slug.required' => 'The Job ID field is required.',
			'slug.unique' => 'A job with the specified ID already exists. The Job ID field must be unique.'
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		$job->update($data);

		return redirect('dashboard/resellers/'.$reseller->id.'/jobs')->with('success', 'Job '.$job->name.' updated successfully!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyJob($id, $jobId)
	{
		$reseller = Reseller::findOrFail($id);
		$job = Job::findOrFail($jobId);

		$job->delete();

		return redirect('dashboard/resellers/'.$reseller->id.'/jobs')->with('success', 'Job '.$job->name.' deleted successfully!');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function weights($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$jobs = Job::where('reseller_id', $id)->get();

		return view('dashboard.weights.index', compact('reseller', 'jobs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $assessmentId
	 * @return \Illuminate\Http\Response
	 */
	public function createWeights($id, $jobId, $assessmentId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$job = Job::findOrFail($jobId);
		$assessment = Assessment::findOrFail($assessmentId);

		return view('dashboard.weights.create', compact('reseller', 'job', 'assessment'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $assessmentId
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeWeights($id, $jobId, $assessmentId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$job = Job::findOrFail($jobId);
		$assessment = Assessment::findOrFail($assessmentId);
		$data = $request->all();

		$validator = Validator::make($data, [
			'total' => 'numeric|size:100'
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Generate dimension weights array
		$weights = [];
		if (! $assessment->dimensions->isEmpty())
		{
			foreach ($data['dimension'] as $i => $dimensionId)
				$weights[$dimensionId] = $data['weight'][$i];
		}

		// Generate divisions array
		$divisions = [];
		foreach ($data['division'] as $i => $division)
		{
			$divisions[$i] = [
				'name' => $division,
				'min' => $data['division_min'][$i],
				'max' => $data['division_max'][$i]
			];
		}

		// Save the new weight
		$weight = new Weight();
		$weight->job_id = $job->id;
		$weight->assessment_id = $assessment->id;
		$weight->weights = $weights;
		$weight->divisions = $divisions;
		$weight->save();

		return redirect('dashboard/resellers/'.$reseller->id.'/weights')->with('success', 'Custom weighting set for '.$job->name.' '.$assessment->name.' successfully!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showWeights($id)
	{
		return view('dashboard.weights.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @param $weightId
	 * @return \Illuminate\Http\Response
	 */
	public function editWeights($id, $weightId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$weight = Weight::findOrFail($weightId);
		$job = $weight->job;
		$assessment = $weight->assessment;

		return view('dashboard.weights.edit', compact('reseller', 'weight', 'job', 'assessment'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 * @param $weightId
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function updateWeights($id, $weightId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$weight = Weight::findOrFail($weightId);
		$job = $weight->job;
		$assessment = $weight->assessment;
		$data = $request->all();

		$validator = Validator::make($data, [
			'total' => 'numeric|size:100'
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		// Generate dimension weights array
		$weights = [];
		if (! $assessment->dimensions->isEmpty())
		{
			foreach ($data['dimension'] as $i => $dimensionId)
				$weights[$dimensionId] = $data['weight'][$i];
		}

		// Generate divisions array
		$divisions = [];
		foreach ($data['division'] as $i => $division)
		{
			$divisions[$i] = [
				'name' => $division,
				'min' => $data['division_min'][$i],
				'max' => $data['division_max'][$i]
			];
		}

		// Update the weight
		$weight->weights = $weights;
		$weight->divisions = $divisions;
		$weight->save();

		return redirect('dashboard/resellers/'.$reseller->id.'/weights')->with('success', 'Custom weighting updated for '.$job->name.' '.$assessment->name.' successfully!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyWeights($id, $weightId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$weight = Weight::findOrFail($weightId);
		$assessment = $weight->assessment;

		$weight->delete();

		return redirect('dashboard/resellers/'.$reseller->id.'/weights')->with('success', 'Custom weighting for '.$assessment->name.' deleted successfully!');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function models($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$jobs = Job::where('reseller_id', $id)->get();

		return view('dashboard.spss.index', compact('reseller', 'jobs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function createModels($id)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$jobs = Job::where('reseller_id', $id)->get();

		$jobsArray = [null => 'Select a Job..'];
		foreach ($jobs as $job)
			$jobsArray[$job->id] = $job->name;

		$jobAssessments = [];
		foreach ($jobs as $job)
		{
			foreach ($job->assessments as $assessment)
			{
				$assessment = Assessment::find($assessment);
				$jobAssessments[$job->id][] = [
					'id' => $assessment->id,
					'name' => $assessment->name,
				];
			}
		}

		return view('dashboard.spss.create', compact('reseller', 'jobsArray', 'jobAssessments'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeModels($id, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'job' => 'required',
			'assessments' => 'required',
			'file' => 'required|mimes:xml',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		$filePath = $request->file('file')->getRealPath();
		$xml = simplexml_load_file($filePath);

		$jsonXml = json_decode(json_encode($xml));

		if (! $jsonXml->Header->Application->{'@attributes'}->name == 'IBM SPSS Modeler')
			return redirect()->back()->withInput()->with('error', 'The provided XML file is not a valid SPSS Modeler PMML file.');

		// Get our factors (what we will need to get scores for)
		$factors = [];
		foreach ($jsonXml->DataDictionary->DataField as $field)
		{
			if ($field->{'@attributes'}->dataType == 'string' || $field->{'@attributes'}->optype == 'categorical')
				continue;

			$factors[] = [
				'name' => $field->{'@attributes'}->name,
				'type' => null,
				'id' => null,
			];
		}

		$job = Job::findOrFail((int)$data['job']);

		$model = new PredictiveModel([
			'name' => $data['name'],
			'job_id' => $job->id,
			'assessments' => $data['assessments'],
			'model' => $xml,
			'filename' => $request->file('file')->getClientOriginalName(),
			'factors' => $factors,
		]);
		$model->save();

		return redirect('dashboard/resellers/'.$reseller->id.'/models/'.$model->id.'/edit')->with('success', 'Predictive model for '.$job->name.' created successfully!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showModels($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function editModels($id, $modelId)
	{
		$reseller = Reseller::findOrFail($id);
		$reseller->db_status = $reseller->checkDbStatus();
		$model = PredictiveModel::findOrFail($modelId);
		$job = $model->job;
		$jobsArray = [$job->id => $job->name];

		$jobAssessments = [];
		foreach ($job->assessments as $assessment)
		{
			$assessment = Assessment::find($assessment);
			$jobAssessments[$assessment->id] = $assessment->name;
		}

		$assessments = [];
		$assessmentsArray = [];
		foreach ($job->assessments as $i => $assessment)
		{
			$assessment = Assessment::find($assessment);
			$assessments[$i]['id'] = $assessment->id;
			$assessments[$i]['name'] = $assessment->name;
			$assessmentsArray[$assessment->id] = $assessment->name;
		}

		$dimensions = [];
		$dimensionsArray = [];
		$i = 0;
		foreach ($job->assessments as $assessment)
		{
			$assessment = Assessment::find($assessment);
			if ($assessment->dimensions)
				foreach ($assessment->dimensions as $dimension)
				{
					$dimensions[$i]['id'] = $dimension->id;
					$dimensions[$i]['name'] = $dimension->name;
					$i++;
					$dimensionsArray[$dimension->id] = $dimension->name;
				}
		}

		return view('dashboard.spss.edit', compact('reseller', 'model', 'job', 'jobsArray', 'jobAssessments', 'assessments', 'dimensions', 'assessmentsArray', 'dimensionsArray'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateModels($id, $modelId, Request $request)
	{
		$reseller = Reseller::findOrFail($id);
		$model = PredictiveModel::findOrFail($modelId);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'job' => 'required',
			'assessments' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		// New file is being uploaded
		if (array_key_exists('file', $data))
		{
			$filePath = $request->file('file')->getRealPath();
			$xml = simplexml_load_file($filePath);

			$jsonXml = json_decode(json_encode($xml));

			if (! $jsonXml->Header->Application->{'@attributes'}->name == 'IBM SPSS Modeler')
				return redirect()->back()->withInput()->with('error', 'The provided XML file is not a valid SPSS Modeler PMML file.');

			// Get our factors (what we will need to get scores for)
			$factors = [];
			foreach ($jsonXml->DataDictionary->DataField as $field)
			{
				if ($field->{'@attributes'}->dataType == 'string' || $field->{'@attributes'}->optype == 'categorical')
					continue;

				$factors[] = [
					'name' => $field->{'@attributes'}->name,
					'type' => null,
					'id' => null,
				];
			}

			$model->update([
				'model' => $xml,
				'filename' => $request->file('file')->getClientOriginalName(),
				'factors' => $factors,
			]);

			return redirect('dashboard/resellers/'.$reseller->id.'/models/'.$model->id.'/edit')->with('success', 'New predictive model uploaded successfully!');
		}

		// Update the factors
		$factors = [];
		foreach ($model->factors as $i => $factor)
		{
			$factors[$i]['name'] = $factor['name'];
			if (array_key_exists($i, $data['factors']['type']))
				$factors[$i]['type'] = $data['factors']['type'][$i];

			if (array_key_exists($i, $data['factors']['id']))
				$factors[$i]['id'] = $data['factors']['id'][$i];
		}

		// Check if all factors are configured
		$configured = 1;
		foreach ($factors as $factor)
			if ($factor['name'] == null || $factor['type'] == null || $factor['id'] == null)
				$configured = 0;

		$model->update([
			'name' => $data['name'],
			'assessments' => $data['assessments'],
			'factors' => $factors,
			'configured' => $configured,
		]);

		return redirect('dashboard/resellers/'.$reseller->id.'/models')->with('success', 'Predictive model updated successfully!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroyModels($id, $modelId)
	{
		$reseller = Reseller::findOrFail($id);
		$model = PredictiveModel::findOrFail($modelId);

		$model->delete();

		return redirect('dashboard/resellers/'.$reseller->id.'/models')->with('success', 'Predictive model deleted successfully!');
	}
}
