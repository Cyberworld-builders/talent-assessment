@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Surveys</h1>
            <p class="description">Manage the surveys this client has pending.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav', ['active' => 'Development'])

        <div class="panel panel-headerless">
            <div class="panel-body">

                <!-- Add Job Button -->
                {{--<div class="pull-right">--}}
                    {{--<a href="{{ url('dashboard/clients/'.$client->id.'/jobs/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Job</a>--}}
                {{--</div>--}}

                <!-- Surveys -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                <th>Survey Name</th>
                                <th>Survey Date</th>
                                <th>Assigned To</th>
                                {{--<th>Job Status</th>--}}
                                {{--<th>Weighting</th>--}}
                                {{--<th>Applicants</th>--}}
                                {{--<th>Settings</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($surveys as $survey)

                                <tr>
                                    {{--<td class="user-cb">--}}
                                    {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                    {{--</td>--}}
                                    <td class="user-name">
                                        <a href="{{ url('dashboard/clients/'.$client->id.'/surveys/'.$survey->created_at) }}" class="name">{{ \App\Assessment::whereId($survey->assessment_id)->first()->name }}</a>
                                    </td>
                                    <td>
                                        <?php $carbonDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $survey->created_at); ?>
                                        <span>{{ $carbonDate->format('l, F jS, Y') }}</span><br/>
                                        <span class="email"><i>{{ $carbonDate->diffForHumans() }}</i></span>
                                    </td>
                                    <td>
                                        <?php
                                            $users = \App\User::where('client_id', $client->id)->get();
                                            $usersArray = [];
                                            foreach ($users as $user)
                                            	$usersArray[] = $user->id;

                                            $assessmentIds = [
												get_global('leader'),
												get_global('leader-s'),
												get_global('leader-sr'),
												get_global('360')
                                            ];

                                            $assignments = \App\Assignment::where([
                                        	    'created_at' => $survey->created_at
                                            ])->get()->filter(function($assignment) use ($usersArray, $assessmentIds) {
                                            	$user = \App\User::find($assignment->user_id);
                                        	    return in_array($user->id, $usersArray) && in_array($assignment->assessment_id, $assessmentIds);
                                            });
                                            echo $assignments->count() . ' User(s)';
                                        ?>
                                        {{--{!! Form::open(['method' => 'delete', 'action' => ['JobsController@destroy', $client->id, $job->id]]) !!}--}}
                                            {{--<a href="#null" class="edit"><i class="linecons-mail"></i> Email Leaders</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                        {{--{!! Form::close() !!}--}}
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });
        });
    </script>

@stop