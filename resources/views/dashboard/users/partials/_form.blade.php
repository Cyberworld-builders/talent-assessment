<div class="panel panel-headerless">
    <div class="panel-body">

        @if ($edit)

            <div class="member-form-add-header">
                <div class="row">
                    <div class="col-md-10 col-sm-8">
                        <div class="user-img">
                            <img src="{{ url('assets/images/user-4.png') }}" class="img-circle" alt="user-pic">
                        </div>
                        <div class="user-name">
                            <a href="#">{{ $user->name }}</a>
                            @if (isset($reseller))
                                <span class="user-title">{{ $user->role_name }}</span>
                            @else
                                <span class="user-title">{{ $user->roles->first()->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        @endif


        <div class="member-form-inputs">

            {{-- Username --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('username', 'Username', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The username that identifies this user.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit)
                        {!! Form::text('username', null, ['class' => 'form-control input-lg', 'disabled']) !!}
                    @else
                        {!! Form::text('username', null, ['class' => 'form-control input-lg']) !!}
                    @endif
                </div>
            </div>

            {{-- Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of this user.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Email --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This user's email. This will be used to email the user their assessments.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('email', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Job Title --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('job_title', 'Job Title', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This user's current job title.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('job_title', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Job Family --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('job_family', 'Job Family', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This user's job family of his current job.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('job_family', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Password --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('password', 'Password', ['class' => 'control-label', 'id' => 'password-label']) !!}
                    <p class="small text-muted">The password of this user.</p>
                </div>
                @if (! $edit)
                    <div class="col-sm-9">
                        {!! Form::text('password', null, ['class' => 'form-control input-lg', 'id' => 'password']) !!}
                    </div>
                @else
                    <div class="col-sm-4">
                        {!! Form::text('', '••••••••', ['class' => 'form-control input-lg', 'id' => 'password', 'disabled']) !!}
                    </div>
                    <div class="col-sm-4">
                        <a id="generate-new-password" class="btn btn-black" style="margin: 0px; padding: 12px 20px;">Generate New Password</a>
                    </div>
                @endif
            </div>

            {{-- Role --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('role', 'Role', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The role of this user. This determines their permissions in this app.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit)
                        @if (isset($reseller))
                            {!! Form::select('role', $rolesArray, $user->role_id, ['class' => 'form-control input-lg', 'id' => 'role']) !!}
                        @else
                            {!! Form::select('role', $rolesArray, $user->roles->first()->id, ['class' => 'form-control input-lg', 'id' => 'role']) !!}
                        @endif
                    @else
                        {!! Form::select('role', $rolesArray, 1, ['class' => 'form-control input-lg', 'id' => 'role']) !!}
                    @endif
                </div>
            </div>

            {{-- Client --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('client_id', 'Client', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The client to which this user belongs to.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('client_id', $clientsArray, null, ['class' => 'form-control input-lg', 'id' => 'client']) !!}
                </div>
            </div>

        </div>

    </div>
</div>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name , ['class' => 'btn btn-primary btn-lg']) !!}
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

        // Store current client value
        var client = $('#client').val();

        // Sidebar menu default
        $('.sidebar-menu-under .menu-category[data-parent="Users"]').show();

        $('#generate-new-password').on('click', function(){
            if (! $('#password').hasClass('active')) {
                $('#password').removeAttr('disabled').attr('name', 'password').val('').addClass('active');
                $(this).removeClass('btn-black').addClass('btn-white').html('Cancel New Password');
            }
            else {
                $('#password').attr('name', '').attr('disabled', '').val('••••••••').removeClass('active');
                $(this).removeClass('btn-white').addClass('btn-black').html('Generate New Password');
            }
        });

        $('#role').on('change', function()
        {
            var val = $(this).val();

            // If User
            if (val == 4) {
                $('#password').val($('#password').attr('data-generated-pass')).attr('disabled', '').attr('name', '').removeClass('active');
                $('#password-label').after(' <span>(Auto-generated)</span>');
                $('#generate-new-password').removeClass('btn-white').addClass('btn-black').html('Generate New Password').hide();
            }

            // Otherwise
            else {
                if ($('#password').hasClass('active')) {
                    //$('#password').removeAttr('disabled').val('');
                    $('#password-label').next('span').remove();
                }
                else {
                    $('#password').val('••••••••');
                    $('#password-label').next('span').remove();
                }
                $('#generate-new-password').show();
            }

            // If Reseller or AOE Admin
            if (val == 2 || val == 1) {
                if ($('#client').val() != null)
                    client = $('#client').val();
                $('#client').val(null).attr('disabled', '');
            }

            // If Client Admin or User
            if (val == 3 || val == 4) {
                $('#client').val(client).removeAttr('disabled');
            }
        });

        if ($('#role').val() == 4) {
            $('#generate-new-password').hide();
            $('#password-label').after(' <span>(Auto-generated)</span>');
        }

        if ($('#role').val() == 1 || $('#role').val() == 2) {
            $('#client').val(null).attr('disabled', '');
        }

        $('input[name="name"]').on('change', function() {
            generatePassword();
        });

        $('input[name="username"]').on('change', function() {
            generatePassword();
        });

        function generatePassword()
        {
            var data = {
                'name': $('input[name="name"]').val(),
                'username': $('input[name="username"]').val()
            };
            var url = '/dashboard/users/generate_password';

            $.ajax({
                type: 'post',
                url: url,
                data: data,
                dataType: 'json',
                success: function (data) {
                    console.log('password generated');
                    console.log(data);
                    $('#password').attr('data-generated-pass', data);
                    if ($('#role').val() == 4) {
                        $('#password').val($('#password').attr('data-generated-pass'));
                    }
                },
                error: function (data) {
                    console.log(data.status + ' ' + data.statusText);
                    $('html').prepend(data.responseText);
                }
            });
        }

        generatePassword();
    });
</script>

