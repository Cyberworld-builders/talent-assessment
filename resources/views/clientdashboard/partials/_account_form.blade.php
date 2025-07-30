{{--<div class="panel panel-headerless">--}}
    <div class="panel-body">
        <div class="member-form-inputs">

            {{-- Username --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('username', 'Username', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Your unique username. This cannot be changed.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('username', null, ['class' => 'form-control input-lg', 'disabled']) !!}
                </div>
            </div>

            {{-- Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Your first and last name.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Email --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Your email address.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('email', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Password --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('password', 'Password', ['class' => 'control-label', 'id' => 'password-label']) !!}
                    <p class="small text-muted">Your password.</p>
                </div>
                <div class="col-sm-4">
                    {!! Form::text('', '••••••••', ['class' => 'form-control input-lg', 'id' => 'password', 'disabled']) !!}
                </div>
                <div class="col-sm-4">
                    <a id="generate-new-password" class="btn btn-black" style="margin: 0px; padding: 12px 20px;">Change Password</a>
                </div>
            </div>

        </div>
    </div>
{{--</div>--}}

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit('Update' , ['class' => 'btn btn-primary btn-lg']) !!}
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
    });
</script>

