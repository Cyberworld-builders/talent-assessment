<?php

namespace App\Http\Controllers;

use App\Client;
use App\Group;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class GroupsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $client = Client::FindOrFail($id);
        $groups = $client->groups;

        return view('dashboard.groups.index', compact('client', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $client = Client::FindOrFail($id);
        $users = $client->users;

        $usersArray = [];
        foreach ($users as $user)
            $usersArray[$user->id] = $user->name . ' (' . $user->email . ')';

		$targetsArray = User::getSelectFormattedArrayForClient($client->id);
		$targetsArray = [0 => 'None'] + $targetsArray;

        return view('dashboard.groups.create', compact('client', 'users', 'usersArray', 'groupRolesArray', 'targetsArray'));
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

        $users = [];
        foreach ($data['user_id'] as $i => $userId)
        {
            if (! $userId) continue;

            $users[$i]['id'] = $userId;
            $users[$i]['position'] = $data['group_position'][$i];
            $users[$i]['leader'] = $data['leader'][$i];
        }

        // Create a new group
        $group = new Group([
            'name' => $data['name'],
            'description' => $data['description'],
            'users' => $users,
        ]);

		// Set the target
		if ($data['target_id'])
			$group->target_id = $data['target_id'];

        $client->groups()->save($group);

        return redirect('dashboard/clients/'.$client->id.'/groups')->with('success', 'Group '.$group->name.' created successfully!');
    }

	public function autoGenerateGroups($id, Request $request)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$targets = [];

		// First, sort our user data by target
		foreach ($data['users'] as $user)
		{
			if (! key_exists($user['target_id'], $targets))
				$targets[$user['target_id']] = [];

			array_push($targets[$user['target_id']], $user);
		}

		// Now for each target, make the users rating him/her a separate group
		$counter = 1;
		foreach ($targets as $targetId => $targetRaters)
		{
			// Setup our users array for the Group model
			$users = [];
			foreach ($targetRaters as $i => $rater)
			{
				$users[$i]['id'] = $rater['id'];
				if ($rater['id'] == $rater['target_id'])
					$rater['role'] = 'Self';
				$users[$i]['position'] = $rater['role'];
				$users[$i]['leader'] = 0;
			}

			// Create a new group
			$group = new Group([
				'name' => 'Group '.$counter,
				'description' => 'Auto-generated group for '.$client->name,
				'users' => $users,
			]);

			// Add also our target into this group
//			$users[$i + 1]['id'] = $rater['target_id'];
//			$users[$i + 1]['position'] = 'User Being Rated';
//			$users[$i + 1]['leader'] = '';
			$group->target_id = $rater['target_id'];

			$client->groups()->save($group);
			$counter++;
		}

//		return redirect('dashboard/clients/'.$client->id.'/groups')->with('success', 'Group '.$group->name.' created successfully!');
		return \Response::json(['success' => 1]);
    }

	/**
	 * Upload and parse an excel spreadsheet of custom fields.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadGroups($id, Request $request)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$users = [];

		$validator = Validator::make($data, [
			'file' => 'required|mimes:xls,xlsx'
		]);

		if ($validator->fails())
			return \Response::json(['errors' => 'File must be a valid .xls or a .xlsx file format.']);

		Excel::load($data['file'], function($reader) use (&$users, $client) {
			$results = $reader->all();

			$reader->each(function($sheet) use (&$users, $client)
			{
				$sheet->each(function($row) use (&$users, $client)
				{
					// These are the columns we need to find
					$searchForColumns = [
						'targetName' => [
							'column' => null,
							'keywords' => ['target', 'name']
						],
						'targetEmail' => [
							'column' => null,
							'keywords' => ['target', 'email']
						],
						'userName' => [
							'column' => null,
							'keywords' => ['name']
						],
						'userEmail' => [
							'column' => null,
							'keywords' => ['email']
						],
						'userRole' => [
							'column' => null,
							'keywords' => ['role']
						]
					];
					$rowArray = $row->toArray();

					// Setup a counter of sorts to keep track of which columns were already found
					$found = [];
					foreach ($rowArray as $column => $value)
						$found[$column] = 0;

					// Find the actual columns
					foreach ($rowArray as $column => $value)
					{
						// For each column we need to find
						foreach ($searchForColumns as $i => $search)
						{
							// If this column has already been found, skip it
							if ($found[$column])
								continue;

							// Search using the keywords
							$keywordsFound = 0;
							foreach ($search['keywords'] as $keyword)
							{
								if (contains_word($column, $keyword))
									$keywordsFound++;
							}

							// If all keywords found in this column name, then we found our column
							if ($keywordsFound == count($search['keywords']))
							{
								$searchForColumns[$i]['column'] = $column;
							}
						}
					}

					// Find the user
					$user = null;
					$email = $rowArray[$searchForColumns['userEmail']['column']];
					$name = $rowArray[$searchForColumns['userName']['column']];
					$role = $rowArray[$searchForColumns['userRole']['column']];
					if ($email and $name)
						$user = User::where([
							'email' => $email,
							'client_id' => $client->id,
						])->orWhere([
							'name' => $name,
							'client_id' => $client->id,
						])->first();

					// Find the target
					$target = null;
					$email = $rowArray[$searchForColumns['targetEmail']['column']];
					$name = $rowArray[$searchForColumns['targetName']['column']];
					if ($email and $name)
						$target = User::where([
							'email' => $email,
							'client_id' => $client->id,
						])->orWhere([
							'name' => $name,
							'client_id' => $client->id,
						])->first();

					// If we have both, spit out our data
					if ($user and $target)
					{
						array_push($users, [
							'id' => $user->id,
							'name' => $user->name,
							'email' => $user->email,
							'role' => $role,
							'target_id' => $target->id,
							'target_name' => $target->name,
							'target_email' => $target->email,
						]);
					}
				});
			});
		});

		return \Response::json(['users' => $users]);
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
     * @param  int $id
     * @param $groupId
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $groupId)
    {
        $client = Client::FindOrFail($id);
        $group = Group::FindOrFail($groupId);
        $users = $client->users;

        $usersArray = [];
        foreach ($users as $user)
        {
            $usersArray[$user->id] = $user->name . ' (' . $user->email . ')';
        }

		$targetsArray = User::getSelectFormattedArrayForClient($client->id);
		$targetsArray = [0 => 'None'] + $targetsArray;

        return view('dashboard.groups.edit', compact('group', 'client', 'users', 'usersArray', 'groupUsers', 'groupRolesArray', 'targetsArray'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param  \Illuminate\Http\Request $request
     * @param $groupId
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, $groupId)
    {
        $client = Client::FindOrFail($id);
        $group = Group::FindOrFail($groupId);
        $data = $request->all();

        $users = [];
        foreach ($data['user_id'] as $i => $userId)
        {
            if (! $userId) continue;

            $users[$i]['id'] = $userId;
            $users[$i]['position'] = $data['group_position'][$i];
            $users[$i]['leader'] = $data['leader'][$i];
        }

        // Update existing group
        $group->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'users' => $users
        ]);

		// Set the target
		if ($data['target_id'])
			$group->target_id = $data['target_id'];
		else
			$group->target_id = NULL;
		$group->save();

        return redirect('dashboard/clients/'.$client->id.'/groups')->with('success', 'Group '.$group->name.' updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param $groupId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $groupId)
    {
        $client = Client::findOrFail($id);
        $group = Group::findOrFail($groupId);

        $group->delete();

        return redirect('dashboard/clients/'.$client->id.'/groups')->with('success', 'Group '.$group->name.' deleted successfully!');
    }
}
