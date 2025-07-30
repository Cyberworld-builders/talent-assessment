<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Assignment extends Model
{
	private static $secretkey = 'SM9UyHvpf30KHyJLmgvOPLIDJtY1fPoh';

	protected $fillable = [
		'id',
		'user_id',
		'assessment_id',
		'completed',
		'custom_fields',
		'started_at',
		'completed_at',
		'created_at',
		'expires',
		'whitelabel',
		'target_id',
		'reminder',
		'next_reminder',
		'reminder_frequency',
		'job_id',
		'short_name',
	];

	protected $dates = ['started_at', 'completed_at', 'expires'];

	/**
	 * Get the user to which this assignment is assigned to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the target of this assignment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function target()
	{
		return $this->belongsTo('App\User');
	}

	/**
	 * Get the job of this assignment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function job()
	{
		return $this->belongsTo('App\Job');
	}


	/**
	 * Get all answers stored for this assignment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function answers()
	{
		return $this->hasMany('App\Answer');
	}

	/**
	 * Return assignments that are completed.
	 *
	 * @param $query
	 */
	public function scopeIsCompleted($query)
	{
		$query->where('completed', 1);
	}

	/**
	 * Get the assessment linked to this assignment.
	 *
	 * @return mixed
	 */
	public function assessment()
	{
		$assessment = Assessment::find($this->assessment_id);

		return $assessment;
	}

	/**
	 * Generate the special URL for a specific assignment.
	 *
	 * @param $id
	 * @param $username
	 * @param $expires
	 * @return string
	 */
	public static function generateURL($id, $username, $expires)
	{
		$url = 'assignment/'.$id;

		$hash = self::generateHash($username, $url, $expires);

		$hashed_url = http_build_query([
			'u' => base64_encode($username),
			'e' => base64_encode($expires),
			't' => base64_encode($hash)
		]);

		$server = $_SERVER['SERVER_NAME'];
		$port = '';
		if ($server == 'localhost')
			$port = ':8000';

		$assignment_url = 'http://'.$server.$port.'/'.$url.'?'.$hashed_url;

		return $assignment_url;
	}

	/**
	 * Generate a hash from the url.
	 *
	 * @param $username
	 * @param $url
	 * @param $expires
	 * @return string
	 */
	private static function generateHash($username, $url, $expires)
	{
		$secretkey = self::$secretkey;

		return hash('sha256', $username.$secretkey.$url.$expires);
	}

	/**
	 * Check the assignment URL to make sure it is valid.
	 *
	 * @param $id
	 * @return bool
	 */
	public static function checkURL($id)
	{
		$url = 'assignment/'.$id;
		$username = base64_decode($_GET['u']);
		$expires = Carbon::createFromFormat('Y-m-d H:i:s', base64_decode($_GET['e']));
		$token = base64_decode($_GET['t']);

		$hash = self::generateHash($username, $url, $expires);

		if ($token != $hash)
			return false;

		return true;
	}

	/**
	 * Get the translated assessment.
	 *
	 * @return null
	 */
	public function translation()
	{
		if (! $this->assessment())
			return false;

		$user = \Auth::user();

		if (! $user)
			return false;

		return Translation::where('assessment_id', $this->assessment()->id)->where('language_id', $user->language_id)->first();
	}

	/**
	 * Serialize anchors when saved in storage.
	 *
	 * @param $value
	 */
	public function setCustomFieldsAttribute($value)
	{
		$this->attributes['custom_fields'] = serialize($value);
	}

	/**
	 * Un-serialize anchors when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getCustomFieldsAttribute()
	{
		return unserialize($this->attributes['custom_fields']);
	}

	public function completed()
	{
		return $this->getOriginal('completed_at') && ($this->getOriginal('completed_at') !== '0000-00-00 00:00:00');
	}

	public function reportTemplate()
	{
		$template = DB::table('assessment_report')->where('assessment_id', $this->assessment_id)->first();

		$reportTemplates = [
			(object)[
				'id' => 1,
				'view' => 'aoep.blade.php'
			]
		];

		if (!$template)
			return false;

		return $template->report_template_id;
	}
}
