{{--<h3>Hello, <b>{{ $user->name }}</b></h3>--}}

{{--<p>--}}
    {{--You have been assigned to complete the following assessments:<br/>--}}
    {{--@foreach ($assessments as $assessment)--}}
        {{--- {{ $assessment->name }}<br/>--}}
    {{--@endforeach--}}
{{--</p>--}}
{{--<p>--}}
    {{--<i>Note: These assignments will expire on <b>{{ $expire_date->format('D, d M Y') }}</b>.</i>--}}
{{--</p>--}}

{{--@if (($user->level() == 1 && !$user->completed_profile) || $mock)--}}

    {{--<p>--}}
        {{--Login <a target="_blank" href="{{ $assignments_link }}">here</a> to view your assignments. You can use the following credentials to log in:<br/>--}}
        {{--username: <i>{{ $user->username }}</i><br/>--}}
        {{--password: <i>{{ $password }}</i>--}}
    {{--</p>--}}

{{--@else--}}

    {{--<p>--}}
        {{--Login <a target="_blank" href="{{ $assignments_link }}">here</a> to view your assignments. You can use the login credentials you specified when you created your profile to log in.--}}
    {{--</p>--}}

{{--@endif--}}

{{--<br/>--}}
{{--<div class="footer-text">&copy; {{ date('Y') }} AOE Science</div>--}}

{!! $body !!}