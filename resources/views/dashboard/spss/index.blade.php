@extends('dashboard.dashboard')

@section('styles')
    <style type="text/css">
        .percentile {
            position: relative;
            font-size: 14px;
            font-weight: 400;
            font-style: italic;
            color: rgb(112, 112, 112);
            padding: 7px 16px;
            background: rgb(238, 238, 238) none repeat scroll 0% 0%;
            border: medium none;
            box-shadow: none;
            border-radius: 3px;
            left: 8px;
            margin-top: -20px;
        }
        .label-dimension {
            font-size: 12px;
            background: white;
            display: block;
            padding: 10px;
            text-align: left;
        }
        .members-table tbody tr td {
            /*padding: 20px;*/
        }
        .division {
            display: block;
            padding: 10px;
            font-size: 13px;
            font-weight: 400;
        }
        .table {
            border: 1px solid #D1D3DC;
        }
        .table-heading {
            padding: 12px 12px;
            background: #A9ABB6;
            color: white;
        }
        .table-heading h3 {
            font-size: 18px;
            margin: 0;
            color: white;
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
                <h1 class="title">Weighting</h1>
            @else
                <h1 class="title">{{ $client->name }}: Weighting</h1>
            @endif
            <p class="description">Adjust the weighting of dimensions for each assessment. This will affect how users are scored.</p>
        </div>
    </div>

    <div class="row">

        {{-- Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav', ['active' => 'Predictive Modeling'])
        @else
            @include('dashboard.clients.partials._subnav', ['active' => 'Predictive Modeling'])
        @endif

        <div class="tab-content">
            <div class="tab-pane active">

                {{-- Add Models Button --}}
                <div class="pull-right">
                    @if (isset($reseller))
                        <a href="{{ url('dashboard/resellers/'.$reseller->id.'/models/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Model</a>
                    @else
                        <a href="{{ url('dashboard/clients/'.$client->id.'/models/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Model</a>
                    @endif
                </div>
                <div style="clear:both;"></div>

                @foreach ($jobs as $job)

                    <div class="table-heading"><h3>{{ $job->name }}</h3></div>

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th>Model Name</th>
                            <th>Assessments</th>
                            <th>Status</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                            @if (count($job->models))
                                @foreach ($job->models as $model)

                                    <tr style="background-color:white;margin-top:2px;">

                                        <td>
                                            <a class="name">{{ $model->name }}</a>
                                        </td>
                                        <td>
                                            @foreach ($model->assessments as $assessmentId)
                                                <?php $assessment = \App\Assessment::find($assessmentId); ?>
                                                <span class="text-muted">{{ $assessment->name }}</span><br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($model->configured)
                                                <span class="status green"></span> Configured
                                            @else
                                                <span class="status red"></span> Not Configured
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($reseller))
                                                {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyModels', $reseller->id, $model->id]]) !!}
                                                    <a href="{{ url('dashboard/resellers/'.$reseller->id.'/models/'.$model->id.'/edit') }}"><i class="fa-edit"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit delete" data-name="{{ $job->name }}"><i class="fa-trash"></i> Remove</a>
                                                {!! Form::close() !!}
                                            @else
                                                {!! Form::open(['method' => 'delete', 'action' => ['PredictiveModelsController@destroy', $client->id, $model->id]]) !!}
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/models/'.$model->id.'/edit') }}"><i class="fa-edit"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit delete" data-name="{{ $job->name }}"><i class="fa-trash"></i> Remove</a>
                                                {!! Form::close() !!}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr style="background-color:white;margin-top:2px;">
                                    <td colspan="4">
                                        <span class="text-muted">No predictive model exists for this job.</span>
                                    </td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                    <br/>
                @endforeach

            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete the predictive model for '+name+'?'))
                    form.submit();
            });
        });
    </script>
@stop