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
                    <div class="well" style="overflow: hidden;">
                        <p>
                            Upload a spreadsheet of custom groupings for faster entry.
                        </p>
                        <p>
                            Structure your excel document in the following manner: The first row in the spreadsheet will be counted as the header. Please make sure you have <b>Target Name</b>, <b>Target Email</b>, <b>Name</b>, <b>Email</b>, and <b>Role</b> as column headers in your first row, in that specific order.
                        </p>
                        <p>
                            <img class="img" src="https://s3-us-west-2.amazonaws.com/aoe-uploads/images/import_targets_sample.png" /><br/>
                            Refer to this image when structuring your spreadsheet file.
                        </p>
                        <p>
                            Accepted file types: <b>.xls</b>, <b>.xlsx</b>
                        </p>
                    </div>
                    {{-- The Url on the form doesn't do anything, the upload button callback queries another url via ajax --}}
                    {{--{!! Form::open(['url' => 'dashboard/assessments/import/', 'files' => true, 'id' => 'uploadform']) !!}--}}
                    {!! Form::file('file', ['id' => 'file']) !!}
                    {{--{!! Form::close() !!}--}}
                    <br/>
                    <div class="progress progress-striped active">
                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
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

            // Import Targets From Excel
            $('#import-groups').on('click', function(){
                $modal = $('#modal-import');
                $modal.modal('show').on('click', '#upload', function()
                {
                    var inputElement = $('input#file')[0];
                    var data = new FormData();
                    data.append('file', inputElement.files[0]);
                    var url = '/dashboard/clients/{{ $client->id }}/upload-groups';

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

                            // Get the total size of files
                            //$.each(document.getElementById('file').files, function(i, file) {
                            //    total += file.size;
                            //});

                            // Get total file size
                            var files = $('#file').prop('files');
                            total = files[0].size;

                            // Check if extension is correct
                            var extension = files[0].name.substr(files[0].name.length - 3);
                            if (extension != 'xls' && extension != 'lsx') {
                                toastr.error('File must be a valid .xls or .xlsx format.', "Error", opts);
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
                            $('html').prepend(data.responseText);
                            console.log(data);

                            if (data['errors']) {
                                toastr.error(data['errors'], "Error", opts);
                                $modal.modal('hide');
                            }

                            if (data['users'])
                            {
                                var url = '/dashboard/clients/{{ $client->id }}/generate-groups';

                                $.ajax({
                                    type: 'post',
                                    url: url,
                                    data: data,
                                    dataType: 'json',
                                    success: function (data) {
                                        $('html').prepend(data.responseText);
                                        console.log(data);

                                        if (data['success'])
                                            location.reload();

                                        else
                                            alert('An error has occurred!');
                                    },
                                    error: function (data) {
                                        console.log(data.status + ' ' + data.statusText);
                                        $('html').prepend(data.responseText);
                                    }
                                });

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