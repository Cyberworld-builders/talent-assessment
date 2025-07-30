<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Assignment;
use App\Client;
use App\DBConnection;
use App\ClientReport;
use App\Dimension;
use App\Job;
use App\PredictiveModel;
use App\Report;
use App\Question;
use App\User;
use App\Weight;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Expr\Assign;
use CanGelis\PDF\PDF;
use League\Flysystem\Adapter\Local;
use Psr\Http\Message\RequestInterface;
use Validator;

class ReportsController extends Controller
{
	protected $availableTemplates = [1, 2, 3, 13, 15];

    /**
     * Display a listing of the resource.
     *
     * @param $clientId
     * @param $userId
     * @return \Illuminate\Http\Response
     */

    public function index($clientId, $jobId, $userId, $export = false)
    {
    	// Temporary fix to support the new download system without removing this deprecated function
    	if ($userId == 'download')
			return $this->downloadReport($clientId, $jobId);

		$job = Job::findOrFail($jobId);

		// Sort assessments
		$assessments = [];
		foreach ($job->assessments as $i => $assessmentId)
			$assessments[] = (int)$assessmentId;
		sort($assessments);

		// Find out which report we need
		$reports = [
			'aoep' => [
				(int)get_global('personality'),
			],
			'aoepa' => [
				(int)get_global('ability'),
				(int)get_global('personality'),
			],
			'aoepas' => [
				(int)get_global('ability'),
				(int)get_global('personality'),
				(int)get_global('safety'),
			],
			'apswmo' => [
				(int)get_global('ability'),
				(int)get_global('personality'),
				(int)get_global('safety'),
				(int)get_global('ospan'),
			],
			'pswms' => [
				(int)get_global('personality'),
				(int)get_global('safety'),
				(int)get_global('sspan'),
			],
			'pwms' => [
				(int)get_global('personality'),
				(int)get_global('sspan'),
			],
			'apwms' => [
				(int)get_global('ability'),
				(int)get_global('personality'),
				(int)get_global('sspan'),
			],
			's' => [
				(int)get_global('safety'),
			],
			'devps' => [
				(int)get_global('personality'),
				(int)get_global('safety'),
			],
			'evonik' => [
				(int)get_global('ability'),
				(int)get_global('personality'),
				(int)get_global('aptitude'),
			],
            'evonik2020' => [
                (int)get_global('personality'),
                (int)get_global('evonik-assessment'),
                (int)get_global('reasoning-b'),
            ],
			'cacique' => [
				(int)get_global('leader')
			],
			'ls' => [
				(int)get_global('leader-s')
			],
			'cacique' => [
				(int)get_global('360')
			],
		];
		$report = null;
		foreach ($reports as $name => $reportAssessments)
		{
			if ($assessments == $reportAssessments)
			{
				$report = $name;
				break;
			}
		}

		// Setup report config variable
		$reportConfig = new \stdClass();
		$reportConfig->ability_id = (int)get_global('ability');
		$reportConfig->personality_id = (int)get_global('personality');
		$reportConfig->aptitude_id = (int)get_global('aptitude');
		$reportConfig->sixty_id = (int)get_global('360');
		$reportConfig->safety_id = (int)get_global('safety');
		$reportConfig->wmo_id = (int)get_global('ospan');
		$reportConfig->wms_id = (int)get_global('sspan');
		$reportConfig->evonik_assessment_id = (int)get_global('evonik-assessment');
		$reportConfig->reasoning_b_id = (int)get_global('reasoning-b');

		// Catch errors
		if (! $report)
			return view('error', ['message' => "A report template could not be found for this report. Please contact an AOE Science administrator."]);

		if (! method_exists($this, $report))
			return view('error', ['message' => "Looks like the method for this report has not been configured yet. Please contact an AOE Science administrator."]);

		// Development reports
		if ($report == 'l' || $report == 'ls' || $report == 'cacique')
			return $this->{$report}($jobId, $userId);

		// Selection reports
		return $this->{$report}($jobId, $userId, $reportConfig, $export);
    }

	public function indexDevelopment($id, $assignmentId, $userId)
	{
		$client = Client::findOrFail($id);
		$assignment = Assignment::findOrFail($assignmentId);

		// Find out which report we need
		$reports = [
			(int)get_global('leader') => 'cacique',
			(int)get_global('leader-s') => 'ls',
			(int)get_global('360') => 'sixty',
			(int)get_global('leader-sr') => 'lsr',
		];
		$report = $reports[$assignment->assessment_id];

		// CTCA Specific
		if ($report == 'sixty' && $client->id == 22)
			$report = 'sixtyctca';

		// Catch errors
		if (! $report)
			return view('error', ['message' => "A report template could not be found for this report. Please contact an AOE Science administrator."]);

		if (! method_exists($this, $report))
			return view('error', ['message' => "Looks like the method for this report has not been configured yet. Please contact an AOE Science administrator."]);

		return $this->{$report}($assignmentId, $userId);
    }

    public function caciquetest()
    {
        return view('reports.caciquetest');
    }

