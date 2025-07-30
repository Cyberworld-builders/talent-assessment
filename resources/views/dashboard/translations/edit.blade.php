@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Edit {{ $translation->name }}</h1>
            <p class="description">Translate this assessment into another language.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav')

        {!! Form::model($translation, ['method' => 'PATCH', 'action' => ['TranslationsController@update', $assessment->id, $translation->id]]) !!}
            @include('dashboard.translations.partials._form', [
                'edit' => true,
                'button_name' => 'Save Changes'
            ])
        {!! Form::close() !!}

    </div>
@stop