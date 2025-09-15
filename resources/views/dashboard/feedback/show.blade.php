@extends('dashboard.dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="fa fa-file-text-o"></i>
                    Feedback Library: {{ $library->name }}
                    <div class="pull-right">
                        <a href="{{ url('dashboard/feedback/' . $library->id . '/edit') }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ url('dashboard/feedback') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <!-- Library Information -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>Library Information</h4>
                        <table class="table table-striped">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $library->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Client:</strong></td>
                                <td>
                                    @if($library->client)
                                        <span class="label label-info">{{ $library->client->name }}</span>
                                    @else
                                        <span class="label label-default">Global</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $library->created_at->format('M j, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Updated:</strong></td>
                                <td>{{ $library->updated_at->format('M j, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Statistics</h4>
                        <table class="table table-striped">
                            <tr>
                                <td><strong>Dimensions:</strong></td>
                                <td>
                                    @if(isset($library->feedback['dimensions']))
                                        {{ count($library->feedback['dimensions']) }} dimensions
                                    @else
                                        <span class="text-muted">No dimensions</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Performance Levels:</strong></td>
                                <td>High, Medium, Low</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Feedback Content -->
                @if(isset($library->feedback['dimensions']) && count($library->feedback['dimensions']) > 0)
                <div class="row">
                    <div class="col-md-12">
                        <h4>Feedback Content</h4>
                        
                        @foreach($library->feedback['dimensions'] as $dimensionName => $dimensionData)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <i class="fa fa-cube"></i>
                                    {{ ucfirst(str_replace('_', ' ', $dimensionName)) }}
                                </h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="panel panel-success">
                                            <div class="panel-heading">
                                                <h6 class="panel-title">
                                                    <i class="fa fa-star"></i> High Performance
                                                </h6>
                                            </div>
                                            <div class="panel-body">
                                                <p>{{ $dimensionData['high'] ?? 'No feedback provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <h6 class="panel-title">
                                                    <i class="fa fa-star-half-o"></i> Medium Performance
                                                </h6>
                                            </div>
                                            <div class="panel-body">
                                                <p>{{ $dimensionData['medium'] ?? 'No feedback provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-danger">
                                            <div class="panel-heading">
                                                <h6 class="panel-title">
                                                    <i class="fa fa-star-o"></i> Low Performance
                                                </h6>
                                            </div>
                                            <div class="panel-body">
                                                <p>{{ $dimensionData['low'] ?? 'No feedback provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <i class="fa fa-warning"></i>
                            This feedback library has no dimensions defined. Please edit the library to add feedback content.
                        </div>
                    </div>
                </div>
                @endif

                <!-- Raw JSON (for debugging) -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>Raw JSON Data</h4>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <pre style="max-height: 300px; overflow-y: auto;">{{ json_encode($library->feedback, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
