@extends('dashboard.clientdashboard')

@section('styles')
    <style>
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .members-table thead tr th {
            font-size: 10px;
        }
        .status {
            display: inline-block;
            vertical-align: middle;
            background: #b7b7b7;
            margin-right: 5px;
            position: relative;
            top: -1px;
            width: 8px;
            height: 8px;
            -webkit-border-radius: 8px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 8px;
            -moz-background-clip: padding;
            border-radius: 8px;
            background-clip: padding-box;
            -webkit-transition: all 220ms ease-in-out;
            -moz-transition: all 220ms ease-in-out;
            -o-transition: all 220ms ease-in-out;
            transition: all 220ms ease-in-out;
        }
        .status.green {
            background-color: #8dc63f;
        }
        .status.lime {
            background-color: #b9c945;
        }
        .status.yellow {
            background-color: #ffba00;
        }
        .status.orange {
            background-color: #d36e30;
        }
        .status.red {
            background-color: #cc3f44;
        }
        .fit {
            background: white none repeat scroll 0% 0%;
            padding: 9px 16px 9px 13px;
            display: inline-block;
            border: 1px solid rgb(239, 239, 239);
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #9e9e9e;
        }
        .table-title {
            color: #000;
            font-weight: normal;
            background-color: transparent;
            padding: 0px;
            font-size: 21px;
            text-align: left;
            margin-top: 40px;
            margin-bottom: -10px;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="fa-line-chart"></i><br/>
            Employee Selection
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Employee: {{ $user->name }}</h1>
                        <p>Manage and update user details for {{ $user->name }}.</p>
                    </div>
                </div>
            </div>
            <br/>

            <div class="row">
                <div class="col-sm-12">

                    {{-- Errors --}}
                    @include('errors.list')

                    {!! Form::model($user, ['method' => 'PATCH', 'action' => ['ClientDashboardController@updateUser', $user->id]]) !!}
                        @include('clientdashboard.partials._userform', ['button_name' => 'Save Changes', 'edit' => true])
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop

