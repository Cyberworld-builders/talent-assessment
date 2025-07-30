@extends('dashboard.clientdashboard')

@section('content')

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="linecons-cog"></i><br/>
            My Account
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>My Account</h1>
                        <p>Manage you account settings and login credentials.</p>
                    </div>
                </div>
                {{--<div class="col-sm-6">--}}
                    {{--<div class="pull-right">--}}
                        {{--<a href="{{ url('logout') }}" class="btn btn-success"><i class="fa-gear"></i> Logout of your Account</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>

            <div class="row">

                {{-- Errors --}}
                @include('errors.list')

                {!! Form::model($user, ['method' => 'PATCH', 'action' => ['ClientDashboardController@updateAccount']]) !!}
                    @include('clientdashboard.partials._account_form')
                {!! Form::close() !!}

            </div>

        </div>
    </div>

@stop