@extends('dashboard.dashboard')

@section('styles')
    <style type="text/css">
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        td.user-name i.fa-user {
            color: #ddd;
            margin-right: 6px;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Assignments</h1>
            <p class="description">View assignments assigned on {{ \Carbon\Carbon::parse($date)->toDayDateTimeString() }}.</p>
        </div>
    </div>

    <div class="row">

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav')

        <div class="panel panel-headerless">
            <div class="panel-body">

                {{-- Buttons --}}
                <div class="pull-right">
                    @role('admin')
                    <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/assign') }}"><i class="linecons-paper-plane"></i> Assign Assessments</a>
                    <a id="download-all-data" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>
                    @endrole
                    @role('reseller')
                    <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/assign') }}"><i class="linecons-paper-plane"></i> Assign Assessments</a>
                    {{--<a id="download-all-data" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>--}}
                    @endrole
                </div>

                {{-- Progress Bar --}}
                <div class="progress-status col-sm-12">
                    <div class="progress progress-striped active" style="background-color:white;">
                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="progress-text"></div>
                </div>
                <div style="clear:both;"></div>

                <div class="well">
                    <h4><strong>Showing:</strong></h4>
                    <p>All assignments assigned on {{ \Carbon\Carbon::parse($date)->toDayDateTimeString() }}</p>
                </div>

                {{-- Assignments --}}
                @include('dashboard.clients.partials._assignments_table', ['assignments' => $assignments])

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

            // Delete the specified resource
            $('.form-settings .delete').on('click', function() {
                var name = $(this).attr('data-name');
                var assessment = $(this).attr('data-assessment');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete the assignment for '+assessment+' for '+name+'?'))
                    form.submit();
            });

            // Server-sent Events
            var es;

            $('#download-all-data').on('click', function()
            {
                var url = '/dashboard/assignments/download/{{ $client->id }}';
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