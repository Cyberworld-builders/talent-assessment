<h3>Hello, {{ $user->name }}</h3>

<p>
    You have been assigned to complete the {{ $assessment->name }} assessment. This assessment will expire on {{ $expire_date->format('D, d M Y') }}.
</p>

@if ($user->level() == 1 || $mock)

    {{--<p>--}}
    {{--If the above link doesn't work, you can manually log in by visiting <a target="_blank" href="{{ $assignments_link }}">this link</a> and using the credentials: <br/>--}}
    {{--username: <i>{{ $user->email }}</i><br/>--}}
    {{--password: <i>{{ $password }}</i>--}}
    {{--</p>--}}
    <p>
        Login <a target="_blank" href="{{ $assignments_link }}">here</a> to view your assignments. You can use the following credentials:<br/>
        username: <i>{{ $user->email }}</i><br/>
        password: <i>{{ $password }}</i>
    </p>

@else

    {{--<p>--}}
    {{--If the above link doesn't work, you can log in at <a target="_blank" href="{{ $assignments_link }}">this link</a> to see all your assignments. <br/>--}}
    {{--You will need to login using the credentials you specified when you created your profile.--}}
    {{--</p>--}}
    <p>
        Login <a target="_blank" href="{{ $assignments_link }}">here</a> to view your assignments. You can use the login credentials you specified when you created your profile.
    </p>

@endif

{!! $assessment->description !!}

{{--<p>--}}
    {{--<a target="_blank" href="{{ $url }}">Click here to begin the assessment</a><br/>--}}
    {{--This link will expire on {{ $expire_date->format('D, d M Y') }}.--}}
{{--</p>--}}

<br/>
<div class="footer-text">&copy; {{ date('Y') }} The AOE Group</div>