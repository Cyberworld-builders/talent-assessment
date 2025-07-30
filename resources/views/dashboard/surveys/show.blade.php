@extends('dashboard.dashboard')

@section('styles')
    <style>
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .user {
            padding: 20px;
            background: #F6F6F6;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .user .info {
            color: #0b97c4;
        }
        .members-table thead tr th {
            font-size: 10px;
        }
        .arrow {
            margin: 10px 23px 10px 65px;
            color: #cfcfcf;
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
    </style>
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: {{ $survey->assessment()->name }} Survey</h1>
            <p class="description">Review the development survey launched on {{ $survey->created_at }}.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        <div class="panel panel-headerless">
            <div class="panel-body">

                {{-- Buttons --}}
                <div class="pull-right">
                    <a style="display:none;" id="generate-reports" class="btn btn-black"><i class="fa-bar-chart"></i> Generate Reports</a>
                </div>

                {{-- Progress Bar --}}
                <div class="progress-status col-sm-12">
                    <div class="progress progress-striped active" style="background-color:white;">
                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="progress-text"></div>
                </div>
                <div style="clear:both;"></div>

                <div class="tab-content">
                    <div class="tab-pane active">

                        @if (! $leaders->isEmpty())

                            <table class="table table-hover members-table middle-align" style="margin: 0;">

                                {{--@if ($i == 0)--}}
                                <thead>
                                <tr>
                                    {{--<th class="hidden-xs hidden-sm"></th>--}}
                                    <th>Leader Name</th>
                                    <th>Data Points</th>
                                    <th>Overall</th>
                                    @foreach ($survey->assessment()->dimensions as $dimension)
                                        <?php if ($dimension->isChild()) { continue; } ?>
                                        {{--<th>{{ $dimension->name }}</th>--}}
                                    @endforeach
                                    <th>Report</th>
                                    {{--<th>Settings</th>--}}
                                </tr>
                                </thead>
                                {{--@endif--}}

                                <tbody>
                                @foreach ($leaders as $i => $leader)
                                    <tr style="background: white; margin-top: 3px; padding: 10px;">

                                        <!-- Name -->
                                        <td class="user-name">
                                            <a href="#" class="name">{{ $leader->name }}</a>
                                        </td>

                                        <!-- Data Points -->
                                        <td class="email">
                                            {{--{{ $leader->survey->completed_at }}--}}
                                            {{ $leader->scorers }}
                                        </td>

                                        <!-- Overall Score -->
                                        <td>
                                            {{--@if ($leader->scorers)--}}
                                                {{--@if ($leader->division['overall'] == 1)--}}
                                                    {{--<div class="fit">--}}
                                                        {{--<span class="status green"></span> High Fit--}}
                                                    {{--</div>--}}
                                                {{--@elseif ($leader->division['overall'] == 2)--}}
                                                    {{--<div class="fit">--}}
                                                        {{--<span class="status yellow"></span> Moderate Fit--}}
                                                    {{--</div>--}}
                                                {{--@elseif ($leader->division['overall'] == 3)--}}
                                                    {{--<div class="fit">--}}
                                                        {{--<span class="status red"></span> Low Fit--}}
                                                    {{--</div>--}}
                                                {{--@endif--}}
                                            {{--@else--}}
                                                {{-------}}
                                            {{--@endif--}}
                                        </td>

                                        <!-- Dimension Scores -->
                                        @foreach ($survey->assessment()->dimensions as $dimension)
                                            <?php if ($dimension->isChild()) { continue; } ?>
                                            {{--<td>--}}
                                                {{--@if ($leader->scorers)--}}
                                                    {{--@if ($leader->division[$dimension->id] == 1)--}}
                                                        {{--<div class="fit">--}}
                                                            {{--<span class="status green"></span> High Fit--}}
                                                        {{--</div>--}}
                                                    {{--@elseif ($leader->division[$dimension->id] == 2)--}}
                                                        {{--<div class="fit">--}}
                                                            {{--<span class="status yellow"></span> Moderate Fit--}}
                                                        {{--</div>--}}
                                                    {{--@elseif ($leader->division[$dimension->id] == 3)--}}
                                                        {{--<div class="fit">--}}
                                                            {{--<span class="status red"></span> Low Fit--}}
                                                        {{--</div>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{-------}}
                                                {{--@endif--}}
                                            {{--</td>--}}
                                        @endforeach

                                        <td>
                                            @if ($leader->scorers)
                                                <a href="{{ url('dashboard/report/development/'.$client->id.'/'.$leader->survey->id.'/'.$leader->id) }}"><i class="fa-file-text-o"></i> View Report</a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="well">
                                No leaders found. This might mean they have not been setup as users.
                            </div>
                            {{--<a href="{{ url('dashboard/assign') }}" class="btn btn-black">Assign Assessments</a>--}}
                        @endif

                    </div>
                </div>

                <!-- Pagination -->
                <ul class="pagination">
                    {{--@if (Request::input('sort'))--}}
{{--                        {!! $paginator->setPath('')->appends(['sort' => Request::input('sort')])->render() !!}--}}
                    {{--@else--}}
                        {!! $paginator->setPath('')->render() !!}
                    {{--@endif--}}
                </ul>

            </div>
        </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            // Server-sent Events
            var es;
            $('#generate-reports').on('click', function()
            {
                var url = '/dashboard/clients/{{ $client->id }}/surveys/{{ $survey->created_at }}/generate';
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
                        //window.location = '/download/' + result.message.file;
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

            // Toaster Options
            var opts = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        });
    </script>

@stop