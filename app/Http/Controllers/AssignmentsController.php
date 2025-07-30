<?php

namespace App\Http\Controllers;

use App\Client;
use App\EmailAddress;
use App\Job;
use App\Mailer;
use App\Question;
use App\Reseller;
use App\Translation;
use App\User;
use App\Answer;
use App\Assessment;
use App\Assignment;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class AssignmentsController
 * @package App\Http\Controllers
 */
class AssignmentsController extends Controller {

    /**
     * Display a listing of assignments for the currently authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        $assignments = $user->assignments;
		$client = $user->client;

		if (! session('reseller'))
			$jaqs = $user->jaqs->where('sent', 1);
		else
			$jaqs = null;

        if ($user->level() > 1)
            return view('dashboard.assignments.index', compact('user', 'assignments'));

        if (($client->require_profile && !$user->completed_profile) || ($client->require_research && !$user->completed_research))
            return redirect('/profile');

        return view('assignment.index', compact('user', 'assignments', 'jaqs'));
    }

	public function indexResellers($id)
	{
		if (\Auth::user())
			return $this->index();

		$reseller = Reseller::findOrFail($id);

		// Change the connection to the Reseller's database
		\Config::set('database.connections.mysql.host', $reseller->getDbHost());
		\Config::set('database.connections.mysql.database', $reseller->getDbName());
		\Config::set('database.connections.mysql.username', $reseller->getDbUser());
		\Config::set('database.connections.mysql.password', $reseller->getDbPass());
		DB::reconnect('mysql');

		// Store the reseller in the session
		session(['reseller' => $reseller]);

		return redirect('/login');
    }

	/**
	 * Show the view for bulk editing assignments.
	 *
	 * @param $id
	 * @return View
	 */
	public function bulk($id)
    {
    	$client = Client::findOrFail($id);
        $users = $client->users;

        $all_assignments = [];
        foreach ($users as $user)
        	foreach (Assignment::where('user_id', $user->id)->get() as $assignment)
				$all_assignments[] = $assignment;

        $final_data = [];
        foreach ($all_assignments as $assignment)
        	if ($assignment)
				$final_data[] = [
					'assignment_id' => $assignment->id,
					'assessment' => $assignment->assessment()->name,
					'assigned_on' => $assignment->created_at,
					'expires' => $assignment->expires,
					'user' => $assignment->user
				];

        $assignments = $final_data;

        return view('dashboard.assignments.bulk', compact('assignments', 'client'));
    }

	/**
	 * Bulk update assignments.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function bulk_update(Request $request)
    {
        $data = $request->all();

        if (isset($data['assignments']))
        {
            foreach ($data['assignments'] as $assignment_id)
            {
				$assignment = Assignment::findOrFail($assignment_id);
				$expires = Carbon::createFromFormat('D, d M Y', $data['expiration']);
				$next_reminder = '';
				$reminder_frequency = '';
				if ($data['reminder'] == 1)
				{
					$next_reminder = strtotime($data['reminder-frequency']);
					$reminder_frequency = $data['reminder-frequency'];
				}

				if (! isset($data['whitelabel']))
					$data['whitelabel'] = $assignment->whitelabel;

				$assignment->update([
					'expires' => $expires,
					'whitelabel' => $data['whitelabel'],
					'reminder' => $data['reminder'],
					'reminder_frequency' => $reminder_frequency,
					'next_reminder' => $next_reminder
				]);
			}

        	return redirect()->back()->with('success', 'Selected Assignments has been updated successfully!');
        }
        else
            return redirect()->back()->with('failed', 'Please select assignment(s)');
    }

    /**
     * Show the form for creating a new assignment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return a view here for creating an assignment
    }

    /**
     * Store a newly created assignment in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return a view here for storing an assignment
    }

	/**
     * Assignments view for clients.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function assignments($id)
    {
		$client = Client::findOrFail($id);
        $users = $client->users;
		$emailBody = get_default_email_body();

		$userIds = [];
        foreach ($users as $user)
            array_push($userIds, $user->id);

        $assignments = Assignment::all()->filter(function($assignment) use ($userIds) {
            return in_array($assignment->user_id, $userIds);
        });

        $dates = [];
        foreach ($assignments as $assignment)
		{
			$date = $assignment->created_at->format('Y-m-d H:i');

			if (! array_key_exists($date, $dates))
				$dates[$date] = [
					'assignments' => [],
					'assessments' => [],
					'users' => []
				];

			array_push($dates[$date]['assignments'], $assignment);

			// Get assessment
			if ($assignment->assessment() && !in_array($assignment->assessment(), $dates[$date]['assessments']))
				array_push($dates[$date]['assessments'], $assignment->assessment());

			// Get users
			$user = User::find($assignment->user_id);
			if ($user && !in_array($user, $dates[$date]['users']))
				array_push($dates[$date]['users'], $user);
		}
		krsort($dates);

        return view('dashboard.clients.dates', compact('client', 'dates', 'emailBody'));
    }

	/**
	 * Assignments view for clients.
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
	 */
	public function assignmentsForDate($id, $date)
	{
		$client = Client::findOrFail($id);
		$users = $client->users;

		$userIds = [];
		foreach ($users as $user)
			array_push($userIds, $user->id);

		$assignments = Assignment::all()->filter(function($assignment) use ($userIds, $date) {
			return (in_array($assignment->user_id, $userIds) && $assignment->created_at->format('Y-m-d H:i') == $date);
		});

		return view('dashboard.clients.assignments', compact('client', 'assignments', 'report', 'date'));
	}



