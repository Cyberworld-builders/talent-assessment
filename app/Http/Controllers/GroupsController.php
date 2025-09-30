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

        // Get group roles for the client
        $groupRoles = $client->groupRoles;
        $groupRolesArray = [];
        foreach ($groupRoles as $role)
            $groupRolesArray[$role->id] = $role->name;

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
	 * Upload and parse a CSV file of groups.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadGroupsFromCsv($id, Request $request)
	{
		$client = Client::findOrFail($id);
		$data = $request->all();
		$users = [];

		$validator = Validator::make($data, [
			'file' => 'required|mimes:csv,txt'
		]);

		if ($validator->fails())
			return \Response::json(['errors' => 'File must be a valid .csv file format.']);

		$file = $data['file'];
		
		// Debug: Log file information
		\Log::info('Groups CSV Upload Debug', [
			'file_name' => $file->getClientOriginalName(),
			'file_size' => $file->getSize(),
			'mime_type' => $file->getMimeType(),
			'temp_path' => $file->getPathname()
		]);
		
		$handle = fopen($file->getPathname(), 'r');
		
		if ($handle === false) {
			\Log::error('Could not open uploaded groups file');
			return \Response::json(['errors' => 'Could not read the uploaded file.']);
		}

		// Read the header row
		$header = fgetcsv($handle);
		if ($header === false) {
			fclose($handle);
			\Log::error('Could not read header row from groups CSV');
			return \Response::json(['errors' => 'Could not read the header row from the CSV file.']);
		}
		
		// Debug: Log header information
		\Log::info('Groups CSV Header', ['header' => $header]);

		// Process each data row
		$rowCount = 0;
		while (($row = fgetcsv($handle)) !== false) {
			$rowCount++;
			\Log::info("Processing groups row $rowCount", ['row' => $row]);
			
			// Skip empty rows (rows with all empty values)
			if (empty(array_filter($row, function($value) { return !empty(trim($value)); }))) {
				\Log::info("Skipping empty groups row $rowCount");
				continue;
			}
			
			// Create an associative array from header and row data
			$rowData = array_combine($header, $row);
			
			// Handle different CSV formats
			$targetName = '';
			$targetEmail = '';
			$userName = '';
			$userEmail = '';
			$userRole = '';
			
			// Check if this is the standard format (Target Name, Target Email, Name, Email, Role)
			if (isset($rowData['Target Name']) || isset($rowData['target_name'])) {
				$targetName = isset($rowData['Target Name']) ? $rowData['Target Name'] : (isset($rowData['target_name']) ? $rowData['target_name'] : '');
				$targetEmail = isset($rowData['Target Email']) ? trim($rowData['Target Email']) : (isset($rowData['target_email']) ? trim($rowData['target_email']) : '');
				$userName = isset($rowData['Name']) ? $rowData['Name'] : (isset($rowData['name']) ? $rowData['name'] : '');
				$userEmail = isset($rowData['Email']) ? trim($rowData['Email']) : (isset($rowData['email']) ? trim($rowData['email']) : '');
				$userRole = isset($rowData['Role']) ? $rowData['Role'] : (isset($rowData['role']) ? $rowData['role'] : '');
			}
			// Handle alternative column names
			else {
				$targetName = isset($rowData['TargetName']) ? $rowData['TargetName'] : (isset($rowData['targetName']) ? $rowData['targetName'] : '');
				$targetEmail = isset($rowData['TargetEmail']) ? trim($rowData['TargetEmail']) : (isset($rowData['targetEmail']) ? trim($rowData['targetEmail']) : '');
				$userName = isset($rowData['UserName']) ? $rowData['UserName'] : (isset($rowData['userName']) ? $rowData['userName'] : '');
				$userEmail = isset($rowData['UserEmail']) ? trim($rowData['UserEmail']) : (isset($rowData['userEmail']) ? trim($rowData['userEmail']) : '');
				$userRole = isset($rowData['UserRole']) ? $rowData['UserRole'] : (isset($rowData['userRole']) ? $rowData['userRole'] : '');
			}

			// Skip rows that don't have at least user name/email and target name/email
			if ((empty(trim($userName)) && empty(trim($userEmail))) || (empty(trim($targetName)) && empty(trim($targetEmail)))) {
				\Log::info("Skipping groups row $rowCount - missing user or target info");
				continue;
			}

			// Find the user
			$user = null;
			if (!empty(trim($userEmail)) && !empty(trim($userName))) {
				$user = User::where([
					'email' => $userEmail,
					'client_id' => $client->id,
				])->orWhere([
					'name' => $userName,
					'client_id' => $client->id,
				])->first();
			}

			// Find the target
			$target = null;
			if (!empty(trim($targetEmail)) && !empty(trim($targetName))) {
				$target = User::where([
					'email' => $targetEmail,
					'client_id' => $client->id,
				])->orWhere([
					'name' => $targetName,
					'client_id' => $client->id,
				])->first();
			}

			// If we have both user and target, add to results
			if ($user && $target) {
				array_push($users, [
					'id' => $user->id,
					'name' => $user->name,
					'email' => $user->email,
					'role' => $userRole,
					'target_id' => $target->id,
					'target_name' => $target->name,
					'target_email' => $target->email,
				]);
			} else {
				\Log::info("Skipping groups row $rowCount - user or target not found", [
					'user_found' => $user ? true : false,
					'target_found' => $target ? true : false,
					'user_email' => $userEmail,
					'user_name' => $userName,
					'target_email' => $targetEmail,
					'target_name' => $targetName
				]);
			}
		}

		fclose($handle);

		\Log::info('Groups CSV Upload Complete', [
			'total_rows_processed' => $rowCount,
			'users_found' => count($users),
			'users' => $users
		]);

		return \Response::json(['users' => $users]);
	}

	/**
	 * Download a CSV template for groups bulk upload.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function downloadGroupsTemplate()
	{
		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="groups_upload_template.csv"',
		];

		$callback = function() {
			$file = fopen('php://output', 'w');
			
			// Write header row
			fputcsv($file, ['Target Name', 'Target Email', 'Name', 'Email', 'Role']);
			
			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
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

        // Get group roles for the client
        $groupRoles = $client->groupRoles;
        $groupRolesArray = [];
        foreach ($groupRoles as $role)
            $groupRolesArray[$role->id] = $role->name;

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
