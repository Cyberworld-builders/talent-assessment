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
            padding: 20px;
        }
        .division {
            display: block;
            padding: 10px;
            font-size: 13px;
            font-weight: 400;
        }
    </style>
@stop

@section('sidebar-class')
    {{--collapsed--}}
@stop

@section('content')

    <div class="page-title">

        <div class="title-env">
            <h1 class="title">Personality Weighting</h1>
            <div class="assessments" style="padding-top: 15px;">
                <div class="label label-white" style="font-size: 15px; background: white; font-weight: 400;">AOE-Personality</div>
            </div>
        </div>

        <div class="pull-right" style="padding-right: 30px; height: 36px;">
            {{--<a href="{{ url('dashboard/clients/'.$client->id.'/edit') }}" class="btn btn-black">Edit</a>--}}
        </div>

    </div>

    <div class="row">

        <div class="progress-status col-md-12">
            <div class="progress progress-striped active">
                <div id="progress-bar" class="progress-bar progress-bar-success" style="width: 0%"></div>
            </div>
            <div id="progress-text"></div>
        </div>

        <div class="col-md-12">
            <div class="pull-right">
                {{--                <a href="{{ url('dashboard/clients/'.$client->id.'/export-users') }}" class="btn btn-black"><i class="fa-users"></i> Download User List</a>--}}
                {{--                <a href="{{ url('dashboard/assignments/download/'.$client->id) }}" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>--}}
                {{--<a id="download-all-data" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>--}}
            </div>
        </div>

        <div class="col-md-12">

            <div class="tab-content">

                <div class="tab-pane active">

                    {{--                    @if (! $assignments->isEmpty())--}}

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th>Function</th>
                            <th>Dimensions</th>
                            <th>Rating Divisions</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                        {{--                            @foreach ($assignments as $i => $assignment)--}}

                        <tr style="background-color:white;margin-top:2px;">

                            {{-- Assignment --}}
                            <td class="user-name">
                                <a href="#" class="name">Sales Manager</a>
                            </td>
                            <td class="user-name">
                                <div class="label label-white label-dimension">Honesty <span class="percentile pull-right">0.21</span></div>
                                <div class="label label-white label-dimension">Emotional Control <span class="percentile pull-right">0.07</span></div>
                                <div class="label label-white label-dimension">Extraversion <span class="percentile pull-right">0.10</span></div>
                                <div class="label label-white label-dimension">Agreeableness <span class="percentile pull-right">0.14</span></div>
                                <div class="label label-white label-dimension">Conscientiousness <span class="percentile pull-right">0.13</span></div>
                                <div class="label label-white label-dimension">Openness <span class="percentile pull-right">0.63</span></div>
                            </td>
                            <td class="hidden-xs hidden-sm action-links">
                                <span class="action-links">
                                    <span class="division label label-success">High</span>
                                    <span class="division label label-warning">Medium</span>
                                    <span class="division label label-danger">Low</span>
                                </span>
                            </td>
                            {{--<td class="action-links">--}}
                            {{--<span class="action-links">--}}
                            {{--<span class="email">---</span>--}}
                            {{--</span>--}}
                            {{--</td>--}}
                            <td>
                                <a href="{{ url('dashboard/weighting/1/functions') }}"><i class="fa-edit"></i> Edit</a><br/>
                                <a href="{{ url('dashboard/weighting/1/functions') }}"><i class="fa-trash"></i> Delete</a>
                            </td>
                        </tr>

                        {{--@endforeach--}}

                        </tbody>
                    </table>

                    {{--{!! $assignments->links() !!}--}}

                    {{--@else--}}
                    {{--<div class="well">--}}
                    {{--No assessments have been assigned.--}}
                    {{--</div>--}}
                    {{--<a href="{{ url('dashboard/assign') }}" class="btn btn-black">Assign Assessments</a>--}}
                    {{--@endif--}}

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
        });
    </script>

@stop

@section('scripts')

@stop