    public function cacique($assignmentId, $userId)
    {
		ini_set('max_execution_time', 520);

        $assignment = Assignment::find($assignmentId);
        $user = User::find($userId);
        $s = new ScoringController();

        // Find all completed assignments that pertain to this specific leader
        $assignments = Assignment::where([
            'created_at' => $assignment->created_at,
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

        // Get the ids of these assignments
        $assignmentIds = [];
        foreach ($assignments as $assignment)
            array_push($assignmentIds, $assignment->id);

        // Find all completed assignments that pertain to all other leaders
        $allAssignments = Assignment::where([
            'created_at' => $assignment->created_at,
            'completed' => 1
        ])->get()->filter(function($assignment) use ($user)
        {
            // Filter out users not belonging to this client
            $assignmentUser = User::find($assignment->user_id);
            if ($assignmentUser->client_id != $user->client_id)
                return false;

            // Filter to exclude this user
//            foreach ($assignment->custom_fields['type'] as $i => $field)
//            {
//                if ($field == 'name')
//                {
//                    $name = $assignment->custom_fields['value'][$i];
//                    if ($name == $user->name)
//                        return false;
//                }
//                if ($field == 'email')
//                {
//                    $email = $assignment->custom_fields['value'][$i];
//                    if ($email == $user->email)
//                        return false;
//                }
//            }
            return true;
        });

        // Get the ids of all assignments across all leaders
        $allAssignmentIds = [];
        foreach ($allAssignments as $assignment)
            array_push($allAssignmentIds, $assignment->id);

        // Grab the main scores for this leader and store them in an array.
        // First we get only the subdimension scores.
        // We will later calculate the main dimensions by getting the average of each sub first,
        // then add the (averaged) subs together and get their average, which will be the main average.

        // Power
        $scores['Main']['Communication Empowerment'] = $this->getScoresArray($assignmentIds, 60);
        $scores['Main']['Autonomy'] = $this->getScoresArray($assignmentIds, 61);

        // Information
        $scores['Main']['General'] = $this->getScoresArray($assignmentIds, 54);
        $scores['Main']['Management Communication'] = $this->getScoresArray($assignmentIds, 55);
        $scores['Main']['Feedback'] = $this->getScoresArray($assignmentIds, 56);

        // Rewards
        $scores['Main']['Rewards'] = $this->getScoresArray($assignmentIds, 51);

        // Knowledge
        $scores['Main']['Empowerment'] = $this->getScoresArray($assignmentIds, 57);
        $scores['Main']['Mentoring'] = $this->getScoresArray($assignmentIds, 58);
        $scores['Main']['Acquisition'] = $this->getScoresArray($assignmentIds, 59);

        // Relationships
        $scores['Main']['Conflict Management'] = $this->getScoresArray($assignmentIds, 62);
        $scores['Main']['Teamwork'] = $this->getScoresArray($assignmentIds, 63);
        $scores['Main']['Communication'] = $this->getScoresArray($assignmentIds, 64);
        $scores['Main']['Respect'] = $this->getScoresArray($assignmentIds, 65);

        // Dummy Scores
//        $categories = ['Main', 'Top', 'Average'];
//        $subcats = ['Communication Empowerment', 'Autonomy', 'Power', 'General', 'Management Communication', 'Feedback', 'Information', 'Rewards', 'Empowerment', 'Mentoring', 'Acquisition', 'Knowledge', 'Conflict Management', 'Teamwork', 'Communication', 'Respect', 'Relationships'];
//        foreach ($categories as $cat)
//        {
//            foreach ($subcats as $subcat)
//            {
//                $scores[$cat][$subcat] = rand(100, 500) / 100;
//            }
//        }

        // Grab the scores from all leaders and store them in an array.
        // Again, for the average, we grab the subdimensions only as the main dimensions will be calculated later.
        // But since we're also going to use them for max value, we calculate the max score of the main dimension right
        // away by adding each sub score and averaging it, so we can get the max of those averages.

        // Power
        $scores['All']['Communication Empowerment'] = $this->getScoresArray($allAssignmentIds, 60);
        $scores['All']['Autonomy'] = $this->getScoresArray($allAssignmentIds, 61);
        foreach ($allAssignmentIds as $i => $id)
        {
            $averageScore = ($scores['All']['Communication Empowerment'][$i]
                    + $scores['All']['Autonomy'][$i]) / 2;
            $scores['All']['Power'][$i] = $averageScore;
        }

        // Information
        $scores['All']['General'] = $this->getScoresArray($allAssignmentIds, 54);
        $scores['All']['Management Communication'] = $this->getScoresArray($allAssignmentIds, 55);
        $scores['All']['Feedback'] = $this->getScoresArray($allAssignmentIds, 56);
        foreach ($allAssignmentIds as $i => $id)
        {
            $averageScore = ($scores['All']['General'][$i]
                    + $scores['All']['Management Communication'][$i]
                    + $scores['All']['Feedback'][$i]) / 3;
            $scores['All']['Information'][$i] = $averageScore;
        }

        // Rewards
        $scores['All']['Rewards'] = $this->getScoresArray($allAssignmentIds, 51);

        // Knowledge
        $scores['All']['Empowerment'] = $this->getScoresArray($allAssignmentIds, 57);
        $scores['All']['Mentoring'] = $this->getScoresArray($allAssignmentIds, 58);
        $scores['All']['Acquisition'] = $this->getScoresArray($allAssignmentIds, 59);
        foreach ($allAssignmentIds as $i => $id)
        {
            $averageScore = ($scores['All']['Empowerment'][$i]
                    + $scores['All']['Mentoring'][$i]
                    + $scores['All']['Acquisition'][$i]) / 3;
            $scores['All']['Knowledge'][$i] = $averageScore;
        }

        // Relationships
        $scores['All']['Conflict Management'] = $this->getScoresArray($allAssignmentIds, 62);
        $scores['All']['Teamwork'] = $this->getScoresArray($allAssignmentIds, 63);
        $scores['All']['Communication'] = $this->getScoresArray($allAssignmentIds, 64);
        $scores['All']['Respect'] = $this->getScoresArray($allAssignmentIds, 65);
        foreach ($allAssignmentIds as $i => $id)
        {
            $averageScore = ($scores['All']['Conflict Management'][$i]
                + $scores['All']['Teamwork'][$i]
                + $scores['All']['Communication'][$i]
                + $scores['All']['Respect'][$i]
            ) / 4;
            $scores['All']['Relationships'][$i] = $averageScore;
        }

        // Now get the average of all the main scores from the array of scores for this leader.
        foreach ($scores['Main'] as $dimension => $scoresArray)
            $scores['Main'][$dimension] = array_average($scoresArray);

        // Calculate these main dimensions separately, after their subdimensions have been averaged.
        $scores['Main']['Power'] = ($scores['Main']['Communication Empowerment']
                + $scores['Main']['Autonomy']) / 2;
        $scores['Main']['Information'] = ($scores['Main']['General']
                + $scores['Main']['Management Communication']
                + $scores['Main']['Feedback']) / 3;
        $scores['Main']['Knowledge'] = ($scores['Main']['Empowerment']
                + $scores['Main']['Mentoring']
                + $scores['Main']['Acquisition']) / 3;
        $scores['Main']['Relationships'] = ($scores['Main']['Conflict Management']
                + $scores['Main']['Teamwork']
                + $scores['Main']['Communication']
                + $scores['Main']['Respect']) / 4;

        // Calculate the top scores from the array of all leader scores.
        foreach ($scores['All'] as $dimension => $scoresArray)
            $scores['Top'][$dimension] = max($scoresArray);

        // Calculate the average scores from the array of all leader scores.
        foreach ($scores['All'] as $dimension => $scoresArray)
            $scores['Average'][$dimension] = array_average($scoresArray);

        // Again, calculate these main dimensions separately, after their subdimensions have been averaged.
        $scores['Average']['Power'] = ($scores['Average']['Communication Empowerment']
            + $scores['Average']['Autonomy']) / 2;
        $scores['Average']['Information'] = ($scores['Average']['General']
                + $scores['Average']['Management Communication']
                + $scores['Average']['Feedback']) / 3;
        $scores['Average']['Knowledge'] = ($scores['Average']['Empowerment']
                + $scores['Average']['Mentoring']
                + $scores['Average']['Acquisition']) / 3;
        $scores['Average']['Relationships'] = ($scores['Average']['Conflict Management']
                + $scores['Average']['Teamwork']
                + $scores['Average']['Communication']
                + $scores['Average']['Respect']) / 4;

        // Finally grab the overall score.
        $overallScoresArray = [
            $scores['Main']['Power'],
            $scores['Main']['Information'],
            $scores['Main']['Rewards'],
            $scores['Main']['Knowledge'],
            $scores['Main']['Relationships'],
        ];
        $scores['Overall'] = number_format(array_average($overallScoresArray), 2);

        // Round all the scores.
        foreach ($scores as $category => $dims)
        {
            // Ignore this one, we only used it for temp score storage
            if ($category == 'All' or $category == 'Overall')
                continue;

            foreach ($dims as $dim => $score)
                $scores[$category][$dim] = number_format($score, 2);
        }

        // Get the amount of scorers that rated this specific leader.
        $scores['Scorers'] = $assignments->count();

        // Setup our strengths text
        $strengthsText['Communication Empowerment'] = '<li><u>Communication Empowerment.</u> There were several notable strengths with empowering employees. Employees report that you seek their input, encourage the free exchange of ideas, and value their opinion.</li>';
        $strengthsText['Autonomy'] = '<li><u>Autonomy.</u> Employees appreciate their freedom to make decisions about work and the autonomy to perform their job.</li>';
        $strengthsText['General'] = '<li><u>Information (General):</u> Employees report good communication regarding company mission and goals and providing valuable information for employees to do their job well.</li>';
        $strengthsText['Management Communication'] = '<li><u>Communication to and from upper management:</u> A specific strength is being a conduit between employees and upper management.</li>';
        $strengthsText['Feedback'] = '<li><u>Feedback:</u> Your employees report that you provide performance feedback that is rewarding and informative.</li>';
        $strengthsText['Rewards'] = '<p>Some notable strengths were in the areas of rewarding employees for strong performance and providing rewards that are meaningful. Employees report that you reinforce their good work with rewards that are valued by employees and really care about their development.</p>';
        $strengthsText['Empowerment'] = '<li><u>Knowledge Empowerment:</u> Employees report that you set clear performance goals, provide feedback, and encourage them to evaluate and record their own performance.</li>';
        $strengthsText['Mentoring'] = '<li><u>Mentoring:</u> Employees appreciate the level of one-on-one coaching and mentoring your provide.</li>';
        $strengthsText['Acquisition'] = '<li><u>Training:</u> Your employees are satisfied with their training and development opportunities.</li>';
        $strengthsText['Conflict Management'] = '<li><u>Conflict management.</u> Employees report your demanding accountability and seeking compromise for win-win resolutions to conflict.</li>';
        $strengthsText['Teamwork'] = '<li><u>Teamwork.</u> Employees complimented your ability to create a safe and open environment. Continue to engage employees to adopt a cooperative mindset and value diversity within the team.</li>';
        $strengthsText['Communication'] = '<li><u>Communication.</u> Specifically, employees remarked about your listening attentively, encouraging honest feedback and responding to all communication in a timely manner.</li>';
        $strengthsText['Respect'] = '';

        // Setup our opportunities text
        $opportunitiesText['Communication Empowerment'] = [
            'Title' => 'Communication Empowerment',
            'Description' => '<p>Communication empowerment refers to encouraging the free exchange of ideas, encouraging employee feedback, and fostering employee confidence that their ideas and expertise are valued.</p>',
            'Action Steps' => [
                '<li>Allow team members to add to the conversation. By allowing them to speak first they will not be guided or constrained by your thoughts.</li>',
                '<li>If your employees/ team members are not willing to participate in open forum then have them write down their ideas independently and go around the table/room asking them to read their ideas aloud for the team to discuss.</li>',
                '<li>If an employee is complaining or constantly voicing problems that is frustrating for you, try not to criticize them for speaking up. Explain that you appreciate their engagement and encourage them to find solutions. Perhaps more importantly, be sure to act on those solutions.</li>',
                '<li>Ask for employee opinions and suggestions in project meetings or prior to making decisions. Encouraging all of your team to provide feedback is important but it may also be effective if you call on specific employees if no one speaks up.</li>',
                '<li>If employees are silent, even after asking for their input, prior to a team meeting assign one member the role of "devil\'s advocate" where their primary objective in the meeting is to voice objections.</li>',
                '<li>Empowering employees can be as simple as encouraging others to speak up in meetings, asking for questions, or promoting open discussion on important issues.</li>',
                '<li>In some cases it is difficult to get employees to speak up, especially when they aren\'t used to voicing their opinions. In those instances, assign someone to specifically play "devil\'s advocate" or ask employees to formulate a plan of action. In cases where decisions are out of their hands such as new policy or new strategy from upper management, then ask employees to develop a shared method of execution and compliance.</li>',
            ],
        ];
        $opportunitiesText['Autonomy'] = [
            'Title' => 'Autonomy',
            'Description' => '<p>It is important to allow team members enough freedom to facilitate proper task completion and the authority to act and make decisions to perform their job.</p>',
            'Action Steps' => [
                '<li>Let your team design and execute tasks without interruption. Focus on giving your team an outcome goal and let them make the decisions on how to accomplish it.</li>',
                '<li>In addition to delegating tasks to the team, drive their confidence by confirming they have the power to act.</li>',
                '<li>Resist the urge to micromanage. Even allowing the team members to fail can produce a rich and lasting learning experience.</li>',
                '<li>Employees can develop the knowledge necessary to manage their own work activities. Trust employees to make the important decisions and encourage them to do so.</li>',
                '<li>Clearly communicate your values and your intent regarding the work. Your values will help guide their autonomous decisions and your intent prompts them to make decisions that achieves your objectives without having to consult with you at every step.</li>',
                '<li>For instances when employees report low autonomy, look for tasks to delegate or interview employees to learn which parts of their jobs are the most difficult. Employees on the front line often have more efficient or effective solutions than those imposed by upper management.</li>',
            ],
        ];
        $opportunitiesText['General'] = [
            'Title' => 'Information (General)',
            'Description' => '<p>Items on the information dimension include factors such as communicating frequently, describing a clear mission for the unit, and giving sufficient notice prior to making decisions.</p>',
            'Action Steps' => [
                '<li>For each difficult decision, provide some additional information for <em>why</em> that decision was necessary.</li>',
                '<li>As much as it is allowed, be transparent about what is going on with the business.</li>',
                '<li>Set aside a time each week to update all of your employees via e-mail, text, or message board.</li>',
                '<li>Inform employees about upcoming decisions or changes to give them time to provide input and prepare.</li>',
                '<li>Think of everyone affected by a decision and make an effort to keep them informed.</li>',
                '<li>Use software that automatically updates the workteam on important information (e.g., budget, client deadlines, production). Or make sure everyone has access to this information.</li>',
            ],
        ];
        $opportunitiesText['Management Communication'] = [
            'Title' => 'Communication to and from Upper Management',
            'Description' => '<p>These items include factors such as being a conduit of communication between upper management and the unit/team. That is, clearly communicating what is learned from upper management and in turn relaying concerns to upper management issues communicated by the unit/team.</p>',
            'Action Steps' => [
                '<li>Create daily and/or weekly information meetings. The meetings should be very short (no more than 10 minutes) where you make announcements about information learned from upper management in the days before.</li>',
                '<li>Solicit feedback/concerns from employees that can be related to upper management.</li>',
                '<li>With permission from upper management, share future changes with employees that will affect their jobs.</li>',
                '<li>Make an effort to relate employee suggestions/requests to upper management. Let the employee know their suggestion/request was communicated and be sure to get back to the employee with an answer, even if the answer is \'no.\'</li>',
                '<li>Take care to provide explanations for upper management decisions which often look illogical when taken out of context. </li>',
            ],
        ];
        $opportunitiesText['Feedback'] = [
            'Title' => 'Feedback',
            'Description' => '<p>Feedback behaviors include factors such as defining performance standards, providing corrective feedback in a professional manner, and using performance feedback to effectively motivate employees.</p>',
            'Action Steps' => [
                '<li>Explain performance standards at every performance evaluation meeting.</li>',
                '<li>Get in the habit of providing daily or weekly feedback to employees. This can be one or two comments informally. Make sure most of the feedback is with regard to above average behaviors (positive feedback) with gewer comments devoted to behaviors that do not meet expectations (critical feedback). Most of the comments should be positive.</li>',
                '<li>When correcting employees, carefully model the correct or expected behavior.</li>',
                '<li>Encourage employees to set goals and performance standards. This helps with generating commitment, involvement, and perception that feedback is relevant and motivating.</li>',
                '<li>Use survey data with feedback to provide information to employees, especially with regard to human capital or customer service.</li>',
                '<li>When providing critical feedback, focus only on behaviors and what was observed. Do not discuss anyone\'s character or try to guess "why" someone did not perform up to expectations.</li>',
            ],
        ];
        $opportunitiesText['Rewards'] = [
            'Title' => 'Rewards',
            'Description' => '<p>Upon closer examination, survey items tapping into recognition and rewards for strong performance were below the average. These items include: rewarding individuals for good performance, providing recognition and praise, and providing valuable rewards to employees.</p>',
            'Action Steps' => [
                '<li>Pay close attention to whether you are rewarding for performance. Many managers do not like differential rewards and may reward employees equally, or may give rewards to the loudest employees that continually demand them. Although this may temporarily quiet the most outspoken employees, it hurts in the long term. Employees know who the strongest performers are and if rewards are not allocated according to the rank order of performance, everyone notices. You risk losing your best employees and demotivating many others that stay.</li>',
                '<li>Rewards do not always have to be monetary (e.g., raises, bonuses) and can be simple recognition for a job well done.</li>',
                '<li>The rewards employees’ value are quite varied and depend on the situation. Some employees enjoy being recognized publicly (e.g., employee of the month) and some prefer to be recognized privately (e.g., a memo, office visit with personal thanks). Other examples of small rewards are valued parking spaces, star rewards, gift cards, gas cards, movie cards, parties or picnics.</li>',
                '<li>Consider rewarding with skill-based pay. That is, employees focused on training and development should be rewarded for their efforts since they will impact the bottom line.</li>',
                '<li>Focus feedback and mentoring on the positive aspects of performance. Many managers are only reminded to provide feedback for negative performance. Recognition for positive performance is a powerful motivating reward.</li>',
            ],
        ];
        $opportunitiesText['Empowerment'] = [
            'Title' => 'Knowledge Empowerment',
            'Description' => '<p>Knowledge empowerment behaviors include factors such as teaching team members how to track and evaluate their own performance, seek new development opportunities, or helping them set realistic performance goals.</p>',
            'Action Steps' => [
                '<li>Focus on assisting in overall self-awareness for team member\'s performance. Perhaps even supply team members with a template for them to assess and track their own performance.</li>',
                '<li>After establishing performance standards and training employees how to use them, work on setting realistic performance goals.</li>',
                '<li>Create training and development plans with team members and emphasize the importance of the team member taking the lead on self-development.</li>',
                '<li>Share with employees some of the economic positions of the department and the organization as a whole. This helps employees to keep score and continually add value.</li>',
                '<li>Set a good example by showing your behaviors and actions are driven by your own performance goals.</li>',
                '<li>Explain to employees how performance is measured and rewarded. The key challenge is to do a good job clearly defining performance standards. Many performance appraisal systems list vague performance objectives (i.e., “customer service” rated on a 3-point scale) and these are not very helpful. Instead, clearly define behaviors expected on the job (e.g.,, smile at customers, listen attentively, follow up on their requests, return phone calls and emails in a timely manner).</li>',
                '<li>Teach employees how to score their own performance. With good performance metrics, employees can monitor their own performance which helps with motivation and accountability.</li>'
            ],
        ];
        $opportunitiesText['Mentoring'] = [
            'Title' => 'Mentoring',
            'Description' => '<p>Sufficient one-on-one time with employees allows for the development of a relationship which fosters goal-setting and reviewing team member performance.</p>',
            'Action Steps' => [
                '<li>Informal mentorships can be just as effective as formal mentorships. Get to know your employees and take a personal interest in them and their career.</li>',
                '<li>Prioritize one-on-one interactions with team members to learn more about their career goals and needs.</li>',
                '<li>Work with team members weekly (or at least monthly) to set process goals and track progress.</li>',
                '<li>Take time to coach employees during project meetings, especially with regard to their specific roles and responsibilities. This is a good opportunity to share your own knowledge, skill, and expertise.</li>',
                '<li>Demonstrate a positive attitude and act as a positive role model. By showing what it takes to be productive and successful, you are demonstrating the behaviors and actions necessary to succeed.</li>',
                '<li>Improve formal and informal mentoring by spending 1-on-1 time with employees, supporting their career aspirations, helping them set goals, and/or seeking and providing support for training opportunities.</li>',
            ],
        ];
        $opportunitiesText['Acquisition'] = [
            'Title' => 'Knowledge Acquisition',
            'Description' => '<p>Employees seek training opportunities and/or support for training.</p>',
            'Action Steps' => [
                '<li>Seek more training and development programs for team members.</li>',
                '<li>Challenge team members to research and find additional training opportunities for themselves.</li>',
                '<li>Communicate to team members the process for submitting requests for training.</li>',
                '<li>Add a section to performance evaluations where employees can list the training they received. Be sure to acknowledge that their efforts for improvement are valued.</li>',
                '<li>Work to generate a strong climate for learning within the department. Seek out opportunities for your own development and share what you have learned so that you set the tone.</li>'
            ],
        ];
        $opportunitiesText['Conflict Management'] = [
            'Title' => 'Conflict Management',
            'Description' => '<p>Acting as a mediator to settle conflicts is often required of leaders. Focus on holding other accountable for their actions in a constructive way and search for win-win solutions to conflict. It is not always the case that one party has to win and the other has to lose.</p>',
            'Action Steps' => [
                '<p>Hold team members accountable by ensuring members understand expectations when dealing with issues.</p>',
                '<li>Train team members on how to address conflict and hold each other accountable. Encourage them to devise a system that achieves a win-win resolution.</li>',
                '<li>In terms of managing conflict, there are several steps to holding others accountable. First, pay attention to expectation-performance gaps. If someone is not behaving in a way that is expected, do not ignore it but do not jump to conclusions about who is at fault. Remember - the key to managing confrontations is not to find blame but to resolve the issue and hold others accountable. In the case of an argument, talk with each party face-to-face and show a sincerity to understand both sides. Focus on behaviors and what is preventing someone from delivering.</li>',
                '<li>In many instances it is important to give employees room to handle their own conflict. Debrief with each party separately to coach them.</li>',
                '<li>Resist the urge to always step in and dictate a solution just to make the problem go away. Try to facilitate harmony in the group by being a neutral observer and let them handle conflict.</li>'
            ],
        ];
        $opportunitiesText['Teamwork'] = [
            'Title' => 'Teamwork',
            'Description' => '<p>Create a safe and open environment for others work cooperatively. Ensure team members always treat others with respect and value the diversity of skills, experience, and background.</p>',
            'Action Steps' => [
                '<li>Hold regular team meetings and allow team members to describe tasks and how to complete them.</li>',
                '<li>Set a code of conduct with the team that emphasizes respect and a safe and open environment.</li>',
                '<li>Highlight to the team the value in having diverse and different opinions in task completion and in the general work environment.</li>',
                '<li>Engage everyone on the team to work cooperatively. Delegate tasks to those who are not engaged and look for team members who might be dominating the conversation and thereby shutting others out. Remind your team that you value the skills and special talents of each one.</li>',
                '<li>To encourage teamwork, outcomes must be rewarded and recognized at the team level.</li>'
            ],
        ];
        $opportunitiesText['Communication'] = [
            'Title' => 'Communication',
            'Description' => '<p>Communicating regularly with employees and in particularly, listening to others.</p>',
            'Action Steps' => [
                '<li>Spend time listening to team members on a one-on-one basis and provide feedback to what you heard. Be open and request feedback. If the team members are reluctant to provide feedback then request that each writes down their specific suggestion.</li>',
                '<li>Set time each day to address team members’ communication.</li>',
                '<li>Effective communication entails first being a good listener so practice allowing team members to speak first and even lead team meetings.</li>',
                '<li>Visit team members regularly to encourage their feedback and input and do this in a way that is nonjudgmental.</li>',
                '<li>To get others engaged it might help to specifically assign employees tasks so they feel part of the team. Everyone has talents and interests they care about and tapping into these will help engage the unmotivated employees.</li>'
            ],
        ];
        $opportunitiesText['Respect'] = [
            'Title' => 'Respect',
            'Description' => '<p>These items include factors such as showing genuine concern, and listening and valuing the perspective of all team members.</p>',
            'Action Steps' => [
                '<li>Show respect to the team through personal communication. This is particularly important with larger groups. Know everyone\'s name and take the time to understand their background and family.</li>',
                '<li>Ask questions of team members on their career paths as this will foster mutual respect.</li>',
                '<li>Value the opinions and initiatives of others. Show appreciation for ongoing efforts of the team members and empower them through positive feedback and reinforcement.</li>'
            ],
        ];

        // Specify dimension parents
        $parent = [
            'Communication Empowerment' => 'Power',
            'Autonomy' => 'Power',
            'General' => 'Information',
            'Management Communication' => 'Information',
            'Feedback' => 'Information',
            'Rewards' => 'Rewards',
            'Empowerment' => 'Knowledge',
            'Mentoring' => 'Knowledge',
            'Acquisition' => 'Knowledge',
            'Conflict Management' => 'Relationships',
            'Teamwork' => 'Relationships',
            'Communication' => 'Relationships',
            'Respect' => 'Relationships',
        ];

        // Calculate the strengths for the report
        $strengths['Power'] = [];
        $strengths['Information'] = [];
        $strengths['Rewards'] = [];
        $strengths['Knowledge'] = [];
        $strengths['Relationships'] = [];
        foreach ($parent as $dimension => $parentDimension)
        {
            if ($scores['Main'][$dimension] >= $scores['Average'][$dimension])
                array_push($strengths[$parentDimension], $strengthsText[$dimension]);
        }

        // Calculate the opportunities for the report
        $opportunities['Power'] = [];
        $opportunities['Information'] = [];
        $opportunities['Rewards'] = [];
        $opportunities['Knowledge'] = [];
        $opportunities['Relationships'] = [];
        foreach ($parent as $dimension => $parentDimension)
        {
            if ($scores['Main'][$dimension] < $scores['Average'][$dimension])
            {
                shuffle($opportunitiesText[$dimension]['Action Steps']);
                array_push($opportunities[$parentDimension], $opportunitiesText[$dimension]);
            }
        }

        // Keep track of page numbers
		$page = 1;

        return view('reports.cacique', compact('job', 'user', 'scores', 'strengths', 'opportunities', 'page'));
    }

	public function ls($assignmentId, $userId)
	{
		ini_set('max_execution_time', 520);
		
		$assignment = Assignment::find($assignmentId);
		$user = User::find($userId);
		$s = new ScoringController();

		// Find all completed assignments that pertain to this specific leader
		$assignments = Assignment::where([
			'created_at' => $assignment->created_at,
			'completed' => 1
		])->get()->filter(function($assignment) use ($user)
		{
			if (! $assignment->custom_fields)
				return false;

			// Filter these to make sure that this assignment was rating this specific user
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

		// Get the ids of these assignments
		$assignmentIds = [];
		foreach ($assignments as $assignment)
			array_push($assignmentIds, $assignment->id);

		// Find all completed assignments that pertain to all other leaders
		$allAssignments = Assignment::where([
			'created_at' => $assignment->created_at,
			'completed' => 1
		])->get()->filter(function($assignment) use ($user)
		{
			// Filter out users not belonging to this client
			$assignmentUser = User::find($assignment->user_id);
			if ($assignmentUser->client_id != $user->client_id)
				return false;

			// Filter to exclude this user
//            foreach ($assignment->custom_fields['type'] as $i => $field)
//            {
//                if ($field == 'name')
//                {
//                    $name = $assignment->custom_fields['value'][$i];
//                    if ($name == $user->name)
//                        return false;
//                }
//                if ($field == 'email')
//                {
//                    $email = $assignment->custom_fields['value'][$i];
//                    if ($email == $user->email)
//                        return false;
//                }
//            }
			return true;
		});

		// Get the ids of all assignments across all leaders
		$allAssignmentIds = [];
		foreach ($allAssignments as $assignment)
			array_push($allAssignmentIds, $assignment->id);

		// Grab the main scores for this leader and store them in an array.
		// First we get only the subdimension scores.
		// We will later calculate the main dimensions by getting the average of each sub first,
		// then add the (averaged) subs together and get their average, which will be the main average.

		// Power
		$scores['Main']['Communication Empowerment'] = $this->getScoresArray($assignmentIds, 60);
		$scores['Main']['Autonomy'] = $this->getScoresArray($assignmentIds, 61);

		// Information
		$scores['Main']['General'] = $this->getScoresArray($assignmentIds, 54);
		$scores['Main']['Management Communication'] = $this->getScoresArray($assignmentIds, 55);
		$scores['Main']['Feedback'] = $this->getScoresArray($assignmentIds, 56);

		// Rewards
		$scores['Main']['Rewards'] = $this->getScoresArray($assignmentIds, 51);

		// Knowledge
		$scores['Main']['Empowerment'] = $this->getScoresArray($assignmentIds, 57);
		$scores['Main']['Mentoring'] = $this->getScoresArray($assignmentIds, 58);
		$scores['Main']['Acquisition'] = $this->getScoresArray($assignmentIds, 59);

		// Relationships
		$scores['Main']['Conflict Management'] = $this->getScoresArray($assignmentIds, 62);
		$scores['Main']['Teamwork'] = $this->getScoresArray($assignmentIds, 63);
		$scores['Main']['Communication'] = $this->getScoresArray($assignmentIds, 64);
		$scores['Main']['Respect'] = $this->getScoresArray($assignmentIds, 65);

		// Dummy Scores
//        $categories = ['Main', 'Top', 'Average'];
//        $subcats = ['Communication Empowerment', 'Autonomy', 'Power', 'General', 'Management Communication', 'Feedback', 'Information', 'Rewards', 'Empowerment', 'Mentoring', 'Acquisition', 'Knowledge', 'Conflict Management', 'Teamwork', 'Communication', 'Respect', 'Relationships'];
//        foreach ($categories as $cat)
//        {
//            foreach ($subcats as $subcat)
//            {
//                $scores[$cat][$subcat] = rand(100, 500) / 100;
//            }
//        }

		// Grab the scores from all leaders and store them in an array.
		// Again, for the average, we grab the subdimensions only as the main dimensions will be calculated later.
		// But since we're also going to use them for max value, we calculate the max score of the main dimension right
		// away by adding each sub score and averaging it, so we can get the max of those averages.

		// Power
		$scores['All']['Communication Empowerment'] = $this->getScoresArray($allAssignmentIds, 60);
		$scores['All']['Autonomy'] = $this->getScoresArray($allAssignmentIds, 61);
		foreach ($allAssignmentIds as $i => $id)
		{
			$averageScore = ($scores['All']['Communication Empowerment'][$i]
					+ $scores['All']['Autonomy'][$i]) / 2;
			$scores['All']['Power'][$i] = $averageScore;
		}

		// Information
		$scores['All']['General'] = $this->getScoresArray($allAssignmentIds, 54);
		$scores['All']['Management Communication'] = $this->getScoresArray($allAssignmentIds, 55);
		$scores['All']['Feedback'] = $this->getScoresArray($allAssignmentIds, 56);
		foreach ($allAssignmentIds as $i => $id)
		{
			$averageScore = ($scores['All']['General'][$i]
					+ $scores['All']['Management Communication'][$i]
					+ $scores['All']['Feedback'][$i]) / 3;
			$scores['All']['Information'][$i] = $averageScore;
		}

		// Rewards
		$scores['All']['Rewards'] = $this->getScoresArray($allAssignmentIds, 51);

		// Knowledge
		$scores['All']['Empowerment'] = $this->getScoresArray($allAssignmentIds, 57);
		$scores['All']['Mentoring'] = $this->getScoresArray($allAssignmentIds, 58);
		$scores['All']['Acquisition'] = $this->getScoresArray($allAssignmentIds, 59);
		foreach ($allAssignmentIds as $i => $id)
		{
			$averageScore = ($scores['All']['Empowerment'][$i]
					+ $scores['All']['Mentoring'][$i]
					+ $scores['All']['Acquisition'][$i]) / 3;
			$scores['All']['Knowledge'][$i] = $averageScore;
		}

		// Relationships
		$scores['All']['Conflict Management'] = $this->getScoresArray($allAssignmentIds, 62);
		$scores['All']['Teamwork'] = $this->getScoresArray($allAssignmentIds, 63);
		$scores['All']['Communication'] = $this->getScoresArray($allAssignmentIds, 64);
		$scores['All']['Respect'] = $this->getScoresArray($allAssignmentIds, 65);
		foreach ($allAssignmentIds as $i => $id)
		{
			$averageScore = ($scores['All']['Conflict Management'][$i]
					+ $scores['All']['Teamwork'][$i]
					+ $scores['All']['Communication'][$i]
					+ $scores['All']['Respect'][$i]
				) / 4;
			$scores['All']['Relationships'][$i] = $averageScore;
		}

		// Now get the average of all the main scores from the array of scores for this leader.
		foreach ($scores['Main'] as $dimension => $scoresArray)
			$scores['Main'][$dimension] = array_average($scoresArray);

		// Calculate these main dimensions separately, after their subdimensions have been averaged.
		$scores['Main']['Power'] = ($scores['Main']['Communication Empowerment']
				+ $scores['Main']['Autonomy']) / 2;
		$scores['Main']['Information'] = ($scores['Main']['General']
				+ $scores['Main']['Management Communication']
				+ $scores['Main']['Feedback']) / 3;
		$scores['Main']['Knowledge'] = ($scores['Main']['Empowerment']
				+ $scores['Main']['Mentoring']
				+ $scores['Main']['Acquisition']) / 3;
		$scores['Main']['Relationships'] = ($scores['Main']['Conflict Management']
				+ $scores['Main']['Teamwork']
				+ $scores['Main']['Communication']
				+ $scores['Main']['Respect']) / 4;

		// Calculate the top scores from the array of all leader scores.
		foreach ($scores['All'] as $dimension => $scoresArray)
			$scores['Top'][$dimension] = max($scoresArray);

		// Calculate the average scores from the array of all leader scores.
		foreach ($scores['All'] as $dimension => $scoresArray)
			$scores['Average'][$dimension] = array_average($scoresArray);

		// Again, calculate these main dimensions separately, after their subdimensions have been averaged.
		$scores['Average']['Power'] = ($scores['Average']['Communication Empowerment']
				+ $scores['Average']['Autonomy']) / 2;
		$scores['Average']['Information'] = ($scores['Average']['General']
				+ $scores['Average']['Management Communication']
				+ $scores['Average']['Feedback']) / 3;
		$scores['Average']['Knowledge'] = ($scores['Average']['Empowerment']
				+ $scores['Average']['Mentoring']
				+ $scores['Average']['Acquisition']) / 3;
		$scores['Average']['Relationships'] = ($scores['Average']['Conflict Management']
				+ $scores['Average']['Teamwork']
				+ $scores['Average']['Communication']
				+ $scores['Average']['Respect']) / 4;

		// Finally grab the overall score.
		$overallScoresArray = [
			$scores['Main']['Power'],
			$scores['Main']['Information'],
			$scores['Main']['Rewards'],
			$scores['Main']['Knowledge'],
			$scores['Main']['Relationships'],
		];
		$scores['Overall'] = number_format(array_average($overallScoresArray), 2);

		// Round all the scores.
		foreach ($scores as $category => $dims)
		{
			// Ignore this one, we only used it for temp score storage
			if ($category == 'All' or $category == 'Overall')
				continue;

			foreach ($dims as $dim => $score)
				$scores[$category][$dim] = number_format($score, 2);
		}

		// Get the amount of scorers that rated this specific leader.
		$scores['Scorers'] = $assignments->count();

		// Specify dimension parents
		$parent = [
			'Communication Empowerment' => 'Power',
			'Autonomy' => 'Power',
			'General' => 'Information',
			'Management Communication' => 'Information',
			'Feedback' => 'Information',
			'Rewards' => 'Rewards',
			'Empowerment' => 'Knowledge',
			'Mentoring' => 'Knowledge',
			'Acquisition' => 'Knowledge',
			'Conflict Management' => 'Relationships',
			'Teamwork' => 'Relationships',
			'Communication' => 'Relationships',
			'Respect' => 'Relationships',
		];

		// Keep track of page numbers
		$page = 1;

		return view('reports.ls', compact('job', 'user', 'scores', 'strengths', 'opportunities', 'page'));
	}

	public function lsr($assignmentId, $userId)
	{
		ini_set('max_execution_time', 520);
		$user = User::find($userId);

		$reportData = null;
		$reportData = DB::table('report_data')->where([
			'user_id' => $userId,
			'assignment_id' => $assignmentId
		])->value('score');

		if (! $reportData)
		{
			$assignment = Assignment::find($assignmentId);
			$s = new ScoringController();

			// Find all completed assignments that pertain to this specific leader
			$assignments = Assignment::where([
				'created_at' => $assignment->created_at,
				'completed'  => 1
			])->get()->filter(function ($assignment) use ($user) {
				// Filter these to make sure that this assignment was rating this specific user
				if (!$assignment->custom_fields)
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

			// Get the ids of these assignments
			$assignmentIds = [];
			foreach ($assignments as $assignment)
				array_push($assignmentIds, $assignment->id);

			// Find all completed assignments that pertain to all other leaders
			$allAssignments = Assignment::where([
				'created_at' => $assignment->created_at,
				'completed'  => 1
			])->get()->filter(function ($assignment) use ($user) {
				// Filter out users not belonging to this client
				$assignmentUser = User::find($assignment->user_id);
				if ($assignmentUser->client_id != $user->client_id)
					return false;

				// Filter to exclude this user
				//            foreach ($assignment->custom_fields['type'] as $i => $field)
				//            {
				//                if ($field == 'name')
				//                {
				//                    $name = $assignment->custom_fields['value'][$i];
				//                    if ($name == $user->name)
				//                        return false;
				//                }
				//                if ($field == 'email')
				//                {
				//                    $email = $assignment->custom_fields['value'][$i];
				//                    if ($email == $user->email)
				//                        return false;
				//                }
				//            }
				return true;
			});

			// Get the ids of all assignments across all leaders
			$allAssignmentIds = [];
			foreach ($allAssignments as $assignment)
				array_push($allAssignmentIds, $assignment->id);

			// Grab the main scores for this leader and store them in an array.
			// First we get only the subdimension scores.
			// We will later calculate the main dimensions by getting the average of each sub first,
			// then add the (averaged) subs together and get their average, which will be the main average.

			// Power
			$scores['Main']['Communication Empowerment'] = $this->getScoresArray($assignmentIds, 60);
			$scores['Main']['Autonomy'] = $this->getScoresArray($assignmentIds, 61);

			// Information
			$scores['Main']['General'] = $this->getScoresArray($assignmentIds, 54);
			$scores['Main']['Management Communication'] = $this->getScoresArray($assignmentIds, 55);
			$scores['Main']['Feedback'] = $this->getScoresArray($assignmentIds, 56);

			// Rewards
			$scores['Main']['Rewards'] = $this->getScoresArray($assignmentIds, 51);

			// Knowledge
			$scores['Main']['Empowerment'] = $this->getScoresArray($assignmentIds, 57);
			$scores['Main']['Mentoring'] = $this->getScoresArray($assignmentIds, 58);
			$scores['Main']['Acquisition'] = $this->getScoresArray($assignmentIds, 59);

			// Relationships
			$scores['Main']['Conflict Management'] = $this->getScoresArray($assignmentIds, 62);
			$scores['Main']['Teamwork'] = $this->getScoresArray($assignmentIds, 63);
			$scores['Main']['Communication'] = $this->getScoresArray($assignmentIds, 64);
			$scores['Main']['Respect'] = $this->getScoresArray($assignmentIds, 65);

			// Dummy Scores
			//        $categories = ['Main', 'Top', 'Average'];
			//        $subcats = ['Communication Empowerment', 'Autonomy', 'Power', 'General', 'Management Communication', 'Feedback', 'Information', 'Rewards', 'Empowerment', 'Mentoring', 'Acquisition', 'Knowledge', 'Conflict Management', 'Teamwork', 'Communication', 'Respect', 'Relationships'];
			//        foreach ($categories as $cat)
			//        {
			//            foreach ($subcats as $subcat)
			//            {
			//                $scores[$cat][$subcat] = rand(100, 500) / 100;
			//            }
			//        }

			// Grab the scores from all leaders and store them in an array.
			// Again, for the average, we grab the subdimensions only as the main dimensions will be calculated later.
			// But since we're also going to use them for max value, we calculate the max score of the main dimension right
			// away by adding each sub score and averaging it, so we can get the max of those averages.

			// Power
			$scores['All']['Communication Empowerment'] = $this->getScoresArray($allAssignmentIds, 60);
			$scores['All']['Autonomy'] = $this->getScoresArray($allAssignmentIds, 61);
			foreach ($allAssignmentIds as $i => $id)
			{
				$averageScore = ($scores['All']['Communication Empowerment'][$i]
						+ $scores['All']['Autonomy'][$i]) / 2;
				$scores['All']['Power'][$i] = $averageScore;
			}

			// Information
			$scores['All']['General'] = $this->getScoresArray($allAssignmentIds, 54);
			$scores['All']['Management Communication'] = $this->getScoresArray($allAssignmentIds, 55);
			$scores['All']['Feedback'] = $this->getScoresArray($allAssignmentIds, 56);
			foreach ($allAssignmentIds as $i => $id)
			{
				$averageScore = ($scores['All']['General'][$i]
						+ $scores['All']['Management Communication'][$i]
						+ $scores['All']['Feedback'][$i]) / 3;
				$scores['All']['Information'][$i] = $averageScore;
			}

			// Rewards
			$scores['All']['Rewards'] = $this->getScoresArray($allAssignmentIds, 51);

			// Knowledge
			$scores['All']['Empowerment'] = $this->getScoresArray($allAssignmentIds, 57);
			$scores['All']['Mentoring'] = $this->getScoresArray($allAssignmentIds, 58);
			$scores['All']['Acquisition'] = $this->getScoresArray($allAssignmentIds, 59);
			foreach ($allAssignmentIds as $i => $id)
			{
				$averageScore = ($scores['All']['Empowerment'][$i]
						+ $scores['All']['Mentoring'][$i]
						+ $scores['All']['Acquisition'][$i]) / 3;
				$scores['All']['Knowledge'][$i] = $averageScore;
			}

			// Relationships
			$scores['All']['Conflict Management'] = $this->getScoresArray($allAssignmentIds, 62);
			$scores['All']['Teamwork'] = $this->getScoresArray($allAssignmentIds, 63);
			$scores['All']['Communication'] = $this->getScoresArray($allAssignmentIds, 64);
			$scores['All']['Respect'] = $this->getScoresArray($allAssignmentIds, 65);
			foreach ($allAssignmentIds as $i => $id)
			{
				$averageScore = ($scores['All']['Conflict Management'][$i]
						+ $scores['All']['Teamwork'][$i]
						+ $scores['All']['Communication'][$i]
						+ $scores['All']['Respect'][$i]
					) / 4;
				$scores['All']['Relationships'][$i] = $averageScore;
			}

			// Now get the average of all the main scores from the array of scores for this leader.
			foreach ($scores['Main'] as $dimension => $scoresArray)
				$scores['Main'][$dimension] = array_average($scoresArray);

			// Calculate these main dimensions separately, after their subdimensions have been averaged.
			$scores['Main']['Power'] = ($scores['Main']['Communication Empowerment']
					+ $scores['Main']['Autonomy']) / 2;
			$scores['Main']['Information'] = ($scores['Main']['General']
					+ $scores['Main']['Management Communication']
					+ $scores['Main']['Feedback']) / 3;
			$scores['Main']['Knowledge'] = ($scores['Main']['Empowerment']
					+ $scores['Main']['Mentoring']
					+ $scores['Main']['Acquisition']) / 3;
			$scores['Main']['Relationships'] = ($scores['Main']['Conflict Management']
					+ $scores['Main']['Teamwork']
					+ $scores['Main']['Communication']
					+ $scores['Main']['Respect']) / 4;

			// Calculate the top scores from the array of all leader scores.
			foreach ($scores['All'] as $dimension => $scoresArray)
				$scores['Top'][$dimension] = max($scoresArray);

			// Calculate the average scores from the array of all leader scores.
			foreach ($scores['All'] as $dimension => $scoresArray)
				$scores['Average'][$dimension] = array_average($scoresArray);

			// Again, calculate these main dimensions separately, after their subdimensions have been averaged.
			$scores['Average']['Power'] = ($scores['Average']['Communication Empowerment']
					+ $scores['Average']['Autonomy']) / 2;
			$scores['Average']['Information'] = ($scores['Average']['General']
					+ $scores['Average']['Management Communication']
					+ $scores['Average']['Feedback']) / 3;
			$scores['Average']['Knowledge'] = ($scores['Average']['Empowerment']
					+ $scores['Average']['Mentoring']
					+ $scores['Average']['Acquisition']) / 3;
			$scores['Average']['Relationships'] = ($scores['Average']['Conflict Management']
					+ $scores['Average']['Teamwork']
					+ $scores['Average']['Communication']
					+ $scores['Average']['Respect']) / 4;

			// Finally grab the overall score.
			$overallScoresArray = [
				$scores['Main']['Power'],
				$scores['Main']['Information'],
				$scores['Main']['Rewards'],
				$scores['Main']['Knowledge'],
				$scores['Main']['Relationships'],
			];
			$scores['Overall'] = number_format(array_average($overallScoresArray), 2);

			// Round all the scores.
			foreach ($scores as $category => $dims)
			{
				// Ignore this one, we only used it for temp score storage
				if ($category == 'All' or $category == 'Overall')
					continue;

				foreach ($dims as $dim => $score)
					$scores[$category][$dim] = number_format($score, 2);
			}

			// Get the amount of scorers that rated this specific leader.
			$scores['Scorers'] = $assignments->count();

			// Setup our strengths text
			$strengthsText['Communication Empowerment'] = '<li><u>Communication Empowerment.</u> There were several notable strengths with empowering employees. Employees report that you seek their input, encourage the free exchange of ideas, and value their opinion.</li>';
			$strengthsText['Autonomy'] = '<li><u>Autonomy.</u> Employees appreciate their freedom to make decisions about work and the autonomy to perform their job.</li>';
			$strengthsText['General'] = '<li><u>Information (General):</u> Employees report good communication regarding company mission and goals and providing valuable information for employees to do their job well.</li>';
			$strengthsText['Management Communication'] = '<li><u>Communication to and from upper management:</u> A specific strength is being a conduit between employees and upper management.</li>';
			$strengthsText['Feedback'] = '<li><u>Feedback:</u> Your employees report that you provide performance feedback that is rewarding and informative.</li>';
			$strengthsText['Rewards'] = '<p>Some notable strengths were in the areas of rewarding employees for strong performance and providing rewards that are meaningful. Employees report that you reinforce their good work with rewards that are valued by employees and really care about their development.</p>';
			$strengthsText['Empowerment'] = '<li><u>Knowledge Empowerment:</u> Employees report that you set clear performance goals, provide feedback, and encourage them to evaluate and record their own performance.</li>';
			$strengthsText['Mentoring'] = '<li><u>Mentoring:</u> Employees appreciate the level of one-on-one coaching and mentoring your provide.</li>';
			$strengthsText['Acquisition'] = '<li><u>Training:</u> Your employees are satisfied with their training and development opportunities.</li>';
			$strengthsText['Conflict Management'] = '<li><u>Conflict management.</u> Employees report your demanding accountability and seeking compromise for win-win resolutions to conflict.</li>';
			$strengthsText['Teamwork'] = '<li><u>Teamwork.</u> Employees complimented your ability to create a safe and open environment. Continue to engage employees to adopt a cooperative mindset and value diversity within the team.</li>';
			$strengthsText['Communication'] = '<li><u>Communication.</u> Specifically, employees remarked about your listening attentively, encouraging honest feedback and responding to all communication in a timely manner.</li>';
			$strengthsText['Respect'] = '';

			// Setup our opportunities text
			$opportunitiesText['Communication Empowerment'] = [
				'Title'        => 'Communication Empowerment',
				'Description'  => '<p>Communication empowerment refers to encouraging the free exchange of ideas, encouraging employee feedback, and fostering employee confidence that their ideas and expertise are valued.</p>',
				'Action Steps' => [
					'<li>Allow team members to add to the conversation. By allowing them to speak first they will not be guided or constrained by your thoughts.</li>',
					'<li>If your employees/ team members are not willing to participate in open forum then have them write down their ideas independently and go around the table/room asking them to read their ideas aloud for the team to discuss.</li>',
					'<li>If an employee is complaining or constantly voicing problems that is frustrating for you, try not to criticize them for speaking up. Explain that you appreciate their engagement and encourage them to find solutions. Perhaps more importantly, be sure to act on those solutions.</li>',
					'<li>Ask for employee opinions and suggestions in project meetings or prior to making decisions. Encouraging all of your team to provide feedback is important but it may also be effective if you call on specific employees if no one speaks up.</li>',
					'<li>If employees are silent, even after asking for their input, prior to a team meeting assign one member the role of "devil\'s advocate" where their primary objective in the meeting is to voice objections.</li>',
					'<li>Empowering employees can be as simple as encouraging others to speak up in meetings, asking for questions, or promoting open discussion on important issues.</li>',
					'<li>In some cases it is difficult to get employees to speak up, especially when they aren\'t used to voicing their opinions. In those instances, assign someone to specifically play "devil\'s advocate" or ask employees to formulate a plan of action. In cases where decisions are out of their hands such as new policy or new strategy from upper management, then ask employees to develop a shared method of execution and compliance.</li>',
				],
			];
			$opportunitiesText['Autonomy'] = [
				'Title'        => 'Autonomy',
				'Description'  => '<p>It is important to allow team members enough freedom to facilitate proper task completion and the authority to act and make decisions to perform their job.</p>',
				'Action Steps' => [
					'<li>Let your team design and execute tasks without interruption. Focus on giving your team an outcome goal and let them make the decisions on how to accomplish it.</li>',
					'<li>In addition to delegating tasks to the team, drive their confidence by confirming they have the power to act.</li>',
					'<li>Resist the urge to micromanage. Even allowing the team members to fail can produce a rich and lasting learning experience.</li>',
					'<li>Employees can develop the knowledge necessary to manage their own work activities. Trust employees to make the important decisions and encourage them to do so.</li>',
					'<li>Clearly communicate your values and your intent regarding the work. Your values will help guide their autonomous decisions and your intent prompts them to make decisions that achieves your objectives without having to consult with you at every step.</li>',
					'<li>For instances when employees report low autonomy, look for tasks to delegate or interview employees to learn which parts of their jobs are the most difficult. Employees on the front line often have more efficient or effective solutions than those imposed by upper management.</li>',
				],
			];
			$opportunitiesText['General'] = [
				'Title'        => 'Information (General)',
				'Description'  => '<p>Items on the information dimension include factors such as communicating frequently, describing a clear mission for the unit, and giving sufficient notice prior to making decisions.</p>',
				'Action Steps' => [
					'<li>For each difficult decision, provide some additional information for <em>why</em> that decision was necessary.</li>',
					'<li>As much as it is allowed, be transparent about what is going on with the business.</li>',
					'<li>Set aside a time each week to update all of your employees via e-mail, text, or message board.</li>',
					'<li>Inform employees about upcoming decisions or changes to give them time to provide input and prepare.</li>',
					'<li>Think of everyone affected by a decision and make an effort to keep them informed.</li>',
					'<li>Use software that automatically updates the workteam on important information (e.g., budget, client deadlines, production). Or make sure everyone has access to this information.</li>',
				],
			];
			$opportunitiesText['Management Communication'] = [
				'Title'        => 'Communication to and from Upper Management',
				'Description'  => '<p>These items include factors such as being a conduit of communication between upper management and the unit/team. That is, clearly communicating what is learned from upper management and in turn relaying concerns to upper management issues communicated by the unit/team.</p>',
				'Action Steps' => [
					'<li>Create daily and/or weekly information meetings. The meetings should be very short (no more than 10 minutes) where you make announcements about information learned from upper management in the days before.</li>',
					'<li>Solicit feedback/concerns from employees that can be related to upper management.</li>',
					'<li>With permission from upper management, share future changes with employees that will affect their jobs.</li>',
					'<li>Make an effort to relate employee suggestions/requests to upper management. Let the employee know their suggestion/request was communicated and be sure to get back to the employee with an answer, even if the answer is \'no.\'</li>',
					'<li>Take care to provide explanations for upper management decisions which often look illogical when taken out of context. </li>',
				],
			];
			$opportunitiesText['Feedback'] = [
				'Title'        => 'Feedback',
				'Description'  => '<p>Feedback behaviors include factors such as defining performance standards, providing corrective feedback in a professional manner, and using performance feedback to effectively motivate employees.</p>',
				'Action Steps' => [
					'<li>Explain performance standards at every performance evaluation meeting.</li>',
					'<li>Get in the habit of providing daily or weekly feedback to employees. This can be one or two comments informally. Make sure most of the feedback is with regard to above average behaviors (positive feedback) with gewer comments devoted to behaviors that do not meet expectations (critical feedback). Most of the comments should be positive.</li>',
					'<li>When correcting employees, carefully model the correct or expected behavior.</li>',
					'<li>Encourage employees to set goals and performance standards. This helps with generating commitment, involvement, and perception that feedback is relevant and motivating.</li>',
					'<li>Use survey data with feedback to provide information to employees, especially with regard to human capital or customer service.</li>',
					'<li>When providing critical feedback, focus only on behaviors and what was observed. Do not discuss anyone\'s character or try to guess "why" someone did not perform up to expectations.</li>',
				],
			];
			$opportunitiesText['Rewards'] = [
				'Title'        => 'Rewards',
				'Description'  => '<p>Upon closer examination, survey items tapping into recognition and rewards for strong performance were below the average. These items include: rewarding individuals for good performance, providing recognition and praise, and providing valuable rewards to employees.</p>',
				'Action Steps' => [
					'<li>Pay close attention to whether you are rewarding for performance. Many managers do not like differential rewards and may reward employees equally, or may give rewards to the loudest employees that continually demand them. Although this may temporarily quiet the most outspoken employees, it hurts in the long term. Employees know who the strongest performers are and if rewards are not allocated according to the rank order of performance, everyone notices. You risk losing your best employees and demotivating many others that stay.</li>',
					'<li>Rewards do not always have to be monetary (e.g., raises, bonuses) and can be simple recognition for a job well done.</li>',
					'<li>The rewards employees’ value are quite varied and depend on the situation. Some employees enjoy being recognized publicly (e.g., employee of the month) and some prefer to be recognized privately (e.g., a memo, office visit with personal thanks). Other examples of small rewards are valued parking spaces, star rewards, gift cards, gas cards, movie cards, parties or picnics.</li>',
					'<li>Consider rewarding with skill-based pay. That is, employees focused on training and development should be rewarded for their efforts since they will impact the bottom line.</li>',
					'<li>Focus feedback and mentoring on the positive aspects of performance. Many managers are only reminded to provide feedback for negative performance. Recognition for positive performance is a powerful motivating reward.</li>',
				],
			];
			$opportunitiesText['Empowerment'] = [
				'Title'        => 'Knowledge Empowerment',
				'Description'  => '<p>Knowledge empowerment behaviors include factors such as teaching team members how to track and evaluate their own performance, seek new development opportunities, or helping them set realistic performance goals.</p>',
				'Action Steps' => [
					'<li>Focus on assisting in overall self-awareness for team member\'s performance. Perhaps even supply team members with a template for them to assess and track their own performance.</li>',
					'<li>After establishing performance standards and training employees how to use them, work on setting realistic performance goals.</li>',
					'<li>Create training and development plans with team members and emphasize the importance of the team member taking the lead on self-development.</li>',
					'<li>Share with employees some of the economic positions of the department and the organization as a whole. This helps employees to keep score and continually add value.</li>',
					'<li>Set a good example by showing your behaviors and actions are driven by your own performance goals.</li>',
					'<li>Explain to employees how performance is measured and rewarded. The key challenge is to do a good job clearly defining performance standards. Many performance appraisal systems list vague performance objectives (i.e., “customer service” rated on a 3-point scale) and these are not very helpful. Instead, clearly define behaviors expected on the job (e.g.,, smile at customers, listen attentively, follow up on their requests, return phone calls and emails in a timely manner).</li>',
					'<li>Teach employees how to score their own performance. With good performance metrics, employees can monitor their own performance which helps with motivation and accountability.</li>'
				],
			];
			$opportunitiesText['Mentoring'] = [
				'Title'        => 'Mentoring',
				'Description'  => '<p>Sufficient one-on-one time with employees allows for the development of a relationship which fosters goal-setting and reviewing team member performance.</p>',
				'Action Steps' => [
					'<li>Informal mentorships can be just as effective as formal mentorships. Get to know your employees and take a personal interest in them and their career.</li>',
					'<li>Prioritize one-on-one interactions with team members to learn more about their career goals and needs.</li>',
					'<li>Work with team members weekly (or at least monthly) to set process goals and track progress.</li>',
					'<li>Take time to coach employees during project meetings, especially with regard to their specific roles and responsibilities. This is a good opportunity to share your own knowledge, skill, and expertise.</li>',
					'<li>Demonstrate a positive attitude and act as a positive role model. By showing what it takes to be productive and successful, you are demonstrating the behaviors and actions necessary to succeed.</li>',
					'<li>Improve formal and informal mentoring by spending 1-on-1 time with employees, supporting their career aspirations, helping them set goals, and/or seeking and providing support for training opportunities.</li>',
				],
			];
			$opportunitiesText['Acquisition'] = [
				'Title'        => 'Knowledge Acquisition',
				'Description'  => '<p>Employees seek training opportunities and/or support for training.</p>',
				'Action Steps' => [
					'<li>Seek more training and development programs for team members.</li>',
					'<li>Challenge team members to research and find additional training opportunities for themselves.</li>',
					'<li>Communicate to team members the process for submitting requests for training.</li>',
					'<li>Add a section to performance evaluations where employees can list the training they received. Be sure to acknowledge that their efforts for improvement are valued.</li>',
					'<li>Work to generate a strong climate for learning within the department. Seek out opportunities for your own development and share what you have learned so that you set the tone.</li>'
				],
			];
			$opportunitiesText['Conflict Management'] = [
				'Title'        => 'Conflict Management',
				'Description'  => '<p>Acting as a mediator to settle conflicts is often required of leaders. Focus on holding other accountable for their actions in a constructive way and search for win-win solutions to conflict. It is not always the case that one party has to win and the other has to lose.</p>',
				'Action Steps' => [
					'<p>Hold team members accountable by ensuring members understand expectations when dealing with issues.</p>',
					'<li>Train team members on how to address conflict and hold each other accountable. Encourage them to devise a system that achieves a win-win resolution.</li>',
					'<li>In terms of managing conflict, there are several steps to holding others accountable. First, pay attention to expectation-performance gaps. If someone is not behaving in a way that is expected, do not ignore it but do not jump to conclusions about who is at fault. Remember - the key to managing confrontations is not to find blame but to resolve the issue and hold others accountable. In the case of an argument, talk with each party face-to-face and show a sincerity to understand both sides. Focus on behaviors and what is preventing someone from delivering.</li>',
					'<li>In many instances it is important to give employees room to handle their own conflict. Debrief with each party separately to coach them.</li>',
					'<li>Resist the urge to always step in and dictate a solution just to make the problem go away. Try to facilitate harmony in the group by being a neutral observer and let them handle conflict.</li>'
				],
			];
			$opportunitiesText['Teamwork'] = [
				'Title'        => 'Teamwork',
				'Description'  => '<p>Create a safe and open environment for others work cooperatively. Ensure team members always treat others with respect and value the diversity of skills, experience, and background.</p>',
				'Action Steps' => [
					'<li>Hold regular team meetings and allow team members to describe tasks and how to complete them.</li>',
					'<li>Set a code of conduct with the team that emphasizes respect and a safe and open environment.</li>',
					'<li>Highlight to the team the value in having diverse and different opinions in task completion and in the general work environment.</li>',
					'<li>Engage everyone on the team to work cooperatively. Delegate tasks to those who are not engaged and look for team members who might be dominating the conversation and thereby shutting others out. Remind your team that you value the skills and special talents of each one.</li>',
					'<li>To encourage teamwork, outcomes must be rewarded and recognized at the team level.</li>'
				],
			];
			$opportunitiesText['Communication'] = [
				'Title'        => 'Communication',
				'Description'  => '<p>Communicating regularly with employees and in particularly, listening to others.</p>',
				'Action Steps' => [
					'<li>Spend time listening to team members on a one-on-one basis and provide feedback to what you heard. Be open and request feedback. If the team members are reluctant to provide feedback then request that each writes down their specific suggestion.</li>',
					'<li>Set time each day to address team members’ communication.</li>',
					'<li>Effective communication entails first being a good listener so practice allowing team members to speak first and even lead team meetings.</li>',
					'<li>Visit team members regularly to encourage their feedback and input and do this in a way that is nonjudgmental.</li>',
					'<li>To get others engaged it might help to specifically assign employees tasks so they feel part of the team. Everyone has talents and interests they care about and tapping into these will help engage the unmotivated employees.</li>'
				],
			];
			$opportunitiesText['Respect'] = [
				'Title'        => 'Respect',
				'Description'  => '<p>These items include factors such as showing genuine concern, and listening and valuing the perspective of all team members.</p>',
				'Action Steps' => [
					'<li>Show respect to the team through personal communication. This is particularly important with larger groups. Know everyone\'s name and take the time to understand their background and family.</li>',
					'<li>Ask questions of team members on their career paths as this will foster mutual respect.</li>',
					'<li>Value the opinions and initiatives of others. Show appreciation for ongoing efforts of the team members and empower them through positive feedback and reinforcement.</li>'
				],
			];

			// Specify dimension parents
			$parent = [
				'Communication Empowerment' => 'Power',
				'Autonomy'                  => 'Power',
				'General'                   => 'Information',
				'Management Communication'  => 'Information',
				'Feedback'                  => 'Information',
				'Rewards'                   => 'Rewards',
				'Empowerment'               => 'Knowledge',
				'Mentoring'                 => 'Knowledge',
				'Acquisition'               => 'Knowledge',
				'Conflict Management'       => 'Relationships',
				'Teamwork'                  => 'Relationships',
				'Communication'             => 'Relationships',
				'Respect'                   => 'Relationships',
			];

			// Calculate the strengths for the report
			$strengths['Power'] = [];
			$strengths['Information'] = [];
			$strengths['Rewards'] = [];
			$strengths['Knowledge'] = [];
			$strengths['Relationships'] = [];
			foreach ($parent as $dimension => $parentDimension)
			{
				if ($scores['Main'][$dimension] >= $scores['Average'][$dimension])
					array_push($strengths[$parentDimension], $strengthsText[$dimension]);
			}

			// Calculate the opportunities for the report
			$opportunities['Power'] = [];
			$opportunities['Information'] = [];
			$opportunities['Rewards'] = [];
			$opportunities['Knowledge'] = [];
			$opportunities['Relationships'] = [];
			foreach ($parent as $dimension => $parentDimension)
			{
				if ($scores['Main'][$dimension] < $scores['Average'][$dimension])
				{
					shuffle($opportunitiesText[$dimension]['Action Steps']);
					array_push($opportunities[$parentDimension], $opportunitiesText[$dimension]);
				}
			}
		}

		else
		{
			$data = json_decode($reportData, true);
			$scores = $data['scores'];
			$strengths = $data['strengths'];
			$opportunities = $data['opportunities'];
		}

		// Keep track of page numbers
		$page = 1;

		return view('reports.lsr', compact('job', 'user', 'scores', 'strengths', 'opportunities', 'page'));
	}

    public function evonik($jobId, $userId, $report)
    {
        $job = Job::findOrFail($jobId);
        $user = User::find($userId);
        $assignments = $user->assignments;
        $s = new ScoringController();

        // Get the assignments that we'll be scoring
//        $aptitude = $assignments->where('assessment_id', $report->aptitude_id)->first();
//        $ability = $assignments->where('assessment_id', $report->ability_id)->first();
//        $personality = $assignments->where('assessment_id', $report->personality_id)->first();
		$aptitude = $user->lastCompletedAssignmentForJob($report->aptitude_id, $job->id);
		$ability = $user->lastCompletedAssignmentForJob($report->ability_id, $job->id);
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);

        $aptitudeScore = $s->score($aptitude->id, $job->id);
        $aptitudeTotal = $aptitude->assessment()->questions()->count();

        $abilityScore = $s->score($ability->id, $job->id);
        $abilityTotal = $ability->assessment()->questions()->count();

        $personalityScore = $s->score($personality->id, $job->id);

		// Setup the zones for the chart graph
		$abilityZones = [
			'value' => [],
			'color' => [],
		];
		$abilityDivisions = $job->weights->where('assessment_id', $report->ability_id)->first()->divisions;
		foreach ($abilityDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['max']);
		}
		sort($abilityZones['value']);
		$abilityZones['color'][0] = '#E32731';
		$abilityZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($abilityZones['value'], $abilityTotal);
		array_push($abilityZones['color'], '#30BD21');

		// Setup the zones for the chart graph
		$aptitudeZones = [
			'value' => [],
			'color' => [],
		];
		$aptitudeDivisions = $job->weights->where('assessment_id', $report->aptitude_id)->first()->divisions;
		foreach ($aptitudeDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $aptitudeZones['value']))
				array_push($aptitudeZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $aptitudeZones['value']))
				array_push($aptitudeZones['value'], $division['max']);
		}
		sort($aptitudeZones['value']);
		$aptitudeZones['color'][0] = '#E32731';
		$aptitudeZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($aptitudeZones['value'], $aptitudeTotal);
		array_push($aptitudeZones['color'], '#30BD21');

        $scores = [
            'Aptitude' => [
                'Score' => $aptitudeScore,
                'Total' => $aptitudeTotal,
                'Accuracy' => number_format(($aptitudeScore / $aptitudeTotal) * 100, 0),
                'Division' => $s->getScoreDivision($aptitude->id, $job->id, $aptitudeScore),
                'Percentile' => $this->getAptitudePercentile($aptitudeScore),
                'Floor' => $this->getAptitudeFloor($aptitudeScore),
                'Ceiling' => $this->getAptitudeCeiling($aptitudeScore),
				'Zones' => $aptitudeZones,
            ],
            'Ability' => [
                'Score' => $abilityScore,
                'Total' => $abilityTotal,
                'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
                'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
                'Percentile' => $this->getAbilityPercentile($abilityScore),
                'Floor' => $this->getAbilityFloor($abilityScore),
                'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $abilityZones,
            ],
            'Personality' => [
                'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
                'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
                'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
                'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
                'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
                'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
                'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
                'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
                'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
                'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
                'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
                'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
                'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
                'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
                'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
                'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
                'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
                'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
                'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
                'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
                'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
                'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
                'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
                'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
                'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
                'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
                'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
                'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
                'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
                'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
                'Overall' => $personalityScore,
                'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
            ],
        ];

        // Go through all the chart coordinates and insert the user's score in the proper place in the array
//        foreach ($scores['Aptitude']['Chart'] as $j => $division)
//        {
//            foreach ($division as $i => $coordinates)
//            {
//                if (! array_key_exists($i + 1, $division))
//                    continue;
//
//                if ($scores['Aptitude']['Score'] >= $division[$i][0] and $scores['Aptitude']['Score'] < $division[$i+1][0])
//                {
//                    array_splice($scores['Aptitude']['Chart'][$j], $i+1, 0, [
//                        [
//                            $scores['Aptitude']['Score'],
//                            $scores['Aptitude']['Percentile'],
//                            true
//                        ],
//                    ]);
//                }
//            }
//        }

        // Go through all the chart coordinates and insert the user's score in the proper place in the array
//        foreach ($scores['Ability']['Chart'] as $j => $division)
//        {
//            foreach ($division as $i => $coordinates)
//            {
//                if (! array_key_exists($i + 1, $division))
//                    continue;
//
//                if ($scores['Ability']['Score'] >= $division[$i][0] and $scores['Ability']['Score'] < $division[$i+1][0])
//                {
//                    array_splice($scores['Ability']['Chart'][$j], $i+1, 0, [
//                        [
//                            $scores['Ability']['Score'],
//                            $scores['Ability']['Percentile'],
//                            true
//                        ],
//                    ]);
//                }
//            }
//        }

        return view('reports.evonik', compact('job', 'user', 'scores'));
    }

    public function evonik2020($jobId, $userId, $report)
    {
        $job = Job::findOrFail($jobId);
        $user = User::find($userId);
        $assignments = $user->assignments;
        $s = new ScoringController();

        // Get the assignments that we'll be scoring
		$aptitude = $user->lastCompletedAssignmentForJob($report->evonik_assessment_id, $job->id);
		$ability = $user->lastCompletedAssignmentForJob($report->reasoning_b_id, $job->id);
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);

        $aptitudeScore = $s->score($aptitude->id, $job->id);
        $aptitudeTotal = $aptitude->assessment()->questions()->count();

        $abilityScore = $s->score($ability->id, $job->id);
        $abilityTotal = $ability->assessment()->questions()->count();

        $personalityScore = $s->score($personality->id, $job->id);

		// Setup the zones for the chart graph
		$abilityZones = [
			'value' => [],
			'color' => [],
		];
		$abilityDivisions = $job->weights->where('assessment_id', $report->reasoning_b_id)->first()->divisions;
		foreach ($abilityDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['max']);
		}
		sort($abilityZones['value']);
		$abilityZones['color'][0] = '#E32731';
		$abilityZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($abilityZones['value'], $abilityTotal);
		array_push($abilityZones['color'], '#30BD21');

