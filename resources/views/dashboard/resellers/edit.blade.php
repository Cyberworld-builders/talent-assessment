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
            <h1 class="title">Edit</h1>
            <p class="description">Manage this reseller's general information and settings.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.resellers.partials._subnav', ['active' => 'Edit'])

        {!! Form::model($reseller, ['method' => 'PATCH', 'action' => ['ResellersController@update', $reseller->id], 'enctype' => 'multipart/form-data']) !!}
        @include('dashboard.resellers.partials._form', [
            'edit' => true,
            'button_name' => 'Save Changes'
        ])
        {!! Form::close() !!}

    </div>

@stop