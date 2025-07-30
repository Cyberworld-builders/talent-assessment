<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
	protected $fillable = [
		'name',
		'address',
		'logo',
		'background',
		'assessments',
		'require_profile',
		'require_research',
        'whitelabel',
		'primary_color',
		'accent_color',
	];

	/**
	 * Role belongs to many users.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->hasMany('App\User');
	}

	/**
	 * Get the groups that belong to this client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function groups()
	{
		return $this->hasMany('App\Group');
	}

	/**
	 * Get the group roles that belong to this client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function groupRoles()
	{
		return $this->hasMany('App\GroupRole');
	}

	/**
	 * Get the jobs that belong to this client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function jobs()
	{
		return $this->hasMany('App\Job');
	}

	/**
	 * Get the analyses that belong to this client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function analyses()
	{
		return $this->hasMany('App\Analysis');
	}

	/**
	 * Get the custom reports that belong to this client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function reports()
	{
		return $this->hasMany('App\ClientReport');
	}

	/**
	 * Get a count of how many assessments the users of this client completed.
	 *
	 * @return bool|int
	 */
	public function assessmentsCompleted()
	{
		$count = 0;
		$users = $this->users;

		if ($users->isEmpty())
			return false;

		foreach ($users as $user) {
			$completed = $user->assignments()->where('completed', 1)->get()->count();
			$count += $completed;
		}

		return $count;
	}

	/**
	 * Get a total count of how many assessments this client has.
	 *
	 * @return bool|int
	 */
	public function assignments()
	{
		$count = 0;
		$users = $this->users;

		if ($users->isEmpty())
			return false;

		foreach ($users as $user) {
			$assignments = $user->assignments()->count();
			$count += $assignments;
		}

		return $count;
	}

	/**
	 * Get a count of how many answers the users of this client have answered.
	 *
	 * @return bool|int
	 */
	public function questionsAnswered()
	{
		$count = 0;
		$users = $this->users;

		if ($users->isEmpty())
			return false;

		foreach ($users as $user) {
			$answers = DB::table('answers')->where('user_id', $user->id)->count();
			$count += $answers;
		}

		return $count;
	}

	/**
	 * Get all users for this client that belong to any job.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function jobUsers()
	{
		$users = [];

		foreach ($this->jobs as $job)
			foreach ($job->applicants() as $applicant)
				$users[] = $applicant;

		return collect($users);
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
	 * Format the clients into an array for a select element.
	 *
	 * @return array
	 */
	public static function getSelectFormattedArray()
	{
		$array = [null => '---'];
		foreach (Client::all() as $client)
			$array[$client->id] = $client->name.' ('.$client->users->count().' users)';

		return $array;
	}
}
