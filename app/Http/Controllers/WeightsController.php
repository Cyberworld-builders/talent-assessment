<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Client;
use App\Job;
use App\PredictiveModel;
use App\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WeightsController extends Controller
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

        return view('dashboard.weights.index', compact('client', 'jobs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $id
     * @param $jobId
     * @param $assessmentId
     * @return \Illuminate\Http\Response
     */
    public function create($id, $jobId, $assessmentId)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $assessment = Assessment::findOrFail($assessmentId);
        
        return view('dashboard.weights.create', compact('client', 'job', 'assessment'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $id
     * @param $jobId
     * @param $assessmentId
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, $jobId, $assessmentId, Request $request)
    {
        $client = Client::findOrFail($id);
        $job = Job::findOrFail($jobId);
        $assessment = Assessment::findOrFail($assessmentId);
        $data = $request->all();

        $validator = Validator::make($data, [
            'total' => 'numeric|size:100'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        // Generate dimension weights array
        $weights = [];
        if (! $assessment->dimensions->isEmpty())
        {
            foreach ($data['dimension'] as $i => $dimensionId)
                $weights[$dimensionId] = $data['weight'][$i];
        }

        // Generate divisions array
        $divisions = [];
        foreach ($data['division'] as $i => $division)
        {
            $divisions[$i] = [
                'name' => $division,
                'min' => $data['division_min'][$i],
                'max' => $data['division_max'][$i]
            ];
        }

        // Save the new weight
        $weight = new Weight();
        $weight->job_id = $job->id;
        $weight->assessment_id = $assessment->id;
        $weight->weights = $weights;
        $weight->divisions = $divisions;
        $weight->save();

        return redirect('dashboard/clients/'.$client->id.'/weights')->with('success', 'Custom weighting set for '.$job->name.' '.$assessment->name.' successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('dashboard.weights.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param $weightId
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $weightId)
    {
        $client = Client::findOrFail($id);
        $weight = Weight::findOrFail($weightId);
        $job = $weight->job;
        $assessment = $weight->assessment;

        return view('dashboard.weights.edit', compact('client', 'weight', 'job', 'assessment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param $weightId
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, $weightId, Request $request)
    {
        $client = Client::findOrFail($id);
        $weight = Weight::findOrFail($weightId);
        $job = $weight->job;
        $assessment = $weight->assessment;
        $data = $request->all();

        $validator = Validator::make($data, [
            'total' => 'numeric|size:100'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

        // Generate dimension weights array
        $weights = [];
        if (! $assessment->dimensions->isEmpty())
        {
            foreach ($data['dimension'] as $i => $dimensionId)
                $weights[$dimensionId] = $data['weight'][$i];
        }

        // Generate divisions array
        $divisions = [];
        foreach ($data['division'] as $i => $division)
        {
            $divisions[$i] = [
                'name' => $division,
                'min' => $data['division_min'][$i],
                'max' => $data['division_max'][$i]
            ];
        }

        // Update the weight
        $weight->weights = $weights;
        $weight->divisions = $divisions;
        $weight->save();

        return redirect('dashboard/clients/'.$client->id.'/weights')->with('success', 'Custom weighting updated for '.$job->name.' '.$assessment->name.' successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $weightId)
    {
        $client = Client::findOrFail($id);
        $weight = Weight::findOrFail($weightId);
        $assessment = $weight->assessment;

        $weight->delete();

        return redirect('dashboard/clients/'.$client->id.'/weights')->with('success', 'Custom weighting for '.$assessment->name.' deleted successfully!');
    }
}
