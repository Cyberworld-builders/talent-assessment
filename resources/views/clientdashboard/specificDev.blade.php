@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Employee Development</h1>
            <p class="description">Whip them employees into shape.</p>
        </div>
    </div>

    <div class="row">

        <!-- Add User Button -->
        <div class="col-md-12">
            <div class="pull-right">
                <a href="{{ url('dashboard/users/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add User</a>
            </div>
        </div>

        <div class="col-md-12">

            <!-- Tabs -->
           {{--  <ul class="nav nav-tabs">
                <li class="active">  
                    <a href="#all" data-toggle="all"></a>
                </li>
            </ul> --}}

            <div class="tab-content">
                    <div class="tab-pane active" id="all">

                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                <th>Name and Role</th>
                                <th>Username</th>
                                <th class="hidden-xs hidden-sm">E-Mail</th>
                                <th>Client</th>
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->client) 
                                        {{ $user->client->name }}
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
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