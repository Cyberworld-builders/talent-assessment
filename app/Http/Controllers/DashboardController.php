<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Assessment;
use App\Assignment;
use App\Client;
use App\DBConnection;
use App\Dimension;
use App\Job;
use App\PredictiveModel;
use App\Question;
use App\Reseller;
use App\User;
use Aws\AwsClient;
use Aws\Rds\RdsClient;
use Aws\S3\S3Client;
use Bican\Roles\Models\Role;
use CanGelis\PDF\PDF;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use League\Flysystem\Adapter\Local;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class DashboardController extends Controller
{
	/**
	 * Dashboard entry point.
	 *
	 * @param Request $request
	 * @return Factory|View
	 */
	public function index(Request $request)
    {
		$user = Auth::user();
		Assessment::updateResellerAssessments();
		Dimension::updateResellerDimensions();

		// Client Dashboard
		if ($user->is('client'))
		{
			$client = Client::findOrFail($user->client_id);
			$client->home = true;

			// Users array to search through
			$usersArray = [];
			foreach ($client->jobUsers() as $clientUser)
				$usersArray[$clientUser->id] = $clientUser->name . ' (' . $clientUser->username . ', ' . $clientUser->email . ')';

			return view('clientdashboard.index', compact('client', 'user', 'usersArray'));
		}

		$answers = Answer::count();
		$clients = Client::count();
		$users = User::count();
		$assessments = Assessment::count();

    	return view('dashboard.index', compact('answers', 'clients', 'users', 'assessments'));
    }

	public function getServerTime()
	{
		return \Response::json(['time' => Carbon::now()]);
    }

	/**
	 * Test function for testing various functionality.
	 */
	public function test()
	{
	    return "Not allowed";

	    echo '<br/><br/>Client time is now: '. Carbon::now() .'<br/><br/>';
		?>
            <meta name="csrf_token" content="<?php echo csrf_token() ?>" />
            <script src="<?php echo asset('assets/js/jquery-1.11.1.min.js') ?>"></script>
			<script>
                jQuery(document).ready(function ($)
                {
                    // Set headers for AJAX
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                        }
                    });

                    var data = {};
                    var url = '/getservertime';

                    $.ajax({
                        type: 'post',
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function (data) {
                            $('body').prepend("Server time is now: "+data.time.date);
                            // console.log(data.time.date);
                        },
                        error: function (data) {
                            //console.log(data.status + ' ' + data.statusText);
                            $('html').prepend(data.responseText);
                        }
                    });
                });
			</script>
		<?php

		return "";

		ini_set('max_execution_time', 300);
		$client = Client::findOrFail(66);
		$users = $client->users->take(10);
		$assessments = [];

		// Get user assessments and assignments
		foreach ($users as $user)
		{
			$userAssessments = [];
			$userAssignments = [];

			foreach ($user->assignments as $assignment)
			{
				// Get the assessments that we'll be reporting on
				if (! in_array($assignment->assessment(), $assessments))
					$assessments[] = $assignment->assessment();

				// Get list of assignment ids for this user
				$userAssessments[] = $assignment->assessment_id;
				$userAssignments[$assignment->assessment_id] = $assignment->id;
			}

			$user->assignmentIds = $userAssignments;
			$user->assignedAssessments = $userAssessments;
		}

		// Track progress
		$total = $users->count() + 4;
		$filename = 'Assessment Data for '.sanitize_string($client->name).' '.Carbon::now();

		$data = Excel::create($filename, function($excel) use ($client, $users, $assessments, $total)
		{
			$excel->setTitle('Assessment Details for '.$client->name);
			$excel->sheet('Details', function($sheet) use ($users, $assessments, $total)
			{
				// Start
				$i = 1;
				$row = [];
				echo $i.': '.number_format(($i / $total) * 100, 2).'%<br/>';

				// Row 1, Assessment Name
				$row = ['Identification', '', '', '', ''];
				foreach ($assessments as $assessment)
				{
					$row[] = $assessment->name;

					if ($assessment->id == get_global('personality'))
						foreach ($assessment->dimensions as $j => $dimension)
						{
							if ($j == 0) continue;
							$row[] = '';
						}
				}
				$sheet->row($i, $row);
				$i++;
				echo $i.': '.number_format(($i / $total) * 100, 2).'%<br/>';

				// Row 2, Dimension Code of each question
				$row = ['UserID', 'Name', 'Email', 'Job Title', 'Job Family'];
				foreach ($assessments as $assessment)
				{
					if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
						$row[] = 'Raw Score';

					if ($assessment->id == get_global('personality'))
					{
						foreach ($assessment->dimensions as $dimension)
							$row[] = $dimension->name;
					}
				}
				$sheet->row($i, $row);
				$i++;
				echo $i.': '.number_format(($i / $total) * 100, 2).'%<br/>';

				// Row 4+, User Rows
				$scoringController = new ScoringController();
				foreach ($users as $user)
				{
					// User Info and Scores
					$row = [$user->username, $user->name, $user->email, $user->job_title, $user->job_family];
					foreach ($assessments as $assessment)
					{
						// If user didn't take this assessment
						$key = array_search($assessment->id, $user->assignedAssessments);
						if ($key === false)
						{
							// Fill with blanks
							if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
								$row[] = '';

							if ($assessment->id == get_global('personality'))
							{
								foreach ($assessment->dimensions as $dimension)
									$row[] = '';
							}
						}

						// If user did take the assessment
						else
						{
							if ($assessment->id == get_global('ability') || $assessment->id == get_global('aptitude') || $assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
							{
								$assignment = Assignment::find($user->assignmentIds[$assessment->id]);
								$row[] = $scoringController->getTotalScore($assignment);
							}

							if ($assessment->id == get_global('ospan') || $assessment->id == get_global('sspan'))
								$row[] = $scoringController->scoreWm($user->assignmentIds[$assessment->id]);

							if ($assessment->id == get_global('personality'))
							{
								foreach ($assessment->dimensions as $dimension)
									$row[] = $scoringController->getScoreForDimension($user->assignmentIds[$assessment->id], $dimension->id);
							}
						}
					}

					$sheet->row($i, $row);
					$i++;
					echo $i.': '.number_format(($i / $total) * 100, 2).'%<br/>';
				}
			});
		});

		dd($data);

		return view('reports.test');
		return 'Not allowed';

		// ------------------------
		// WM Answers Time Analysis
		// ------------------------

		$assignmentIds = [];

		// Find all assignments on WM OSpan
		$assignments = Assignment::where('assessment_id', 15)->get();
		foreach ($assignments as $assignment)
			$assignmentIds[] = $assignment->id;

		// Find all assignments on WM SSpan
		$assignments = Assignment::where('assessment_id', 17)->get();
		foreach ($assignments as $assignment)
			$assignmentIds[] = $assignment->id;

		// Question types
		$questionType = [
			4 => 'Letters',
			5 => 'Math',
			6 => 'Math and Letters',
			7 => 'Squares',
			8 => 'Symmetry',
			9 => 'Symmetry Squares',
		];

		// Build our table
		echo '<style>td { padding: 5px; }</style>';
		echo '<table border="1">
			<thead>
				<td>Assignment ID</td>
				<td>Assessment</td>
				<td>User</td>
				<td>Question #</td>
				<td>Type</td>
				<td>Practice?</td>
				<td>Score</td>
				<td>Possible Score</td>
				<td>Time (Seconds)</td>
			</thead>';
		foreach ($assignmentIds as $id)
		{
			$assignment = Assignment::find($id);
			$assessment = $assignment->assessment();

			$answers = Answer::where('assignment_id', $id)->get();
			foreach ($answers as $answer)
			{
				// Find out time it took to answer
				if ($answer->question->type == 6)
				{
					$val = unserialize($answer->value);
					$time = '';
					foreach ($val['equations'] as $i => $equation)
						$time .= (float)($equation['time'] / 1000) . ' (equation '.($i + 1).')<br/>';
					$time .= (float)($val['letters']['time'] / 1000) . ' (letters)';
				}
				elseif ($answer->question->type == 9)
				{
					$val = unserialize($answer->value);
					$time = '';
					foreach ($val['symmetries'] as $i => $symmetry)
						$time .= (float)($symmetry['time'] / 1000) . ' (symmetry '.($i + 1).')<br/>';
					$time .= (float)($val['squares']['time'] / 1000) . ' (squares)';
				}
				else
					$time = (float)($answer->time / 1000);

				$user = User::find($answer->user_id);
				echo '<tr>';
					echo '<td>' . $id . '</td>';
					echo '<td>' . $assessment->name . '</td>';
					echo '<td>' . $user->name . '</td>';
					echo '<td>' . $answer->question->number . '</td>';
					echo '<td>' . $questionType[$answer->question->type] . '</td>';
					echo '<td>' . ($answer->question->practice ? 'Practice' : '') . '</td>';
					echo '<td>' . $answer->scoreWm() . '</td>';
					echo '<td>' . $answer->possibleWmScore() . '</td>';
					echo '<td>' . $time . '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		// Testing question serialization problems

		$question = Question::findOrFail(346);
		dd(strlen('A.	Prescribe the drug as requested'),
			strlen('B.	Seek advice from another senior colleague at the hospital.'),
			strlen('C.	Do not prescribe the drug and make a note in the patientâ€™s file that the drug would contradict the patientâ€™s existing treatments.'),
			strlen('D.	Try to contact the supervisor to inform him/her of the patientâ€™s existing treatments before prescribing the drug.'));
		dd(strlen('a:4:{i:0;a:2:{s:3:"tag";s:34:"A.	Prescribe the drug as requested";s:5:"value";s:1:"0";}i:1;a:2:{s:3:"tag";s:61:"B.	Seek advice from another senior colleague at the hospital.";s:5:"value";s:1:"0";}i:2;a:2:{s:3:"tag";s:136:"C.	Do not prescribe the drug and make a note in the patientâ€™s file that the drug would contradict the patientâ€™s existing treatments.";s:5:"value";s:1:"0";}i:3;a:2:{s:3:"tag";s:118:"D.	Try to contact the supervisor to inform him/her of the patientâ€™s existing treatments before prescribing the drug.";s:5:"value";s:1:"1";}}'));
		$test = strlen("D.	Try to contact the supervisor to inform him/her of the patient's existing treatments before prescribing the drug.");

		dd($test);

		// ----------------------
		// Sending emails testing
		// ----------------------

		$user = User::FindOrFail(13083);

		Mail::send('welcome', [], function ($m) use ($user)
		{
			$m->from('postmaster@mg.aoescience.com', 'AOE Science');
			$m->to($user->email, $user->name)->subject('Email Test '.Carbon::now());
		});

		return 'Email has been sent to '.$user->name.'. '.Carbon::now();

		// -------------------------
		// Opening Files for Writing
		// -------------------------

		// Note: If you place colons (:) into the filename, it will throw a fopen: Protocol error
		$fileHandle = fopen('/var/www/storage/exports/Selection Overview for Chemical Operator - 2, Evonik, 2017-07-27 20_50_53.csv', 'wb+');
		echo 'asdf';
		fclose($fileHandle);

		// -----------------------
		// Download a PDF from URL
		// -----------------------

		$pdf = new PDF($_SERVER['DOCUMENT_ROOT'].'/../wkhtmltox/bin/wkhtmltopdf');
		$pdf->loadURL('https://my.aoescience.com')->save("asdf.pdf", new Local($_SERVER['DOCUMENT_ROOT'].'/../storage/exports'));

		return 'Exported successfully';

		// -------------------------
		// Upload files to S3 bucket
		// -------------------------

		// Make sure it's a legitimate image first
		$info = getimagesize('uploads/aoe-a.png');
		if ($info === FALSE)
			abort('403', 'Unable to determine image type of uploaded file.');

		// Make sure it's a supported file format
		if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG))
			abort('403', 'Image must be in GIF, JPEG, or PNG format.');

		// Upload the file
		$file = fopen('uploads/aoe-a.png', 'r');
		$s3 = new S3Client(config('aws'));
		$result = $s3->upload('aoe-uploads', 'images/aoe-a.png', $file);

		// Store the URL path to the image in the database
		dd($result->get('ObjectURL'));

		// Test WM scoring
//		$assignment = Assignment::find(8535);
//		$assessment = $assignment->assessment();
//		$answers = $assignment->answers;
//
//		$score = 0;
//		foreach ($answers as $i => $answer)
//		{
//			if ($answer->question->practice)
//				continue;
//
//			$score += $answer->scoreWm();
//		}
//		dd($score);

//		$wmoScore = $s->scoreWm($wmo->id, $job->id);
//		$wmoTotal = $s->getWmTotal($wmo->id);

		// Get database status
//		$rds = new RdsClient(config('aws'));
//		$instance = $rds->describeDBInstances(['DBInstanceIdentifier' => 'laraveldb']);
//		dd($instance['DBInstances'][0]['DBInstanceStatus']);

		// Setup DB password and username
//		$dbUsername = (env('APP_ENV') == 'staging') ? $_SERVER['RDS_USERNAME'] : 'admaster';
//		$dbPassword = (env('APP_ENV') == 'staging') ? $_SERVER['RDS_PASSWORD'] : 'fater1ft';

		// Create the new database
//		$db = $rds->createDBInstance([
//			'DBInstanceClass' => 'db.t2.micro',
//			'DBInstanceIdentifier' => 'aws-test-583049583',
//			'DBName' => 'sometest_database',
//			'Engine' => 'mysql',
//			'MasterUserPassword' => $dbPassword,
//			'MasterUsername' => $dbUsername,
//			'AllocatedStorage' => 5,
//		]);

		// Delete a database from aws
//		$db = $rds->deleteDBInstance([
//			'DBInstanceIdentifier' => 'aws-test-583049583',
//			'SkipFinalSnapshot' => true,
//		]);
//
//		dd($db);

		// Get stuff from database
//		$db = new DBConnection([
//			'host' => 'aws-test-583049583.ci5dyasfrpba.us-west-2.rds.amazonaws.com:3306',
//			'database' => 'sometest_database',
//			'username' => 'admaster',
//			'password' => 'fater1ft',
//		]);

		// Migrate
//		Artisan::call('migrate', [
//			'--database' => $db->getConnection()->getName()
//		]);

		// Seed the database
//		Artisan::call('db:seed', [
//			'--database' => $db->getConnection()->getName(),
//			'--class' => 'DatabaseSeeder'
//		]);

		// Drop any unnecessary tables
//		$db->getConnection()->statement('DROP TABLE permission_role');
//		$db->getConnection()->statement('DROP TABLE permission_user');
//		$db->getConnection()->statement('DROP TABLE permissions');
//		$db->getConnection()->statement('DROP TABLE resellers');
//		$db->getConnection()->statement('DROP TABLE jaqs');
//		$db->getConnection()->statement('DROP TABLE analysis');

		// Get stuff from database
		//$stuff = $db->getConnection()->table('stuff')->get();
		//dd($stuff);

//		$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/test.xml');
//		dd($xml);

		// Test implementation of predictive modeling

		// Grab our predictive model
//		$model = PredictiveModel::first();

		// Get our divisions
//		$divisions = [];
//		foreach ($model->model->DataDictionary->DataField as $field)
//		{
//			if ($field->{'@attributes'}->dataType != 'string' || $field->{'@attributes'}->optype != 'categorical' || !isset($field->Value))
//				continue;
//
//			foreach ($field->Value as $division)
//				$divisions[] = $division->{'@attributes'}->value;
//		}

		// Get our factors (what we will need to get scores for)
//		$factors = [];
//		foreach ($model->model->DataDictionary->DataField as $field)
//		{
//			if ($field->{'@attributes'}->dataType == 'string' || $field->{'@attributes'}->optype == 'categorical')
//				continue;
//
//			$factors[] = [
//				'name' => $field->{'@attributes'}->name,
//				'type' => null,
//				'id' => null,
//			];
//		}

		// Get the scores to the various factors
//		$scores = [
//			'ability' => 42,
//			'OS' => 42,
//			'humility' => 4.0,
//			'agree' => 4.0,
//			'extra' => 4.0,
//			'emotion' => 4.0,
//			'consc' => 4.0,
//			'open' => 4.0,
//		];

		// Store current attributes and distribution
//		$currentNode = $model->model->TreeModel->Node;
//		$attributes = $currentNode->{'@attributes'};
//		$distribution = $currentNode->ScoreDistribution;

		// Go through the nodes
//		$i = 0;
//		while (isset($currentNode->Node))
//		{
//			// For each choice of node
//			foreach ($currentNode->Node as $node)
//			{
//				$pass = false;
//				$field = $node->SimplePredicate->{'@attributes'}->field;
//				$operator = $node->SimplePredicate->{'@attributes'}->operator;
//				$value = $node->SimplePredicate->{'@attributes'}->value;
//				//if ($i == 7) dd($i, $node->SimplePredicate->{'@attributes'});
//
//				// Check if we pass the test
//				switch ($operator)
//				{
//					case 'lessOrEqual':
//						if ($scores[$field] <= $value) $pass = true;
//						break;
//
//					case 'greaterThan':
//						if ($scores[$field] > $value) $pass = true;
//						break;
//				}
//
//				// If so
//				if ($pass)
//				{
//					// Adjust our distribution
//					$attributes = $node->{'@attributes'};
//					$distribution = $node->ScoreDistribution;
//
//					// If next node doesn't exist, stop
//					if (! isset($node->Node))
//						break 2;
//
//					// Continue
//					$currentNode = $node;
//					$i++;
//				}
//			}
//		}

		// Get the stats for the current score from the distribution array
//		$stats = null;
//		foreach ($distribution as $category)
//			if ($category->{'@attributes'}->value == $attributes->score)
//			{
//				$stats = $category->{'@attributes'};
//				break;
//			}
//
//		$score = $attributes->score;
//		$confidence = $stats->confidence * 100 . '%';
//
//		dd($i, $score, $confidence, $attributes, $stats);

		// WM Randomization of questions
		$wm = Assessment::find(15);
		$questions = [];
		$randomize = [];
		$keys = [];

		// When grabbing our questions, randomize all non-practice questions that are complex type
		// (Math letters or Symmetry sequence) that are adjacent to each other only
		foreach ($wm->questions as $i => $question)
		{
			// Ignore the non-complex questions
			if ($question->type != 6 && $question->type != 9)
				continue;

			// Ignore questions that aren't in a series (if next question is of different type)
			if (array_key_exists($i + 1, $wm->questions->toArray()) && $wm->questions[$i + 1]->type != $question->type && $wm->questions[$i - 1]->type != $question->type)
				continue;

			// Add a flag to randomize this question
			$wm->questions[$i]->randomize = true;
			$keys[] = $i;
		}

		// Extract the elements that need to be shuffled into a separate array
		$temp = [];
		foreach ($keys as $i => $key)
			$temp[$i] = $wm->questions[$key];

		// Shuffle them
		shuffle($temp);

		// Place them back
		foreach ($keys as $i => $key)
			$wm->questions[$key] = $temp[$i];

		// Gather which questions are to be randomized
		foreach ($wm->questions as $question)
		{
			if ($question->randomize)
				$randomize[] = 'Randomize';
			else
				$randomize[] = '';
		}

		// Gather our questions so we can visually see what's going on
		foreach ($wm->questions as $question)
		{
			if ($question->type == 10)
				$questions[] = 'Instructions';

			elseif ($question->type == 6)
				$questions[] = implode(', ', json_decode($question->content)->letters);

			elseif ($question->type == 9)
			{
				$squares = '';
				foreach (json_decode($question->content)->squares as $square)
					$squares .= '[]';
				$questions[] = $squares;
			}

			else
				$questions[] = $question->content;
		}

		// See the results
		dd($randomize, $questions);
	}

	public function config(Request $request)
	{
		// Check if we're saving
		if (! empty($request->all()))
		{
			$data = $request->all();

			// Add new option
			if ($data['new_option']['name'])
				\DB::table('globals')->insert([
					'name' => $data['new_option']['name'],
					'value' => $data['new_option']['value'],
				]);

			// Update existing options
			if ($data['options'])
			{
				foreach ($data['options']['id'] as $i => $id)
				{
					$name = $data['options']['name'][$i];
					$value = $data['options']['value'][$i];

					// Delete option
					if ($name == '' && $value == '')
					{
						\DB::table('globals')->where('id', $id)->delete();
						continue;
					}

					// Update option
					\DB::table('globals')->where('id', $id)->update([
						'name' => $name,
						'value' => $value,
					]);
				}
			}

			return redirect()->back()->with('success', 'Global config options updated successfully!');
		}

		$options = \DB::table('globals')->get();

		return view('dashboard.config', compact('options'));
	}

	public function home()
	{
		return redirect('/dashboard');
	}

	public function databases()
	{
		$resellers = Reseller::all();

		foreach ($resellers as $reseller)
			$reseller->db_updated = $reseller->checkDbUpdated();

		return view('dashboard.databases', compact('resellers'));
	}

	/**
	 * Show user account view.
	 */
	public function account()
    {
		$user = Auth::user();

		// Client Dashboard
		if ($user->is('client'))
		{
			$client = Client::findOrFail($user->client_id);

			return view('clientdashboard.account', compact('client', 'user'));
		}

		return 'Coming Soon';
    }

	/**
	 * Retrieve a file from storage.
	 *
	 * @param $file
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function download($file)
	{
		// Check if file exists in app/storage/file folder
		$file_path = storage_path() .'/exports/'. $file;
		if (file_exists($file_path))
		{
			// Send Download
			return \Response::download($file_path, $file, [
				'Content-Length: '. filesize($file_path)
			]);
		}
		else
		{
			// Error
			exit('Requested file does not exist on our server!');
		}
	}

	/**
	 * Check if a user exists.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function check_if_user_exists(Request $request)
	{
		$data = $request->all();
		$user = User::whereUsername($data['username'])->first();

		if (empty($user)) {
			return \Response::json(false);
		}

		return \Response::json($user);
	}

	/**
	 * Check if an applicant exists.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function check_if_applicant_exists(Request $request)
	{
		$data = $request->all();
		$job = Job::find($data['jobId']);
		$user = $job->viableApplicants()->where('username', $data['username'])->first();

		if (empty($user)) {
			return \Response::json(false);
		}

		return \Response::json($user);
	}

	/**
	 * Get user from user id.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getUser(Request $request)
	{
		$data = $request->all();
		$user = User::whereId($data['id'])->first();

		if (empty($user)) {
			return \Response::json(false);
		}

		return \Response::json($user);
	}
}
