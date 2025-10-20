<p>Hello, {{ $user->name }}</p>

<p>
    This is a friendly reminder that you have a pending assessment that needs to be completed.
</p>

<p>
    Assessment: {{ $assessment->name }}<br/>
    Expires: {{ $expires->format('l, F jS, Y') }}<br/>
    @if($days_remaining > 1)
        Time Remaining: {{ $days_remaining }} days
    @elseif($days_remaining == 1)
        Time Remaining: 1 day
    @elseif($days_remaining == 0)
        ⚠️ This assessment expires today!
    @else
        ⚠️ This assessment is overdue!
    @endif
</p>

<p>
    Login <a target="_blank" href="{{ $assignments_link }}">here</a> to complete your assessment. You can use the following credentials:<br/>
    username: {{ $user->username }}<br/>
    password: {{ $user->generate_password_for_user() }}
</p>

<br/>
<div class="footer-text">&copy; {{ date('Y') }} Involved Talent</div>

