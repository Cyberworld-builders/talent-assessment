<?php

namespace App;

use App\Http\Requests\Request;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class User extends Model implements AuthenticatableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, HasRoles;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'name', 'email', 'password', 'client_id', 'job_title', 'job_family', 'industry_id', 'language_id'];

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
     * Compatibility method for old bican/roles is() method
     * @param string $role
     * @return bool
     */
    public function is($role)
    {
        // Handle pipe-separated roles (e.g., 'admin|reseller')
        if (strpos($role, '|') !== false) {
            $roles = explode('|', $role);
            $roleMap = [
                'admin' => 'AOE Admin',
                'reseller' => 'Reseller',
                'client' => 'Client Admin',
                'user' => 'User'
            ];
            
            foreach ($roles as $r) {
                $r = trim($r);
                $mappedRole = $roleMap[$r] ?? $r;
                if ($this->hasRole($mappedRole)) {
                    return true;
                }
            }
            return false;
        }

        // Map old role names to new ones
        $roleMap = [
            'admin' => 'AOE Admin',
            'reseller' => 'Reseller',
            'client' => 'Client Admin',
            'user' => 'User'
        ];

        $newRole = $roleMap[$role] ?? $role;
        return $this->hasRole($newRole);
    }

    /**
     * Compatibility method for old bican/roles level() method
     * @return int
     */
    public function level()
    {
        $role = $this->roles()->first();
        if ($role) {
            return $role->level ?? 1;
        }
        return 1;
    }

    /**
     * Compatibility method for old bican/roles isReseller() method
     * @return bool
     */
    public function isReseller()
    {
        return $this->hasRole('Reseller');
    }

    /**
     * Compatibility method for old bican/roles isAdmin() method
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('AOE Admin');
    }

    /**
     * Compatibility method for old bican/roles isClient() method
     * @return bool
     */
    public function isClient()
    {
        return $this->hasRole('Client Admin');
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
//			$assignment = Assignment::where([
//				'assessment_id' => $assessmentId,
//				'user_id' => $this->id
//			])->first();

			$assignment = $this->lastCompletedAssignmentForJob($assessmentId, $job->id);

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
	 * The last completed assignment tied to a specific job.
	 * Expands to include non-job-specific assignments and non-complete assignments if cannot find the above first.
	 *
	 * @param $assessmentId
	 * @param $jobId
	 * @return Assignment
	 */
	public function lastCompletedAssignmentForJob($assessmentId, $jobId)
	{
		// Find specific to job, and complete
		$assignment = Assignment::where([
			'assessment_id' => $assessmentId,
			'user_id' => $this->id,
			'job_id' => $jobId,
			'completed' => 1
		])->orderBy('created_at', 'desc')->first();

		// Find specific to job
		if (! $assignment)
			$assignment = Assignment::where([
				'assessment_id' => $assessmentId,
				'user_id' => $this->id,
				'job_id' => $jobId,
			])->orderBy('created_at', 'desc')->first();

		// Find any that is complete
		if (! $assignment)
			$assignment = Assignment::where([
				'assessment_id' => $assessmentId,
				'user_id' => $this->id,
				'job_id' => null,
				'completed' => 1
			])->orderBy('created_at', 'desc')->first();

		// Find any
		if (! $assignment)
			$assignment = Assignment::where([
				'assessment_id' => $assessmentId,
				'user_id' => $this->id,
				'job_id' => null,
			])->orderBy('created_at', 'desc')->first();

		return $assignment;
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
     * Get the industry to which this user belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo('App\Industry');
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
        return $this->roles()->first();
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
     * Get the language that this user has selected.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('App\Language');
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
		$relation = 'Subordinate';
		$groupUsers = null;

		// See if target is self
		if ($this->id == $target->id)
			return 'Self';

		// Get the users of the group in which target is a target of
		if ($this->groups())
		{
			$group = $this->groups()->filter(function($group) use ($target) {
				return $group->target_id == $target->id;
			})->first();
			if ($group)
				$groupUsers = $group->users;
		}

		// Find the user out of that group and get his position
		if ($groupUsers)
		{
			foreach ($groupUsers as $user)
			{
				if ($user['id'] == $this->id) {
					$relation = $user['position'];
					break;
				}
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
		// Map old role names to new ones
		$roleMap = [
			'admin' => 'AOE Admin',
			'reseller' => 'Reseller',
			'client' => 'Client Admin',
			'user' => 'User'
		];

		$newRole = $roleMap[$roleSlug] ?? $roleSlug;
		return $this->hasRole($newRole);
	}
}
