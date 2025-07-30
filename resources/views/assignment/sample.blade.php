@extends('app')

@section('title')
    Assignments
@stop

@section('styles')
    <style type="text/css">
        .profile .main-content {
            display: block;
            background-color: transparent;
            max-width: 1170px;
            margin: 0 auto;
            font-family: "Avenir Next LT Pro";
        }
        .profile .main-content .description {
            text-align: center;
        }
        .profile .main-content .description h1 {
            font-family: "Bebas Neue";
        }
        .profile .main-content .input-field {
            margin-bottom: 20px;
        }
        footer {
            clear: both;
            text-align: center;
        }
        .select2-container.select2-allowclear .select2-choice abbr {
            margin-top: 3px;
        }
    </style>
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    {{--@include('assignment.partials._nav')--}}

    <div class="page-container profile assignment">

{{--        @include('assignment.partials._header', ['preview' => false])--}}

        <div class="heading">

            {{-- Background --}}
            {{--<img class="background" src="{{ $assessment->background }}" />--}}

            {{-- Logo --}}
            {{--<div class="logo">--}}
            {{--<img src="{{ $assessment->logo }}" />--}}
            {{--</div>--}}

            {{-- Title --}}
            {{--<div class="title">{{ $assessment->name }}</div>--}}

            {{-- Background --}}
            <img class="background" src="{{ show_image($assessment->background) }}" />

            {{-- Logo --}}
            <div class="logo">
                <img style="height: 100%;" src="{{ $assessment->logo }}" />
            </div>

            {{-- Title --}}
            <div class="title">{{ $assessment->name }}</div>

        </div>

        <div class="main-content">

            {{-- Description --}}
            <div class="description">
                <h1>{{ translate('Instructions') }}</h1>
                @if ($assignment->translation() && $assignment->translation()->description)
                    {!! custom_fields($assignment->id, $assignment->translation()->description) !!}
                @else
                    {!! custom_fields($assignment->id, $assessment->description) !!}
                @endif
            </div>



            {{-- Description --}}
            <div class="description">
                <h1>{{ translate('Your Information') }}</h1>
                <p>{{ translate('Please provide your name and email address below.') }}</p>
            </div>

            <!-- Errors -->
            @include('errors.list')

            {!! Form::open(['url' => 'assessment/sample/'.$assessmentName.'/take/'.$assignment->code]) !!}

            <div class="panel panel-default panel-border" style="margin: 0 auto; max-width: 600px;">

                <div>
                    <div class="input-field">
                        {!! Form::label('name', translate('Name').' *', ['class' => 'control-label']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="input-field">
                        {!! Form::label('email', translate('Email').' *', ['class' => 'control-label']) !!}
                        {!! Form::text('email', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>

            </div>

            {{--<div class="form-group">--}}
                {{--<br/>--}}
                {{--<div style="align:center;">--}}
                    {{--{!! Form::submit(translate('Begin The Assessment'), ['class' => 'btn btn-primary btn-lg']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            <br/><br/>

            <div style="text-align: center;">
                {{--<a style="line-height: 56px;" href="{{ $assignment->url }}" class="btn btn-primary btn-lg">{{ translate('Begin The Assessment') }}</a>--}}
                <input type="submit" style="line-height: 56px;" class="btn btn-primary btn-lg" value="{{ translate('Begin The Assessment') }}"/>
            </div>

            {!! Form::close() !!}

            <script type="text/javascript">
                jQuery(document).ready(function($) {

                    // Reveal field by selection
                    /*$('.reveal-field-by-selection').on('change', function () {
                     $('.' + $(this).attr('data-field-to-reveal')).hide();
                     $('.' + $(this).attr('data-field-to-reveal') + '.' + $(this).val()).slideDown();
                     });

                     // Check for fields that should already be revealed
                     $('.reveal-field-by-selection').each(function () {
                     $('.' + $(this).attr('data-field-to-reveal')).hide();
                     $('.' + $(this).attr('data-field-to-reveal') + '.' + $(this).val()).show();
                     });*/
                });
            </script>

            <footer>
                <img src="{{ asset('assets/images/powered-by-aoe.png') }}" />
            </footer>

        </div>

    </div>

@stop

@section('scripts')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop