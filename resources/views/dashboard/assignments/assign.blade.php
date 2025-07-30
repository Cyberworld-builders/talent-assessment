@extends('dashboard.dashboard')

@section('styles')
    {{--<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@stop

@section('body-class')
    page-assignments
@stop

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Assign Assessment</h1>
            <p class="description">Assign these assessments to specific users.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <!-- Title -->
            {{--<div class="page-heading">--}}
                {{--<h1>{{ $assessment->name }}</h1>--}}
            {{--</div>--}}

            <!-- Assignments -->
            <div class="assignments">

                {!! Form::open(['url' => 'dashboard/assign']) !!}

                <!-- Assignment Details -->
                <div class="panel panel-default">
                    {{--<div class="panel-heading">--}}
                        {{--Assignment Details--}}
                    {{--</div>--}}

                    <h2>Assignment Details</h2>

                    <div class="panel-body">

                        <!-- Assessment -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="assessments[]" class="control-label">Assessments</label>
                                </div>
                                <div class="col-sm-8">
                                    {!! Form::select('assessments[]', $assessmentsArray, null, ['class' => 'form-control input-lg', 'id' => 'assessments', 'multiple']) !!}
                                    <script type="text/javascript">
                                        jQuery(document).ready(function($)
                                        {
                                            $("#assessments").select2({
                                                placeholder: 'Select Assessments',
                                                allowClear: true
                                            }).on('select2-open', function()
                                            {
                                                // Adding Custom Scrollbar
                                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <!-- Expires -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="expiration" class="control-label">Expires</label>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        {!! Form::text('expiration', Carbon\Carbon::tomorrow()->format('D, d M Y'), [
                                            'class' => 'form-control input-lg datepicker',
                                            'data-format' => 'D, dd M yyyy',
                                        ]) !!}
                                        <div class="input-group-addon">
                                            <i class="linecons-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Whitelabel -->
                        @role('admin')
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="whitelabel" class="control-label">White-label this assignment?</label>
                                    <p>White-labeled assessments will display the logo and background of the client to which the user belongs to.</p>
                                </div>
                                <div class="col-sm-8">
                                    {!! Form::checkbox('whitelabel', 1, 1, [
                                        'class' => 'icheck',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        @endrole

                        <!-- Send Email -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="send-email" class="control-label">Send out email to valid addresses?</label>
                                    <p>Users with a valid email address will receive an email notification.</p>
                                </div>
                                <div class="col-sm-8">
                                    {!! Form::checkbox('send-email', 1, null, [
                                        'class' => 'icheck reveal-field',
                                        'data-field-to-reveal' => 'field-email'
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Custom Assessment Fields -->
                @foreach ($assessmentsArray as $assessment_id => $assessment_name)
                    @if (\App\Assessment::find($assessment_id)->use_custom_fields)
                        <div class="panel panel-default reveal-when-selected selected-{{ $assessment_id }}" style="display:none;"> <!--What to do with this line-->
                            <div class="panel-heading">
                                Custom Fields For: <span class="label label-blue">{{ $assessment_name }}</span>
                            </div>
                            <div class="panel-body">
                                @foreach (\App\Assessment::find($assessment_id)->custom_fields['tag'] as $i => $custom_field)
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                {!! Form::label('customfield', 'The custom field:') !!}
                                                {!! Form::text('customfield', '['.$custom_field.']', ['class' => 'form-control input-lg', 'readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::label('custom_fields['.$assessment_id.'][type][]', 'Will be replaced with:') !!}
                                            {!! Form::select('custom_fields['.$assessment_id.'][type][]', [
                                                'custom' => 'Custom Value',
                                                'name' => 'Name',
                                                'username' => 'UserID',
                                                'email' => 'Email',
                                                'group-role' => 'Group Role',
                                                'expiration' => 'Expiration Date',
                                            ], null, ['class' => 'form-control input-lg custom-field-value']) !!}
                                            <script type="text/javascript">
                                                jQuery(document).ready(function($)
                                                {
                                                    $("#value").select2({
                                                        //placeholder: 'Select your value...',
                                                        //allowClear: true
                                                    }).on('select2-open', function()
                                                    {
                                                        // Adding Custom Scrollbar
                                                        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <div class="col-sm-3 col">
                                            <div class="form-group">
                                                {!! Form::label('custom_fields['.$assessment_id.'][value][]', 'This value:') !!}
                                                {!! Form::text('custom_fields['.$assessment_id.'][value][]', null, ['class' => 'form-control input-lg custom-field']) !!}
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="take-value-from-field">
                                            {!! Form::label('custom_fields['.$assessment_id.'][from]', 'Take values from:') !!}
                                            {!! Form::select('custom_fields['.$assessment_id.'][from]', [
                                                'self' => 'The user taking this assessment (Self Rater)',
                                                'all' => 'All users taking this assessment (Round Robin)',
                                                'group' => 'Users who are in the same group as assessment taker (Custom Grouping)',
                                                'user' => 'Specify a specific user for each assessment taker (Leader Rater)',
                                                //'self-all' => 'All users in this group, including self',
                                                //'admin' => 'Admin Account',
                                            ], null, ['class' => 'form-control input-lg take-value-from']) !!}
                                            <script type="text/javascript">
                                                jQuery(document).ready(function($)
                                                {
                                                    $("#from").select2({
                                                        //placeholder: 'Select your value...',
                                                        //allowClear: true
                                                    }).on('select2-open', function()
                                                    {
                                                        // Adding Custom Scrollbar
                                                        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Email Preview -->
                <div class="panel panel-default field-email">
                    <div class="panel-heading">
                        <h4>Email Preview</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            {{--<div class="col-sm-6">--}}
                                {{--<div class="form-group">--}}
                                    {{--{!! Form::label('email-from', 'From:') !!}--}}
                                    {{--{!! Form::text('email-from', 'AOE Science', ['class' => 'form-control input-lg']) !!}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('email-subject', 'Subject:') !!}
                                    {!! Form::text('email-subject', null, ['class' => 'form-control input-lg', 'placeholder' => 'New assessments have been assigned to you']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="well">
                                    {{--@include('emails.assignment', [--}}
                                        {{--'user' => \Auth::user(),--}}
                                        {{--'assessment' => App\Assessment::find(1),--}}
                                        {{--'expire_date' => \Carbon\Carbon::tomorrow(),--}}
                                        {{--'assignments_link' => '/assignments',--}}
                                        {{--'password' => 'password',--}}
                                        {{--'mock' => true,--}}
                                    {{--])--}}
                                    <h3>Hello, <b>John Smith</b></h3>
                                    <p>
                                        You have been assigned to complete the following assessments:<br/>
                                        - Assessment Name<br/>
                                        - Assessment Name<br/>
                                        - Assessment Name<br/>
                                    </p>
                                    <p><i>Note: These assignments will expire on <b>expiration date</b>.</i></p>
                                    <p>
                                        Login <a target="_blank" href="#null">here</a> to view your assignments. You can use the following credentials to log in:<br/>
                                        username: <i>user12345</i><br/>
                                        password: <i>password</i>
                                    </p>
                                    <br/>
                                    <div class="footer-text">&copy; {{ date('Y') }} The AOE Group</div>
                                </div>
                            </div>
                            {{--<div class="col-sm-6">--}}
                                {{--<textarea id="email-content" class="form-control input-lg">@include('emails.assignment', [--}}
                                    {{--'user' => \Auth::user(),--}}
                                    {{--'assessment' => App\Assessment::find(1),--}}
                                    {{--'expire_date' => \Carbon\Carbon::tomorrow(),--}}
                                    {{--'assignments_link' => '/assignments'--}}
                                {{--])</textarea>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                </div>

                <!-- Assign to Users -->
                <div class="panel panel-headerless">
                    <div class="panel-body">
                        <div class="pull-left">
                            <h4>Assign To These Users</h4>
                        </div>
                        <div class="pull-right">
                            <a id="add-row" class="btn btn-black"><i class="fa-plus"></i> Add Row</a>
                            <a id="add-users-from-client" class="btn btn-black"><i class="fa-user"></i> Add Users From Client</a>
                            <a id="import" class="btn btn-black"><i class="fa-edit"></i> Upload Custom Fields</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="user-forms">
                    @include('dashboard.assignments.partials._userform')
                </div>

                <!-- Submit Field -->
                <div class="form-group">
                    <br/>
                    <div class="pull-left">
                        {!! Form::button('Assign Assessments', ['class' => 'btn btn-black btn-lg', 'id' => 'submit_button']) !!}
                    </div>
                    <div class="clearfix"></div>
                </div>

                {!! Form::close() !!}

            </div>

            <div class="templates">
                @include('dashboard.assignments.partials._userform')
            </div>

            <!-- Add Users From Client Modal -->
            <div class="modal fade" id="modal-users">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Add Users From Client</h4>
                        </div>

                        <div class="modal-body">
                            {!! Form::select('client', $clientsArray, 0, ['class' => 'form-control input-lg', 'id' => 'client']) !!}
                            <script type="text/javascript">
                                jQuery(document).ready(function($)
                                {
                                    $("#client").select2({
                                        placeholder: '---',
                                        allowClear: true
                                    }).on('select2-open', function()
                                    {
                                        // Adding Custom Scrollbar
                                        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                    });
                                });
                            </script>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                            <button type="button" id="add-users" class="btn btn-small-font btn-orange">Add Users</button>
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
                                    Upload a spreadsheet of custom fields for faster entry. The first row in the spreadsheet will be counted as the header.
                                    Please make sure you have <b>Manager Email</b> and <b>Manager Name</b> as column headers in your first row, as these are required.
                                    Accepted file types: <b>.xls</b>, <b>.xlsx</b>
                                </p>
                            </div>
                            {!! Form::open(['url' => 'dashboard/assignments/import/', 'files' => true, 'id' => 'uploadform']) !!}
                            {!! Form::file('file', ['id' => 'file']) !!}
                            {!! Form::close() !!}
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

                    // Checkboxes
                    $('input.icheck').iCheck({
                        checkboxClass: 'icheckbox_square-aero',
                        radioClass: 'iradio_square-aero'
                    });

                    // Reveal hidden fields
                    $('.reveal-field').on('ifChecked', function(event){
                        $('.'+$(this).attr('data-field-to-reveal')).slideDown();
                    }).on('ifUnchecked', function(){
                        $('.'+$(this).attr('data-field-to-reveal')).slideUp();
                    });

                    // Check for fields that should already be revealed
                    $('.reveal-field').each(function(){
                        if ($(this).is(':checked')) {
                            $('.'+$(this).attr('data-field-to-reveal')).show();
                        }
                    });

                    // Check for custom fields when an assessment with custom fields is selected
                    $('#assessments').on('change', function(){
                        var val = $(this).val();
                        $('.reveal-when-selected').slideUp();
                        for (var i = 0; i < val.length; i += 1) {
                            $('.reveal-when-selected.selected-'+val[i]).slideDown();
                        }
                    });

                    // Reveal fields right away that need to be revealed
                    var val = $('#assessments').val();
                    if (val) {
                        $('.reveal-when-selected').slideUp();
                        for (var i = 0; i < val.length; i += 1) {
                            $('.reveal-when-selected.selected-'+val[i]).slideDown();
                        }
                    }

                    // Check for custom field value box
                    $('.custom-field-value').on('change', function(){
                        var val = $(this).val();
                        $custom_field = $(this).closest('.row').find('.custom-field');
                        $custom_field_col = $custom_field.closest('.col');

                        if (val == 'expiration')
                            $custom_field.attr('readonly', '').val($('input[name="expiration"]').val());
                        if (val == 'custom')
                            $custom_field.removeAttr('readonly').val('');
                        if (val == 'name')
                            $custom_field.attr('readonly', '').val('{name}');
                        if (val == 'username')
                            $custom_field.attr('readonly', '').val('{username}');
                        if (val == 'email')
                            $custom_field.attr('readonly', '').val('{email}');
                        if (val == 'role')
                            $custom_field.attr('readonly', '').val('{role}');

                        if (val == 'custom' || val == 'expiration') {
                            $custom_field_col.show();
//                            $(this).closest('.row').find('.take-value-from-field').fadeOut();
                            return;
                        }

//                        $(this).closest('.row').find('.take-value-from-field').fadeIn();
                        $custom_field_col.hide();
                    });

                    $('.take-value-from').on('change', function() {
                        var val = $(this).val();
                        $user_field = $('.user-forms').find('.from-user-field');

                        if (val == 'user')
                        {
                            $user_field.show();
                            $('#import').show();
                        }

                        else
                        {
                            $user_field.hide();
                            $('#import').hide();
                        }
                    });

                    var val = $('.take-value-from').val();

                    console.log(val);
                    $user_field = $('.user-forms').find('.from-user-field');

                    if (val == 'user')
                        $user_field.show();
                    else
                        $user_field.hide();

                    // Check for custom field value box right away
                    $('.custom-field-value').each(function(){
                        var val = $(this).val();
                        $custom_field = $(this).closest('.row').find('.custom-field');
                        $custom_field_col = $custom_field.closest('.col');

                        if (val == 'expiration')
                            $custom_field.attr('readonly', '').val($('input[name="expiration"]').val());
                        if (val == 'custom')
                            $custom_field.removeAttr('readonly').val('');
                        if (val == 'name')
                            $custom_field.attr('readonly', '').val('{name}');
                        if (val == 'username')
                            $custom_field.attr('readonly', '').val('{username}');
                        if (val == 'email')
                            $custom_field.attr('readonly', '').val('{email}');
                        if (val == 'role')
                            $custom_field.attr('readonly', '').val('{role}');

                        if (val == 'custom' || val == 'expiration') {
                            $custom_field_col.show();
//                            $(this).closest('.row').find('.take-value-from-field').fadeOut();
                            return;
                        }

//                        $(this).closest('.row').find('.take-value-from-field').fadeIn();
                        $custom_field_col.hide();
                    });

                    $('#add-row').on('click', function(){
                        $user_add_form = $('.templates .panel').clone();
                        $('.user-forms').append($user_add_form);
                    });

                    $('#add-users-from-client').on('click', function(){
                        $modal = $('#modal-users');
                        $modal.modal('show').on('click', '#add-users', function(){

                            var data = {
                                'client': $('#client').val(),
                            };
                            var url = '/dashboard/assign/add-users';

                            $.ajax({
                                type: 'post',
                                url: url,
                                data: data,
                                dataType: 'json',
                                success: function (data) {
                                    console.log(data);

                                    if (data['users']) {
                                        $('.user-forms .panel').remove();

                                        for (var i = 0; i < data['users'].length; i += 1) {
                                            var user = data['users'][i];

                                            $user_add_form = $('.templates .panel').clone();
                                            $user_add_form.find('input[name="username[]"]').val(user['username']);
                                            $user_add_form.find('input[name="email[]"]').val(user['email']);
                                            $user_add_form.find('input[name="name[]"]').val(user['name']);
                                            $user_add_form.find('.status').text('Existing user will be used').addClass('alert-info');
                                            $('.user-forms').append($user_add_form);
                                        }
                                    }

                                    $modal.modal('hide');
                                },
                                error: function (data) {
                                    console.log(data.status + ' ' + data.statusText);
                                    $('html').prepend(data.responseText);
                                }
                            });
                        });
                    });

                    $('.user-forms').on('click', '.remove-row-button', function(){
                        $(this).closest('.panel').remove();
                    });

                    $('#email-content').on('change', function(){
                        var val = $(this).val();
                    });

                    // Sidebar menu default
                    $('.sidebar-menu-under .menu-category[data-parent="Assessments"]').show();

                    $('.user-forms').on('change', 'input[name="username[]"]', function(){
                        var val = $(this).val();
                        var $row = $(this).closest('.row');

                        var $status = $row.find('.status');
                        $status.removeClass('alert-success').removeClass('alert-info').removeClass('alert-danger').text('');

                        //if (! isValidEmailAddress(val)) {
                        //    $status.text('Email not valid').addClass('alert-danger');
                        //    return false;
                        //}

                        var url = '/dashboard/check_user';

                        $.ajax({
                            type: 'post',
                            url: url,
                            data: { username: val },
                            dataType: 'json',
                            success: function(data) {
                                //console.log(data);

                                var $name = $row.find('#name');
                                //var $expires = $('#expires');

                                if (! data) {
                                    $status.text('New user will be created').addClass('alert-success');
                                    $('input[name="name[]"]', $row).val('');
                                    $('input[name="email[]"]', $row).val('');
                                }

                                else {
                                    $status.text('Existing user will be used').addClass('alert-info');
                                    $('input[name="name[]"]', $row).val(data.name);
                                    $('input[name="email[]"]', $row).val(data.email);
                                }

                                //$name.fadeIn();
                                //$expires.fadeIn();
                            },
                            error: function(data) {
                                console.log(data.status+' '+data.statusText);
                                $('html').prepend(data.responseText);
                            }
                        });
                    });

                    $('#import').on('click', function(){
                        $modal = $('#modal-import');
                        $modal.modal('show').on('click', '#upload', function(){
                            var form = $('#uploadform').get(0);
                            var data = new FormData(form);
                            var url = '/dashboard/assignments/upload';

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

                                    if (data['users']) {
                                        //$('.user-forms .panel').remove();
                                        $('.user-forms .row').each(function() {
                                            var $row = $(this);
                                            var $email = $('input[name="email[]"]', $row);
                                            var email = $email.val();

                                            var $name = $('input[name="name[]"]', $row);
                                            var name = $name.val();

                                            var found = false;

                                            for (var i = 0; i < data['users'].length; i += 1) {
                                                var user = data['users'][i];
                                                if (email == user['email'])
                                                {
                                                    $row = $email.closest('.row');
                                                    $('input[name="from_name[]"]', $row).val(user['manager_name']);
                                                    $('input[name="from_email[]"]', $row).val(user['manager_email']);
                                                    found = true;
                                                }
                                            }

                                            if (! found)
                                            {
                                                for (var i = 0; i < data['users'].length; i += 1) {
                                                    var user = data['users'][i];
                                                    if (name == user['name'])
                                                    {
                                                        $row = $email.closest('.row');
                                                        $('input[name="from_name[]"]', $row).val(user['manager_name']);
                                                        $('input[name="from_email[]"]', $row).val(user['manager_email']);
                                                        found = true;
                                                    }
                                                }
                                            }

                                            if (! found)
                                                console.log('no match found');
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

                    $('#submit_button').on('click', function(){
                        var error = false;
                        var assessment_error = false;

                        $('.user-forms .row').each(function(){
                            var $row = $(this);

                            $status = $row.find('.status');

                            if ($status.hasClass('alert-danger'))
                                $status.removeClass('alert-danger').text('');

                            var $email = $('input[name="email[]"]', $row);
                            var $name = $('input[name="name[]"]', $row);
                            var $username = $('input[name="username[]"]', $row);

                            $email.removeClass('error');
                            $name.removeClass('error');
                            $username.removeClass('error');

                            /*if ($email.val() == '' || ! isValidEmailAddress($email.val())) {
                                $email.addClass('error');
                                error = true;
                            }*/

                            if ($username.val() == '') {
                                $username.addClass('error');
                                error = true;
                            }

                            if ($name.val() == '') {
                                $name.addClass('error');
                                error = true;
                            }

                            if (error) {
                                $status.text('Username and Name fields are required.').addClass('alert-danger');
                                return false;
                            }
                        });

                        $select = $('select[name="assessments[]"]');

                        if ($select.val() == null) {
                            assessment_error = true;
                        }

                        if (assessment_error) {
                            toastr.error("You must select an assessment to assign.", "Error", opts);
                            return false;
                        }

                        if (error) {
                            toastr.error("Errors found in the form. Please resolve all errors and try again.", "Error", opts);
                            return false;
                        }

                        $("form:first").submit();
                    });

                    /*function isValidEmailAddress(emailAddress) {
                        var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
                        return pattern.test(emailAddress);
                    }*/
                });
            </script>

        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop