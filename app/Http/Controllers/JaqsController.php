<?php

namespace App\Http\Controllers;

use App\Analysis;
use App\Client;
use App\Jaq;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class JaqsController extends Controller
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
	 * Store an answer to a JAQ question, from the user side.
	 * See storeAdmin() for storing from the admin side.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store($id, Request $request)
	{
		$jaq = Jaq::findOrFail($id);
		$user = \Auth::user();
		$analysis = $jaq->analysis;
		$data = $request->all();

		// Safety checks
		if (! $analysis)
			abort(403, 'Analysis for this questionnaire does not exist.');
		if ($jaq->user_id != $user->id)
			abort(403, 'Not allowed to access this questionnaire.');
		if ($jaq->completed)
			abort(403, 'This questionnaire has already been completed.');

		// Complete the questionnaire
		if ($data['complete'])
		{
			$jaq->completed = true;
			$jaq->completed_at = Carbon::now();
			$jaq->save();
			return \Response::json(['success' => true, 'redirect' => true]);
		}

		// Store a questionnaire question
		else
		{
			// If input is an array
			if (strpos($data['name'], '['))
			{
				$name = substr($data['name'], 0, strpos($data['name'], '['));
				preg_match_all("/\[([^\]]*)\]/", $data['name'], $indeces);
				$index = (int)$indeces[1][0];

				// Save tasks
				if ($name == 'tasks')
				{
					$tasks = [];
					foreach ($data['value']['tasks'] as $task)
					{
						if (! $task['task'])
							continue;

						if (! array_key_exists('relevant', $task))
							$task['relevant'] = 0;

						$tasks[] = $task;
					}

					$jaq->update([
						'tasks' => $tasks
					]);
				}

				// Save ksas
				if ($name == 'ksas')
				{
					$ksas = [];
					foreach ($data['value']['ksas'] as $ksa)
					{
						if (! $ksa['name'])
							continue;

						if (! array_key_exists('relevant', $ksa))
							$ksa['relevant'] = 0;

						$ksas[] = $ksa;
					}

					$jaq->update([
						'ksas' => $ksas
					]);
				}

				// Save ratings
				if ($name == 'ratings')
				{
					$ratings = $jaq->ratings;
					$ratings[$index] = (float)$data['value'];

					$jaq->update([
						'ratings' => $ratings
					]);
				}

				// Save linkages
				if ($name == 'ksa_linkages')
				{
					$jaq->update([
						'ksa_linkages' => $data['value']['ksa_linkages']
					]);
				}
			}

			// Otherwise
			else
			{
				// Save regular input
				$jaq->update([
					$data['name'] => $data['value']
				]);
			}

			// Update tasks and ksas on position change
			if ($data['name'] == 'position')
			{
				$tasks = [];
				foreach ($analysis->tasks[$data['value']] as $i => $task)
				{
					$tasks[$i]['task'] = $task;
					$tasks[$i]['relevant'] = 0;
				}
				$ksas = [];
				foreach ($analysis->ksas[$data['value']] as $i => $ksa)
				{
					$ksas[$i]['name'] = $ksa['name'];
					$ksas[$i]['description'] = $ksa['description'];
					$ksas[$i]['relevant'] = 0;
				}
				$ratings = [];
				foreach ($analysis->ratings[$data['value']] as $i => $rating)
					$ratings[$i] = 3;

				$jaq->update([
					'tasks' => $tasks,
					'ksas' => $ksas,
					'ratings' => $ratings,
				]);
			}
		}

		return \Response::json(['success' => true]);
	}

    /**
     * Store an answer to a JAQ question, from the admin side.
	 * See store() for storing from the user side.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminStore($id, $analysisId, $jaqId, Request $request)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$jaq = Jaq::findOrFail($jaqId);
		$data = $request->all();

		// Complete the questionnaire
		if ($data['complete'])
		{
			$jaq->completed = true;
			$jaq->completed_at = Carbon::now();
			$jaq->save();
			return \Response::json(['success' => true, 'redirect' => true]);
		}

		// Store a questionnaire question
		else
		{
			// If input is an array
			if (strpos($data['name'], '['))
			{
				$name = substr($data['name'], 0, strpos($data['name'], '['));
				preg_match_all("/\[([^\]]*)\]/", $data['name'], $indeces);
				$index = (int)$indeces[1][0];

				// Save tasks
				if ($name == 'tasks')
				{
					$tasks = [];
					foreach ($data['value']['tasks'] as $task)
					{
						if (! $task['task'])
							continue;

						if (! array_key_exists('relevant', $task))
							$task['relevant'] = 0;

						$tasks[] = $task;
					}

					$jaq->update([
						'tasks' => $tasks
					]);
				}

				// Save ksas
				if ($name == 'ksas')
				{
					$ksas = [];
					foreach ($data['value']['ksas'] as $ksa)
					{
						if (! $ksa['name'])
							continue;

						if (! array_key_exists('relevant', $ksa))
							$ksa['relevant'] = 0;

						$ksas[] = $ksa;
					}

					$jaq->update([
						'ksas' => $ksas
					]);
				}

				// Save ratings
				if ($name == 'ratings')
				{
					$ratings = $jaq->ratings;
					$ratings[$index] = (float)$data['value'];

					$jaq->update([
						'ratings' => $ratings
					]);
				}

				// Save linkages
				if ($name == 'ksa_linkages')
				{
					$jaq->update([
						'ksa_linkages' => $data['value']['ksa_linkages']
					]);
				}
			}

			// Otherwise
			else
			{
				// Save regular input
				$jaq->update([
					$data['name'] => $data['value']
				]);
			}

			// Update tasks and ksas on position change
			if ($data['name'] == 'position')
			{
				$tasks = [];
				foreach ($analysis->tasks[$data['value']] as $i => $task)
				{
					$tasks[$i]['task'] = $task;
					$tasks[$i]['relevant'] = 0;
				}
				$ksas = [];
				foreach ($analysis->ksas[$data['value']] as $i => $ksa)
				{
					$ksas[$i]['name'] = $ksa['name'];
					$ksas[$i]['description'] = $ksa['description'];
					$ksas[$i]['relevant'] = 0;
				}
				$ratings = [];
				foreach ($analysis->ratings[$data['value']] as $i => $rating)
					$ratings[$i] = 3;

				$jaq->update([
					'tasks' => $tasks,
					'ksas' => $ksas,
					'ratings' => $ratings,
				]);
			}
		}

		return \Response::json(['success' => true]);
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @param $analysisId
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function show($id, $analysisId, $jaqId, Request $request)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$jaq = Jaq::findOrFail($jaqId);
		$page = $request->input('page');

		// Check if questionnaire is already completed
		if ($jaq->completed and !\Auth::user()->is('admin'))
			return view('questionnaire.complete');

		$analysis->job_code = [''=>null] + explode(',', $analysis->job_code);
		$analysis->department_name = [''=>null] + explode(',', $analysis->department_name);
		$analysis->supervisor_title = [''=>null] + explode(',', $analysis->supervisor_title);
		$analysis->location = [''=>null] + explode(',', $analysis->location);

        return view('questionnaire.show', compact('client', 'analysis', 'jaq', 'page'));
    }

	public function showForUser($id, Request $request)
	{
		$user = \Auth::user();
		$jaq = Jaq::findOrFail($id);

		if ($user->id != $jaq->user_id)
			abort(403, 'Not allowed to access this job analysis questionnaire');

		$client = Client::findOrFail($user->client->id);
		$analysis = Analysis::findOrFail($jaq->analysis->id);
		$page = $request->input('page');

		// Check if questionnaire is already completed
		if ($jaq->completed)
			return view('questionnaire.complete');

		$analysis->job_code = [''=>null] + explode(',', $analysis->job_code);
		$analysis->department_name = [''=>null] + explode(',', $analysis->department_name);
		$analysis->supervisor_title = [''=>null] + explode(',', $analysis->supervisor_title);
		$analysis->location = [''=>null] + explode(',', $analysis->location);

		return view('questionnaire.show', compact('client', 'analysis', 'jaq', 'page'));
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
    public function destroy($id, $analysisId, $jaqId)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$jaq = Jaq::findOrFail($jaqId);
		$user = $jaq->user;

		// Remove the user form the analysis
		$userIds = [];
		foreach ($analysis->users as $userId)
			if ($userId != $user->id)
				$userIds[] = $userId;
		$analysis->update([
			'users' => $userIds
		]);

		// Delete the jaq itself
		$jaq->delete();

		return redirect('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id)->with('success', 'Removed '.$user->name.' from this analysis successfully!');
    }

	/**
	 * Reset the questionnaire inputs, except sent_at date.
	 *
	 * @param $id
	 * @param $analysisId
	 * @param $jaqId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function reset($id, $analysisId, $jaqId)
	{
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$jaq = Jaq::findOrFail($jaqId);

		// Get our tasks, ksas, and ratings together
		$tasks = [];
		foreach ($analysis->tasks[0] as $i => $task)
		{
			$tasks[$i]['task'] = $task;
			$tasks[$i]['relevant'] = 0;
		}
		$ksas = [];
		foreach ($analysis->ksas[0] as $i => $ksa)
		{
			$ksas[$i]['name'] = $ksa['name'];
			$ksas[$i]['description'] = $ksa['description'];
			$ksas[$i]['relevant'] = 0;
		}
		$ratings = [];
		foreach ($analysis->ratings[0] as $i => $rating)
			$ratings[$i] = 3;

		$jaq->update([
			'name' => null,
			'position' => 0,
			'job_code' => 0,
			'department_name' => null,
			'location' => null,
			'supervisor_name' => null,
			'supervisor_title' => null,
			'position_desc' => null,
			'tasks' => $tasks,
			'ksas' => $ksas,
			'ksa_linkages' => null,
			'min_education' => null,
			'preferred_education' => null,
			'min_experience' => null,
			'preferred_experience' => null,
			'additional_requirements' => null,
			'ratings' => $ratings,
			'completed_at' => 0,
		]);

		return redirect()->back()->with('success', 'JAQ reset successfully!');
    }
}
