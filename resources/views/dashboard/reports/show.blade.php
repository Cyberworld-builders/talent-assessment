@extends('app')

@section('body')

    {{-- Report Template --}}
    <div class="page-container assignment details">
        <div class="main-content">
            <div class="report-template">
                @include('dashboard.reports.partials._report', [
                    'export' => $export
                ])
            </div>
        </div>
    </div>

@stop