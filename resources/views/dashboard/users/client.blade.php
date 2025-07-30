@extends('dashboard.dashboard')

@section('styles')

@stop

@section('sidebar-class')
    {{--collapsed--}}
@stop

@section('content')

    <div class="page-title">

        <div class="title-env">
            <h1 class="title" style="display: inline;">Users For: {{ $client->name }}</h1>
            @if ($client->logo)
                <img src="{{ $client->logo }}" class="img-circle" alt="user-pic" width="30px" height="30px" style="position: relative; top: -6px; margin-left: 4px;">
            @else
                <img src="{{ asset('assets/images/client-1.png') }}" class="img-circle" alt="user-pic" width="30px" height="30px" style="position: relative; top: -6px; margin-left: 4px;">
            @endif
        </div>

    </div>

    {{--<div class="panel panel-headerless">--}}
        {{--<div class="panel-body">--}}

            {{--<div class="member-form-add-header">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-md-2 col-sm-4 pull-right-sm">--}}
                        {{--<a href="" class="btn btn-white"><i class="fa-gear"></i> Change Image</a>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-10 col-sm-8">--}}

                        {{--<div class="user-img">--}}
                            {{--<img src="http://affectstudios.net/aoe/images/evonik.png" class="img-circle" alt="user-pic" width="80px" height="80px">--}}
                        {{--</div>--}}
                        {{--<div class="user-name">--}}
                            {{--<a href="#">Evonik</a>--}}
                            {{--<span>AOE Admin</span>--}}
                        {{--</div>--}}

                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="row">

        <div class="col-md-12">
            <div class="pull-right">
                <a href="{{ url('dashboard/users/create/'.$client->id) }}" class="btn btn-black"><i class="fa-plus"></i> Add/Import Users</a>
            </div>
        </div>

        <div class="col-md-12">

            <div class="tab-content" style="background:#fff;">

                <div class="tab-pane active">

                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="hidden-xs hidden-sm"></th>
                            <th>Name and Role</th>
                            <th>Username</th>
                            <th class="hidden-xs hidden-sm">E-mail</th>
                            {{--<th>Users</th>--}}
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($users as $user)

                        <tr>
                            <td class="user-cb">
                                <input type="checkbox" class="cbr" name="members-list[]" value="1" checked />
                            </td>
                            <td class="user-image hidden-xs hidden-sm">
                                <a href="#">
                                    <img src="{{ asset('assets/images/user-1.png') }}" class="img-circle" alt="user-pic" />
                                </a>
                            </td>
                            <td class="user-name">
                                <a href="#" class="name">{{ $user->name }}</a>
                                <span>{{ $user->roles->first()->name }}</span>
                            </td>
                            <td class="user-name">
                                <span class="email">{{ $user->username }}</span>
                            </td>
                            <td class="hidden-xs hidden-sm">
                                @if ($user->email)
                                    <span class="email">{{ $user->email }}</span>
                                @else
                                    ---
                                @endif
                            </td>
                            {{--<td class="client">--}}
                                {{-------}}
                            {{--</td>--}}
                            <td class="">
                                {{--<a href="{{ url('/dashboard/users/'.$user->id.'/edit') }}" class="edit">--}}
                                {{--<i class="linecons-pencil"></i>--}}
                                {{--Edit--}}
                                {{--</a>--}}

                                {{--<a href="{{ url('/dashboard/users/'.$user->id.'/delete') }}" class="delete">--}}
                                {{--<i class="linecons-trash"></i>--}}
                                {{--Delete--}}
                                {{--</a>--}}

                                <a href="{{ url('dashboard/assignments/'.$user->id) }}"><i class="fa-file-o"></i> Assignments</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                @role('admin')
                                    <a href="{{ url('dashboard/users/'.$user->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="#null" class="edit delete-user" data-name="{{ $user->name }}" data-url="/dashboard/users/{{ $user->id }}"><i class="linecons-trash"></i> Delete</a>
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

            // Sidebar menu default
            $('.sidebar-menu-under .menu-category[data-parent="Clients"]').show();

            $('.delete-user').on('click', function() {
                var name = $(this).attr('data-name');
                var url = $(this).attr('data-url');

                if (confirm('Are you sure you want to delete '+name+' ?'))
                {
                    $.ajax({
                        type: 'delete',
                        url: url,
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            window.location.reload();
                        },
                        error: function (data) {
                            console.log(data.status + ' ' + data.statusText);
                            $('html').prepend(data.responseText);
                        }
                    });
                }
            });

        });
    </script>

@stop

@section('scripts')

@stop