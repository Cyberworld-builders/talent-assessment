<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jaq extends Model
{
	/**
	 * The columns allowed to be filled.
	 *
	 * @var array
	 */
	protected $fillable = [
		'analysis_id',
		'user_id',
		'name',
		'position',
		'job_code',
		'department_name',
		'location',
		'supervisor_name',
		'supervisor_title',
		'position_desc',
		'tasks',
		'ksas',
		'ksa_linkages',
		'min_education',
		'preferred_education',
		'min_experience',
		'preferred_experience',
		'additional_requirements',
		'ratings',
		'sent',
		'completed',
		'sent_at',
		'completed_at'
	];
	protected $dates = ['sent_at', 'completed_at'];

	/**
	 * Get the user to which this JAQ belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the analysis to which this JAQ belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function analysis()
	{
		return $this->belongsTo('App\Analysis');
	}

	/**
	 * Serialize users when saved in storage.
	 *
	 * @param $value
	 */
	public function setTasksAttribute($value)
	{
		$this->attributes['tasks'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getTasksAttribute()
	{
		return unserialize($this->attributes['tasks']);
	}

	/**
	 * Serialize users when saved in storage.
	 *
	 * @param $value
	 */
	public function setKsasAttribute($value)
	{
		$this->attributes['ksas'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getKsasAttribute()
	{
		return unserialize($this->attributes['ksas']);
	}

	/**
	 * Serialize users when saved in storage.
	 *
	 * @param $value
	 */
	public function setKsaLinkagesAttribute($value)
	{
		$this->attributes['ksa_linkages'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getKsaLinkagesAttribute()
	{
		return unserialize($this->attributes['ksa_linkages']);
	}

	/**
	 * Serialize users when saved in storage.
	 *
	 * @param $value
	 */
	public function setRatingsAttribute($value)
	{
		$this->attributes['ratings'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getRatingsAttribute()
	{
		return unserialize($this->attributes['ratings']);
	}
}
