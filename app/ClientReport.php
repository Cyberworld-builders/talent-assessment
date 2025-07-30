<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientReport extends Model
{
	/**
	* The columns allowed to be filled.
	*
	* @var array
	*/
	protected $fillable = [
		'client_id',
		'report_id',
		'job_id',
		'report',
		'client',
		'fields',
		'enabled',
		'visible'
	];

	/**
	 * Remove timestamps for this resource.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * Serialize fields when saved in storage.
	 *
	 * @param $value
	 */
	public function setFieldsAttribute($value)
	{
		$this->attributes['fields'] = serialize($value);
	}

	/**
	 * Un-serialize fields when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getFieldsAttribute()
	{
		return unserialize($this->attributes['fields']);
	}

	/**
	 * Get the report on which this Client Report is based.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function report()
	{
		return $this->belongsTo('App\Report');
	}

	/**
	 * Get the job that this Client Report is for.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function job()
	{
		return $this->belongsTo('App\Job');
	}
}
