<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Assignment;
use App\Client;
use App\Job;
use App\Post;
use App\User;
use Bican\Roles\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Stripe\Product;
use Validator;

class ClientDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	/**
     * Selection
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selection()
    {
        $user = Auth::user();
		$client = $user->client;
        $jobs = $user->client->jobs;

        return view('clientdashboard.selection', compact('user', 'jobs', 'client'));
    }

	/**
     * Development
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function development()
    {
       // $user = Auth::user();
        //$users = $user->client->users;

        $surveys = array(
            array('id'=>1,
                'name'=>'AOE 1',
                'date'=>'2016-08-24',
                'leader'=>'1'),
            array('id'=>2,
                'name'=>'Parul',
                'date'=>'2016-08-25',
                'leader'=>'2'),
            array('id'=>3,
                'name'=>'Paras',
                'date'=>'2016-08-26',
                'leader'=>'3'),
            array('id'=>4,
                'name'=>'Alex',
                'date'=>'2016-08-27',
                'leader'=>'4')
        );

        return view('clientdashboard.development', compact('surveys'));
    }

    public function specificDev($id)
    {
        $user = User::find($id);
        

        return view('clientdashboard.specificDev', compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function job($id, Request $request)
    {
        $client = \Auth::user()->client;
        $job = Job::findOrFail($id);
        $s = new ScoringController();
		$r = new ReportsController();
        $users = $job->viableApplicants();

        // Split users' names into first name and last name
        foreach ($users as $user)
        {
            $name = explode(' ', $user->name);
            $user->firstName = $name[0];
            if (count($name) == 3)
            {
                $user->firstName = $name[0].' '.$name[1];
                $user->lastName = $name[2];
            }
            else if (count($name) == 2)
                $user->lastName = $name[1];
        }

        // Sort the query
        if ($request->sort) {

            // Sort by score
            if ($request->sort == 1 or $request->sort == 2 or $request->sort == 3) {

                // Find and calculate the score first
                $assessment = Assessment::find($job->assessments[$request->sort - 1]);
                
                // Do that for each user
                foreach ($users as $user)
                {
                    $assignment = $assessment->getAssignmentsForUser($user->id)->first();

                    // Assignment completed
                    if ($assignment and $assignment->completed())
                    {
                        $user->score = $s->score($assignment->id, $job->id);

                        // Get percentile for Aptitude assessments
                        if ($assignment->assessment()->id == get_global('aptitude') || $assignment->assessment()->id == get_global('evonik-assessment')) {
                            $user->percentile = $r->getAptitudePercentile($user->score);
                        }

                        // Get percentiles for any other types of assessments
                        else {
                            $user->percentile = $r->getAbilityPercentile($user->score);
                        }
                    }

                    // Assignment is not completed
                    else {
                        $user->score = 0;
                        $user->percentile = 0;
                    }
                }

                // Sort by it
                $users = $users->sortByDesc('score');
            }

            // Sort by regular query
            else {
                $users = $users->sortBy($request->sort);
            }
        }

        // Default sort by name
        else {
            $users = $users->sortBy('name');
        }

        // Paginate the results
        $page = $request->page;
        if (! $page) $page = 1;
        $perPage = 15;
        $paginator = new LengthAwarePaginator($users->forPage($page, $perPage), $users->count(), $perPage, $page);
        $users = collect($paginator->items());

        // Gather our assessments
        $assessmentsArray = [];
        foreach ($job->assessments as $assessmentId) {
            array_push($assessmentsArray, Assessment::find($assessmentId));
        }
        $job->assessments = collect($assessmentsArray);

        // Gather score data for each user
        foreach ($users as $user)
        {
            // For each assignment
            $assignments = [];
            foreach ($job->assessments as $assessment)
            {
                // Find the assessment and assignment for that specific user
                $assignment = $assessment->getAssignmentsForUser($user->id)->first();

                // Get scores for the assignment, only if it's completed
                if ($assignment and $assignment->completed())
                {
                    $assignment->score = $s->score($assignment->id, $job->id);

                    $assignment->division = $s->getScoreDivision($assignment->id, $job->id, $assignment->score);

                    // Get percentile for Aptitude assessments
                    if ($assignment->assessment()->id == get_global('aptitude') || $assignment->assessment()->id == get_global('evonik-assessment')) {
                        $assignment->percentile = $r->getAptitudePercentile($assignment->score);
                    }

                    // Get percentiles for any other types of assessments
                    else {
                        $assignment->percentile = $r->getAbilityPercentile($assignment->score);
                    }
                }

                // Push into array
                array_push($assignments, $assignment);
            }
            $user->assignments = collect($assignments);
        }

        // Set the base url for re-sorting columns
        $baseUrl = 'dashboard/jobs/'.$job->id.'?page='.$paginator->currentPage();

		// Users array to search through
		$usersArray = [];
		foreach ($job->applicants() as $user) {
			$usersArray[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';
        }

        return view('clientdashboard.job', compact('job', 'users', 'client', 'paginator', 'baseUrl', 'usersArray'));
    }

    /**
     * Show the form for assigning assessments to applicants.
     *
     * @param $id
     * @return View
     */
    public function assign($id)
    {
		$client = \Auth::user()->client;
        $job = Job::findOrFail($id);
        $assessments = Assessment::all()->filter(function($assessment) use ($job) {
            return in_array($assessment->id, $job->assessments);
        });
		$assessmentsArray = [];
		if ($assessments)
			$assessmentsArray = get_select_formatted_array($assessments);
		$emailBody = get_default_email_body();
        $usersArray = [];
        foreach ($job->viableApplicants() as $user)
            $usersArray[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';

        return view('clientdashboard.assign', compact('client', 'assessmentsArray', 'job', 'custom_fields', 'usersArray', 'emailBody', 'job'));
    }

	/**
	 * Show all users.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function users()
    {
        $client = Client::findOrFail(Auth::user()->client_id);
//        $users = $client->users->filter(function($user) {
//        	// Exclude the client admin user
//        	return $user->role()->level == 1;
//		});
		$users = $client->jobUsers();
        $roles = Role::all();

        return view('clientdashboard.users', compact('users', 'roles', 'client'));
    }

	/**
	 * Show a single user.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function user($id)
    {
        $client = Client::findOrFail(Auth::user()->client_id);
        $user = User::findOrFail($id);
		$jobs = $user->jobs();
		$s = new ScoringController();

		// Gather this user's assignments
		$jobsArray = [];
		foreach ($jobs as $job)
		{
			$assignmentsArray = [];
			foreach ($job->assessments as $assessmentId)
			{
				// Find the assessment and assignment for that specific user
				$assignment = Assignment::where([
					'assessment_id' => $assessmentId,
					'user_id' => $user->id,
				])->first();

				// Get scores for the assignment, only if it's completed
				if ($assignment and $assignment->completed())
				{
					$assignment->score = $s->score($assignment->id, $job->id);
					if (Auth::user('reseller|client') && session('reseller') && session('reseller')->id == get_global('risk66') && !$job->models->isEmpty())
					{
						$r = new ReportsController();
						$assignment->division = 6 - $r->getModelDivision($client->id, $job->id, $user->id, $job->models()->first()->id);
					}
					else
						$assignment->division = $s->getScoreDivision($assignment->id, $job->id, $assignment->score);
					$assignment->percentile = $s->getPercentile($assignment->id, $assignment->score);
				}

				// Push into array
				array_push($assignmentsArray, $assignment);
			}
			$job->assignments = collect($assignmentsArray);
			array_push($jobsArray, $job);
		}

		$jobs = collect($jobsArray);

		$assignments = $user->assignments;

        return view('clientdashboard.user', compact('user', 'jobs', 'client', 'assignments'));
    }

	/**
	 * Show the form for editing the specified user.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function editUser($id)
	{
		$client = Client::findOrFail(Auth::user()->client_id);
		$user = User::findOrFail($id);

		return view('clientdashboard.editUser', compact('client', 'user'));
	}

	/**
	 * Update the specified user in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateUser(Request $request, $id)
	{
		$client = Client::findOrFail(Auth::user()->client_id);
		$user = User::findOrFail($id);
		$data = $request->all();

		if (! array_key_exists('password', $data))
			$data['password'] = false;

		$validator = Validator::make($data, [
			'name' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		if ($data['password'])
			$data['password'] = bcrypt($data['password']);
		else
			unset($data['password']);

		$user->update($data);

		return redirect()->back()->with('success', 'User updated successfully!');
	}

	/**
	 * Show the form for adding applicants to a specific job.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function addApplicants()
    {
        $client = Client::findOrFail(Auth::user()->client_id);
        $jobs = $client->jobs;

        $jobsArray = [];
        if (! $jobs->isEmpty())
        {
            foreach ($jobs as $job)
                $jobsArray[$job->id] = 'Add to ' . $job->name;
        }

        return view('clientdashboard.addapplicants', compact('client', 'jobsArray'));
    }

	/**
	 * Store applicants for a specific job.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function storeApplicants(Request $request)
    {
        $client = Client::findOrFail(Auth::user()->client_id);
        $data = $request->all();
        $count = 0;
        $errors = [];
        $users = [];

        // For each user field
        foreach($data['username'] as $i => $username) {

            $users[$i] = false;
            $name = $data['name'][$i];
            $email = $data['email'][$i];
            $job = $data['job_id'][$i];

            // Generate new user
            $user = new User([
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(\Auth::user()->generate_password($name, $username)),
                'client_id' => $client->id
            ]);

            // Attempt to save
            try {
                $user->save();
                $role = Role::find(4);
                $user->attachRole($role);
                $count += 1;

                // Add as applicant of job, if set
                if ($job)
                {
                    DB::table('job_users')->insert([
                        'user_id' => $user->id,
                        'job_id' => $job,
                        'viable' => true,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            // If can't save, must be a duplicate entry
            catch (\Exception $e) {
                $error = '';

                if (strpos($e, 'Duplicate entry'))
                    $error = 'Username '.$username.' is already in use.';

                array_push($errors, 'User '.$name.' could not be added. '.$error);
            }

            $users[$i] = $user;
        }

        $usersController = new UsersController();
        $file = $usersController->download_generated_users($users);
        $download_link = '/download/'.$file['file'];

        return \Response::json(['count' => $count, 'errors' => $errors, 'users' => $users, 'download_link' => $download_link]);
    }

	/**
	 * Export an excel list of all applicants for a specific job.
	 *
	 * @param $jobId
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
	 */
	public function exportApplicants($jobId)
	{
		$client = Client::findOrFail(Auth::user()->client_id);
		$job = Job::findOrFail($jobId);
		$users = $job->applicants();

		$usersController = new UsersController();
		$file = $usersController->download_generated_users($users);
		$download_link = '/download/'.$file['file'];

		return redirect($download_link);
    }

	/**
	 * Download all data for a specific job.
	 *
	 * @param $id
	 * @param Request $request
	 * @return bool
	 */
	public function download_job_data($id, Request $request)
	{
		ini_set('max_execution_time', 2080);

		$data = $request->all();
		$includeRejected = (array_key_exists('includeRejected', $data) && $data['includeRejected']) ? 1 : 0;
		$percentileAsScore = (array_key_exists('percentileAsScore', $data) && $data['percentileAsScore']) ? 1 : 0;
		$fitAsScore = (array_key_exists('fitAsScore', $data) && $data['fitAsScore']) ? 1 : 0;

		$client = Client::findOrFail(Auth::user()->client_id);
		$job = Job::findOrFail($id);
		$s = new ScoringController();
		$r = new ReportsController();

		// Split name into first name and last name
		$users = $includeRejected ? $job->applicants() : $job->viableApplicants();
		foreach ($users as $user)
		{
			$name = explode(' ', $user->name);
			$user->firstName = $name[0];
			if (count($name) == 3)
			{
				$user->firstName = $name[0].' '.$name[1];
				$user->lastName = $name[2];
			}
			else if (count($name) == 2)
				$user->lastName = $name[1];
		}
		$users = $users->sortBy('name');

		// Gather our assessments
		$assessmentsArray = [];
		foreach ($job->assessments as $assessmentId)
			array_push($assessmentsArray, Assessment::find($assessmentId));
		$job->assessments = collect($assessmentsArray);

		// Setup the excel doc
		$filename = 'Selection Overview for '.$job->name.', '.$client->name.', '.Carbon::now();
		$filename = str_replace(":", "_", $filename);

		// Track progress
		sse_init();
		$total = $users->count() + 4;

		// Generate excel file
		$data = Excel::create($filename, function($excel) use ($client, $users, $job, $total, $s, $r, $percentileAsScore, $fitAsScore)
		{
			$excel->setTitle('Applicant Details for '.$job->name);
			$excel->sheet('Details', function($sheet) use ($users, $total, $job, $s, $r, $percentileAsScore, $fitAsScore)
			{
				$i = 1;
				sse_send($i, ($i / $total) * 100);

				// Row 1, Header Titles
				$row = ['First Name', 'Last Name'];
				foreach ($job->assessments as $assessment)
					array_push($row, $assessment->name);

				$sheet->row($i, $row);
				$i++;
				sse_send($i, ($i / $total) * 100);

				// User Rows
				foreach ($users as $user)
				{
					$row = [$user->firstName, $user->lastName];

					// Each assessment
					foreach ($job->assessments as $assessment)
					{
						// Find the assessment and assignment for that specific user
						$assignment = $assessment->getAssignmentsForUser($user->id)->first();

						// Get scores for the assignment, only if it's completed
						if ($assignment and $assignment->completed())
						{
							$assignment->score = $s->score($assignment->id, $job->id);
							$assignment->division = $s->getScoreDivision($assignment->id, $job->id, $assignment->score);
							if ($assignment->assessment()->id == get_global('aptitude'))
								$assignment->percentile = $r->getAptitudePercentile($assignment->score);
							else if ($assignment->assessment()->id == get_global('ability'))
								$assignment->percentile = $r->getAbilityPercentile($assignment->score);
						}

						// Now print the scores
						if ($assignment and $assignment->completed)
						{
							if ($assignment->assessment()->id == get_global('personality'))
							{
								if ($fitAsScore) {
									array_push($row, number_format($assignment->score, 2));
								}
								else
								{
									switch ($assignment->division)
									{
										case 1:
											array_push($row, 'High Fit');
											break;
										case 2:
											array_push($row, 'Moderate-To-High Fit');
											break;
										case 3:
											array_push($row, 'Moderate Fit');
											break;
										case 4:
											array_push($row, 'Moderate-To-Low Fit');
											break;
										case 5:
											array_push($row, 'Low Fit');
											break;
									}
								}
							}
							else if ($assignment->assessment()->id == get_global('safety'))
							{
								array_push($row, number_format($assignment->score, 2));
							}
							else {
								if ($percentileAsScore)
									array_push($row, number_format($assignment->score, 2));
								else
									array_push($row, $assignment->percentile . '%');
							}
						}
						elseif ($assignment)
							array_push($row, 'In Progress');

						else
							array_push($row, 'Not Assigned');
					}
					$sheet->row($i, $row);
					$i++;
					sse_send($i, ($i / $total) * 100);
				}
			});
		});

		$return_data = $data->store('csv', false, true);

		sse_complete($return_data);

		return true;
	}

	/**
	 * Update account.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updateAccount(Request $request)
	{
		$user = Auth::user();

		if (! $user->is('client'))
			return redirect()->back()->with('errors', 'You do not have permission to modify this account.');

		$data = $request->all();

		if (! array_key_exists('password', $data))
			$data['password'] = false;

		$validator = Validator::make($data, [
			'name' => 'required',
			'password' => 'required'
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		if ($data['password'])
			$data['password'] = bcrypt($data['password']);
		else
			unset($data['password']);

		$user->update($data);

		return redirect()->back()->with('success', 'Your account has been updated successfully!');
	}

	public function assignments()
	{
		$client = Auth::user()->client;
		$users = $client->users;
		$emailBody = get_default_email_body();

		$userIds = [];
		foreach ($users as $user)
			array_push($userIds, $user->id);

		$allowedAssessments = [];
		foreach ($client->jobs as $job)
			foreach ($job->assessments as $assessmentId)
				$allowedAssessments[] = $assessmentId;

		$assignments = Assignment::all()->filter(function($assignment) use ($userIds, $allowedAssessments) {
			if (! in_array($assignment->assessment_id, $allowedAssessments))
				return false;
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

		return view('clientdashboard.assignments.dates', compact('client', 'dates', 'emailBody'));
	}

	public function assignmentsForDate($date)
	{
		$client = Auth::user()->client;
		$users = $client->users;

		$userIds = [];
		foreach ($users as $user)
			array_push($userIds, $user->id);

		$assignments = Assignment::all()->filter(function($assignment) use ($userIds, $date) {
			return (in_array($assignment->user_id, $userIds) && $assignment->created_at->format('Y-m-d H:i') == $date);
		});

		return view('clientdashboard.assignments.assignments', compact('client', 'assignments', 'report', 'date'));
	}

	public function editAssignment($id)
	{
		$client = Auth::user()->client;
		$assignment = Assignment::findOrFail($id);
		$user = $assignment->user;
		$assessment = $assignment->assessment();
		$assignment->expiration = date('D, d M Y', strtotime($assignment->expires));

		if ($user->client != $client)
			abort('403', 'Error: This assignment doesn\'t exist.');

		return view('clientdashboard.assignments.edit', compact('assignment', 'assessment', 'user', 'client'));
	}

	/**
	 * Show the view for bulk editing assignments.
	 *
	 * @return View
	 */
	public function bulk($id = null)
	{
		$client = Auth::user()->client;
		$users = $client->users;

		if ($id !== null)
		{
			$job = Job::findOrFail($id);
			$users = $job->applicants();
		}

		$allowedAssessments = [];
		foreach ($client->jobs as $job)
			foreach ($job->assessments as $assessmentId)
				$allowedAssessments[] = $assessmentId;

		$all_assignments = [];
		foreach ($users as $user)
			foreach (Assignment::where('user_id', $user->id)->get() as $assignment)
			{
				if (! in_array($assignment->assessment_id, $allowedAssessments))
					continue;
				$all_assignments[] = $assignment;
			}

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

		return view('clientdashboard.assignments.bulk', compact('assignments', 'client'));
	}
}
