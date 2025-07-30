@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
        .remove-row-button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            color: #bbbbbb;
        }
        .user-add-form .user-name {
            font-size: 18px;
            font-weight: bold;
            margin: 4px 0 7px 0;
        }
        .user-add-form .user-tab i {
            color: #bebebe;
            padding-right: 5px;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <!-- Name -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Group Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of the group.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <!-- Description -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    <p class="small text-muted">An optional description to describe what this group is about.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::textarea('description', null, ['class' => 'form-control input-lg', 'rows' => '4']) !!}
                </div>
            </div>

            <!-- Target -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('target_id', 'Target', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Specify an optional group target. This can be used to easily assign assessments where a user must rate someone other than themselves.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('target_id', $targetsArray, null, ['class' => 'form-control input-lg', 'id' => 'target']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#target").select2({
//                                placeholder: 'Search for a target by Name, Email, or UserId',
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

<div class="col-sm-12">

    <!-- Add Users To Group Button -->
    <div class="row">
        <div class="pull-left">
            <a class="btn btn-black toggle-modal" data-toggle="add-users"><i class="fa-plus"></i> Add Users To This Group</a>
        </div>
    </div><br/>

    <!-- Users -->
    <div class="row">
        <div class="user-forms">

            @if ($edit)
                @foreach ($group->users as $groupUser)
                    <?php $user = \App\User::find($groupUser['id']); ?>
                    @include('dashboard.groups.partials._userform', [
                        'userId' => $user->id,
                        'user' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'position' => $groupUser['position'],
                        'leader' => $groupUser['leader']
                    ])
                @endforeach
            @endif

        </div>
    </div>

</div>

<!-- User Form Template -->
<div class="templates" style="display:none;">
    @include('dashboard.groups.partials._userform', [
        'userId' => '',
        'user' => '',
        'username' => '',
        'email' => '',
        'position' => '',
        'leader' => ''
    ])
</div>

<!-- Add Users Modal -->
<div class="modal fade" id="add-users">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Users</h4>
            </div>

            <div class="modal-body">
                {!! Form::select('users[]', $usersArray, null, ['class' => 'form-control input-lg', 'id' => 'users', 'multiple']) !!}
                <script type="text/javascript">
                    jQuery(document).ready(function($)
                    {
                        $("#users").select2({
                            placeholder: 'Select User(s)',
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
                <button type="button" id="submit" class="btn btn-small-font btn-orange save-button">Add Users</button>
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

        // Toggle the specified modal box
        $('.toggle-modal').on('click', function(){
            var toggle = $(this).attr('data-toggle');
            $modal = $('#'+toggle);

            // Clear any cached input in the users input field
            $('#s2id_users .select2-search-choice', $modal).remove();
            $('select[name="users[]"]', $modal).val('');

            // Show the modal, Submit modal form
            $modal.modal('show').on('click', '#submit', function(){

                // Get all user ids from the input field
                var user_ids = $('#users').val();
                var data = {
                    'ids': user_ids,
                };
                var url = '/dashboard/users/get_users_from_ids';

                // Get users from the server
                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data)
                    {
                        // Show server errors
                        $('html').prepend(data.responseText);

                        // If we have a response
                        if (data['users']){
                            var users = data['users'];
                            for (var i in users)
                            {
                                // Skip loop if the property is from prototype
                                if (! users.hasOwnProperty(i)) continue;
                                var user = users[i];

                                // Find the fields in the user template form
                                var id_field = '.user-id';
                                var name_field = '.user-name';
                                var username_field = '.user-username';
                                var email_field = '.user-email';

                                // Populate them with the server data
                                $user_add_form = $('.templates .panel').clone();
                                $user_add_form.find(id_field).val(user.id);
                                $user_add_form.find(name_field).text(user.name);
                                $user_add_form.find(username_field).text(user.username);
                                $user_add_form.find(email_field).text(user.email);

                                // Append the new user form to the DOM
                                $('.user-forms').append($user_add_form);
                            }
                            $modal.modal('hide');
                        }
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });
        });

        // Remove a row
        $('.user-forms').on('click', '.remove-row-button', function(){
            $(this).closest('.panel').remove();
        });
    });
</script>

<!-- Submit Button -->
<div class="form-group">

    <div style="clear:both;"></div>
    <br/>

    <div class="pull-right">
        {!! Form::submit($button_name , ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>

@section('scripts')
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop