@extends('dashboard.dashboard')

@section('styles')
    {{--<link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('assets/js/daterangepicker/daterangepicker-bs3.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        input.error {
            border-color: red;
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
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
        .control-label {
            font-size: 18px;
        }
        .form-group {
            margin-bottom: 30px;
        }
        .label.label-blue {
            background-color: #9CC2CB;
        }
    </style>
@stop

@section('sidebar-class')
    @role('admin')
    collapsed
    @endrole
@stop

@section('content')

    <div class="page-title">

        <div class="title-env">
            <h1 class="title">Assign Assessments</h1>
            {{--<p class="description">Assign this assessment to specific users.</p>--}}
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

                    <h2>Assignment Settings</h2>

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

                        <!-- Expiration -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="expiration" class="control-label">Expiration</label>
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
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="whitelabel" class="control-label">White-label these assignments?</label>
                                    <p>White-labeled assessments will display the logo and background of the client to which the user belongs to.</p>
                                </div>
                                <div class="col-sm-8">
                                    {!! Form::checkbox('whitelabel', 1, 1, [
                                        'class' => 'icheck',
                                    ]) !!}
                                </div>
                            </div>
                        </div>

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
                        <div class="panel panel-default reveal-when-selected selected-{{ $assessment_id }}" style="display:none;">
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
                                        <div class="col-sm-3 col">
                                            <div class="form-group from-user-field">
                                                {!! Form::label('custom_fields['.$assessment_id.'][user][]', 'Of this user:') !!}
                                                {!! Form::text('custom_fields['.$assessment_id.'][user][]', null, ['class' => 'form-control input-lg from-user']) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="take-value-from-field" style="">
                                            {!! Form::label('custom_fields['.$assessment_id.'][from]', 'Take values from:') !!}
                                            {!! Form::select('custom_fields['.$assessment_id.'][from]', [
                                                'self' => 'The user taking this assessment (Self Rater)',
                                                'user' => 'Specify a specific user (Leader Rater)',
                                                'all' => 'All users taking this assessment (Round Robin)',
                                                'group' => 'Users who are in the same group as assessment taker (Custom Grouping)',
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
                        <div class="panel panel-default field-email" style="display:none">
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
                        <div class="panel panel-headerless" style="margin-bottom:5px;">
                            <div class="panel-body">
                                <div class="pull-left">
                                    <h4>Assign To These Users</h4>
                                </div>
                                <div class="pull-right">
                                    {{--<a id="add-row" class="btn btn-black"><i class="fa-plus"></i> Add Row</a>--}}
                                    <a id="add-users-from-client" class="btn btn-black"><i class="fa-user"></i> Add Users From Client</a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="user-forms">
{{--                            @include('dashboard.assignments.partials._previewitem')--}}
                        </div>

                        <div class="panel panel-headerless" style="margin-bottom:5px;">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <h3 style="font-size: 17px;"><i class="fa fa-plus"></i> Add User</h3>
                                    </div>
                                    <div class="col-sm-10">
                                        {!! Form::select('user_add_form', $usersArray, null, ['class' => 'form-control input-lg user-add-form', 'id' => 'user_add_form', 'multiple']) !!}
                                        <script type="text/javascript">
                                            jQuery(document).ready(function($)
                                            {
                                                $("#user_add_form").select2({
                                                    placeholder: 'Search for a user by Name, Email, or UserId',
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

            <div class="templates" style="display:none;">
                @include('dashboard.assignments.partials._previewitem')
                @include('dashboard.assignments.partials._assessment')
                @include('dashboard.assignments.partials._customfields')
                @include('dashboard.assignments.partials._customfield')
            </div>

            <!-- Add Users From Client Modal -->
            <div class="modal fade" id="modal-users">
                <div class="modal-dialog" style="width: 60%;">
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

                    // Remove cached values from user add form
                    function clear_user_add_form()
                    {
                        $('.user-add-form').val('');
                        $('.user-add-form .select2-search-choice').remove();
                        $('.user-add-form .select2-input').attr('placeholder', 'Search for a user by Name, Email, or UserId').width(380);
                    }
                    clear_user_add_form();

                    // User add form
                    $('.user-add-form').on('change', function(){
                        var userid = $(this).val();

                        // Add users
                        add_user(userid[0]);

                        clear_user_add_form();
                    });

                    function add_user(userid)
                    {
                        var assessment_ids = $('select[name="assessments[]"]').val();
                        var data = {
                            'id': userid,
                            'assessments': assessment_ids
                        };
                        var url = '/dashboard/assign/add-user';

                        $.ajax({
                            type: 'post',
                            url: url,
                            data: data,
                            dataType: 'json',
                            success: function (data) {
                                console.log(data);

                                if (data['user'])
                                {
                                    var user = data['user'];
                                    $user_add_form = $('.templates .panel').clone();
                                    $user_add_form.find('input[name="user[]"]').val(user['id']);
                                    $user_add_form.find('#user-name').text(user['name']);
                                    $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');

                                    $assessment_tabs = $user_add_form.find('.assessment-tabs');
                                    if (data['assessments'])
                                    {
                                        for (var i = 0; i < data['assessments'].length; i += 1)
                                        {
                                            var assessment = data['assessments'][i];
                                            $assessment_tab = $('.templates .assessment').clone();
                                            $assessment_tab.find('#assessment-name').text(assessment['name']);
                                            $assessment_tab.find('#assessment-logo').attr('src', assessment['logo']);

                                            if (assessment['custom_fields'])
                                            {
                                                $custom_fields_field = $assessment_tab.find('.custom-fields-field');
                                                $custom_fields = $('.templates .custom-fields').clone();
                                                $custom_fields_field.append($custom_fields);
                                                $custom_fields_tabs = $custom_fields.find('.custom-fields-tabs');

                                                for (var j = 0; j < assessment['custom_fields']['tag'].length; j += 1)
                                                {
                                                    var tag = assessment['custom_fields']['tag'][j];
                                                    $custom_field = $('.templates .custom-field').clone();
                                                    $custom_field.find('#custom-tag').text('[' + tag + ']');
                                                    $custom_fields_tabs.append($custom_field);
                                                }
                                            }
                                            else
                                                $assessment_tab.find('.right-arrow').remove();

                                            if (i > 0)
                                                $assessment_tabs.append('<hr/>');
                                            $assessment_tabs.append($assessment_tab);
                                        }
                                    }

                                    $('.user-forms').append($user_add_form);
                                }
                            },
                            error: function (data) {
                                console.log(data.status + ' ' + data.statusText);
                                $('html').prepend(data.responseText);
                            }
                        });
                    }


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
                        $user_field = $(this).closest('.panel-body').find('.from-user-field');

                        if (val == 'user')
                            $user_field.show();

                        else
                            $user_field.hide();
                    });

                    var val = $('.take-value-from').val();
                    $user_field = $('.take-value-from').closest('.panel-body').find('.from-user-field');

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

                    @if ($message)
                        toastr.success("{{ $message }}", "Success", opts);
                    @endif
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