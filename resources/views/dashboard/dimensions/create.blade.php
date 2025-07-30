@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Create New Dimension</h1>
            <p class="description">Create a new dimension for this assessment.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav')

        {!! Form::open(['url' => 'dashboard/assessments/'.$assessment->id.'/dimensions']) !!}
        @include('dashboard.dimensions.partials._form', [
            'button_name' => 'Create Dimension'
        ])
        {!! Form::close() !!}

    </div>

@stop