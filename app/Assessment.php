<?php

namespace App;

use App\Http\Controllers\AssessmentsController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Assessment extends Model
{
    protected $fillable = [
    	'id',
    	'name',
    	'description',
    	'logo',
    	'background',
    	'paginate',
    	'items_per_page',
    	'translation',
    	'language',
    	'whitelabel',
    	'company_labeled_for',
    	'timed',
    	'time_limit',
		'use_custom_fields',
		'custom_fields',
		'target',
    	'last_modified'
    ];
    protected $dates = ['last_modified'];

	/**
	 * Get all questions associated with this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function questions()
	{
		return $this->hasMany('App\Question');
	}

	/**
	 * Get all dimensions associated with this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function dimensions()
	{
		return $this->hasMany('App\Dimension');
	}

	/**
	 * Get all questions associated with this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function translations()
	{
		return $this->hasMany('App\Translation');
	}

	/**
	 * Get all custom weights associated with this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function weights()
	{
		return $this->hasMany('App\Weight');
	}

	/**
	 * Get the weights associated with this assessment.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function weightsForJob($jobId)
	{
		$weights = Weight::where([
			'assessment_id' => $this->id,
			'job_id' => $jobId
		])->get();

		// If reseller, grab the weights from master database
		if (Auth::user()->is('reseller|client') && session('reseller'))
		{
			$jobTemplateId = Job::find($jobId)->job_template_id;

			// Access the master database
			$db = DBConnection::getConnectionToMasterDatabase();

			$weights = collect($db->getConnection()->table('weights')->where([
				'job_id' => $jobTemplateId,
				'assessment_id' => $this->id,
			])->get());
		}

		if ($weights->isEmpty())
			return false;

		return $weights;
	}

	/**
	 * Get all questions that are not descriptors, ordered by dimension id.
	 * @return mixed
	 */
	public function filteredQuestions()
	{
		return $this->questions->filter(function($question) {
			return ($question->type != 2 && $question->type != 10);
		})->sortBy(function($question) {
			if ($question->dimension_id)
				return $question->dimension_id;
			return $question->number;
		});
	}

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
	 * Get the ids of all questions associated with this assessment.
	 *
	 * @return array
	 */
	public function get_existing_question_ids()
	{
		$ids = [];

		foreach ($this->questions as $i => $question)
		{
			$ids = array_add($ids, $i, $question->id);
		}

		return $ids;
	}

	/**
	 * Get the specific translation in the language of the currently authenticated user.
	 *
	 * @return mixed
	 */
	public function translation()
	{
		$user = \Auth::user();

		if (! $user)
			return false;

		return Translation::where('assessment_id', $this->id)->where('language_id', $user->language_id)->first();
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

	/**
	 * Get all the assignments generated for this assessment for a specific user.
	 *
	 * @param $id
	 */
	public function getAssignmentsForUser($id)
	{
		$assignments = Assignment::where([
			'assessment_id' => $this->id,
			'user_id' => $id
		])->get();

		return $assignments;
	}

	public function createWMTask($assignment = null)
	{
		if (! $this->isWM())
			return null;

		// Get translation
		$assessmentsController = new AssessmentsController();
		$translation = null;
		if ($assignment)
		{
			$user = User::findOrFail($assignment->user_id);
			$translation = Translation::where('assessment_id', $this->id)->where('language_id', $user->language_id)->first();
		}

		$questions = $this->getWMQuestionsExcept();

		if (! $questions || $questions->isEmpty())
			return null;

		return $assessmentsController->createWMTask($questions, $translation);
	}

	public function isWM()
	{
		$questions = $this->getWMQuestionsExcept(3);

		if ($questions && !$questions->isEmpty())
			return true;

		return false;
	}

	public function getWMQuestionsExcept($except = null)
	{
		return $this->questions()->orderBy('number', 'asc')->get()->filter(function($question) use ($except) {
			return $question->isWMType($except);
		});
	}
	
	/**
	 * Grab the current assessments from the master database, and copy them over.
	 *
	 * @return bool
	 */
	public static function updateResellerAssessments()
	{
		if (! \Auth::user()->isReseller() && !session('reseller'))
			return false;

		$assessments = session('reseller')->getAssessments();

		foreach ($assessments as $assessment)
		{
			if (Assessment::find($assessment->id))
				continue;

			$newAssessment = new Assessment((array)$assessment);
			\Auth::user()->assessments()->save($newAssessment);
		}

		Assessment::updateResellerQuestions();
		Assessment::updateResellerTranslations();

		return true;
	}

	/**
	 * Copy over assessment questions from the master database.
	 */
	public static function updateResellerQuestions()
	{
		$assessments = Assessment::all();

		foreach ($assessments as $assessment)
		{
			if (! in_array($assessment->id, session('reseller')->assessments))
				continue;

			foreach ($assessment->getQuestions() as $question)
			{
				if (Question::find($question->id))
					continue;

				$newQuestion = new Question((array)$question);
				$newQuestion->anchors = unserialize($question->anchors);
				$assessment->questions()->save($newQuestion);
			}
		}
	}

	/**
	 * Copy over translations data from the master database.
	 */
	public static function updateResellerTranslations()
	{
		$assessments = Assessment::all();

		foreach ($assessments as $assessment)
		{
			if (! in_array($assessment->id, session('reseller')->assessments))
				continue;

			foreach ($assessment->getTranslations() as $translation)
			{
				if (Translation::find($translation->id))
					continue;

				$newTranslation = new Translation((array)$translation);
				\Auth::user()->translations()->save($newTranslation);
			}
		}
	}

	/**
	 * Get all the questions for a specific assessment from the master database.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getQuestions()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$questions = collect($db->getConnection()->table('questions')->where('assessment_id', $this->id)->get());

		return $questions;
	}

	/**
	 * Get all the translations for a specific assessment from the master database.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function getTranslations()
	{
		if (env('APP_ENV') == 'staging')
		{
			$db_host = $_SERVER['RDS_HOSTNAME'].':'.$_SERVER['RDS_PORT'];
			$db_database = $_SERVER['RDS_DB_NAME'];
			$db_username = $_SERVER['RDS_USERNAME'];
			$db_password = $_SERVER['RDS_PASSWORD'];
		}
		else
		{
			$db_host = env('DB_HOST', 'localhost');
			$db_database = env('DB_DATABASE', 'forge');
			$db_username = env('DB_USERNAME', 'forge');
			$db_password = env('DB_PASSWORD', '');
		}

		$db = new DBConnection([
			'host' => $db_host,
			'database' => $db_database,
			'username' => $db_username,
			'password' => $db_password,
		]);

		$translations = collect($db->getConnection()->table('translations')->where('assessment_id', $this->id)->get());

		return $translations;
	}

	public function customizedReportForClient($clientId)
	{
		// Get report template for this assessment
		$reportId = null;
		$reports = Report::all();
		foreach ($reports as $report)
		{
			$assessments = json_decode($report->assessments);
			if (count($assessments) == 1 && $assessments[0] == $this->id)
				$reportId = $report->id;

			if ($reportId) break;
		}

		$clientReport = ClientReport::where([
			'client_id' => $clientId,
			'report_id' => $reportId,
			'job_id' => null,
			'enabled' => 1
		])->first();

		return $clientReport;
	}

	public static function getSampleTestId($name)
	{
		$sampleTests = [
			'wmo' => 33,
			'wms' => 36,
			'personality' => 35,
		];

		if (! array_key_exists($name, $sampleTests))
			return null;

		return $sampleTests[$name];
	}
}
