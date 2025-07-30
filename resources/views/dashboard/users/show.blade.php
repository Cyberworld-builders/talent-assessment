@extends('dashboard.dashboard')

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    <section class="profile-env">
        <div class="row">
            <div class="col-sm-3">

                {{-- Sidebar --}}
                <div class="user-info-sidebar">

                    {{-- User Info --}}
                    <a href="#" class="user-img">
                        <img src="{{ url('assets/images/user-4.png') }}" alt="user-img" class="img-cirlce img-responsive img-thumbnail" />
                    </a>
                    <a href="#" class="user-name">{{ $user->name }}</a>
                    @if (isset($reseller))
                        <span class="user-title">{{ $user->role_name }}</span>
                    @else
                        <span class="user-title">{{ $user->roles->first()->name }}</span>
                    @endif
                    <hr />

                    {{-- Credentials --}}
                    <ul class="list-unstyled user-info-list">
                        <li>
                            <i class="fa-user"></i>
                            {{ $user->username }}
                        </li>
                        @if ($user->email)
                            <li>
                                <i class="fa-envelope"></i>
                                <a href="#">{{ $user->email }}</a>
                            </li>
                        @endif
                        @if ($user->client)
                            <li>
                                <i class="fa-building"></i>
                                {{ $user->client->name }}
                            </li>
                        @endif
                        @if ($user->job_title)
                            <li>
                                <i class="fa-briefcase"></i>
                                {{ $user->job_title }}
                            </li>
                        @endif
                        @if ($user->job_family)
                            <li>
                                <i class="fa-group"></i>
                                {{ $user->job_family }}
                            </li>
                        @endif
                    </ul>
                    <hr />

                    {{-- Assignments Count --}}
                    <ul class="list-unstyled user-friends-count">
                        <li>
                            <span>{{ count($user->assignments) }}</span>
                            Assignments
                        </li>
                        <li>
                            <?php
                                $answers = 0;
                                if ($user->assignments)
                                    foreach ($user->assignments as $assignment)
                                        $answers += count($assignment->answers);
                            ?>
                            <span>{{ $answers }}</span>
                            Answers
                        </li>
                    </ul>
                    <hr><br>

                    {{-- Controls --}}
                    <div style="text-align: center;">
                        @if (isset($reseller))
                            <a class="btn btn-black" href="/dashboard/resellers/{{ $reseller->id }}/users/{{ $user->id }}/edit" style="padding: 8px 39px"><i class="fa-pencil"></i> Edit User</a><br>
                        @else
                            <a class="btn btn-black" href="/dashboard/users/{{ $user->id }}/edit" style="padding: 8px 39px"><i class="fa-pencil"></i> Edit User</a><br>
                        @endif
                        @if (! isset($reseller) || (isset($reseller) && $user->role_name == "Reseller"))
                            <a class="btn btn-success log-in-as-user" style="padding: 8px 25px"><i class="fa-lock"></i> Log In As User</a>
                        @endif
                    </div>
                </div>

            </div>

            <div class="col-sm-9">

                {{-- Assignments --}}
                <section class="user-timeline-stories">
                    <article class="timeline-story">
                        <i class="fa-paper-plane-empty block-icon"></i>
                        <header>
                            <h3>Assignments</h3>
                        </header>

                        @if (count($user->assignments))
                            <table class="table table-hover members-table middle-align">
                                <tbody>
                                    @foreach ($user->assignments as $assignment)

                                        <tr style="margin-top:3px;">

                                            <!-- Assignment Name -->
                                            <td class="user-name">
                                                @if (isset($reseller) && $assignment->assessment)
                                                    <a class="name">{{ $assignment->assessment->name }}</a>
                                                @elseif (!isset($reseller) && $assignment->assessment())
                                                    <a class="name" href="/dashboard/assignments/{{ $assignment->id }}/details">{{ $assignment->assessment()->name }}</a>
                                                @else
                                                    <div>
                                                        <span class="text-danger">
                                                            <i class="fa-exclamation-circle"></i> <i>Assessment Not Found</i>
                                                        </span>
                                                    </div>
                                                @endif
                                                <span>
                                                    Assigned on {{ $assignment->created_at->format('M d Y - h:i:s') }}
                                                    @if (! isset($reseller))
                                                        <a class="text-muted" href="/dashboard/assignments/{{ $assignment->id }}/edit">
                                                            <i class="fa-pencil"></i>
                                                        </a>
                                                    @endif
                                                </span>
                                            </td>

                                            <!-- Completion Status -->
                                            <td class="action-links">
                                                @if ($assignment->completed)
                                                    <a class="edit"><i class="fa-check"></i> Completed {{ $assignment->completed_at->format('M d Y - h:i:s') }}</a>
                                                    <span style="color:#aaa;font-size:11px;"><i class="fa-clock-o"></i> Completed in {{ $assignment->completed_at->diffForHumans($assignment->started_at, true) }}</span>
                                                @else
                                                    @if (! $assignment->started_at)
                                                        <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                                    @else
                                                        <?php
                                                            $answers = $assignment->answers->count();
                                                            if (! isset($reseller))
                                                                $questions = $assignment->assessment()->questions->count();
                                                            else
																$questions = $assignment->assessment->questions->count();
                                                            $percentage = ($answers / $questions) * 100 . '%';
                                                        ?>
                                                        <div class="progress" style="width: 30%;">
                                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $answers }}" aria-valuemin="0" aria-valuemax="{{ $questions }}" style="width: {{ $percentage }}"></div>
                                                        </div>
                                                        <span style="color:#aaa;font-size:11px;">{{ $answers }} / {{ $questions }}</span>
                                                    @endif
                                                @endif
                                            </td>

                                            <!-- Expiration -->
                                            <td class="action-links">
                                                <span class="action-links">
                                                    @if ($assignment->completed)
                                                        {{--<span class="email">---</span>--}}
                                                    @else
                                                        @if ($assignment->expires > Carbon\Carbon::now())
                                                            <a class="email">Expires in {{ displayElapsedTime($assignment->expires) }}</a>
                                                        @else
                                                            <a class="" style="color: #9f9f9f;">Expired {{ $assignment->expires->diffForHumans(null, true) }} ago</a>
                                                        @endif
                                                    @endif
                                                </span>
                                            </td>

                                            <!-- Reminder Email -->
                                            {{--<td class="action-links">--}}
                                                {{--<span class="action-links">--}}
                                                    {{--@if (! $assignment->completed)--}}
                                                        {{--<a class="name" href="/dashboard/assignments/{{ $assignment->id }}/details">Re-send Assignment Email</a>--}}
                                                    {{--@endif--}}
                                                {{--</span>--}}
                                            {{--</td>--}}

                                        </tr>

                                    @endforeach

                                </tbody>
                            </table>
                        @else
                            <p>This user doesn't have any assigned assessments.</p>
                        @endif
                    </article>
                </section>

                @if (!isset($reseller))
                @role('admin')
                {{-- JAQs --}}
                <br>
                <section class="user-timeline-stories">
                    <article class="timeline-story">
                        <i class="fa-paper-plane-empty block-icon"></i>
                        <header>
                            <h3>JAQs</h3>
                        </header>

                        @if (count($user->jaqs))
                            <table class="table table-hover members-table middle-align">
                                <tbody>
                                @foreach ($user->jaqs as $jaq)

                                    <tr style="margin-top:3px;">

                                        <!-- Assignment Name -->
                                        <td class="user-name">
                                            <a class="name" href="#">{{ $jaq->analysis->name }}</a>
                                            @if ($jaq->sent)
                                                <span>Sent on {{ $jaq->sent_at->format('M d Y - h:i:s') }}</span>
                                            @else
                                                <span>Not sent yet</span>
                                            @endif
                                        </td>

                                        <!-- Completion Status -->
                                        <td class="action-links">
                                            @if ($jaq->completed)
                                                <a class="edit"><i class="fa-check"></i> Completed {{ $jaq->completed_at->format('M d Y - h:i:s') }}</a>
                                            @else
                                                <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                            @endif
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>
                            </table>
                        @else
                            <p>This user doesn't have any pending job analysis questionnaires.</p>
                        @endif
                    </article>
                </section>

                @endrole
                @endif

            </div>
        </div>
    </section>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });

            // Log in as user
            $('.log-in-as-user').on('click', function() {
                var url = '/dashboard/users/{{ $user->id }}/auth';

                if (confirm('You are about to be logged in with this user\'s credentials. \nThis will log you out of your current session. \nAre you sure you wish to continue?'))
                    window.location.href = url;
            });
        });
    </script>

@stop

@section('scripts')

@stop