<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
	protected $fillable = [
		'id',
		'name',
		'description',
		'assessment_id',
		'language_id',
	];

	/**
	 * Get the user that created this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the assessment this translation was created for.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function assessment()
	{
		return $this->belongsTo('App\Assessment');
	}

	/**
	 * Get all translated questions associated with this translation.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function questions()
	{
		return $this->hasMany('App\TranslatedQuestion');
	}

	public function language()
	{
		return Language::find($this->language_id);
	}
}
