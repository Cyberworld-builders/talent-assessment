<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Client;
use App\Job;
use App\PredictiveModel;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class PredictiveModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
		$client = Client::findOrFail($id);
		$jobs = Job::where('client_id', $id)->get();

		return view('dashboard.spss.index', compact('client', 'jobs'));
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

		$jobsArray = [null => 'Select a Job..'];
		foreach ($jobs as $job)
			$jobsArray[$job->id] = $job->name;

		$jobAssessments = [];
		foreach ($jobs as $job)
		{
			foreach ($job->assessments as $assessment)
			{
				$assessment = Assessment::find($assessment);
				$jobAssessments[$job->id][] = [
					'id' => $assessment->id,
					'name' => $assessment->name,
				];
			}
		}

		return view('dashboard.spss.create', compact('client', 'jobsArray', 'jobAssessments'));
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
			'job' => 'required',
			'assessments' => 'required',
			'file' => 'required|mimes:xml',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		$filePath = $request->file('file')->getRealPath();
		$xml = simplexml_load_file($filePath);

		$jsonXml = json_decode(json_encode($xml));

		if (! $jsonXml->Header->Application->{'@attributes'}->name == 'IBM SPSS Modeler')
			return redirect()->back()->withInput()->with('error', 'The provided XML file is not a valid SPSS Modeler PMML file.');

		// Get our factors (what we will need to get scores for)
		$factors = [];
		foreach ($jsonXml->DataDictionary->DataField as $field)
		{
			if ($field->{'@attributes'}->dataType == 'string' || $field->{'@attributes'}->optype == 'categorical')
				continue;

			$factors[] = [
				'name' => $field->{'@attributes'}->name,
				'type' => null,
				'id' => null,
			];
		}

		$job = Job::findOrFail((int)$data['job']);

		$model = new PredictiveModel([
			'name' => $data['name'],
			'job_id' => $job->id,
			'assessments' => $data['assessments'],
			'model' => $xml,
			'filename' => $request->file('file')->getClientOriginalName(),
			'factors' => $factors,
		]);
		$model->save();

		return redirect('dashboard/clients/'.$client->id.'/models/'.$model->id.'/edit')->with('success', 'Predictive model for '.$job->name.' created successfully!');
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
    public function edit($id, $modelId)
    {
		$client = Client::findOrFail($id);
		$model = PredictiveModel::findOrFail($modelId);
		$job = $model->job;
		$jobsArray = [$job->id => $job->name];

		$jobAssessments = [];
		foreach ($job->assessments as $assessment)
		{
			$assessment = Assessment::find($assessment);
			$jobAssessments[$assessment->id] = $assessment->name;
		}

		$assessments = [];
		$assessmentsArray = [];
		foreach ($job->assessments as $i => $assessment)
		{
			$assessment = Assessment::find($assessment);
			$assessments[$i]['id'] = $assessment->id;
			$assessments[$i]['name'] = $assessment->name;
			$assessmentsArray[$assessment->id] = $assessment->name;
		}

		$dimensions = [];
		$dimensionsArray = [];
		$i = 0;
		foreach ($job->assessments as $assessment)
		{
			$assessment = Assessment::find($assessment);
			if ($assessment->dimensions)
				foreach ($assessment->dimensions as $dimension)
				{
					$dimensions[$i]['id'] = $dimension->id;
					$dimensions[$i]['name'] = $dimension->name;
					$i++;
					$dimensionsArray[$dimension->id] = $dimension->name;
				}
		}

		return view('dashboard.spss.edit', compact('client', 'model', 'job', 'jobsArray', 'jobAssessments', 'assessments', 'dimensions', 'assessmentsArray', 'dimensionsArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, $modelId, Request $request)
    {
		$client = Client::findOrFail($id);
		$model = PredictiveModel::findOrFail($modelId);
		$data = $request->all();

		$validator = Validator::make($data, [
			'name' => 'required',
			'job' => 'required',
			'assessments' => 'required',
		]);

		if ($validator->fails())
			return redirect()->back()->withInput()->withErrors($validator->errors());

		// New file is being uploaded
		if (array_key_exists('file', $data))
		{
			$filePath = $request->file('file')->getRealPath();
			$xml = simplexml_load_file($filePath);

			$jsonXml = json_decode(json_encode($xml));

			if (! $jsonXml->Header->Application->{'@attributes'}->name == 'IBM SPSS Modeler')
				return redirect()->back()->withInput()->with('error', 'The provided XML file is not a valid SPSS Modeler PMML file.');

			// Get our factors (what we will need to get scores for)
			$factors = [];
			foreach ($jsonXml->DataDictionary->DataField as $field)
			{
				if ($field->{'@attributes'}->dataType == 'string' || $field->{'@attributes'}->optype == 'categorical')
					continue;

				$factors[] = [
					'name' => $field->{'@attributes'}->name,
					'type' => null,
					'id' => null,
				];
			}

			$model->update([
				'model' => $xml,
				'filename' => $request->file('file')->getClientOriginalName(),
				'factors' => $factors,
			]);

			return redirect('dashboard/clients/'.$client->id.'/models/'.$model->id.'/edit')->with('success', 'New predictive model uploaded successfully!');
		}

		// Update the factors
		$factors = [];
		foreach ($model->factors as $i => $factor)
		{
			$factors[$i]['name'] = $factor['name'];
			if (array_key_exists($i, $data['factors']['type']))
				$factors[$i]['type'] = $data['factors']['type'][$i];

			if (array_key_exists($i, $data['factors']['id']))
				$factors[$i]['id'] = $data['factors']['id'][$i];
		}

		// Check if all factors are configured
		$configured = 1;
		foreach ($factors as $factor)
			if ($factor['name'] == null || $factor['type'] == null || $factor['id'] == null)
				$configured = 0;

		$model->update([
			'name' => $data['name'],
			'assessments' => $data['assessments'],
			'factors' => $factors,
			'configured' => $configured,
		]);

		return redirect('dashboard/clients/'.$client->id.'/models')->with('success', 'Predictive model updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $modelId)
    {
		$client = Client::findOrFail($id);
		$model = PredictiveModel::findOrFail($modelId);

		$model->delete();

		return redirect('dashboard/clients/'.$client->id.'/models')->with('success', 'Predictive model deleted successfully!');
    }
}
