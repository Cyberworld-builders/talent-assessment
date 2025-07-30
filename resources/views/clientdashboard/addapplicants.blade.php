@extends('dashboard.clientdashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        input.error {
            border-color: red;
        }
        .panel {
            margin-bottom: 5px;
        }
        .remove-row-button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            color: #bbbbbb;
        }
        .remove-row-button:hover {
            color: #eee;
        }
        input.form-control.input-lg {
            height: 49px;
        }
        .user-add-form .row:before {
            margin: 0;
            display: none;
        }
        form .user-forms .panel.panel-headerless {
            border: none !important;
            border-bottom: 2px solid #eee !important;
            background: none !important;
            padding-bottom: 17px;
        }
        .user-add-form .form-control.select2-container {
            border: 2px solid #e7e7e7;
            font-size: 10px !important;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="fa-line-chart"></i><br/>
            Employee Selection
        </h1>
    </div>

    {{--<div class="page-title">--}}
        {{--<div class="title-env">--}}
            {{--<h1 class="title">Employee Selection: Add Applicants</h1>--}}
            {{--<p class="description">Add applicants to specific jobs.</p>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row heading-row">
                <div class="col-sm-12">
                    <div class="title-env">
                        <h1>Employee Selection: Add Applicants</h1>
                        <p>Add applicants to specific jobs.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-headerless" style="clear:both; padding:30px 0;">
                    <div class="panel-body">
                        <div class="member-form-inputs">

                            <!-- Add Row -->
                            <div class="pull-left">
                                <a href="{{ url('dashboard/selection') }}" class="btn btn-black"><i class="fa-chevron-left"></i> All Jobs</a>
                                <a id="add-row" class="btn btn-black"><i class="fa-plus"></i> Add Row</a>
                            </div>

                            <!-- Generate -->
                            <div class="pull-right">
                                {{--<a id="generate" class="btn btn-black"><i class="fa-refresh"></i> Auto-generate Users</a>--}}
                                <div class="btn-group" style="display: inline-block;">
                                    <button aria-expanded="false" type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa-refresh"></i> Auto-Generate <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-black" role="menu">
                                        <li><a id="generate-ids">UserIDs From Entered Names</a></li>
                                        <li><a id="generate-users">Random Users</a></li>
                                    </ul>
                                </div>
                                <a id="import" class="btn btn-black"><i class="fa-list-ol"></i> Import From Excel</a>
                                <a id="remove-incomplete" class="btn btn-black"><i class="fa-trash-o"></i> Remove Incomplete</a>
                            </div>

                            <div style="clear:both;"></div><br/>

                        {!! Form::open(['url' => 'dashboard/add-applicants']) !!}

                        <!-- User Add Form -->
                            <div class="user-forms">
                                @include('dashboard.users.partials._userform')
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <br/>
                                <div class="pull-right">
                                    {!! Form::button('Add Users' , ['class' => 'btn btn-primary btn-lg', 'id' => 'save']) !!}
                                </div>
                            </div>

                            {!! Form::close() !!}


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Templates -->
    <div class="templates" style="display:none;">
        @include('dashboard.users.partials._userform')
    </div>

    <!-- Upload File Modal -->
    <div class="modal fade" id="modal-import">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Upload Users</h4>
                </div>

                <div class="modal-body">
                    <div class="well">
                        <p>
                            Upload a spreadsheet of users for faster entry. The first row in the spreadsheet will be counted as the header.
                            Please make sure you have <b>email</b> and <b>name</b> as column headers in your first row, as these are required.
                            Accepted file types: <b>.xls</b>, <b>.xlsx</b>
                        </p>
                    </div>
                    {{--                    {!! Form::open(['url' => 'dashboard/users/import/', 'files' => true, 'id' => 'uploadform']) !!}--}}
                    {!! Form::file('file', ['id' => 'file']) !!}
                    {{--{!! Form::close() !!}--}}
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

    <!-- Auto Generate Users Modal -->
    <div class="modal fade" id="modal-generate">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Auto-generate Users</h4>
                </div>

                <div class="modal-body">
                    {!! Form::label('autogen-number', 'Number of Users To Generate') !!}
                    {!! Form::input('number', 'autogen-number', 1, ['class' => 'form-control input-lg']) !!}
                    <br/>
                    {!! Form::label('autogen-prefix', 'Username Prefix') !!}
                    {!! Form::text('autogen-prefix', $client->name, ['class' => 'form-control input-lg', 'id' => 'prefix']) !!}
                    <br/>
                    {!! Form::label('', 'Example Username:') !!}
                    <div class="well" id="example-username"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                    <button type="button" id="generate" class="btn btn-small-font btn-orange save-button">Generate Users</button>
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

            var opts = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Sidebar menu default
            $('.sidebar-menu-under .menu-category[data-parent="Users"]').show();

            $('.user-forms').on('click', '#apply-job-to-all', function(){
                if (confirm('Apply this selection to all rows?'))
                {
                    //var val = $(this).next().next('select').val();
                    var val = $(this).next('select').val();
                    $('select[name="job_id[]"]').val(val);
                }
            });

            $('#add-row').on('click', function(){
                $user_add_form = $('.templates .panel').clone();
                $('.user-forms').append($user_add_form);
//                $("#job_id", $user_add_form).select2({
//                    allowClear: true
//                }).on('select2-open', function()
//                {
//                    $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
//                });
            });

            $('#remove-incomplete').on('click', function(){
                $('.user-forms .panel').each(function() {
                    var email = $('input[name="email[]"]', this).val();
                    var name = $('input[name="name[]"]', this).val();

                    if (email == '' || name == '')
                        $(this).remove();
                });
            });

            $('#import').on('click', function(){
                $modal = $('#modal-import');
                $modal.modal('show').on('click', '#upload', function(){

                    var inputElement = $('input#file')[0];
                    var data = new FormData();
                    data.append('file', inputElement.files[0]);
                    var url = '/dashboard/users/upload';

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

                            if (data['users']) {
                                for (var i = 0; i < data['users'].length; i += 1) {
                                    var user = data['users'][i];
                                    $user_add_form = $('.templates .panel').clone();
                                    $user_add_form.find('input[name="email[]"]').val(user['email']);
                                    $user_add_form.find('input[name="name[]"]').val(user['name']);
                                    $user_add_form.find('input[name="username[]"]').val(user['username']);
                                    $('.user-forms').append($user_add_form);
                                }
                                $modal.modal('hide');
                            }

                            else {
                                alert('No users found in Excel document, or the file could not be read correctly.');
                            }

                            $modal.modal('hide');
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

            $('#generate-users').on('click', function() {
                $modal = $('#modal-generate').clone();
                $modal.modal('show').on('change', '#prefix', function(){
                    generateExampleUsername($modal);
                }).on('click', '#generate', function(){
                    generateUsernames($modal);
                    $modal.modal('hide');
                });
                generateExampleUsername($modal);

                $modal.on('hidden.bs.modal', function() {
                    $modal.remove();
                });
            });

            $('#generate-ids').on('click', function() {
                $('.user-forms .panel').each(function(){
                    var username = $('input[name="username[]"]', this).val();
                    if (username == '') {
                        var name = $('input[name="name[]"]', this).val();
                        var username = name.toLowerCase();
                        username = username.replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '');
                        username = username.replace(/\s+/g, '');
                        var suffix = randomString(4, '123456789');
                        username += suffix;
                        $('input[name="username[]"]', this).val(username);
                    }
                });
            });

            function randomString(length, chars)
            {
                var result = '';
                for (var i = length; i > 0; i--)
                    result += chars[Math.floor(Math.random() * chars.length)];

                return result;
            }

            $('.user-forms').on('click', '.remove-row-button', function(){
                $(this).closest('.panel').remove();
            });

            $('#save').on('click', function(){

                var errors = false;
                var email_error = false;
                var name_error = false;
                var username_error = false;
                $('.user-forms input').removeClass('error');

                $('.user-forms input').each(function(){

                    if ($(this).attr('name') == 'username[]' && $(this).val() == '') {
                        $(this).addClass('error');
                        errors = true;
                        username_error = true;
                    }

                    if ($(this).attr('name') == 'name[]' && $(this).val() == '') {
                        $(this).addClass('error');
                        errors = true;
                        name_error = true;
                    }

                    if ($(this).attr('name') == 'email[]' && $(this).val() != '' && ! isValidEmailAddress($(this).val())) {
                        $(this).addClass('error');
                        errors = true;
                        email_error = true;
                    }
                });

                if (errors) {
                    if (name_error)
                        toastr.error("Name fields are required.", "Name Error", opts);
                    if (username_error)
                        toastr.error("Username fields are required.", "Username Error", opts);
                    if (email_error)
                        toastr.error("Email fields must be valid email addresses.", "Email Error", opts);
                    return false;
                }

                var data = $('form').serialize();
                var url = window.location.pathname;

                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        var message = data['count'] + ' users added successfully!\n';
                        for (var i = 0; i < data['errors'].length; i += 1) {
                            message += '\n'+data['errors'][i];
                        }
                        alert(message);

                        var offset = 0;
                        for (var i = 0; i < data['users'].length; i += 1) {
                            if (data['users'][i]) {
                                $('.user-forms .panel:nth-child('+(i+1-offset)+')').remove();
                                offset += 1;
                            }
                        }

                        toastr.success(data['count']+" users added successfully!", "Success", opts);

                        //console.log(data);

                        if (data['download_link']) {
                            var link = data['download_link'];
                            var count = data['count'];
                            //$('#save').before('<a class="btn btn-black" target="_blank" href="'+link+'"><i class="fa-download"></i> Download Generated Users</a>');
                            $('.heading-row').after('<div class="row"><div class="alert alert-white"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><strong>'+count+' Users Generated!</strong> <a href="'+link+'">Download Excel file of generated users.</a></div></div>');
                        }
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });

            var opts = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            function isValidEmailAddress(emailAddress) {
                var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
                return pattern.test(emailAddress);
            }

            function generateExampleUsername($modal)
            {
                var data = {
                    'prefix': $('#prefix', $modal).val(),
                    'number': 1
                };
                var url = '/dashboard/users/generate_username';

                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        //console.log('username generated');
                        //console.log(data);
                        $('#example-username', $modal).html(data);
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            }

            function generateUsernames($modal)
            {
                var number = $('input[name="autogen-number"]', $modal).val();
                if (isNaN(number))
                    number = 1;

                var data = {
                    'prefix': $('#prefix', $modal).val(),
                    'number': number
                };
                console.log(data);
                var url = '/dashboard/users/generate_username';

                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        if (data) {
                            //$('.user-forms .panel').remove();
                            for (var i = 0; i < data.length; i += 1)
                            {
                                $user_add_form = $('.templates .panel').clone();
                                $user_add_form.find('input[name="username[]"]').val(data[i]);
                                $user_add_form.find('input[name="name[]"]').val(data[i]);
                                $('.user-forms').append($user_add_form);
                            }
                        }
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            }
        });
    </script>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
@stop