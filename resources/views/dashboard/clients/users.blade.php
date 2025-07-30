@extends('dashboard.dashboard')

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Users</h1>
            <p class="description">Manage this client's users.</p>
        </div>
    </div>

    <div class="row">

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav', ['active' => 'Users'])

        <div class="panel panel-headerless">
            <div class="panel-body">

                {{-- Add / Imports Users Button --}}
                <div class="pull-right">
                    @if (isset($reseller))
                    @else
                        <a href="{{ url('dashboard/clients/'.$client->id.'/export-users') }}" class="btn btn-black"><i class="fa-users"></i> Download User List</a>
                        <a href="{{ url('dashboard/users/create/'.$client->id) }}" class="btn btn-black"><i class="fa-plus"></i> Add/Import Users</a>
                    @endif
                </div>

                {{-- Users --}}
                <div class="tab-content" style="background:#fff;">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                <th class="hidden-xs hidden-sm"></th>
                                <th>Name and Role</th>
                                <th>Username</th>
                                <th>E-mail</th>
                                <th>Job Title</th>
                                <th>Job Family</th>
                                {{--<th>Users</th>--}}
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="user-image hidden-xs hidden-sm">
                                            <img src="{{ asset('assets/images/user-1.png') }}" class="img-circle" alt="user-pic" />
                                        </td>
                                        <td>
                                            @if (isset($reseller))
                                                <a class="name" href="/dashboard/resellers/{{ $reseller->id }}/users/{{ $user->id }}">
                                            @else
                                                <a class="name" href="/dashboard/users/{{ $user->id }}">
                                            @endif
                                                {{ $user->name }}
                                                @if (isset($reseller))
                                                    @if ($user->role_id)
                                                        @if ($user->slug == 'client')
                                                            <span class="label label-default" style="font-weight:400;font-style:italic;font-size:10px;margin-left:5px;position:relative;top:-2px;background:#ECECEC;color:#2b2b2b;">Client Admin</span>
                                                        @elseif ($user->slug == 'reseller')
                                                            <span class="label label-default" style="font-weight:400;font-style:italic;font-size:10px;margin-left:5px;position:relative;top:-2px;background:#ECECEC;color:#2b2b2b;">Reseller Admin</span>
                                                        @endif
                                                    @else
                                                        <span class="small text-danger" style="font-weight:400;font-style:italic;">&nbsp;No Role Assigned</span>
                                                    @endif
                                                @else
                                                    @if ($user->roles and $user->roles->first())
                                                        @if ($user->roles->first()->slug == 'client')
                                                            <span class="label label-default" style="font-weight:400;font-style:italic;font-size:10px;margin-left:5px;position:relative;top:-2px;background:#ECECEC;color:#2b2b2b;">Client Admin</span>
                                                        @elseif ($user->roles->first()->slug == 'reseller')
                                                            <span class="label label-default" style="font-weight:400;font-style:italic;font-size:10px;margin-left:5px;position:relative;top:-2px;background:#ECECEC;color:#2b2b2b;">Reseller</span>
                                                        @elseif ($user->roles->first()->slug == 'admin')
                                                            <span class="label label-warning" style="font-weight:400;font-style:italic;font-size:10px;margin-left:5px;position:relative;top:-2px;">AOE Admin</span>
                                                        @endif
                                                    @else
                                                        <span class="small text-danger" style="font-weight:400;font-style:italic;">&nbsp;No Role Assigned</span>
                                                    @endif
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <span class="email">{{ $user->username }}</span>
                                        </td>
                                        <td>
                                            @if ($user->email)
                                                <span class="email">{{ $user->email }}</span>
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->job_title)
                                                <span class="email">{{ $user->job_title }}</span>
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->job_family)
                                                <span class="email">{{ $user->job_family }}</span>
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>
                                            {{--<a href="{{ url('dashboard/assignments/'.$user->id) }}"><i class="fa-file-o"></i> Assignments</a> &nbsp;&nbsp;&nbsp;&nbsp;--}}
                                            @if (isset($reseller))
                                                @role('admin')
{{--                                                    {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyUser', $reseller->id, $user->id]]) !!}--}}
                                                        <a href="{{ url('dashboard/resellers/'.$reseller->id.'/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                        {{--<a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Delete</a>--}}
{{--                                                    {!! Form::close() !!}--}}
                                                @endrole
                                            @else
                                                @role('admin')
                                                    {!! Form::open(['method' => 'delete', 'action' => ['UsersController@destroy', $user->id]]) !!}
                                                        <a href="{{ url('dashboard/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Delete</a>
                                                    {!! Form::close() !!}
                                                @endrole
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        @if (isset($reseller))

        @else
            {!! $users->render() !!}
        @endif
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