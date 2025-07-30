<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	protected $fillable = [
		'client_id',
		'users',
		'name',
		'description',
		'target_id'
	];

	/**
	 * Get the client to which this group belongs.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function question()
	{
		return $this->belongsTo('App\Client');
	}

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

//	public function groupUsers()
//	{
//		$groupUsers = User::all()->filter(function($user) {
//			foreach ($this->users as $groupUser)
//			{
//				if ($groupUser['id'] == $user->id)
//					return true;
//			}
//			return false;
//		});
//
//		return $groupUsers;
//	}
}
