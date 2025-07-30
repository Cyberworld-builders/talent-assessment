@extends('app')

@section('title')
    Previewing {{ $assessment->name }}
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    @include('dashboard.assessments.partials._nav')

    <div class="page-container assignment preview {!! ($task) ? 'wmtask' : '' !!}"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        {{--@include('dashboard.partials._sidebar')--}}
        @include('assignment.partials._header', ['preview' => true])

        <div class="main-content">
            @include('assignment.form', ['preview' => true])
            @include('assignment.partials._footer')
        </div>

    </div>

@stop