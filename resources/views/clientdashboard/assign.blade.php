@extends('dashboard.clientdashboard')

@section('styles')
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
            z-index: 10;
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
        .assignment-user {
            border: 1px solid #eee;
            padding: 20px;
            position: relative;
        }
        .assignment-user .row:before {
            display: none;
        }
        .assignment-user .user-tab .fa-user,
        .assignment-user .user-tab .linecons-user {
            float: left;
            font-size: 36px;
            padding-right: 10px;
            color: rgb(207, 207, 207);
        }
        .assignment-user .user-tab h3 {
            font-size: 17px;
            font-weight: bold;
            margin: 0;
        }
        .assignment-user .user-tab .right-arrow {
            float: right;
            position: relative;
            top: -10px;
        }
        .assignment-user .target-label {
            margin-left: 10px;
            padding: 7px 14px;
            font-size: 15px;
            position: relative;
            top: 1px;
        }
        .assignment-user .target-span {
            display: inline-block;
            margin-top: 9px;
        }
        .assignment-user .target-button {
            margin-right: 30px;
            position: relative;
            top: 5px;
        }
        p.small .badge {
            font-size: 11px;
            font-weight: normal;
            border-radius: 1px;
            padding: 7px;
            margin: 0 4px 7px 0;
        }
        .form-group .form-control.select2-container,
        .modal-body .form-control.select2-container {
            border: 2px solid #e4e4e4;
        }
        .form-group .form-control.select2-container:hover,
        .form-group .form-control.select2-container:focus,
        .form-group .form-control.select2-container:active,
        .modal-body .form-control.select2-container:hover,
        .modal-body .form-control.select2-container:focus,
        .modal-body .form-control.select2-container:active {
            border-color: #9FC2CC;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="header">
        <h1>
            <i class="fa-line-chart"></i><br/>
            Assign Assessments
        </h1>
    </div>

    <div class="content">
        <div class="wrapper">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-6">
                    <div class="title-env">
                        <h1>Assign Assessments</h1>
                        <p>Assign assessments to specific applicants.</p>
                    </div>
                </div>
            </div>

            {{-- Assignments --}}
            {!! Form::open(['url' => 'dashboard/assign']) !!}
                <div class="assign-panel" style="margin-top: 10px;">

                    <h3>Basic Info</h3><br/>

                        {{-- Assessment --}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    {!! Form::label('assessments[]', 'Assessments', ['class' => 'control-label']) !!}
                                    <p class="small text-muted">The assessments which will be assigned to these users.</p>
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

                        {{-- Exipration Field --}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    {!! Form::label('expiration', 'Expiration Date', ['class' => 'control-label']) !!}
                                    <p class="small text-muted">Users will not be able to start or finish unfinished assignments after they have expired.</p>
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

                        {{-- Send Email --}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    {!! Form::label('send-email', 'Email Notification', ['class' => 'control-label']) !!}
                                    <p class="small text-muted">Users with a valid email address will receive a notification for this assessment.</p>
                                </div>
                                <div class="col-sm-8">
                                    {!! Form::select('send-email', [
                                        0 => 'No',
                                        1 => 'Yes',
                                    ], 0, ['class' => 'form-control input-lg', 'id' => 'send-email']) !!}
                                    <script type="text/javascript">
                                        jQuery(document).ready(function($)
                                        {
                                            $("#send-email").select2({
    //                                            placeholder: 'Select Assessments',
    //                                            allowClear: true
    //                                        }).on('select2-open', function()
    //                                        {
    //                                             Adding Custom Scrollbar
    //                                            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        {{-- Email Preview Window --}}
                        <div class="field-email" style="display: none;">

                            <br/><br/>
                            <h3>Email Preview</h3><br/>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        {!! Form::label('email-subject', 'Subject', ['class' => 'control-label']) !!}
                                        <p class="small text-muted">The subject line of the email that will go out to assigned users.</p>
                                    </div>
                                    <div class="col-sm-8">
                                        {!! Form::text('email-subject', null, ['class' => 'form-control input-lg', 'placeholder' => 'New assessments have been assigned to you']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        {!! Form::label('email-body', 'Body', ['class' => 'control-label']) !!}
                                        <p class="small text-muted">
                                            The body content of the email that will go out to assigned users.<br/>
                                            Note: You can use the following shortcodes in the body email to display specific information about the assessment or the user.<br/><br/>
                                            <span class="badge badge-white">[name]</span>
                                            <span class="badge badge-white">[username]</span>
                                            <span class="badge badge-white">[email]</span>
                                            <span class="badge badge-white">[password]</span>
                                            <span class="badge badge-white">[login-link]</span>
                                            <span class="badge badge-white">[assessments]</span>
                                            <span class="badge badge-white">[expiration-date]</span>
                                        </p>
                                    </div>
                                    <div class="col-sm-8">
                                        {!! Form::hidden('email-body', $emailBody) !!}
                                        <div class="well email-body-preview">
                                            {!! $emailBody !!}
                                        </div>
                                        <div class="btn btn-small btn-black edit-email-body" style="margin-top: 10px;float:right;">Edit</div>
                                    </div>
                                </div>
                            </div>

                            {{--<div class="col-sm-6">--}}
                            {{--<div class="form-group">--}}
                            {{--{!! Form::label('email-subject', 'Subject:') !!}--}}
                            {{--{!! Form::text('email-subject', null, ['class' => 'form-control input-lg', 'placeholder' => 'New assessments have been assigned to you']) !!}--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-6"></div>--}}

                            {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}
                            {{--<div class="well">--}}
                            {{--<h3>Hello, <b>John Smith</b></h3>--}}
                            {{--<p>--}}
                            {{--You have been assigned to complete the following assessments:<br/>--}}
                            {{--- Assessment Name<br/>--}}
                            {{--- Assessment Name<br/>--}}
                            {{--- Assessment Name<br/>--}}
                            {{--</p>--}}
                            {{--<p><i>Note: These assignments will expire on <b>expiration date</b>.</i></p>--}}
                            {{--<p>--}}
                            {{--Login <a target="_blank" href="#null">here</a> to view your assignments. You can use the following credentials to log in:<br/>--}}
                            {{--username: <i>user12345</i><br/>--}}
                            {{--password: <i>password</i>--}}
                            {{--</p>--}}
                            {{--<br/>--}}
                            {{--<div class="footer-text">&copy; {{ date('Y') }} AOE Science</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                        </div>

                        <br/>
                        <h3>Assign To</h3><br/>

                        <div class="pull-left">
                            <a id="add-user-modal" class="btn btn-black"><i class="fa-user"></i> Specific User</a>
                            <a id="add-applicants-from-job" class="btn btn-black"><i class="fa-user"></i> Add All Viable Applicants</a>
                        </div>
                        <div class="pull-right">
                            {{--@if ($assessment->target > 0)--}}
                                {{--<a id="import" class="btn btn-black"><i class="fa-edit"></i> Upload Targets From Excel</a>--}}
                            {{--@endif--}}
                        </div>
                        <div style="clear:both;"></div>

                        {{-- User Forms --}}
                        <div class="user-forms"></div>

                </div>

                {{-- Submit Field --}}
                <div class="form-group">
                    <br/>
                    <div class="pull-right">
                        {!! Form::submit('Assign', ['class' => 'btn btn-primary btn-lg', 'id' => 'save']) !!}
                    </div>
                    <div class="clearfix"></div>
                </div>

                {{-- Templates --}}
                <div class="templates" style="display:none;">
                    @include('dashboard.assessments.partials._previewitem', [
                        'target' => 0,
                    ])
                    {{--@include('dashboard.assignments.partials._assessment')--}}
                    {{--@include('dashboard.assignments.partials._customfields')--}}
                    {{--@include('dashboard.assignments.partials._customfield')--}}
                </div>

                {{-- Add Specific User Modal --}}
                <div class="modal fade" id="modal-user">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Add A Specific User</h4>
                            </div>

                            <div class="modal-body">
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

                            <div class="modal-footer">
                                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Close</button>
                                {{--<button type="button" id="add-users" class="btn btn-small-font btn-orange">Add User</button>--}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Users From Client Modal --}}
                <div class="modal fade" id="modal-users">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Add All Users From Specific Client</h4>
                            </div>

                            <div class="modal-body">
                                {{--{!! Form::select('client', $clientsArray, 0, ['class' => 'form-control input-lg', 'id' => 'client']) !!}--}}
                                {{--<script type="text/javascript">--}}
                                    {{--jQuery(document).ready(function($)--}}
                                    {{--{--}}
                                        {{--$("#client").select2({--}}
                                            {{--placeholder: '---',--}}
                                            {{--allowClear: true--}}
                                        {{--}).on('select2-open', function()--}}
                                        {{--{--}}
                                            {{--// Adding Custom Scrollbar--}}
                                            {{--$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();--}}
                                        {{--});--}}
                                    {{--});--}}
                                {{--</script>--}}
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                                <button type="button" id="add-users" class="btn btn-small-font btn-orange">Add Users</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Target Modal --}}
                <div class="modal fade" id="modal-target">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Set Target User</h4>
                            </div>

                            <div class="modal-body">
                                {!! Form::select('target_add_form', $usersArray, null, ['class' => 'form-control input-lg target-add-form', 'id' => 'target_add_form', 'multiple']) !!}
                                <script type="text/javascript">
                                    jQuery(document).ready(function($)
                                    {
                                        $("#target_add_form").select2({
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

                            <div class="modal-footer">
                                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Close</button>
                                {{--<button type="button" id="add-users" class="btn btn-small-font btn-orange">Add User</button>--}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload File Modal --}}
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

                {{-- WYSIWYG Modal --}}
                <div class="modal fade" id="modal-wysiwyg">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Modal Title</h4>
                            </div>

                            <div class="modal-body"><textarea id="Editor" class="form-control input-lg "></textarea></div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-small-font btn-orange save-button">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scripts --}}
                <script type="text/javascript">
                    jQuery(document).ready(function ($)
                    {
                        // Set headers for AJAX
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                            }
                        });

                        // Convert shortcodes
                        var content = $('input[name="email-body"]').val();
                        var preview_content = do_shortcodes(content);
                        $('.email-body-preview').html(preview_content);

                        // Show email preview panel if sending out emails
                        $('select[name="send-email"]').on('change', function()
                        {
                            var val = $(this).val();
                            if (val == 1)
                                $('.field-email').slideDown();
                            else
                                $('.field-email').slideUp();
                        });

                        // Check if send-email is already selected
                        if ($('select[name="send-email"]').val() == 1)
                            $('.field-email').show();

                        // Edit email body with wysiwyg
                        $('.edit-email-body').on('click', function(){
                            var current_content = $('input[name="email-body"]').val();

                            $edit_field = $('input[name="email-body"]');
                            $preview_field = $('.email-body-preview');

                            $modal = $('#modal-wysiwyg');
                            $modal.find('.modal-title').html('Email Body');
                            $modal.find('.save-button').attr('id', 'save-email-body');

                            $textarea = $modal.find('textarea').html(current_content);
                            if (! CKEDITOR.instances.Editor)
                                $editor = $textarea.ckeditor();
                            else
                                CKEDITOR.instances.Editor.setData(current_content);
                            $modal.modal('show');

                            $modal.on('click', '#save-email-body', function(){

                                var new_content = CKEDITOR.instances.Editor.document.getBody().getHtml();

                                if (new_content.trim() == '')
                                    new_content = '';

                                // Replace shortcodes just for preview
                                preview_content = do_shortcodes(new_content);

                                $edit_field.val(new_content);
                                $preview_field.html(preview_content);

                                $modal.modal('hide');
                            });
                        });

                        function do_shortcodes(content)
                        {
                            var new_content = content;
                            new_content = new_content.split('[name]').join('John Smith');
                            new_content = new_content.split('[email]').join('john@test.com');
                            new_content = new_content.split('[username]').join('john123');
                            new_content = new_content.split('[password]').join('Nj1NB73w');
                            new_content = new_content.split('[assessments]').join('- Example Assessment');
                            new_content = new_content.split('[expiration-date]').join('Monday, January 1st, 2017');
                            new_content = new_content.split('[login-link]').join('http://aoescience.com/login');

                            return new_content;
                        }

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

                        // Ajax call to add a user
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
                                        $user_add_form = $('.templates .assignment-user').clone();
                                        $user_add_form.find('input[name="user[]"]').val(user['id']);
                                        $user_add_form.find('#user-name').text(user['name']);
                                        $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');


        //                        $assessment_tabs = $user_add_form.find('.assessment-tabs');
        //                        if (data['assessments'])
        //                        {
        //                            for (var i = 0; i < data['assessments'].length; i += 1)
        //                            {
        //                                var assessment = data['assessments'][i];
        //                                $assessment_tab = $('.templates .assessment').clone();
        //                                $assessment_tab.find('#assessment-name').text(assessment['name']);
        //                                $assessment_tab.find('#assessment-logo').attr('src', assessment['logo']);
        //
        //                                if (assessment['custom_fields'])
        //                                {
        //                                    $custom_fields_field = $assessment_tab.find('.custom-fields-field');
        //                                    $custom_fields = $('.templates .custom-fields').clone();
        //                                    $custom_fields_field.append($custom_fields);
        //                                    $custom_fields_tabs = $custom_fields.find('.custom-fields-tabs');
        //
        //                                    for (var j = 0; j < assessment['custom_fields']['tag'].length; j += 1)
        //                                    {
        //                                        var tag = assessment['custom_fields']['tag'][j];
        //                                        $custom_field = $('.templates .custom-field').clone();
        //                                        $custom_field.find('#custom-tag').text('[' + tag + ']');
        //                                        $custom_fields_tabs.append($custom_field);
        //                                    }
        //                                }
        //                                else
        //                                    $assessment_tab.find('.right-arrow').remove();
        //
        //                                if (i > 0)
        //                                    $assessment_tabs.append('<hr/>');
        //                                $assessment_tabs.append($assessment_tab);
        //                            }
        //                        }

                                        $('.user-forms').append($user_add_form);
                                    }
                                },
                                error: function (data) {
                                    console.log(data.status + ' ' + data.statusText);
                                    $('html').prepend(data.responseText);
                                }
                            });
                        }

                        // Target add form
        //        $('.target-add-form').on('change', function(){
        //            var targetid = $(this).val();
        //
        //            // Add users
        //            add_target(userid, targetid[0]);
        //
        //            clear_target_add_form();
        //        });

                        $('#add-applicants-from-job').on('click', function(){
        //                        $modal = $('#modal-users');
        //                        $modal.modal('show').on('click', '#add-users', function(){

                            var data = {
                                'job': {{ $job->id }},
                            };
                            var url = '/dashboard/assign/add-applicants';

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

                                            $user_add_form = $('.templates .assignment-user').clone();
                                            $user_add_form.find('input[name="user[]"]').val(user['id']);
                                            $user_add_form.find('#user-name').text(user['name']);
                                            $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');

                                            $('.user-forms').append($user_add_form);
                                        }
                                    }

        //                                    $modal.modal('hide');
                                },
                                error: function (data) {
                                    console.log(data.status + ' ' + data.statusText);
                                    $('html').prepend(data.responseText);
                                }
                            });
        //                        });
                        });

                        // Remove cached values from target add form
                        function clear_target_add_form()
                        {
                            $('.target-add-form').val('');
                            $('.target-add-form .select2-search-choice').remove();
                            $('.target-add-form .select2-input').attr('placeholder', 'Search for a user by Name, Email, or UserId').width(380);
                        }
                        clear_target_add_form();

                        // Ajax call to set the target
                        function add_target(userid, targetid)
                        {
                            var assessment_ids = $('select[name="assessments[]"]').val();
                            var data = {
                                'id': targetid,
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
                                        $row = $('input[name="user[]"][value="'+userid+'"]').closest('.assignment-user');
                                        $row.find('input[name="target[]"]').val(user['id']);
                                        $row.find('.target-label').text(user['name']+" ("+user['email']+")");
        //                        $user_add_form = $('.templates .assignment-user').clone();
        //                        $user_add_form.find('input[name="user[]"]').val(user['id']);
        //                        $user_add_form.find('#user-name').text(user['name']);
        //                        $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');
        //                        $('.user-forms').append($user_add_form);
                                    }
                                },
                                error: function (data) {
                                    console.log(data.status + ' ' + data.statusText);
                                    $('html').prepend(data.responseText);
                                }
                            });
                        }

                        // Remove User
                        $('.user-forms').on('click', '.remove-row-button', function(){
                            $(this).closest('.assignment-user').remove();
                        });

                        // Add Specific Users
                        $('#add-user-modal').on('click', function(){
                            $modal = $('#modal-user');
                            $modal.modal('show');
                        });

                        // Add All Users From Client
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

                                                $user_add_form = $('.templates .assignment-user').clone();
                                                $user_add_form.find('input[name="user[]"]').val(user['id']);
                                                $user_add_form.find('#user-name').text(user['name']);
                                                $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');
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

                        // Set the Target
                        $('.user-forms').on('click', '#add-target-modal', function(){
                            $row = $(this).closest('.assignment-user');
                            var userid = $row.find('input[name="user[]"]').val();
                            $modal = $('#modal-target');

                            $modal.on('show.bs.modal', function (e) {
        //               alert(userid);
                            });

                            $modal.modal('show').on('change', '.target-add-form', function(){
                                var targetid = $(this).val();
                                add_target(userid, targetid[0]);
                                clear_target_add_form();
                            });
                        });

                        // Import Targets From Excel
                        $('#import').on('click', function(){
                            $modal = $('#modal-import');
                            $modal.modal('show').on('click', '#upload', function()
                            {
                                var inputElement = $('input#file')[0];
                                var data = new FormData();
                                data.append('file', inputElement.files[0]);
                                var url = '/dashboard/assessments/upload';

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
                                            $('.user-forms .assignment-user').each(function()
                                            {
                                                var $row = $(this);
                                                var $id = $('input[name="user[]"]', $row);
                                                var id = $id.val();
                                                var found = false;

                                                // Loop through the uploaded users, grabbing their targets
                                                for (var i = 0; i < data['users'].length; i += 1)
                                                {
                                                    var user = data['users'][i];
                                                    if (id == user['id'])
                                                    {
                                                        $row.find('input[name="target[]"]').val(user['target_id']);
                                                        $row.find('input[name="role[]"]').val(user['role']);
                                                        $row.find('.target-label').text(user['target_name']+" ("+user['target_email']+")");
                                                        found = true;
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

                        // Toaster Options
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
                    });
                </script>
            {!! Form::close() !!}

        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
@stop

