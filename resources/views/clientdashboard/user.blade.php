@extends('dashboard.clientdashboard')

@section('styles')
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
        .table-title {
            color: #000;
            font-weight: normal;
            background-color: transparent;
            padding: 0px;
            font-size: 21px;
            text-align: left;
            margin-top: 40px;
            margin-bottom: -10px;
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

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Employee: {{ $user->name }}</h1>
                        <p>See Selection and Development results for {{ $user->name }}.</p>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="row">

                {{-- Back Button --}}
                <div class="col-md-12">
                    <a href="{{ url('dashboard/selection') }}" class="btn btn-black"><i class="fa-chevron-left"></i> All Jobs</a>
                </div>

                {{-- Selection --}}
                <div class="col-md-12">
                    <div class="table-title">Jobs Applied For</div>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            @foreach ($jobs as $job)
                                <table class="table table-hover members-table middle-align" style="margin: 0;">
                                    <thead>
                                    <tr>
                                        {{--<th class="hidden-xs hidden-sm"></th>--}}
                                        <th>Job Title</th>
                                        <th>Job ID</th>
                                        @foreach ($job->getAssessments() as $assessment)
                                            <th>{{ $assessment->name }}</th>
                                        @endforeach
                                        <th>Report</th>
                                        <th>Status</th>
                                        <th>Settings</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                        <tr style="background: white; margin-top: 3px; padding: 10px;">

                                            {{-- Name --}}
                                            <td class="user-name">
                                                {{ $job->name }}
                                            </td>

                                            {{-- ID --}}
                                            <td>
                                                <span class="email">{{ $job->slug }}</span>
                                            </td>

                                            {{-- Fit Recommendation --}}
                                            @foreach ($job->assignments as $assignment)
                                                <td>
                                                    @include('clientdashboard.partials._fit', ['assignment' => $assignment])
                                                </td>
                                            @endforeach

                                            {{-- Report --}}
                                            <td>
                                                @if ($user->allAssessmentsCompletedForJob($job->id))
                                                    {{--<a href="{{ url('dashboard/report/'.$client->id.'/'.$job->id.'/'.$user->id) }}"><i class="fa-file-text-o"></i> View Report</a>--}}
                                                    @if (! $job->getModels()->isEmpty())
                                                        <a href="{{ url('dashboard/model/'.$client->id.'/'.$job->id.'/'.$user->id.'/'.$job->getModels()->first()->id) }}"><i class="fa-file-text-o"></i> View Report</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        {{-- <a href="{{ url('dashboard/model/'.$client->id.'/'.$job->id.'/'.$user->id.'/'.$job->models()->first()->id.'/download') }}"><i class="fa-download"></i> Download</a>--}}
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

                                            {{-- Viability --}}
                                            <td>
                                                @if ($user->isViableForJob($job->id))
                                                    <span class="text">Viable</span>
                                                @else
                                                    <span class="text">Rejected</span>
                                                @endif
                                            </td>

                                            {{-- Controls --}}
                                            <td>
                                                @if ($user->isViableForJob($job->id))
                                                    {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants/'.$user->id.'/reject']) !!}
                                                    <a href="#null" class="reject-applicant orange"><i class="fa-times"></i> Reject</a>
                                                    {!! Form::close() !!}
                                                @else
                                                    {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants/'.$user->id.'/unreject']) !!}
                                                    <a href="#null" class="unreject-applicant orange"><i class="fa-check"></i> Unreject</a>
                                                    {!! Form::close() !!}
                                                @endif
                                            </td>

                                        </tr>
                                    </tbody>

                                </table>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div style="clear: both;height: 10px;"></div>

                {{-- Assignments --}}
                <div class="col-md-12">
                    <div class="table-title">Assignments</div>
                    @include('dashboard.clients.partials._assignments_table', ['assignments' => $assignments, 'hideUser' => true])
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
                    $(this).closest('form').submit();
                }
            });

            // Reject applicant
            $('.unreject-applicant').on('click', function() {
                if (confirm('Are you sure you want to unreject this applicant?'))
                {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>

@stop