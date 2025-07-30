<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Job extends Model
{
	protected $fillable = [
		'name',
		'slug',
		'description',
		'client_id',
		'active',
		'assessments',
		'reseller_id',
		'job_template_id',
	];

	/**
	 * Get the client to which this job belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo('App\Client');
	}

	/**
	 * Get the weights that pertain to this job.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function weights()
	{
		return $this->hasMany('App\Weight');
	}

	public function getWeights()
	{
		if (Auth::user()->is('reseller|client') && session('reseller'))
		{
			$db = DBConnection::getConnectionToMasterDatabase();
			return collect($db->getConnection()->table('weights')->where('job_id', $this->job_template_id)->get());
		}

		return $this->weights;
	}

	/**
	 * Get the predictive models that pertain to this job.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function models()
	{
		return $this->hasMany('App\PredictiveModel');
	}

	public function getModels()
	{
		if (Auth::user()->is('reseller|client') && session('reseller'))
		{
			$db = DBConnection::getConnectionToMasterDatabase();
			return collect($db->getConnection()->table('predictive_models')->where('job_id', $this->job_template_id)->get());
		}

		return $this->models;
	}

	/**
	 * Get all applicants for this job.
	 */
	public function applicants()
	{
		// Get job users relationships
		$jobUsers = DB::table('job_users')->where('job_id', $this->id)->get();

		// Create array of users from the relationships, adding the viable attribute to them
		$users = [];
		foreach ($jobUsers as $jobUser)
		{
			$user = User::find($jobUser->user_id);
			$user->viable = $jobUser->viable;
			array_push($users, $user);
		}

		// Make it a collection
		$users = collect($users);

		return $users;
	}

	public function getAssessments()
	{
		$assessmentsArray = [];
		foreach ($this->assessments as $assessmentId)
		{
			$assessment = Assessment::find($assessmentId);
			array_push($assessmentsArray, $assessment);
		}
		$assessments = collect($assessmentsArray);

		return $assessments;
	}

	/**
	 * Get viable applicants for this job only.
	 */
	public function viableApplicants()
	{
		// Get job users relationships
		$jobUsers = DB::table('job_users')->where(['job_id' => $this->id, 'viable' => true])->get();

		// Create array of users from the relationships, adding the viable attribute to them
		$users = [];
		foreach ($jobUsers as $jobUser)
		{
			$user = User::find($jobUser->user_id);
			$user->viable = $jobUser->viable;
			array_push($users, $user);
		}

		// Make it a collection
		$users = collect($users);

		return $users;
	}

	/**
	 * Serialize anchors when saved in storage.
	 *
	 * @param $value
	 */
	public function setAssessmentsAttribute($value)
	{
		$this->attributes['assessments'] = serialize($value);
	}

	/**
	 * Un-serialize anchors when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getAssessmentsAttribute()
	{
		return unserialize($this->attributes['assessments']);
	}

	public function hasReports()
	{
		$reports = $this->reports();

		if ($reports->isEmpty())
			return false;

		return true;
	}

	public function reports()
	{
		return Report::where([
			'job_id' => $this->id,
			'client_id' => $this->client_id,
			'enabled' => 1
		])->get()->filter(function($report)
		{
			// See if the assessments required for the job match any reports
			$assessmentsFound = [];
			foreach ($this->assessments as $assessmentId)
			{
				$assessmentsFound[$assessmentId] = false;
				if (in_array($assessmentId, \GuzzleHttp\json_decode($report->assessments)))
					$assessmentsFound[$assessmentId] = true;
			}

			// If not all the assessments match, don't return the report
			if (in_array(false, $assessmentsFound))
				return false;

			return true;
		});
	}
}
