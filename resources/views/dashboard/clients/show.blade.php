@extends('dashboard.dashboard')

@section('styles')
    <style>
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .user {
            padding: 20px;
            background: #F6F6F6;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .user .info {
            color: #0b97c4;
        }
        .members-table thead tr th {
            font-size: 10px;
        }
        .arrow {
            margin: 10px 23px 10px 65px;
            color: #cfcfcf;
        }
        .status {
            display: inline-block;
            vertical-align: middle;
            background: #b7b7b7;
            margin-right: 5px;
            position: relative;
            top: -1px;
            width: 8px;
            height: 8px;
            -webkit-border-radius: 8px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 8px;
            -moz-background-clip: padding;
            border-radius: 8px;
            background-clip: padding-box;
            -webkit-transition: all 220ms ease-in-out;
            -moz-transition: all 220ms ease-in-out;
            -o-transition: all 220ms ease-in-out;
            transition: all 220ms ease-in-out;
        }
        .status.green {
            background-color: #8dc63f;
        }
        .status.lime {
            background-color: #b9c945;
        }
        .status.yellow {
            background-color: #ffba00;
        }
        .status.orange {
            background-color: #d36e30;
        }
        .status.red {
            background-color: #cc3f44;
        }
        .fit {
            background: white none repeat scroll 0% 0%;
            padding: 9px 16px 9px 13px;
            display: inline-block;
            border: 1px solid rgb(239, 239, 239);
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #9e9e9e;
        }
    </style>
@stop

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Overview</h1>
            <p class="description">View all the info at a glance for this client.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav', ['active' => 'General'])

        <div class="panel panel-headerless">
            <div class="panel-body">
                <section class="profile-env">
                    <div class="row">
                        <div class="col-sm-3" style="padding: 18px;">

                            {{-- Sidebar --}}
                            <div class="user-info-sidebar">

                                {{-- User Info --}}
                                <a class="user-img">
                                    @if ($client->logo)
                                        <img src="{{ show_image($client->logo) }}" alt="user-img" class="img-responsive" style="max-width: 190px;margin-bottom: 18px;" />
                                    @else
                                        <img src="{{ url('assets/images/user-4.png') }}" alt="user-img" class="img-circle img-responsive img-thumbnail" />
                                    @endif
                                </a>
                                <a class="user-name">{{ $client->name }}</a>
                                <span class="user-title">{{ $client->address }}</span>
                                <hr />

                                {{-- Assignments Count --}}
                                <ul class="list-unstyled user-friends-count">
                                    <li>
                                        @if (isset($reseller))
                                            <span>{{ $client->users->count() }}</span>
                                        @else
                                            <span>{{ $client->users()->count() }}</span>
                                        @endif
                                        Users
                                    </li>
                                    <li>
                                        <span>
                                            @if (isset($reseller))
                                                {{ $client->assignmentsCount }}
                                            @else
                                                @if ($client->assignments())
                                                    {{ $client->assignments() }}
                                                @else
                                                    0
                                                @endif
                                            @endif
                                        </span>
                                        Assignments
                                    </li>
                                </ul>
                                <hr><br>

                                {{-- Controls --}}
                                <div style="text-align: center;">
                                    @if (isset($reseller))
                                        <a class="btn btn-black" href="/dashboard/resellers/{{ $reseller->id }}/clients/{{ $client->id }}/edit" style="padding: 8px 53px"><i class="fa-pencil"></i> Edit Client</a><br>
                                    @else
                                        <a class="btn btn-black" href="/dashboard/clients/{{ $client->id }}/edit" style="padding: 8px 53px"><i class="fa-pencil"></i> Edit Client</a><br>
                                        <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/export-users') }}" style="padding: 8px 14px"><i class="fa-users"></i> Download List of Users</a><br>
                                        @role('admin')
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info dropdown-toggle" style="padding: 8px 38px" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-download"></i> Download Data
                                                </button>
                                                <ul class="dropdown-menu dropdown-info" role="menu">
                                                    <li>
                                                        <a class="download-all-data" data-type="1">Assignment Answers</a>
                                                    </li>
                                                    <li>
                                                        <a class="download-all-data" data-type="2">Detailed Dimension Scores</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            {{--<a id="download-all-data" class="btn btn-info" style="padding: 8px 29px"><i class="fa-download"></i> Download All Data</a>--}}
                                        @endrole
                                    @endif
                                </div>

                                {{-- Progress Bar --}}
                                <div class="progress-status col-sm-12">
                                    <div class="progress progress-striped active" style="background-color:white;">
                                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                                    </div>
                                    <div id="progress-text"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>

                        </div>

                        <div class="col-sm-9">

                            {{-- Assignments --}}
                            <section class="user-timeline-stories" style="padding-top: 0;">
                                <article class="timeline-story">
                                    <i class="fa-paper-plane-empty block-icon"></i>
                                    <header style="margin-bottom: 30px;">
                                        <h3>Assignments</h3>
                                        @if (isset($reseller))
                                            <p>{{ $client->completedAssignmentsCount }} Completed (of {{ $client->assignmentsCount }})</p>
                                        @else
                                            @if ($client->assignments())
                                                <p>{{ $client->assessmentsCompleted() }} Completed (of {{ $client->assignments() }})</p>
                                            @endif
                                        @endif
                                    </header>

                                    {{-- Users --}}
                                    <div class="users">
                                        <div class="tab-content">
                                            <div class="tab-pane active">

                                                @if (! $users->isEmpty())
                                                    @foreach ($users as $i => $user)
                                                        <table class="table table-hover members-table middle-align" style="margin: 0;border: 1px solid #E6E6E6;">
                                                            <tbody>
                                                            <tr style="background: #f2f2f2; margin-top: 3px; padding: 10px;">

                                                                {{-- Name --}}
                                                                <td class="user-name">
                                                                    <a href="/dashboard/users/{{ $user->id }}" class="name">{{ $user->name }}</a>
                                                                </td>

                                                            </tr>
                                                            </tbody>
                                                        </table>

                                                        {{-- User Assignments --}}
                                                        @if ($user->assignments && $user->assignments->count() > 0)
                                                            <table class="table table-hover members-table middle-align">
                                                                <tbody>

                                                                @foreach ($user->assignments as $assignment)
                                                                    <tr style="background-color:white;margin-top:3px;">

                                                                        {{-- Assignment Name --}}
                                                                        <td class="user-name">
                                                                            @if (isset($reseller))
                                                                                @if ($assignment->assessment)
                                                                                    {{-- /dashboard/assignments/{{ $assignment->id }}/details --}}
                                                                                    <a href="#null">{{ $assignment->assessment->name }}</a>
                                                                                @else
                                                                                    <div>
                                                                                        <span class="text-danger">
                                                                                            <i class="fa-exclamation-circle"></i> <i>Assessment Not Found</i>
                                                                                        </span>
                                                                                    </div>
                                                                                @endif
                                                                            @else
                                                                                @if (!isset($reseller) && $assignment->assessment())
                                                                                    <a class="" href="/dashboard/assignments/{{ $assignment->id }}/details">{{ $assignment->assessment()->name }}</a>
                                                                                @elseif (isset($reseller) && $assignment->assessment)
                                                                                    <a class="" href="/dashboard/assignments/{{ $assignment->id }}/details">{{ $assignment->assessment->name }}</a>
                                                                                @else
                                                                                    <div>
                                                                                        <span class="text-danger">
                                                                                            <i class="fa-exclamation-circle"></i> <i>Assessment Not Found</i>
                                                                                        </span>
                                                                                    </div>
                                                                                @endif
                                                                            @endif
                                                                        </td>

                                                                        {{-- Completion Status --}}
                                                                        <td class="action-links">
                                                                            @if ($assignment->completed)
                                                                                <a class="edit"><i class="fa-check"></i> Completed {{ $assignment->completed_at->format('M d Y - h:i:s') }}</a>
                                                                            @else
                                                                                @if (! $assignment->started_at)
                                                                                    <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                                                                @else
                                                                                    <?php
                                                                                        $answers = $assignment->answers->count();
                                                                                        if (!isset($reseller))
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

                                                                        {{-- Expiration --}}
                                                                        <td class="action-links">
                                                                            <span class="action-links">
                                                                                @if (! $assignment->completed)
                                                                                    @if ($assignment->expires > Carbon\Carbon::now())
                                                                                        <a class="email">Expires in {{ $assignment->expires->diffForHumans(null, true) }}</a>
                                                                                    @else
                                                                                        <a class="" style="color: #9f9f9f;">Expired {{ $assignment->expires->diffForHumans(null, true) }} ago</a>
                                                                                    @endif
                                                                                @endif
                                                                            </span>
                                                                        </td>

                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>

                                                        @else
                                                            <div style="padding: 10px; border-bottom: 1px solid #eee;">
                                                                This user has no assignments.
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                @else
                                                    <div class="well">
                                                        There are no users belonging to this client.
                                                    </div>
                                                @endif

                                            </div>

                                            {{-- Pagination --}}
                                            @if (count($users) > 10)
                                                @include('dashboard.partials._pagination')
                                            @endif

                                        </div>
                                    </div>
                                </article>
                            </section>

                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            // Server-sent Events
            var es;

            $('.download-all-data').on('click', function()
            {
                var type = $(this).attr('data-type');
                var url = '/dashboard/assignments/download/{{ $client->id }}/'+type;
                es = new EventSource(url);

                // Add a cancel option
                $cancel = $('<a id="cancel-download"><i class="fa-times"></i> Cancel</a>');
                $('#progress-text').after($cancel);

                // Listen for messages
                es.addEventListener('message', function(e) {
                    var result = JSON.parse(e.data);

                    // Completed
                    if (result.i == -1)
                    {
                        es.close();
                        $('#progress-text').text('');
                        $('#progress-bar').css('width', '0%');
                        $('#cancel-download').remove();
                        $('section.user-timeline-stories').prepend(result.message);
                        //console.log(result);
                        window.location = '/download/' + result.message.file;
                    }

                    // Update progress
                    else
                    {
                        $('#progress-text').text('Preparing data... ' + result.message.toFixed(2) + '%');
                        $('#progress-bar').css('width', result.message.toFixed(2) + '%');
                    }
                });

                // Error
                es.addEventListener('error', function(e) {
                    console.log(e);
                    alert('Error occurred');
                    es.close();
                });

            });

            // Cancel download
            $('.progress-status').on('click', '#cancel-download', function(){
                es.close();
                $('#progress-text').text('');
                $('#progress-bar').css('width', '0%');
                $(this).remove();
            });
        });
    </script>

@stop