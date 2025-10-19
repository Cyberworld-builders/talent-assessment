<?php


namespace App;

use Carbon\Carbon;
use Mail;

class Mailer {

	/**
	 * Email domain from which to send emails.
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * Global from name.
	 *
	 * @var string
	 */
	protected $from;

	/**
	 * Constructor to set up email configuration from environment variables.
	 */
	public function __construct()
	{
		$this->domain = env('MAIL_FROM_ADDRESS', 'postmaster@mg.involvedtalent.com');
		$this->from = env('MAIL_FROM_NAME', 'Involved Talent');
	}

	/**
	 * Global blind carbon copy email.
	 *
	 * @var string
	 */
	protected $bcc = 'xaonst@gmail.com';

	/**
	 * Send multiple assignments in one email to the specified user.
	 *
	 * @param $user
	 * @param Assignment $ids
	 * @param Carbon $expiration
	 * @param String $subject
	 * @param String $body
	 */
	public function send_assignments($user, $ids, $expiration, $subject, $body)
	{
		if (!$subject)
			$subject = 'New assessments have been assigned to you';
		//$assignment = Assignment::findOrFail($id);
		//$assessment = Assessment::findOrFail($assignment->assessment_id);
		$assessments = Assessment::all()->filter(function($assessment) use ($ids) {
			foreach ($ids as $id) {
				$assessment_id = Assignment::find($id)->assessment_id;
				if ($assessment->id == $assessment_id)
					return true;
				//return in_array($assessment->id, $ids);
			}
			return false;
		});

		$assessmentsList = '<ul>';
		foreach ($assessments as $assessment)
			$assessmentsList .= '<li>'.$assessment->name.'</li>';
		$assessmentsList .= '</ul>';

		// Piece together assignments link
		$server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
		if ($server == 'localhost')
			$server .= ':8000';
		if (session('reseller'))
			$server .= '/r/'.session('reseller')->id;
		$assignments_link = 'https://'.$server.'/assignments';

		$body = do_shortcodes([
			'name'             => $user->name,
			'username'         => $user->username,
			'email'            => $user->email,
			'password'         => $user->generate_password_for_user(),
			'expiration-date'  => Carbon::createFromFormat('D, d M Y', $expiration)->format('l, F jS, Y'),
			'login-link'       => $assignments_link,
			'assessments'      => $assessmentsList,
		], $body);

		$view = 'emails.assignments';
		$view_data = ['body' => $body];
//		$view_data = [
//			'user'             => $user,
//			'assessments'      => $assessments,
//			//'url'              => $assignment->url,
//			'expire_date'      => Carbon::createFromFormat('D, d M Y', $expiration),
//			'assignments_link' => $assignments_link,
//			'password'         => $user->generate_password_for_user(),
//			'body'			   => $body,
//			'mock'             => false
//		];
		//$subject = 'New assessments have been assigned to you';



//		return view($view, compact('body'));

		Mail::send($view, $view_data, function ($m) use ($user, $subject)
		{
			$m->from($this->domain, $this->from);
			$m->to($user->email, $user->name)->subject($subject);
		});
	}

	/**
	 * Send an assignment email to the specified user for a specific assignment.
	 *
	 * @param $user
	 * @param Assignment $id
	 */
	public function send_assignment($user, $id)
	{
		$assignment = Assignment::findOrFail($id);
		$assessment = Assessment::findOrFail($assignment->assessment_id);

		// Piece together assignments link
		$server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
		if ($server == 'localhost')
			$server .= ':8000';
		$assignments_link = 'https://'.$server.'/assignments';

		$view = 'emails.assignment';
		$view_data = [
			'user'             => $user,
			'assessment'       => $assessment,
			'url'              => $assignment->url,
			'expire_date'      => Carbon::parse($assignment->expires),
			'assignments_link' => $assignments_link,
			'password'         => $user->generate_password_for_user(),
			'mock'             => false
		];
		$subject = $assessment->name . ' Login Details';

		Mail::send($view, $view_data, function ($m) use ($user, $assessment, $subject)
		{
			$m->from($this->domain, $this->from);
			$m->to($user->email, $user->name)->subject($subject);
		});
	}

	/**
	 * Send a completion email to the specified user for a specific assignment.
	 *
	 * @param $user
	 * @param Assignment $id
	 */
	public function send_completed($user, $id)
	{
		$assignment = Assignment::findOrFail($id);
		$assessment = Assessment::findOrFail($assignment->assessment_id);

		$view = 'emails.completed';
		$view_data = [
			'user' => $user,
			'assessment' => $assessment
		];
		$subject = $assessment->name . ' Complete';

		Mail::send($view, $view_data, function ($m) use ($user, $assessment, $subject)
		{
			$m->from($this->domain, $this->from);
			$m->to($user->email, $user->name)->subject($subject);
		});
	}

