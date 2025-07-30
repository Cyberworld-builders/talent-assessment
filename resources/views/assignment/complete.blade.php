@extends('app')

@section('title')
    Assessment Complete
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    {{--@include('assignment.partials._nav')--}}

    <div class="page-container assignment"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        {{--@include('dashboard.partials._sidebar')--}}

        {{--@include('assignment.partials._header')--}}

        <div class="main-content">

            <h3>{{ translate('This assessment has been completed!') }}</h3>

            <p>{{ translate('Thank you for participating, your answers have been recorded and a confirmation email has been sent to you.') }}</p>

            <br/><br/><a href="{{ url('/assignments') }}" class="btn btn-white">{{ translate('Back To Assignments') }}</a>

        </div>

    </div>

@stop