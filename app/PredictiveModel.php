<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PredictiveModel extends Model
{
	protected $fillable = [
		'job_id',
		'name',
		'assessments',
		'model',
		'filename',
		'factors',
		'configured',
	];

	/**
	 * Get the job to which this weight belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function job()
	{
		return $this->belongsTo('App\Job');
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
	 * Serialize assessments when saved in storage.
	 *
	 * @param $value
	 */
	public function setFactorsAttribute($value)
	{
		$this->attributes['factors'] = serialize($value);
	}

	/**
	 * Un-serialize assessments when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getFactorsAttribute()
	{
		return unserialize($this->attributes['factors']);
	}
}
