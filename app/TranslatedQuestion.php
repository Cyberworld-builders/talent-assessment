<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatedQuestion extends Model
{
	protected $fillable = [
		'question_id',
		'content',
		'anchors'
	];

	/**
	 * Get the translation to which this question belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function translation()
	{
		return $this->belongsTo('App\Translation');
	}

	/**
	 * Serialize anchors when saved in storage.
	 *
	 * @param $value
	 */
	public function setAnchorsAttribute($value)
	{
		$this->attributes['anchors'] = serialize($value);
	}

	/**
	 * Un-serialize anchors when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getAnchorsAttribute()
	{
		return unserialize($this->attributes['anchors']);
	}
}
