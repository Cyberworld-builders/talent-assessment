@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Create New Group</h1>
            <p class="description">Create a new grouping for this client's users.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/groups']) !!}
            @include('dashboard.groups.partials._form', [
                'edit' => false,
                'button_name' => 'Create Group'
            ])
        {!! Form::close() !!}

    </div>

@stop