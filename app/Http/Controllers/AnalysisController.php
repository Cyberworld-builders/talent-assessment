<?php

namespace App\Http\Controllers;

use App\Analysis;
use App\Assessment;
use App\Client;
use App\Dimension;
use App\Jaq;
use App\Mailer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AnalysisController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @param $id
	 * @return \Illuminate\Http\Response
	 */
    public function index($id)
    {
		$client = Client::findOrFail($id);
		$analyses = $client->analyses;

		return view('dashboard.analysis.index', compact('client', 'analyses'));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param $id
	 * @return \Illuminate\Http\Response
	 */
    public function create($id)
    {
		$client = Client::findOrFail($id);

		$usersArray = [];
		foreach ($client->users as $user)
			$usersArray[$user->id] = $user->name . ' (' . $user->email . ')';

		$assessmentsArray = get_select_formatted_array(Assessment::all());
		$dimensionsArray = get_select_formatted_array(Dimension::all());

		return view('dashboard.analysis.create', compact('client', 'usersArray', 'assessmentsArray', 'dimensionsArray'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $id
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function store($id, Request $request)
    {
		$client = Client::findOrFail($id);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		$analysis = new Analysis($data);
		$client->analyses()->save($analysis);

		// Get our tasks and ksas together
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

		// Create a new JAQ for each user
		foreach ($analysis->users as $userId)
		{
			$user = User::find($userId);
			if (!$user)
				continue;

			$jaq = new Jaq([
				'user_id' => $user->id,
				'position' => 0,
				'tasks' => $tasks,
				'ksas' => $ksas
			]);
			$analysis->jaqs()->save($jaq);
		}

		return redirect('dashboard/clients/'.$client->id.'/analysis')->with('success', 'Analysis '.$analysis->name.' created successfully!');
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @param $analysisId
	 * @return \Illuminate\Http\Response
	 */
    public function show($id, $analysisId)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);

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

		// Check if any users don't have JAQs, and create empty ones for them
		if ($analysis->users)
		{
			foreach ($analysis->users as $userId)
			{
				$user = User::find($userId);
				if (!$user)
					continue;

				$jaq = $user->getJaqForAnalysis($analysis->id);
				if ($jaq)
					continue;

				$jaq = new Jaq([
					'user_id' => $user->id,
					'position' => 0,
					'tasks' => $tasks,
					'ksas' => $ksas,
					'ratings' => $ratings,
				]);
				$analysis->jaqs()->save($jaq);
			}
		}

		// Check if there are any left-over JAQs from removed users and purge them
		foreach ($analysis->jaqs as $jaq)
		{
			if (in_array($jaq->user_id, $analysis->users))
				continue;

			$jaq->delete();
		}

        return view('dashboard.analysis.show', compact('client', 'analysis'));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @param $analysisId
	 * @return \Illuminate\Http\Response
	 */
    public function edit($id, $analysisId)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);

		$usersArray = [];
		foreach ($client->users as $user)
			$usersArray[$user->id] = $user->name . ' (' . $user->email . ')';

		$assessmentsArray = get_select_formatted_array(Assessment::all());
		$dimensionsArray = get_select_formatted_array(Dimension::all());

		return view('dashboard.analysis.edit', compact('client', 'analysis', 'usersArray', 'assessmentsArray', 'dimensionsArray'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @param $analysisId
	 * @return \Illuminate\Http\Response
	 */
    public function update(Request $request, $id, $analysisId)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withErrors($validator->errors());

		$analysis->update($data);

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

		// Create new JAQs for newly-added users
		if ($analysis->users)
		{
			foreach ($analysis->users as $userId)
			{
				$user = User::find($userId);
				if (!$user)
					continue;

				$jaq = $user->getJaqForAnalysis($analysis->id);
				if ($jaq)
					continue;

				$jaq = new Jaq([
					'user_id' => $user->id,
					'position' => 0,
					'tasks' => $tasks,
					'ksas' => $ksas,
					'ratings' => $ratings,
				]);
				$analysis->jaqs()->save($jaq);
			}
		}

		return redirect('dashboard/clients/'.$client->id.'/analysis')->with('success', 'Analysis '.$analysis->name.' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $analysisId)
    {
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$analysis->delete();

		return redirect('dashboard/clients/'.$client->id.'/analysis')->with('success', 'Analysis '.$analysis->name.' deleted successfully!');
    }

	/**
	 * Send the questionnaires.
	 *
	 * @param  int $id
	 * @param $analysisId
	 * @return \Illuminate\Http\Response
	 */
	public function send($id, $analysisId)
	{
		$client = Client::findOrFail($id);
		$analysis = Analysis::findOrFail($analysisId);
		$subject = 'Please complete this Job Analysis Questionnaire';
		$body = '<h3>Hello [name]</h3><p>You have been assigned to complete a Job Analysis Questionnaire (JAQ) for the position of [analysis].</p><p>Login <a target="_blank" href="[login-link]">here</a> to view your assignments. You can use the following credentials:<br/>username: <i>[username]</i><br/>password: <i>[password]</i></p>';

		$count = 0;
		foreach ($analysis->users as $userId)
		{
			$user = User::find($userId);

			if (! $user)
				continue;

			$jaq = $analysis->jaqs()->where('user_id', $user->id)->first();

			if (! $jaq)
				continue;

			if ($this->send_questionnaire_to_user($user, $jaq->id, $subject, $body))
				$count++;
		}

		if (! $count)
			return redirect()->back()->with('error', 'Analysis not sent out. Invalid email addresses, users, or an error has occurred.');

		$analysis->sent_at = Carbon::now();
		$analysis->save();

		return redirect()->back()->with('success', 'Analysis sent to '.$count.' users successfully!');
	}

	/**
	 * Send the questionnaire to the user.
	 *
	 * @param $user
	 * @param $jaqId
	 * @param $subject
	 * @param $body
	 * @return bool
	 */
	private function send_questionnaire_to_user($user, $jaqId, $subject, $body)
	{
		if ($user->email && !filter_var($user->email, FILTER_VALIDATE_EMAIL) === false)
		{
			$mailer = new Mailer();
			return $mailer->send_questionnaire($user, $jaqId, $subject, $body);
		}

		return false;
	}
}
