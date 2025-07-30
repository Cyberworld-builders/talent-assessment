<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use Aws\S3\S3Client;
use Bican\Roles\Models\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Dimension;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\AssessmentRequest;
use App\Assessment;
use App\Question;
use Carbon\Carbon;
use App\Mailer;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentsController extends Controller
{
	/**
	 * Show listing of all assessments.
	 *
	 * @return Factory|View
	 */
	public function index()
	{
		$assessments = Assessment::all();

		if (\Auth::user()->is('client'))
		{
			$assessments = Assessment::all()->filter(function ($assessment) {
				$clientAssessments = \Auth::user()->client->assessments;
				if ($clientAssessments)
					return in_array($assessment->id, $clientAssessments);
			});
		}

		return view('dashboard.assessments.index', compact('assessments'));
	}

	/**
	 * Show a single assessment.
	 *
	 * @param $id
	 * @return Factory|View
	 */
	public function show($id)
	{
		$assessment = Assessment::findOrFail($id);
		$questions = $this->paginate_questions($assessment);

		// Create a WM Task if this is a WM assessment
		$task = $assessment->createWMTask();

		return view('dashboard.assessments.show', compact('assessment', 'questions', 'task'));
	}

	/**
	 * Show form for creating a new assessment.
	 *
	 * @return Factory|View
	 */
    public function create()
    {
        $dimensions = Dimension::all();
		$questions = [
			[
				'id' => 0,
				'content' => "This is a sample question",
				'number' => 1,
				'type' => 1,
				'dimension_id' => 0,
				'anchors' => [],
				'practice' => false,
			]
		];

    	return view('dashboard.assessments.create', compact('dimensions', 'questions'));
    }

	/**
	 * Store a new assessment in storage.
	 *
	 * @param AssessmentRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(AssessmentRequest $request)
    {
        $assessment_data = $request->except('questions');
        $question_data = json_decode($request->get('questions'));

		// If target is Self
		if ($assessment_data['target'] == 0)
		{
			$assessment_data['use_custom_fields'] = 0;
			$assessment_data['custom_fields'] = null;
		}

		// If target is Other User
		if ($assessment_data['target'] == 1)
		{
			$assessment_data['use_custom_fields'] = 1;
			$assessment_data['custom_fields'] = [
				'tag' => ['name', 'email'],
				'default' => ['', ''],
			];
		}

		// If target is Group Leader
		if ($assessment_data['target'] == 2)
		{
			$assessment_data['use_custom_fields'] = 1;
			$assessment_data['custom_fields'] = [
				'tag' => ['name', 'email', 'grouprole'],
				'default' => ['', '', ''],
			];
		}

		// Store the logo
		if ($request->file('logo'))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$assessment_data['logo'] = $imageName;
			$assessment_data['logo'] = $result->get('ObjectURL');
		}

		// Store the background
		if ($request->file('background'))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$assessment_data['background'] = $imageName;
			$assessment_data['background'] = $result->get('ObjectURL');
		}

		// Store assessment
        $assessment = new Assessment($assessment_data);
        \Auth::user()->assessments()->save($assessment);

		// Store the questions
        foreach ($question_data as $data)
        {
			$array = json_decode(json_encode($data), true);
            $question = new Question($array);
            $assessment->questions()->save($question);
        }

        return \Response::json([
			'success' => true,
			'redirect' => '/dashboard/assessments/'.$assessment->id.'/edit',
		]);
    }

	/**
	 * Edit a specified assessment in storage.
	 *
	 * @param $id
	 * @return View
	 */
	public function edit($id)
    {
    	$assessment = Assessment::findOrFail($id);
        $dimensions = Dimension::all();
//		$unsorted_questions = $assessment->questions->toArray();

		// Edit the questions and resort them
//		$questions = [];
//		foreach ($unsorted_questions as $i => $question) {
//			$numeric_order = $question['number'];
//			$questions[$numeric_order] = $question;
//		}
//		ksort($questions);

		$questions = $assessment->questions()->orderBy('number', 'asc')->get()->toArray();

    	return view('dashboard.assessments.edit', compact('assessment', 'dimensions', 'questions'));
    }

	/**
	 * Update the specified assessment in storage.
	 *
	 * @param $id
	 * @param AssessmentRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($id, AssessmentRequest $request)
    {
		$assessment_data = $request->except(['questions', 'deleted_questions']);
		$question_data = json_decode($request->get('questions'));
		$deleted_questions = json_decode($request->get('deleted_questions'));

		// If target is Self
//		if ($assessment_data['target'] == 0)
//		{
//			$assessment_data['use_custom_fields'] = 0;
//			$assessment_data['custom_fields'] = null;
//		}
//
//		// If target is Other User
//		if ($assessment_data['target'] == 1)
//		{
//			$assessment_data['use_custom_fields'] = 1;
//			$assessment_data['custom_fields'] = [
//				'tag' => ['name', 'email'],
//				'default' => ['', ''],
//			];
//		}
//
//		// If target is Group Leader
//		if ($assessment_data['target'] == 2)
//		{
//			$assessment_data['use_custom_fields'] = 1;
//			$assessment_data['custom_fields'] = [
//				'tag' => ['name', 'email', 'grouprole'],
//				'default' => ['', '', ''],
//			];
//		}

		// Store the logo
		if (array_key_exists('logo', $assessment_data))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$assessment_data['logo'] = $imageName;
			$assessment_data['logo'] = $result->get('ObjectURL');
		}

		// Store the background
		if (array_key_exists('background', $assessment_data))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$assessment_data['background'] = $imageName;
			$assessment_data['background'] = $result->get('ObjectURL');
		}

		// Update the assessment
    	$assessment = Assessment::findOrFail($id);
    	$assessment->update($assessment_data);

		$valid_ids = $assessment->get_existing_question_ids();
		$questions_without_ids = $this->update_questions($question_data, $valid_ids, $assessment);
		$this->delete_questions($deleted_questions, $valid_ids);

		return \Response::json(['success' => true, 'data' => $question_data, 'non-ids' => $questions_without_ids, 'reload' => true]);
    }

	/**
	 * Show the form for assigning a specified assessment to a user.
	 *
	 * @param $id
	 * @return View
	 */
	public function assign($id)
	{
		$assessment = Assessment::findOrFail($id);
		$usersArray = User::getSelectFormattedArray();
		$clientsArray = Client::getSelectFormattedArray();
		$assessmentsArray = get_select_formatted_array(Assessment::all());
		$emailBody = get_default_email_body();

		return view('dashboard.assessments.assign', compact('assessment', 'usersArray', 'clientsArray', 'assessmentsArray', 'emailBody'));
	}

	/**
	 * Assign an assessment to a user.
	 *
	 * @param Request $request
	 * @return bool|\Illuminate\Http\RedirectResponse
	 */
	public function assign_assessment($id, Request $request)
	{
		$assessment = Assessment::findOrFail($id);
		$data = $request->all();
		$expiration = $data['expiration'];

		if (! $data['user'])
			return false;

		if (! $data['email-subject'])
			$data['email-subject'] = 'New assessments have been assigned to you';

		$validator = Validator::make($data, [
			'assessments' => 'required',
			'user' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		// Find all the users we are assigning to
		$group = [];
		foreach ($data['user'] as $i => $userId)
		{
			$user = User::find($userId);
			if ($user)
				array_push($group, $user);
		}

		// Generate assignments for each user, per assessment
		foreach ($group as $i => $user)
		{
			$assignment_ids = [];
			foreach ($data['assessments'] as $assessment_id)
			{
				$assessment = Assessment::find($assessment_id);

				// Target another User as the subject
				if ($assessment->target == 1 or $assessment->target == 2)
				{
					// If target not assigned, ignore
					if (! $data['target'][$i])
						continue;

					$target = User::find($data['target'][$i]);
					$role = $data['role'][$i];
					$custom_fields = [
						'type' => ['name', 'email', 'role'],
						'value' => [$target->name, $target->email, $role],
					];

					// Generate assignment for user
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $expiration, $data['whitelabel'], $custom_fields, $target->id,$data['reminder'],$data['reminder-frequency']);
					array_push($assignment_ids, $assignment_id);
				}

				// Regular assignment without custom fields
				else
				{

					// Generate assignment for user

					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $expiration, $data['whitelabel'], 0,$user->id,$data['reminder'],$data['reminder-frequency']);
					array_push($assignment_ids, $assignment_id);
				}
			}

			// Email assignment links to the user
			if ($data['send-email'])
			{
				$this->send_assignment_link_to_user($user, $assignment_ids, $expiration, $data['email-subject'], $data['email-body']);
			}
		}

		// Generate string message of assigned assessments
		$assessments_string = '';
		foreach ($data['assessments'] as $assessment_id) {
			$assessment = Assessment::findOrFail($assessment_id);
			$assessments_string .= ', ' . $assessment->name;
		}

		return redirect()->back()->with('success', ($i + 1).' users have been assigned '.count($data['assessments']).' assessments'.$assessments_string);
	}

	/**
	 * Generate assignment for user.
	 *
	 * @param $assessment_id
	 * @param $user
	 * @param $expiration
	 * @param $custom_fields
	 * @return mixed
	 */
	public function generate_assignment_for_user($assessment_id, $user, $expiration, $whitelabel, $custom_fields, $target_id,$reminder,$reminder_frequency)
	{
		$expires = Carbon::createFromFormat('D, d M Y', $expiration);
		$next_reminder = '';
		if ($reminder)
			$next_reminder = strtotime($reminder_frequency);
		else
			$reminder_frequency = '';

		// Create new assignment
		$assignment = new Assignment([
			'assessment_id' => $assessment_id,
			'expires' => $expires,
			'whitelabel' => $whitelabel,
			'target_id' => $target_id,
			'reminder'=>$reminder,
			'next_reminder'=>$next_reminder,
			'reminder_frequency'=> $reminder_frequency
		]);

		// Custom fields
		if ($custom_fields)
			$assignment->custom_fields = $custom_fields;

		$user->assignments()->save($assignment);

		// Generate encrypted url and save it
		$url = Assignment::generateURL($assignment->id, $user->username, $expires);
		$assignment->url = $url;
		$assignment->save();

		//$this->send_assignment_link_to_user($assignment->id);
		return $assignment->id;
	}

	/**
	 * Upload and parse an excel spreadsheet of custom fields.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function upload_from_file(Request $request)
	{
		$data = $request->all();
		$users = [];
		$errors = [];
		$nonUsers = [];
		$clientId = $data['clientId'];

		$validator = Validator::make($data, [
			'file' => 'required|mimes:xls,xlsx'
		]);

		if ($validator->fails())
			return \Response::json(['errors' => ['File must be a valid .xls or a .xlsx file format.']]);

		Excel::load($data['file'], function($reader) use (&$users, &$errors, &$nonUsers, $clientId) {
			$results = $reader->all();

			$reader->each(function($sheet) use (&$users, &$errors, &$nonUsers, $clientId)
			{
				$sheet->each(function($row) use (&$users, &$errors, &$nonUsers, $clientId)
				{
					// These are the columns we need to find
					$searchForColumns = [
						'targetName' => [
							'column' => null,
							'keywords' => ['target', 'name']
						],
						'targetEmail' => [
							'column' => null,
							'keywords' => ['target', 'email']
						],
						'userName' => [
							'column' => null,
							'keywords' => ['name']
						],
						'userEmail' => [
							'column' => null,
							'keywords' => ['email']
						],
						'userRole' => [
							'column' => null,
							'keywords' => ['role']
						]
					];
					$rowArray = $row->toArray();

					// Setup a counter of sorts to keep track of which columns were already found
					$found = [];
					foreach ($rowArray as $column => $value)
						$found[$column] = 0;

					// Find the actual columns
					foreach ($rowArray as $column => $value)
					{
						// For each column we need to find
						foreach ($searchForColumns as $i => $search)
						{
							// If this column has already been found, skip it
							if ($found[$column])
								continue;

							// Search using the keywords
							$keywordsFound = 0;
							foreach ($search['keywords'] as $keyword)
							{
								if (contains_word($column, $keyword))
									$keywordsFound++;
							}

							// If all keywords found in this column name, then we found our column
							if ($keywordsFound == count($search['keywords']))
							{
								$searchForColumns[$i]['column'] = $column;
							}
						}
					}

					// Check that the columns have been found
					if (! $searchForColumns['userEmail']['column'])
						$errors[] = 'Did not find a column for Email. Please check your spreadsheet to make sure it is formatted correctly.';
					if (! $searchForColumns['userName']['column'])
						$errors[] = 'Did not find a column for Name. Please check your spreadsheet to make sure it is formatted correctly.';
					if (! $searchForColumns['userRole']['column'])
						$errors[] = 'Did not find a column for Role. Please check your spreadsheet to make sure it is formatted correctly.';
					if (! $searchForColumns['targetEmail']['column'])
						$errors[] = 'Did not find a column for Target Email. Please check your spreadsheet to make sure it is formatted correctly.';
					if (! $searchForColumns['targetName']['column'])
						$errors[] = 'Did not find a column for Target Name. Please check your spreadsheet to make sure it is formatted correctly.';
					if (!empty($errors))
						return false;

					// Find the user
					$user = null;
					$email = $rowArray[$searchForColumns['userEmail']['column']];
					$name = $rowArray[$searchForColumns['userName']['column']];
					$role = $rowArray[$searchForColumns['userRole']['column']];
					if ($email and $name)
						$user = User::where('email', $email)
							->where('client_id', $clientId)
							->orWhere('name', $name)
							->where('client_id', $clientId)
							->first();

					// If not found, add to list of potential users
					if (! $user)
					{
						$nonUser = [
							'name' => $name,
							'email' => $email
						];
						if (! in_array($nonUser, $nonUsers))
							$nonUsers[] = $nonUser;
					}

					// Find the target
					$target = null;
					$email = $rowArray[$searchForColumns['targetEmail']['column']];
					$name = $rowArray[$searchForColumns['targetName']['column']];
					if ($email and $name)
						$target = User::where('email', $email)
							->where('client_id', $clientId)
							->orWhere('name', $name)
							->where('client_id', $clientId)
							->first();

					// If not found, add to list of potential users
					if (! $target)
					{
						$nonUser = [
							'name' => $name,
							'email' => $email
						];
						if (! in_array($nonUser, $nonUsers))
							$nonUsers[] = $nonUser;
					}

					// If we have both, spit out our data
					if ($user and $target)
					{
						array_push($users, [
							'id' => $user->id,
							'name' => $user->name,
							'email' => $user->email,
							'role' => $role,
							'target_id' => $target->id,
							'target_name' => $target->name,
							'target_email' => $target->email,
						]);
					}

					return true;
				});
			});
		});

		if (! empty($errors))
			return \Response::json(['errors' => $errors]);

		return \Response::json([
			'users' => $users,
			'nonusers' => $nonUsers
		]);
	}

	/**
	 * Creates a new assignment for a new or existing user, and sends them a notification.
	 *
	 * @param Request $request
	 * @param Assessment $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
//	public function generate_assignment_for_assessment(Request $request, $id)
//	{
//		$data = $request->all();
//
//		$user = User::whereEmail($data['email'])->first();
//		if (! $user)
//			$user = $this->create_new_user($data['name'], $data['email']);
//		$expires = Carbon::createFromFormat('D, d M Y', $data['expiration']);
//
//		// Create new assignment
//		$assignment = new Assignment([
//			'assessment_id' => $id,
//			'expires' => $expires
//		]);
//		$user->assignments()->save($assignment);
//
//		// Generate encrypted url and save it
//		$url = Assignment::generateURL($assignment->id, $data['email'], $expires);
//		$assignment->url = $url;
//		$assignment->save();
//
//		$this->send_assignment_link_to_user($assignment->id);
//
//		return redirect('dashboard/assessments/'.$id.'/assign');
//	}

//	public function generate_assignment_for_user($email, $name, $expiration)
//	{
//		$user = User::whereEmail($email)->first();
//		if (! $user)
//			$user = $this->create_new_user($name, $email);
//		$expires = Carbon::createFromFormat('D, d M Y', $expiration);
//
//		// Create new assignment
//		$assignment = new Assignment([
//			'assessment_id' => $this->id,
//			'expires' => $expires
//		]);
//		$user->assignments()->save($assignment);
//
//		// Generate encrypted url and save it
//		$url = Assignment::generateURL($assignment->id, $email, $expires);
//		$assignment->url = $url;
//		$assignment->save();
//
//		$this->send_assignment_link_to_user($assignment->id);
//
//		return true;
//	}

//	/**
//	 * Create a new user and assign basic permissions.
//	 *
//	 * @param $name
//	 * @param $email
//	 * @return User
//	 */
//	public function create_new_user($name, $email)
//	{
//		$user = new User([
//			'name' => $name,
//			'email' => $email
//		]);
//		$user->password = bcrypt($user->generate_password_for_user());
//		$user->save();
//
//		// Set low level user permissions
//		$roleUser = Role::whereSlug('user')->first();
//		$user->attachRole($roleUser);
//
//		return $user;
//	}

	/**
	 * Get assessment questions, paginating them if needed.
	 *
	 * @param $assessment
	 * @return mixed
	 */
	private function paginate_questions($assessment)
	{
		$results = $assessment->questions()->orderBy('number', 'asc')->get();

		if ($assessment->paginate) {
			$results = $assessment->questions()->orderBy('number', 'asc')->simplePaginate($assessment->items_per_page);
		}

		return $results;
	}

	/**
	 * Delete specified questions from storage.
	 *
	 * @param $deleted_questions
	 * @param $valid_ids
	 * @return bool
	 */
	private function delete_questions($deleted_questions, $valid_ids)
	{
		if (empty($deleted_questions))
			return false;

		foreach ($deleted_questions as $deleted_question)
		{
			// Get data in array format
			$deleted_question = json_decode(json_encode($deleted_question), true);

			if (! $deleted_question['id'])
				continue;

			if (! in_array($deleted_question['id'], $valid_ids))
				continue;

			$question = Question::findOrFail($deleted_question['id']);
			$question->delete();
		}

		return true;
	}

	/**
	 * Update each question in storage, creating it if need be.
	 *
	 * @param $question_data
	 * @param $valid_ids
	 * @param $assessment
	 * @return array
	 */
	private function update_questions($question_data, $valid_ids, $assessment)
	{
		$questions_without_ids = [];
		$i = 0;

		if (empty($question_data))
			return false;

		foreach ($question_data as $data)
		{
			// Get data in array format
			$data = json_decode(json_encode($data), true);

			// If question doesn't have an id, it needs to be created
			if (! $data['id'])
			{
				$question = new Question($data);
				$questions_without_ids = array_add($questions_without_ids, $i, $question);
				$assessment->questions()->save($question);
				$i++;
				continue;
			}

			// If not valid id, ignore
			if (! in_array($data['id'], $valid_ids))
				continue;

			// If anchors don't exist in array, create the anchors key
			if (! array_key_exists('anchors', $data))
				$data['anchors'] = '';

			// Update an existing question
			$question = Question::findOrFail($data['id']);
			$question->update($data);
		}

		return $questions_without_ids;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$assessment = Assessment::findOrFail($id);

		$assessment->delete();

		return redirect('dashboard/assessments')->with('success', 'Assessment '.$assessment->name.' and all of its data deleted successfully!');
	}

	/**
	 * Send the specified assignments link to the user.
	 *
	 * @param User $user
	 * @param Array Assignment $ids
	 * @param Carbon $expiration
	 * @param String $subject
	 */
	private function send_assignment_link_to_user($user, $ids, $expiration, $subject, $body)
	{
		//$assignment = Assignment::findOrFail($id);
		//$user = $assignment->user;

		if ($user->email && !filter_var($user->email, FILTER_VALIDATE_EMAIL) === false)
		{
			$mailer = new Mailer();
			//$mailer->send_assignment($user, $id);
			$mailer->send_assignments($user, $ids, $expiration, $subject, $body);
		}
	}

	/**
	 * Create a new WM task, for displaying WM-specific questions using react.
	 *
	 * @param $wmQuestions
	 * @param bool $translation
	 * @return array
	 */
	public function createWMTask($wmQuestions, $translation = false)
	{
		// Randomize all non-practice questions that are complex type (Math letters, Symmetry squares) that are adjacent to each other only
		$keys = [];
		foreach ($wmQuestions as $i => $question)
		{
			// Ignore the non-complex questions
			if ($question->type != 6 && $question->type != 9)
				continue;

			// Ignore practice questions
			if ($question->practice)
				continue;

			// Ignore questions that aren't in a series (if next question is of different type)
			if (array_key_exists($i + 1, $wmQuestions->toArray()) && $wmQuestions[$i + 1]->type != $question->type && $wmQuestions[$i - 1]->type != $question->type)
				continue;

			// Add a flag to randomize this question
			$wmQuestions[$i]->randomize = true;
			$keys[] = $i;
		}

		// Extract the elements that need to be shuffled into a separate array
		$temp = [];
		foreach ($keys as $i => $key)
			$temp[$i] = $wmQuestions[$key];

		// Shuffle them
		shuffle($temp);

		// Place them back
		foreach ($keys as $i => $key)
			$wmQuestions[$key] = $temp[$i];

		// Create a new task
		$task = [
			'blocks' => [],
			'instructs' => [],
			'struct' => []
		];

		// Check for translations
		if ($translation)
			$translatedQuestions = $translation->questions;

		$i = 0;
		foreach ($wmQuestions as $question)
		{
			// Instruction
			if ($question->type == 10)
			{
				// Create a new instruction
				$instruct = [];
				$instruct['text'] = json_decode($question->content)->text;
				$instruct['next'] = json_decode($question->content)->next;

				// Check for translation
				if ($translation)
				{
					$instruct['text'] = json_decode($translatedQuestions->where('question_id', $question->id)->first()->content)->text;
					$instruct['next'] = json_decode($translatedQuestions->where('question_id', $question->id)->first()->content)->next;
				}

				// Add the instruction to our task
				array_push($task['instructs'], $instruct);
			}

			// Problem block
			else
			{
				// Create a new block for each wm question
				$block = [];
				$block['problems'] = [];

				// Figure out if it's a practice question or not
				$block['practice'] = $question->practice;

				// Create a new problem for each question
				$problem = [];

				// Figure out the question type as relating to wm
				$problem['id'] = $i;
				$problem['questionId'] = $question->id;
				$problem['type'] = $question->getTypeSlug();

				// Text Input
				if ($problem['type'] == 'input')
					$problem['text'] = $question->content;

				// Letter Sequence
				if ($problem['type'] == 'ls')
					$problem['letters'] = explode(',', $question->content);

				// Math Equation
				if ($problem['type'] == 'eq')
					$problem['equation'] = $question->content;

				// Math and Letters
				if ($problem['type'] == 'eqls')
				{
					$problem['letters'] = json_decode($question->content)->letters;
					$problem['equations'] = json_decode($question->content)->equations;
				}

				// Square Sequence
				if ($problem['type'] == 'sq')
					$problem['squares'] = json_decode($question->content);

				// Symmetry
				if ($problem['type'] == 'sy')
					$problem['symmetry'] = json_decode($question->content);

				// Square Symmetry
				if ($problem['type'] == 'sysq')
				{
					$problem['squares'] = json_decode($question->content)->squares;
					$problem['symmetries'] = json_decode($question->content)->symmetries;
				}

				// Add the problem to the block
				array_push($block['problems'], $problem);

				// Finally add the block to our task
				array_push($task['blocks'], $block);
			}

			// Add it to the structure as well
			array_push($task['struct'], [
				'id'   => ($question->type == 10) ? sizeof($task['instructs'])-1 : sizeof($task['blocks'])-1,
				'type' => ($question->type == 10) ? 'inst' : 'block',
			]);

			$i++;
		}

		return $task;
	}
}