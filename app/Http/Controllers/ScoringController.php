<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Assignment;
use App\Client;
use App\Dimension;
use App\Job;
use App\Question;
use App\Report;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Monolog\Handler\ElasticSearchHandler;
use PhpParser\Node\Expr\Assign;

class ScoringController extends Controller
{

	/**
	 * Generate a score for a specific assignment for a specific job.
	 *
	 * @param $assignmentId
	 * @param $jobId
	 * @return int
	 */
	public function score($assignmentId, $jobId)
	{
		$assignment = Assignment::findOrFail($assignmentId);
		$job = Job::findOrFail($jobId);
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$score = null;

		// First try to find the score in the database
		$savedScore = DB::table('report_data')->where('assignment_id', $assignment->id)->first();

		// See if it's relevant
		if ($savedScore && $savedScore->updated_at == $assignment->completed_at)
			$score = json_decode($savedScore->score);

		// If we are using the saved score, bypass computing it again
		if ($score)
			return $score;

		// Personality, Safety, Evonik Questionnaire, L, Ls, Lsr
		if ($assessment->id == get_global('personality') || $assessment->id == get_global('safety') || $assessment->id == get_global('evonik-questionnaire') || $assessment->id == get_global('leader') || $assessment->id == get_global('leader-s') || $assessment->id == get_global('leader-sr')) {
			$score = $this->getWeightedScoreAverage($assignment, $job);
		}

		// OSpan, SSpan
		else if ($assessment->id == get_global('ospan') || $assessment->id == get_global('sspan')) {
			$score = $this->scoreWm($assignment->id);
		}

		// Ability, Aptitude, Evonik Assessment, Reasoning B
		else if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('evonik-assessment') || $assessment->id == get_global('reasoning-b')) {
			$score = $this->getTotalScore($assignment);
		}

		// All other assignments
		else {
			$score = $this->getTotalScore($assignment);
		}

		// Save the computed score in report data so we can pull it later
		DB::table('report_data')->insert(
			[
				'user_id' => $assignment->user_id,
				'assignment_id' => $assignment->id,
				'created_at' => Carbon::now(),
				'updated_at' => $assignment->completed_at,
				'score' => json_encode($score)
			]
		);

