
@extends('dashboard.dashboard')

@section('styles')
    <style>
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .user {
            padding: 20px;
            background: #F6F6F6;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .user .info {
            color: #0b97c4;
        }
        .members-table thead tr th {
            font-size: 10px;
        }
        .arrow {
            margin: 10px 23px 10px 65px;
            color: #cfcfcf;
        }
        .status {
            display: inline-block;
            vertical-align: middle;
            background: #b7b7b7;
            margin-right: 5px;
            position: relative;
            top: -1px;
            width: 8px;
            height: 8px;
            -webkit-border-radius: 8px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 8px;
            -moz-background-clip: padding;
            border-radius: 8px;
            background-clip: padding-box;
            -webkit-transition: all 220ms ease-in-out;
            -moz-transition: all 220ms ease-in-out;
            -o-transition: all 220ms ease-in-out;
            transition: all 220ms ease-in-out;
        }
        .status.green {
            background-color: #8dc63f;
        }
        .status.lime {
            background-color: #b9c945;
        }
        .status.yellow {
            background-color: #ffba00;
        }
        .status.orange {
            background-color: #d36e30;
        }
        .status.red {
            background-color: #cc3f44;
        }
        .fit {
            background: white none repeat scroll 0% 0%;
            padding: 9px 16px 9px 13px;
            display: inline-block;
            border: 1px solid rgb(239, 239, 239);
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #9e9e9e;
        }
    </style>
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Employee Selection: {{ $job->name }}</h1>
            <p class="description">Manage and review viable applicants for the job {{ $job->name }}.</p>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane active">

                    @if (! $users->isEmpty())

                            <table class="table table-hover members-table middle-align" style="margin: 0;">

                                {{--@if ($i == 0)--}}
                                <thead>
                                <tr>
                                    {{--<th class="hidden-xs hidden-sm"></th>--}}
                                    <th>Name</th>
                                    @foreach ($job->assessments as $assessmentId)
                                        <?php $assessment = \App\Assessment::find($assessmentId); ?>
                                        <th>{{ $assessment->name }}</th>
                                    @endforeach
                                    <th>Report</th>
                                    <th>Settings</th>
                                </tr>
                                </thead>
                                {{--@endif--}}

                                <tbody>
                                @foreach ($users as $i => $user)
                                <tr style="background: white; margin-top: 3px; padding: 10px;">

                                    <!-- User Image -->
                                    {{--<td class="user-image hidden-xs hidden-sm" style="padding: 20px 10px;">--}}
                                        {{--<a href="#"><img src="http://localhost:8000/assets/images/user-1.png" class="img-circle" alt="user-pic"></a>--}}
                                    {{--</td>--}}

                                    <!-- Name -->
                                    <td class="user-name">
                                        <a href="#" class="name">{{ $user->name }}</a>
                                        {{--<span>User ID: {{ $user->username }}</span>--}}
                                        {{--@if ($user->email)--}}
                                            {{--<span style="font-size: 12px; color: #9d9d9d;">, Emaiil: {{ $user->emal }}</span>--}}
                                        {{--@endif--}}
                                    </td>

                                    <!-- Assignments Count -->
                                    <!-- Recommendation -->
                                    <td>
                                        <div class="fit">
                                            <span class="status red"></span> Low Fit
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fit">
                                            <span class="status yellow"></span> Moderate Fit
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fit">
                                            <span class="status green"></span> High Fit
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ url('dashboard/report/'.$user->id) }}"><i class="fa-file-text-o"></i> View Report</a>
                                    </td>

                                    <td>
                                        <a href="#null" class="reject-applicant"><i class="fa-times"></i> Reject Applicant</a>
                                    </td>

                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                    @else
                        <div class="well">
                            There are no applicants for this job.
                        </div>
                        {{--<a href="{{ url('dashboard/assign') }}" class="btn btn-black">Assign Assessments</a>--}}
                    @endif

                </div>

                <!-- Pagination -->
                <ul class="pagination">

                    <!-- Previous Page Link -->
                    @if ($paginator->currentPage() > 1)
                        <li><a href="?page={{ $paginator->currentPage() - 1 }}"><i class="fa-angle-left"></i></a></li>
                    @else
                        <li class="disabled"><a href="#"><i class="fa-angle-left"></i></a></li>
                    @endif

                    <!-- Page Numbers -->
                    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                        <li {{ ($paginator->currentPage() == $i ? 'class=active' : '') }}>
                            <a {{ ($paginator->currentPage() == $i ? '' : 'href=?page=' . $i) }}>{{ $i }}</a>
                        </li>
                    @endfor

                    <!-- Next Page Link -->
                    @if ($paginator->currentPage() < $paginator->lastPage())
                        <li><a href="?page={{ $paginator->currentPage() + 1 }}"><i class="fa-angle-right"></i></a></li>
                    @else
                        <li class="disabled"><a href="#"><i class="fa-angle-right"></i></a></li>
                    @endif
                </ul>

            </div>

        </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Sidebar menu default
            //$('.sidebar-menu-under .menu-category[data-parent="Users"]').show();

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            $('.reject-applicant').on('click', function() {
                if (confirm('Are you sure you want to reject this applicant?'))
                {
                    $(this).closest('tr').remove();
                }
            });

            // Server-sent Events
            var es;

            $('#download-all-data').on('click', function()
            {
                var url = '/dashboard/assignments/download/{{ $client->id }}';
                es = new EventSource(url);

                // Add a cancel option
                $cancel = $('<a id="cancel-download"><i class="fa-times"></i> Cancel</a>');
                $('#progress-text').after($cancel);

                // Listen for messages
                es.addEventListener('message', function(e) {
                    var result = JSON.parse(e.data);

                    // Completed
                    if (result.i == -1)
                    {
                        es.close();
                        $('#progress-text').text('');
                        $('#progress-bar').css('width', '0%');
                        $('#cancel-download').remove();
                        window.location = '/download/' + result.message.file;
                    }

                    // Update progress
                    else
                    {
                        $('#progress-text').text('Preparing data... ' + result.message.toFixed(2) + '%');
                        $('#progress-bar').css('width', result.message.toFixed(2) + '%');
                    }
                });

                // Error
                es.addEventListener('error', function(e) {
                    console.log(e);
                    alert('Error occurred');
                    es.close();
                });

//                var xhr = new XMLHttpRequest();
//                xhr.open('GET', url);
//                xhr.overrideMimeType("text/plain; charset=x-user-defined");

//                xhr.addEventListener("progress", function(e) {
//                    if (e.lengthComputable)
//                    {
//                        var percent = e.loaded / e.total;
//                        $('#progress-text').text('Downloading... ' + percent + '%');
//                        $('#progress-bar').css('width', percent + '%');
//                    }
//                    else {
//                        alert('Unable to compute progress information since the total size is unknown');
//                    }
//                });

//                xhr.addEventListener("load", function(e) {
//                    alert('File downloaded successfully');
//                });

//                xhr.addEventListener("error", function(e) {
//                    alert('An error occurred while downloading the file');
//                });

//                xhr.addEventListener("abort", function(e) {
//                    alert('File download canceled by the the user');
//                });
//
//                $.ajax({
//                    type: 'get',
//                    processData: false,
//                    contentType: false,
//                    url: url,
//                    data: data,
//                    dataType: 'json',
//                    xhr: function() {
//                        var xhr = new XMLHttpRequest();
//                        var total = 0;

                // Get the total size of files
                //$.each(document.getElementById('file').files, function(i, file) {
                //    total += file.size;
                //});

                // Get total file size
//                        var files = $('#file').prop('files');
//                        total = files[0].size;

                // Check if extension is correct
//                        var extension = files[0].name.substr(files[0].name.length - 3);
//                        if (extension != 'xls' && extension != 'lsx') {
//                            toastr.error('File must be a valid .xls or .xlsx format.', "Error", opts);
//                            return false;
//                        }

                //console.log(files[0]);

                // Called when upload progress changes. xhr2
//                        xhr.addEventListener("progress", function(evt) {
//                            var loaded = (evt.loaded / total).toFixed(2)*100; // percent
//                            if (loaded > 100)
//                                loaded = 100;
//                            $('#progress-text').text('Downloading... ' + evt.loaded + '%');
//                            $('#progress-bar').css('width', loaded + '%');
//                        }, false);

//                        return xhr;
//                    },
//                    success: function (data) {
//                        $('html').prepend(data.responseText);
//                        console.log(data);

//                        if (data['errors']) {
//                            toastr.error(data['errors'], "Error", opts);
//                            $modal.modal('hide');
//                        }

//                        if (data['users']) {
//                            for (var i = 0; i < data['users'].length; i += 1) {
//                                var user = data['users'][i];
//                                $user_add_form = $('.templates .panel').clone();
//                                $user_add_form.find('input[name="email[]"]').val(user['email']);
//                                $user_add_form.find('input[name="name[]"]').val(user['name']);
//                                $user_add_form.find('input[name="username[]"]').val(user['username']);
//                                $('.user-forms').append($user_add_form);
//                            }
//                            $modal.modal('hide');
//                        }

                //$modal.modal('hide');
//                        alert('downloaded successfully');
//                    },
//                    error: function (data) {
//                        console.log(data.status + ' ' + data.statusText);
//                        $('html').prepend(data.responseText);
//                    }
//                });
            });

            // Cancel download
            $('.progress-status').on('click', '#cancel-download', function(){
                es.close();
                $('#progress-text').text('');
                $('#progress-bar').css('width', '0%');
                $(this).remove();
            });
        });
    </script>

@stop

@section('scripts')

@stop