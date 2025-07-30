<div class="panel panel-headerless" style="margin-bottom: 10px;">
    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">

        <!-- User Add Field -->
        <div class="user-add-form">
            <div class="row">
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('user[]', 'User', ['class' => 'control-label']) !!}--}}
                    {{--{!! Form::text('user[]', $user, ['class' => 'form-control input-lg', 'readonly']) !!}--}}
                {{--</div>--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('username[]', 'Username', ['class' => 'control-label']) !!}--}}
                    {{--{!! Form::text('username[]', $username, ['class' => 'form-control input-lg', 'readonly']) !!}--}}
                {{--</div>--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('email[]', 'Email', ['class' => 'control-label']) !!}--}}
                    {{--{!! Form::text('email[]', $email, ['class' => 'form-control input-lg', 'readonly']) !!}--}}
                {{--</div>--}}
                {{--<div class="col-sm-3">--}}
                    {{--<label class="control-label" for="group_role[]">Group Role</label>--}}
                    {{--{!! Form::select('group_role[]', $groupRolesArray, $groupRole, ['class' => 'form-control input-lg']) !!}--}}
                {{--</div>--}}

                <!-- User -->
                <div class="col-sm-3">
                    {!! Form::hidden('user_id[]', $userId, ['class' => 'user-id']) !!}
                    <div class="user-tab">
                        <h3 class="user-name">{{ $user }}</h3>
                        <table>
                            <tr>
                                <td><i>User ID: </i></td>
                                <td><span class="user-username">{{ $username }}</span></td>
                            </tr>
                            <tr>
                                <td><i>Email: </i></td>
                                <td><span class="user-email">{{ $email }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Position In Group -->
                <div class="col-sm-3">
                    {!! Form::label('group_position[]', 'Position In Group', ['class' => 'control-label']) !!}
                    {!! Form::text('group_position[]', $position, ['class' => 'form-control input-lg']) !!}
                </div>

                <!-- Is this a leadership position? -->
                <div class="col-sm-3">
                    {!! Form::label('leader[]', 'Leadership Position?', ['class' => 'control-label']) !!}
                    {!! Form::select('leader[]', [
                        0 => 'No',
                        1 => 'Yes'
                    ], $leader, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>
        </div>

    </div>
</div>