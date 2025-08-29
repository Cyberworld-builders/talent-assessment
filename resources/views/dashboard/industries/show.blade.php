@extends('dashboard.dashboard')

@section('title')
    Industry Details
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Industry Details: {{ $industry->name }}</h3>
                    <div class="panel-options">
                        <a href="{{ url('dashboard/industries/' . $industry->id . '/edit') }}" class="btn btn-info">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ url('dashboard/industries') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Industries
                        </a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <tr>
                                    <th>ID:</th>
                                    <td>{{ $industry->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $industry->name }}</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $industry->created_at->format('M d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $industry->updated_at->format('M d, Y \a\t g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Statistics</h4>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Users in this industry:</strong> {{ $industry->users->count() }}</p>
                                    <p><strong>Benchmarks:</strong> {{ $industry->benchmarks->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

