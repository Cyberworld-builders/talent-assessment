{{-- Assignments --}}
<div class="tab-content">
    <div class="tab-pane active">
        @if (! $assignments->isEmpty())
            <table class="table table-hover members-table middle-align">
                <thead>
                <tr>
                    @if (! isset($hideUser))
                        <th>Assigned To</th>
                    @endif
                    <th>Assignment</th>
                    <th>Completed</th>
                    <th>Expiration</th>
                    <th>Settings</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($assignments as $i => $assignment)

                    <tr style="background-color:white;margin-top:2px;">

                        {{-- User--}}
                        @if (! isset($hideUser))
                            <td class="user-name">
                                <a href="/dashboard/users/{{ $assignment->user->id }}"><i class="fa-user"></i> {{ $assignment->user->name }}</a>
                            </td>
                        @endif

                        {{-- Assignment --}}
                        <td class="user-name">
                            @if ($assignment->assessment())
                                <a href="{{ (\Illuminate\Support\Facades\Auth::user()->is('admin|reseller') ? url('dashboard/assignments/'.$assignment->id.'/details') : '#null') }}" class="name">
                                    {{ $assignment->assessment()->name }}
                                    @if ($assignment->target)
                                        For {{ $assignment->target->name }}
                                    @endif
                                </a>
                            @else
                                <div><span class="text-danger"><i class="fa-exclamation-circle"></i> <i>Assessment Not Found</i></span></div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="hidden-xs hidden-sm action-links">
                            <span class="action-links">
                                @if ($assignment->completed)
                                    <a class="edit"><i class="fa-check"></i> Completed {{ $assignment->completed_at->format('M d Y - h:i:s') }}</a>
                                @else
                                    <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                @endif
                            </span>
                        </td>

                        {{-- Expiration --}}
                        <td class="action-links">
                            <span class="action-links">
                                @if ($assignment->expires > Carbon\Carbon::now())
                                    <span class="email">Expires in {{ displayElapsedTime($assignment->expires) }}</span>
                                @else
                                    <a class="delete">Expired {{ $assignment->expires->diffForHumans(null, true) }} ago</a>
                                @endif
                            </span>
                        </td>

                        {{-- Settings --}}
                        <td class="form-settings">
                            @role('admin')
                                {!! Form::open(['method' => 'delete', 'action' => ['AssignmentsController@destroy', $assignment->id]]) !!}
                                    <a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="{{ url('dashboard/assignments/'.$assignment->id.'/details') }}"><i class="fa-list-ol"></i> View Details</a>&nbsp;&nbsp;&nbsp;
                                    @if ($assignment->assessment())
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="{{ $assignment->assessment()->name }}"><i class="linecons-trash"></i> Delete</a>
                                    @else
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="Deleted Assessment"><i class="linecons-trash"></i> Delete</a>
                                    @endif
                                {!! Form::close() !!}
                                {{--@if ($assignment->completed and $assignment->reportTemplate())--}}
                                    {{--&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{ url('dashboard/report/'.$assignment->reportTemplate().'/'.$assignment->id) }}"><i class="fa-file-text-o"></i> View Report</a>--}}
                                {{--@endif--}}
                            @endrole

                            @role('reseller')
                                {!! Form::open(['method' => 'delete', 'action' => ['AssignmentsController@destroy', $assignment->id]]) !!}
                                    <a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    @if ($assignment->assessment())
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="{{ $assignment->assessment()->name }}"><i class="linecons-trash"></i> Delete</a>
                                    @else
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="Deleted Assessment"><i class="linecons-trash"></i> Delete</a>
                                    @endif
                                {!! Form::close() !!}
                            @endrole

                            @role('client')
                                {!! Form::open(['method' => 'delete', 'action' => ['AssignmentsController@destroy', $assignment->id]]) !!}
                                    <a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    @if ($assignment->assessment())
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="{{ $assignment->assessment()->name }}"><i class="linecons-trash"></i> Delete</a>
                                    @else
                                        <a href="#null" class="edit delete" data-name="{{ $assignment->user->name }}" data-assessment="Deleted Assessment"><i class="linecons-trash"></i> Delete</a>
                                    @endif
                                {!! Form::close() !!}
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