		return $score;
	}

	/**
	 * Get the raw total of all scores for each answer, without any weighting.
	 *
	 * @param $assignment
	 * @return int
	 */
	public function getTotalScore($assignment)
	{
		$answers = $assignment->answers;

		if (! $answers)
			return 0;

		$score = 0;
		foreach ($answers as $answer)
			$score += $answer->score();

		return $score;
	}

	/**
	 * Get the average score of answers per dimension, weighted based on custom job weights.
	 *
	 * @param $assignment
	 * @param $job
	 * @return int
	 */
	public function getWeightedScoreAverage($assignment, $job)
	{
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$score = 0;

		$dimensionCount = $assessment->dimensions->where('parent', 0)->count();
		$weight = $assessment->weightsForJob($job->id);
		if ($weight)
			$weight = $assessment->weightsForJob($job->id)->first();
		$customWeights = [];

		// If we have custom weights, use them
		if ($weight)
			$customWeights = $weight->weights;

		// Otherwise, use an even weighting across all dimensions
		else
		{
			foreach ($assessment->dimensions->where('parent', 0) as $dimension)
				$customWeights[$dimension->id] = 100 / $dimensionCount;
		}

		// Now calculate the score
		foreach ($assessment->dimensions->where('parent', 0) as $dimension)
			$score += $this->getScoreAverageForDimension($assignment, $dimension) * $customWeights[$dimension->id] / 100;

		return $score;
	}

	/**
	 * Get the average score of all answers for a specific dimension.
	 *
	 * @param $assignment_id
	 * @param $dimension_id
	 * @return int
	 */
	public function getScoreForDimension($assignment_id, $dimension_id)
	{
		// Calculate scores for one assignment, as normal
		$assignment = Assignment::find($assignment_id);
		$dimension = Dimension::find($dimension_id);

		// Get id of dimension
		$dimension_ids = [$dimension->id];

		// If parent, get ids of child dimensions
		if ($dimension->isParent())
		{
			$dimension_ids = [];
			foreach ($dimension->getChildren() as $dimension)
				array_push($dimension_ids, $dimension->id);
		}

		// Start a new array of answers
		$answersArray = [];

		// New method - For each dimension, add answers to our array
		// This one calculates the average of each dimension separately, then averages the averages together, to get the parent dimension score
		foreach ($dimension_ids as $i => $dimension_id)
		{
			$answers = $assignment->answers()->get()->filter(function($answer) use ($dimension_id) {
				$question = Question::find($answer->question_id);
				return $question->dimension_id == $dimension_id;
			});
			$answersArray[$i] = [];
			foreach ($answers as $answer)
				array_push($answersArray[$i], $answer);
		}

		$scores = 0;
		foreach ($answersArray as $answers)
		{
			// Collect our answers
			$answers = collect($answers);
			$scores += $this->getScoreAverage($answers);
		}

		return $scores/count($answersArray);
	}

	/**
	 * Get the average score from answers.
	 *
	 * @param $answers
	 * @return int
	 */
	public function getScoreAverage($answers)
	{
		$score = 0;

		if ($answers->count() <= 0)
			return $score;

		foreach ($answers as $answer)
			$score += $answer->score();

		$score /= $answers->count();

		return $score;
	}

	/**
	 * Get the average score of all answers for a specific dimension.
	 *
	 * @param $assignment
	 * @param $dimension
	 * @return int
	 */
	public function getScoreAverageForDimension($assignment, $dimension)
	{
		// Get id of dimension
		$dimension_ids = [$dimension->id];

		// If parent, get ids of child dimensions
		if ($dimension->isParent())
		{
			$dimension_ids = [];
			foreach ($dimension->getChildren() as $childDimension)
				array_push($dimension_ids, $childDimension->id);
		}

		// Start a new collection of answers
		$answersArray = [];

		// For each dimension, add answers to our collection
		foreach ($dimension_ids as $dimension_id)
		{
			$answers = $assignment->answers()->get()->filter(function($answer) use ($dimension_id) {
				$question = Question::find($answer->question_id);
				return $question->dimension_id == $dimension_id;
			});

			foreach ($answers as $answer)
				array_push($answersArray, $answer);
		}

		// Get a collection of all the answers
		$answers = collect($answersArray);

		// Ger the average score for these answers
		return $this->getScoreAverageForAnswers($answers);
	}

	/**
	 * Get the average score for a collection of answers.
	 *
	 * @param $answers
	 * @return int
	 */
	public function getScoreAverageForAnswers($answers)
	{
		$score = 0;

		if ($answers->count() <= 0)
			return $score;

		foreach ($answers as $answer)
			$score += $answer->score();

		$score /= $answers->count();

		return $score;
	}

	/**
	 * Get which division the score falls into.
	 *
	 * @param $assignmentId
	 * @param $jobId
	 * @param $score
	 * @return int
	 */
	public function getScoreDivision($assignmentId, $jobId, $score)
	{
		$assignment = Assignment::findOrFail($assignmentId);
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$weight = $assessment->weightsForJob($jobId);
		if ($weight) {
			$weight = $assessment->weightsForJob($jobId)->first();
		}
		$customDivisions = [];

		if ($weight && is_string($weight->divisions)) {
			$weight->divisions = unserialize($weight->divisions);
		}

		// If we have custom divisions, use them
		if ($weight and !empty($weight->divisions))
		{
			foreach ($weight->divisions as $i => $division)
			{
				if ($division['min'] == 0 and $division['max'] == 0)
					continue;

				$customDivisions[$i] = [
					'min' => $division['min'],
					'max' => $division['max']
				];
			}
		}

		// Otherwise, use default evenly-distributed divisions
		else
		{
			// If we are dealing with score average
			if ($assessment->id == get_global('personality') || $assessment->id == get_global('safety') || $assessment->id == get_global('evonik-questionnaire') || $assessment->id == get_global('leader') || $assessment->id == get_global('leader-s'))
			{
				$customDivisions = [
					0 => ['min' => 0,    'max' => 4],
					1 => ['min' => 3.25, 'max' => 4],
					2 => ['min' => 2.5,  'max' => 3.25],
					3 => ['min' => 2,    'max' => 2.5],
					4 => ['min' => 2,    'max' => 0],
				];
			}

			// Otherwise, we are dealing with a raw score
			else
			{
				$questionCount = $assessment->questions->count();

				// Calculate divisions based on the bell curve used for the Ability test
				$max = (int)floor($questionCount / 1.81818);
				$mid = (int)floor($questionCount / 2.3076);

				// For the Ability test, which has 60 questions total, this will give
				// Max 33, Mid 26

				// Set the custom divisions using the above calculated bell curve
				$customDivisions = [
					0 => ['min' => $max, 'max' => 0],
					2 => ['min' => $mid, 'max' => $max],
					4 => ['min' => 0,  'max' => $mid],
				];
			}
		}

		// Get our division
		$scoreDivision = 0;
		$i = 0;
		$last = count($customDivisions) - 1;
		foreach ($customDivisions as $j => $division)
		{
			// If first value, check only the min
			if ($i == 0)
			{
				if ($score >= $division['min'])
					$scoreDivision = $j + 1;
			}

			// If last value, check only the max
			elseif ($i == $last)
			{
				if ($score < $division['max'])
					$scoreDivision = $j + 1;
			}

			// Otherwise, check both values
			else
			{
				if ($score >= $division['min'] and $score < $division['max'])
					$scoreDivision = $j + 1;
			}

			// Increment count
			$i++;
		}

		return $scoreDivision;
	}

	/**
	 * Get which division the score falls into.
	 *
	 * @param $reportId
	 * @param $assignmentId
	 * @param $score
	 * @return int
	 */
	public function getDivision($reportId, $assignmentId, $score)
	{
		$report = Report::findOrFail($reportId);
		$assignment = Assignment::findOrFail($assignmentId);
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$divisions = [];

		// If we have custom divisions, use them
		if ($report->divisions && property_exists(\GuzzleHttp\json_decode($report->divisions), $assessment->id))
			$divisions = \GuzzleHttp\json_decode($report->divisions)->{$assessment->id};

		// Otherwise, use default divisions
		else
		{
			// If we are dealing with score average
			if ($assessment->id == get_global('personality') || $assessment->id == get_global('safety') || $assessment->id == get_global('evonik-questionnaire') || $assessment->id == get_global('leader') || $assessment->id == get_global('leader-s'))
			{
				$divisions = [
					0 => ['min' => 0,    'max' => 4],
					1 => ['min' => 3.25, 'max' => 4],
					2 => ['min' => 2.5,  'max' => 3.25],
					3 => ['min' => 2,    'max' => 2.5],
					4 => ['min' => 2,    'max' => 0],
				];
			}

			// Otherwise, we are dealing with a raw score
			else
			{
				$divisions = $this->getScoreDefaults($assessment->id, 'divisions');
			}
		}

		// Get our division
		$division = 5;
		$highest = null;
		foreach (array_reverse($divisions) as $div)
		{
			if ($div != '')
				$highest = $div;

			if ($score < $div || ($div == '' && $score < $highest))
				$division--;
		}

		return $division;
	}

	/**
	 * Get score for a working memory assignment.
	 *
	 * @param $assignmentId
	 * @return int
	 */
	public function scoreWm($assignmentId)
	{
		$assignment = Assignment::findOrFail($assignmentId);

		$answers = $assignment->answers;

		if (! $answers)
			return 0;

		$score = 0;
		foreach ($answers as $answer)
		{
			if ($answer->question->practice)
				continue;

			$score += $answer->scoreWm();
		}

		return $score;
	}

	/**
	 * Get the total points possible for a working memory assessment.
	 *
	 * @param $assignmentId
	 * @return int
	 */
	public function getWmTotal($assignmentId)
	{
		$assignment = Assignment::findOrFail($assignmentId);
		$assessment = Assessment::findOrFail($assignment->assessment_id);

		$questions = $assessment->questions;

		$total = 0;
		foreach ($questions as $i => $question)
		{
			if ($question->practice)
				continue;

			// Math and letters
			if ($question->type == 6)
				$total += count(json_decode($question->content)->equations);

			// Square symmetry
			if ($question->type == 9)
				$total += count(json_decode($question->content)->squares);
		}

		return $total;
	}

	/**
	 * Get the percentile to in which a score belongs in.
	 *
	 * @param $assignmentId
	 * @param $score
	 * @return int|mixed
	 */
	public function getPercentile($assignmentId, $score)
	{
		$assignment = Assignment::findOrFail($assignmentId);
		$assessment = $assignment->assessment();
		$percentiles = [];

		if ($assessment->id == get_global('aptitude'))
			$percentiles = [
			0 => 0,
			1 => 0,
			2 => 0,
			3 => 0,
			4 => 0,
			5 => 0,
			6 => 0,
			7 => 0,
			8 => 0,
			9 => 0,
			10 => 1,
			11 => 1,
			12 => 1,
			13 => 1,
			14 => 1,
			15 => 1,
			16 => 1,
			17 => 1,
			18 => 1,
			19 => 1,
			20 => 2,
			21 => 2,
			22 => 4,
			23 => 5,
			24 => 7,
			25 => 9,
			26 => 11,
			27 => 16,
			28 => 23,
			29 => 29,
			30 => 42,
			31 => 56,
			32 => 74,
			33 => 84,
			34 => 94,
			35 => 99,
			36 => 100,
			37 => 100,
			38 => 100,
			39 => 100,
			40 => 100,
			41 => 100
		];

		if ($assessment->id == get_global('ability'))
			$percentiles = [
				0 => 0,
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 0,
				8 => 0,
				9 => 0,
				10 => 1,
				11 => 2,
				12 => 2,
				13 => 3,
				14 => 3,
				15 => 3,
				16 => 4,
				17 => 7,
				18 => 10,
				19 => 15,
				20 => 21,
				21 => 26,
				22 => 30,
				23 => 34,
				24 => 40,
				25 => 44,
				26 => 48,
				27 => 55,
				28 => 60,
				29 => 63,
				30 => 68,
				31 => 72,
				32 => 76,
				33 => 80,
				34 => 85,
				35 => 86,
				36 => 89,
				37 => 90,
				38 => 91,
				39 => 93,
				40 => 94,
				41 => 96,
				42 => 97,
				43 => 98,
				44 => 99,
				45 => 99,
				46 => 99,
				47 => 99,
				48 => 99,
				49 => 99,
				50 => 100,
				51 => 100,
				52 => 100,
				53 => 100,
				54 => 100,
				55 => 100,
				56 => 100,
				57 => 100,
				58 => 100,
				59 => 100,
				60 => 100
			];

		if (! array_key_exists(intval($score), $percentiles))
			return 0;

		return $percentiles[intval($score)];
	}

	public function getScoreDefaults($assessmentId, $score = null)
	{
		$defaults = [];

		// Ability
		if ($assessmentId == get_global('ability'))
		{
			$defaults = [
				'score' => 34,
				'total' => 60,
				'accuracy' => 62,
				'percentile' => 87,
				'division' => 3,
				'divisions' => [0, '', 26, '', 33],
			];

			if ($score)
				return $defaults[$score];
		}

		// Personality
		if ($assessmentId == get_global('personality'))
		{
			$defaults = [
				'humility' => 3.50,
				'emotion' => 4.23,
				'extraversion' => 5.0,
				'agreeableness' => 3.21,
				'consc' => 2.45,
				'openness' => 3.65,
				'fairness' => 3.69,
				'greed' => 3.96,
				'modesty' => 2.98,
				'sincerity' => 4.25,
				'composure' => 4.12,
				'fearlessness' => 4.01,
				'independence' => 3.52,
				'stoical' => 3.78,
				'liveliness' => 5.0,
				'boldness' => 2.36,
				'esteem' => 4.58,
				'sociability' => 4.68,
				'flexibility' => 4.78,
				'forgiveness' => 2.69,
				'patience' => 4.78,
				'gentleness' => 4.35,
				'achievement' => 4.16,
				'detailed' => 3.98,
				'organization' => 3.74,
				'prudence' => 4.05,
				'appreciation' => 2.54,
				'creativity' => 2.12,
				'inquisitiveness' => 4.56,
				'unconventionality' => 4.68,
				'score' => 3.51,
				'division' => 3
			];
		}

		// Safety
		if ($assessmentId == get_global('safety'))
		{
			$defaults = [
				'self_confidence' => 3.50,
				'focus' => 4.23,
				'control' => 5.0,
				'knowledge' => 3.21,
				'motivation' => 2.45,
				'risk' => 3.65,
				'score' => 3.51,
				'division' => 3
			];
		}

		// Leadership
		if ($assessmentId == get_global('leader'))
		{
			$categories = [
				'main',
				'top',
				'average'
			];
        	$subcats = [
        		'commem',
				'autonomy',
				'power',
				'general',
				'management',
				'feedback',
				'information',
				'rewards',
				'empowerment',
				'mentoring',
				'acquisition',
				'knowledge',
				'conflict',
				'teamwork',
				'communication',
				'respect',
				'relationships'
			];
			foreach ($categories as $cat)
				foreach ($subcats as $subcat)
					$defaults[$cat][$subcat] = rand(100, 500) / 100;
			foreach ($defaults['top'] as $subcat => $val)
				if ($defaults['top'][$subcat] < $defaults['main'][$subcat])
					$defaults['top'][$subcat] = $defaults['main'][$subcat];
			foreach ($defaults['average'] as $subcat => $val)
				if ($defaults['average'][$subcat] > $defaults['top'][$subcat])
					$defaults['average'][$subcat] = $defaults['average'][$subcat] / 2;
			$defaults['scorers'] = 3;
			$defaults['strengths'] = $this->getLeaderStrengths($defaults);
			$defaults['opportunities'] = $this->getLeaderOpportunities($defaults);
		}

		// WM OSpan
		if ($assessmentId == get_global('ospan'))
		{
			$defaults = [
				'score' => 20,
				'total' => 50,
				'accuracy' => 62,
				'percentile' => 87,
				'division' => 5,
				'divisions' => [0, '', 8, '', 15],
			];

			if ($score)
				return $defaults[$score];
		}

		return $defaults;
	}

	public function getZones($reportId = null, $assessmentId, $scoringMethod = null)
	{
		$report = Report::find($reportId);
		$assessment = Assessment::findOrFail($assessmentId);
		$total = $assessment->questions()->count();
		$colors = [
			'#e32731',
			'#d36e30',
			'#e7b428',
			'#b9c945',
			'#30bd21',
		];
		$defaultColor = '#acb8d2';
		$values = $this->getScoreDefaults($assessmentId, 'divisions');

		if (($report && $report->score_method == 2) || $scoringMethod == 2)
			return [
				[
					'value' => $total,
					'color' => $defaultColor
  				]
			];

		if ($report && $report->divisions && property_exists(\GuzzleHttp\json_decode($report->divisions), $assessmentId))
			$values = \GuzzleHttp\json_decode($report->divisions)->{$assessmentId};

		$zones = [];
		$previousColor = $colors[0];
		foreach ($values as $i => $value)
		{
			if (! $value)
				continue;

			$zones[] = [
				'value' => $value,
				'color' => $previousColor
			];

			$previousColor = $colors[$i];
		}
		$zones[] = [
			'value' => $total,
			'color' => $colors[4]
		];

		return $zones;
	}

	public function getScoreZones($assessmentId, $jobId)
	{
		$job = Job::findOrFail($jobId);
		$zones = [
			'value' => [],
			'color' => [],
		];
		$weights = $job->weights->where('assessment_id', $assessmentId)->first();

		// If no weighting, grab some default scores
		if (!$weights || !$weights->divisions)
			return $this->getScoreDefaults($assessmentId, 'zones');

		// Grab the divisions from the database
		foreach ($weights->divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $zones['value']))
				array_push($zones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $zones['value']))
				array_push($zones['value'], $division['max']);
		}
		sort($zones['value']);
		$zones['color'][0] = '#E32731';
		$zones['color'][1] = '#E7B428';

		// Add in the final value for total
		$assessment = Assessment::findOrFail($assessmentId);
		$total = $assessment->questions()->count();
		array_push($zones['value'], $total);
		array_push($zones['color'], '#30BD21');

		return $zones;
	}

	public function getUserScoresForReport($reportId, $userId)
	{
		$report = Report::findOrFail($reportId);
		$client = Client::findOrFail($report->client_id);
		$job = Job::findOrFail($report->job_id);
		$user = User::findOrFail($userId);

		// If scores are saved, just load them
		if ($report->scores)
			return object_to_array(\GuzzleHttp\json_decode($report->scores));

		// Otherwise, calculate them
		else
		{
			// Get all the assignments we need to score
			$assignments = [];
			foreach (\GuzzleHttp\json_decode($report->assessments) as $assessmentId)
				$assignments[] = $user->lastCompletedAssignmentForJob($assessmentId, $job->id);

			// Gather our scores for each assessment
			$scores = [];
			foreach (\GuzzleHttp\json_decode($report->assessments) as $assessmentId)
			{
				$gotScores = false;
				foreach ($assignments as $i => $assignment)
					if ($assignment && $assignment->assessment_id == $assessmentId)
					{
						$scores[$assessmentId] = $this->getAssessmentScores($assignment->assessment_id, $assignment->id, $job->id, $report->id);
						$gotScores = true;
						break;
					}

				if (! $gotScores)
					throw new Exception('User has not completed all required assignments for this report. This report cannot be shown.');
			}
		}

		// Save report scores for later
//		if (! $report->scores)
//		{
//			$report->scores = \GuzzleHttp\json_encode($scores);
//			$report->save();
//		}

		return $scores;
	}

	public function getAssessmentScores($assessmentId, $assignmentId, $jobId, $reportId)
	{
		$assessment = Assessment::findOrFail($assessmentId);
		$assignment = Assignment::findOrFail($assignmentId);
		$user = User::findOrFail($assignment->user_id);
		$client = Client::findOrFail($user->client_id);
		$report = Report::findOrFail($reportId);
		$scores = [];

		// Ability
		if ($assessment->id == get_global('ability'))
		{
			$score = $this->score($assignmentId, $jobId);
			$total = $assessment->questions()->count();
			$scores = [
				'score' => $score,
				'total' => $total,
				'accuracy' => number_format(($score / $total) * 100, 0),
				'percentile' => $this->getPercentile($assignmentId, $score),
				'zones' => $this->getZones($report->id, $assessmentId),
			];

			// Weighted
			if ($report->score_method == 1)
			{
				$scores['division'] = $this->getDivision($report->id, $assignmentId, $score);
				$scores['confidence'] = 100;
			}

			// Predictive
			elseif ($report->score_method == 2)
			{
				$model = $this->getModelRank($report->id, $user->id, $jobId);
				$scores['division'] = $model['division'];
				$scores['confidence'] = $model['confidence'];
			}
		}

		// Personality
		if ($assessment->id == get_global('personality'))
		{
			$score = $this->score($assignmentId, $jobId);
			$scores = [
				'humility' => number_format($this->getScoreForDimension($assignment->id, 1), 2),
				'emotion' => number_format($this->getScoreForDimension($assignment->id, 2), 2),
				'extraversion' => number_format($this->getScoreForDimension($assignment->id, 3), 2),
				'agreeableness' => number_format($this->getScoreForDimension($assignment->id, 4), 2),
				'consc' => number_format($this->getScoreForDimension($assignment->id, 5), 2),
				'openness' => number_format($this->getScoreForDimension($assignment->id, 6), 2),
				'fairness' => number_format($this->getScoreForDimension($assignment->id, 7), 2),
				'greed' => number_format($this->getScoreForDimension($assignment->id, 8), 2),
				'modesty' => number_format($this->getScoreForDimension($assignment->id, 9), 2),
				'sincerity' => number_format($this->getScoreForDimension($assignment->id, 10), 2),
				'composure' => number_format($this->getScoreForDimension($assignment->id, 11), 2),
				'fearlessness' => number_format($this->getScoreForDimension($assignment->id, 12), 2),
				'independence' => number_format($this->getScoreForDimension($assignment->id, 13), 2),
				'stoical' => number_format($this->getScoreForDimension($assignment->id, 14), 2),
				'liveliness' => number_format($this->getScoreForDimension($assignment->id, 15), 2),
				'boldness' => number_format($this->getScoreForDimension($assignment->id, 16), 2),
				'esteem' => number_format($this->getScoreForDimension($assignment->id, 17), 2),
				'sociability' => number_format($this->getScoreForDimension($assignment->id, 18), 2),
				'flexibility' => number_format($this->getScoreForDimension($assignment->id, 19), 2),
				'forgiveness' => number_format($this->getScoreForDimension($assignment->id, 20), 2),
				'patience' => number_format($this->getScoreForDimension($assignment->id, 21), 2),
				'gentleness' => number_format($this->getScoreForDimension($assignment->id, 22), 2),
				'achievement' => number_format($this->getScoreForDimension($assignment->id, 23), 2),
				'detailed' => number_format($this->getScoreForDimension($assignment->id, 24), 2),
				'organization' => number_format($this->getScoreForDimension($assignment->id, 25), 2),
				'prudence' => number_format($this->getScoreForDimension($assignment->id, 26), 2),
				'appreciation' => number_format($this->getScoreForDimension($assignment->id, 27), 2),
				'creativity' => number_format($this->getScoreForDimension($assignment->id, 28), 2),
				'inquisitiveness' => number_format($this->getScoreForDimension($assignment->id, 29), 2),
				'unconventionality' => number_format($this->getScoreForDimension($assignment->id, 30), 2),
				'score' => $score
			];

			// Weighted
			if ($report->score_method == 1)
			{
				$scores['division'] = $this->getDivision($report->id, $assignmentId, $score);
				$scores['confidence'] = 100;
			}

			// Predictive
			elseif ($report->score_method == 2)
			{
				$model = $this->getModelRank($report->id, $user->id, $jobId);
				$scores['division'] = $model['division'];
				$scores['confidence'] = $model['confidence'];
			}
		}

		// Safety
		if ($assessment->id == get_global('safety'))
		{
			$score = $this->score($assignmentId, $jobId);
			$scores = [
				'self_confidence' => number_format($this->getScoreForDimension($assignment->id, 31), 2),
				'focus' => number_format($this->getScoreForDimension($assignment->id, 32), 2),
				'control' => number_format($this->getScoreForDimension($assignment->id, 33), 2),
				'knowledge' => number_format($this->getScoreForDimension($assignment->id, 34), 2),
				'motivation' => number_format($this->getScoreForDimension($assignment->id, 35), 2),
				'risk' => number_format($this->getScoreForDimension($assignment->id, 36), 2),
				'score' => $score
			];

			// Weighted
			if ($report->score_method == 1)
			{
				$scores['division'] = $this->getDivision($report->id, $assignmentId, $score);
				$scores['confidence'] = 100;
			}

			// Predictive
			elseif ($report->score_method == 2)
			{
				$model = $this->getModelRank($report->id, $user->id, $jobId);
				$scores['division'] = $model['division'];
				$scores['confidence'] = $model['confidence'];
			}
		}

		// Leadership
		if ($assessment->id == get_global('leader'))
		{
			$scores = [];

			// Find all completed assignments that pertain to this specific leader
			$assignments = $this->findAllAssignmentsForTarget($assignment, $user);
			$assignmentIds = get_property_list($assignments, 'id');

			// Grab the subdimension scores
			$scores['main']['commem'] = $this->getScoresArray($assignmentIds, 60); // Power
			$scores['main']['autonomy'] = $this->getScoresArray($assignmentIds, 61); // Power
			$scores['main']['general'] = $this->getScoresArray($assignmentIds, 54); // Information
			$scores['main']['management'] = $this->getScoresArray($assignmentIds, 55); // Information
			$scores['main']['feedback'] = $this->getScoresArray($assignmentIds, 56); // Information
			$scores['main']['rewards'] = $this->getScoresArray($assignmentIds, 51); // Rewards
			$scores['main']['empowerment'] = $this->getScoresArray($assignmentIds, 57); // Knowledge
			$scores['main']['mentoring'] = $this->getScoresArray($assignmentIds, 58); // Knowledge
			$scores['main']['acquisition'] = $this->getScoresArray($assignmentIds, 59); // Knowledge
			$scores['main']['conflict'] = $this->getScoresArray($assignmentIds, 62); // Relationships
			$scores['main']['teamwork'] = $this->getScoresArray($assignmentIds, 63); // Relationships
			$scores['main']['communication'] = $this->getScoresArray($assignmentIds, 64); // Relationships
			$scores['main']['respect'] = $this->getScoresArray($assignmentIds, 65); // Relationships

			// Get the average of the subdimension scores
			foreach ($scores['main'] as $dimension => $scoresArray) $scores['main'][$dimension] = array_average($scoresArray);

			// Get the average for the main dimensions, using the subdimensions
			$scores['main']['power'] = ($scores['main']['commem'] + $scores['main']['autonomy']) / 2;
			$scores['main']['information'] = ($scores['main']['general'] + $scores['main']['management'] + $scores['main']['feedback']) / 3;
			$scores['main']['knowledge'] = ($scores['main']['empowerment'] + $scores['main']['mentoring'] + $scores['main']['acquisition']) / 3;
			$scores['main']['relationships'] = ($scores['main']['conflict'] + $scores['main']['teamwork'] + $scores['main']['communication'] + $scores['main']['respect']) / 4;

			// Find all completed assignments from all other leaders
			$allAssignmentIds = [];
			foreach ($client->users as $u)
				foreach ($u->assignments as $a)
					if ($a->created_at == $assignment->created_at && $a->completed)
						$allAssignmentIds[] = $a->id;

			// Grab the subdimension scores from all leaders
			$scores['all']['commem'] = $this->getScoresArray($allAssignmentIds, 60); // Power
			$scores['all']['autonomy'] = $this->getScoresArray($allAssignmentIds, 61); // Power
			$scores['all']['general'] = $this->getScoresArray($allAssignmentIds, 54); // Information
			$scores['all']['management'] = $this->getScoresArray($allAssignmentIds, 55); // Information
			$scores['all']['feedback'] = $this->getScoresArray($allAssignmentIds, 56); // Information
			$scores['all']['rewards'] = $this->getScoresArray($allAssignmentIds, 51); // Rewards
			$scores['all']['empowerment'] = $this->getScoresArray($allAssignmentIds, 57); // Knowledge
			$scores['all']['mentoring'] = $this->getScoresArray($allAssignmentIds, 58); // Knowledge
			$scores['all']['acquisition'] = $this->getScoresArray($allAssignmentIds, 59); // Knowledge
			$scores['all']['conflict'] = $this->getScoresArray($allAssignmentIds, 62); // Relationships
			$scores['all']['teamwork'] = $this->getScoresArray($allAssignmentIds, 63); // Relationships
			$scores['all']['communication'] = $this->getScoresArray($allAssignmentIds, 64); // Relationships
			$scores['all']['respect'] = $this->getScoresArray($allAssignmentIds, 65); // Relationships

			// Get the average of the subdimension scores for all leaders
			foreach ($allAssignmentIds as $i => $id)
			{
				$scores['all']['power'][$i] = ($scores['all']['commem'][$i] + $scores['all']['autonomy'][$i]) / 2;
				$scores['all']['information'][$i] = ($scores['all']['general'][$i] + $scores['all']['management'][$i] + $scores['all']['feedback'][$i]) / 3;
				$scores['all']['knowledge'][$i] = ($scores['all']['empowerment'][$i] + $scores['all']['mentoring'][$i] + $scores['all']['acquisition'][$i]) / 3;
				$scores['all']['relationships'][$i] = ($scores['all']['conflict'][$i] + $scores['all']['teamwork'][$i] + $scores['all']['communication'][$i] + $scores['all']['respect'][$i]) / 4;
			}

			// Calculate the top scores from all leader scores
			foreach ($scores['all'] as $dimension => $scoresArray) $scores['top'][$dimension] = max($scoresArray);

			// Calculate the average scores from all leader scores
			foreach ($scores['all'] as $dimension => $scoresArray) $scores['average'][$dimension] = array_average($scoresArray);

			// Get the average for the main dimensions, using the subdimensions
			$scores['average']['power'] = ($scores['average']['commem'] + $scores['average']['autonomy']) / 2;
			$scores['average']['information'] = ($scores['average']['general'] + $scores['average']['management'] + $scores['average']['feedback']) / 3;
			$scores['average']['knowledge'] = ($scores['average']['empowerment'] + $scores['average']['mentoring'] + $scores['average']['acquisition']) / 3;
			$scores['average']['relationships'] = ($scores['average']['conflict'] + $scores['average']['teamwork'] + $scores['average']['communication'] + $scores['average']['respect']) / 4;

			// Finally grab the overall score
			$overallScoresArray = [
				$scores['all']['power'],
				$scores['all']['information'],
				$scores['all']['rewards'],
				$scores['all']['knowledge'],
				$scores['all']['relationships'],
			];
			$scores['overall'] = number_format(array_average($overallScoresArray), 2);

			// Round all the scores
			foreach ($scores as $category => $dims)
			{
				// Ignore these categories
				if ($category == 'all' or $category == 'overall')
					continue;

				foreach ($dims as $dim => $score)
					$scores[$category][$dim] = number_format($score, 2);
			}

			// Get the amount of scorers that rated this specific leader
			$scores['scorers'] = $assignments->count();

			// Get the strengths and opportunities based on the scores
			$scores['strengths'] = $this->getLeaderStrengths($scores);
			$scores['opportunities'] = $this->getLeaderOpportunities($scores);
		}

		return $scores;
	}

	public function findAllAssignmentsForTarget($assignment, $user)
	{
		return Assignment::where([
			'created_at' => $assignment->created_at,
			'completed' => 1
		])->get()->filter(function($assignment) use ($user)
		{
			// Filter these to make sure that this assignment was rating this specific user
			if (! $assignment->custom_fields)
				return false;

			foreach ($assignment->custom_fields['type'] as $i => $field)
			{
				if ($assignment->target_id == $user->id)
					return true;

				if ($field == 'name')
				{
					$name = $assignment->custom_fields['value'][$i];
					if ($name == $user->name)
						return true;
				}
				if ($field == 'email')
				{
					$email = $assignment->custom_fields['value'][$i];
					if ($email == $user->email)
						return true;
				}
			}
			return false;
		});
	}

	/**
	 * Get an array of scores for the following assignments.
	 *
	 * @param $assignment_ids
	 * @param $dimension_id
	 * @return int|mixed
	 */
	public function getScoresArray($assignment_ids, $dimension_id)
	{
		if (is_array($assignment_ids))
		{
			$scoresArray = [];
			foreach ($assignment_ids as $assignmentId)
			{
				$score = $this->getScoreForDimension($assignmentId, $dimension_id);
				array_push($scoresArray, $score);
			}

			return $scoresArray;
		}
	}

	public function getLeaderStrengths($scores)
	{
		// Setup our strengths text
		$strengthsText['commem'] = '<li><u>Communication Empowerment.</u> There were several notable strengths with empowering employees. Employees report that you seek their input, encourage the free exchange of ideas, and value their opinion.</li>';
		$strengthsText['autonomy'] = '<li><u>Autonomy.</u> Employees appreciate their freedom to make decisions about work and the autonomy to perform their job.</li>';
		$strengthsText['general'] = '<li><u>Information (General):</u> Employees report good communication regarding company mission and goals and providing valuable information for employees to do their job well.</li>';
		$strengthsText['management'] = '<li><u>Communication to and from upper management:</u> A specific strength is being a conduit between employees and upper management.</li>';
		$strengthsText['feedback'] = '<li><u>Feedback:</u> Your employees report that you provide performance feedback that is rewarding and informative.</li>';
		$strengthsText['rewards'] = '<p>Some notable strengths were in the areas of rewarding employees for strong performance and providing rewards that are meaningful. Employees report that you reinforce their good work with rewards that are valued by employees and really care about their development.</p>';
		$strengthsText['empowerment'] = '<li><u>Knowledge Empowerment:</u> Employees report that you set clear performance goals, provide feedback, and encourage them to evaluate and record their own performance.</li>';
		$strengthsText['mentoring'] = '<li><u>Mentoring:</u> Employees appreciate the level of one-on-one coaching and mentoring your provide.</li>';
		$strengthsText['acquisition'] = '<li><u>Training:</u> Your employees are satisfied with their training and development opportunities.</li>';
		$strengthsText['conflict'] = '<li><u>Conflict management.</u> Employees report your demanding accountability and seeking compromise for win-win resolutions to conflict.</li>';
		$strengthsText['teamwork'] = '<li><u>Teamwork.</u> Employees complimented your ability to create a safe and open environment. Continue to engage employees to adopt a cooperative mindset and value diversity within the team.</li>';
		$strengthsText['communication'] = '<li><u>Communication.</u> Specifically, employees remarked about your listening attentively, encouraging honest feedback and responding to all communication in a timely manner.</li>';
		$strengthsText['respect'] = '';

		// Specify dimension parents
		$parent = [
			'commem'        => 'power',
			'autonomy'      => 'power',
			'general'       => 'information',
			'management'    => 'information',
			'feedback'      => 'information',
			'rewards'       => 'rewards',
			'empowerment'   => 'knowledge',
			'mentoring'     => 'knowledge',
			'acquisition'   => 'knowledge',
			'conflict'      => 'relationships',
			'teamwork'      => 'relationships',
			'communication' => 'relationships',
			'respect'       => 'relationships',
		];

		// Calculate the strengths for the leader
		$strengths['power'] = [];
		$strengths['information'] = [];
		$strengths['rewards'] = [];
		$strengths['knowledge'] = [];
		$strengths['relationships'] = [];
		foreach ($parent as $dimension => $parentDimension)
		{
			if ($scores['main'][$dimension] >= $scores['average'][$dimension])
				array_push($strengths[$parentDimension], $strengthsText[$dimension]);
		}

		return $strengths;
	}

	public function getLeaderOpportunities($scores)
	{
		// Setup our opportunities text
		$opportunitiesText['commem'] = [
			'Title' => 'Communication Empowerment',
			'Description' => '<p>Communication empowerment refers to encouraging the free exchange of ideas, encouraging employee feedback, and fostering employee confidence that their ideas and expertise are valued.</p>',
			'Action Steps' => [
				'<li>Allow team members to add to the conversation. By allowing them to speak first they will not be guided or constrained by your thoughts.</li>',
				'<li>If your employees/ team members are not willing to participate in open forum then have them write down their ideas independently and go around the table/room asking them to read their ideas aloud for the team to discuss.</li>',
				'<li>If an employee is complaining or constantly voicing problems that is frustrating for you, try not to criticize them for speaking up. Explain that you appreciate their engagement and encourage them to find solutions. Perhaps more importantly, be sure to act on those solutions.</li>',
				'<li>Ask for employee opinions and suggestions in project meetings or prior to making decisions. Encouraging all of your team to provide feedback is important but it may also be effective if you call on specific employees if no one speaks up.</li>',
				'<li>If employees are silent, even after asking for their input, prior to a team meeting assign one member the role of "devil\'s advocate" where their primary objective in the meeting is to voice objections.</li>',
				'<li>Empowering employees can be as simple as encouraging others to speak up in meetings, asking for questions, or promoting open discussion on important issues.</li>',
				'<li>In some cases it is difficult to get employees to speak up, especially when they aren\'t used to voicing their opinions. In those instances, assign someone to specifically play "devil\'s advocate" or ask employees to formulate a plan of action. In cases where decisions are out of their hands such as new policy or new strategy from upper management, then ask employees to develop a shared method of execution and compliance.</li>',
			],
		];
		$opportunitiesText['autonomy'] = [
			'Title' => 'Autonomy',
			'Description' => '<p>It is important to allow team members enough freedom to facilitate proper task completion and the authority to act and make decisions to perform their job.</p>',
			'Action Steps' => [
				'<li>Let your team design and execute tasks without interruption. Focus on giving your team an outcome goal and let them make the decisions on how to accomplish it.</li>',
				'<li>In addition to delegating tasks to the team, drive their confidence by confirming they have the power to act.</li>',
				'<li>Resist the urge to micromanage. Even allowing the team members to fail can produce a rich and lasting learning experience.</li>',
				'<li>Employees can develop the knowledge necessary to manage their own work activities. Trust employees to make the important decisions and encourage them to do so.</li>',
				'<li>Clearly communicate your values and your intent regarding the work. Your values will help guide their autonomous decisions and your intent prompts them to make decisions that achieves your objectives without having to consult with you at every step.</li>',
				'<li>For instances when employees report low autonomy, look for tasks to delegate or interview employees to learn which parts of their jobs are the most difficult. Employees on the front line often have more efficient or effective solutions than those imposed by upper management.</li>',
			],
		];
		$opportunitiesText['general'] = [
			'Title' => 'Information (General)',
			'Description' => '<p>Items on the information dimension include factors such as communicating frequently, describing a clear mission for the unit, and giving sufficient notice prior to making decisions.</p>',
			'Action Steps' => [
				'<li>For each difficult decision, provide some additional information for <em>why</em> that decision was necessary.</li>',
				'<li>As much as it is allowed, be transparent about what is going on with the business.</li>',
				'<li>Set aside a time each week to update all of your employees via e-mail, text, or message board.</li>',
				'<li>Inform employees about upcoming decisions or changes to give them time to provide input and prepare.</li>',
				'<li>Think of everyone affected by a decision and make an effort to keep them informed.</li>',
				'<li>Use software that automatically updates the workteam on important information (e.g., budget, client deadlines, production). Or make sure everyone has access to this information.</li>',
			],
		];
		$opportunitiesText['management'] = [
			'Title' => 'Communication to and from Upper Management',
			'Description' => '<p>These items include factors such as being a conduit of communication between upper management and the unit/team. That is, clearly communicating what is learned from upper management and in turn relaying concerns to upper management issues communicated by the unit/team.</p>',
			'Action Steps' => [
				'<li>Create daily and/or weekly information meetings. The meetings should be very short (no more than 10 minutes) where you make announcements about information learned from upper management in the days before.</li>',
				'<li>Solicit feedback/concerns from employees that can be related to upper management.</li>',
				'<li>With permission from upper management, share future changes with employees that will affect their jobs.</li>',
				'<li>Make an effort to relate employee suggestions/requests to upper management. Let the employee know their suggestion/request was communicated and be sure to get back to the employee with an answer, even if the answer is \'no.\'</li>',
				'<li>Take care to provide explanations for upper management decisions which often look illogical when taken out of context. </li>',
			],
		];
		$opportunitiesText['feedback'] = [
			'Title' => 'Feedback',
			'Description' => '<p>Feedback behaviors include factors such as defining performance standards, providing corrective feedback in a professional manner, and using performance feedback to effectively motivate employees.</p>',
			'Action Steps' => [
				'<li>Explain performance standards at every performance evaluation meeting.</li>',
				'<li>Get in the habit of providing daily or weekly feedback to employees. This can be one or two comments informally. Make sure most of the feedback is with regard to above average behaviors (positive feedback) with gewer comments devoted to behaviors that do not meet expectations (critical feedback). Most of the comments should be positive.</li>',
				'<li>When correcting employees, carefully model the correct or expected behavior.</li>',
				'<li>Encourage employees to set goals and performance standards. This helps with generating commitment, involvement, and perception that feedback is relevant and motivating.</li>',
				'<li>Use survey data with feedback to provide information to employees, especially with regard to human capital or customer service.</li>',
				'<li>When providing critical feedback, focus only on behaviors and what was observed. Do not discuss anyone\'s character or try to guess "why" someone did not perform up to expectations.</li>',
			],
		];
		$opportunitiesText['rewards'] = [
			'Title' => 'Rewards',
			'Description' => '<p>Upon closer examination, survey items tapping into recognition and rewards for strong performance were below the average. These items include: rewarding individuals for good performance, providing recognition and praise, and providing valuable rewards to employees.</p>',
			'Action Steps' => [
				'<li>Pay close attention to whether you are rewarding for performance. Many managers do not like differential rewards and may reward employees equally, or may give rewards to the loudest employees that continually demand them. Although this may temporarily quiet the most outspoken employees, it hurts in the long term. Employees know who the strongest performers are and if rewards are not allocated according to the rank order of performance, everyone notices. You risk losing your best employees and demotivating many others that stay.</li>',
				'<li>Rewards do not always have to be monetary (e.g., raises, bonuses) and can be simple recognition for a job well done.</li>',
				'<li>The rewards employees’ value are quite varied and depend on the situation. Some employees enjoy being recognized publicly (e.g., employee of the month) and some prefer to be recognized privately (e.g., a memo, office visit with personal thanks). Other examples of small rewards are valued parking spaces, star rewards, gift cards, gas cards, movie cards, parties or picnics.</li>',
				'<li>Consider rewarding with skill-based pay. That is, employees focused on training and development should be rewarded for their efforts since they will impact the bottom line.</li>',
				'<li>Focus feedback and mentoring on the positive aspects of performance. Many managers are only reminded to provide feedback for negative performance. Recognition for positive performance is a powerful motivating reward.</li>',
			],
		];
		$opportunitiesText['empowerment'] = [
			'Title' => 'Knowledge Empowerment',
			'Description' => '<p>Knowledge empowerment behaviors include factors such as teaching team members how to track and evaluate their own performance, seek new development opportunities, or helping them set realistic performance goals.</p>',
			'Action Steps' => [
				'<li>Focus on assisting in overall self-awareness for team member\'s performance. Perhaps even supply team members with a template for them to assess and track their own performance.</li>',
				'<li>After establishing performance standards and training employees how to use them, work on setting realistic performance goals.</li>',
				'<li>Create training and development plans with team members and emphasize the importance of the team member taking the lead on self-development.</li>',
				'<li>Share with employees some of the economic positions of the department and the organization as a whole. This helps employees to keep score and continually add value.</li>',
				'<li>Set a good example by showing your behaviors and actions are driven by your own performance goals.</li>',
				'<li>Explain to employees how performance is measured and rewarded. The key challenge is to do a good job clearly defining performance standards. Many performance appraisal systems list vague performance objectives (i.e., “customer service” rated on a 3-point scale) and these are not very helpful. Instead, clearly define behaviors expected on the job (e.g.,, smile at customers, listen attentively, follow up on their requests, return phone calls and emails in a timely manner).</li>',
				'<li>Teach employees how to score their own performance. With good performance metrics, employees can monitor their own performance which helps with motivation and accountability.</li>'
			],
		];
		$opportunitiesText['mentoring'] = [
			'Title' => 'Mentoring',
			'Description' => '<p>Sufficient one-on-one time with employees allows for the development of a relationship which fosters goal-setting and reviewing team member performance.</p>',
			'Action Steps' => [
				'<li>Informal mentorships can be just as effective as formal mentorships. Get to know your employees and take a personal interest in them and their career.</li>',
				'<li>Prioritize one-on-one interactions with team members to learn more about their career goals and needs.</li>',
				'<li>Work with team members weekly (or at least monthly) to set process goals and track progress.</li>',
				'<li>Take time to coach employees during project meetings, especially with regard to their specific roles and responsibilities. This is a good opportunity to share your own knowledge, skill, and expertise.</li>',
				'<li>Demonstrate a positive attitude and act as a positive role model. By showing what it takes to be productive and successful, you are demonstrating the behaviors and actions necessary to succeed.</li>',
				'<li>Improve formal and informal mentoring by spending 1-on-1 time with employees, supporting their career aspirations, helping them set goals, and/or seeking and providing support for training opportunities.</li>',
			],
		];
		$opportunitiesText['acquisition'] = [
			'Title' => 'Knowledge Acquisition',
			'Description' => '<p>Employees seek training opportunities and/or support for training.</p>',
			'Action Steps' => [
				'<li>Seek more training and development programs for team members.</li>',
				'<li>Challenge team members to research and find additional training opportunities for themselves.</li>',
				'<li>Communicate to team members the process for submitting requests for training.</li>',
				'<li>Add a section to performance evaluations where employees can list the training they received. Be sure to acknowledge that their efforts for improvement are valued.</li>',
				'<li>Work to generate a strong climate for learning within the department. Seek out opportunities for your own development and share what you have learned so that you set the tone.</li>'
			],
		];
		$opportunitiesText['conflict'] = [
			'Title' => 'Conflict Management',
			'Description' => '<p>Acting as a mediator to settle conflicts is often required of leaders. Focus on holding other accountable for their actions in a constructive way and search for win-win solutions to conflict. It is not always the case that one party has to win and the other has to lose.</p>',
			'Action Steps' => [
				'<p>Hold team members accountable by ensuring members understand expectations when dealing with issues.</p>',
				'<li>Train team members on how to address conflict and hold each other accountable. Encourage them to devise a system that achieves a win-win resolution.</li>',
				'<li>In terms of managing conflict, there are several steps to holding others accountable. First, pay attention to expectation-performance gaps. If someone is not behaving in a way that is expected, do not ignore it but do not jump to conclusions about who is at fault. Remember - the key to managing confrontations is not to find blame but to resolve the issue and hold others accountable. In the case of an argument, talk with each party face-to-face and show a sincerity to understand both sides. Focus on behaviors and what is preventing someone from delivering.</li>',
				'<li>In many instances it is important to give employees room to handle their own conflict. Debrief with each party separately to coach them.</li>',
				'<li>Resist the urge to always step in and dictate a solution just to make the problem go away. Try to facilitate harmony in the group by being a neutral observer and let them handle conflict.</li>'
			],
		];
		$opportunitiesText['teamwork'] = [
			'Title' => 'Teamwork',
			'Description' => '<p>Create a safe and open environment for others work cooperatively. Ensure team members always treat others with respect and value the diversity of skills, experience, and background.</p>',
			'Action Steps' => [
				'<li>Hold regular team meetings and allow team members to describe tasks and how to complete them.</li>',
				'<li>Set a code of conduct with the team that emphasizes respect and a safe and open environment.</li>',
				'<li>Highlight to the team the value in having diverse and different opinions in task completion and in the general work environment.</li>',
				'<li>Engage everyone on the team to work cooperatively. Delegate tasks to those who are not engaged and look for team members who might be dominating the conversation and thereby shutting others out. Remind your team that you value the skills and special talents of each one.</li>',
				'<li>To encourage teamwork, outcomes must be rewarded and recognized at the team level.</li>'
			],
		];
		$opportunitiesText['communication'] = [
			'Title' => 'Communication',
			'Description' => '<p>Communicating regularly with employees and in particularly, listening to others.</p>',
			'Action Steps' => [
				'<li>Spend time listening to team members on a one-on-one basis and provide feedback to what you heard. Be open and request feedback. If the team members are reluctant to provide feedback then request that each writes down their specific suggestion.</li>',
				'<li>Set time each day to address team members’ communication.</li>',
				'<li>Effective communication entails first being a good listener so practice allowing team members to speak first and even lead team meetings.</li>',
				'<li>Visit team members regularly to encourage their feedback and input and do this in a way that is nonjudgmental.</li>',
				'<li>To get others engaged it might help to specifically assign employees tasks so they feel part of the team. Everyone has talents and interests they care about and tapping into these will help engage the unmotivated employees.</li>'
			],
		];
		$opportunitiesText['respect'] = [
			'Title' => 'Respect',
			'Description' => '<p>These items include factors such as showing genuine concern, and listening and valuing the perspective of all team members.</p>',
			'Action Steps' => [
				'<li>Show respect to the team through personal communication. This is particularly important with larger groups. Know everyone\'s name and take the time to understand their background and family.</li>',
				'<li>Ask questions of team members on their career paths as this will foster mutual respect.</li>',
				'<li>Value the opinions and initiatives of others. Show appreciation for ongoing efforts of the team members and empower them through positive feedback and reinforcement.</li>'
			],
		];

		// Specify dimension parents
		$parent = [
			'commem'        => 'power',
			'autonomy'      => 'power',
			'general'       => 'information',
			'management'    => 'information',
			'feedback'      => 'information',
			'rewards'       => 'rewards',
			'empowerment'   => 'knowledge',
			'mentoring'     => 'knowledge',
			'acquisition'   => 'knowledge',
			'conflict'      => 'relationships',
			'teamwork'      => 'relationships',
			'communication' => 'relationships',
			'respect'       => 'relationships',
		];

		// Calculate the opportunities for the report
		$opportunities['power'] = [];
		$opportunities['information'] = [];
		$opportunities['rewards'] = [];
		$opportunities['knowledge'] = [];
		$opportunities['relationships'] = [];
		foreach ($parent as $dimension => $parentDimension)
		{
			if ($scores['main'][$dimension] < $scores['average'][$dimension])
			{
				shuffle($opportunitiesText[$dimension]['Action Steps']);
				array_push($opportunities[$parentDimension], $opportunitiesText[$dimension]);
			}
		}

		return $opportunities;
	}

	public function getModelRank($reportId, $userId, $jobId)
	{
		$report = Report::findOrFail($reportId);
		$user = User::findOrFail($userId);

		if (! $report->model)
			return false;

		// Get our divisions
		$divisions = [];
		foreach ($report->model->DataDictionary->DataField as $field)
		{
			if ($field->{'@attributes'}->dataType != 'string' || $field->{'@attributes'}->optype != 'categorical' || !isset($field->Value))
				continue;

			foreach ($field->Value as $division)
				$divisions[] = $division->{'@attributes'}->value;
		}

		// Get the scores to the various factors
		$scores = [];
		foreach ($report->model_factors as $factor)
		{
			// Assessment
			if ($factor->type == 'assessment')
			{
				$assessment = Assessment::findOrFail($factor->id);
				$s = new ScoringController();
				$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $jobId);
				$score = $s->score($assignment->id, $jobId);

				$scores[$factor->name] = $score;
			}

			// Dimension
			if ($factor->type == 'dimension')
			{
				$dimension = Dimension::findOrFail($factor->id);
				$assessment = $dimension->assessment;

				// Dimensions from Personality
				if ($assessment->id == get_global('personality'))
				{
					$s = new ScoringController();
					$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $jobId);
					$score = $s->getScoreForDimension($assignment->id, $dimension->id);

					$scores[$factor->name] = $score;
					continue;
				}
			}
		}

		// Check to make sure we don't have any missing factors
		$missingFactors = [];
		foreach ($report->model_factors as $factor)
		{
			if (! array_key_exists($factor->name, $scores))
				$missingFactors[] = $factor->name;
		}
		if (count($missingFactors))
			throw new Exception('Cannot execute the Decision Tree matrix. The following factors are missing or have no scores: '.implode(', ', $missingFactors).'. Make sure '.$user->name.' has completed all assessments related to this job.');

		// Store current attributes and distribution
		$currentNode = $report->model->TreeModel->Node;
		$attributes = $currentNode->{'@attributes'};
		$distribution = $currentNode->ScoreDistribution;

		// Go through the nodes
		$i = 0;
		while (isset($currentNode->Node))
		{
			// For each choice of node
			foreach ($currentNode->Node as $j => $node)
			{
				$pass = false;
				$field = $node->SimplePredicate->{'@attributes'}->field;
				$operator = $node->SimplePredicate->{'@attributes'}->operator;
				$value = $node->SimplePredicate->{'@attributes'}->value;

				// Check if we pass the test
				switch ($operator)
				{
					case 'lessOrEqual':
						if ($scores[$field] <= $value) $pass = true;
						break;

					case 'greaterThan':
						if ($scores[$field] > $value) $pass = true;
						break;
				}

				// If so
				if ($pass)
				{
					// Adjust our distribution
					$attributes = $node->{'@attributes'};
					$distribution = $node->ScoreDistribution;

					// If next node doesn't exist, stop
					if (! isset($node->Node))
						break 2;

					// Continue
					$currentNode = $node;
					$i++;
				}
			}
		}

		// Get the stats for the current score from the distribution array
		$stats = null;
		foreach ($distribution as $category)
			if ($category->{'@attributes'}->value == $attributes->score)
			{
				$stats = $category->{'@attributes'};
				break;
			}

		$rank = $attributes->score;
		$confidence = $stats->confidence * 100 . '%';
		$value = 0;
		if ($rank == 'a') $value = 5;
		if ($rank == 'b') $value = 4;
		if ($rank == 'c') $value = 3;
		if ($rank == 'd') $value = 2;
		if ($rank == 'f') $value = 1;

		return [
			'division' => $value,
			'confidence' => $confidence,
		];
	}
}
