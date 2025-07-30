@extends('dashboard.dashboard')

@section('content')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="assets/js/multiselect/css/multi-select.css">
    <style>
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
        .remove-row-button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            color: #bbbbbb;
        }
        .user-add-form .user-name {
            font-size: 18px;
            font-weight: bold;
            margin: 4px 0 7px 0;
        }
        .user-add-form .user-tab i {
            color: #bebebe;
            padding-right: 5px;
        }
        .remove-task, .remove-ksa, .remove-position {
            position: absolute;
            top: 29px;
            left: -28px;
            cursor: pointer;
            padding: 5px;
            z-index: 10;
        }
        .remove-ksa {
            top: 64px;
        }
        .remove-position {
            top: 9px;
            left: -15px;
        }
        .remove-task:hover, .remove-ksa:hover, .remove-position:hover {
            color: #aaa;
        }
        .task, .ksa, .position, .position-col {
            position: relative;
        }
    </style>
@stop

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Create New Report</h1>
            <p class="description">Create a new report for a specific client.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/reports']) !!}
        @include('dashboard.reports.partials._form', [
            'edit' => false,
            'button_name' => 'Create Report'
        ])
        {!! Form::close() !!}
    </div>
@stop