<h3>Hello, {{ $user->name }}</h3>

<p>
    This is a friendly reminder that you have a pending assessment that needs to be completed.
</p>

<p>
    <strong>Assessment:</strong> {{ $assessment->name }}<br/>
    <strong>Expires:</strong> {{ $expires->format('l, F jS, Y') }}<br/>
    @if($days_remaining > 1)
        <strong>Time Remaining:</strong> {{ $days_remaining }} days
    @elseif($days_remaining == 1)
        <strong>Time Remaining:</strong> 1 day
    @elseif($days_remaining == 0)
        <strong>⚠️ This assessment expires today!</strong>
    @else
        <strong>⚠️ This assessment is overdue!</strong>
    @endif
</p>

<p>
    Login <a target="_blank" href="{{ $assignments_link }}">here</a> to complete your assessment. You can use the following credentials:<br/>
    username: <i>{{ $user->username }}</i><br/>
    password: <i>{{ $user->generate_password_for_user() }}</i>
</p>

<br/>
<div class="footer-text">&copy; {{ date('Y') }} Involved Talent</div>

