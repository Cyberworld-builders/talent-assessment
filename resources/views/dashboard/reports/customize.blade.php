@extends('app')

@section('styles')
    <style>
        .navbar.horizontal-menu .navbar-inner > .nav > li > button {
            background: white;
            color: black;
            border: none;
        }
        .navbar.horizontal-menu .navbar-inner > .nav > li > button:hover {
            background: #fafafa;
        }
        .navbar.horizontal-menu .navbar-inner > .nav > li > a {
            color: black;
        }
        form {
            margin-bottom: 0;
        }
    </style>
@stop

@section('body')

    {!! Form::model($report, ['method' => 'PATCH', 'action' => ['ReportsController@updateCustomizations', $client->id, $report->id]]) !!}

    <nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
        <div class="navbar-inner">

            {{-- Header --}}
            <div class="navbar-brand">
                <div class="logo">
                    <a href="#" class="logo-expanded"><br/>
                        <p><strong>Customizing Report: </strong><i>{{ $report->name }}</i></p>
                    </a>
                </div>
            </div>

            {{-- Navbar --}}
            <ul class="nav nav-userinfo navbar-right">
                <li>
                    <a href="{{ url('/dashboard/clients/'.$client->id.'/reports/'.$report->id.'/edit') }}">
                        <i class="fa-chevron-left"></i>
                        <span class="title">Go Back</span>
                    </a>
                </li>
                <li>
                    <button type="submit">
                        <i class="fa-save"></i>
                        <span class="title">Save</span>
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Report Template --}}
    <div class="page-container assignment details">
        <div class="main-content">
            <div class="report-template">
                @include('dashboard.reports.partials._report', [
                    'export' => false
                ])
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop

@section('scripts')
    <script src="{{ asset('js/autosize.js') }}"></script>
    <script>
        // Helper Functions
        (function($){
            $(document).ready(function(){
                $('.autosize').autosize();
            });
        })(jQuery);
    </script>
@stop