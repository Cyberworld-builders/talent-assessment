<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Client;
use App\Dimension;
use App\Job;
use App\Report;
use App\Question;
use App\User;
use App\ClientReport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Assign;
use Illuminate\Support\Facades\Validator;

class ClientReportsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @param $id
	 * @param null $jobId
	 * @return \Illuminate\Http\Response
	 */

    public function index($id, $jobId)
    {
        $client = Client::findOrFail($id);
		$reports = Report::where('client_id', $id)->get();
		if (!$jobId) $jobId = null;
        $clientReports = $client->reports()->where('job_id', $jobId)->get();

        // For each available report template
		foreach ($reports as $report)
		{
			$report->customized = false;
			$report->client_report = null;

			// Find any customizations
			foreach ($clientReports as $clientReport)
			{
				if ($clientReport->report_id != $report->id)
					continue;

				$report->client_report = $clientReport;
				if ($clientReport->fields) $report->customized = true;
				break;
			}
		}

        return view('dashboard.reports.index', compact('reports', 'client', 'clientReports', 'jobId'));
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $reportsId
	 * @return \Illuminate\Http\Response
	 */
	public function create($id, $jobId, $reportsId)
	{
		$client = Client::findOrFail($id);
        $report = Report::findOrFail($reportsId);
		$clientReport = new ClientReport([
			'client_id' => $client->id,
			'report_id' => $reportsId,
			'job_id' => ($jobId ? $jobId : null),
			'enabled' => 0,
			'visible' => 0,
			'fields' => null,
		]);
		$clientReport->save();
		$edit = true;

		// Decode the default fields
		$report->fields = json_decode($report->fields);

		return view('dashboard.reports.edit', compact('client', 'report', 'clientReport', 'jobId', 'edit'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $reportsId
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function store($id, $jobId, $reportsId, Request $request)
    {
		$client = Client::findOrFail($id);
		$data = $request->all();
		$report = Report::findOrFail($reportsId);

		$clientReport = new ClientReport([
			'client_id' => $client->id,
			'report_id' => $report->id,
			'job_id' => ($jobId ? $jobId : null),
			'fields' => $data['fields'],
		]);

		$clientReport->save();
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $reportsId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
	 */
	public function edit($id, $jobId, $reportsId)
    {
        $client = Client::findorFail($id);
        $report = Report::findOrFail($reportsId);
        $clientReport = ClientReport::where([
            'client_id' => $client->id,
            'report_id' => $report->id,
			'job_id' => ($jobId ? $jobId : null)
        ])->first();
        $edit = true;

        if (! $clientReport)
            return $this->create($id, $jobId, $reportsId);

        // Decode the default fields
		$report->fields = json_decode($report->fields);

        return view('dashboard.reports.edit', compact('client', 'report', 'clientReport', 'jobId', 'edit'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $id
	 * @param $jobId
	 * @param $clientReportId
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 * @internal param $reportsId
	 */
	public function update($id, $jobId, $clientReportId, Request $request)
    {
        $client = Client::findorFail($id);
        $clientReport = ClientReport::findOrFail($clientReportId);
        $data = $request->all();
        $clientReport->update($data);

        return redirect('dashboard/clients/'.$client->id.'/jobs/'.$jobId.'/reports')->with('success', 'Client report customization saved successfully!');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @param $jobId
	 * @param $clientReportId
	 * @return \Illuminate\Http\Response
	 * @internal param $reportsId
	 */
	public function destroy($id, $jobId, $clientReportId)
	{
		$client = Client::findOrFail($id);
		$clientReport = ClientReport::findOrFail($clientReportId);
		$report = $clientReport->report;
		$clientReport->delete();

		return redirect('dashboard/clients/'.$client->id.'/jobs/'.$jobId.'/reports')->with('success', $report->name.' report customizations reset successfully!');
	}

	/**
	 * Enable or disable a client report.
	 *
	 * @param $id
	 * @param $reportId
	 * @param Request $request
	 * @param null $jobId
	 */
	public function toggleVisibility($id, $jobId, $reportId, Request $request)
	{
		$client = Client::findorFail($id);
		if (!$jobId) $jobId = null;
		$data = $request->all();
		$clientReport = ClientReport::where([
			'client_id' => $client->id,
			'report_id' => $reportId,
			'job_id' => $jobId
		])->first();

		if (! $clientReport)
			$clientReport = new ClientReport([
				'client_id' => $client->id,
				'report_id' => $reportId,
				'job_id' => $jobId
			]);

		$clientReport->enabled = $data['enabled'];
		$clientReport->visible = $data['visible'];
		$clientReport->save();
	}
}
