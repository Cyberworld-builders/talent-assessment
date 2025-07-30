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
            @if (isset($reseller))
                <h1 class="title">Edit {{ $job->name }}</h1>
                <p class="description">Edit the job {{ $job->name }} for this reseller.</p>
            @else
                <h1 class="title">{{ $client->name }}: Edit {{ $job->name }}</h1>
                <p class="description">Edit the job {{ $job->name }} for this client.</p>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav')
        @else
            @include('dashboard.clients.partials._subnav')
        @endif

        @if (isset($reseller))
            {!! Form::model($job, ['method' => 'PATCH', 'action' => ['ResellersController@updateJob', $reseller->id, $job->id]]) !!}
                @include('dashboard.jobs.partials._form', [
                    'button_name' => 'Save Changes'
                ])
            {!! Form::close() !!}
        @else
            {!! Form::model($job, ['method' => 'PATCH', 'action' => ['JobsController@update', $client->id, $job->id]]) !!}
                @include('dashboard.jobs.partials._form', [
                    'button_name' => 'Save Changes'
                ])
            {!! Form::close() !!}
        @endif

    </div>

@stop