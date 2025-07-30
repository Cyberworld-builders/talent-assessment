<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use App\Http\Controllers\ReportsController;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SurveysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $client = Client::findOrFail($id);
        $surveys = DB::table('assignments')
            ->join('users', 'users.id', '=', 'assignments.user_id')
            ->select('assignments.*')
			->where('users.client_id', '=', $client->id)
			->whereIn('assignments.assessment_id', [
				get_global('leader'),
				get_global('leader-s'),
				get_global('leader-sr'),
				get_global('360')
			])
            ->groupBy('created_at')
            ->get();

		$surveys = array_reverse($surveys);

        return view('dashboard.surveys.index', compact('client', 'surveys'));
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
     * @param  int $id
     * @param $date
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, $date, Request $request)
    {
        $client = Client::findOrFail($id);
//        $assignments = Assignment::where('created_at', $date)->groupBy('custom_fields')->get();
		$developmentAssessments = [
			(int)get_global('leader'),
			(int)get_global('leader-s'),
			(int)get_global('leader-sr'),
			(int)get_global('360'),
		];
        $assignments = Assignment::where('created_at', $date)->get()->filter(function($assignment) use ($developmentAssessments) {
        	if (! in_array($assignment->assessment_id, $developmentAssessments))
        		return false;
        	return true;
		});
        $survey = Assignment::where('created_at', $date)->first();
        $s = new ReportsController();

        // Get all leaders that were rated
        $leadersArray = [];
		$previouslyPicked = [];
        foreach ($assignments as $assignment)
        {
        	if (! $assignment->custom_fields)
        		continue;

            foreach ($assignment->custom_fields['type'] as $i => $field)
            {
                $user = false;
                
                // Find by name
                if ($field == 'name')
                {
                    $name = $assignment->custom_fields['value'][$i];
                    $user = User::where('name', $name)->first();
                }

                // Find by email
                if ($field == 'email')
                {
                    $email = $assignment->custom_fields['value'][$i];
                    $user = User::where('email', $email)->first();
                }

				// Find by target
				if ($assignment->target_id)
					$user = User::where('id', $assignment->target_id)->first();

                // If user exists
                if ($user)
                {
                    // Ignore users not part of this client
                    if ($user->client_id != $client->id)
                        continue;

					// Ignore users already added
					if (in_array($user->id, $previouslyPicked))
						continue;

                    // Add to leaders array
                    $user->survey = $assignment;
                    array_push($leadersArray, $user);
					array_push($previouslyPicked, $user->id);
                }
            }
        }

        // Make into a collection
        $leaders = collect($leadersArray)->sortBy('name');

        // Paginate the results
        $page = $request->page;
        if (! $page) $page = 1;
        $perPage = 10;
        $paginator = new LengthAwarePaginator($leaders->forPage($page, $perPage), $leaders->count(), $perPage, $page);
        $leaders = collect($paginator->items());

        // Add in the scores
        foreach ($leaders as $leader)
        {
            // Get all assignments which rated this leader
            $assignmentIds = $this->getAssignmentIds($date, $leader);
            $leader->scorers = count($assignmentIds);

            // Calculate the scores
//            if ($leader->scorers)
//                $score['overall'] = $s->getOverallLeaderScore($survey, $assignmentIds);
//            else
//                $score['overall'] = 0;
//            $division['overall'] = $s->getLeaderDivision($score['overall']);

            // Calculate scores for each dimension
//                    foreach ($survey->assessment()->dimensions as $dimension)
//                    {
//                        if ($dimension->isChild())
//                            continue;
//
//                        $dimScore = $s->getScoreForDimension($assignmentIds, $dimension->id);
//                        $score[$dimension->id] = $dimScore;
//                        $division[$dimension->id] = $s->getLeaderDivision($dimScore);
//                    }

            // Add to collection
//            $leader->division = $division;
//            $leader->score = $score;
        }

        return view('dashboard.surveys.show', compact('client', 'survey', 'leaders', 'paginator'));
    }

	/**
     * Get a count of all the users that rated this leader.
     *
     * @param $date
     * @param $user
     * @return mixed
     */
    public function getAssignmentIds($date, $user)
    {
        // Find all completed assignments that pertain to this leader
        $assignments = Assignment::where([
            'created_at' => $date,
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

        $assignmentIds = [];
        foreach ($assignments as $assignment)
            array_push($assignmentIds, $assignment->id);

        return $assignmentIds;
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
	 * Generate reports.
	 *
	 * @param  int $id
	 * @param $date
	 * @return \Illuminate\Http\Response
	 */
	 public function generate($id, $date)
	{
		ini_set('max_execution_time', 520);
		$client = Client::findOrFail($id);

		$developmentAssessments = [
			(int)get_global('leader'),
			(int)get_global('leader-s'),
			(int)get_global('leader-sr'),
			(int)get_global('360'),
		];

		$assignments = Assignment::where('created_at', $date)->get()->filter(function($assignment) use ($developmentAssessments) {
			if (! in_array($assignment->assessment_id, $developmentAssessments))
				return false;
			return true;
		});
		$survey = Assignment::where('created_at', $date)->first();

		// Get all leaders that were rated
		$leadersArray = [];
		$previouslyPicked = [];
		foreach ($assignments as $assignment)
		{
			if (! $assignment->custom_fields)
				continue;

			foreach ($assignment->custom_fields['type'] as $i => $field)
			{
				$user = false;

				// Find by name
				if ($field == 'name')
				{
					$name = $assignment->custom_fields['value'][$i];
					$user = User::where('name', $name)->first();
				}

				// Find by email
				if ($field == 'email')
				{
					$email = $assignment->custom_fields['value'][$i];
					$user = User::where('email', $email)->first();
				}

				// Find by target
				if ($assignment->target_id)
					$user = User::where('id', $assignment->target_id)->first();

				// If user exists
				if ($user)
				{
					// Ignore users not part of this client
					if ($user->client_id != $client->id)
						continue;

					// Ignore users already added
					if (in_array($user->id, $previouslyPicked))
						continue;

					// Add to leaders array
					$user->survey = $assignment;
					array_push($leadersArray, $user);
					array_push($previouslyPicked, $user->id);
				}
			}
		}

		// Make into a collection
		$leaders = collect($leadersArray)->sortBy('name');

		// Add in the scores
		foreach ($leaders as $leader)
		{
			// Get all assignments which rated this leader
			$assignmentIds = $this->getAssignmentIds($date, $leader);
			$leader->scorers = count($assignmentIds);
			$leader->assignmentIds = $assignmentIds;
		}

		// Calculate the scores and store them
		$total = 0;
		foreach ($leaders as $leader){$total += count($leader->assignmentIds);}
		$i = 0;
		sse_init();
		$r = new ReportsController();

		foreach ($leaders as $leader)
		{
			foreach ($leader->assignmentIds as $assignmentId)
			{
				// Check for existing stored report data
				$reportData = DB::table('report_data')->where([
					'user_id' => $leader->id,
					'assignment_id' => $assignmentId
				])->value('score');

				if ($reportData) {
					$i++;
					sse_send($i, ($i / $total) * 100);
					continue;
				}

				$view = $r->indexDevelopment($id, $assignmentId, $leader->id);
				$reportData = $view->getData();

				DB::table('report_data')->insert([
					'user_id' => $leader->id,
					'assignment_id' => $assignmentId,
					'score' => json_encode($reportData)
				]);

				$i++;
				sse_send($i, ($i / $total) * 100);
			}
		}

		$return_data = 'All done!';
		sse_complete($return_data);

		dd($leaders);
	}
}
