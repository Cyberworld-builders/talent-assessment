@extends('dashboard.clientdashboard')

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
            {{--<h1 class="title">Employee Selection</h1>--}}
            {{--<p class="description">Manage and review viable applicants for specific jobs.</p>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="title-env">
                        <h1>Employee Selection</h1>
                        <p>Manage and review viable applicants for specific jobs.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="pull-right">
                        <a href="{{ url('dashboard/add-applicants') }}" class="btn btn-orange btn-lg"><i class="fa-plus"></i> Add Applicants</a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active">

                            <table class="table table-hover members-table middle-align">
                                <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Job ID</th>
                                    <th>Assessments</th>
                                    <th>Job Status</th>
                                    <th>Viable Applicants</th>
                                    <th>Settings</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($jobs as $job)
                                    <tr>
                                        <td class="user-name">
                                            <a href="{{ url('dashboard/jobs/'.$job->id) }}" class="name">{{ $job->name }} <i class="linecons-eye"></i></a>
{{--                                            <a class="email" style="text-decoration: underline;" href="{{ url('dashboard/jobs/'.$job->id) }}">View Details</a>--}}
                                        </td>
                                        <td class="user-name">
                                            <span class="email">{{ $job->slug }}</span>
                                        </td>
                                        <td>
                                            @if ($job->assessments)
                                                @foreach ($job->assessments as $assessment_id)
                                                    <?php
                                                    $assessment = \App\Assessment::find($assessment_id);
                                                    if ($assessment)
                                                        echo '<span class="email">'.$assessment->name.'</span><br/>';
                                                    ?>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if ($job->active)
                                                <span class="text">Open</span>
                                            @else
                                                <span class="text">Closed</span>
                                            @endif
                                        </td>
                                        <td class="user-name">
                                            {{ $job->viableApplicants()->count() }} <a href="{{ url('dashboard/jobs/'.$job->id) }}"><i class="linecons-eye"></i></a>
                                        </td>
                                        <td>
                                            @if ($job->active)
                                                <a class="orange" href="{{ url('dashboard/jobs/'.$job->id.'/assign') }}"><i class="fa-paper-plane-o"></i> Assign Assessments</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
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
    </div>

@stop