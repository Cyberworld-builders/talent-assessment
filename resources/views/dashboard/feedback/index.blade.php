@extends('dashboard.layout')

@section('title')
    Feedback Libraries
@stop

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Feedback Libraries</div>
                    <div class="panel-options">
                        <a href="{{ url('dashboard/feedback/create') }}" class="btn btn-primary">
                            <i class="fa-plus"></i> Create New Library
                        </a>
                    </div>
                </div>
                <div class="panel-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($libraries->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($libraries as $library)
                                    <tr>
                                        <td>{{ $library->name }}</td>
                                        <td>{{ $library->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ url('dashboard/feedback/' . $library->id . '/edit') }}" 
                                               class="btn btn-xs btn-primary">
                                                <i class="fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ url('dashboard/feedback/' . $library->id) }}" 
                                                  style="display: inline;">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-xs btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this feedback library?')">
                                                    <i class="fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No feedback libraries found.</p>
                            <a href="{{ url('dashboard/feedback/create') }}" class="btn btn-primary">
                                Create Your First Library
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
