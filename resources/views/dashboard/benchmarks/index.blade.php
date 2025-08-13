@extends('dashboard.dashboard')

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Benchmarks - {{ $assessment->name }} - {{ $industry->name }}</h1>
            <p class="description">Manage industry-specific benchmarks for assessment dimensions.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Success Messages --}}
        @if(session('success'))
            <div class="col-md-12">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Upload Errors --}}
        @if(session('upload_errors'))
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <h4>Upload Warnings:</h4>
                    <ul>
                        @foreach(session('upload_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Excel Upload Section --}}
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Bulk Upload Benchmarks</h3>
                </div>
                <div class="panel-body">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Upload CSV File</h4>
                            <p class="text-muted">Upload a CSV file with dimension names and benchmark values. Excel support coming soon.</p>
                            
                            {!! Form::open(['url' => 'dashboard/benchmarks/' . $assessment->id . '/upload', 'files' => true]) !!}
                                {!! Form::hidden('industry_id', $industry->id) !!}
                                
                                <div class="form-group">
                                    {!! Form::label('excel_file', 'CSV File (.csv)') !!}
                                    {!! Form::file('excel_file', ['class' => 'form-control', 'accept' => '.csv']) !!}
                                    <p class="help-block">File should have dimension names in column A and benchmark values in column B.</p>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::submit('Upload Benchmarks', ['class' => 'btn btn-primary']) !!}
                                </div>
                            {!! Form::close() !!}
                            
                            <div class="alert alert-info mt-3">
                                <strong>Note:</strong> Excel file uploads are temporarily unavailable. Please use CSV format for now.
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Download Template</h4>
                            <p class="text-muted">Download a CSV template file with the correct format. Excel support coming soon.</p>
                            
                            <a href="{{ url('dashboard/benchmarks/' . $assessment->id . '/template-csv') }}" 
                               class="btn btn-success">
                                <i class="fa-download"></i> Download CSV Template
                            </a>
                            
                            <div class="mt-3">
                                <h5>Template Format:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Column A</th>
                                            <th>Column B</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Dimension Name</td>
                                            <td>Benchmark Value</td>
                                        </tr>
                                        <tr>
                                            <td>Leadership</td>
                                            <td>75</td>
                                        </tr>
                                        <tr>
                                            <td>Communication</td>
                                            <td>80</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Manual Entry Section --}}
        <div class="col-md-12">
            <div class="panel panel-default panel-manual-entry">
                <div class="panel-heading">
                    <h3 class="panel-title">Manual Benchmark Entry</h3>
                </div>
                <div class="panel-body">
                    
                    {!! Form::open(['url' => 'dashboard/benchmarks']) !!}
                        {!! Form::hidden('assessment_id', $assessment->id) !!}
                        {!! Form::hidden('industry_id', $industry->id) !!}
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Dimension</th>
                                        <th>Benchmark Value</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dimensions as $index => $dimension)
                                        <tr>
                                            <td>
                                                <strong>{{ $dimension->name }}</strong>
                                                {!! Form::hidden('benchmarks[' . $index . '][dimension_id]', $dimension->id) !!}
                                            </td>
                                            <td>
                                                {!! Form::text('benchmarks[' . $index . '][value]', 
                                                    isset($benchmarks[$dimension->id]) ? $benchmarks[$dimension->id]->value : '', 
                                                    ['class' => 'form-control', 'placeholder' => 'Enter benchmark value']) !!}
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $dimension->description ?: 'No description available' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::submit('Save Benchmarks', ['class' => 'btn btn-primary btn-lg']) !!}
                            <a href="{{ url('dashboard/benchmarks') }}" class="btn btn-default btn-lg">Back to Assessments</a>
                        </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>

    </div>

@stop

@section('styles')
    <style>
        /* Ensure the manual entry panel is always visible */
        .panel-manual-entry {
            display: block !important;
        }
        .panel-manual-entry .panel-body {
            display: block !important;
        }
        .panel-manual-entry .table-responsive {
            display: block !important;
        }
        .panel-manual-entry .table {
            display: table !important;
        }
        .panel-manual-entry .table tbody {
            display: table-row-group !important;
        }
        .panel-manual-entry .table tr {
            display: table-row !important;
        }
        .panel-manual-entry .table td,
        .panel-manual-entry .table th {
            display: table-cell !important;
        }
        /* Ensure input fields are visible and interactive */
        .panel-manual-entry input[type="text"] {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            min-height: 34px !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .panel-manual-entry .form-control {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            min-height: 34px !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
@stop

@section('scripts')
    <script>
        $(document).ready(function() {
            // Ensure manual entry panel is not collapsed
            $('.panel-manual-entry').removeClass('collapsed');
            
            // Add validation only to the manual entry form (not the upload form)
            $('.panel-manual-entry form').on('submit', function() {
                var hasValues = false;
                $('input[name*="[value]"]').each(function() {
                    if ($(this).val().trim() !== '') {
                        hasValues = true;
                        return false;
                    }
                });
                
                if (!hasValues) {
                    alert('Please enter at least one benchmark value.');
                    return false;
                }
            });
        });
    </script>
@stop
