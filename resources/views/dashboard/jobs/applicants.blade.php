@extends('dashboard.dashboard')

@section('styles')
    <style>
        table.small tbody tr td {
            padding: 0 10px 0 0;
        }
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .progress {
            background: #fff;
        }
    </style>
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Applicants for {{ $job->name }}</h1>
            <p class="description">Manage the applicants for this job.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        <div class="panel panel-headerless">
            <div class="panel-body">

                <!-- Add Applicants Button -->
                <div class="pull-right">
                    {{--<a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants/add') }}" class="btn btn-black"><i class="fa-group"></i> Add Applicants</a>--}}
                    <div class="btn-group" style="display: inline-block;">
                        <button aria-expanded="false" type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown">
                            <i class="fa-users"></i> Add Applicants <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-black" role="menu">
                            <li><a href="{{ url('dashboard/users/create/'.$client->id.'?job='.$job->id) }}">Create New Users</a></li>
                            <li><a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants/add') }}">Add Existing Users</a></li>
                        </ul>
                    </div>
                    <button aria-expanded="false" type="button" class="btn btn-black" id="download-data-options">
                        <i class="fa-save"></i> Download Data
                    </button>
                </div>

                {{-- Progress Bar --}}
                <div class="progress-status col-sm-12">
                    <div class="progress progress-striped active">
                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="progress-text"></div>
                </div>


                <!-- Applicants -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                <th>Name</th>
                                <th>Viable / Rejected</th>
                                <th>Assessments Completed</th>
                                {{--<th>Models</th>--}}
                                <th>New Reports</th>
                                <th>Reports</th>
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)

                                <tr>
                                    {{--<td class="user-cb">--}}
                                    {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                    {{--</td>--}}
                                    <td class="user-name">
                                        <a href="{{ url('dashboard/users/'.$user->id) }}" class="name">{{ $user->name }}</a>
                                    </td>

                                    <td>
                                        @if ($user->viable)
                                            <span class="text-success">Viable</span>
                                        @else
                                            <span class="text-danger">Rejected</span>
                                        @endif
                                    </td>

                                    <td>
                                        <?php
//                                          // Show assessments
                                            echo '<table class="small">';
                                            foreach ($job->assessments as $assessmentId)
                                            {
                                                $assessment = \App\Assessment::find($assessmentId);
                                                if (! $assessment)
                                                	continue;
                                                echo '<tr>';

                                                $assignment = $user->lastCompletedAssignmentForJob($assessment->id, $job->id);

												// Check its status
												if ($assignment)
                                                {
													echo '<td><span class="text-muted"><a href="/dashboard/assignments/'.$assignment->id.'/edit">'.$assessment->name.'</a></span></td>';

													if ($assignment->completed)
														echo ' <td><span class="text-small text-success">Completed</span></br></td>';
													else if (\Carbon\Carbon::now() > $assignment->expires)
														echo ' <td><span class="text-small text-warning">Expired</span></br></td>';
													else if ($assignment->started_at)
														echo ' <td><span class="text-small text-info">In Progress</span></br></td>';
													else
														echo ' <td><span class="text-small text-muted">Not Started Yet</span></br></td>';
                                                }

                                                // Never assigned
												else
                                                {
                                                    echo '<td><span class="text-muted">'.$assessment->name.'</span></td>';
													echo ' <td><span class="text-small text-danger">Never Assigned</span></br></td>';
												}

                                                echo '</tr>';
                                            }
										    echo '</table>';
                                        ?>
                                        {{--{{ $user->assessmentsCompletedForJob($job->id) }} / {{ count($job->assessments) }}--}}
                                    </td>

                                    <td>
                                        @if ($user->allAssessmentsCompletedForJob($job->id))
                                            <div class="btn-group" style="display: inline-block;">
                                                <button aria-expanded="false" type="button" class="btn btn-gray dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa-file-text-o"></i> View Report
                                                </button>
                                                <ul class="dropdown-menu dropdown-gray" role="menu">
                                                    @if ($job->hasReports())
                                                        @foreach ($job->reports() as $report)
                                                            <li><a href="/dashboard/report/{{ $report->id }}/{{ $user->id }}">{{ $report->name }}</a></li>
                                                        @endforeach
                                                    @else
                                                        <li><a style="font-size: 10px;">No reports enabled for this job</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                            <div class="btn-group" style="display: inline-block;">
                                                <button aria-expanded="false" type="button" class="btn btn-gray dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fa-file-text-o"></i> Download
                                                </button>
                                                <ul class="dropdown-menu dropdown-gray" role="menu">
                                                    @if ($job->hasReports())
                                                        @foreach ($job->reports() as $report)
                                                            <li><a href="/dashboard/report/{{ $report->id }}/{{ $user->id }}/download">{{ $report->name }}</a></li>
                                                        @endforeach
                                                    @else
                                                        <li><a style="font-size: 10px;">No reports enabled for this job</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @else
                                            ---
                                        @endif
                                    </td>

                                    <td>
                                        @if ($user->allAssessmentsCompletedForJob($job->id))
                                            @if (! $job->getModels()->isEmpty())
                                                <a href="{{ url('dashboard/model/'.$client->id.'/'.$job->id.'/'.$user->id.'/'.$job->getModels()->first()->id) }}"><i class="fa-file-text-o"></i> View Report</a>&nbsp;&nbsp;&nbsp;&nbsp;
{{--                                                <a href="{{ url('dashboard/model/'.$client->id.'/'.$job->id.'/'.$user->id.'/'.$job->models()->first()->id.'/download') }}"><i class="fa-download"></i> Download</a>--}}
                                            @elseif (! $job->getWeights()->isEmpty())
                                                <a href="{{ url('dashboard/report/'.$client->id.'/'.$job->id.'/'.$user->id) }}"><i class="fa-file-text-o"></i> View Report</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                @if ($client->id == 12)
                                                    <a href="{{ url('dashboard/report/'.$client->id.'/'.$job->id.'/'.$user->id.'/download') }}"><i class="fa-download"></i> Download</a>
                                                @endif
                                            @else
                                                Not Available
                                            @endif
                                        @else
                                            <span class="text-muted">N / A</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{--{!! Form::open(['method' => 'delete', 'action' => ['JobsController@destroy', $client->id, $job->id]]) !!}--}}
                                            {{--<a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Reject</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                            <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Remove</a>
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

    {{-- Data Download Options Window --}}
    <div class="modal fade" id="modal-download-options">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Download Data</h4>
                </div>

                <div class="modal-body">
                    <form id="download-options">
                        <div class="form-block">
                            <label style="cursor:pointer;">
                                <input type="checkbox" class="cbr" name="include-rejected" />
                                Include rejected applicants
                            </label>
                        </div>
                        <div class="form-block">
                            <label style="cursor:pointer;">
                                <input type="checkbox" class="cbr" name="percentile-as-score" />
                                Show raw score instead of percentile
                            </label>
                        </div>
                        <div class="form-block">
                            <label style="cursor:pointer;">
                                <input type="checkbox" class="cbr" name="fit-as-score" />
                                Show raw score instead of fit recommendation
                            </label>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Close</button>
                    <button type="button" id="download-data" class="btn btn-small-font btn-orange">Download Data</button>
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

            // Download Data Options
            $('#download-data-options').on('click', function() {
                var $modal = $('#modal-download-options');
                $modal.modal('show');

                $modal.on('click', '#download-data', function() {
                    $modal.modal('hide');
                });
            });

            // Server-sent Events
            var es;

            $('#download-data').on('click', function()
            {
                var includeRejected = $('#download-options input[name="include-rejected"]:checked').length;
                var percentileAsScore = $('#download-options input[name="percentile-as-score"]:checked').length;
                var fitAsScore = $('#download-options input[name="fit-as-score"]:checked').length;

                var url = '/dashboard/clients/{{ $client->id }}/jobs/{{ $job->id }}/download';

                // Include option flags in URL
                if (includeRejected || percentileAsScore || fitAsScore) url += '?';
                if (includeRejected) url += 'includeRejected=1&';
                if (percentileAsScore) url += 'percentileAsScore=1&';
                if (fitAsScore) url += 'fitAsScore=1&';

                // For debug, uncomment these two lines
                // window.location = url;
                // return true;

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