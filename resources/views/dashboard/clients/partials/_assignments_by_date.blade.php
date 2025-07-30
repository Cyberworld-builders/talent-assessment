<div class="panel panel-headerless">
    <div class="panel-body">

        {{-- Buttons --}}
        <div class="pull-right">
            @role('admin')
                <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/assign') }}"><i class="linecons-paper-plane"></i> Assign Assessments</a>
                <a id="download-all-data" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>
                <a href="{{ url('dashboard/clients/'.$client->id.'/assignments/bulk-edit') }}" class="btn btn-black"><i class="linecons-pencil"></i> Bulk Edit</a>
            @endrole
            @role('reseller')
                <a class="btn btn-black" href="{{ url('dashboard/clients/'.$client->id.'/assign') }}"><i class="linecons-paper-plane"></i> Assign Assessments</a>
                {{--<a id="download-all-data" class="btn btn-black"><i class="fa-download"></i> Download All Data</a>--}}
                <a href="{{ url('dashboard/clients/'.$client->id.'/assignments/bulk-edit') }}" class="btn btn-black"><i class="linecons-pencil"></i> Bulk Edit</a>
            @endrole
        </div>

        {{-- Progress Bar --}}
        @role('admin')
            <div class="progress-status col-sm-12">
                <div class="progress progress-striped active" style="background-color:white;">
                    <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                </div>
                <div id="progress-text"></div>
            </div>
            <div style="clear:both;"></div>
        @endrole

        {{-- Assignments --}}
        <div class="tab-content">
            <div class="tab-pane active">
                @if (count($dates))
                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th>Sent</th>
                            <th>Assessments</th>
                            <th>Sent To</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($dates as $i => $date)

							<?php if (! count($date['assessments'])) continue; ?>

                            <tr style="background-color:white;margin-top:2px;">

                                {{-- Assignment Date --}}
                                <td class="user-name">
                                    <a class="name" href="/dashboard/clients/{{ $client->id }}/assignments/{{ $i }}">{{ \Carbon\Carbon::parse($i)->toDayDateTimeString() }}</a>
                                    <span>{{ \Carbon\Carbon::parse($i)->diffForHumans() }}</span>
                                </td>

                                <td class="user-name">
                                    @if ($date['assessments'])
                                        @foreach ($date['assessments'] as $j => $assessment)
                                            {{ $assessment->name }}
                                            @if ($j < sizeof($date['assessments']))
                                                <br/>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>

                                <td class="hidden-xs hidden-sm action-links">
                                    {{ count($date['users']) }} User(s)
                                </td>

                                <td>
                                    @role('admin')
                                        <a href="/dashboard/clients/{{ $client->id }}/assignments/{{ $i }}"><i class="fa-copy"></i> View Assignments</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="#null" class="send-emails" data-date="{{ $i }}"><i class="linecons-paper-plane"></i> Re-send Emails</a>
                                        {{--{!! Form::open(['method' => 'delete', 'action' => ['AssignmentsController@destroy', $assignment->id]]) !!}--}}
                                        {{--<a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                                        {{--<a href="{{ url('dashboard/assignments/'.$assignment->id.'/details') }}"><i class="fa-list-ol"></i> View Details</a>--}}
                                        {{--@if ($assignment->assessment())--}}
                                        {{--<a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="{{ $assignment->assessment()->name }}"><i class="linecons-trash"></i> Delete</a>--}}
                                        {{--@else--}}
                                        {{--<a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="Deleted Assessment"><i class="linecons-trash"></i> Delete</a>--}}
                                        {{--@endif--}}
                                        {{--{!! Form::close() !!}--}}
                                    @endrole

                                    @role('reseller')
                                        {{--{!! Form::open(['method' => 'delete', 'action' => ['AssignmentsController@destroy', $assignment->id]]) !!}--}}
                                        {{--<a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                                        {{--@if ($assignment->assessment())--}}
                                        {{--<a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="{{ $assignment->assessment()->name }}"><i class="linecons-trash"></i> Delete</a>--}}
                                        {{--@else--}}
                                        {{--<a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="Deleted Assessment"><i class="linecons-trash"></i> Delete</a>--}}
                                        {{--@endif--}}
                                        {{--{!! Form::close() !!}--}}
                                    @endrole

                                    @role('client')
                                        <a href="/dashboard/assignments/{{ $i }}"><i class="fa-copy"></i> View Assignments</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="#null" class="send-emails" data-date="{{ $i }}"><i class="linecons-paper-plane"></i> Re-send Emails</a>
                                    @endrole

                                </td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                @else
                    <div class="well">No assignments have been assigned yet.</div>
                @endif
            </div>
        </div>

    </div>
</div>