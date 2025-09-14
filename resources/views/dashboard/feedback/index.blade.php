@extends('dashboard.dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <i class="fa fa-file-text-o"></i>
                    Feedback Libraries
                    <div class="pull-right">
                        <a href="{{ url('dashboard/feedback/create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create New Library
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(count($libraries) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Client</th>
                                    <th>Dimensions</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($libraries as $library)
                                <tr>
                                    <td>
                                        <strong>{{ $library->name }}</strong>
                                    </td>
                                    <td>
                                        @if($library->client)
                                            <span class="label label-info">{{ $library->client->name }}</span>
                                        @else
                                            <span class="label label-default">Global</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($library->feedback['dimensions']))
                                            {{ count($library->feedback['dimensions']) }} dimensions
                                        @else
                                            <span class="text-muted">No dimensions</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $library->created_at->format('M j, Y') }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ url('dashboard/feedback/' . $library->id) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ url('dashboard/feedback/' . $library->id . '/edit') }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-library" 
                                                    data-id="{{ $library->id }}" 
                                                    data-name="{{ $library->name }}"
                                                    title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center" style="padding: 40px;">
                        <i class="fa fa-file-text-o fa-3x text-muted"></i>
                        <h3 class="text-muted">No Feedback Libraries</h3>
                        <p class="text-muted">Get started by creating your first feedback library.</p>
                        <a href="{{ url('dashboard/feedback/create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create New Library
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the feedback library <strong id="library-name"></strong>?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-library').on('click', function() {
        var libraryId = $(this).data('id');
        var libraryName = $(this).data('name');
        
        $('#library-name').text(libraryName);
        $('#confirm-delete').data('id', libraryId);
        $('#deleteModal').modal('show');
    });
    
    $('#confirm-delete').on('click', function() {
        var libraryId = $(this).data('id');
        
        $.ajax({
            url: '/dashboard/feedback/' + libraryId,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting feedback library: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endsection