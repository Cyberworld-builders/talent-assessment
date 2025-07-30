<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Analysis extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analysis';

	/**
	 * The columns allowed to be filled.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'client_id',
		'users',
		'sent_at',
		'job_code',
		'department_name',
		'location',
		'position',
		'supervisor_title',
		'tasks',
		'ksas',
		'ratings'
	];

	/**
	 * Serialize users when saved in storage.
	 *
	 * @param $value
	 */
	public function setUsersAttribute($value)
	{
		$this->attributes['users'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getUsersAttribute()
	{
		return unserialize($this->attributes['users']);
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
	public function setPositionAttribute($value)
	{
		$this->attributes['position'] = serialize($value);
	}

	/**
	 * Un-serialize users when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getPositionAttribute()
	{
		return unserialize($this->attributes['position']);
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

	/**
	 * Get the client to which this analysis belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo('App\Client');
	}

	/**
	 * Get all JAQs associated with this analysis.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function jaqs()
	{
		return $this->hasMany('App\Jaq');
	}

	public function scopeCompleted($query)
	{
		$query->where('completed', true);
	}
}