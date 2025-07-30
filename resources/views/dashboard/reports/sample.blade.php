@extends('app')

@section('title')
    AOE : Completed
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
            font-size: 40px;
            color: #02244a !important;
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

        <div class="login-header">
            <img src="https://s3-us-west-2.amazonaws.com/aoe-uploads/images/character_informative.png" /><br/><br/>
            <h4>Assessment Complete</h4>
            <p>{{ $message }}</p>
        </div>

        {!! Form::open(['url' => 'assessment/sample/'.$assessmentName.'/report']) !!}

        <div style="text-align: center;">
            <br/><br/>
            <input type="submit" style="line-height: 56px;" class="btn btn-primary btn-lg" value="{{ translate('View Sample Report') }}"/>
        </div>

        {!! Form::close() !!}

    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>
@stop