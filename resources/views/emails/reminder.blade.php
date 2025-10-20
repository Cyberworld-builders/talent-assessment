Hello, {{ $user->name }}

This is a friendly reminder that you have a pending assessment that needs to be completed.

Assessment: {{ $assessment->name }}
Expires: {{ $expires->format('l, F jS, Y') }}
@if($days_remaining > 1)
Time Remaining: {{ $days_remaining }} days
@elseif($days_remaining == 1)
Time Remaining: 1 day
@elseif($days_remaining == 0)
⚠️ This assessment expires today!
@else
⚠️ This assessment is overdue!
@endif

Login here to complete your assessment: {{ $assignments_link }}

You can use the following credentials:
username: {{ $user->username }}
password: {{ $user->generate_password_for_user() }}


© {{ date('Y') }} Involved Talent

