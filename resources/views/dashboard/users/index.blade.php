@extends('dashboard.dashboard')

@section('styles')
    @if (isset($reseller))
        <style>
            .nav.nav-tabs + .tab-content {
                background: #FFF;
                padding: 0;
                margin-bottom: 0;
                position: relative;
                top: -4px;
            }
            .panel .nav.nav-tabs > li {
                border: 1px solid #f0f0f0;
                margin-right: 5px;
            }
            .panel .nav.nav-tabs > li > a {
                margin-right: 0;
                background: #fff;
            }
            .panel .nav.nav-tabs > li.active > a {
                background: #f4f4f4;
            }
        </style>
    @endif
@stop

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
            <h1 class="title">Users</h1>
            <p class="description">Manage all users across all clients.</p>
        </div>
    </div>

    <div class="row">

        {{-- Reseller Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav', ['active' => 'Users'])
        @endif

        {{-- Reseller Panel --}}
        @if (isset($reseller))
            <div class="panel panel-headerless">
            <div class="panel-body">
        @endif

        {{-- Add User Button --}}
        <div class="{{ (isset($reseller) ? '' : 'col-md-12') }}">
            <div class="pull-right">
                @if (isset($reseller))
                    <a href="{{ url('dashboard/resellers/'.$reseller->id.'/users/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add User</a>
                @else
                    <a href="{{ url('dashboard/users/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add User</a>
                @endif
            </div>
        </div>

        {{-- Tabbed Content --}}
        <div class="{{ (isset($reseller) ? '' : 'col-md-12') }}">

            {{-- Tabs --}}
            <ul class="nav nav-tabs">
                @foreach ($roles as $i => $role)
                    <li<?php echo ($i == 0) ? ' class="active"' : '' ?>>
                        <a href="#{{ $role->slug }}s" data-toggle="tab">{{ $role->name }}s</a>
                    </li>
                @endforeach
            </ul>

            {{-- Content --}}
            <div class="tab-content">
                @foreach ($roles as $i => $role)
                    <div class="tab-pane<?php echo ($i == 0) ? ' active' : '' ?>" id="{{ $role->slug }}s">
                        <table class="table table-hover members-table middle-align">

                            {{-- Heading --}}
                            <thead>
                                <tr>
                                    <th>Name and Role</th>
                                    <th>Username</th>
                                    <th class="hidden-xs hidden-sm">E-Mail</th>
                                    @if ($role->slug == 'reseller' && session('reseller'))
                                        <th>Client</th>
                                    @elseif ($role->slug == 'reseller')
                                        <th>Reseller</th>
                                    @else
                                        <th>Client</th>
                                    @endif
                                    <th>Settings</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- Reseller Tab for AOE Admins --}}
                                @if ($role->slug == 'reseller' && !session('reseller'))
                                    <?php
                                        if (isset($reseller))
                                        {
                                            // Get Reseller Admins for one specific reseller
											$resellerUsers = $reseller->adminUsers();
                                        }
                                        else
                                        {
											// Get all Reseller Admin users
											$resellerUsers = collect([]);
											foreach (\App\Reseller::all() as $resellerInstance)
												$resellerUsers = $resellerUsers->merge($resellerInstance->adminUsers()->toArray());
                                        }
                                    ?>
                                    @foreach ($resellerUsers as $user)
                                        <tr>
                                            <td>
                                                <a class="name" href="/dashboard/resellers/{{ $user->reseller_id }}/users/{{ $user->id }}">{{ $user->name }}</a>
                                                <span>Reseller Admin</span>
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
                                                {{ \App\Reseller::find($user->reseller_id)->name }}
                                            </td>
                                            <td>
                                                {!! Form::open(['method' => 'delete', 'action' => ['UsersController@destroy', $user->id]]) !!}
                                                    @role('admin')
                                                        <a href="{{ url('dashboard/resellers/'.$user->reseller_id.'/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Delete</a>
                                                    @endrole
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach

                                {{-- All Other Tabs --}}
                                @else
                                    @foreach ($users as $user)

                                        {{-- If user role is not what the current tab is on, skip --}}
                                        @if ((!isset($reseller) && !$user->has($role->slug)) || (isset($reseller) && $user->slug != $role->slug))
                                            <?php continue; ?>
                                        @endif

                                        <tr>
                                            <td>
                                                @if (isset($reseller))
                                                    <a class="name" href="/dashboard/resellers/{{ $reseller->id }}/users/{{ $user->id }}">{{ $user->name }}</a>
                                                @else
                                                    <a class="name" href="/dashboard/users/{{ $user->id }}">{{ $user->name }}</a>
                                                @endif
                                                @if (session('reseller') && $role->slug == 'reseller')
                                                    <span>Administrator</span>
                                                @else
                                                    @if (isset($reseller))
                                                        <span>{{ $user->role_name }}</span>
                                                    @else
                                                        <span>{{ $user->roles->first()->name }}</span>
                                                    @endif
                                                @endif
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
                                                @if ($user->client)
                                                    {{ $user->client->name }}
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($reseller))
                                                    {!! Form::open(['method' => 'delete', 'action' => ['ResellersController@destroyUser', $reseller->id, $user->id]]) !!}
                                                        <a href="{{ url('dashboard/resellers/'.$reseller->id.'/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Delete</a>
                                                    {!! Form::close() !!}
                                                @else
                                                    {!! Form::open(['method' => 'delete', 'action' => ['UsersController@destroy', $user->id]]) !!}
                                                        @role('admin|reseller')
                                                            <a href="{{ url('dashboard/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Delete</a>
                                                        @endrole
                                                    {!! Form::close() !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- End Reseller Panel --}}
        @if (isset($reseller))
            </div>
            </div>
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