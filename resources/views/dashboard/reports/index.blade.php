@extends('dashboard.dashboard')

@section('styles')
    <style>
        input.switch:empty {
            margin-left: -3000px;
        }
        input.switch:empty ~ label {
            position: relative;
            float: left;
            line-height: 1.6em;
            text-indent: 4em;
            margin: 0.2em 0;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        input.switch:empty ~ label:before, input.switch:empty ~ label:after {
            position: absolute;
            display: block;
            top: 0;
            bottom: 0;
            left: 0;
            content: ' ';
            width: 3.6em;
            background-color: #f4f4f4;
            border-radius: 0.3em;
            box-shadow: inset 0 0em 0 rgba(0, 0, 0, 0.3);
            -webkit-transition: all 100ms ease-in;
            transition: all 100ms ease-in;
            border: 2px solid #dadada;
        }
        input.switch:empty ~ label:after {
            width: 1.4em;
            top: 0.1em;
            bottom: 0.1em;
            margin-left: 0.1em;
            background-color: #fff;
            border-radius: 0.2em;
            box-shadow: inset 0 -0.2em 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #bebebe;
        }
        input.switch:checked ~ label:before {
            border-color: #8DC63F;
            background: #D6FEA6;
        }
        input.switch:checked ~ label:after {
            margin-left: 2.1em;
            background: #8DC63F;
            border: none;
        }
        .tocify .tocify-item.active > a {
            color: #333;
            font-size: 16px;
            cursor: pointer;
        }
        .tocify .tocify-item.active > a:hover {
            background: #f0f0f0;
        }
        .tocify .tocify-item > a {
            color: #333;
            font-size: 16px;
            cursor: pointer;
        }
        .tocify .tocify-item > a:hover {
            background: #f0f0f0;
            color: #777;
        }
        .tocify .tocify-item a i {
            margin-right: 5px;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Reports</h1>
            <p class="description">Customize the reports that clients can view.</p>
        </div>
    </div>

    {{-- Sub Navigation --}}
    @include('dashboard.clients.partials._subnav', ['active' => 'Reports'])

    <div class="panel panel-headerless">
        <div class="panel-body">
            <div class="row">

                {{-- Controls --}}
                <div class="pull-right">
                    @role('admin')
                        <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/reports/create') }}"><i class="fa-plus"></i> Create New Report</a>
                    @endrole
                </div>
                <div style="clear:both;"></div>

                {{-- Sidebar --}}
                {{--<div class="col-sm-3">--}}
                    {{--<div class="full-width tocify">--}}
                        {{--<ul class="tocify-header nav nav-list">--}}
                            {{--<li class="tocify-item {!! ($jobId == 0 ? 'active' : '') !!}"><a href="{{ url('dashboard/clients/'.$client->id.'/jobs/0/reports') }}"><i class="fa-copy"></i> All Assignments</a></li>--}}
                        {{--</ul>--}}
                        {{--@foreach ($client->jobs as $job)--}}
                            {{--<ul class="tocify-header nav nav-list">--}}
                                {{--<li class="tocify-item {!! ($job->id == $jobId ? 'active' : '') !!}"><a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/reports') }}"><i class="fa-file-o"></i> {{ $job->name }}</a></li>--}}
                            {{--</ul>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{-- Table --}}
                {{--<div class="col-sm-12">--}}
                    <div class="tab-content" style="background:#fff;">
                        <div class="tab-pane active">
                            <table class="table table-hover members-table middle-align">
                                <thead>
                                <tr>
                                    <th>Report</th>
                                    <th>For</th>
                                    <th>Assessments</th>
                                    <th>Customize</th>
                                    <th>Enabled</th>
                                    <th>Visible To Client Admin</th>
                                    <th>Settings</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>
                                            <a class="name">
                                                {{ $report->name }}
                                                @if ($report->customized())
                                                    <span class="text-small text-success"><br/>Customized</span>
                                                @else
                                                    <span class="text-small text-danger"><br/>Not Customized</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            @if ($report->job_id)
                                                {{ $report->job()->name }}
                                            @else
                                                All Assignments
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->assessments && json_decode($report->assessments))
                                                @foreach (json_decode($report->assessments) as $assessmentId)
                                                    <?php $assessment = \App\Assessment::find($assessmentId); ?>
                                                    <span class="text-muted">
                                                        {{ $assessment->name }}<br/>
                                                    </span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->customized())
                                                {!! Form::open(['method' => 'patch', 'action' => ['ReportsController@resetCustomizations', $client->id, $report->id]]) !!}
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/customize') }}" class="edit"><i class="linecons-pencil"></i> Customize</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit reset" data-name="{{ $report->name }}"><i class="fa-times"></i> Reset</a>
                                                {!! Form::close(); !!}
                                            @else
                                                <a href="{{ url('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/customize') }}" class="edit"><i class="linecons-pencil"></i> Customize</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            @endif
                                            {{--@role('admin')--}}
                                            {{--@if ($report->customized)--}}
                                            {{--{!! Form::open(['method' => 'delete', 'action' => ['ClientReportsController@destroy', $client->id, ($jobId ? $jobId : 0), $report->client_report->id]]) !!}--}}
                                            {{--<a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.($jobId ? $jobId : 0).'/reports/'.$report->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                            {{--<a href="#null" class="edit delete" data-name="{{ $report->name }}"><i class="fa-times"></i> Reset Customizations</a>--}}
                                            {{--{!! Form::close() !!}--}}
                                            {{--@else--}}
                                            {{--<a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.($jobId ? $jobId : 0).'/reports/'.$report->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Customize</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                            {{--@endif--}}
                                            {{--@endrole--}}
                                        </td>
                                        <td>
                                            @if ($report->customized())
                                                <div>
                                                    <input type="checkbox" id="switch1" name="enabled" class="switch" data-id="{{ $report->id }}" {!! ($report->enabled) ? 'checked' : '' !!} />
                                                    <label for="switch1" class="text-muted">Enabled</label>
                                                </div>
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->customized())
                                                <div>
                                                    <input type="checkbox" id="switch2" name="visible" class="switch" data-id="{{ $report->id }}" {!! ($report->visible) ? 'checked' : '' !!} />
                                                    <label for="switch2" class="text-muted">Visible</label>
                                                </div>
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! Form::open(['method' => 'delete', 'action' => ['ReportsController@destroy', $client->id, $report->id]]) !!}
                                                <a href="{{ url('dashboard/clients/'.$client->id.'/reports/'.$report->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="#null" class="edit delete" data-name="{{ $report->name }}"><i class="linecons-trash"></i> Delete</a>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                {{--</div>--}}

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

            // Grabbing data and sending data
            $('.switch').click(function() {
                $(this).toggleClass('active');

                var id = $(this).attr('data-id');
                var url = window.location.pathname + "/" + id + "/toggle";
                $row = $(this).closest('tr');
                var isEnabled = Number($('input[name="enabled"]', $row).is(':checked'));
                var isVisibleToClient = Number($('input[name="visible"]', $row).is(':checked'));

                $.ajax({
                    type: 'post',
                    url: url,
                    data: {
                      enabled: isEnabled,
                      visible: isVisibleToClient
                    },
                    dataType: 'boolean',
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'? \n\nNOTE: This will NOT affect any assessments taken or any of their data.'))
                    form.submit();
            });

            // Reset the specified resource
            $('.reset').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to reset all the customizations for the '+name+' report? \n\nNOTE: This will also temporarily disable the report and make it invisible to Client Admins until you re-activate it again.'))
                    form.submit();
            });
        });
    </script>

@stop
