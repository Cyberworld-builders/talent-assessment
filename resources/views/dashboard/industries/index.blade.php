@extends('dashboard.dashboard')

@section('title')
    Industries
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Industries</h3>
                    <div class="panel-options">
                        <a href="{{ url('dashboard/industries/create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Industry
                        </a>
                    </div>
                </div>
                <div class="panel-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($industries->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($industries as $industry)
                                    <tr>
                                        <td>{{ $industry->name }}</td>
                                        <td>{{ $industry->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ url('dashboard/industries/' . $industry->id . '/edit') }}" 
                                               class="btn btn-xs btn-info">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ url('dashboard/industries/' . $industry->id) }}" 
                                                  style="display: inline;">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-xs btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this industry?')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No industries found.</p>
                            <a href="{{ url('dashboard/industries/create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add First Industry
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

