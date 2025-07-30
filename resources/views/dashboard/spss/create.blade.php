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
                <h1 class="title">Add Predictive Model</h1>
            @else
                <h1 class="title">{{ $client->name }}: Add Predictive Model</h1>
            @endif
            <p class="description">Set a predictive model for a specific job. This will affect how the assessments in that job will be scored.</p>
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
            {!! Form::open(['url' => 'dashboard/resellers/'.$reseller->id.'/models', 'enctype' => 'multipart/form-data']) !!}
            @include('dashboard.spss.partials._form', [
                'edit' => false,
                'button_name' => 'Create Model'
            ])
            {!! Form::close() !!}
        @else
            {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/models', 'enctype' => 'multipart/form-data']) !!}
            @include('dashboard.spss.partials._form', [
                'edit' => false,
                'button_name' => 'Create Model'
            ])
            {!! Form::close() !!}
        @endif

    </div>
@stop