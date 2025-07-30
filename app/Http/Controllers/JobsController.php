<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Client;
use App\Job;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $client = Client::findOrFail($id);
        $jobs = $client->jobs;

        $jobTemplates = null;
        if (Auth::user()->is('reseller') && session('reseller'))
			$jobTemplates = session('reseller')->jobTemplates();

        return view('dashboard.jobs.index', compact('client', 'jobs', 'jobTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $client = Client::findOrFail($id);
        $jobs = $client->jobs;

        $assessments = Assessment::all();
        $assessmentsArray = [];
        foreach ($assessments as $assessment)
            $assessmentsArray[$assessment->id] = $assessment->name;

        return view('dashboard.jobs.create', compact('client', 'jobs', 'assessmentsArray'));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function createFromTemplate($id, $jobTemplateId)
	{
		$client = Client::findOrFail($id);
		$jobTemplate = null;
		if (Auth::user()->is('reseller') && session('reseller'))
			$jobTemplate = session('reseller')->jobTemplate($jobTemplateId);

		if (! $jobTemplate)
			return abort('403', 'You cannot create a job using this template.');

		$assessments = session('reseller')->assessments;
		$assessmentsArray = [];
		foreach ($assessments as $assessmentId)
		{
			$assessment = Assessment::find($assessmentId);
			if (! $assessment)
				continue;

			$assessmentsArray[$assessment->id] = $assessment->name;
		}

		return view('dashboard.jobs.create', compact('client', 'jobTemplate', 'assessmentsArray'));
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        $client = Client::findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'slug' => 'required|unique:jobs,slug',
            'assessments' => 'required',
        ],
		[
			'slug.required' => 'The Job ID field is required.',
			'slug.unique' => 'A job with the specified ID already exists. The Job ID field must be unique.'
		]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $job = new Job($data);
        $client->jobs()->save($job);

        return redirect('dashboard/clients/'.$client->id.'/jobs')->with('success', 'Job '.$job->name.' created successfully!');
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeFromTemplate($id, $jobTemplateId, Request $request)
	{
		$client = Client::findOrFail($id);
		$jobTemplate = null;
		if (Auth::user()->is('reseller') && session('reseller'))
			$jobTemplate = session('reseller')->jobTemplate($jobTemplateId);

		if (! $jobTemplate)
			return abort('403', 'You cannot store a new job using this template.');

		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'slug' => 'required|unique:jobs,slug',
			'assessments' => 'required',
		],
		[
			'slug.required' => 'The Job ID field is required.',
			'slug.unique' => 'A job with the specified ID already exists. The Job ID field must be unique.'
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		$job = new Job($data);
		$job->job_template_id = $jobTemplate->id;
		$client->jobs()->save($job);

		return redirect('dashboard/clients/'.$client->id.'/jobs')->with('success', 'Job '.$job->name.' created successfully!');
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $client = \Auth::user()->client;
        $users = $client->users->sortBy('name');

        // Paginate the results
        $paginator = new LengthAwarePaginator($users, $users->count(), 10);
        $users = collect(array_slice($paginator->items(), 0, $paginator->perPage()));

        // If different page, offset the results
        if ($request->page && $request->page > 1 && $request->page <= $paginator->lastPage())
        {
            $offset = ($paginator->currentPage() - 1) * $paginator->perPage();
            $users = collect(array_slice($paginator->items(), $offset, $paginator->perPage()));
        }

        return view('dashboard.jobs.show', compact('users', 'client', 'paginator'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function applicants($id, $jobId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $users = $job->applicants();

        return view('dashboard.jobs.applicants', compact('client', 'job', 'users'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function addApplicants($id, $jobId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $users = $client->users;

        $usersArray = [];
        foreach ($users as $user)
        {
            $jobUser = DB::table('job_users')->where(['job_id' => $job->id, 'user_id' => $user->id])->first();
            if ($jobUser)
                continue;

            $usersArray[$user->id] = $user->name . ' (' . $user->username . ', ' . $user->email . ')';
        }

        return view('dashboard.jobs.add', compact('client', 'job', 'usersArray'));
    }

	/**
     * Store existing users as applicants for a specific job.
     *
     * @param $id
     * @param $jobId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeApplicants($id, $jobId, Request $request)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $data = $request->all();

        foreach ($data['user_id'] as $userId)
        {
            $user = User::find($userId);

            DB::table('job_users')->insert([
                'user_id' => $user->id,
                'job_id' => $job->id,
                'viable' => true,
                'created_at' => Carbon::now()
            ]);
        }

        return redirect()->back()->with('success', 'Users added as applicants successfully!');
    }

    /**
     * Reject an applicant, flagging him as non-viable for a specific job.
     *
     * @param $id
     * @param $jobId
     * @param $userId
     * @return \Illuminate\Http\RedirectResponse
     * @internal param $applicantId
     */
    public function rejectApplicant($id, $jobId, $userId)
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($userId);

        $applicant = DB::table('job_users')->where([
            'job_id' => $jobId,
            'user_id' => $userId
        ])->update([
            'viable' => false
        ]);

        return redirect()->back()->with('success', 'Applicant '.$user->name.' rejected successfully!');
    }

    /**
     * Un-reject an applicant, flagging him as viable for a specific job again.
     *
     * @param $id
     * @param $jobId
     * @param $userId
     * @return \Illuminate\Http\RedirectResponse
     * @internal param $applicantId
     */
    public function unrejectApplicant($id, $jobId, $userId)
    {
        $client = Client::findOrFail($id);
        $user = User::findOrFail($userId);

        $applicant = DB::table('job_users')->where([
            'job_id' => $jobId,
            'user_id' => $userId
        ])->update([
            'viable' => true
        ]);

        return redirect()->back()->with('success', 'Applicant '.$user->name.' un-rejected successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $jobId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);

        if (Auth::user()->is('reseller') && session('reseller'))
		{
			$assessments = session('reseller')->assessments;
			$assessmentsArray = [];
			foreach ($assessments as $assessmentId)
			{
				$assessment = Assessment::find($assessmentId);
				if (! $assessment)
					continue;

				$assessmentsArray[$assessment->id] = $assessment->name;
			}
		}
		else
		{
			$assessments = Assessment::all();
			$assessmentsArray = [];
			foreach ($assessments as $assessment)
				$assessmentsArray[$assessment->id] = $assessment->name;
		}

        return view('dashboard.jobs.edit', compact('client', 'job', 'assessmentsArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @param $jobId
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, $jobId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'slug' => 'required|unique:jobs,slug,'.$job->id,
            'assessments' => 'required',
        ],
		[
			'slug.required' => 'The Job ID field is required.',
			'slug.unique' => 'A job with the specified ID already exists. The Job ID field must be unique.'
		]);

        if ($validator->fails())
            return redirect()->back()->withInput()->withErrors($validator->errors());

        $job->update($data);

        return redirect('dashboard/clients/'.$client->id.'/jobs')->with('success', 'Job '.$job->name.' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $jobId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);

        $job->delete();

        return redirect('dashboard/clients/'.$client->id.'/jobs')->with('success', 'Job '.$job->name.' deleted successfully!');
    }

	/**
	 * Download all data for a specific job.
	 *
	 * @param $id
	 * @param Request $request
	 * @return bool
	 */
	public function download_job_data($id, $jobId, Request $request)
	{
		ini_set('max_execution_time', 2080);

		$data = $request->all();
		$includeRejected = (array_key_exists('includeRejected', $data) && $data['includeRejected']) ? 1 : 0;
		$percentileAsScore = (array_key_exists('percentileAsScore', $data) && $data['percentileAsScore']) ? 1 : 0;
		$fitAsScore = (array_key_exists('fitAsScore', $data) && $data['fitAsScore']) ? 1 : 0;

		$client = Client::findOrFail($id);
		$job = Job::findOrFail($jobId);
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
                            // Score assessments
							$assignment->score = $s->score($assignment->id, $job->id);

                            // Get the division
							$assignment->division = $s->getScoreDivision($assignment->id, $job->id, $assignment->score);

                            // Get percentile for Aptitude assessments
							if ($assignment->assessment()->id == get_global('aptitude') || $assignment->assessment()->id == get_global('evonik-assessment')) {
								$assignment->percentile = $r->getAptitudePercentile($assignment->score);
                            }

                            // Get percentiles for Ability assessments
							else if ($assignment->assessment()->id == get_global('ability') || $assignment->assessment()->id == get_global('reasoning-b')) {
								$assignment->percentile = $r->getAbilityPercentile($assignment->score);
                            }

                            // Get percentiles for any other types of assessments
                            else {
                                $assignment->percentile = $r->getAbilityPercentile($assignment->score);
                            }
						}

						// Now print the scores
						if ($assignment and $assignment->completed)
						{
                            // Personality scores
							if ($assignment->assessment()->id == get_global('personality')) {
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

                            // Safety scores
							else if ($assignment->assessment()->id == get_global('safety')) {
								array_push($row, number_format($assignment->score, 2));
							}

                            // All other scores
							else {
                                // Show as raw score
								if ($percentileAsScore) {
									array_push($row, number_format($assignment->score, 2));
                                }
                                // Show as a percentile
								else {
									array_push($row, $assignment->percentile . '%');
                                }
							}
						}

                        // Assignment assigned but not completed
						elseif ($assignment) {
							array_push($row, 'In Progress');
                        }

                        // No assignment
						else {
							array_push($row, 'Not Assigned');
                        }
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

}