	/**
	 * Send a questionnaire to a user.
	 * @param $user
	 * @param $jaqId
	 * @param $subject
	 * @param $body
	 * @return bool
	 */
	public function send_questionnaire($user, $jaqId, $subject, $body)
	{
		// Get our JAQ
		$jaq = Jaq::find($jaqId);
		if (! $jaq)
			return false;

		// Get our analysis
		$analysis = $jaq->analysis;
		if (! $analysis)
			return false;

		// Piece together assignments link
		$server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
		if ($server == 'localhost')
			$server .= ':8000';
		$assignments_link = 'https://'.$server.'/assignments';

		$body = do_shortcodes([
			'name'             => $user->name,
			'username'         => $user->username,
			'email'            => $user->email,
			'password'         => $user->generate_password_for_user(),
			'login-link'       => $assignments_link,
			'analysis'         => $analysis->name,
		], $body);

		$view = 'emails.assignments';
		$view_data = ['body' => $body];

		Mail::send($view, $view_data, function ($m) use ($user, $subject)
		{
			$m->from($this->domain, $this->from);
			$m->to($user->email, $user->name)->subject($subject);
		});

		// Mark the JAQ as sent
		$jaq->sent = 1;
		$jaq->sent_at = Carbon::now();
		$jaq->save();

		return true;
	}

	/**
	 * Send a reminder email for an assignment.
	 *
	 * @param Assignment $assignment
	 * @return bool
	 */
	public function send_reminder($assignment)
	{
		$user = $assignment->user;
		$assessment = $assignment->assessment();

		if (!$user || !$assessment) {
			return false;
		}

		// Piece together assignments link
		$server = $_SERVER['SERVER_NAME'] ?? parse_url(env('APP_URL'), PHP_URL_HOST);
		if ($server == 'localhost')
			$server .= ':8000';
		if (session('reseller'))
			$server .= '/r/'.session('reseller')->id;
		$assignments_link = 'https://'.$server.'/assignments';

		// Calculate days until expiration
		$expires = Carbon::parse($assignment->expires);
		$daysUntilExpiration = Carbon::now()->diffInDays($expires, false);
		
		// Build the reminder body
		$body = "This is a friendly reminder that you have a pending assessment that needs to be completed.";
		$body .= "\n\n";
		$body .= "<strong>Assessment:</strong> {$assessment->name}<br>";
		$body .= "<strong>Expires:</strong> {$expires->format('l, F jS, Y')}<br>";
		
		if ($daysUntilExpiration > 0) {
			$body .= "<strong>Time Remaining:</strong> {$daysUntilExpiration} day(s)<br>";
		} else if ($daysUntilExpiration == 0) {
			$body .= "<strong>⚠️ This assessment expires today!</strong><br>";
		} else {
			$body .= "<strong>⚠️ This assessment is overdue!</strong><br>";
		}
		
		$body .= "\n\n";
		$body .= "Please log in to complete your assessment:";

		// Apply shortcodes
		$body = do_shortcodes([
			'name'             => $user->name,
			'username'         => $user->username,
			'email'            => $user->email,
			'password'         => $user->generate_password_for_user(),
			'login-link'       => $assignments_link,
			'assessment'       => $assessment->name,
			'expiration-date'  => $expires->format('l, F jS, Y'),
			'days-remaining'   => max(0, $daysUntilExpiration),
		], $body);

		$subject = "Reminder: {$assessment->name} Assessment Due";
		
		if ($daysUntilExpiration == 0) {
			$subject = "⚠️ Urgent: {$assessment->name} Assessment Expires Today";
		} else if ($daysUntilExpiration < 0) {
			$subject = "⚠️ Overdue: {$assessment->name} Assessment";
		}

		$view = 'emails.reminder';
		$view_data = [
			'body' => $body,
			'user' => $user,
			'assessment' => $assessment,
			'assignment' => $assignment,
			'assignments_link' => $assignments_link,
			'expires' => $expires,
			'days_remaining' => max(0, $daysUntilExpiration),
		];

		Mail::send($view, $view_data, function ($m) use ($user, $subject)
		{
			$m->from($this->domain, $this->from);
			$m->to($user->email, $user->name)->subject($subject);
		});

		return true;
	}
}