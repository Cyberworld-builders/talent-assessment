@extends('dashboard.clientdashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .members-table thead tr th {
            font-size: 10px;
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

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="fa-line-chart"></i><br/>
            Employee Selection
        </h1>
    </div>

    {{--<div class="page-title">--}}
        {{--<div class="title-env">--}}
            {{--<h1 class="title">Employee Selection: {{ $job->name }}</h1>--}}
            {{--<p class="description">Manage and review viable applicants for the job {{ $job->name }}.</p>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Employee Selection: {{ $job->name }}</h1>
                        @if ($job->description)
                            <p>{{ $job->description }}</p>
                        @else
                            <p>Manage and review viable applicants for the job {{ $job->name }}.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="row">

                {{-- Back Button --}}
                <div class="col-md-12">
                    <a href="{{ url('dashboard/selection') }}" class="btn btn-black"><i class="fa-chevron-left"></i> All Jobs</a>
                    <div class="pull-right">
                        <a href="#null" id="search-users" class="btn btn-black"><i class="fa-search"></i> Search Users</a>
                        <a href="#null" id="download-data-options" class="btn btn-black"><i class="fa-download"></i> Download Data</a>
                        <a href="{{ url('dashboard/jobs/'.$job->id.'/export-users') }}" id="export-users" class="btn btn-black"><i class="fa-users"></i> Export User List</a>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="progress-status col-sm-12">
                    <div class="progress progress-striped active">
                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="progress-text"></div>
                </div>

                {{-- Job Details --}}
                <div class="col-md-12">
                    <div class="tab-content">

                        <div class="tab-pane active">
                            @if (! $users->isEmpty())
                                <table class="table table-hover members-table middle-align" style="margin: 0;">
                                    <thead>
                                    <tr>
                                        <th><a href="{{ url($baseUrl.'&sort=name') }}">First Name</a></th>
                                        <th><a href="{{ url($baseUrl.'&sort=lastName') }}">Last Name</a></th>
                                        @foreach ($job->assessments as $i => $assessment)
                                            <th><a href="{{ url($baseUrl.'&sort='.($i+1)) }}">{{ $assessment->name }}</th>
                                        @endforeach
                                        <th>Report</th>
                                        <th>Settings</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($users as $i => $user)
                                        <tr style="background: white; margin-top: 3px; padding: 10px;">

                                            {{-- First Name --}}
                                            <td class="user-name">
                                                <a href="{{ url('dashboard/all-users/'.$user->id) }}" class="name">{{ $user->firstName }}</a>
                                            </td>

                                            {{-- Last Name --}}
                                            <td class="user-name">
                                                <a href="{{ url('dashboard/all-users/'.$user->id) }}" class="name">{{ $user->lastName }}</a>
                                            </td>

                                            {{-- Fit --}}
                                            @foreach ($user->assignments as $assignment)
                                                <td>
                                                    @include('clientdashboard.partials._fit', ['assignment' => $assignment])
                                                </td>
                                            @endforeach

                                            {{-- Report --}}
                                            <td>
                                                @if ($user->allAssessmentsCompletedForJob($job->id))
                                                    <a href="{{ url('dashboard/report/'.$client->id.'/'.$job->id.'/'.$user->id) }}"><i class="fa-file-text-o"></i> View Report</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    @if ($client->id == 12)
                                                        <a href="{{ url('dashboard/report/'.$client->id.'/'.$job->id.'/'.$user->id.'/download') }}"><i class="fa-download"></i> Download</a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N / A</span>
                                                @endif
                                            </td>

                                            {{-- Settings --}}
                                            <td>
                                                {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants/'.$user->id.'/reject']) !!}
                                                <a href="#null" class="reject-applicant orange"><i class="fa-times"></i> Reject Applicant</a>
                                                {!! Form::close() !!}
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            @else
                                <div class="well">
                                    There are no applicants for this job.
                                </div>
                                {{--<a href="{{ url('dashboard/assign') }}" class="btn btn-black">Assign Assessments</a>--}}
                            @endif
                        </div>

                        {{-- Pagination --}}
                        <ul class="pagination">
                            @if (Request::input('sort'))
                                {!! $paginator->setPath('')->appends(['sort' => Request::input('sort')])->render() !!}
                            @else
                                {!! $paginator->setPath('')->render() !!}
                            @endif
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search For Users --}}
    <div class="modal fade" id="modal-user">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Search For User</h4>
                </div>

                <div class="modal-body">
                    {!! Form::select('user_search_form', $usersArray, null, ['class' => 'form-control input-lg user-search-form', 'id' => 'user_search_form', 'multiple']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#user_search_form").select2({
                                placeholder: 'Search for a user by Name, Email, or UserId',
                                //allowClear: true
                            }).on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Close</button>
                    {{--<button type="button" id="add-users" class="btn btn-small-font btn-orange">Add User</button>--}}
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
                        {{--<div class="form-block">--}}
                            {{--<label style="cursor:pointer;">--}}
                                {{--<input type="checkbox" class="cbr" name="fit-as-score" />--}}
                                {{--Show raw score instead of fit recommendation--}}
                            {{--</label>--}}
                        {{--</div>--}}
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

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            // Reject applicant
            $('.reject-applicant').on('click', function() {
                if (confirm('Are you sure you want to reject this applicant?'))
                {
//                    $(this).closest('tr').remove();
                    $(this).closest('form').submit();
                }
            });

            // Sort by different column
            $('.sort').on('click', function(){
                var sortBy = $(this).attr('data-sort');
                var data = {sort: sortBy};
                var url = window.location.pathname;

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        window.location.reload();
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });

            // Search Users
            $('#search-users').on('click', function(){
                console.log('clicked');
                var $modal = $('#modal-user');
                $modal.modal('show');
            });

            // Remove cached values from user add form
            function clear_user_search_form()
            {
                $('.user-search-form').val('');
                $('.user-search-form .select2-search-choice').remove();
                $('.user-search-form .select2-input').attr('placeholder', 'Search for a user by Name, Email, or UserId').width(380);
            }
            clear_user_search_form();

            // User add form
            $('.user-search-form').on('change', function(){
                var userid = $(this).val();
                window.location = '/dashboard/all-users/'+userid[0];
                clear_user_search_form();
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

                var url = '/dashboard/jobs/{{ $job->id }}/download';

                // Include option flags in URL
                if (includeRejected || percentileAsScore || fitAsScore) url += '?';
                if (includeRejected) url += 'includeRejected=1&';
                if (percentileAsScore) url += 'percentileAsScore=1&';
                if (fitAsScore) url += 'fitAsScore=1&';

                // For debug, uncomment these two lines
                //window.location = url;
                //return true;

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

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
@stop