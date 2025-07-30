@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Edit {{ $analysis->name }}</h1>
            <p class="description">Edit this analysis.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        {!! Form::model($analysis, ['method' => 'PATCH', 'action' => ['AnalysisController@update', $client->id, $analysis->id]]) !!}
            @include('dashboard.analysis.partials._form', [
                'edit' => true,
                'button_name' => 'Update Analysis'
            ])
        {!! Form::close() !!}

    </div>
@stop

