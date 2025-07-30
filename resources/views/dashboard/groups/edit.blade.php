@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Edit {{ $group->name }}</h1>
            <p class="description">Edit the group {{ $group->name }} for this client.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        {!! Form::model($group, ['method' => 'PATCH', 'action' => ['GroupsController@update', $client->id, $group->id]]) !!}
            @include('dashboard.groups.partials._form', [
                'edit' => true,
                'button_name' => 'Update Group'
            ])
        {!! Form::close() !!}

    </div>

@stop

