@extends('dashboard.clientdashboard')

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
    <div class="header">
        <h1>
            <i class="fa-list-ol"></i><br/>
            Assignments
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Sub Title --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Assignments</h1>
                        <p>View assignments assigned on {{ \Carbon\Carbon::parse($date)->toDayDateTimeString() }}.</p>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Buttons --}}
                <div class="col-md-12">
                    <a href="{{ url('dashboard/assignments') }}" class="btn btn-black"><i class="fa-chevron-left"></i> Go Back</a>
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                <i class="linecons-paper-plane"></i> Assign Assessments <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-black" role="menu">
                                @foreach ($client->jobs as $job)
                                    <li>
                                        <a href="{{ url('dashboard/jobs/'.$job->id.'/assign') }}">For {{ $job->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <br/><br/>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-headerless">
                        <div class="panel-body">

                            {{-- Assignments --}}
                            @include('dashboard.clients.partials._assignments_table', ['assignments' => $assignments])

                        </div>
                    </div>
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

            // Delete the specified resource
            $('.form-settings .delete').on('click', function() {
                var name = $(this).attr('data-name');
                var assessment = $(this).attr('data-assessment');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete the assignment for '+assessment+' for '+name+'?'))
                    form.submit();
            });
        });
    </script>

@stop