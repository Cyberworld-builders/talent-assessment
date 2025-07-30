@extends('dashboard.dashboard')

@section('body-class')
    page-clients
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Create Reseller</h1>
            <p class="description">Create a new reseller.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        {!! Form::open(['url' => 'dashboard/resellers', 'enctype' => 'multipart/form-data']) !!}
        @include('dashboard.resellers.partials._form', [
            'edit' => false,
            'button_name' => 'Add Reseller',
        ])
        {!! Form::close() !!}

    </div>

@stop