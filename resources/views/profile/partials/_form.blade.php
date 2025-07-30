<div class="panel panel-default panel-border">


        {{--<div class="member-form-add-header">--}}
            {{--<div class="row">--}}

                {{--<div class="col-md-2 col-sm-4 pull-right-sm">--}}
                {{--<a href="" class="btn btn-white"><i class="fa-gear"></i> Change Image</a>--}}
                {{--</div>--}}

                {{--<div class="col-md-10 col-sm-8">--}}
                    {{--<div class="user-img">--}}
                        {{--<img src="{{ url('assets/images/user-4.png') }}" class="img-circle" alt="user-pic">--}}
                    {{--</div>--}}
                    {{--<div class="user-name">--}}
                        {{--<a href="#">{{ $user->name }}</a>--}}
                        {{--<span>{{ $user->roles->first()->name }}</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}


        <div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-field">
                        {!! Form::label('first_name', translate('First Name').' *', ['class' => 'control-label']) !!}
                        {!! Form::text('first_name', $first_name, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="input-field">
                        {!! Form::label('middle_name', translate('Middle Name'), ['class' => 'control-label']) !!}
                        {!! Form::text('middle_name', $middle_name, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="input-field">
                        {!! Form::label('last_name', translate('Last Name').' *', ['class' => 'control-label']) !!}
                        {!! Form::text('last_name', $last_name, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    {{--<div class="input-field">--}}
                        {{--{!! Form::label('organization_id', 'Organization ID *', ['class' => 'control-label']) !!}--}}
                        {{--{!! Form::text('organization_id', null, ['class' => 'form-control input-lg']) !!}--}}
                    {{--</div>--}}
                    <div class="input-field">
                        {!! Form::label('email', translate('Email').' *', ['class' => 'control-label']) !!}
                        {!! Form::text('email', $user->email, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="input-field">
                        {!! Form::label('password', translate('New Password').' *', ['class' => 'control-label']) !!}
                        {!! Form::input('password', 'password', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="input-field">
                        {!! Form::label('password_confirmation', translate('Confirm New Password').' *', ['class' => 'control-label']) !!}
                        {!! Form::input('password', 'password_confirmation', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>
        </div>

</div>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit(translate('Next'), ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>