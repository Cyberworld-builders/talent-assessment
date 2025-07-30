@extends('app')

@section('title')
    Terms & Conditions
@stop

@section('styles')
    <style type="text/css">
        .terms .main-content {
            display: block;
            background-color: transparent;
            max-width: 900px;
            margin: 0 auto;
            font-family: "Avenir Next LT Pro";
        }
        .terms .main-content .description {
            text-align: center;
        }
        .terms .main-content .description h1 {
            font-family: "Bebas Neue";
        }
        .terms .main-content .input-field {
            margin-bottom: 20px;
        }
        .terms .main-content p,
        .terms .main-content li {
            line-height: 26px;
            font-size: 14px;
        }
        .terms .main-content h4,
        .terms .main-content h3 {
            margin: 30px 0;
        }
        .terms .main-content h3 {
            line-height: 32px;
        }
        footer {
            clear: both;
            text-align: center;
        }
        .select2-container.select2-allowclear .select2-choice abbr {
            margin-top: 3px;
        }
        .panel-page {
            padding: 14px 60px 60px;
        }
    </style>
@stop

@section('body')

    {{-- @include('dashboard.partials._settings') --}}

    @include('assignment.partials._nav')

    <div class="page-container terms"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->

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
                <h1>Terms & Conditions</h1>
                {{--<p>These questions are voluntary and will only be used for research purposes.</p>--}}
            </div>

            <!-- Errors -->
            @include('errors.list')

            {!! Form::open(['url' => 'terms']) !!}
            <div class="panel panel-default panel-border panel-page">
                <div>
                    <h3>Non-Disclosure Agreement and General Terms of Use For AOEScience Assessments</h3>
                    <p>This Non-Disclosure Agreement and General Terms of Use entered into as of the date the assessment link is sent by and between AOE Science and you (the assessment taker). AOE Assessments are confidential information and are protected by intellectual property laws. They are made available solely for the purpose of assessing a candidate’s standing on the constructs. </p>
                    <h4>Exam Security and Integrity</h4>
                    <p>The candidate may be prohibited from taking any AOE Assessments and/or may be disqualified from consideration of the job applied for if AOE Science believes the candidate violated our Assessment Agreement and/or engaged in any misconduct. This policy is enforced to ensure the integrity of the Assessments. Examples of misconduct and/or misuse of the Exam include, but are not limited to, the following:</p>
                    <ul>
                        <li>Modifying and/or altering the original results/score report for any assessments or feedback reports.</li>
                        <li>Fraudulently impersonating another to gain access to the assessments.</li>
                        <li>Submission of any work that is not completely your own.</li>
                        <li>Providing or accepting assistance to answer the assessment items.</li>
                        <li>Using unauthorized materials in an attempt to satisfy assessment requirements (this includes using brain-dump material and/or unauthorized publication of Exam questions with or without answers).</li>
                        <li>Disseminating actual Exam content or answers.</li>
                        <li>Possession of non-authorized items at the testing center during an Exam.</li>
                        <li>Misconduct as determined by statistical analysis.</li>
                        <li>Copying, publishing, disclosing, transmitting, selling, offering to sell, posting, downloading, distributing in any way, or otherwise transferring, modifying, making derivative works of, reverse engineering, decompiling, disassembling or translating any Exam in whole or in part, in any form or by any means, verbal or written, electronic or mechanical, for any purpose.</li>
                        <li>Using the Exam content in any manner that violates applicable law.</li>
                    </ul>
                    <h4 style="margin-bottom: 10px;">Agreement Acceptance</h4>
                    <div class="form-group">
                        <div class="input-field">
                            {!! Form::label('signature', 'Please type your name to specify that you agree to these Terms and Conditions *', ['class' => 'control-label']) !!}
                            {!! Form::text('signature', null, ['class' => 'form-control input-lg', 'id' => 'signature']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <br/>
                <div class="pull-right">
                    {!! Form::submit('Accept Terms', ['class' => 'btn btn-primary btn-lg', 'id' => 'submit', 'disabled']) !!}
                </div>
            </div>
            {!! Form::close() !!}

            <script type="text/javascript">
                jQuery(document).ready(function($) {

                    $('#signature').on('change', function(){
                        var val = $(this).val().trim();

                        if (! val)
                            document.getElementById('submit').disabled = true;

                        else
                            document.getElementById('submit').disabled = false;
                    });

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