<!-- Assign to Users -->
<div class="panel panel-headerless" style="margin-bottom:5px;">
    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">
        <div class="row">

            <!-- Status -->
            <div class="col-sm-3" style="">
                <div class="form-group" style="margin:0;">
                    {!! Form::label('status', 'Status:') !!}
                    <div class="alert status" style="margin: 0px; padding: 12px 10px;">Input a username to validate</div>
                </div>
            </div>

            <!-- Username -->
            <div class="col-sm-3">
                <div class="form-group" style="margin:0;">
                    {!! Form::label('username[]', 'Username:') !!}
                    {!! Form::text('username[]', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <!-- Name -->
            <div class="col-sm-3">
                <div class="form-group" id="name" style="margin:0;">
                    {!! Form::label('name[]', 'Name:') !!}
                    {!! Form::text('name[]', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <!-- Email -->
            <div class="col-sm-3">
                <div class="form-group" id="email" style="margin:0;">
                    {!! Form::label('email[]', 'Email:') !!}
                    {!! Form::text('email[]', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>


            <!-- Custom From Value -->
            <div class="from-user-field" style="display: none; clear:both; padding-top: 20px;">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('from_name[]', 'Leader User Name:') !!}
                        {!! Form::text('from_name[]', null, ['class' => 'form-control input-lg from-user']) !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('from_email[]', 'Leader User Email:') !!}
                        {!! Form::text('from_email[]', null, ['class' => 'form-control input-lg from-user']) !!}
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>