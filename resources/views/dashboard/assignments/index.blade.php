@extends('dashboard.dashboard')

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Assignments for {{ $user->name }}</h1>
            <p class="description">Manage all the assigments for this user.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane active">

                    @if (! $assignments->isEmpty())

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Completed</th>
                            <th>Expiration</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($assignments as $i => $assignment)

                            <tr style="background-color:white;margin-top:2px;">
                                <td class="user-name">
                                    <a href="#" class="name">{{ $assignment->assessment()->name }}</a>
                                    <span>Assigned on {{ $assignment->created_at->format('M d Y - h:i:s') }}</span>
                                </td>
                                <td class="hidden-xs hidden-sm action-links">
                                    <span class="action-links">
                                        @if ($assignment->completed)
                                            <a class="edit"><i class="fa-check"></i> Completed {{ $assignment->completed_at->format('M d Y - h:i:s') }}</a>
                                        @else
                                            <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                        @endif
                                    </span>
                                </td>
                                <td class="action-links">
                                    <span class="action-links">
                                        @if ($assignment->expires > Carbon\Carbon::now())
                                            <span class="email">Expires in {{ $assignment->expires->diffForHumans(null, true) }}</span>
                                        @else
                                            <a class="delete">Expired {{ $assignment->expires->diffForHumans(null, true) }} ago</a>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @role('admin')
                                        <a href="{{ url('dashboard/assignments/'.$assignment->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="{{ url('dashboard/assignments/'.$assignment->id.'/details') }}"><i class="fa-list-ol"></i> View Details</a>
                                    @endrole
                                </td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                    @else
                        <div class="well">You have no assessments assigned to you.</div>
                    @endif

                </div>
            </div>
        </div>
    </div>

@stop