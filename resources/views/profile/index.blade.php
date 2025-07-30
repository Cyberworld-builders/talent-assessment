@extends('app')

@section('title')
    Assignments
@stop

@section('styles')
    <style type="text/css">
        .profile .main-content {
            display: block;
            background-color: transparent;
            max-width: 1170px;
            margin: 0 auto;
            font-family: "Avenir Next LT Pro";
        }
        .profile .main-content .description {
            text-align: center;
        }
        .profile .main-content .description h1 {
            font-family: "Bebas Neue";
        }
        .profile .main-content .input-field {
            margin-bottom: 20px;
        }
        footer {
            clear: both;
            text-align: center;
        }
    </style>
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

     @include('assignment.partials._nav')

    <div class="page-container profile"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        <div class="heading">

            {{-- Background --}}
            {{--<img class="background" src="{{ $assessment->background }}" />--}}

            {{-- Logo --}}
            {{--<div class="logo">--}}
                {{--<img src="{{ $assessment->logo }}" />--}}
            {{--</div>--}}

            {{-- Title --}}
            {{--<div class="title">{{ $assessment->name }}</div>--}}

        </div>

        <div class="main-content">

            {{-- Description --}}
            <div class="description">
                <h1>{{ translate('Create Your Profile') }}</h1>
                <p>{{ translate('Please make sure that this information is accurate.') }}</p>
            </div>

            <!-- Errors -->
            @include('errors.list')

            {!! Form::open(['url' => 'profile']) !!}
{{--            {!! Form::model($user, ['method' => 'PATCH', 'action' => ['UsersController@update_profile', $user->id]]) !!}--}}
                @include('profile.partials._form')
            {!! Form::close() !!}

            <footer>
                <img src="{{ asset('assets/images/powered-by-aoe.png') }}" />
            </footer>

        </div>

    </div>

@stop