@extends('app')

@section('title')
    Job Analysis Questionnaire
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    {{--@include('assignment.partials._nav')--}}

    <div class="page-container assignment"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        {{--@include('dashboard.partials._sidebar')--}}
        @include('questionnaire.partials._header')

        <div class="main-content">
            @include('questionnaire.partials._form')
            {{--<script src="{{ asset('js/assignment.js') }}"></script>--}}
            <footer>
                <img src="{{ asset('assets/images/powered-by-aoe.png') }}" />
            </footer>
        </div>

    </div>

@stop