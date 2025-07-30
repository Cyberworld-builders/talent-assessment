<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <h3>Basic Info</h3><br/>

            @if (! $edit)
                {{-- Assessment --}}
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::label('assessments[]', 'Assessments', ['class' => 'control-label assessments-field']) !!}
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
                                    }).on('select2-open', function() {
                                        // Adding Custom Scrollbar
                                        //$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                    }).on('change', function() {
                                        $('.hidden-group').slideDown();
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            @endif
            <div class="hidden-group" <?php echo ($edit || $oldInput) ? '' : 'style="display:none;"' ?>>

                {{-- Expiration Field --}}
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::label('expiration', 'Expiration Date', ['class' => 'control-label']) !!}
                            <p class="small text-muted">Users will not be able to start or finish unfinished assignments after they have expired.</p>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group">
                                @if (! $edit)
                                    {!! Form::text('expiration', Carbon\Carbon::tomorrow()->format('D, d M Y'), [
                                        'class' => 'form-control input-lg datepicker',
                                        'data-format' => 'D, dd M yyyy',
                                    ]) !!}
                                @else
                                    {!! Form::text('expiration', $assignment->expiration, [
                                        'class' => 'form-control input-lg datepicker',
                                        'data-format' => 'D, dd M yyyy',
                                    ]) !!}
                                @endif
                                <div class="input-group-addon">
                                    <i class="linecons-calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Whitelabel Field --}}
                @role('admin')
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('whitelabel', 'White-Label', ['class' => 'control-label']) !!}
                                <p class="small text-muted">White-labeled assessments will display the logo and background of the client to which the user belongs to.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('whitelabel', [
                                    0 => 'No',
                                    1 => 'Yes',
                                ], ($edit ? $assignment->whitelabel : 0), ['class' => 'form-control input-lg']) !!}
                            </div>
                        </div>
                    </div>
                @endrole

                {{-- Tie To Specific Job --}}
                @role('admin')
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('job_id', 'Lock To Specific Job', ['class' => 'control-label']) !!}
                                <p class="small text-muted">Assessments locked to a job will have their results factored in only for that specific job. Use this setting for validations.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('job_id', $jobsArray, ($edit ? $assignment->job_id : 0), ['class' => 'form-control input-lg']) !!}
                            </div>
                        </div>
                    </div>
                @endrole

                {{-- Make Part of Existing Survey --}}
                @if (!$edit || ($edit && $assessment->target))
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::label('created_at', 'Add To Existing Survey', ['class' => 'control-label']) !!}
                            <p class="small text-muted">Assign this assessment as part of an existing survey of Development assessments that you assigned previously to other users.</p>
                        </div>
                        <div class="col-sm-8">
                            {!! Form::select('created_at', $surveysArray, null, ['class' => 'form-control input-lg', 'id' => 'surveys']) !!}
                            <script type="text/javascript">
                                jQuery(document).ready(function($)
                                {
                                    $("#surveys").select2({
                                        placeholder: 'No',
                                        allowClear: true
                                    }).on('select2-open', function() {
                                        // Adding Custom Scrollbar
                                        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
                @endif

                @if (! $edit)
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
                                ], 0, ['class' => 'form-control input-lg']) !!}
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
                    </div>

                    <br/>
                    <h3>Assign To</h3><br/>

                    {{-- Helper Buttons --}}
                    <div class="pull-left">
                        <a id="add-user-modal" class="btn btn-black"><i class="fa-user"></i> Specific User</a>
                        <a id="add-users-from-client" class="btn btn-black"><i class="fa-users"></i> All Users</a>
                        <a id="add-users-from-groups" class="btn btn-black"><i class="fa-users"></i> From Groups</a>
                        @if (count($client->jobs))
                            <div class="btn-group" style="display: inline-block; margin-left:5px;">
                                <button aria-expanded="false" type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa-briefcase"></i> From Job <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-black" role="menu">
                                    @foreach ($client->jobs as $job)
                                        <li><a class="add-by-job" data-job-id="{{ $job->id }}">{{ $job->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @role('admin|reseller')
                            <div class="btn-group" style="display: inline-block; margin-left:5px;">
                                <button aria-expanded="false" type="button" class="btn btn-black dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa-briefcase"></i> From Job Family <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-black" role="menu">
                                    @foreach ($jobFamilies as $family)
                                        <li><a class="add-by-job-family">{{ $family }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endrole
                    </div>

                    {{-- Upload --}}
                    <div class="pull-right">
                        <a id="import" class="btn btn-black"><i class="fa-edit"></i> Upload Targets From Excel</a>
                    </div>
                    <div style="clear:both;"></div>

                    {{-- User Forms --}}
                    <div class="user-forms">
                        @if ($oldInput)
                            @foreach ($oldInput['user'] as $i => $userId)
                                <?php
                                    if (! $userId) continue;

                                    $tempInputs = [
										'target' => 1,
										'tempUser' => \App\User::find($userId)
                                    ];

                                    if ($oldInput['target'][$i])
                                        $tempInputs['tempTarget'] = \App\User::find($oldInput['target'][$i]);

                                    if ($oldInput['role'][$i])
                                        $tempInputs['tempRole'] = $oldInput['role'][$i];
                                ?>
                                @include('dashboard.assessments.partials._previewitem', $tempInputs)
                            @endforeach
                        @endif
                    </div>
                @else
                    <br/>
                    <h3>Assigned To: </h3>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="well">
                                <strong>{{ $user->name }}</strong><br/>
                                Username: <i>{{ $user->username }}</i><br/>
                                Email: <i>{{ $user->email }}</i><br/>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Submit Field --}}
<div class="form-group">
    <br/>
    <div class="pull-right">
        @if (! $edit)
            {!! Form::submit($buttonName, ['class' => 'btn btn-primary btn-lg', 'id' => 'save']) !!}
        @else
            {!! Form::submit($buttonName, ['class' => 'btn btn-primary btn-lg']) !!}
        @endif
    </div>
    <div class="clearfix"></div>
</div>

@if (! $edit)
    {{-- Templates --}}
    <div class="templates" style="display:none;">
        @include('dashboard.assessments.partials._previewitem', [
            //'target' => $assessment->target,
            'target' => 1,
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
                    <h4 class="modal-title">Upload Targets</h4>
                </div>

                <div class="modal-body">
                    <div class="well" style="overflow:hidden;">
                        <p>
                            Upload a spreadsheet of targets for faster entry. Make sure you already have the users for which you wish to set targets added under the Assign To section.
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

    {{-- Verification Modal --}}
    <div class="modal fade" id="modal-verify">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Duplicate Assessments Found</h4>
                </div>

                <div class="modal-body"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-small-font btn-orange ok-button">Ok</button>
                </div>
            </div>
        </div>
    </div>

    {{-- New Users Modal --}}
    <div class="modal fade" id="modal-nonusers">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Uploading Non-Existing Users</h4>
                </div>

                <div class="modal-body">
                    <p>It appears that you are trying to upload targets for users that have not been created yet:</p>
                    <ul class="nonusers-list"></ul>
                    <p>Would you like to create new user accounts for these people?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-small-font btn-orange ok-button">Create User Accounts</button>
                </div>
            </div>
        </div>
    </div>
@endif

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

        // New save button
        $('#save').on('click', function(e) {
            e.preventDefault();

            var users = $('.user-forms input[name="user[]"]').val();
            if (! users)
            {
                toastr.error('You have no users added to assign the assessment to.', "Error", opts);
                return false;
            }

            $.ajax({
                type: 'post',
                url: '/dashboard/clients/{{ $client->id }}/assign/verify',
                data: $('form').serialize(),
                dataType: 'json',
                success: function (data)
                {
                    // Show modal verify box
                    if (data)
                    {
                        $modal = $('#modal-verify');
                        $modal.find('.modal-body').html('<p>'+data+'</p>');
                        $modal.modal('show');

                        $modal.on('click', '.ok-button', function(){
                            $modal.modal('hide');
                            $('form').submit();
                        });
                    }

                    // Show the confirmation screen right away
                    else {
                        $('form').submit();
                    }
                },
                error: function (data) {
                    console.log(data.status + ' ' + data.statusText);
                    $('html').prepend(data.responseText);
                }
            });
        });

        @if (! $edit)

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
                    //console.log(data);

                    if (data['user'])
                    {
                        var user = data['user'];
                        $user_add_form = $('.templates .assignment-user').clone();
                        $user_add_form.find('input[name="user[]"]').val(user['id']);
                        $user_add_form.find('#user-name').text(user['name']);
                        $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');

                        $('.user-forms').append($user_add_form);
                    }
                },
                error: function (data) {
                    console.log(data.status + ' ' + data.statusText);
                    $('html').prepend(data.responseText);
                }
            });
        }

        // Remove cached values from target add form
        function clear_target_add_form()
        {
            $('.target-add-form').val('');
            $('.target-add-form .select2-search-choice').remove();
            $('.target-add-form .select2-input').attr('placeholder', 'Search for a user by Name, Email, or UserId').width(380);
        }
        clear_target_add_form();

        // Ajax call to set the target
        function add_target(userid, targetid, $row)
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

                    if (data['user'])
                    {
                        var user = data['user'];
                        $row.find('input[name="target[]"]').val(user['id']);
                        $row.find('.target-label').text(user['name']+" ("+user['email']+")");
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

            var data = {
                'client': {{ $client->id }},
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

        // Add All Users From Groups
        $('#add-users-from-groups').on('click', function()
        {
            var url = '/dashboard/clients/{{ $client->id }}/add-from-groups';

            $.ajax({
                type: 'post',
                url: url,
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
                            $user_add_form.find('input[name="target[]"]').val(user.target['id']);
                            $user_add_form.find('.target-label').text(user.target['name']+" ("+user.target['email']+")");
                            $user_add_form.find('input[name="role[]"]').val(user['position']);
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

        // Add Users From A Specific Job Family
        $('.add-by-job-family').on('click', function()
        {
            var url = '/dashboard/clients/{{ $client->id }}/add-from-job-family';
            var family = $(this).text();

            $.ajax({
                type: 'post',
                url: url,
                data: {
                    family: family
                },
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

        // Add Users From A Specific Job Family
        $('.add-by-job').on('click', function()
        {
            var url = '/dashboard/clients/{{ $client->id }}/add-from-job';
            var job = $(this).attr('data-job-id');

            $.ajax({
                type: 'post',
                url: url,
                data: {
                    job: job
                },
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

        // Set the Target
        $('.user-forms').on('click', '#add-target-modal', function(){
            $row = $(this).closest('.assignment-user');
            $modal = $('#modal-target');

            $modal.modal('show').on('change', '.target-add-form', function(){
                var targetid = $(this).val();
                var userid = $row.find('input[name="user[]"]').val();
                if (targetid)
                    add_target(userid, targetid[0], $row);
                clear_target_add_form();
                $modal.modal('hide');
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
                data.append('clientId', {{ $client->id }});
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

                        if (data['errors']) {
                            for (var i = 0; i < data['errors'].length; i++)
                                toastr.error(data['errors'][i], "Error", opts);
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

                        if (data['nonusers'] && data['nonusers'][0]['name'])
                        {
                            var list = '';
                            for (var i = 0; i < data['nonusers'].length; i++)
                                if (data['nonusers'][i]['name'])
                                    list += '<li>' + data['nonusers'][i]['name'] + ', ' + data['nonusers'][i]['email'] + '</li>';

                            $modal = $('#modal-nonusers');
                            $modal.find('.modal-body .nonusers-list').html(list);
                            $modal.modal('show');

                            $modal.on('click', '.ok-button', function(){
                                var url = '/dashboard/users/create-from-list/{{ $client->id }}';

                                $.ajax({
                                    type: 'post',
                                    url: url,
                                    data: {
                                        users: data['nonusers']
                                    },
                                    dataType: 'json',
                                    success: function (data) {
                                        console.log(data);

                                        if (data['errors']) {
                                            for (var i = 0; i < data['errors'].length; i++)
                                                toastr.error(data['errors'][i], "Error", opts);
                                        }

                                        if (data['users']) {
                                            for (var i = 0; i < data['users'].length; i += 1) {
                                                var user = data['users'][i];

                                                $user_add_form = $('.templates .assignment-user').clone();
                                                $user_add_form.find('input[name="user[]"]').val(user['id']);
                                                $user_add_form.find('#user-name').text(user['name']);
                                                $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');
                                                $('.user-forms').append($user_add_form);
                                            }
                                            $('#upload').trigger('click');
                                        }

                                        //$modal.modal('hide');
                                    },
                                    error: function (data) {
                                        console.log(data.status + ' ' + data.statusText);
                                        $('html').prepend(data.responseText);
                                    }
                                });
                                $modal.modal('hide');
                            });
                        }
                        toastr.success('Targets have been added successfully!', "Success", opts);
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
        @endif

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