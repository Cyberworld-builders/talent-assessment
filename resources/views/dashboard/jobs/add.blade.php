@extends('dashboard.dashboard')

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

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Add Applicants To {{ $job->name }}</h1>
            <p class="description">Add these existing users as applicants for the job {{ $job->name }}.</p>
        </div>
    </div>

    <div class="row">

        <!-- Errors -->
        @include('errors.list')

        <!-- Sub Navigation -->
        @include('dashboard.clients.partials._subnav')

        <!-- Form -->
        {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs/'.$job->id.'/applicants']) !!}
        <div class="panel panel-headerless">
            <div class="panel-body">
                <div class="member-form-inputs">

                    <!-- Add Users -->
                    <div class="row">
                        <div class="col-sm-3">
                            {!! Form::label('', 'Add User', ['class' => 'control-label']) !!}
                            <p class="small text-muted">Select a user to add them as an applicant.</p>
                        </div>
                        <div class="col-sm-9">
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
        </div>

        <!-- User Form -->
        <div class="user-forms"></div>

        <!-- Submit Button -->
        <div class="form-group">

            <div style="clear:both;"></div>
            <br/>

            <div class="pull-right">
                {!! Form::submit('Add Users As Applicants' , ['class' => 'btn btn-primary btn-lg']) !!}
            </div>
        </div>

        {!! Form::close() !!}

        <!-- User Form Template -->
        <div class="templates" style="display:none;">

            <div class="panel panel-headerless" style="margin-bottom: 10px;">
                <a class="remove-row-button" href="#null"><i class="fa-times"></i></a>
                <div class="panel-body">

                    <!-- User Add Field -->
                    <div class="user-add-form">
                        <div class="row">

                            <!-- User -->
                            <div class="col-sm-3">
                                {!! Form::hidden('user_id[]', null, ['class' => 'user-id']) !!}
                                <div class="user-tab">
                                    <h3 class="user-name"></h3>
                                    <table>
                                        <tr>
                                            <td><i>User ID: </i></td>
                                            <td><span class="user-username"></span></td>
                                        </tr>
                                        <tr>
                                            <td><i>Email: </i></td>
                                            <td><span class="user-email"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>
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
                    var data = {
                        'id': userid,
                    };
                    var url = '/dashboard/get-user';

                    $.ajax({
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
//
                            if (data['id'])
                            {
                                var id = data['id'];
                                var name = data['name'];
                                var username = data['username'];
                                var email = data['email'];

                                $user_add_form = $('.templates .panel').clone();
                                $user_add_form.find('input[name="user_id[]"]').val(id);
                                $user_add_form.find('.user-name').text(name);
                                $user_add_form.find('.user-username').text(username);
                                $user_add_form.find('.user-email').text(email);

                                $('.user-forms').append($user_add_form);
                            }
                        },
                        error: function (data) {
                            console.log(data.status + ' ' + data.statusText);
                            $('html').prepend(data.responseText);
                        }
                    });
                }

                // Remove a row
                $('.user-forms').on('click', '.remove-row-button', function(){
                    $(this).closest('.panel').remove();
                });
            });
        </script>

    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop