@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Create New Analysis</h1>
            <p class="description">Create a new analysis for a specific job.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/analysis']) !!}
            @include('dashboard.analysis.partials._form', [
                'edit' => false,
                'button_name' => 'Create Analysis'
            ])
        {!! Form::close() !!}

    </div>
@stop

