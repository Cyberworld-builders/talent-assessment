@extends('dashboard.clientdashboard')

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
    <div class="header">
        <h1>
            <i class="fa-list-ol"></i><br/>
            Assignments
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Sub Title --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Assignments: Bulk Edit</h1>
                        <p>Edit multiple assignments at once.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">

                    {{-- Errors --}}
                    @include('errors.list')

                    <br/><br/>
                    {!! Form::model($assignments, ['method' => 'post', 'action' => ['AssignmentsController@bulk_update']]) !!}
                        @include('dashboard.assignments.partials._bulkform')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
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