    /**
     * Store answer responses for a specific assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_answers($id, Request $request)
    {
        $assignment = Assignment::findOrFail($id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);
        $data = $request->all();
        $complete = $data['complete'];

        // Safety check to make sure we're still on the same assignment
        if (! \Auth::user()->owns($assignment))
            return \Response::json(['errors' => 'Assignment not assigned to this user']);

        // Complete assignment if done
        if ($complete && $this->complete($id)) {
            return \Response::json(['success' => true, 'reload' => true]);
        }

        // Check if assignment isn't complete yet
        if ($assignment->completed)
            return \Response::json(['errors' => 'This assignment has already been completed']);

        $valid_ids = $assessment->get_existing_question_ids();
        $question_id = $data['question_id'];
        $value = $data['value'];

        // If question id isn't valid, ignore this answer
        if (! in_array($question_id, $valid_ids))
            return \Response::json(['errors' => 'Question ID not valid']);

        $this->update_answer($assignment, $question_id, $value);

        return \Response::json(['success' => true]);
    }

	public function wm_save(Request $request)
	{
		$data = $request->all();
		$assignment = Assignment::findOrFail($data['assignmentId']);
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$valid_ids = $assessment->get_existing_question_ids();
		$problems = json_decode($data['json']);

		foreach ($problems as $problemArray)
		{
			$problem = $problemArray[0];

			// If question id isn't valid, ignore this answer
			if (! in_array($problem->questionId, $valid_ids))
				continue;

			// Get question type
			$question = Question::find($problem->questionId);
			$slug = $question->getTypeSlug();
			$value = null;

			// Input value
			if ($slug == 'input')
				$value = $problem->response;

			// Letter sequence value
			if ($slug == 'ls')
				$value = serialize(['response' => $problem->response, 'options' => $problem->options]);

			// Math equation
			if ($slug == 'eq')
				$value = $problem->response;

			// Math and letters value
			if ($slug == 'eqls')
			{
				$value = [
					'letters' => ['options' => $problem->letters->options, 'response' => $problem->letters->response, 'time' => $problem->letters->time],
					'equations' => []
				];

				foreach ($problem->equations as $equation)
					array_push($value['equations'], ['response' => $equation->response, 'time' => $equation->time]);

				$value = serialize($value);
			}

			// Square sequence value
			if ($slug == 'sq')
				$value = serialize($problem->response);

			// Symmetry value
			if ($slug == 'sy')
				$value = $problem->response;

			// Symmetry squares value
			if ($slug == 'sysq') {
				$value = [
					'squares' => ['response' => $problem->squares->response, 'time' => $problem->squares->time],
					'symmetries' => []
				];

				foreach ($problem->symmetries as $symmetry)
					array_push($value['symmetries'], ['response' => $symmetry->response, 'time' => $symmetry->time]);

				$value = serialize($value);
			}

			// Set the response time
			$time = null;
			if (property_exists($problem, 'time'))
				$time = $problem->time;

			// Save our answer
			$this->update_answer($assignment, $problem->questionId, $value, $time);
		}

		// If preview
		//if (json_decode($data['preview']))
		//	dd($assignment->answers->toArray());


		return \Response::json([
			'success' => true,
		]);
    }

	/**
     * Complete the specified assignment.
     *
     * @param $id
     * @return bool
     */
    public function complete($id)
    {
        $assignment = Assignment::findOrFail($id);

        $assignment->completed = 1;
        $assignment->completed_at = Carbon::now();
        $assignment->save();

        $this->send_completion_notification_to_user($id);

        return true;
    }

