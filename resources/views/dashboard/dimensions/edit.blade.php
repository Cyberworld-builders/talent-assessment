@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Edit {{ $dimension->name }}</h1>
            <p class="description">Edit the dimension {{ $dimension->name }} for this assessment.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav')

        {!! Form::model($dimension, ['method' => 'PATCH', 'action' => ['DimensionsController@update', $assessment->id, $dimension->id]]) !!}
        @include('dashboard.dimensions.partials._form', [
            'button_name' => 'Save Changes'
        ])
        {!! Form::close() !!}

    </div>
@stop