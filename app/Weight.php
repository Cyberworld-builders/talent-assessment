<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
	protected $fillable = [
		'assessment_id',
		'weights',
		'divisions',
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
	 * Get the job to which this weight belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function assessment()
	{
		return $this->belongsTo('App\Assessment');
	}

	/**
	 * Serialize weights when saved in storage.
	 *
	 * @param $value
	 */
	public function setWeightsAttribute($value)
	{
		$this->attributes['weights'] = serialize($value);
	}

	/**
	 * Un-serialize weights when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getWeightsAttribute()
	{
		return unserialize($this->attributes['weights']);
	}

	/**
	 * Serialize divisions when saved in storage.
	 *
	 * @param $value
	 */
	public function setDivisionsAttribute($value)
	{
		$this->attributes['divisions'] = serialize($value);
	}

	/**
	 * Un-serialize divisions when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getDivisionsAttribute()
	{
		return unserialize($this->attributes['divisions']);
	}
}