	/**
     * Show the form for assigning assessments to users.
     *
     * @return View
     */
    public function assign()
    {
        $clients = Client::all();
        $assessments = Assessment::all();

        if (\Auth::user()->is('client'))
        {
            $assessments = '';
            if (\Auth::user()->client->assessments)
            {
                $assessments = Assessment::all()->filter(function ($assessment) {
                    return in_array($assessment->id, \Auth::user()->client->assessments);
                });
            }
        }

        $assessmentsArray = [];
        if ($assessments)
        {
            foreach ($assessments as $assessment)
                $assessmentsArray[$assessment->id] = $assessment->name;
        }

        $clientsArray = [null => '---'];
        foreach ($clients as $client)
            $clientsArray[$client->id] = $client->name.' ('.$client->users->count().' users)';

        $usersArray = [];
        foreach (User::all() as $user)
            $usersArray[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';

        return view('dashboard.assignments.assign', compact('assessmentsArray', 'clientsArray', 'custom_fields', 'usersArray'));
    }

	/**
	 * Show the form for assigning assessments to users.
	 *
	 * @return View
	 */
	public function assignToClient($id)
	{
		$client = Client::findOrFail($id);
		$usersArray = User::getSelectFormattedArrayForClient($id);
		$emailBody = get_default_email_body();
		$assessments = Assessment::all();
		if (session('reseller'))
			$assessments = Assessment::find(session('reseller')->assessments);
		$assessmentsArray = get_select_formatted_array($assessments);

		$jobsArray = [null => 'No'] + get_select_formatted_array(Job::where('client_id', $client->id)->get());

		$jobFamilies = [];
		foreach ($client->users as $user)
			if ($user->job_family && !in_array($user->job_family, $jobFamilies))
				$jobFamilies[] = $user->job_family;

		$surveys = [];
		foreach ($assessments as $assessment)
		{
			if ($assessment->target == 0)
				continue;

			$query = DB::table('assignments')
				->join('users', 'users.id', '=', 'assignments.user_id')
				->select('assignments.*')
				->where('users.client_id', '=', $client->id)
				->where('assignments.assessment_id', '=', $assessment->id)
				->groupBy('created_at')
				->get();

			$query = array_reverse($query);

			$surveys[$assessment->id] = $query;
		}

		$surveysArray = [null => 'No'];
		foreach ($surveys as $assessmentId => $assignments)
		{
			if (empty($assignments))
				continue;

			foreach ($assignments as $assignment)
			{
				$assessment = Assessment::find($assignment->assessment_id);
				$date = Carbon::createFromFormat('Y-m-d H:i:s', $assignment->created_at);
				$surveysArray[$assignment->created_at] = $assessment->name . ' on ' . $date->format('l, F jS, Y, H:i:s') . ' (' . $date->diffForHumans() . ')';
			}
		}

		$oldInput = session()->getOldInput();

		return view('dashboard.assignments.assignToClient', compact('client', 'usersArray', 'assessmentsArray', 'emailBody', 'jobFamilies', 'jobsArray', 'oldInput', 'surveysArray'));
	}

	/**
	 * Check if any assessments to be assigned have already been assigned before.
	 *
	 * @param Request $request
	 * @return mixed
	 */
	public function verifyAssessments(Request $request)
	{
		$data = $request->all();

		// Gather assessments
		$assessments = null;
		foreach ($data['assessments'] as $assessmentId)
		{
			$assessment = Assessment::find($assessmentId);
			$assessments[] = $assessment;
		}

		// Gather users
		$users = null;
		foreach ($data['user'] as $userId)
		{
			$user = User::find($userId);
			if (!$user)
				continue;

			// Find duplicate assignments
			$duplicates = null;
			foreach ($user->assignments as $assignment)
			{
				if ($assignment->assessment() && in_array($assignment->assessment()->id, $data['assessments']))
				{
					$assessment = $assignment->assessment();
					$assessment->assignment = $assignment;
					$duplicates[] = $assessment;
				}
			}
			$user->duplicates = $duplicates;
			$users[] = $user;
		}

		// Construct message
		$message = '';
		foreach ($users as $user)
		{
			if (!$user->duplicates)
				continue;

			$message .= '<strong>' . $user->name . '</strong>:<br/>';
			foreach ($user->duplicates as $assessment)
			{
				$assigned = ' <span class="text-muted">Assigned '.$assessment->assignment->created_at->diffForHumans().'</span>';
				$completed = ' <span class="text-small text-danger">Not Completed</span>';
				if ($assessment->assignment->completed)
					$completed = ' <span class="text-small text-success">Completed</span>';
				$message .= '- ' . $assessment->name . $assigned . $completed . '</br>';
			}
			$message .= '<br/>';
		}

		if ($message)
			$message = 'The following users already have these assessments assigned to them:<br/><br/><div style="overflow-y:scroll;max-height:400px;">' . $message . '</div><br/>Are you sure wish to assign these assessments again?';

		return \Response::json($message);
	}

	/**
	 * Assign an assessment to a user.
	 *
	 * @param Request $request
	 * @return bool|\Illuminate\Http\RedirectResponse
	 */
	public function assignAssessment(Request $request)
	{
		$data = $request->all();
		$expiration = $data['expiration'];

		if (! $data['user'])
			return false;

		if (! $data['email-subject'])
			$data['email-subject'] = 'New assessments have been assigned to you';

		// If whitelabel option is not present, such as when client admin is assigning
		if (! key_exists('whitelabel', $data))
		{
			// If it's a client admin, set it to their whitelabel preferences
			if (\Auth::user()->is('client'))
				$data['whitelabel'] = \Auth::user()->client->whitelabel;

			// Otherwise, don't whitelabel
			else
			$data['whitelabel'] = 0;
		}

		// Compatibility with reseller dash
		if (! key_exists('job_id', $data))
			$data['job_id'] = '';

		// Sanitize the user data input (by default it's catching the template user as well)
		foreach ($data['user'] as $i => $userId)
			if ($userId == 0)
			{
				unset($data['user'][$i]);
				unset($data['target'][$i]);
				unset($data['role'][$i]);
			}

		$validator = Validator::make($data, [
			'assessments' => 'required',
			'user' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		// Check to make sure we have targets set
		foreach ($data['user'] as $i => $userId)
		{
			if (! $userId)
				continue;

			foreach ($data['assessments'] as $assessment_id)
			{
				$assessment = Assessment::find($assessment_id);
				if ($assessment->target == 1 or $assessment->target == 2)
				{
					// If target not assigned, throw error
					if (! $data['target'][$i])
						return redirect()->back()->withInput()->withErrors('Targets must be set when assigning development assessments');
				}
			}
		}

		// Set survey date
		$created_at = Carbon::now();
		if (key_exists('created_at', $data) && $data['created_at'])
			$created_at = $data['created_at'];

		// Generate assignments for each user, per assessment
		foreach ($data['user'] as $i => $userId)
		{
			$user = User::find($userId);
			if (! $user)
				continue;

			$assignment_ids = [];
			foreach ($data['assessments'] as $assessment_id)
			{
				$assessment = Assessment::find($assessment_id);

				// Target another User as the subject
				if ($assessment->target == 1 or $assessment->target == 2)
				{
					$target = User::find($data['target'][$i]);
					$role = $data['role'][$i];
					$custom_fields = [
						'type' => ['name', 'email', 'role'],
						'value' => [$target->name, $target->email, $role],
					];

					// Generate assignment for user
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $data['job_id'], $expiration, $data['whitelabel'], $custom_fields, $target->id, $created_at);
					array_push($assignment_ids, $assignment_id);
				}

				// Self-report version
				elseif ($assessment->id == get_global('leader-sr'))
				{
					$custom_fields = [
						'type' => ['name', 'email', 'role'],
						'value' => [$user->name, $user->email, "Self"],
					];

					// Generate assignment for user
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $data['job_id'], $expiration, $data['whitelabel'], $custom_fields, $user->id, $created_at);
					array_push($assignment_ids, $assignment_id);
				}

				// Regular assignment without custom fields
				else
				{
					// Generate assignment for user
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $data['job_id'], $expiration, $data['whitelabel'], 0, 0, $created_at);
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
	 * Re-send the assignment email to users.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function sendAssignmentEmail(Request $request)
	{
		$data = $request->all();

		foreach ($data['users'] as $userId)
		{
			$user = User::find($userId);
			if (! $user)
				continue;

			// Find user assignments and expiration
			$assignments = Assignment::all()->filter(function($assignment) use ($user, $data) {
				return (($assignment->user_id == $user->id) && ($assignment->created_at->format('Y-m-d H:i') == $data['date']));
			});
			$assignment_ids = get_property_list($assignments, 'id');
			$expiration = $assignments->first()->expires->format('D, d M Y');

			$this->send_assignment_link_to_user($user, $assignment_ids, $expiration, $data['subject'], $data['message']);
		}

		return \Response::json(['success' => true]);
	}

	/**
     * Assign an assessment to a user.
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    public function assign_assessment2(Request $request)
    {
		$data = $request->all();
		$expiration = $data['expiration'];

		if (! $data['user'])
			return false;

		if (! $data['assessments'])
			return false;

		if (! array_key_exists('whitelabel', $data))
			$data['whitelabel'] = 0;

		if (! $data['email-subject'])
			$data['email-subject'] = 'New assessments have been assigned to you';

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
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $expiration, $data['whitelabel'], $custom_fields, $target->id);
					array_push($assignment_ids, $assignment_id);
				}

				// Regular assignment without custom fields
				else
				{
					// Generate assignment for user
					$assignment_id = $this->generate_assignment_for_user($assessment->id, $user, $expiration, $data['whitelabel'], 0, 0, $user->id);
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
     * Display the landing page for the assignment the user is about to take.
     *
     * @param Assignment $id
     * @return Factory|View
     */
    public function stage($id)
    {
        $user = \Auth::user();
        $assignment = Assignment::findOrFail($id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);
		$task = null;

        return view('assignment.stage', compact('user', 'assignment', 'assessment', 'task'));
    }

	/**
	 * Display the landing page for the assessment without the usual authentication.
	 *
	 * @param Assignment $name
	 * @return Factory|View
	 */
	public function stageWithoutAuth($name)
	{
		$id = Assessment::getSampleTestId($name);
		$assessmentName = $name;

		if (! $id)
			return view('error', ['message' => 'You are trying to access an assessment that does not exist.']);

		$assessment = Assessment::findOrFail($id);
		$assignment = new Assignment([
			'assessment_id' => $assessment->id,
			'whitelabel' => 0
		]);
		$assignment->url = '/assessment/sample/'.$name.'/take';
		$r1 = (random_int(1000,9999) / 100);
		$r2 = 100 / $r1;
		$assignment->code = base64_encode($r1.'-'.$r2);
		$user = null;
		$task = null;

		return view('assignment.sample', compact('user', 'assignment', 'assessment', 'task', 'assessmentName'));
	}

	/**
	 *	Send reminder email.
	 */
	public function send_reminder()
	{
        $today = strtotime('today');
        $tomorrow = strtotime('tomorrow');

        $assignment = Assignment::where('next_reminder', '>' ,$today)->where('next_reminder','<',$tomorrow)->where('reminder','=',1)->get();
        foreach ($assignment as $assign)
        {
            // Reminder found, send notification

			// Update next reminder date
            $assignment = Assignment::findOrFail($assign['id']);
			$assignment->update([
				'next_reminder' => strtotime('+2 weeks')
			]);
        }
    }

	public function completeSample($name)
	{
		$message = 'This concludes the sample assessment. ';
		if ($name == 'personality') $message .= 'Thank you for exploring the power of personality for talent assessment!';
		if ($name == 'wmo' || $name == 'wms') $message .= 'Thank you for exploring the powerful assessment of working memory!';

		return view('dashboard.reports.sample', ['message' => $message, 'assessmentName' => $name]);
	}

	/**
     * Show assignment details for a specific assignment.
     *
     * @param $assignment_id
     * @return View
     */
    public function show_assignment_details($assignment_id)
    {
        $assignment = Assignment::findOrFail($assignment_id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);
        $answers = $assignment->answers;
        $questions = $assessment->questions()->orderBy('number', 'asc')->get();
        $user = User::findOrFail($assignment->user_id);
		$task = null;

		// Get total of correct answers
		if ($assessment->id == get_global('ability'))
		{
			$score = 0;
			foreach ($assignment->answers as $answer)
				$score += $answer->score();
			$assignment->score = $score;
		}

        return view('dashboard.assignments.show', compact('assessment', 'questions', 'answers', 'user', 'assignment', 'task'));
    }


	/**
	 * Display the specified assignment.
	 *
	 * @param  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$assignment = Assignment::findOrFail($id);
		$user = User::findOrFail($assignment->user_id);

		// Check if the link token is valid
		// !! Link checking should be done through the dashboard controller
		if (! Assignment::checkURL($id))
			abort(403, 'Invalid session token');

		// Check if the link is already expired
		if ($assignment->expires < Carbon::now())
			abort(403, 'Link expired'); // This should be a view probably

		// Try to authenticate with the user
		if (! \Auth::loginUsingId($user->id))
			abort(403, 'Invalid user');

		// Safety check to make sure we're on the right assignment
		if (! $user->owns($assignment))
			abort(403, 'Not allowed to access this assignment');

		// Check if assignment is already completed
		if ($assignment->completed)
			return view('assignment.complete');

		// Find the assessment
		$assessment = Assessment::findOrFail($assignment->assessment_id);

		// Show paginated results if necessary
		if ($assessment->paginate) {
			$questions = $assessment->questions()->orderBy('number', 'asc')->simplePaginate($assessment->items_per_page);
			$questions->appends(['u' => $_GET['u'], 'e' => $_GET['e'], 't' => $_GET['t']])->render();
		}

		// Otherwise, show regular results
		else {
			$questions = $assessment->questions()->orderBy('number', 'asc')->get();
		}

		// Store when the assignment was started
		if (! $assignment->started_at)
		{
			$assignment->started_at = Carbon::now();
			$assignment->save();
		}

		// Send along the client for white-labeling the assignment
		$client = $user->client;

		// Create a WM Task if this is a WM assessment
		$task = $assessment->createWMTask($assignment);

		return view('assignment.show', compact('id', 'assessment', 'questions', 'assignment', 'user', 'task'));
	}

	/**
	 * Display the specified assignment without the usual authentication.
	 *
	 * @param  $name
	 * @return \Illuminate\Http\Response
	 */
	public function showWithoutAuth($name, $code, Request $request)
	{
		$id = Assessment::getSampleTestId($name);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'email' => 'required|email',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		// Check for valid code
		$ar = explode('-', base64_decode($code));
		if (round($ar[0] * $ar[1]) != 100)
			return view('error', ['message' => 'Something is wrong with your authentication code. No cheating now!']);

		// Check for valid assessment
		if (! $id)
			return view('error', ['message' => 'You are trying to access an assessment that does not exist.']);

		// Store email info
		$email = EmailAddress::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'assessment_id' => $id
		])->save();

		$assessment = Assessment::findOrFail($id);
		$startDate = Carbon::now();
		$assignment = new Assignment([
			'id' => 123456789,
			'short_name' => $name,
			'user_id' => null,
			'assessment_id' => $assessment->id,
			'started_at' => $startDate,
			'completed_at' => $startDate,
			'expires' => $startDate,
			'completed' => 0,
			'whitelabel' => 0,
			'target_id' => 0,
			'reminder' => 0,
			'next_reminder' => null,
			'reminder_frequency' => null,
			'job_id' => null,
			'custom_fields' => null,
		]);
		$user = null;
		$translation = null;
		//$translation = Translation::where('assessment_id', $assessment->id)->where('language_id', $user->language_id)->first();

		// Show paginated results if necessary
		if ($assessment->paginate) {
			$questions = $assessment->questions()->orderBy('number', 'asc')->simplePaginate($assessment->items_per_page);
			//$questions->appends(['u' => $_GET['u'], 'e' => $_GET['e'], 't' => $_GET['t']])->render();
		}

		// Otherwise, show regular results
		else {
			$questions = $assessment->questions()->orderBy('number', 'asc')->get();
		}

		// Get all WM specific questions
		$wmQuestions = $assessment->questions()->orderBy('number', 'asc')->get()->filter(function($question) {
			return $question->isWMType();
		});
		$task = null;
		$assessmentsController = new AssessmentsController();
		if ($wmQuestions and !$wmQuestions->isEmpty())
			$task = $assessmentsController->createWMTask($wmQuestions, $translation);

		return view('assignment.show', compact('id', 'assessment', 'questions', 'assignment', 'user', 'task'));
	}

	public function edit_assignment_details($assignment_id,Request $request)
    {
        $data = $request->all();
        $assignment = Assignment::findOrFail($assignment_id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);
        $answers = $assignment->answers;
        $questions = $assessment->questions;
        $user = User::findOrFail($assignment->user_id);
        $expires = date('D, d M Y',strtotime($assignment->expires));
        if (count($data) > 0)
        {
			$expiration = $data['expiration'];
			$whitelabel = $data['whitelabel'];
			$this->update_assignment_for_user($assignment_id,$user,$expiration,$whitelabel);
		    $assignment = Assignment::findOrFail($assignment_id);
		    $expires = date('D, d M Y',strtotime($assignment->expires));

		}

        return view('dashboard.assignments.edit_assignment', compact('assessment', 'questions', 'answers', 'user', 'assignment','expires'));
    }

	/**
	 * Show all assignments for a specific user.
	 *
	 * @param $user_id
	 * @return View
	 */
	public function show_assignments_for_user($user_id)
	{
		$user = User::findOrFail($user_id);
		$assignments = $user->assignments;

		return view('dashboard.assignments.index', compact('user', 'assignments'));
	}

	public function update_assignment_for_user($assignment_id, $user, $expiration, $whitelabel)
    {
        $expires = Carbon::createFromFormat('D, d M Y', $expiration);

        $assignment = Assignment::findOrFail($assignment_id);
        $assignment->update([
            'expires' => $expires,
            'whitelabel' => $whitelabel
        ]);

      	return $assignment->id;
    }

	/**
     * Download assignment data to excel.
     *
     * @param int Assignment $id
     * @return View
     */
    public function download_assignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);
        $answers = $assignment->answers;
        $user = User::findOrFail($assignment->user_id);

        $questions = $assessment->filteredQuestions();
//        foreach ($questions as $question)
//        {
//            if (! $question->dimension())
//            {
//                $question->dimension_code = $question->number;
//                continue;
//            }
//
//            $question->dimension_code = '';
//
//            if ($question->dimension()->parent_exists())
//                $question->dimension_code .= $question->dimension()->getParent()->code;
//
//            $question->dimension_code .= $question->dimension()->code;
//            $question->dimension_code .= $question->number;
//        }

        $filename = $assessment->name.' for '.$user->name.' '.$assignment->completed_at;

        $data = Excel::create($filename, function($excel) use ($assessment, $questions, $answers, $user, $assignment)
        {
            $excel->setTitle($assessment->name.' Details');
            $excel->sheet('Details', function($sheet) use ($assessment, $questions, $answers, $user, $assignment) {
                $sheet->loadView('excel.assignments.show', compact('assessment', 'questions', 'answers', 'user', 'assignment'));
            });
        });

        $data->store('csv')->export('csv');

        //return view('excel.assignments.show', compact('assessment', 'questions', 'answers', 'user', 'assignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$assignment = Assignment::findOrFail($id);
		$assessment = Assessment::findOrFail($assignment->assessment_id);
		$user = User::findOrFail($assignment->user_id);
		$client = $user->client;
		$assignment->expiration = date('D, d M Y', strtotime($assignment->expires));
        $switch_reminder = 'none';
        if($assignment->reminder){
           // $assignment->reminder = 'true';
            $switch_reminder = 'block';
        }
		$jobsArray = [null => 'No'] + get_select_formatted_array(Job::where('client_id', $client->id)->get());

		$surveysArray = [null => 'No'];
		if ($assessment->target)
		{
			$surveys = DB::table('assignments')
				->join('users', 'users.id', '=', 'assignments.user_id')
				->select('assignments.*')
				->where('users.client_id', '=', $client->id)
				->where('assignments.assessment_id', '=', $assessment->id)
				->groupBy('created_at')
				->get();

			$surveys = array_reverse($surveys);

			foreach ($surveys as $survey)
			{
				$date = Carbon::createFromFormat('Y-m-d H:i:s', $survey->created_at);
				$surveysArray[$survey->created_at] = $assessment->name . ' on ' . $date->format('l, F jS, Y, H:i:s') . ' (' . $date->diffForHumans() . ')';
			}
		}

		return view('dashboard.assignments.edit', compact('client', 'assignment', 'assessment', 'user', 'switch_reminder', 'jobsArray', 'surveysArray'));
    }

