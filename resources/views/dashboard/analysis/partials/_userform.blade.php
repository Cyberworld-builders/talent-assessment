<div class="panel panel-headerless" style="margin-bottom: 10px;">
    <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
    <div class="panel-body">

        <!-- User Add Field -->
        <div class="user-add-form">
            <div class="row">

                <!-- User -->
                <div class="col-sm-3">
                    {!! Form::hidden('user_id[]', $userId, ['class' => 'user-id']) !!}
                    <div class="user-tab">
                        <h3 class="user-name">{{ $user }}</h3>
                        {{--<table>--}}
                            {{--<tr>--}}
                                {{--<td><i>User ID: </i></td>--}}
                                {{--<td><span class="user-username">{{ $username }}</span></td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td><i>Email: </i></td>--}}
                                {{--<td><span class="user-email">{{ $email }}</span></td>--}}
                            {{--</tr>--}}
                        {{--</table>--}}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="user-tab">
                        <table>
                            <tr><td><i>User ID: </i></td></tr>
                            <tr><td><span class="user-username">{{ $username }}</span></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="user-tab">
                        <table>
                            <tr><td><i>Email: </i></td></tr>
                            <tr><td><span class="user-email">{{ $email }}</span></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="user-tab">
                        <table>
                            <tr><td><i>Position: </i></td></tr>
                            <tr><td><span class="user-position"></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>