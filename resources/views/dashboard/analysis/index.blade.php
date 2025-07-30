@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Job Analysis</h1>
            <p class="description">Analyze specific jobs for this client with job analysis questionnaires.</p>
        </div>
    </div>

    <!-- Sub Navigation -->
    @include('dashboard.clients.partials._subnav', ['active' => 'Job Analysis'])

    <div class="panel panel-headerless">
        <div class="panel-body">

            <!-- Add / Imports Users Button -->
            <div class="pull-right">
                {{--<a href="{{ url('dashboard/clients/'.$client->id.'/export-users') }}" class="btn btn-black"><i class="fa-users"></i> Get Analysis</a>--}}
                <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/create') }}" class="btn btn-black"><i class="fa-plus"></i> New Job Analysis</a>
            </div>

            <!-- Users -->
            <div class="tab-content" style="background:#fff;">
                <div class="tab-pane active">
                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            {{--<th></th>--}}
                            {{--<th class="hidden-xs hidden-sm"></th>--}}
                            <th>Analysis Name</th>
                            <th>Users</th>
                            <th>Sent</th>
                            <th>Status</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyses as $analysis)
                                <tr>
                                    <td>
                                        <a class="name">{{ $analysis->name }}</a>
                                    </td>
                                    <td>
                                        @if ($analysis->users)
                                            {{ count($analysis->users) }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if ($analysis->sent_at && $analysis->sent_at != 0)
                                            <span class="email">{{ \Carbon\Carbon::parse($analysis->sent_at)->diffForHumans() }}</span>
                                        @else
                                            <span class="email">Not Sent</span>
                                        @endif
                                    </td>
                                    <td>
                                        <?php
										    $percent = 0;
                                            if (count($analysis->jaqs))
                                            {
												$completed = count($analysis->jaqs()->where('completed', 1)->get());
												$total = count($analysis->jaqs);
												$percent = ($completed / $total) * 100;
                                            }
                                        ?>
                                            <div class="progress" style="width: 65%;">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="text-muted text-small">{{ $percent }}% Complete</span>
                                    </td>
                                    <td>
                                        @role('admin')
                                        {!! Form::open(['method' => 'delete', 'action' => ['AnalysisController@destroy', $client->id, $analysis->id]]) !!}
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id) }}" class="edit"><i class="linecons-note"></i> View JAQs</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            @if (count($analysis->users))
                                                @if ($analysis->sent_at && $analysis->sent_at != 0)
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/send') }}" class="edit"><i class="linecons-paper-plane"></i> Re-Send</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                @else
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/send') }}" class="edit"><i class="linecons-paper-plane"></i> Send</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                @endif
                                            @endif
{{--                                            <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/export') }}" class="edit"><i class="linecons-database"></i> Export</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                            <a href="#null" class="edit delete" data-name="{{ $analysis->name }}"><i class="linecons-trash"></i> Delete</a>
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
