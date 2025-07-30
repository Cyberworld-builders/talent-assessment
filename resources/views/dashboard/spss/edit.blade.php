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
                <h1 class="title">Edit {{ $model->name }}</h1>
            @else
                <h1 class="title">{{ $client->name }}: Edit {{ $model->name }}</h1>
            @endif
            <p class="description">Edit this predictive model, or upload a different one. This will affect how the assessments in this job will be scored.</p>
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
            {!! Form::model($model, ['method' => 'PATCH', 'enctype' => 'multipart/form-data', 'action' => ['ResellersController@updateModels', $reseller->id, $model->id]]) !!}
            @include('dashboard.spss.partials._form', [
                'edit' => true,
                'button_name' => 'Update Model'
            ])
            {!! Form::close() !!}
        @else
            {!! Form::model($model, ['method' => 'PATCH', 'enctype' => 'multipart/form-data', 'action' => ['PredictiveModelsController@update', $client->id, $model->id]]) !!}
            @include('dashboard.spss.partials._form', [
                'edit' => true,
                'button_name' => 'Update Model'
            ])
            {!! Form::close() !!}
        @endif


    </div>
@stop