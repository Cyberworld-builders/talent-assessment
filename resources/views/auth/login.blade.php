@extends('app')

@section('title')
    Involved Talent : Login
@stop

@section('styles')
    <style type="text/css">
        .login-page,
        .login-page.login-light {
            background: white;
            padding-top: 0 !important;
            background: url('{{ asset('images/background.jpg') }}') no-repeat scroll 50% 50% !important;
        }
        .login-page .login-container,
        .login-page.login-light .login-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translateX(-50%)translateY(-65%);
        }
        .login-page .login-form,
        .login-page.login-light .login-form {
            max-width: 370px;
            margin: 0 auto;
            padding: 0;
            background: transparent;
        }
        .login-page .login-form .login-header {
            text-align: center;
        }
        .login-page .login-form .login-header img,
        .login-page.login-light .login-form .login-header img {
            max-width: 340px;
        }
        .login-page .login-form .login-header p {
            color: #3a3a3a;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 18px !important;
            color: #aaa !important;
        }
        .login-page .login-form .login-header h4 {
            color: #3a3a3a;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 24px;
        }
        .login-page .login-form input,
        .login-page .login-form button {
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 15px !important;
        }
        .login-page .login-form .login-footer {
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

            <div class="login-header">
                <img src="{{ asset('images') }}/logo.png" /><br/><br/><br/>
                
                <h4>Welcome To Involved Talent</h4>
                <p>Please enter your user information below to login.</p>
                <br/>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" name="password" id="passwd" placeholder="Password" autocomplete="off" />
            </div>

            <input type="hidden" name="timezone" id="timezone">

            <div class="form-group">
                <button type="submit" class="btn btn-black  btn-block text-left">
                    Log In
                </button>
            </div>

            <div class="login-footer">
                <a href="{{ url('password') }}">Forgot your password?</a>
            </div>
        </form>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment-timezone-with-data.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
    <script>
        (function($){
            $(document).ready(function(){
                var timezone = moment.tz.guess();
                $('#timezone').val(timezone);
            });
        })(jQuery);
    </script>
@stop