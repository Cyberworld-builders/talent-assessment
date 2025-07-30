@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{$client->name}} Reports</h1>
            <p class="description">Reports Reports Reports</p>
        </div>
    </div>

    <!-- Sub Navigation -->
  @include('dashboard.clients.partials._subnav', ['active' => 'Reports'])

    <div class="panel panel-headerless">
        <div class="panel-body">

            <!-- Add / Imports Users Button -->
            <div class="pull-right">
                {{--<a href="{{ url('dashboard/clients/'.$client->id.'/export-users') }}" class="btn btn-black"><i class="fa-users"></i> Get Report</a>--}}
                <a href="{{ url('dashboard/clients/'.$client->id.'/report/create') }}" class="btn btn-black"><i class="fa-plus"></i> New Report</a>
            </div>

            <div class="tab-content" style="background:#fff;">
                <div class="tab-pane active">
                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            {{--<th></th>--}}
                            {{--<th class="hidden-xs hidden-sm"></th>--}}
                            <th>Report Name</th>
                            <th>Users</th>
                            <th>Date Sent</th>
                            <th>Status</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>
                                        <a class="name">{{ $report->name }}</a>
                                    </td>
                                    <td>
                                        @if ($report->users)
                                            {{ count($report->users) }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->sent_at)
                                            <span class="email">{{ $report->sent_at->diffForHumans() }}</span>
                                        @else
                                            <span class="email">Not Sent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->sent_at)
                                            <div class="progress" style="width: 30%;">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="30" aria-valuemin="30" aria-valuemax="100" style="width: 30%"></div>
                                            </div>
                                            <span class="text-muted text-small">30% Complete</span>
                                        @else
                                            <span class="email">Not Sent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @role('admin')
                                        {!! Form::open(['method' => 'delete', 'action' => ['ClientReportsController@destroy', $client->id, $report->id]]) !!}
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/clientReports/'.$reports->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit / Send</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/clientReports/'.$reports->id) }}" class="edit"><i class="linecons-note"></i> Results</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/clientReports/'.$reports->id.'/export') }}" class="edit"><i class="linecons-database"></i> Export</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="edit delete" data-name="{{ $reports->name }}"><i class="linecons-trash"></i> Delete</a>
                                        {!! Form::close() !!}
                                        @endrole
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });
        });
    </script>

  @stop
