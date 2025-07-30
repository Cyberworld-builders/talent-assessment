<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Assignment;
use App\Client;
use App\Analysis;
use App\GroupRole;
use Aws\S3\S3Client;
use Input;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();

        return view('dashboard.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $assessments = Assessment::all();
        $assessmentsArray = [];
        foreach ($assessments as $assessment)
            $assessmentsArray[$assessment->id] = $assessment->name;

        return view('dashboard.clients.create', compact('assessmentsArray'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|unique:clients',
            'logo' => 'image',
            'background' => 'image'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

		// Store the logo
		if ($request->file('logo'))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$data['logo'] = $imageName;
			$data['logo'] = $result->get('ObjectURL');
		}

		// Store the background
		if ($request->file('background'))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$data['background'] = $imageName;
			$data['background'] = $result->get('ObjectURL');
		}

        $client = new Client($data);
        $client->save();

        // Create group roles
        if (key_exists('role', $data))
        {
            foreach ($data['role'] as $i => $name)
            {
                $role = new GroupRole([
                    'name' => $name,
                    'client_id' => $client->id,
                    'slug' => $data['slug'][$i],
                    'level' => $data['level'][$i],
                ]);
                $role->save();
            }
        }

        return redirect('dashboard/clients')->with('success', 'Client '.$client->name.' created successfully!');
    }

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function show($id, Request $request)
    {
        $client = Client::findOrFail($id);
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

        return view('dashboard.clients.show', compact('users', 'client', 'paginator'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        $assessments = Assessment::all();
        $assessmentsArray = [];
        foreach ($assessments as $assessment)
            $assessmentsArray[$assessment->id] = $assessment->name;

        $groupRoles = $client->groupRoles;

        $jobs = $client->jobs;

        return view('dashboard.clients.edit', compact('client', 'assessmentsArray', 'groupRoles', 'jobs'));
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
        $data = $request->all();
        $client = Client::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|unique:clients,name,'.$client->id,
			'logo' => 'image',
			'background' => 'image'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());

		// Update the logo
		if (array_key_exists('logo', $data))
		{
			$imageName = $request->file('logo')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('logo')));
			//$request->file('logo')->move(uploads_path(), $imageName);
			//$data['logo'] = $imageName;
			$data['logo'] = $result->get('ObjectURL');
		}

		// Update the background
		if (array_key_exists('background', $data))
		{
			$imageName = $request->file('background')->getClientOriginalName();
			$s3 = new S3Client(config('aws'));
			$result = $s3->upload('aoe-uploads', 'images/'.$imageName, file_get_contents($request->file('background')));
			//$request->file('background')->move(uploads_path(), $imageName);
			//$data['background'] = $imageName;
			$data['background'] = $result->get('ObjectURL');
		}

        $client->update($data);

        return redirect()->back()->with('success', 'Client '.$client->name.' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);

		// Delete client's users and groups first
		foreach ($client->users as $user)
		{
			foreach ($user->groups() as $group)
				$group->delete();

			$user->delete();
		}

		// Delete the client itself
        $client->delete();

        return redirect('dashboard/clients')->with('success', 'Client '.$client->name.' deleted successfully!');
    }

	/**
     * Export users in excel format.
     *
     * @param $id
     */
    public function export_users($id)
    {
        $client = Client::find($id);
        $users = $client->users;

        $filename = 'Users for '.sanitize_string($client->name).' '.Carbon::now();

        $data = Excel::create($filename, function($excel) use ($users, $client)
        {
            $excel->setTitle('Users');
            $excel->sheet('Details', function($sheet) use ($users, $client) {
                $sheet->loadView('excel.client-users', compact('users', 'client'));
            });
        });

        $data->store('csv')->export('csv');
    }
}
