<h3>Hello, {{ $user->name }}</h3>

<p>
    This is a reminder that you have a pending assessment that needs to be completed.
</p>

<p>
    Assessment: {{ $assessment->name }}<br/>
    Expires: {{ $expires->format('l, F jS, Y') }}<br/>
    @if($days_remaining > 1)
        Time Remaining: {{ $days_remaining }} days
    @elseif($days_remaining == 1)
        Time Remaining: 1 day
    @elseif($days_remaining == 0)
        <b>This assessment expires today!</b>
    @else
        <b>This assessment is overdue!</b>
    @endif
</p>

<p>
    Login <a target="_blank" href="{{ $assignments_link }}">here</a> to complete your assessment. You can use the following credentials:<br/>
    username: <i>{{ $user->username }}</i><br/>
    password: <i>{{ $user->generate_password_for_user() }}</i>
</p>

<br/>
<div class="footer-text">&copy; {{ date('Y') }} Involved Talent</div>

