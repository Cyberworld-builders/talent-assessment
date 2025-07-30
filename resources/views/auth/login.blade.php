@extends('app')

@section('title')
    AOE : Login
@stop

@section('styles')
    <style type="text/css">
        .login-header {
            text-align: center;
        }
        .login-header p {
            font-family: "Avenir Next LT Pro";
            font-size: 15px !important;
            color: #aaa !important;
        }
        .login-header h4 {
            font-family: "Bebas Neue";
            font-size: 30px;
        }
        input, button {
            font-family: "Avenir Next LT Pro" !important;
            font-size: 15px !important;
        }
        .login-footer {
            text-align: center;
        }
    </style>
@stop

@section('body-class')
    login-page login-light
@stop

@section('body')

    <div class="login-container">

        <script type="text/javascript">
            jQuery(document).ready(function($)
            {
                // Reveal Login form
                setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);

                // Validation and Ajax action
                /*$("form#login").validate({
                 rules: {
                 username: {
                 required: true
                 },

                 passwd: {
                 required: true
                 }
                 },

                 messages: {
                 username: {
                 required: 'Please enter your username.'
                 },

                 passwd: {
                 required: 'Please enter your password.'
                 }
                 },

                 // Form Processing via AJAX
                 submitHandler: function(form)
                 {
                 show_loading_bar(70); // Fill progress bar to 70% (just a given value)

                 var opts = {
                 "closeButton": true,
                 "debug": false,
                 "positionClass": "toast-top-full-width",
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

                 $.ajax({
                 url: "data/login-check.php",
                 method: 'POST',
                 dataType: 'json',
                 data: {
                 do_login: true,
                 username: $(form).find('#username').val(),
                 passwd: $(form).find('#passwd').val(),
                 },
                 success: function(resp)
                 {
                 show_loading_bar({
                 delay: .5,
                 pct: 100,
                 finish: function(){

                 // Redirect after successful login page (when progress bar reaches 100%)
                 if(resp.accessGranted)
                 {
                 window.location.href = 'dashboard-1.html';
                 }
                 }
                 });

                 // Remove any alert
                 $(".errors-container .alert").slideUp('fast');

                 // Show errors
                 if(resp.accessGranted == false)
                 {
                 $(".errors-container").html('<div class="alert alert-danger">\
                 <button type="button" class="close" data-dismiss="alert">\
                 <span aria-hidden="true">&times;</span>\
                 <span class="sr-only">Close</span>\
                 </button>\
                 ' + resp.errors + '\
                 </div>');

                 $(".errors-container .alert").hide().slideDown();
                 $(form).find('#passwd').select();
                 }
                 }
                 });
                 }
                 });*/

                // Set Form focus
                $("form#login .form-group:has(.form-control):first .form-control").focus();
            });
        </script>

        <!-- Errors container -->
        <div class="errors-container"></div>

        <!-- Errors -->
        @include('errors.list')

            <!-- Add class "fade-in-effect" for login form effect -->
            <form method="post" role="form" id="login" class="login-form fade-in-effect" action="login">
            {!! csrf_field() !!}

            @if (! isset($reseller))

                <div class="login-header">
                    <img src="https://s3-us-west-2.amazonaws.com/aoe-uploads/images/aoe-logo-black.png" /><br/><br/>

                    <h4>Welcome To AOE Science</h4>
                    <p>Please enter your user information below to login</p>
                </div>

            @else

                <div class="login-header">
                    @if ($reseller->logo)
                        <img style="position: relative;max-width: 100%;" src="{{ show_image($reseller->logo) }}" />
                    @else
                        <img src="https://s3-us-west-2.amazonaws.com/aoe-uploads/images/aoe-logo-black.png" />
                    @endif
                    <br/><br/>

                    <h4>Welcome To {{ $reseller->name }}</h4>
                    <p>Please enter your user information below to login</p>
                </div>

            @endif

            <div class="form-group">
                <label class="control-label" for="username">User ID</label>
                <input type="text" class="form-control" name="username" id="username" autocomplete="off" />
            </div>

            <div class="form-group">
                <label class="control-label" for="passwd">Password</label>
                <input type="password" class="form-control" name="password" id="passwd" autocomplete="off" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-black  btn-block text-left">
                    Log In
                </button>
            </div>

            <div class="login-footer">
                @if (! isset($reseller))
                    <a href="{{ url('password') }}">Forgot your password?</a>
                    <br>
                    <a href="{{ url('resellers') }}">Login to a Reseller's dashboard?</a>
                @endif
            </div>

        </form>

    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
@stop