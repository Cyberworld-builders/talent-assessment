<?php

namespace App;

use Artisan;
use Aws\Rds\RdsClient;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Mockery\Exception;

class Reseller extends Model
{
	/**
	 * The columns allowed to be filled.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'db_instance',
		'db_host',
		'db_name',
		'db_user',
		'db_pass',
		'logo',
		'background',
		'primary_color',
		'accent_color',
		'assessments',
	];

	/**
	 * The tables not allowed in the reseller's database.
	 *
	 * @var array
	 */
	protected $blacklistedTables = [
		'permission_role',
		'permission_user',
		'permissions',
		'resellers',
		'jaqs',
		'analysis',
		'new_reports',
		'predictive_models',
		'globals',
	];

	/**
	 * Get an array of blacklisted tables.
	 *
	 * @return array
	 */
	public function getBlacklistedTables()
	{
		return $this->blacklistedTables;
	}

	/**
	 * Serialize assessments when saved in storage.
	 *
	 * @param $value
	 */
	public function setAssessmentsAttribute($value)
	{
		$this->attributes['assessments'] = serialize($value);
	}

	/**
	 * Un-serialize assessments when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getAssessmentsAttribute()
	{
		return unserialize($this->attributes['assessments']);
	}

	/**
	 * Get all assessments from the master database.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getAssessments()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$assessments = collect($db->getConnection()->table('assessments')->get());

		return $assessments;
	}

	/**
	 * Get all dimensions from the master database.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getDimensions()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$dimensions = collect($db->getConnection()->table('dimensions')->get());

		return $dimensions;
	}

	/**
	 * Get a specific assessment from the master database.
	 *
	 * @param $assessment_id
	 * @return mixed
	 */
	public function getAssessment($assessment_id)
	{
		return $this->getAssessments()->where('id', (int)$assessment_id)->first();
	}

	public function clientsCount()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return null;

