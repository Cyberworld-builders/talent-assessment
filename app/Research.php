<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	protected $table = 'research';

	protected $fillable = [
		'age',
		'gender',
		'ethnicity',
		'industry',
		'purpose'
	];

	/**
	 * Get the user that filled out these research questions.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
