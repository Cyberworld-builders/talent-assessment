@extends('dashboard.dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        input.error {
            border-color: red;
        }
        .remove-row-button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            color: #bbbbbb;
            z-index: 10;
        }
        .remove-row-button:hover {
            color: #eee;
        }
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
        .control-label {
            font-size: 18px;
        }
        .form-group {
            margin-bottom: 30px;
        }
        .label.label-blue {
            background-color: #9CC2CB;
        }
        .assignment-user {
            border: 1px solid #eee;
            padding: 20px;
            position: relative;
        }
        .assignment-user .row:before {
            display: none;
        }
        .assignment-user .user-tab .fa-user,
        .assignment-user .user-tab .linecons-user {
            float: left;
            font-size: 36px;
            padding-right: 10px;
            color: rgb(207, 207, 207);
        }
        .assignment-user .user-tab h3 {
            font-size: 17px;
            font-weight: bold;
            margin: 0;
        }
        .assignment-user .user-tab .right-arrow {
            float: right;
            position: relative;
            top: -10px;
        }
        .assignment-user .target-label {
            margin-left: 10px;
            padding: 7px 14px;
            font-size: 15px;
            position: relative;
            top: 1px;
        }
        .assignment-user .target-span {
            display: inline-block;
            margin-top: 9px;
        }
        .assignment-user .target-button {
            margin-right: 30px;
            position: relative;
            top: 5px;
        }
        p.small .badge {
            font-size: 11px;
            font-weight: normal;
            border-radius: 1px;
            padding: 7px;
            margin: 0 4px 7px 0;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Assign</h1>
            <p class="description">Assign assessments to this client's users.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav')

        {{-- Assignments --}}
        {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/assign']) !!}
            @include('dashboard.assignments.partials._assignform', ['edit' => false, 'buttonName' => 'Assign'])
        {!! Form::close() !!}
    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
@stop