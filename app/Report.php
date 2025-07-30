<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
	protected $table = 'new_reports';

	/**
	 * The columns allowed to be filled.
	 *
	 * @var array
	 */
	protected $fillable = [
		'assessments',
		'view',
		'fields',
		'name',
		'job_id',
		'client_id',
		'score_method',
		'weights',
		'divisions',
		'model',
		'model_filename',
		'model_factors',
		'model_configured',
		'scores',
		'show_fit',
		'show_item_data',
	];

	/**
	* Remove timestamps for this resource.
	*
	* @var bool
	*/
	public $timestamps = false;

	public function job()
	{
		if ($this->job_id)
			return Job::find($this->job_id);

		return false;
	}

	public function customized()
	{
		if ($this->fields)
			return true;

		return false;
	}

	public function getAssessments()
	{
		$assessments = [];
		if ($this->assessments and \GuzzleHttp\json_decode($this->assessments))
		{
			foreach (\GuzzleHttp\json_decode($this->assessments) as $assessmentId)
			{
				$assessment = Assessment::find($assessmentId);

				if (! $assessment)
					continue;

				$assessments[] = $assessment;
			}
		}

		return $assessments;
	}

	/**
	 * JSON encode model when saved in storage.
	 *
	 * @param $value
	 */
	public function setModelAttribute($value)
	{
		$this->attributes['model'] = json_encode($value);
	}

	/**
	 * JSON decode model when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getModelAttribute()
	{
		return json_decode($this->attributes['model']);
	}

	/**
	 * Serialize assessments when saved in storage.
	 *
	 * @param $value
	 */
	public function setModelFactorsAttribute($value)
	{
		$this->attributes['model_factors'] = json_encode($value);
	}

	/**
	 * Un-serialize assessments when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getModelFactorsAttribute()
	{
		return json_decode($this->attributes['model_factors']);
	}
}