		try {
			$clients = $db->getConnection()->table('clients')->get();
			return count($clients);
		}
		catch(\Exception $e) {
			return null;
		}
	}

	/**
	 * Get all the clients that belong to this reseller.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function clients($clients = null)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return null;

		try {
			if (! $clients)
				$clients = $db->getConnection()->table('clients')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		$assignments = $this->assignments();
		$assessments = $this->assessments();
		$answers = $this->answers();

		foreach ($clients as $i => $client)
		{
			// Format dates
			$clients[$i]->created_at = new Carbon($client->created_at);
			$clients[$i]->updated_at = new Carbon($client->updated_at);

			// Get users
			$clients[$i]->users = $this->users($clients, $assignments, $assessments, $answers)->where('client_id', $client->id);

			// Get assignments count
			$assignmentsCount = 0;
			foreach ($clients[$i]->users as $user)
				$assignmentsCount += count($user->assignments);
			$clients[$i]->assignmentsCount = $assignmentsCount;

			// Get completed assignments count
			$completedAssignmentsCount = 0;
			foreach ($clients[$i]->users as $user)
				if ($user->assignments)
					$completedAssignmentsCount += count($user->assignments->where('completed', 1));
			$clients[$i]->completedAssignmentsCount = $completedAssignmentsCount;
		}

		return collect($clients);
	}

	/**
	 * Get a specific client belonging to this reseller.
	 *
	 * @return \Illuminate\Support\Collection|null
	 */
	public function client($id)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return null;

		try {
			$clients = $db->getConnection()->table('clients')->where('id', (int)$id)->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return $this->clients($clients)->first();
	}

	public function usersCount()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return null;

		try {
			$users = $db->getConnection()->table('users')->get();
			return count($users);
		}
		catch(\Exception $e) {
			return null;
		}
	}

	/**
	 * Get all the users that belong to this reseller.
	 *
	 * @param null $clients
	 * @param null $assignments
	 * @param null $assessments
	 * @param null $answers
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function users($clients = null, $assignments = null, $assessments = null, $answers = null)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$users = $db->getConnection()->table('users')
				->select('*', 'users.id', 'users.name', 'role_user.id as role_user_id', 'roles.id as role_id', 'roles.name as role_name')
				->join('role_user', 'role_user.id', '=', 'users.id')
				->join('roles', 'roles.id', '=', 'role_user.role_id')
				->get();
		}
		catch(\Exception $e) {
			return null;
		}

		// Save time by checking if clients query was already passed
		if (! $clients)
			$clients = $db->getConnection()->table('clients')->get();
		$clients = collect($clients);

		// Get client
		foreach ($users as $i => $user)
			$users[$i]->client = $clients->where('id', $user->client_id)->first();

		// Get assignments
		if (! $assignments)
			$assignments = $this->assignments();
		if (! $assessments)
			$assessments = $this->assessments();
		if (! $answers)
			$answers = $this->answers();
		foreach ($users as $i => $user)
			$users[$i]->assignments = $this->assignmentsForUser($user->id, $assignments, $assessments, $answers);

		return collect($users);
	}

	/**
	 * Get all the Admin users that belong to this reseller.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function adminUsers()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		if (! $db->getConnection()->getSchemaBuilder()->hasTable('users'))
			return null;

		$users = collect($db->getConnection()->table('users')
			->select('*', 'users.id', 'users.name', 'role_user.id as role_user_id', 'roles.id as role_id', 'roles.name as role_name')
			->join('role_user', 'role_user.id', '=', 'users.id')
			->join('roles', 'roles.id', '=', 'role_user.role_id')
			->get())->filter(function($user) {
				$user->reseller_id = $this->id;
				return $user->slug == 'reseller';
			}
		);

		return $users;
	}

	/**
	 * Get a specific user that belongs to this reseller.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function user($id)
	{
		return $this->users()->where('id', (int)$id)->first();
	}

	public function assignments()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$assignments = $db->getConnection()->table('assignments')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return collect($assignments);
	}

	/**
	 * Get all assignments for a specific user.
	 *
	 * @param $userId
	 * @param null $assignments
	 * @param null $assessments
	 * @param null $answers
	 * @return bool|null
	 */
	public function assignmentsForUser($userId, $assignments = null, $assessments = null, $answers = null)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			if (! $assignments)
				$assignments = collect($db->getConnection()->table('assignments')->where('user_id', $userId)->get());
			else
				$assignments = $assignments->where('user_id', $userId);
		}
		catch(\Exception $e) {
			return null;
		}

		if ($assignments->isEmpty())
			return null;

		if (! $assessments)
			$assessments = $this->assessments();

		if (! $answers)
			$answers = $this->answers();

		foreach ($assignments as $i => $assignment)
		{
			// Format dates
			$assignments[$i]->created_at = new Carbon($assignment->created_at);
			$assignments[$i]->updated_at = new Carbon($assignment->updated_at);
			$assignments[$i]->started_at = new Carbon($assignment->started_at);
			$assignments[$i]->completed_at = new Carbon($assignment->completed_at);
			$assignments[$i]->expires = new Carbon($assignment->expires);

			// Get assessment info
			$assignment->assessment = $assessments->where('id', $assignment->assessment_id)->first();

			// Get answers
			$assignment->answers = $answers->where('assignment_id', $assignment->id);
		}

		return collect($assignments);
	}

	/**
	 * Get all assessments for this reseller.
	 *
	 * @return bool|\Illuminate\Support\Collection|null
	 */
	public function assessments()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$assessments = $db->getConnection()->table('assessments')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		$questions = $this->questions();

		// Get questions
		foreach ($assessments as $i => $assessment)
			$assessments[$i]->questions = $questions->where('assessment_id', (int)$assessment->id);

		return collect($assessments);
	}

	public function questions()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$questions = $db->getConnection()->table('questions')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return collect($questions);
	}

	/**
	 * Get a specific assessment for this reseller.
	 *
	 * @param $id
	 * @return mixed
	 */
	public function assessment($id)
	{
		return $this->assessments()->where('id', (int)$id)->first();
	}

	public function answers()
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$answers = $db->getConnection()->table('answers')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return collect($answers);
	}

	/**
	 * Get all answers for a specific assignment.
	 *
	 * @param $id
	 * @return bool|\Illuminate\Support\Collection|null
	 */
	public function answersForAssignment($id)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$db->getConnection()->table('answers')->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return collect($db->getConnection()->table('answers')->where('assignment_id', (int)$id)->get());
	}

	/**
	 * Get all questions for a specific assessment.
	 *
	 * @param $id
	 * @return bool|\Illuminate\Support\Collection|null
	 */
	public function questionsForAssessment($id)
	{
		$db = $this->connectToDatabase();
		if (!$db)
			return false;

		try {
			$questions = $db->getConnection()->table('questions')->where('assessment_id', (int)$id)->get();
		}
		catch(\Exception $e) {
			return null;
		}

		return collect($questions);
	}

	/**
	 * Get all user roles, filtered for Resellers.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function roles()
	{
		$roles = Role::where('slug', '!=', 'admin')->get();
		$roles->first()->name = 'Administrator';

		return $roles;
	}

	/**
	 * Get all the jobs that belong to this reseller.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function jobs()
	{
//		$db = $this->connectToDatabase();
//		if (!$db)
//			return null;
//
//		if (! $db->getConnection()->getSchemaBuilder()->hasTable('clients'))
//			return null;
//
//		$clients = $db->getConnection()->table('clients')->get();
//		foreach ($clients as $i => $client)
//		{
//			// Format dates
//			$clients[$i]->created_at = new Carbon($client->created_at);
//			$clients[$i]->updated_at = new Carbon($client->updated_at);
//
//			// Get users
//			$clients[$i]->users = $this->users()->where('client_id', $client->id);
//
//			// Get assignments count
//			$assignmentsCount = 0;
//			foreach ($clients[$i]->users as $user)
//				$assignmentsCount += count($user->assignments);
//			$clients[$i]->assignmentsCount = $assignmentsCount;
//
//			// Get completed assignments count
//			$completedAssignmentsCount = 0;
//			foreach ($clients[$i]->users as $user)
//				$completedAssignmentsCount += count($user->assignments->where('completed', 1));
//			$clients[$i]->completedAssignmentsCount = $completedAssignmentsCount;
//		}
//
//		return collect($clients);

		$jobs = Job::where('reseller_id', $this->id)->get();

		return $jobs;
	}

	public function jobTemplates()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$jobs = collect($db->getConnection()->table('jobs')->where('reseller_id', $this->id)->get());

		return $jobs;
	}

	public function jobTemplate($id)
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$job = $db->getConnection()->table('jobs')->where([
			'reseller_id' => $this->id,
			'id' => $id
		])->first();

		return $job;
	}

	/**
	 * Get the database host of this reseller.
	 *
	 * @return mixed|string
	 */
	public function getDbHost()
	{
		if ($this->db_host)
			return $this->db_host;

		if (env('APP_ENV') == 'staging')
			return $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];

		return env('DB_HOST', 'localhost');
	}

	/**
	 * Get the database username of this reseller.
	 *
	 * @return mixed
	 */
	public function getDbUser()
	{
		if ($this->db_user)
			return $this->db_user;

		if (env('APP_ENV') == 'staging')
			return $_SERVER['RDS_USERNAME'];

		if (env('APP_ENV') == 'local')
			return 'admaster';

		return env('DB_USERNAME', 'root');
	}

	/**
	 * Get the database password of this reseller.
	 *
	 * @return mixed
	 */
	public function getDbPass()
	{
		if ($this->db_pass)
			return $this->db_pass;

		if (env('APP_ENV') == 'staging')
			return $_SERVER['RDS_PASSWORD'];

		if (env('APP_ENV') == 'local')
			return 'fater1ft';

		return env('DB_PASSWORD', '');
	}

	/**
	 * Get the database name of this reseller.
	 *
	 * @return mixed
	 */
	public function getDbName()
	{
		if ($this->db_name)
			return $this->db_name;

		if (env('APP_ENV') == 'staging')
			return $_SERVER['RDS_DB_NAME'];

		return env('DB_DATABASE', 'forge');
	}

	/**
	 * Check the status of the database.
	 *
	 * @return string available/creating/backing-up/failed
	 */
	public function checkDbStatus()
	{
		// If local, return available
//		if (env('APP_ENV') != 'staging')
//		{
//			if ($this->db_host)
//				return 'cannot-access-aws-host';
//
//			return 'available';
//		}

		// If there is no instance, then something went wrong
		if (!$this->db_instance)
			return 'failed';

		// If the host is set, that means the database is already created
		if ($this->db_host)
			$status = 'available';

		// Otherwise
		else
		{
			// Check the status through aws
			$rds = new RdsClient(config('aws'));
			$instance = $rds->describeDBInstances(['DBInstanceIdentifier' => $this->db_instance]);
			$status = $instance['DBInstances'][0]['DBInstanceStatus'];
		}

		// Check if available, but database host hasn't been set yet
		if ($status == 'available')
		{
			// If host is not set
			if (!$this->db_host)
			{
				// Set the host
				$this->db_host = $instance['DBInstances'][0]['Endpoint']['Address'] . ':' . $instance['DBInstances'][0]['Endpoint']['Port'];
				$this->save();
			}

			// Connect to the database
			$db = new DBConnection([
				'host'     => $this->getDbHost(),
				'database' => $this->getDbName(),
				'username' => $this->getDbUser(),
				'password' => $this->getDbPass(),
			]);

			try {
				$db->getConnection()->table('users')->get();
			}

			// If there are no tables on the database yet
			catch(\Exception $e)
			{
				// Migrate and seed the database
				Artisan::call('migrate', [
					'--database' => $db->getConnection()->getName()
				]);
				Artisan::call('db:seed', [
					'--database' => $db->getConnection()->getName(),
					'--class' => 'DatabaseSeeder'
				]);

				// Drop any unnecessary tables
				foreach ($this->blacklistedTables as $tableName)
					$db->getConnection()->statement('DROP TABLE '.$tableName);
			}
		}

		return $status;
	}

	/**
	 * Returns true if the database for this reseller is not ready to be accessed.
	 *
	 * @return bool
	 */
	public function dbNotReady()
	{
		$status = $this->db_status;

		if (! $status)
			$status = $this->checkDbStatus();

		$statusCodes = [
			'creating',
			'backing-up',
			'deleting',
			'maintenance',
			'modifying',
			'rebooting',
			'renaming',
			'resetting-master-credentials',
			'upgrading'
		];

		if (in_array($status, $statusCodes))
			return true;

		return false;
	}

	/**
	 * Returns true if the database for this reseller has encountered some kind of error.
	 *
	 * @return bool
	 */
	public function dbError()
	{
		$status = $this->db_status;

		if (! $status)
			$status = $this->checkDbStatus();

		$statusCodes = [
			'failed',
			'inaccessible-encryption-credentials',
			'incompatible-credentials',
			'incompatible-network',
			'incompatible-option-group',
			'incompatible-parameters',
			'incompatible-restore',
			'restore-error',
			'storage-full',
			'cannot-access-aws-host',
		];

		if (in_array($status, $statusCodes))
			return true;

		return false;
	}

	/**
	 * Returns true if the database for this Reseller has the same migrations and tables as the master database.
	 * Returns an array list of items that are missing if the database is out of sync with the master database.
	 *
	 * @return array|bool|null
	 */
	public function checkDbUpdated()
	{
		// Get our schema for the master database
		$schema = 'aoe';
		if (env('APP_ENV') == 'staging')
			$schema = 'ebdb';

		// Get all the migrations we're supposed to have
		$masterDb = $this->connectToMasterDatabase();
		$masterMigrations = get_property_list($masterDb->getConnection()->select('SELECT migration FROM migrations'), 'migration');

		// Get all the tables we're supposed to have
		$query = $masterDb->getConnection()->select('SELECT TABLE_NAME FROM information_schema.tables WHERE table_type="base table" AND table_schema="'.$schema.'"');
		$tables = get_property_list($query, 'TABLE_NAME');

		// Get all the columns we're supposed to have for each table
		$masterTables = [];
		foreach ($tables as $i => $table)
		{
			$query = $masterDb->getConnection()->select('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name="'.$table.'" AND table_schema="'.$schema.'"');
			$columns = get_property_list($query, 'COLUMN_NAME');
			$masterTables[$table] = $columns;
		}

		// Now connect to our other database
		$db = $this->connectToDatabase();
		if (! $db)
			return ['Database could not be accessed'];

		// And get our migrations
		$migrations = get_property_list($db->getConnection()->select('SELECT migration FROM migrations'), 'migration');

		// Now get our database tables and columns
		$query = $db->getConnection()->select('SELECT TABLE_NAME FROM information_schema.tables WHERE table_type="base table" AND table_schema="'.$this->db_name.'"');
		$rawTables = get_property_list($query, 'TABLE_NAME');
		$tables = [];
		foreach ($rawTables as $i => $table)
		{
			$query = $db->getConnection()->select('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name="'.$table.'" AND table_schema="'.$this->db_name.'"');
			$columns = get_property_list($query, 'COLUMN_NAME');
			$tables[$table] = $columns;
		}

		// Now run the check
		$updated = true;
		$missing = [];

		// Count how many migrations we are behind
		$behind = count($masterMigrations) - count($migrations);
		if ($behind)
		{
			$missing[] = $behind . ' migrations behind master:';

			// Check which migrations specifically we're missing
			foreach ($masterMigrations as $migration)
				if (!in_array($migration, $migrations))
					$missing[] = $migration;
			$missing[] = '';
		}

		// Check if there are missing tables
		foreach ($masterTables as $tableName => $columns)
		{
			if (!array_key_exists($tableName, $tables))
			{
				if (!in_array($tableName, $this->blacklistedTables))
				{
					$updated = false;
					$missing[] = 'Missing '.$tableName.' table';
				}
				continue;
			}

			// Check for missing columns too
			$missingColumns = [];
			foreach ($columns as $column)
			{
				if (!in_array($column, $tables[$tableName]))
					$missingColumns[] = $column;
			}

			if ($missingColumns)
			{
				$updated = false;
				$missing[] = $tableName.' table is missing columns: '.implode(', ', $missingColumns);
			}
		}

		if ($updated)
			return true;

		return $missing;
	}

	/**
	 * Re-migrate this reseller's database.
	 *
	 * @return null
	 */
	public function updateDatabase()
	{
		$db = $this->connectToDatabase();
		if (! $db)
			return false;

		// Migrate the database
		Artisan::call('migrate', [
			'--database' => $db->getConnection()->getName()
		]);

		// Drop any unnecessary tables
		foreach ($this->blacklistedTables as $tableName)
			if ($db->getConnection()->getSchemaBuilder()->hasTable($tableName))
				$db->getConnection()->statement('DROP TABLE '.$tableName);
	}

	/**
	 * Connect to this Reseller's database
	 *
	 * @return DBConnection
	 */
	public function connectToDatabase()
	{
		if ($this->dbNotReady() || $this->dbError())
			return null;

		return new DBConnection([
			'host'     => $this->getDbHost(),
			'database' => $this->getDbName(),
			'username' => $this->getDbUser(),
			'password' => $this->getDbPass(),
		]);
	}

	/**
	 * Connect to the Master database
	 *
	 * @return DBConnection
	 */
	public function connectToMasterDatabase()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		return new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);
	}
}
