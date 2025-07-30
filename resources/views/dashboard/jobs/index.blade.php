@extends('dashboard.dashboard')

@section('styles')
    <style>
        .btn-tiny {
            padding: 4px 11px;
            font-size: 11px;
            margin-top: -3px;
            margin-bottom: -4px !important;
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
            @if (isset($reseller))
                <h1 class="title">Reseller Jobs</h1>
                <p class="description">Manage the jobs this reseller is allowed to assign to their clients.</p>
            @else
                <h1 class="title">{{ $client->name }}: Jobs</h1>
                <p class="description">Manage the jobs this client is taking applicants for.</p>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav', ['active' => 'Jobs'])
        @else
            @include('dashboard.clients.partials._subnav', ['active' => 'Selection'])
        @endif

        <div class="panel panel-headerless">
            <div class="panel-body">

                {{--@role('admin')--}}
                    {{-- Add Job Button --}}
                    @if (isset($reseller))
                        <div class="pull-right">
                            <a href="{{ url('dashboard/resellers/'.$reseller->id.'/jobs/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Job</a>
                        </div>
                    @else
                        <div class="pull-right">
                            @role('admin')
                                <a href="{{ url('dashboard/clients/'.$client->id.'/jobs/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Job</a>
                            @endrole
                            @role('reseller')
                                <a href="#null" id="add-job" class="btn btn-black"><i class="fa-plus"></i> Add Job</a>
                            @endrole
                        </div>
                    @endif
                {{--@endrole--}}

                @role('reseller')
                    {{--<div class="alert alert-info">--}}
                        {{--<button type="button" class="close" data-dismiss="alert">--}}
                            {{--<span aria-hidden="true">×</span>--}}
                            {{--<span class="sr-only">Close</span>--}}
                        {{--</button>--}}
                        {{--Please contact an AOE Administrator if you wish to add a new Selection Survey related to a specific job.--}}
                    {{--</div>--}}
                @endrole

                <!-- Jobs -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                <th>Job Title</th>
                                <th>Job ID</th>
                                <th>Assessments</th>
                                <th>Job Status</th>
                                {{--<th>Weighting</th>--}}
                                @if (!isset($reseller))
                                    <th>Applicants</th>
                                @endif
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($jobs))
                                @foreach($jobs as $job)

                                    <tr>
                                        <td class="user-name">
                                            @if (isset($reseller))
                                                <a href="{{ url('dashboard/resellers/'.$reseller->id.'/jobs/'.$job->id.'/edit') }}" class="name">{{ $job->name }}</a>
                                            @else
                                                <a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants') }}" class="name">{{ $job->name }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="email">{{ $job->slug }}</span>
                                        </td>
                                        <td>
                                            @if ($job->assessments)
                                                {{--<table>--}}
                                                    {{--<tr style="border: 1px solid #eee; font-size: 11px; font-weight: bold;">--}}
                                                        {{--<td style="border-right: 1px solid #eee; padding: 7px;">Name</td>--}}
                                                        {{--<td style="border-right: 1px solid #eee; padding: 7px;">Weighting</td>--}}
                                                        {{--<td style="padding: 7px;">Setup</td>--}}
                                                    {{--</tr>--}}
                                                    @foreach ($job->assessments as $assessment_id)
                                                        <?php $assessment = \App\Assessment::find($assessment_id); ?>
                                                        @if ($assessment)
                                                            {{--<tr style="border: 1px solid #eee; font-size: 11px;">--}}
                                                                {{--<td style="border-right: 1px solid #eee; padding: 5px;">{{ $assessment->name }}</td>--}}
                                                                {{--<td style="border-right: 1px solid #eee; padding: 7px;"><span class="text-danger">Not Set</span></td>--}}
                                                                {{--<td style="padding: 7px;"><a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/weights/create/'.$assessment->id) }}" class="edit"><i class="linecons-database"></i> Configure</a></td>--}}
                                                            {{--</tr>--}}
                                                            <span class="email">{{ $assessment->name}}</span><br/>
                                                        @endif
                                                    @endforeach
                                                {{--</table>--}}
                                            @else
                                                <span class="email">No Assessments Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($job->active)
                                                <span class="text-success">Open</span>
                                            @else
                                                <span class="text-danger">Closed</span>
                                            @endif
                                        </td>
                                        @if (!isset($reseller))
                                            <td>
                                                {{ $job->applicants()->count() }}
                                                <a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants') }}" class="edit text-muted">&nbsp;&nbsp;View Applicants</a>
                                            </td>
                                        @endif
                                        <td>
                                            @if (isset($reseller))
                                                {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyJob', $reseller->id, $job->id]]) !!}
                                                    <a href="{{ url('dashboard/resellers/'.$reseller->id.'/jobs/'.$job->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit delete" data-name="{{ $job->name }}"><i class="linecons-trash"></i> Delete</a>
                                                {!! Form::close() !!}
                                            @else
                                                {!! Form::open(['method' => 'delete', 'action' => ['JobsController@destroy', $client->id, $job->id]]) !!}
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit delete" data-name="{{ $job->name }}"><i class="linecons-trash"></i> Delete</a>
                                                {!! Form::close() !!}
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        @if (isset($reseller))
                                            This reseller does not have any jobs assigned to them.<br/>
                                        @else
                                            There are no active Selection Surveys at this time.
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>

    @role('reseller')

        {{-- Add Job From Template --}}
        <div class="modal fade" id="modal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add Job From Template</h4>
                    </div>

                    <div class="modal-body">
                        <table class="table table-hover members-table middle-align">
                            <tbody>
                                @foreach ($jobTemplates as $job)
                                    <tr><td><strong>{{ $job->name }}</strong> <a href="{{ url('dashboard/clients/'.$client->id.'/jobs/create/'.$job->id) }}" class="btn btn-black btn-tiny pull-right"><i class="fa-plus"></i> Add Job From This Template</a></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                        {{--<button type="button" class="btn btn-small-font btn-orange save-button">Add Job</button>--}}
                    </div>
                </div>
            </div>
        </div>
    @endrole

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });

            @role('reseller')
                // Add Job from template
                $('#add-job').click(function(){
                    $modal = $('#modal');
                    $modal.modal('show');

                    $modal.on('click', '.add-job-from-template', function()
                    {
                        alert('job added from template');
                        $modal.modal('hide');
                    });
                });
            @endrole
        });
    </script>

@stop