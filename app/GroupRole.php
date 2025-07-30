<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupRole extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'client_id',
		'slug',
		'description',
		'level'
	];

	/**
	 * Get the client to which this role belongs.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function question()
	{
		return $this->belongsTo('App\Client');
	}
}
