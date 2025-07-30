<?php

namespace App;

use App;
use Config;
use DB;

class DBConnection
{
	/**
	 * The name of the database we're connecting to.
	 *
	 * @var string $database
	 */
	protected $database;

	/**
	 * The database connection.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * Create a new database connection.
	 *
	 * @param  array $options
	 */
	public function __construct($options = null)
	{
		// Set the database
		$database = $options['database'];
		$this->database = $database;

		// Figure out the driver and get the default configuration for the driver
		$driver  = isset($options['driver']) ? $options['driver'] : Config::get("database.default");
		$default = Config::get("database.connections.$driver");

		// Loop through our default array and update options if we have non-defaults
		foreach($default as $item => $value)
			$default[$item] = isset($options[$item]) ? $options[$item] : $default[$item];

		// Set the temporary configuration
		Config::set("database.connections.$database", $default);

		// Create the connection
		$this->connection = DB::connection($database);
	}

	/**
	 * Get the connection.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Get a table from the current connection.
	 *
	 * @var    string $table
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function getTable($table = null)
	{
		return $this->getConnection()->table($table);
	}

	/**
	 * Add a new configuration for a specific database to the app config at run-time.
	 *
	 * @param $dbName
	 */
	static function getDatabaseConfig($dbName)
	{
		// Just get access to the config
		$config = App::make('config');

		// Will contain the array of connections that appear in our database config file
		$connections = $config->get('database.connections');

		// This line pulls out the default connection by key (by default it's `mysql`)
		$defaultConnection = $connections[$config->get('database.default')];

		// Now we simply copy the default connection information to our new connection
		$newConnection = $defaultConnection;

		// Override the database name
		$newConnection['database'] = $dbName;

		// This will add our new connection to the run-time configuration for the duration of the request
		App::make('config')->set('database.connections.'.$dbName, $newConnection);
	}

	static function getConnectionToMasterDatabase()
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

		return $db;
	}
}