    /**
     * Update the specified assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$data = $request->all();
		$assignment = Assignment::findOrFail($id);
		$expires = Carbon::createFromFormat('D, d M Y', $data['expiration']);

		if (! array_key_exists('job_id', $data) || $data['job_id'] == '')
			$data['job_id'] = null;

		if (! array_key_exists('whitelabel', $data))
			$data['whitelabel'] = $assignment->whitelabel;

		// Setup reminders (needs to be cleaned up)
        $next_reminder = '';
        $reminder_frequency = '';
		if (array_key_exists('reminder', $data) && $data['reminder'] == 1)
		{
			$next_reminder = strtotime($data['reminder-frequency']);
			$reminder_frequency = $data['reminder-frequency'];
		}
		else
			$reminder_frequency = '';

		$assignment->update([
			'expires' => $expires,
			'whitelabel' => $data['whitelabel'],
			'job_id' => $data['job_id'],
			'created_at' => $data['created_at'],
		]);

		if ($next_reminder)
		{
			$assignment->update([
				'reminder' => $data['reminder'],
				'reminder_frequency' => $reminder_frequency,
				'next_reminder' => $next_reminder
			]);
		}

		return redirect()->back()->with('success', 'Assignment has been updated successfully!');
    }

    /**
     * Remove the specified assignment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);

        if (Auth::user()->is('client') && ($assignment->user->client != Auth::user()->client))
        	abort('403', 'Error: Trying to delete non-existing assignment.');

        // Delete the assignment itself
        $assignment->delete();

        return redirect()->back()->with('success', 'Assignment has been deleted successfully!');
    }

	/**
     * Update the time left for a specified assignment.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_time_limit($id)
    {
        return \Response::json($this->getTimeLimit($id));
    }

	/**
     * Get time left until the time limit is reached for a specific assignment.
     *
     * @param $id
     * @return int
     */
    public function getTimeLimit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assessment = Assessment::findOrFail($assignment->assessment_id);

