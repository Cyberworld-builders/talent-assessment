@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Create New Translation</h1>
            <p class="description">Translate this assessment into another language.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav')

        {!! Form::open(['url' => 'dashboard/assessments/'.$assessment->id.'/translations']) !!}
        @include('dashboard.translations.partials._form', [
            'edit' => false,
            'button_name' => 'Create Translation'
        ])
        {!! Form::close() !!}

    </div>
@stop