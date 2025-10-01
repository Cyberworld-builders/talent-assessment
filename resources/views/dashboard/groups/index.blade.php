@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Groups</h1>
            <p class="description">Organize the users into logical groupings for this client.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav', ['active' => 'Groups'])

        <div class="panel panel-headerless">
            <div class="panel-body">

                <!-- Create New Group Button -->
                <div class="pull-right">
                    <a href="{{ url('dashboard/clients/'.$client->id.'/groups/create') }}" class="btn btn-black"><i class="fa-plus"></i> Create New Group</a>
                    <a href="#null" id="import-groups" class="btn btn-black"><i class="fa-list-ol"></i> Import Groups</a>
                </div>

                <!-- Groups -->
                <div class="tab-content" style="background:#fff;">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                {{--<th class="hidden-xs hidden-sm"></th>--}}
                                <th>Name</th>
                                <th>Target</th>
                                <th>Users In Group</th>
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($groups as $group)

                                <tr>
                                    {{--<td class="user-cb">--}}
                                        {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                    {{--</td>--}}
                                    <td class="user-name">
                                        <a class="name">{{ $group->name }}</a>
                                    </td>
                                    <td>
                                        <span class="email">
                                            @if ($group->target_id)
                                                {{ \App\User::find($group->target_id)->name }}
                                            @else
                                                ---
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @foreach ($group->users as $user)
                                            <span class="email">
                                                {{ \App\User::find($user['id'])->name }}

                                                @if ($user['position'])
                                                    <i {!! (strtolower($user['position']) == 'self' ? 'class="text-success"' : '') !!}>({{ $user['position'] }})</i>
                                                @endif
                                            </span><br/>
                                        @endforeach
                                    </td>
                                    <td>
                                        {!! Form::open(['method' => 'delete', 'action' => ['GroupsController@destroy', $client->id, $group->id]]) !!}
                                            <a href="{{ url('dashboard/clients/'.$client->id.'/groups/'.$group->id.'/edit') }}" class="edit"><i class="linecons-user"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="edit delete" data-name="{{ $group->name }}"><i class="linecons-trash"></i> Delete</a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Upload File Modal -->
    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Upload Users</h4>
                </div>

                <div class="modal-body">
                    <div class="well">
                        <p>
                            Upload a CSV file of groups for faster entry. The first row in the CSV file will be counted as the header.
                            Please make sure you have <b>Group Name</b>, <b>Target Name</b>, <b>Target Email</b>, <b>Name</b>, <b>Email</b>, and <b>Role</b> as column headers in your first row.
                            Accepted file types: <b>.csv</b>
                        </p>
                        <p>
                            <a href="/dashboard/groups/template" class="btn btn-sm btn-info">
                                <i class="fa fa-download"></i> Download CSV Template
                            </a>
                            <small class="text-muted">Download a blank template with the correct headers</small>
                        </p>
                    </div>
                    {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/upload-groups-csv', 'files' => true, 'id' => 'uploadform']) !!}
                        {!! Form::file('file', ['id' => 'file']) !!}
                    {!! Form::close() !!}
                    <br/>
                    <div class="progress progress-striped active">
                        <div id="progress-bar" class="progress-bar progress-bar-success" style="width: 0%"></div>
                    </div>
                    <div id="progress-text"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                    <button type="button" id="upload" class="btn btn-small-font btn-orange save-button">Upload File</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });

            // Import Groups From CSV
            $('#import-groups').on('click', function(){
                $modal = $('#modal-import');
                $modal.modal('show').on('click', '#upload', function()
                {
                    var inputElement = $('input#file')[0];
                    var data = new FormData();
                    data.append('file', inputElement.files[0]);
                    var url = '/dashboard/clients/{{ $client->id }}/upload-groups-csv';

                    $.ajax({
                        type: 'post',
                        processData: false,
                        contentType: false,
                        url: url,
                        data: data,
                        dataType: 'json',
                        xhr: function() {
                            var xhr = new XMLHttpRequest();
                            var total = 0;

                            // Get total file size
                            var files = $('#file').prop('files');
                            total = files[0].size;

                            // Check if extension is correct
                            var extension = files[0].name.substr(files[0].name.length - 3);
                            if (extension != 'csv') {
                                toastr.error('File must be a valid .csv format.', "Error", opts);
                                return false;
                            }

                            //console.log(files[0]);

                            // Called when upload progress changes. xhr2
                            xhr.upload.addEventListener("progress", function(evt) {
                                var loaded = (evt.loaded / total).toFixed(2)*100; // percent
                                if (loaded > 100)
                                    loaded = 100;
                                $('#progress-text').text('Uploading... ' + loaded + '%');
                                $('#progress-bar').css('width', loaded + '%');
                            }, false);

                            return xhr;
                        },
                        success: function (data) {
                            console.log('CSV Upload Success Response:', data);
                            
                            if (data['errors']) {
                                console.log('Errors found:', data['errors']);
                                toastr.error(data['errors'], "Error");
                                $modal.modal('hide');
                                return;
                            }

                            if (data['groups']) {
                                console.log('Groups created successfully:', data['groups']);
                                toastr.success('Groups created successfully!', "Success");
                                $modal.modal('hide');
                                
                                // Force page reload to show new groups
                                setTimeout(function() {
                                    console.log('Reloading page...');
                                    window.location.reload(true);
                                }, 1000);
                            } else {
                                console.log('No groups found in response');
                                toastr.warning('No groups were created', "Warning");
                                $modal.modal('hide');
                            }

                            //$modal.modal('hide');
                        },
                        error: function (data) {
                            console.log(data.status + ' ' + data.statusText);
                            $('html').prepend(data.responseText);
                        }
                    });
                });

                $modal.on('hidden.bs.modal', function() {
                    //$modal.remove();
                });
            });

        });
    </script>

@stop