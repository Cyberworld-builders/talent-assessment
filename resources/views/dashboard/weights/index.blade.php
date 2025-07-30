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
            @include('dashboard.resellers.partials._subnav', ['active' => 'Weighting'])
        @else
            @include('dashboard.clients.partials._subnav', ['active' => 'Weighting'])
        @endif

        <div class="tab-content">
            <div class="tab-pane active">
                @foreach ($jobs as $job)

                <div class="table-heading"><h3>{{ $job->name }}</h3></div>

                <table class="table table-hover members-table middle-align">
                    <thead>
                    <tr>
                        <th>Assessment</th>
                        <th>Dimension Weights</th>
                        <th>Score Divisions</th>
                        <th>Percentiles</th>
                        <th>Settings</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($job->assessments as $i => $assessment_id)
                        <?php $assessment = \App\Assessment::find($assessment_id); ?>

                        <tr style="background-color:white;margin-top:2px;">

                            <td>
                                <a class="name">{{ $assessment->name }}</a>
                            </td>
                            <td>
                                @if (!$assessment->dimensions->isEmpty())
                                    @if ($assessment->weightsForJob($job->id))
                                        <?php $weight = $assessment->weightsForJob($job->id)->first(); ?>
                                        <table>
                                            <tr>
                                                @foreach ($weight->weights as $dimensionId => $dimensionWeight)
                                                    <td style="border: 1px solid #eee;padding:3px 6px;">{{ \App\Dimension::find($dimensionId)->code }}</td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach ($weight->weights as $dimensionWeight)
                                                    <td style="border: 1px solid #eee;padding:3px 6px;">{{ $dimensionWeight / 100 }}</td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    @else
                                        <span class="text-muted">Not Set</span>
                                    @endif
                                @else
                                    <span class="text-muted">No Dimensions</span>
                                @endif
                            </td>
                            <td class="action-links">
                                {{--<span class="action-links">--}}
                                    {{--<span class="division label label-success">High</span>--}}
                                    {{--<span class="division label label-warning">Moderate</span>--}}
                                    {{--<span class="division label label-danger">Low</span>--}}
                                {{--</span>--}}
                                @if ($assessment->weightsForJob($job->id))
                                    <?php $weight = $assessment->weightsForJob($job->id)->first(); ?>
                                    <table>
                                        <tr>
                                            @foreach ($weight->divisions as $i => $division)
                                                @if ($division['min'] or $division['max'])
                                                    <td style="border: 1px solid #eee;padding:3px 6px;">
                                                        @if ($i == 0)
                                                            <span class="status green"></span>
                                                        @elseif ($i == 1)
                                                            <span class="status lime"></span>
                                                        @elseif ($i == 2)
                                                            <span class="status yellow"></span>
                                                        @elseif ($i == 3)
                                                            <span class="status orange"></span>
                                                        @else
                                                            <span class="status red"></span>
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($weight->divisions as $division)
                                                @if ($division['min'] or $division['max'])
                                                    <td style="border: 1px solid #eee;padding:3px 6px;">
                                                        @if ($division['max'])
                                                            {{ $division['max'] }}-{{ $division['min'] }}
                                                        @elseif ($division['min'])
                                                            {{ $division['min'] }}+
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    </table>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">Not Set</span>
                            </td>
                            <td>
                                @if ($assessment->weightsForJob($job->id))
                                    <?php $weight = $assessment->weightsForJob($job->id)->first(); ?>
                                    @if (isset($reseller))
                                        {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyWeights', $reseller->id, $weight->id]]) !!}
                                            <a href="{{ url('dashboard/resellers/'.$reseller->id.'/weights/'.$weight->id.'/edit') }}"><i class="fa-edit"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="edit delete" data-name="{{ $assessment->name }}"><i class="fa-trash"></i> Remove</a>
                                        {!! Form::close() !!}
                                    @else
                                        {!! Form::open(['method' => 'delete', 'action' => ['WeightsController@destroy', $client->id, $weight->id]]) !!}
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/weights/'.$weight->id.'/edit') }}"><i class="fa-edit"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="edit delete" data-name="{{ $assessment->name }}"><i class="fa-trash"></i> Remove</a>
                                        {!! Form::close() !!}
                                    @endif
                                @else
                                    @if (isset($reseller))
                                        <a href="{{ url('dashboard/resellers/'.$reseller->id.'/weights/create/'.$job->id.'/'.$assessment->id) }}"><i class="fa-plus"></i> Set Custom Weights</a><br/>
                                    @else
                                        <a href="{{ url('dashboard/clients/'.$client->id.'/weights/create/'.$job->id.'/'.$assessment->id) }}"><i class="fa-plus"></i> Set Custom Weights</a><br/>
                                    @endif
                                @endif
                            </td>
                        </tr>

                    @endforeach
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

                if (confirm('Are you sure you want to delete the custom weighting for '+name+'?'))
                    form.submit();
            });
        });
    </script>
@stop