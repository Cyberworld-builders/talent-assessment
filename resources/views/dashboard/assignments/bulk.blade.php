@extends('dashboard.dashboard')

@section('styles')
    <style type="text/css">
        .input-group p {
            margin-top: 3px !important;
        }
        thead tr {
            font-size: 14px;
        }
        thead tr th {
            padding: 10px 0;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Bulk Edit</h1>
            <p class="description">Edit multiple assignments at once.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav', ['active' => 'Edit'])

        {!! Form::model($assignments, ['method' => 'post', 'action' => ['AssignmentsController@bulk_update']]) !!}
            @include('dashboard.assignments.partials._bulkform')
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

