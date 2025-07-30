@extends('app')

@section('title')
    {{ $assessment->name }}
@stop

@section('scripts')
    @if ($assessment->timed)
        <script src="{{ asset('js/timer.js') }}"></script>
    @endif
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    {{--@include('assignment.partials._nav')--}}

    <div class="page-container assignment {!! ($task) ? 'wmtask' : '' !!}"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        {{--@include('dashboard.partials._sidebar')--}}
        @include('assignment.partials._header', ['preview' => false])

        <div class="main-content">
            @include('assignment.form', ['preview' => false])
            <script src="{{ asset('js/assignment.js') }}"></script>
            @include('assignment.partials._footer')
        </div>

    </div>

@stop