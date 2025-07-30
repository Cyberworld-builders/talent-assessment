@extends('dashboard.dashboard')

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Edit</h1>
            <p class="description">Manage this client's general information and assessment settings.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav', ['active' => 'Edit'])

        @if (isset($reseller))
            {!! Form::model($client, ['method' => 'PATCH', 'action' => ['ResellersController@updateClient', $reseller->id, $client->id], 'enctype' => 'multipart/form-data']) !!}
        @else
            {!! Form::model($client, ['method' => 'PATCH', 'action' => ['ClientsController@update', $client->id], 'enctype' => 'multipart/form-data']) !!}
        @endif
            @include('dashboard.clients.partials._form', [
                'edit' => true,
                'button_name' => 'Save Changes'
            ])
        {!! Form::close() !!}

    </div>

@stop