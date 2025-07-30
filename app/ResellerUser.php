<?php

namespace App;

use Bican\Roles\Models\Role;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Bican\Roles\Traits\HasRoleAndPermission;
use Bican\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use Illuminate\Support\Facades\DB;
use Route;

class ResellerUser extends Model implements AuthenticatableContract,
	CanResetPasswordContract,
	HasRoleAndPermissionContract
{
	use Authenticatable, CanResetPassword, HasRoleAndPermission;

//	public function __construct(array $attributes = array())
//	{
//		parent::__construct($attributes);
//
//		// Set the database connection
//		$resellerId = Route::current()->parameters()['id'];
//		$reseller = Reseller::findOrFail($resellerId);
//
//		$this->setConnection(DBConnection::getDatabaseConfig($reseller->db_name));
//	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aoe_red_brick_company.users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'name', 'email', 'password', 'client_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'last_login_at', 'completed_profile', 'accepted_terms', 'accepted_at', 'accepted_signature'];

	/**
	 * Ability to retrive user articles.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function articles()
	{
		return $this->hasMany('App\Article');
	}

	/**
	 * Get all assessments that belong to this user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function assessments()
	{
		return $this->hasMany('App\Assessment');
	}

	/**
	 * Get all translations that belong to this user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function translations()
	{
		return $this->hasMany('App\Translation');
	}

	/**
	 * Get all assignments assigned to this user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function assignments()
	{
		return $this->hasMany('App\Assignment');
	}

	/**
	 * Get all JAQs for this user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function jaqs()
	{
		return $this->hasMany('App\Jaq');
	}

	public function completedAssignments()
	{
		return $this->assignments->filter(function($assignment) {
			return $assignment->completed;
		});
	}

	public function allAssignmentsCompleted()
	{
		return $this->completedAssignments()->count() == $this->assignments->count();
	}

	/**
	 * The number of assessments the user has completed for a specific job.
	 *
	 * @param $jobId
	 * @return int
	 */
	public function assessmentsCompletedForJob($jobId)
	{
		$job = Job::findOrFail($jobId);
		$assessmentIds = $job->assessments;

		// Find the assignments corresponding to the assessments for the user
		$assignments = [];
		foreach ($assessmentIds as $assessmentId)
		{
			$assignment = Assignment::where([
				'assessment_id' => $assessmentId,
				'user_id' => $this->id,
			])->first();
			if ($assignment)
				array_push($assignments, $assignment);
		}
		$assignments = collect($assignments);

		// Count how many of them are completed
		$i = 0;
		if (! $assignments->isEmpty())
		{
			foreach ($assignments as $assignment)
				if ($assignment->completed)
					$i++;
		}

		return $i;
	}

	/**
	 * Check to see if all the assessments have been completed for a specific job.
	 *
	 * @param $jobId
	 * @return bool
	 */
	public function allAssessmentsCompletedForJob($jobId)
	{
		$job = Job::find($jobId);

		if ($this->assessmentsCompletedForJob($jobId) == count($job->assessments))
			return true;

		return false;
	}

	/**
	 * Get the client to which this user belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo('App\Client');
	}

	/**
	 * Get the client to which this user belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function resellerClient($reseller)
	{
		$db = new DBConnection(['database' => $reseller->db_name]);
		$client = $db->getConnection()->table('clients')->where('id', $this->client_id)->first();

		if (! $client)
			return false;

		return $client;
	}

	/**
	 * Get user's role.
	 *
	 * @return static|mixed
	 */
	public function role()
	{
		$roles = Role::all();

		foreach ($roles as $role)
		{
			if ($this->is($role->slug))
				return $role;
		}
	}

	/**
	 * Get the group role of the user.
	 *
	 * @param $group_id
	 * @return mixed
	 */
	public function groupRole($group_id)
	{
		$groupRoles = DB::select('select * from group_role_user where user_id = :id and group_id = :gid', ['id' => $this->id, 'gid' => $group_id]);

		return $groupRoles[0];
	}

	/**
	 * Get all the jobs the user is applied for.
	 *
	 * @return bool|\Illuminate\Support\Collection
	 */
	public function jobs()
	{
		$jobUsers = DB::table('job_users')->where('user_id', $this->id)->get();

		if (empty($jobUsers))
			return false;

		$jobsArray = [];
		foreach ($jobUsers as $jobUser)
		{
			$job = Job::find($jobUser->job_id);
			array_push($jobsArray, $job);
		}

		$jobs = collect($jobsArray);

		return $jobs;
	}

	/**
	 * Check if user is viable for a specific job.
	 *
	 * @param $id
	 * @return boolean
	 */
	public function isViableForJob($id)
	{
		$jobUser = DB::table('job_users')->where([
			'user_id' => $this->id,
			'job_id' => $id,
		])->first();

		return $jobUser->viable;
	}

	public function groups()
	{
//        $groupsArray = [];
//        $results = DB::select('select * from group_role_user where user_id = :id', ['id' => $this->id]);
//
//        foreach ($results as $result)
//            array_push($groupsArray, Group::Find($result->group_id));
//
//        $groups = collect($groupsArray);

		$groupsArray = [];
		$clientGroups = Group::where('client_id', $this->client_id)->get();

		if (! $clientGroups)
			return false;

		foreach ($clientGroups as $group)
		{
			foreach ($group->users as $groupUser)
			{
				if ($this->id == $groupUser['id'])
					array_push($groupsArray, $group);
			}
		}

		$groups = collect($groupsArray);

		return $groups;
	}

	/**
	 * Get the research questions that this user filled out.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function research()
	{
		return $this->hasOne('App\Research');
	}

	/**
	 * Get the research questions that this user filled out.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function language()
	{
		return Language::find($this->language_id);
	}

	/**
	 * Get all answers this user submitted for a specific assignment.
	 *
	 * @param $assignment_id
	 */
	public function answersFor($assignment_id)
	{
		$assignment = Assignment::where(['id' => $assignment_id, 'user_id' => $this->id])->first();
	}

	/**
	 * Returns whether this user is assigned to a specific assignment.
	 *
	 * @param Assignment $assignment
	 * @return bool
	 */
	public function owns(Assignment $assignment)
	{
		return $this->id == $assignment->user_id;
	}

	/**
	 * Generate a new password for an existing user.
	 *
	 * @return string
	 */
	public function generate_password_for_user()
	{
		return $this->generate_password($this->name, $this->username);
	}

	/**
	 * Generate a new password from the given name and email.
	 *
	 * @param $name
	 * @param $username
	 * @return string
	 */
	public function generate_password($name, $username)
	{
		$password = substr(str_replace('=', '', strrev(base64_encode(substr($username, 0, 4) . $name))), 0, 8);
		$password = str_replace("1", "2", $password);
		$password = str_replace("l", "L", $password);
		$password = str_replace("I", "T", $password);

		return $password;
	}

	/**
	 * Generate username.
	 *
	 * @param $prefix
	 * @return string
	 */
	public function generate_username($prefix)
	{
		$prefix = preg_replace('/[^A-Za-z0-9\-]/', '', $prefix);
		$prefix = str_replace(' ', '', $prefix);
		$prefix = strtolower($prefix);
		if ($prefix)
			$prefix .= '_';

		return $prefix . substr(md5(microtime()), rand(0, 26), 5);
	}

	/**
	 * Format the users into an array for a select element.
	 *
	 * @return array
	 */
	public static function getSelectFormattedArray()
	{
		$array = [];
		foreach (User::all() as $user)
			$array[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';

		return $array;
	}

	/**
	 * Format the users into an array for a select element.
	 *
	 * @param $clientId
	 * @return array
	 */
	public static function getSelectFormattedArrayForClient($clientId)
	{
		$client = Client::find($clientId);
		$array = [];
		foreach ($client->users as $user)
			$array[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';

		return $array;
	}

	public function getUserTargetRelation($target)
	{
		$relation = false;

		// Get the users of the group in which target is a target of
		$groupUsers = $this->groups()->filter(function($group) use ($target) {
			return $group->target_id == $target->id;
		})->first()->users;

		// Find the user out of that group and get his position
		foreach ($groupUsers as $user)
		{
			if ($user['id'] == $this->id) {
				$relation = $user['position'];
				break;
			}
		}

		return $relation;
	}

	public function getJaqForAnalysis($analysisId)
	{
		$analysis = Analysis::find($analysisId);

		if (!$analysis)
			return false;

		$jaq = Jaq::where([
			'user_id' => $this->id,
			'analysis_id' =>  $analysis->id
		])->first();

		if (!$jaq)
			return false;

		return $jaq;
	}

	public function has($roleSlug)
	{
		$role = Role::where('slug', $roleSlug);

		if (! $role)
			return false;

		$userRole = DB::table('role_user')->where([
			'user_id' => $this->id,
			'role_id' => $role->id
		])->first();

		if ($userRole)
			return true;

		return false;
	}
}