		// Setup the zones for the chart graph
		$aptitudeZones = [
			'value' => [],
			'color' => [],
		];
		$aptitudeDivisions = $job->weights->where('assessment_id', $report->evonik_assessment_id)->first()->divisions;
		foreach ($aptitudeDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $aptitudeZones['value']))
				array_push($aptitudeZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $aptitudeZones['value']))
				array_push($aptitudeZones['value'], $division['max']);
		}
		sort($aptitudeZones['value']);
		$aptitudeZones['color'][0] = '#E32731';
		$aptitudeZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($aptitudeZones['value'], $aptitudeTotal);
		array_push($aptitudeZones['color'], '#30BD21');

        $scores = [
            'Aptitude' => [
                'Score' => $aptitudeScore,
                'Total' => $aptitudeTotal,
                'Accuracy' => number_format(($aptitudeScore / $aptitudeTotal) * 100, 0),
                'Division' => $s->getScoreDivision($aptitude->id, $job->id, $aptitudeScore),
                'Percentile' => $this->getAptitudePercentile($aptitudeScore),
                'Floor' => $this->getAptitudeFloor($aptitudeScore),
                'Ceiling' => $this->getAptitudeCeiling($aptitudeScore),
				'Zones' => $aptitudeZones,
            ],
            'Ability' => [
                'Score' => $abilityScore,
                'Total' => $abilityTotal,
                'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
                'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
                'Percentile' => $this->getAbilityPercentile($abilityScore),
                'Floor' => $this->getAbilityFloor($abilityScore),
                'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $abilityZones,
            ],
            'Personality' => [
                'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
                'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
                'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
                'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
                'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
                'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
                'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
                'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
                'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
                'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
                'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
                'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
                'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
                'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
                'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
                'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
                'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
                'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
                'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
                'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
                'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
                'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
                'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
                'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
                'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
                'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
                'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
                'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
                'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
                'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
                'Overall' => $personalityScore,
                'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
            ],
        ];

        return view('reports.evonik2020', compact('job', 'user', 'scores'));
    }

	public function aoep($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$personalityScore = $s->score($personality->id, $job->id);

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
		];

		return view('reports.aoep', compact('job', 'user', 'scores'));
    }

	public function getPScores($jobId, $userId)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
		$personality = $user->lastCompletedAssignmentForJob(get_global('personality'), $job->id);
		$personalityScore = $s->score($personality->id, $job->id);

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
		];

		return $scores;
    }

	public function aoepa($jobId, $userId, $report, $export)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$ability = $assignments->where('assessment_id', $report->ability_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$ability = $user->lastCompletedAssignmentForJob($report->ability_id, $job->id);

		$abilityScore = $s->score($ability->id, $job->id);
		$abilityTotal = $ability->assessment()->questions()->count();
		$personalityScore = $s->score($personality->id, $job->id);

		// Setup the zones for the chart graph
		$zones = [
			'value' => [],
			'color' => [],
		];
		$abilityDivisions = $job->weights->where('assessment_id', $report->ability_id)->first()->divisions;
		foreach ($abilityDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $zones['value']))
				array_push($zones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $zones['value']))
				array_push($zones['value'], $division['max']);
		}
		sort($zones['value']);
		$zones['color'][0] = '#E32731';
		$zones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($zones['value'], $abilityTotal);
		array_push($zones['color'], '#30BD21');

		$scores = [
			'Ability' => [
				'Score' => $abilityScore,
				'Total' => $abilityTotal,
				'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
				'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
				'Percentile' => $this->getAbilityPercentile($abilityScore),
				'Floor' => $this->getAbilityFloor($abilityScore),
				'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $zones,
			],
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
		];

		return view('reports.aoepa', compact('job', 'user', 'scores', 'export'));
	}

	public function sixty($assignmentId, $userId)
	{
		ini_set('max_execution_time', 520);
		$assignment = Assignment::find($assignmentId);
		$user = User::find($userId);
		$s = new ScoringController();

		// Find all completed assignments that pertain to this specific leader
		$assignments = Assignment::where([
			'created_at' => $assignment->created_at,
			'completed' => 1
		])->get()->filter(function($assignment) use ($user)
		{
			// Filter these to make sure that this assignment was rating this specific user
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

		// Get the dimensions we're rating on
		$dimensions = [
			'Creative Problem Solving' => [
				'Answers' => [],
				'Definition' => 'Creative Problem Solving is defined as proactive problem-solving focusing on the successful implementation of novel and creative solutions to known or anticipated problems, being mindful of and efficient in using resources (i.e., resourcefulness), and utilizing a visionary mindset.',
				'Expectations' => [
					'1' => [
						'Consistently misses deadlines or lacks in contribution with enterprise or internal creation/development projects they are working on.',
						'Consistently fails to utilize talent/resources to improve processes or procedures',
						'New initiatives are costly and fail to produce significant results as promised',
						'Typically satisfied with following the same processes, using the same roles, etc. over extended periods of time',
						'Resistant to change management efforts (fails to analyze staffing, fails to accept direction to analyze processes, etc.)',
					],
					'3' => [
						'Development and implementation of a process or procedure that has limited impact and improves outcomes and revenue',
						'Implementation of a new/improved process or procedure that reduces staff requirements or increases productivity with limited additional human/capital resource',
						'Implementation of a new/improved process which directly impacts the productivity or efficiency of the stakeholder\'s own department or role(s) stakeholder is either in or has direct oversight over',
					],
					'5' => [
						'Creates/Develops/Assists within and across the enterprise with initiatives that significantly impact outcomes/safety and improve revenue',
						'Develops and implements a process or procedure that improves outcomes, safety and significantly improves revenue',
						'Implementation of a new/improved process or procedure that increases productivity without additional human/capital resources',
						'Consistently educates self in areas of expertise and researches new technologies, processes, etc.',
						'Takes positive action, even in the face of unforeseen obstacles, and works to overcome traditional boundaries to move ahead in day-to-day activities, but also long-term activities.',
					],
				]
			],
			'Leadership Adaptability' => [
				'Answers' => [],
				'Definition' => 'Leadership Adaptability is having the ability to see the need for change early on. Having the willingness to smoothly and comfortably adjust his/her work style to the change as well as assist his/her team in positively adapting to the change. This competency also captures a manager\'s psychological ownership over the change. ',
				'Expectations' => [
					'1' => [
						'Fails to make changes happen that are presented to him/her and creates negative impact within their teams and other departments or enterprise wide if applicable.',
						'Does not react well to changing environments and fails to maneuver resources/processes which negatively impact others',
						'Handles change poorly creating a negative environment within their department or other departments',
					],
					'3' => [
						'Makes changes happen that are presented to him/her without significant negative impact within their teams and other departments or enterprise wide if applicable.',
						'Reacts well to changing environments and maneuvers resources/processes to minimize impact to others',
						'Controls change management messaging, understands initiatives, supports change',
						'Handles change well without creating a negative environment within their department or other departments',
					],
					'5' => [
						'Takes ownership of changes that are presented to him/her and creates a positive change atmosphere within their teams and other departments or enterprise wide if applicable.',
						'Has the ability to see change coming and actively maneuvers resources/processes to create a positive impact to others',
						'Acts as a spokesperson/ambassador for change to others and departments to gather support for change',
						'Volunteers to be an active participant in groups or task force where change of a process is expected.',
					],
				]
			],
			'Collaboration' => [
				'Answers' => [],
				'Definition' => 'Collaboration is being able to effectively work with internal stakeholders up and down the chain (vertical) and horizontally with peers as well as external individuals/organizations/partners (e.g., vendor, community leaders)',
				'Expectations' => [
					'1' => [
						'Focus is solely on their own department/ function',
						'Does not seek to develop internal relationships',
						'No focus on community partnerships. Views business partners as a resource to consume and are disposable',
						'Eschews partnerships to serve own interests, &ldquo;goes it alone&rdquo;, lacks interest in others opinions and expertise',
						'Disruptive to relationships and negotiated efforts to create change',
					],
					'3' => [
						'Interfaces on occasion with peers in the enterprise and utilizes information learned to improve the strategic positioning of their department for success.',
						'Develops relationships with a few other leaders (Peer/Senior/subordinate) and occasionally assists others in understanding business objectives and achieving expected results',
						'Maintains solid, respected peer relationships',
						'Interfaces with community partners to volunteer with Hope Works partners, create valuable relationships within the Community.',
						'Has solid relationships with external business partners who are responsive to the company&#39;s needs.',
					],
					'5' => [
						'Interfaces purposely with peers in the enterprise and utilizes information learned to improve the strategic positioning of the company for success.',
						'Develops strong relationships with other leaders (Peer/Senior/subordinate) and assists others in understanding business objectives and achieving expected results',
						'Interfaces with community partners to develop new services within company/enterprise wide, raise funds to support community partners, create valuable relationships within the Community.',
						'Builds networks across enterprise/business units with key leaders/stakeholders who provide expertise and center of excellence resources for projects and know how, serve as trusted &ldquo;go-to&rsquo;s&rdquo;',
						'Utilizes peer relationships to move forward significant projects, affect change in process/policy, and/or thought partner to advance innovation and create successful outcomes',
					],
				]
			],
			'Self-Development' => [
				'Answers' => [],
				'Definition' => 'Self-Development encompasses identification of weaknesses and positive action to overcome identified weaknesses, continuing to build one\'s strengths, being self-aware of actions and how they impact others, transferring formal and informal training/development back to one\'s daily job, and mastering all aspects of management (e.g., finance), not just functional expertise (e.g., clinic)',
				'Expectations' => [
					'1' => [
						'Comfortable in their own skin, overly satisfied with current state',
						'Defiant when faced with feedback. Provides counter reasoning rather than accepting coaching',
						'Unwilling to learn new skills or avoids learning opportunities which are challenging/uncomfortable',
						'Mandatory training is rarely completed on time for self and supervised staff',
					],
					'3' => [
						'Attends training as required for new activities related to their area in the hospital and demonstrates applied learning',
						'Works with their one up to improve on areas that they are weak to be more successful in their current role',
						'Is able to introduce some of the new skills into daily work routine',
						'Ensures any mandatory training is completed timely for self and staff',
					],
					'5' => [
						'Actively evaluates their current skill set and identifies weaknesses/creates improvement plans to then correct and strengthen those areas without impact to their strengths',
						'Seeks feedback on regular basis as to soft skills. Use EQ to assess inter-relational competency and actively works to develop new approaches and communication styles',
						'Seeks out challenging learning opportunities (webinars, conferences, higher ed) to develop new technical skills, specifically in traditional areas of weakness (finance, etc.) and abilities to grow in the areas required for their next career step',
						'Works with one up to create development plans in order to advance their job knowledge/skill set',
						'Implements new acquired knowledge into new skills and applies them directly to their job',
					],
				]
			],
			'Business Mindset' => [
				'Answers' => [],
				'Definition' => 'Business Mindset encompasses having a clear and complete focus on the entire operation as a sustainable/growing business, seeing and implementing the entire vision of the company, inclusive of internal and external stakeholders, and linking departmental operations to all business indicators (i.e., labor costs, patient satisfaction, quality), and how departmental metrics truly impact the entire business.',
				'Expectations' => [
					'1' => [
						'Weak team building ability with hiring and holding underperforming employees accountable/plays favorites',
						'Goal development is not tied to company objectives and are weak goals',
						'Takes little effort with goal development for team members',
						'Handles all projects on their own without utilizing talent for development opportunities',
					],
					'3' => [
						'Builds a good team with decent hires and holds most employees accountable for their performance',
						'Ability to structure SMART department goals to align with company goals',
						'Develops solid general goals for team members',
						'Utilizes high level talent to lead department specific initiatives',
					],
					'5' => [
						'Develops a strong team by identifying the best people for open positions in departments and assertively holds underperforming employees accountable',
						'Ability to structure robust and SMART department goals to align with company objectives and ensure the individual goals for his/her team members are properly supporting these department goals while stretching the development of the stakeholder.',
						'Creates career paths within the department, properly identifies stakeholders that have the ability to develop into more advanced roles, and applyies development plans for stakeholders identified for growth to assist them in gathering the skills and abilities needed for the next role.',
						'Identifies and develops successors for self and others',
						'Creates development opportunities with team members that give them the ability to grow in areas outside of the department (example: working on enterprise initiatives, works on multi-department projects for expanded developmental exposure).',
					],
				]
			],
			'Performance Management' => [
				'Answers' => [],
				'Definition' => 'Performance Management is defined as the ability to consistently lead and develop individuals and teams, set goals, creates and implements a valued rewards structure, consistently holding individuals and teams accountable in an effective and positive manner, and providing the necessary information in a clear and actionable manner. ',
				'Expectations' => [
					'1' => [
						'Does not fully understand the financial reporting of the company and how they can impact the financial success of the company by implementing change',
						'Comfortable with the status quo',
						'Only concerned with how their department operates versus the revenue it generates or the cost incurred by the company',
					],
					'3' => [
						'Has a general understanding of the financial reporting of the company and uses the information gathered to make some business decisions to support financial improvement',
						'Able to adjust some department resources to support overarching company objectives and able to save in a few areas of the department operations and budgeting',
						'Understands how other departments operations affect his/her department functions and works to minimize impact to their department through collaborative efforts',
					],
					'5' => [
						'Can interpret and understand the financial reports of the company and uses them as a guide to make financial related business decisions',
						'Utilizes financial resources to support overarching company objectives and trims wasteful spending in areas that do not support company objectives',
						'Understands how the performance of his/her department affects other departments in the organization and works to solve problems cooperatively and supports other departments needs that support the companies objectives',
						'Translates department level KPIs and tactics into plans to drive and support strategic growth and to reach function or unit level business objectives including growth, NOI, etc.',
					],
				]
			]
		];

		// For each assignment, collect answers pertaining to each dimension
		foreach ($assignments as $assignment)
		{
			foreach ($dimensions as $dimensionName => $dimension)
			{
				$answers = $assignment->answers->filter(function($answer) use ($dimensionName) {
					$question = Question::find($answer->question_id);

					if ($question->dimension()->name == $dimensionName)
					{
						$answer->question_type = $question->type;
						return true;
					}
				});

				// Get relation of user to the target
				$relationToTarget = $assignment->user->getUserTargetRelation($assignment->target);

				// If this doesn't exist in our array, add that relation as a separate category
				if (! array_key_exists($relationToTarget, $dimensions[$dimensionName]['Answers']))
					$dimensions[$dimensionName]['Answers'][$relationToTarget] = [];

				// Sort them neatly into the dimension array
				array_push($dimensions[$dimensionName]['Answers'][$relationToTarget], $answers);
			}
		}

		// Get our scores and free-form responses
		foreach ($dimensions as $dimensionName => $dimensionData)
		{
			// First the scores for each category separately
			$totalScore = 0;
			$totalCount = 0;
			foreach ($dimensionData['Answers'] as $categoryName => $answersCollection)
			{
				// Get score for the category
				$score = 0;
				$count = 0;
				foreach ($answersCollection as $answers)
				{
					foreach ($answers as $answer)
					{
						if ($answer->question_type != 1)
							continue;

						$score += $answer->value;
						$count++;
					}
				}

				// Gather our scores
				$scores[$dimensionName]['Score'][$categoryName] = 0;
				if ($count)
					$scores[$dimensionName]['Score'][$categoryName] = ($score / $count) + 1;

				// Update the totals
				$totalScore += $score;
				$totalCount += $count;
			}

			// Gather the total score
			$scores[$dimensionName]['Score']['Total'] = ($totalScore / $totalCount) + 1;

			// Then the responses for each category separately
			foreach ($dimensionData['Answers'] as $categoryName => $answersCollection)
			{
				// Get response for the category
				$responses = [];
				foreach ($answersCollection as $answers)
				{
					foreach ($answers as $answer)
					{
						if ($answer->question_type != 3 or !$answer->value)
							continue;

						array_push($responses, $answer->value);
					}
				}

				// Gather our responses
				$scores[$dimensionName]['Feedback'][$categoryName] = $responses;
			}

			// Gather other info as well
			$scores[$dimensionName]['Definition'] = $dimensions[$dimensionName]['Definition'];
			$scores[$dimensionName]['Expectations'] = $dimensions[$dimensionName]['Expectations'];
		}

		// Show only the Self and Direct Report sub scores
//		foreach ($scores as $dimension => $data)
//		{
//			foreach ($data['Score'] as $category => $score)
//			{
//				if ($category == 'Self' or $category == 'Direct Report' or $category == 'Total')
//					continue;
//
//				unset($scores[$dimension]['Score'][$category]);
//			}
//		}

		// Show feedback in three categories: Self, Direct Report, and Other
		foreach ($scores as $dimension => $data)
		{
			$otherFeedback = [];
			foreach ($data['Feedback'] as $category => $feedback)
			{
				if ($category == 'Self' or $category == 'Direct Report')
					continue;

				foreach ($scores[$dimension]['Feedback'][$category] as $response)
					array_push($otherFeedback, $response);
				unset($scores[$dimension]['Feedback'][$category]);
			}
			$scores[$dimension]['Feedback']['Others'] = $otherFeedback;
		}

		return view('reports.360', compact('user', 'scores'));
	}

	public function sixtyctca($assignmentId, $userId)
	{
		ini_set('max_execution_time', 520);
		$assignment = Assignment::find($assignmentId);
		$user = User::find($userId);
		$s = new ScoringController();

		// Find all completed assignments that pertain to this specific leader
		$assignments = Assignment::where([
			'created_at' => $assignment->created_at,
			'completed' => 1
		])->get()->filter(function($assignment) use ($user)
		{
			// Filter these to make sure that this assignment was rating this specific user
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

		// Get the dimensions we're rating on
		$dimensions = [
			'Creative Problem Solving' => [
				'Answers' => [],
				'Definition' => 'Creative Problem Solving is defined as proactive problem-solving focusing on the successful implementation of novel and creative solutions to known or anticipated problems, being mindful of and efficient in using resources (i.e., resourcefulness), and utilizing a visionary mindset.',
				'Expectations' => [
					'1' => [
						'Consistently misses deadlines or lacks in contribution with enterprise or internal creation/development projects they are working on.',
						'Consistently fails to utilize talent/resources to improve processes or procedures',
						'New initiatives are costly and fail to produce significant results as promised',
						'Stakeholder is typically satisfied with following the same processes, using the same roles, etc. over extended periods of time',
						'Stakeholder is resistant to change management efforts (fails to analyze staffing, fails to accept direction to analyze processes, etc.)',
					],
					'3' => [
						'Development and implementation of a process or procedure that impacts limited departments within SRMC and improves patient outcomes and safety and revenue',
						'Implementation of a new/improved process or procedure that reduces staff requirements or increases productivity with limited additional human/capital resource',
						'Implementation of a new/improved process which directly impacts the productivity or efficiency of the stakeholders own department or role(s) either stakeholder is in or has direct oversight over',
					],
					'5' => [
						'Creates/Develops/Assists within and across the enterprise with initiatives that significantly impact one or more CTCA hospitals with long lasting impact on patient outcomes/safety and improves revenue',
						'Develops and implements a process or procedure that impacts multiple departments within SRMC and improves patient outcomes and safety and significantly improves revenue',
						'Implementation of a new/improved process or procedure that reduces staff requirements or increases productivity without additional human/capital resources',
						'Consistently educates self in areas of expertise and researches new technologies, processes, etc.',
						'Takes positive action, even in the face of unforseen obstacles, and works to ovecome traditional boundaries to move ahead in day-to-day activties, but also long-term activities.',
					],
				]
			],
			'Leadership Adaptability' => [
				'Answers' => [],
				'Definition' => 'Leadership Adaptability is having the ability to see the need for change early on. Having the willingness to smoothly and comfortably adjust his/her work style to the change as well as assist his/her team in positively adapting to the change. This competency also captures a manager\'s psychological ownership over the change. ',
				'Expectations' => [
					'1' => [
						'Fails to make changes happen that are presented to him/her and creates negative impact within their teams and other departments within SRMC or enterprise wide if applicable.',
						'Does not react well to changing environments and fails to maneuver resources/processes which negatively impact staff/patients',
						'Handles change poorly creating a negative environment within their department or other departments in SRMC',
					],
					'3' => [
						'Makes changes happen that are presented to him/her without significant negative impact within their teams and other departments within SRMC or enterprise wide if applicable.',
						'Reacts well to changing environments and maneuvers resources/processes to minimize impact to staff/patients',
						'Controls change management messaging, understands initiatives, supports change',
						'Handles change well without creating a negative environment within their department or other departments in SRMC',
					],
					'5' => [
						'Takes ownership of changes that are presented to him/her and creates a positive change atmosphere within their teams and other departments within SRMC or enterprise wide if applicable.',
						'Has the ability to see change coming and actively maneuvers resources/processes to create a positive impact to staff/patients',
						'Acts as a spokesperson/ambassador for change to other stakeholders and departments to gather support for change',
						'Volunteers to be an active participant in groups or task force where change of a process is expected.',
					],
				]
			],
			'Collaboration' => [
				'Answers' => [],
				'Definition' => 'Collaboration is being able to effectively work with internal stakeholders up and down the chain of CTCA hierarchy (vertical) and horizontally with peers as well as external individuals/organizations/partners (e.g., vendor, community leaders)',
				'Expectations' => [
					'1' => [
						'Focus is solely on their own department/ function',
						'Does not seek to develop internal relationships',
						'No focus on community partnerships. Views business partners as a resource to consume and are disposable',
						'Eschews partnerships to serve own interests, &ldquo;goes it alone&rdquo;, lacks interest in others opinions and expertise',
						'Disruptive to relationships and negotiated efforts to create change',
					],
					'3' => [
						'Interfaces on occasion with peers in the enterprise and utilizes information learned to improve the strategic positioning of their department for success.',
						'Develops relationships with a few other SRMC leaders (Peer/Senior/subordinate) and occasionally assists others in understanding business objectives and achieving expected results',
						'Maintains solid, respected peer relationships',
						'Interfaces with community partners to volunteer with Hope Works partners, create valuable relationships within the Community.',
						'Has solid relationships with external business partners who are responsive to the hospital&#39;s needs.',
					],
					'5' => [
						'Interfaces purposely with peers in the enterprise and utilizes information learned to improve the strategic positioning of SRMC for success.',
						'Develops strong relationships with other SRMC leaders (Peer/Senior/subordinate) and assists others in understanding business objectives and achieving expected results',
						'Interfaces with community partners to develop new services within SRMC/enterprise wide, raise funds to support community partners, volunteer with Hope Works partners, create valuable relationships within the Community.',
						'Builds networks across enterprise/business units with key leaders/stakeholders who provide expertise and center of excellence resources for projects and know how, serve as trusted &ldquo;go-to&rsquo;s&rdquo;',
						'Utilizes peer relationships to move forward significant projects, affect change in process/policy, and/or thought partner to advance innovation and create successful outcomes',
					],
				]
			],
			'Self-Development' => [
				'Answers' => [],
				'Definition' => 'Self-Development encompasses identification of weaknesses and positive action to overcome identified weaknesses, continuing to build one\'s strengths, being self-aware of actions and how they impact others, transferring formal and informal training/development back to one\'s daily job, and mastering all aspects of management (e.g., finance), not just functional expertise (e.g., clinic)',
				'Expectations' => [
					'1' => [
						'Comfortable in their own skin, overly satisfied with current state',
						'Defiant when faced with feedback. Provides counter reasoning rather than accepting coaching',
						'Unwilling to learn new skills or avoids learning opportunities which are challenging/uncomfortable',
						'Mandatory training is rarely completed on time for self and supervised staff',
					],
					'3' => [
						'Attends training as required for new activities related to their area in the hospital and demonstrates applied learning',
						'Works with their one up to improve on areas that they are weak to be more successful in their current role',
						'Is able to introduce some of the new skills into daily work routine',
						'Ensures any mandatory training is completed timely for self and staff',
					],
					'5' => [
						'Actively evaluates their current skill set and identifies weaknesses/creates improvement plans to then correct and strengthen those areas without impact to their strengths',
						'Seeks feedback on regular basis as to soft skills. Use EQ to assess inter-relational competency and actively works to develop new approaches and communication styles',
						'Seeks out challenging learning opportunities (webinars, conferences, higher ed) to develop new technical skills, specifically in traditional areas of weakness (finance, etc.) and abilities to grow in the areas required for their next career step',
						'Works with one up to create development plans in order to advance their job knowledge/skill set',
						'Implements new acquired knowledge into new skills and applies them directly to their job',
					],
				]
			],
			'Business Mindset' => [
				'Answers' => [],
				'Definition' => 'Business Mindset encompasses having a clear and complete focus on the entire operation as a sustainable/growing business, seeing and implementing the entire vision of CTCA, inclusive of internal and external stakeholders, and linking departmental operations to all business indicators (i.e., labor costs, patient satisfaction, quality), and how departmental metrics truly impact the entire business.',
				'Expectations' => [
					'1' => [
						'Weak team building ability with hiring and holding underperforming employees accountable/plays favorites',
						'Goal development is not tied to company objectives and are weak goals',
						'Takes little effort with goal development for team members',
						'Handles all projects on their own without utilizing talent for development opportunities',
					],
					'3' => [
						'Builds a good team with decent hires and holds most employees accountable for their performance',
						'Ability to structure SMART department goals to align with company goals',
						'Develops solid general goals for team members',
						'Utilizes high level talent to lead department specific initiatives',
					],
					'5' => [
						'Develops a strong team by identifying the best people for open positions in departments and assertively holds underperforming employees accountable',
						'Ability to structure robust and SMART department goals to align with company objectives and ensure the individual goals for his/her team members are properly supporting these department goals while stretching the development of the stakeholder.',
						'Creates career paths within the department, properly identifies stakeholders that have the ability to develop into more advanced roles, and applyies development plans for stakeholders identified for growth to assist them in gathering the skills and abilities needed for the next role.',
						'Identifies and develops successors for self and others',
						'Creates development opportunities with team members that give them the ability to grow in areas outside of the department (example: working on enterprise initiatives, works on multi-department projects for expanded developmental exposure).',
					],
				]
			],
			'Performance Management' => [
				'Answers' => [],
				'Definition' => 'Performance Management is defined as the ability to consistently lead and develop individuals and teams, set goals, creates and implements a valued rewards structure, consistently holding individuals and teams accountable in an effective and positive manner, and providing the necessary information in a clear and actionable manner. ',
				'Expectations' => [
					'1' => [
						'Does not fully understand the financial reporting of the company and how they can impact the financial success of the company by implementing change',
						'Comfortable with the status quo',
						'Only concerned with how their department operates versus the revenue it generates or the cost incurred by the company',
					],
					'3' => [
						'Has a general understanding of the financial reporting of the company and uses the information gathered to make some business decisions to support financial improvement',
						'Able to adjust some department resources to support overarching company objectives and able to save in a few areas of the department operations and budgeting',
						'Understands how other departments operations affect his/her department functions and works to minimize impact to their department through collaborative efforts',
					],
					'5' => [
						'Can interpret and understand the financial reports of SRMC and uses them as a guide to make financial related business decisions',
						'Utilizes financial resources to support overarching company objectives and trims wasteful spending in areas that do not support company objectives',
						'Understands how the performance of his/her department affects other departments in the organization and works to solve problems cooperatively and supports other departments needs that support the companies objectives',
						'Translates department level KPIs and tactics into plans to drive and support strategic growth and to reach function or unit level business objectives including growth, NOI, etc.',
					],
				]
			]
		];

		// For each assignment, collect answers pertaining to each dimension
		foreach ($assignments as $assignment)
		{
			foreach ($dimensions as $dimensionName => $dimension)
			{
				$answers = $assignment->answers->filter(function($answer) use ($dimensionName) {
					$question = Question::find($answer->question_id);

					if ($question->dimension()->name == $dimensionName)
					{
						$answer->question_type = $question->type;
						return true;
					}
				});

				// Get relation of user to the target
				$relationToTarget = $assignment->user->getUserTargetRelation($assignment->target);

				// If this doesn't exist in our array, add that relation as a separate category
				if (! array_key_exists($relationToTarget, $dimensions[$dimensionName]['Answers']))
					$dimensions[$dimensionName]['Answers'][$relationToTarget] = [];

				// Sort them neatly into the dimension array
				array_push($dimensions[$dimensionName]['Answers'][$relationToTarget], $answers);
			}
		}

		// Get our scores and free-form responses
		foreach ($dimensions as $dimensionName => $dimensionData)
		{
			// First the scores for each category separately
			$totalScore = 0;
			$totalCount = 0;
			foreach ($dimensionData['Answers'] as $categoryName => $answersCollection)
			{
				// Get score for the category
				$score = 0;
				$count = 0;
				foreach ($answersCollection as $answers)
				{
					foreach ($answers as $answer)
					{
						if ($answer->question_type != 1)
							continue;

						$score += $answer->value;
						$count++;
					}
				}

				// Gather our scores
				$scores[$dimensionName]['Score'][$categoryName] = 0;
				if ($count)
					$scores[$dimensionName]['Score'][$categoryName] = ($score / $count) + 1;

				// Update the totals
				$totalScore += $score;
				$totalCount += $count;
			}

			// Gather the total score
			$scores[$dimensionName]['Score']['Total'] = ($totalScore / $totalCount) + 1;

			// Then the responses for each category separately
			foreach ($dimensionData['Answers'] as $categoryName => $answersCollection)
			{
				// Get response for the category
				$responses = [];
				foreach ($answersCollection as $answers)
				{
					foreach ($answers as $answer)
					{
						if ($answer->question_type != 3 or !$answer->value)
							continue;

						array_push($responses, $answer->value);
					}
				}

				// Gather our responses
				$scores[$dimensionName]['Feedback'][$categoryName] = $responses;
			}

			// Gather other info as well
			$scores[$dimensionName]['Definition'] = $dimensions[$dimensionName]['Definition'];
			$scores[$dimensionName]['Expectations'] = $dimensions[$dimensionName]['Expectations'];
		}

		// Show only the Self and Direct Report scores
		foreach ($scores as $dimension => $data)
		{
			foreach ($data['Score'] as $category => $score)
			{
				if ($category == 'Self' or $category == 'Direct Report' or $category == 'Total')
					continue;

				unset($scores[$dimension]['Score'][$category]);
			}
		}

		// Show feedback in three categories: Self, Direct Report, and Other
		foreach ($scores as $dimension => $data)
		{
			$otherFeedback = [];
			foreach ($data['Feedback'] as $category => $feedback)
			{
				if ($category == 'Self' or $category == 'Direct Report')
					continue;

				foreach ($scores[$dimension]['Feedback'][$category] as $response)
					array_push($otherFeedback, $response);
				unset($scores[$dimension]['Feedback'][$category]);
			}
			$scores[$dimension]['Feedback']['Others'] = $otherFeedback;
		}

		return view('reports.360ctca', compact('user', 'scores'));
	}

	public function aoepas($jobId, $userId, $report, $export)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$ability = $assignments->where('assessment_id', $report->ability_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$ability = $user->lastCompletedAssignmentForJob($report->ability_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);

		$abilityScore = $s->score($ability->id, $job->id);
		$abilityTotal = $ability->assessment()->questions()->count();
		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);

		// Setup the zones for the chart graph
		$zones = [
			'value' => [],
			'color' => [],
		];
		$abilityDivisions = $job->weights->where('assessment_id', $report->ability_id)->first()->divisions;
//		dd($abilityDivisions);
		foreach ($abilityDivisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $zones['value']))
				array_push($zones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $zones['value']))
				array_push($zones['value'], $division['max']);
		}
		sort($zones['value']);
		$zones['color'][0] = '#E32731';
		$zones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($zones['value'], $abilityTotal);
		array_push($zones['color'], '#30BD21');

		$scores = [
			'Ability' => [
				'Score' => $abilityScore,
				'Total' => $abilityTotal,
				'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
				'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
				'Percentile' => $this->getAbilityPercentile($abilityScore),
				'Floor' => $this->getAbilityFloor($abilityScore),
				'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $zones,
			],
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
		];

		return view('reports.aoepas', compact('job', 'user', 'scores', 'export'));
	}

	public function aoes($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);

		$scores = [
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
		];

		return view('reports.s', compact('job', 'user', 'scores'));
	}

	public function evonikps($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);

		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
		];

		return view('reports.evonikps', compact('job', 'user', 'scores'));
	}

	public function risk66ps($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);

		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
		];

		return view('reports.risk66ps', compact('job', 'user', 'scores'));
	}

	public function apswmo($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$ability = $assignments->where('assessment_id', $report->ability_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
//		$wmo = $assignments->where('assessment_id', $report->wmo_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$ability = $user->lastCompletedAssignmentForJob($report->ability_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);
		$wmo = $user->lastCompletedAssignmentForJob($report->wmo_id, $job->id);

		$abilityScore = $s->score($ability->id, $job->id);
		$abilityTotal = $ability->assessment()->questions()->count();
		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);
		$wmoScore = $s->scoreWm($wmo->id);
		$wmoTotal = $s->getWmTotal($wmo->id);

		// Setup the zones for the chart graph
		$abilityZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->ability_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['max']);
		}
		sort($abilityZones['value']);
		$abilityZones['color'][0] = '#E32731';
		$abilityZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($abilityZones['value'], $abilityTotal);
		array_push($abilityZones['color'], '#30BD21');

		$wmZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->wmo_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $wmZones['value']))
				array_push($wmZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $wmZones['value']))
				array_push($wmZones['value'], $division['max']);
		}
		sort($wmZones['value']);
		$wmZones['color'][0] = '#E32731';
		$wmZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($wmZones['value'], $wmoTotal);
		array_push($wmZones['color'], '#30BD21');

		$scores = [
			'Ability' => [
				'Score' => $abilityScore,
				'Total' => $abilityTotal,
				'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
				'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
				'Percentile' => $this->getAbilityPercentile($abilityScore),
				'Floor' => $this->getAbilityFloor($abilityScore),
				'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $abilityZones,
			],
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
			'WM' => [
				'Score' => $wmoScore,
				'Total' => $wmoTotal,
				'Accuracy' => number_format(($wmoScore / $wmoTotal) * 100, 0),
				'Division' => $s->getScoreDivision($wmo->id, $job->id, $wmoScore),
				'Percentile' => $this->getOspanPercentile($wmoScore),
//				'Floor' => $this->getAbilityFloor($wmoScore),
//				'Ceiling' => $this->getAbilityCeiling($wmoTotal),
				'Zones' => $wmZones,
			],
		];

		return view('reports.apswmo', compact('job', 'user', 'scores'));
	}

	public function pswms($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
//		$wms = $assignments->where('assessment_id', $report->wms_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);
		$wms = $user->lastCompletedAssignmentForJob($report->wms_id, $job->id);

		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);
		$wmsScore = $s->scoreWm($wms->id);
		$wmsTotal = $s->getWmTotal($wms->id);

		$wmZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->wms_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $wmZones['value']))
				array_push($wmZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $wmZones['value']))
				array_push($wmZones['value'], $division['max']);
		}
		sort($wmZones['value']);
		$wmZones['color'][0] = '#E32731';
		$wmZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($wmZones['value'], $wmsTotal);
		array_push($wmZones['color'], '#30BD21');

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
			'WM' => [
				'Score' => $wmsScore,
				'Total' => $wmsTotal,
				'Accuracy' => number_format(($wmsScore / $wmsTotal) * 100, 0),
				'Division' => $s->getScoreDivision($wms->id, $job->id, $wmsScore),
				'Percentile' => $this->getSspanPercentile($wmsScore),
//				'Floor' => $this->getAbilityFloor($wmoScore),
//				'Ceiling' => $this->getAbilityCeiling($wmoTotal),
				'Zones' => $wmZones,
			],
		];

		return view('reports.pswms', compact('job', 'user', 'scores'));
	}

	public function pwms($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$wms = $assignments->where('assessment_id', $report->wms_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$wms = $user->lastCompletedAssignmentForJob($report->wms_id, $job->id);

		$personalityScore = $s->score($personality->id, $job->id);
		$wmsScore = $s->scoreWm($wms->id);
		$wmsTotal = $s->getWmTotal($wms->id);

		$wmZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->wms_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $wmZones['value']))
				array_push($wmZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $wmZones['value']))
				array_push($wmZones['value'], $division['max']);
		}
		sort($wmZones['value']);
		$wmZones['color'][0] = '#E32731';
		$wmZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($wmZones['value'], $wmsTotal);
		array_push($wmZones['color'], '#30BD21');

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'WM' => [
				'Score' => $wmsScore,
				'Total' => $wmsTotal,
				'Accuracy' => number_format(($wmsScore / $wmsTotal) * 100, 0),
				'Division' => $s->getScoreDivision($wms->id, $job->id, $wmsScore),
				'Percentile' => $this->getSspanPercentile($wmsScore),
//				'Floor' => $this->getAbilityFloor($wmoScore),
//				'Ceiling' => $this->getAbilityCeiling($wmoTotal),
				'Zones' => $wmZones,
			],
		];

		return view('reports.pwms', compact('job', 'user', 'scores'));
	}

	public function apwms($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$wms = $assignments->where('assessment_id', $report->wms_id)->first();
		$ability = $user->lastCompletedAssignmentForJob($report->ability_id, $job->id);
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$wms = $user->lastCompletedAssignmentForJob($report->wms_id, $job->id);

		$abilityScore = $s->score($ability->id, $job->id);
		$abilityTotal = $ability->assessment()->questions()->count();
		$personalityScore = $s->score($personality->id, $job->id);
		$wmsScore = $s->scoreWm($wms->id);
		$wmsTotal = $s->getWmTotal($wms->id);

		// Setup the zones for the chart graph
		$abilityZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->ability_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $abilityZones['value']))
				array_push($abilityZones['value'], $division['max']);
		}
		sort($abilityZones['value']);
		$abilityZones['color'][0] = '#E32731';
		$abilityZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($abilityZones['value'], $abilityTotal);
		array_push($abilityZones['color'], '#30BD21');

		// Setup zones for WM
		$wmZones = [
			'value' => [],
			'color' => [],
		];
		$divisions = $job->weights->where('assessment_id', $report->wms_id)->first()->divisions;
		foreach ($divisions as $division)
		{
			if (!$division['min'] and !$division['max'])
				continue;

			if ($division['min'] and !in_array($division['min'], $wmZones['value']))
				array_push($wmZones['value'], $division['min']);

			if ($division['max'] and !in_array($division['max'], $wmZones['value']))
				array_push($wmZones['value'], $division['max']);
		}
		sort($wmZones['value']);
		$wmZones['color'][0] = '#E32731';
		$wmZones['color'][1] = '#E7B428';

		// Add in the final value for total
		array_push($wmZones['value'], $wmsTotal);
		array_push($wmZones['color'], '#30BD21');

		$scores = [
			'Ability' => [
				'Score' => $abilityScore,
				'Total' => $abilityTotal,
				'Accuracy' => number_format(($abilityScore / $abilityTotal) * 100, 0),
				'Division' => $s->getScoreDivision($ability->id, $job->id, $abilityScore),
				'Percentile' => $this->getAbilityPercentile($abilityScore),
				'Floor' => $this->getAbilityFloor($abilityScore),
				'Ceiling' => $this->getAbilityCeiling($abilityTotal),
				'Zones' => $abilityZones,
			],
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'WM' => [
				'Score' => $wmsScore,
				'Total' => $wmsTotal,
				'Accuracy' => number_format(($wmsScore / $wmsTotal) * 100, 0),
				'Division' => $s->getScoreDivision($wms->id, $job->id, $wmsScore),
				'Percentile' => $this->getSspanPercentile($wmsScore),
//				'Floor' => $this->getAbilityFloor($wmoScore),
//				'Ceiling' => $this->getAbilityCeiling($wmoTotal),
				'Zones' => $wmZones,
			],
		];

		return view('reports.apwms', compact('job', 'user', 'scores'));
	}

	public function devps($jobId, $userId, $report)
	{
		$job = Job::findOrFail($jobId);
		$user = User::find($userId);
		$assignments = $user->assignments;
		$s = new ScoringController();

		// Get the assignments that we'll be scoring
//		$personality = $assignments->where('assessment_id', $report->personality_id)->first();
//		$safety = $assignments->where('assessment_id', $report->safety_id)->first();
//		$wmo = $assignments->where('assessment_id', $report->wmo_id)->first();
		$personality = $user->lastCompletedAssignmentForJob($report->personality_id, $job->id);
		$safety = $user->lastCompletedAssignmentForJob($report->safety_id, $job->id);

		$personalityScore = $s->score($personality->id, $job->id);
		$safetyScore = $s->score($safety->id, $job->id);
//		$wmoScore = $s->scoreWm($wmo->id, $job->id);
//		$wmoTotal = $s->getWmTotal($wmo->id);

//		$wmZones = [
//			'value' => [],
//			'color' => [],
//		];
//		$divisions = $job->weights->where('assessment_id', $report->wmo_id)->first()->divisions;
//		foreach ($divisions as $division)
//		{
//			if (!$division['min'] and !$division['max'])
//				continue;
//
//			if ($division['min'] and !in_array($division['min'], $wmZones['value']))
//				array_push($wmZones['value'], $division['min']);
//
//			if ($division['max'] and !in_array($division['max'], $wmZones['value']))
//				array_push($wmZones['value'], $division['max']);
//		}
//		sort($wmZones['value']);
//		$wmZones['color'][0] = '#E32731';
//		$wmZones['color'][1] = '#E7B428';
//
//		// Add in the final value for total
//		array_push($wmZones['value'], $wmoTotal);
//		array_push($wmZones['color'], '#30BD21');

		$scores = [
			'Personality' => [
				'Honesty-Humility' => number_format($this->getScoreForDimension($personality->id, 1), 2),
				'Emotional Control' => number_format($this->getScoreForDimension($personality->id, 2), 2),
				'Extraversion' => number_format($this->getScoreForDimension($personality->id, 3), 2),
				'Agreeableness' => number_format($this->getScoreForDimension($personality->id, 4), 2),
				'Conscientiousness' => number_format($this->getScoreForDimension($personality->id, 5), 2),
				'Openness' => number_format($this->getScoreForDimension($personality->id, 6), 2),
				'Fairness' => number_format($this->getScoreForDimension($personality->id, 7), 1),
				'Greed Avoidance' => number_format($this->getScoreForDimension($personality->id, 8), 1),
				'Modesty' => number_format($this->getScoreForDimension($personality->id, 9), 1),
				'Sincerity' => number_format($this->getScoreForDimension($personality->id, 10), 1),
				'Composure' => number_format($this->getScoreForDimension($personality->id, 11), 1),
				'Fearlessness' => number_format($this->getScoreForDimension($personality->id, 12), 1),
				'Independence' => number_format($this->getScoreForDimension($personality->id, 13), 1),
				'Stoical' => number_format($this->getScoreForDimension($personality->id, 14), 1),
				'Liveliness' => number_format($this->getScoreForDimension($personality->id, 15), 1),
				'Social Boldness' => number_format($this->getScoreForDimension($personality->id, 16), 1),
				'Self-Esteem' => number_format($this->getScoreForDimension($personality->id, 17), 1),
				'Sociability' => number_format($this->getScoreForDimension($personality->id, 18), 1),
				'Flexibility' => number_format($this->getScoreForDimension($personality->id, 19), 1),
				'Forgiveness' => number_format($this->getScoreForDimension($personality->id, 20), 1),
				'Patience' => number_format($this->getScoreForDimension($personality->id, 21), 1),
				'Gentleness' => number_format($this->getScoreForDimension($personality->id, 22), 1),
				'Achievement' => number_format($this->getScoreForDimension($personality->id, 23), 1),
				'Detailed' => number_format($this->getScoreForDimension($personality->id, 24), 1),
				'Organization' => number_format($this->getScoreForDimension($personality->id, 25), 1),
				'Prudence' => number_format($this->getScoreForDimension($personality->id, 26), 1),
				'Aesthetic Appreciation' => number_format($this->getScoreForDimension($personality->id, 27), 1),
				'Creativity' => number_format($this->getScoreForDimension($personality->id, 28), 1),
				'Inquisitiveness' => number_format($this->getScoreForDimension($personality->id, 29), 1),
				'Unconventionality' => number_format($this->getScoreForDimension($personality->id, 30), 1),
				'Overall' => $personalityScore,
				'Division' => $s->getScoreDivision($personality->id, $job->id, $personalityScore)
			],
			'Safety' => [
				'Confidence' => number_format($this->getScoreForDimension($safety->id, 31), 2),
				'Focus' => number_format($this->getScoreForDimension($safety->id, 32), 2),
				'Control' => number_format($this->getScoreForDimension($safety->id, 33), 2),
				'Safety Knowledge' => number_format($this->getScoreForDimension($safety->id, 34), 2),
				'Safety Motivation' => number_format($this->getScoreForDimension($safety->id, 35), 2),
				'Risk-Taking' => number_format($this->getScoreForDimension($safety->id, 36), 2),
				'Overall' => $safetyScore,
				'Division' => $s->getScoreDivision($safety->id, $job->id, $safetyScore)
			],
//			'WM' => [
//				'Score' => $wmoScore,
//				'Total' => $wmoTotal,
//				'Accuracy' => number_format(($wmoScore / $wmoTotal) * 100, 0),
//				'Division' => $s->getScoreDivision($wmo->id, $job->id, $wmoScore),
//				'Percentile' => $this->getOspanPercentile($wmoScore),
//				'Zones' => $wmZones,
//			],
		];

		return view('reports.devps', compact('job', 'user', 'scores'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param $id
	 * @return \Illuminate\Http\Response
	 */

	public function reportsIndex($id)
	{
		$client = Client::findOrFail($id);
		$reports = Report::where('client_id', $id)->get();

		return view('dashboard.reports.index', compact('reports', 'client', 'clientReports'));
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

		$jobs = $client->jobs;
		$jobsArray = [null => 'All Assignments'];
		foreach ($jobs as $job)
			$jobsArray[$job->id] = $job->name;

		$assessments = Assessment::all()->filter(function($assessment) {
			return in_array($assessment->id, $this->availableTemplates);
		});

		return view('dashboard.reports.create', compact('client', 'jobsArray', 'assessments'));
	}

    /**
     * Store a newly created resource in storage.
     *
	 * @param $clientId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($clientId, Request $request)
    {
    	$data = $request->all();
    	$data['client_id'] = $clientId;
    	$data['assessments'] = \GuzzleHttp\json_encode($data['assessments']);

    	$report = new Report($data);
    	$report->save();

		Session::flash('success', 'New report '.$report->name.' created successfully!');
		return \Response::json([
			'success' => true,
			'clientId' => $clientId,
			'reportId' => $report->id,
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $userId, $export = false)
    {
		$report = Report::findOrFail($id);
		$client = Client::findOrFail($report->client_id);
		$job = Job::findOrFail($report->job_id);
		$user = User::findOrFail($userId);
		$assessments = $report->getAssessments();
		if ($report->fields) $report->fields = json_decode($report->fields);

		$scorer = new ScoringController();
		$scores = $scorer->getUserScoresForReport($report->id, $user->id);

		if ($export)
			return view('dashboard.reports.partials._report', compact('report', 'client', 'job', 'assessments', 'user', 'scores', 'export'));

		return view('dashboard.reports.show', compact('report', 'client', 'job', 'assessments', 'user', 'scores', 'export'));
    }

	public function showSample($name)
	{
		$id = null;
		if ($name == 'personality') $id = get_global('personality');
		if ($name == 'wmo') $id = get_global('ospan');
		if ($name == 'wms') $id = get_global('ospan');

		if (! $id)
			return view('error', ['message' => 'Looks like we don\'t have a sample report for that assessment.']);

		$assessments = [Assessment::find($id)];
		$export = false;
		$report = new Report(['score_method' => 1, 'show_fit' => 1]);
		$user = new User(['name' => 'Sample Person']);
		$job = new Job(['name' => 'Supervisor/Manager']);
		$scoringController = new \App\Http\Controllers\ScoringController();
		$scores[$id] = $scoringController->getScoreDefaults($id);
		if ($name == 'wmo' || $name == 'wms')
			$scores[$id]['zones'] = $scoringController->getZones(null, $id, 1);

		return view('dashboard.reports.show', compact('report', 'job', 'assessments', 'user', 'scores', 'export'));
    }

	public function downloadReport($id, $userId)
	{
		$user = User::findOrFail($userId);
		$headers = ['Content-Type: application/pdf'];
		$filename = "Report for " . $user->name . ".pdf";
		$dir = $_SERVER['DOCUMENT_ROOT'].'/../storage/exports';
		$pdf = new PDF($_SERVER['DOCUMENT_ROOT'].'/../wkhtmltox/bin/wkhtmltopdf');

		$html = $this->show($id, $userId, true)->render();
		$pdf->loadHTML($html)->save($filename, new Local($dir), true);

		return response()->download($dir.'/'.$filename, $filename, $headers);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param  int  $reportId
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $reportId)
    {
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);

		$jobs = $client->jobs;
		$jobsArray = [null => 'All Assignments'];
		foreach ($jobs as $job)
			$jobsArray[$job->id] = $job->name;

		$assessments = Assessment::all()->filter(function($assessment) {
			return in_array($assessment->id, $this->availableTemplates);
		});

		return view('dashboard.reports.edit', compact('client', 'jobsArray', 'assessments', 'assessments', 'report'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $reportId
     * @return \Illuminate\Http\Response
     */
    public function update($id, $reportId, Request $request)
    {
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$data = $request->all();
		$data['assessments'] = \GuzzleHttp\json_encode($data['assessments']);
		$data['scores'] = null;

		$report->update($data);

		Session::flash('success', 'Report '.$report->name.' updated successfully!');
		return \Response::json([
			'success' => true,
			'clientId' => $client->id,
			'reportId' => $report->id,
		]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  int  $reportId
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $reportId)
    {
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$report->delete();

		return redirect('dashboard/clients/'.$client->id.'/reports')->with('success', 'Report '.$report->name.' deleted successfully!');
    }

	/**
	 * Enable or disable a report.
	 *
	 * @param $id
	 * @param $reportId
	 * @param Request $request
	 */
	public function toggleVisibility($id, $reportId, Request $request)
	{
		$client = Client::findorFail($id);
		$report = Report::findOrFail($reportId);
		$data = $request->all();

		if (array_key_exists('enabled', $data))
			$report->enabled = $data['enabled'];

		if (array_key_exists('visible', $data))
			$report->visible = $data['visible'];

		$report->save();
	}

	/**
	 * Show the form for customizing the specified resource.
	 *
	 * @param  int  $id
	 * @param  int  $reportId
	 * @return \Illuminate\Http\Response
	 */
	public function customize($id, $reportId)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$edit = true;
		$assessments = $report->getAssessments();
		if ($report->fields) $report->fields = json_decode($report->fields);

		return view('dashboard.reports.customize', compact('client', 'report', 'assessments', 'edit'));
	}

	/**
	 * Update the customizations for the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @param  int  $reportId
	 * @return \Illuminate\Http\Response
	 */
	public function updateCustomizations($id, $reportId, Request $request)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$data = $request->all();

		$report->update([
			'fields' => \GuzzleHttp\json_encode($data['fields'])
		]);

		return redirect('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/customize')->with('success', 'Report '.$report->name.' customizations updated successfully!');
	}

	/**
	 * Reset the customizations for the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @param  int  $reportId
	 * @return \Illuminate\Http\Response
	 */
	public function resetCustomizations($id, $reportId, Request $request)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);

		$report->update([
			'fields' => null,
			'enabled' => 0,
			'visible' => 0
		]);

		return redirect('dashboard/clients/'.$client->id.'/reports')->with('success', 'Report '.$report->name.' customizations reset successfully!');
	}

	public function weighting($id, $reportId)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$assessments = $report->getAssessments();

		return view('dashboard.reports.weighting', compact('client', 'report', 'assessments'));
	}

	public function updateWeighting($id, $reportId, Request $request)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$data = $request->all();

		$reportWeights = [];
		$reportDivisions = [];
		foreach ($data['divisions'] as $i => $divisions)
		{
			// Get the input
			$dimensions = (array_key_exists('dimension', $data) ? (array_key_exists($i, $data['dimension']) ? $data['dimension'][$i] : null) : null);
			$weights = (array_key_exists('weight', $data) ? (array_key_exists($i, $data['weight']) ? $data['weight'][$i] : null) : null);
			$total = (array_key_exists('total', $data) ? (array_key_exists($i, $data['total']) ? $data['total'][$i] : null) : null);

			// Default values
			$reportWeights[$i] = null;
			$reportDivisions[$i] = null;

			// Divisions
			if ($divisions)
				$reportDivisions[$i] = $divisions;

			// Dimension weights
			if ($dimensions)
			{
				$reportWeights[$i] = [];
				foreach ($dimensions as $j => $dimensionId)
					$reportWeights[$i][$dimensionId] = $weights[$j];
			}
		}

		$report->weights = json_encode($reportWeights);
		$report->divisions = json_encode($reportDivisions);
		$report->scores = null;
		$report->save();

		return redirect('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/weighting')->with('success', 'Report weighting updated successfully!');
	}

	public function modeling($id, $reportId)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);

		// Get our assessments
		$assessments = $report->getAssessments();
		$assessmentsArray = [];
		foreach ($assessments as $assessment)
			$assessmentsArray[$assessment->id] = $assessment->name;

		// Get our dimensions
		$dimensions = [];
		$dimensionsArray = [];
		$i = 0;
		foreach ($assessments as $assessment)
		{
			if (! $assessment->dimensions)
				continue;

			foreach ($assessment->dimensions as $dimension)
			{
				$dimensions[$i]['id'] = $dimension->id;
				$dimensions[$i]['name'] = $dimension->name;
				$i++;
				$dimensionsArray[$dimension->id] = $dimension->name;
			}
		}

		return view('dashboard.reports.modeling', compact('client', 'report', 'assessments', 'dimensions', 'dimensionsArray', 'assessmentsArray'));
	}

	public function updateModeling($id, $reportId, Request $request)
	{
		$client = Client::findOrFail($id);
		$report = Report::findOrFail($reportId);
		$data = $request->all();

		// New file is being uploaded
		if (array_key_exists('file', $data))
		{
			$validator = Validator::make($data, [
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

			$report->update([
				'model' => $xml,
				'model_filename' => $request->file('file')->getClientOriginalName(),
				'model_factors' => $factors,
				'model_configured' => 0
			]);

			return redirect('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/modeling')->with('success', 'Report model updated successfully!');
		}

		// Update the factors
		$factors = [];
		foreach ($report->model_factors as $i => $factor)
		{
			$factors[$i]['name'] = $factor->name;
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

		$report->update([
			'model_factors' => $factors,
			'model_configured' => $configured,
		]);

		return redirect('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/modeling')->with('success', 'Report model updated successfully!');
	}

	/**
     * Get an array of scores for the following assignments.
     *
     * @param $assignment_ids
     * @param $dimension_id
     * @return int|mixed
     */
    public function getScoresArray($assignment_ids, $dimension_id)
    {
        // Recursively calculate scores for multiple assignments at once, getting the average between them
        if (is_array($assignment_ids))
        {
            $scoresArray = [];
            foreach ($assignment_ids as $assignmentId)
            {
                $score = $this->getScoreForDimension($assignmentId, $dimension_id);
                array_push($scoresArray, $score);
            }

            return $scoresArray;
        }
    }

	/**
     * Get the average score of all answers for a specific dimension.
     *
     * @param $assignment_id
     * @param $dimension_id
     * @return int
     */
    public function getScoreForDimension($assignment_id, $dimension_id)
    {
        // Calculate scores for one assignment, as normal
        $assignment = Assignment::find($assignment_id);
        $dimension = Dimension::find($dimension_id);

        // Get id of dimension
        $dimension_ids = [$dimension->id];

        // If parent, get ids of child dimensions
        if ($dimension->isParent())
        {
            $dimension_ids = [];
            foreach ($dimension->getChildren() as $dimension)
                array_push($dimension_ids, $dimension->id);
        }

        // Start a new array of answers
        $answersArray = [];

        // For each dimension, add answers to our array
        // This one calculates the average of all dimensions at once to get the parent dimension score
//        foreach ($dimension_ids as $dimension_id)
//        {
//            $answers = $assignment->answers()->get()->filter(function($answer) use ($dimension_id) {
//                $question = Question::find($answer->question_id);
//                return $question->dimension_id == $dimension_id;
//            });
//            foreach ($answers as $answer)
//                array_push($answersArray, $answer);
//        }
//
//        // Collect our answers
//        $answers = collect($answersArray);
//
//        return $this->getScoreAverage($answers);

        // New method - For each dimension, add answers to our array
        // This one calculates the average of each dimension separately, then averages the averages together, to get the parent dimension score
        foreach ($dimension_ids as $i => $dimension_id)
        {
            $answers = $assignment->answers()->get()->filter(function($answer) use ($dimension_id) {
                $question = Question::find($answer->question_id);
                if (! $question)
                	return false;
                return $question->dimension_id == $dimension_id;
            });
            $answersArray[$i] = [];
            foreach ($answers as $answer)
                array_push($answersArray[$i], $answer);
        }

        $scores = 0;
        foreach ($answersArray as $answers)
        {
            // Collect our answers
            $answers = collect($answers);
            $scores += $this->getScoreAverage($answers);
        }

        return $scores/count($answersArray);
    }

	/**
     * Get the average score from answers.
     *
     * @param $answers
     * @return int
     */
    public function getScoreAverage($answers)
    {
        $score = 0;

        if ($answers->count() <= 0)
            return $score;

        foreach ($answers as $answer)
            $score += $answer->score();

        $score /= $answers->count();

        return $score;
    }

    /**
     * Get the top average score from multiple assignments of all answers for a specific dimension.
     *
     * @param $assignment_id
     * @param $dimension_id
     * @return int
     */
    public function getTopScoreForDimension($assignment_id, $dimension_id)
    {
        // Recursively calculate scores for multiple assignments at once, getting the top score between them
        if (is_array($assignment_id))
        {
            $scoresArray = [];
            foreach ($assignment_id as $id)
            {
                $score = $this->getScoreForDimension($id, $dimension_id);
                array_push($scoresArray, $score);
            }

            $topScore = 0;
            foreach ($scoresArray as $score)
                if ($score > $topScore)
                    $topScore = $score;

            return $topScore;
        }

        return false;
    }

	/**
     * Get the overall score. EVONIK SPECIFIC
     *
     * @param $assignment_id
     * @return int
     */
    public function getOverallScore($assignment_id)
    {
        // Custom Evonik weights
        $weights = [
            1 => 0.2,
            2 => 0.25,
            3 => 0.1,
            4 => 0.16,
            5 => 0.25,
            6 => 0.04,
        ];

        return $this->getScoreForDimension($assignment_id, 1) * $weights[1]
            + $this->getScoreForDimension($assignment_id, 2) * $weights[2]
            + $this->getScoreForDimension($assignment_id, 3) * $weights[3]
            + $this->getScoreForDimension($assignment_id, 4) * $weights[4]
            + $this->getScoreForDimension($assignment_id, 5) * $weights[5]
            + $this->getScoreForDimension($assignment_id, 6) * $weights[6];
    }

    /**
     * Get the overall score. CACIQUE SPECIFIC
     *
     * @param $assignment
     * @param $assignmentIds
     * @return int
     * @internal param $assignment_ids
     */
    public function getOverallLeaderScore($assignment, $assignmentIds)
    {
        $dimensions = $assignment->assessment()->dimensions;
        $overallScore = 0;
        $count = 0;

        // Get scores for all parent dimensions for a specific assessment
        foreach ($dimensions as $dimension)
        {
            if ($dimension->isChild())
                continue;

            $overallScore += array_average($this->getScoresArray($assignmentIds, $dimension->id));
            $count++;
        }
        $overallScore /= $count;

        return $overallScore;
    }

	/**
     * Get the division. EVONIK SPECIFIC
     *
     * @param $overallScore
     * @return int
     */
    public function getPersonalityDivision($overallScore)
    {
        if ($overallScore < 3.44)
            return 1;

        else if ($overallScore >= 3.44 and $overallScore < 3.56)
            return 2;

        else if ($overallScore >= 3.56 and $overallScore < 3.68)
            return 3;

        else if ($overallScore >= 3.68 and $overallScore < 3.87)
            return 4;

        else if ($overallScore >= 3.87)
            return 5;
    }

    public function getAptitudeDivision($overallScore)
    {
        if ($overallScore < 26)
            return 1;

        else if ($overallScore >= 26 and $overallScore < 32)
            return 2;

        else if ($overallScore >= 32)
            return 3;
    }

    public function getAbilityDivision($overallScore)
    {
        if ($overallScore <= 20)
            return 1;

        else if ($overallScore > 20 and $overallScore <= 26)
            return 2;

        else if ($overallScore > 26)
            return 3;
    }

    // CACIQUE SPECIFIC
    public function getLeaderDivision($overallScore)
    {
        if ($overallScore <= 2.5)
            return 3;

        else if ($overallScore > 2.5 and $overallScore <= 3.5)
            return 2;

        else if ($overallScore > 3.5)
            return 1;
    }

    public function getTotalScore($assignment_id)
    {
        $assignment = Assignment::find($assignment_id);

        $answers = $assignment->answers;

        if (! $answers)
            return 0;

        $score = 0;
        foreach ($answers as $answer)
            $score += $answer->score();

        return $score;
    }

    public function getAptitudePercentile($overallScore)
    {
        $percentiles = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
            14 => 1,
            15 => 1,
            16 => 1,
            17 => 1,
            18 => 1,
            19 => 1,
            20 => 2,
            21 => 2,
            22 => 4,
            23 => 5,
            24 => 7,
            25 => 9,
            26 => 11,
            27 => 16,
            28 => 23,
            29 => 29,
            30 => 42,
            31 => 56,
            32 => 74,
            33 => 84,
            34 => 94,
            35 => 99,
            36 => 100,
            37 => 100,
            38 => 100,
            39 => 100,
            40 => 100,
            41 => 100
        ];

        if (! array_key_exists(intval($overallScore), $percentiles))
            return 0;

        return $percentiles[intval($overallScore)];
    }

    public function getAbilityPercentile($overallScore)
    {
        $percentiles = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 1,
            11 => 2,
            12 => 2,
            13 => 3,
            14 => 3,
            15 => 3,
            16 => 4,
            17 => 7,
            18 => 10,
            19 => 15,
            20 => 21,
            21 => 26,
            22 => 30,
            23 => 34,
            24 => 40,
            25 => 44,
            26 => 48,
            27 => 55,
            28 => 60,
            29 => 63,
            30 => 68,
            31 => 72,
            32 => 76,
            33 => 80,
            34 => 85,
            35 => 86,
            36 => 89,
            37 => 90,
            38 => 91,
            39 => 93,
            40 => 94,
            41 => 96,
            42 => 97,
            43 => 98,
            44 => 99,
            45 => 99,
            46 => 99,
            47 => 99,
            48 => 99,
            49 => 99,
            50 => 100,
            51 => 100,
            52 => 100,
            53 => 100,
            54 => 100,
            55 => 100,
            56 => 100,
            57 => 100,
            58 => 100,
            59 => 100,
            60 => 100
        ];

        if (! array_key_exists(intval($overallScore), $percentiles))
            return 0;

        return $percentiles[intval($overallScore)];
    }

    public function getOspanPercentile($overallScore)
    {
        $percentiles = [
			0 => 3.91,
			1 => 4.47,
			2 => 4.47,
			3 => 5.03,
			4 => 5.59,
			5 => 6.15,
			6 => 6.7,
			7 => 6.7,
			8 => 7.82,
			9 => 9.5,
			10 => 11.17,
			11 => 11.73,
			12 => 12.85,
			13 => 13.41,
			14 => 13.97,
			15 => 15.08,
			16 => 15.64,
			17 => 15.64,
			18 => 16.2,
			19 => 18.44,
			20 => 20.67,
			21 => 20.67,
			22 => 22.91,
			23 => 23.46,
			24 => 24.02,
			25 => 24.58,
			26 => 25.7,
			27 => 31.28,
			28 => 34.08,
			29 => 35.2,
			30 => 40.78,
			31 => 41.9,
			32 => 43.58,
			33 => 46.37,
			34 => 48.04,
			35 => 51.96,
			36 => 54.75,
			37 => 56.98,
			38 => 59.78,
			39 => 61.45,
			40 => 64.8,
			41 => 67.6,
			42 => 70.95,
			43 => 77.09,
			44 => 78.77,
			45 => 81.56,
			46 => 85.47,
			47 => 88.83,
			48 => 91.06,
			49 => 92.74,
			50 => 100
        ];

        if (! array_key_exists(intval($overallScore), $percentiles))
            return 0;

        return $percentiles[intval($overallScore)];
    }

    public function getSspanPercentile($overallScore)
    {
        $percentiles = [
            0 => 0,
			1 => 7.26,
			2 => 10.61,
			3 => 11.73,
			4 => 12.85,
			5 => 13.41,
			6 => 15.64,
			7 => 17.88,
			8 => 18.99,
			9 => 21.79,
			10 => 25.14,
			11 => 26.82,
			12 => 29.61,
			13 => 35.2,
			14 => 36.31,
			15 => 39.66,
			16 => 42.46,
			17 => 44.13,
			18 => 49.16,
			19 => 51.4,
			20 => 55.31,
			21 => 58.1,
			22 => 62.01,
			23 => 64.25,
			24 => 67.6,
			25 => 68.72,
			26 => 70.39,
			27 => 73.18,
			28 => 75.42,
			29 => 77.65,
			30 => 82.68,
			31 => 84.36,
			32 => 84.92,
			33 => 88.27,
			34 => 89.94,
			35 => 91.06,
			36 => 91.62,
			37 => 91.62,
			38 => 92.18,
			39 => 93.3,
			40 => 94.41,
			41 => 94.97,
			42 => 94.97,
			43 => 95.53,
			44 => 95.53,
			45 => 95.53,
			46 => 96.09,
			47 => 96.65,
			48 => 97.21,
			49 => 97.77,
			50 => 100
        ];

        if (! array_key_exists(intval($overallScore), $percentiles))
            return 0;

        return $percentiles[intval($overallScore)];
    }

    public function getAptitudeFloor($overallScore)
    {
        if ($overallScore < 3)
            return 0;

        if ($overallScore < 20)
            return $overallScore - 3;

        return 17;
    }

    public function getAptitudeCeiling($overallScore)
    {
        return 41;
    }

    public function getAbilityFloor($overallScore)
    {
        if ($overallScore < 3)
            return 0;

        if ($overallScore < 13)
            return $overallScore - 3;

        return 10;
    }

    public function getAbilityCeiling($overallScore)
    {
        if ($overallScore > 57)
            return 60;

        if ($overallScore > 47)
            return $overallScore + 3;

        return 50;
    }

	/**
	 * Predictive model.
	 *
	 * @param $clientId
	 * @param $jobId
	 * @param $userId
	 * @param $modelId
	 * @return string
	 */
	public function model($clientId, $jobId, $userId, $modelId, $export = false)
	{
		$client = Client::findOrFail($clientId);
		$job = Job::findOrFail($jobId);
		$user = User::findOrFail($userId);
		if (Auth::user()->is('reseller|client') && session('reseller'))
		{
			$db = DBConnection::getConnectionToMasterDatabase();
			$model = $db->getConnection()->table('predictive_models')->where('id', $modelId)->first();
			$model->assessments = unserialize($model->assessments);
			$model->model = json_decode($model->model);
			$model->factors = unserialize($model->factors);
		}
		else
			$model = PredictiveModel::findOrFail($modelId);
		$assignments = $user->assignments;

		// Get our divisions
		$divisions = [];
		foreach ($model->model->DataDictionary->DataField as $field)
		{
			if ($field->{'@attributes'}->dataType != 'string' || $field->{'@attributes'}->optype != 'categorical' || !isset($field->Value))
				continue;

			foreach ($field->Value as $division)
				$divisions[] = $division->{'@attributes'}->value;
		}

		// Get the scores to the various factors
		$scores = [];
		foreach ($model->factors as $factor)
		{
			// Assessment
			if ($factor['type'] == 'assessment')
			{
				$assessment = Assessment::findOrFail($factor['id']);
				$s = new ScoringController();
				$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $job->id);
				$score = $s->score($assignment->id, $job->id);

				$scores[$factor['name']] = $score;
			}

			// Dimension
			if ($factor['type'] == 'dimension')
			{
				$dimension = Dimension::findOrFail($factor['id']);
				$assessment = $dimension->assessment;

				// Dimensions from Personality
				if ($assessment->id == get_global('personality'))
				{
					$s = new ScoringController();
					$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $job->id);
					$score = $s->getScoreForDimension($assignment->id, $dimension->id);

					$scores[$factor['name']] = $score;
					continue;
				}
			}
		}

		// Check to make sure we don't have any missing factors
		$missingFactors = [];
		foreach ($model->factors as $factor)
		{
			if (! array_key_exists($factor['name'], $scores))
				$missingFactors[] = $factor['name'];
		}
		if (count($missingFactors))
			return 'Cannot execute the Decision Tree matrix. The following factors are missing or have no scores: '.implode(', ', $missingFactors).'. Make sure '.$user->name.' has completed all assessments related to this job.';

		// Store current attributes and distribution
		$currentNode = $model->model->TreeModel->Node;
		$attributes = $currentNode->{'@attributes'};
		$distribution = $currentNode->ScoreDistribution;

		// Go through the nodes
		$i = 0;
		while (isset($currentNode->Node))
		{
			// For each choice of node
			foreach ($currentNode->Node as $node)
			{
				$pass = false;
				$field = $node->SimplePredicate->{'@attributes'}->field;
				$operator = $node->SimplePredicate->{'@attributes'}->operator;
				$value = $node->SimplePredicate->{'@attributes'}->value;

				// Check if we pass the test
				switch ($operator)
				{
					case 'lessOrEqual':
						if ($scores[$field] <= $value) $pass = true;
						break;

					case 'greaterThan':
						if ($scores[$field] > $value) $pass = true;
						break;
				}

				// If so
				if ($pass)
				{
					// Adjust our distribution
					$attributes = $node->{'@attributes'};
					$distribution = $node->ScoreDistribution;

					// If next node doesn't exist, stop
					if (! isset($node->Node))
						break 2;

					// Continue
					$currentNode = $node;
					$i++;
				}
			}
		}

		// Get the stats for the current score from the distribution array
		$stats = null;
		foreach ($distribution as $category)
			if ($category->{'@attributes'}->value == $attributes->score)
			{
				$stats = $category->{'@attributes'};
				break;
			}

		$rank = $attributes->score;
		$confidence = number_format($stats->confidence * 100, 2) . '%';
		$value = 0;
		if ($rank == 'a') $value = 5;
		if ($rank == 'b') $value = 4;
		if ($rank == 'c') $value = 3;
		if ($rank == 'd') $value = 2;
		if ($rank == 'f') $value = 1;

		$factors = [];
		foreach ($model->factors as $factor)
			$factors[] = $factor['name'];

		if (Auth::user()->is('reseller|client') && session('reseller') && session('reseller')->id == get_global('risk66'))
		{
			$scores = $this->getPScores($job->id, $user->id);
			return view('reports.hire2p', compact('scores', 'user', 'job', 'scores', 'confidence', 'rank', 'value', 'export'));
		}
		elseif (Auth::user()->is('admin'))
		{
			if ($job->assessments = [get_global('personality')])
			{
				$scores = $this->getPScores($job->id, $user->id);
				return view('reports.modelp', compact('scores', 'user', 'job', 'scores', 'confidence', 'rank', 'value', 'export'));
			}
			return view('dashboard.spss.report', compact('user', 'job', 'factors', 'scores', 'confidence', 'rank', 'value', 'export'));
		}
		else
			return 'You do not have permission to view this report. Please contact an AOE Administrator.';
    }

	public function getModelDivision($clientId, $jobId, $userId, $modelId)
	{
		$client = Client::findOrFail($clientId);
		$job = Job::findOrFail($jobId);
		$user = User::findOrFail($userId);
		if (Auth::user()->is('reseller|client') && session('reseller'))
		{
			$db = DBConnection::getConnectionToMasterDatabase();
			$model = $db->getConnection()->table('predictive_models')->where('id', $modelId)->first();
			$model->assessments = unserialize($model->assessments);
			$model->model = json_decode($model->model);
			$model->factors = unserialize($model->factors);
		}
		else
			$model = PredictiveModel::findOrFail($modelId);
		$assignments = $user->assignments;

		// Get our divisions
		$divisions = [];
		foreach ($model->model->DataDictionary->DataField as $field)
		{
			if ($field->{'@attributes'}->dataType != 'string' || $field->{'@attributes'}->optype != 'categorical' || !isset($field->Value))
				continue;

			foreach ($field->Value as $division)
				$divisions[] = $division->{'@attributes'}->value;
		}

		// Get the scores to the various factors
		$scores = [];
		foreach ($model->factors as $factor)
		{
			// Assessment
			if ($factor['type'] == 'assessment')
			{
				$assessment = Assessment::findOrFail($factor['id']);
				$s = new ScoringController();
				$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $job->id);
				$score = $s->score($assignment->id, $job->id);

				$scores[$factor['name']] = $score;
			}

			// Dimension
			if ($factor['type'] == 'dimension')
			{
				$dimension = Dimension::findOrFail($factor['id']);
				$assessment = $dimension->assessment;

				// Dimensions from Personality
				if ($assessment->id == get_global('personality'))
				{
					$s = new ScoringController();
					$assignment = $user->lastCompletedAssignmentForJob($assessment->id, $job->id);
					$score = $s->getScoreForDimension($assignment->id, $dimension->id);

					$scores[$factor['name']] = $score;
					continue;
				}
			}
		}

		// Check to make sure we don't have any missing factors
		$missingFactors = [];
		foreach ($model->factors as $factor)
		{
			if (! array_key_exists($factor['name'], $scores))
				$missingFactors[] = $factor['name'];
		}
		if (count($missingFactors))
			return 'Cannot execute the Decision Tree matrix. The following factors are missing or have no scores: '.implode(', ', $missingFactors).'. Make sure '.$user->name.' has completed all assessments related to this job.';

		// Store current attributes and distribution
		$currentNode = $model->model->TreeModel->Node;
		$attributes = $currentNode->{'@attributes'};
		$distribution = $currentNode->ScoreDistribution;

		// Go through the nodes
		$i = 0;
		while (isset($currentNode->Node))
		{
			// For each choice of node
			foreach ($currentNode->Node as $node)
			{
				$pass = false;
				$field = $node->SimplePredicate->{'@attributes'}->field;
				$operator = $node->SimplePredicate->{'@attributes'}->operator;
				$value = $node->SimplePredicate->{'@attributes'}->value;

				// Check if we pass the test
				switch ($operator)
				{
					case 'lessOrEqual':
						if ($scores[$field] <= $value) $pass = true;
						break;

					case 'greaterThan':
						if ($scores[$field] > $value) $pass = true;
						break;
				}

				// If so
				if ($pass)
				{
					// Adjust our distribution
					$attributes = $node->{'@attributes'};
					$distribution = $node->ScoreDistribution;

					// If next node doesn't exist, stop
					if (! isset($node->Node))
						break 2;

					// Continue
					$currentNode = $node;
					$i++;
				}
			}
		}

		// Get the stats for the current score from the distribution array
		$stats = null;
		foreach ($distribution as $category)
			if ($category->{'@attributes'}->value == $attributes->score)
			{
				$stats = $category->{'@attributes'};
				break;
			}

		$rank = $attributes->score;
		$confidence = $stats->confidence * 100 . '%';
		$value = 0;
		if ($rank == 'a') $value = 5;
		if ($rank == 'b') $value = 4;
		if ($rank == 'c') $value = 3;
		if ($rank == 'd') $value = 2;
		if ($rank == 'f') $value = 1;

		$factors = [];
		foreach ($model->factors as $factor)
			$factors[] = $factor['name'];

		return $value;
    }

	public function download($clientId, $jobId, $userId)
	{
		$user = User::findOrFail($userId);
		$job = Job::findOrFail($jobId);

		$headers = ['Content-Type: application/pdf'];
		$filename = "Report for " . $user->name . " - " . $job->name . ".pdf";
		$dir = $_SERVER['DOCUMENT_ROOT'].'/../storage/exports';
		$pdf = new PDF($_SERVER['DOCUMENT_ROOT'].'/../wkhtmltox/bin/wkhtmltopdf');

		$reportsController = new ReportsController();
		$html = $reportsController->index($clientId, $jobId, $userId, true)->render();
		$pdf->loadHTML($html)->save($filename, new Local($dir), true);

		return response()->download($dir.'/'.$filename, $filename, $headers);
    }

	public function downloadModel($clientId, $jobId, $userId, $modelId)
	{
		$user = User::findOrFail($userId);
		$headers = ['Content-Type: application/pdf'];
		$filename = $user->name.".pdf";
		$dir = $_SERVER['DOCUMENT_ROOT'].'/../storage/exports';
		$pdf = new PDF($_SERVER['DOCUMENT_ROOT'].'/../wkhtmltox/bin/wkhtmltopdf');

		$reportsController = new ReportsController();
		$html = $reportsController->model($clientId, $jobId, $userId, $modelId, true)->render();
		$pdf->loadHTML($html)->save($filename, new Local($dir), true);

		return response()->download($dir.'/'.$filename, $filename, $headers);
	}

	/**
	 * View a customized client report for this user.
	 *
	 * @param $id
	 * @param $clientReportId
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function clientReport($id, $clientReportId)
	{
		$user = User::findOrFail($id);
		$clientReport = ClientReport::findOrFail($clientReportId);
		$job = $clientReport->job;
		$report = $clientReport->report;
		$report->fields = json_decode($report->fields);
		$scores = $this->score($report);

		return view('dashboard.reports.show', compact('clientReport', 'report', 'user', 'job'));
    }

	public function score($report)
	{
		$scores = [];
		$assessments = json_decode($report->assessments);

		var_dump($assessments);
		die();

		return $scores;
    }
}
