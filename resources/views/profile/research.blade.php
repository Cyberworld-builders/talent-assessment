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

    @include('assignment.partials._nav')

    <div class="page-container profile"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

        <div class="heading">

            {{-- Background --}}
            {{--<img class="background" src="{{ $assessment->background }}" />--}}

            {{-- Logo --}}
            {{--<div class="logo">--}}
            {{--<img src="{{ $assessment->logo }}" />--}}
            {{--</div>--}}

            {{-- Title --}}
            {{--<div class="title">{{ $assessment->name }}</div>--}}

        </div>

        <div class="main-content">

            {{-- Description --}}
            <div class="description">
                <h1>{{ translate('Research Questions') }}</h1>
                <p>{{ translate('These questions are voluntary and will only be used for research purposes.') }}</p>
            </div>

            <!-- Errors -->
{{--            @include('errors.list')--}}

            {!! Form::open(['url' => 'profile/research']) !!}
                @include('profile.partials._researchform')
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