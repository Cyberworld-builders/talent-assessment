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

                // Set Form focus
                $("form#login .form-group:has(.form-control):first .form-control").focus();
            });
        </script>

        <!-- Add class "fade-in-effect" for login form effect -->
        <form method="post" role="form" id="login" class="login-form fade-in-effect" action="login">
            {{--{!! csrf_field() !!}--}}

            <div class="login-header">
                <img src="http://affectstudios.net/aoe/images/aoe-logo-black.png" /><br/><br/>

                <h4>Choose Reseller</h4>
                <p>Please select the Reseller you wish to login for below</p>
            </div>

            <div class="form-group">
                {!! Form::select('reseller', $resellersArray, null, ['class' => 'form-control input-lg', 'id' => 'reseller', 'style' => 'height:46px;']) !!}
            </div>

        </form>
    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
    <script>
        jQuery(document).ready(function ($)
        {
            $('#reseller').on('change', function(){
                var id = $(this).val();
                window.location.href = '/resellers/' + id + '/login';
            });
        });
    </script>
@stop