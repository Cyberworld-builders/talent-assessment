@extends('dashboard.clientdashboard')

@section('content')

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="linecons-user"></i><br/>
            View Applicants
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="title-env">
                        <h1>All Applicants</h1>
                        <p>View and manage all applicants across all jobs.</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="pull-right">
                        <a href="{{ url('dashboard/add-applicants') }}" class="btn btn-orange btn-lg"><i class="fa-plus"></i> Add Applicants</a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="row">

                {{-- Selection --}}
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active">

                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                {{--<th class="hidden-xs hidden-sm"></th>--}}
                                <th>Name</th>
                                <th>Username</th>
                                <th class="hidden-xs hidden-sm">E-Mail</th>
                                <th>Job Applied For</th>
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($users as $user)

                                <tr>
                                    {{--<td class="user-cb">--}}
                                        {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                    {{--</td>--}}
                                    {{--<td class="user-image hidden-xs hidden-sm">--}}
                                        {{--<img src="{{ asset('assets/images/user-1.png') }}" class="img-circle" alt="user-pic" />--}}
                                    {{--</td>--}}
                                    <td>
                                        <a href="{{ url('dashboard/all-users/'.$user->id) }}" class="name">{{ $user->name }}</a>
                                        {{--<span>{{ $user->roles->first()->name }}</span>--}}
                                    </td>
                                    <td>
                                        <span class="email">{{ $user->username }}</span>
                                    </td>
                                    <td class="hidden-xs hidden-sm">
                                        @if ($user->email)
                                            <span class="email">{{ $user->email }}</span>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->jobs())
                                            @foreach ($user->jobs() as $job)
                                                <a href="{{ url('dashboard/jobs/'.$job->id) }}" class="name">{{ $job->name }} <i class="linecons-eye" style="color:#e77928;"></i></a>
                                            @endforeach
                                        @else
                                            ---
                                        @endif
                                    </td>
                                    {{--<td>--}}
                                        {{--@if ($user->client)--}}
                                            {{--{{ $user->client->name }}--}}
                                        {{--@else--}}
                                            {{-------}}
                                        {{--@endif--}}
                                    {{--</td>--}}
                                    <td>
{{--                                        {!! Form::open(['method' => 'delete', 'action' => ['UsersController@destroy', $user->id]]) !!}--}}
                                        <a href="{{ url('dashboard/all-users/'.$user->id) }}"><i class="fa-file-o"></i> View Details</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="{{ url('dashboard/all-users/'.$user->id.'/edit') }}"><i class="fa-pencil"></i> Edit User</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                        {{--{!! Form::close() !!}--}}
                                    </td>
                                </tr>

                            @endforeach

                            </tbody>
                        </table>

                    </div>
                {{--@endforeach--}}
            </div>

        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

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

@section('scripts')

@stop