        return (60 * $assessment->time_limit) - (Carbon::now()->timestamp - $assignment->started_at->timestamp);
    }

	/**
	 * Update the specified answer in storage, creating one if it does not exist.
	 *
	 * @param $assignment
	 * @param $question_id
	 * @param $value
	 * @param null $time
	 * @return bool
	 */
    private function update_answer($assignment, $question_id, $value, $time = null)
    {
    	// Cannot save null value, so ignore it
		if ($value == null)
			return false;

    	// Find existing answer
        $answer = $assignment->answers()->where('question_id', $question_id)->first();

        // If answer doesn't exist, create a new one
        if (empty($answer->id))
        {
            $answer = new Answer([
                'question_id' => $question_id,
                'user_id'     => $assignment->user_id,
                'value'       => $value,
                'time'        => $time
            ]);
            $assignment->answers()->save($answer);
            return true;
        }

        // Otherwise, update it
        $answer->update([
            'question_id' => $question_id,
            'user_id'     => $assignment->user_id,
            'value'       => $value,
            'time'        => $time,
        ]);

        return true;
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
	public function generate_assignment_for_user($assessment_id, $user, $job_id, $expiration, $whitelabel, $custom_fields, $target_id, $created_at = null)
	{
		$expires = Carbon::createFromFormat('D, d M Y', $expiration);

		// Create new assignment
		$assignment = new Assignment([
			'assessment_id' => $assessment_id,
			'expires' => $expires,
			'whitelabel' => $whitelabel,
			'target_id' => $target_id,
		]);

		// Job Id
		if ($job_id)
			$assignment->job_id = $job_id;

		// Custom fields
		if ($custom_fields)
			$assignment->custom_fields = $custom_fields;

		$user->assignments()->save($assignment);

		// Force created_at date
		if ($created_at)
		{
			$assignment->created_at = $created_at;
			$assignment->save();
		}

		// Generate encrypted url and save it
		$url = Assignment::generateURL($assignment->id, $user->username, $expires);
		$assignment->url = $url;
		$assignment->save();

		//$this->send_assignment_link_to_user($assignment->id);
		return $assignment->id;
	}

    /**
     * Create a new user and assign basic permissions.
     *
     * @param $name
     * @param $username
     * @param $email
     * @return User
     */
    public function create_new_user($name, $username, $email = null)
    {
        $user = new User([
            'name' => $name,
            'username' => $username,
            'email' => $email
        ]);
        $user->password = bcrypt($user->generate_password_for_user());
        $user->save();

        // Set low level user permissions
        $roleUser = Role::whereSlug('user')->first();
        $user->attachRole($roleUser);

        return $user;
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
     * Send a completion email to the user for the specified assignment.
     *
     * @param $id
     */
    private function send_completion_notification_to_user($id)
    {
        $user = \Auth::user();

        if ($user->email && !filter_var($user->email, FILTER_VALIDATE_EMAIL) === false)
        {
            $mailer = new Mailer();
            $mailer->send_completed($user, $id);
        }
    }

    public function add_user_to_assignment(Request $request)
    {
        $data = $request->all();

        $user = User::find($data['id']);

        $assessments = [];
        foreach ($data['assessments'] as $assessmentId)
            array_push($assessments, Assessment::find($assessmentId));

        return \Response::json([
            'user' => $user,
            'assessments' => $assessments
        ]);
    }

	/**
     * Add users to an assignment from a specific client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_users_to_assignment_from_client(Request $request)
    {
        $data = $request->all();
        $client = Client::findOrFail($data['client']);

        $users = $client->users;

        return \Response::json(['users' => $users]);
    }

	/**
	 * Add users to an assignment from a specific group.
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @internal param Request $request
	 */
	public function addFromGroups($id)
	{
		$client = Client::findOrFail($id);
		$groups = $client->groups;
		$users = [];

		foreach ($groups as $group)
		{
			foreach ($group->users as $userArray)
			{
				$user = User::find($userArray['id']);
				$user->position = $userArray['position'];
				$user->leader = $userArray['leader'];

				if ($group->target_id)
					$user->target = User::find($group->target_id);

				array_push($users, $user);
			}
		}

		return \Response::json(['users' => $users]);
	}

	/**
	 * Add users to an assignment from a specific job family.
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @internal param Request $request
	 */
	public function addFromJobFamily($id, Request $request)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$users = [];

		foreach ($client->users as $user)
		{
			if ($user->job_family && $user->job_family == $data['family'])
				$users[] = [
					'id' => $user->id,
					'name' => $user->name,
					'username' => $user->username,
					'email' => $user->email,
				];
		}

		return \Response::json(['users' => $users]);
	}

	/**
	 * Add users to an assignment from a specific job.
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 * @internal param Request $request
	 */
	public function addFromJob($id, Request $request)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$job = Job::findOrFail($data['job']);
		$users = [];

		foreach ($job->applicants() as $user)
		{
			$users[] = [
				'id' => $user->id,
				'name' => $user->name,
				'username' => $user->username,
				'email' => $user->email,
			];
		}

		return \Response::json(['users' => $users]);
	}

    /**
     * Add users to an assignment from a specific client.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_users_to_assignment_from_job(Request $request)
    {
        $data = $request->all();
        $job = Job::findOrFail($data['job']);

        $users = $job->viableApplicants();

        return \Response::json(['users' => $users]);
    }

    /**
     * Download all assignment data for a specific client.
     *
     * @param $client_id
     * @return bool
     */
    public function download_all_assignments_for_client($client_id, $type)
    {
		ini_set('max_execution_time', 300);
        $client = Client::findOrFail($client_id);
        $users = $client->users;
        $assessments = [];

        // Get user assessments and assignments
        foreach ($users as $user)
        {
            $userAssessments = [];
            $userAssignments = [];

            foreach ($user->assignments as $assignment)
            {
                // Get the assessments that we'll be reporting on
                if (! in_array($assignment->assessment(), $assessments))
                    $assessments[] = $assignment->assessment();

                // Get list of assignment ids for this user
				$userAssessments[] = $assignment->assessment_id;
				$userAssignments[$assignment->assessment_id] = $assignment->id;
            }

            $user->assignmentIds = $userAssignments;
            $user->assignedAssessments = $userAssessments;
        }

        // Track progress
        sse_init();
        $total = $users->count() + 4;

        // Generate excel file
		if ($type == 1)
        	$data = $this->excelTemplateAssignmentAnswers($client, $users, $assessments, $total);
		if ($type == 2)
			$data = $this->excelTemplateDetailedDimensionScores($client, $users, $assessments, $total);

        // Return a csv
        $return_data = $data->store('csv', false, true);
        sse_complete($return_data);
        return true;
        //return view('excel.assignments.index', compact('users', 'assessments'));
    }

	public function excelTemplateAssignmentAnswers($client, $users, $assessments, $total)
	{
		$filename = 'Assessment Data for '.sanitize_string($client->name).' '.Carbon::now();

		return Excel::create($filename, function($excel) use ($client, $users, $assessments, $total)
		{
			$excel->setTitle('Assessment Details for '.$client->name);
			$excel->sheet('Details', function($sheet) use ($users, $assessments, $total)
			{
				// Start
				$i = 1;
				$row = [];
				sse_send($i, ($i / $total) * 100);

				// Row 1, Assessment Name
				$row = ['Identification', '', '', '', ''];
				foreach ($assessments as $assessment)
				{
					$row[] = $assessment->name;
					foreach ($assessment->filteredQuestions() as $question)
						$row[] = '';
				}
				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// Row 2, Dimension Code of each question
				$row = ['UserID', 'Name', 'Email', 'Job Title', 'Job Family'];
				foreach ($assessments as $assessment)
				{
					foreach ($assessment->filteredQuestions() as $question)
						array_push($row, $question->dimension_code());
				}
				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// Row 3, Question text
				$row = ['', '', '', '', ''];
				foreach ($assessments as $assessment)
				{
					foreach ($assessment->filteredQuestions() as $question)
						array_push($row, $question->number . '. ' . $question->getContentForExcel());
				}
				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// Row 4+, User Rows
				foreach ($users as $user)
				{
					// User Info and Scores
					$row = [$user->username, $user->name, $user->email, $user->job_title, $user->job_family];
					foreach ($assessments as $assessment)
					{
						// If user didn't take this assessment
						$key = array_search($assessment->id, $user->assignedAssessments);
						if ($key === false)
						{
							// Fill with blanks
							foreach ($assessment->filteredQuestions() as $question)
								$row[] = '';
							continue;
						}

						// If user did take the assessment
						else
						{
							// Show the answers for that assignment
							foreach ($assessment->filteredQuestions() as $question)
							{
								// If user answered this question, print score
								if ($question->answer_exists($user->assignmentIds[$assessment->id], $user->id))
									$row[] = $question->answerFromAssignment($user->assignmentIds[$assessment->id])->questionScore();

								// Otherwise, print a blank
								else
									$row[] = '';
							}
						}

						// For each each assignment of this specific assessment
//						$assignments = $assessment->getAssignmentsForUser($user->id);
//						foreach ($assignments as $s => $assignment)
//						{
//							// Reset headers
//							if ($s > 0)
//								$row = ['', '', '', '', ''];
//
//							// If the assessment has a target
//							if ($assessment->target)
//							{
//								// Store target information if it's present only in custom fields
//								// Backwards Compatibility for data that didn't have the target_id column
//								if (!$assignment->target && $assignment->custom_fields && array_key_exists('type', $assignment->custom_fields))
//								{
//									$target = null;
//									foreach ($assignment->custom_fields['type'] as $index => $value)
//									{
//										$target = User::where($value, $assignment->custom_fields['value'][$index])->first();
//
//										if ($target)
//											break;
//									}
//									$assignment->target = $target;
//								}
//
//								// Then show target name
//								if ($assignment->target)
//									array_push($row, $assignment->target->name);
//								else
//									array_push($row, 'Target Not Found');
//							}
//
//							// Show the answers for that assignment
//							foreach ($assessment->filteredQuestions() as $question)
//							{
//								// If user answered this question, print score
//								if ($question->answer_exists($assignment->id, $user->id))
//									array_push($row, $question->answerFromAssignment($assignment->id)->questionScore());
//
//								// Otherwise, print a blank
//								else
//									array_push($row, '');
//							}
//
//							$sheet->row($i, $row);
//							$i++;
//							//sse_send($i, ($i / $total) * 100);
//							echo 'Added row '.($i - 1).', user '.$user->name.' <br/>';
//							echo $i . ': ';
//							echo number_format(($i / $total) * 100, 2) . '% <br/><br/>';
//
//							if ($i >= 20)
//								return true;
//						}



						/*// Target
						if ($assessment->target)
						{
							$assignment = $assessment->getAssignmentsForUser($user->id)->first();
							$assignments = $assessment->getAssignmentsForUser($user->id);
							// HERE
							sse_complete('<pre>Output Data:</pre><pre>'.json_encode($assignments).'</pre>');

							// Store target information if it's present only in custom fields (target_id used to not exist)
							if (!$assignment->target && $assignment->custom_fields && array_key_exists('type', $assignment->custom_fields))
							{
								$target = null;
								foreach ($assignment->custom_fields['type'] as $index => $value)
								{
									$target = User::where($value, $assignment->custom_fields['value'][$index])->first();

									if ($target)
										break;
								}
								$assignment->target = $target;
							}

							// Show target name
							if ($assignment->target)
								array_push($row, $assignment->target->name);
							else
								array_push($row, 'Target Not Found');
						}

						foreach ($assessment->filteredQuestions() as $question)
						{
							// If user answered this question, print score
							if ($question->answer_exists($user->assigned_assessments['assignment_ids'][$key], $user->id))
								array_push($row, $question->answerFromAssignment($user->assigned_assessments['assignment_ids'][$key])->questionScore());

							// Otherwise, print a blank
							else
								array_push($row, '');
						}*/
					}

					$sheet->row($i, $row);
					$i++;
					sse_send($i, ($i / $total) * 100);
				}
			});
		});
    }

	public function excelTemplateDetailedDimensionScores($client, $users, $assessments, $total)
	{
		$filename = 'Assessment Data for '.sanitize_string($client->name).' '.Carbon::now();

		return Excel::create($filename, function($excel) use ($client, $users, $assessments, $total)
		{
			$excel->setTitle('Assessment Details for '.$client->name);
			$excel->sheet('Details', function($sheet) use ($users, $assessments, $total)
			{
				// Start
				$i = 1;
				$row = [];
				sse_send($i, ($i / $total) * 100);

				// Row 1, Assessment Name
				$row = ['Identification', '', '', '', ''];
				foreach ($assessments as $assessment)
				{
					$row[] = $assessment->name;

					if ($assessment->id == get_global('personality'))
						foreach ($assessment->dimensions as $j => $dimension)
						{
							if ($j == 0) continue;
							$row[] = '';
						}
				}
				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// Row 2, Dimension Code of each question
				$row = ['UserID', 'Name', 'Email', 'Job Title', 'Job Family'];
				foreach ($assessments as $assessment)
				{
					if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
						$row[] = 'Raw Score';

					if ($assessment->id == get_global('personality'))
					{
						foreach ($assessment->dimensions as $dimension)
							$row[] = $dimension->name;
					}
				}
				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// Row 4+, User Rows
				$scoringController = new ScoringController();
				foreach ($users as $user)
				{
					// User Info and Scores
					$row = [$user->username, $user->name, $user->email, $user->job_title, $user->job_family];
					foreach ($assessments as $assessment)
					{
						// If user didn't take this assessment
						$key = array_search($assessment->id, $user->assignedAssessments);
						if ($key === false)
						{
							// Fill with blanks
							if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
								$row[] = '';

							if ($assessment->id == get_global('personality'))
							{
								foreach ($assessment->dimensions as $dimension)
									$row[] = '';
							}
						}

						// If user did take the assessment
						else
						{
							if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude'))
							{
								$assignment = Assignment::find($user->assignmentIds[$assessment->id]);
								$row[] = $scoringController->getTotalScore($assignment);
							}

							if ($assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
								$row[] = $scoringController->scoreWm($user->assignmentIds[$assessment->id]);

							if ($assessment->id == get_global('personality'))
							{
								foreach ($assessment->dimensions as $dimension)
									$row[] = $scoringController->getScoreForDimension($user->assignmentIds[$assessment->id], $dimension->id);
							}
						}
					}

					$sheet->row($i, $row);
					$i++;
					sse_send($i, ($i / $total) * 100);
				}
			});
		});
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

        $validator = Validator::make($data, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        if ($validator->fails())
            return \Response::json(['errors' => 'File must be a valid .xls or a .xlsx file format.']);

        Excel::load($data['file'], function($reader) use (&$users) {
            $results = $reader->all();

            $reader->each(function($sheet) use (&$users) {

                $sheet->each(function($row) use (&$users) {
                    $name = $row->name;
                    $email = $row->email;
                    $manager_name = $row->manager_name;
                    $manager_email = $row->manager_email;

                    if (! $row->email && $row->e_mail)
                        $email = $row->e_mail;

                    if (! $row->manager_name && $row->mngr_name)
                        $manager_name = $row->mngr_name;

                    if (! $row->manager_email && $row->mngr_email)
                        $manager_email = $row->mngr_email;

                    array_push($users, ['email' => $email, 'name' => $name, 'manager_name' => $manager_name, 'manager_email' => $manager_email]);
                });
            });
        });

        return \Response::json(['users' => $users]);
    }
}
