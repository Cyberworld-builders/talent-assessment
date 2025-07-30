@extends('app')

@section('title')
    {{ $assessment->name }}
@stop

@section('styles')
    <style>
        .page-container .main-content {
            padding: 0;
        }
    </style>
@stop

@section('body')

    <nav class="navbar horizontal-menu navbar-fixed-top navbar-minimal">
        <div class="navbar-inner">

            {{-- Header --}}
            <div class="navbar-brand">
                <div class="logo">
                    <a href="#" class="logo-expanded"><br/>
                        <p><strong>Viewing Assignment: </strong><i>{{ $assessment->name }}</i></p>
                    </a>
                </div>
            </div>

            {{-- Navbar --}}
            <ul class="nav nav-userinfo navbar-right">
                <li>
                    <a href="{{ url('/dashboard/users/'.$user->id) }}">
                        <i class="fa-chevron-left"></i>
                        <span class="title">Go Back</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/dashboard/assignment/'.$assignment->id.'/download') }}">
                        <i class="fa-download"></i>
                        <span class="title">Download Data</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Main Page --}}
    <div class="page-container assignment details">
        <div class="main-content">
            <div class="report-template">

                <script src="/wp/wp-content/themes/aoe/js/highcharts.js"></script>
                <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
                {{--<link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">--}}
                {{--<link rel="stylesheet" type="text/css" media="all" href="/wp/wp-content/themes/aoe/reports/reports.css">--}}
                <style>
                    .page-container.assignment.details {
                        /*background-image:none;*/
                        /*background-color:#ddd;*/
                        background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
                        background-size: 100% 100%;
                    }
                    .assignment.details .main-content {
                        background: transparent;
                        padding-top: 55px;
                    }
                    .report-template .page-container {
                        background-color: white;
                        height: 1100px;
                        width: 850px;
                        padding: 45px 45px;
                        page-break-after: always;
                        margin: 0 auto;
                        margin-bottom: 15px;
                    }
                    .report-template .img-container-1, .report-template .img-container-2 {position: absolute; height:997px; width:760px;}
                    .report-template .img-container-1 small, .report-template .img-container-2 small {top:0; right:15px; position:absolute;}
                    .report-template .img-container-1 img {position:absolute; bottom:0px; right:15px; width:100px;}
                    .report-template .img-container-2 img {position:absolute; bottom:0px; left:15px; width:100px;}
                    .report-template h1 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:64px; color:#02244a; line-height:64px; margin-top:0px;}
                    .report-template h2 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:32px; color:#02244a; line-height:45px; margin-top:0px;}
                    .report-template h3 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:23px; color:#02244a; line-height:32px; margin-top:0px;}
                    .report-template .cover-for h3 {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:23px;}
                    .report-template h4 {-webkit-print-color-adjust: exact; font-family:'Didot Italic'; color:#02244a; font-size:32px; line-height:32px;}
                    .report-template h5 {font-family:'Avenir Next LT Pro Medium'; font-size:1.616em; line-height:1.616em; color:#02244a;}
                    .report-template .yellow-block {
                        display: block;
                        float: left;
                        height: 150px;
                        width: 150px;
                        background: #E7B428;
                        line-height: 23px;
                        border-radius: 100px;
                        margin-top: -42px;
                        margin-right: 18px;
                    }
                    .report-template h6 {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:23px; margin-top:0px;}
                    .report-template p, .report-template ul li {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:26px; margin-bottom:16px; list-style-type:square;}
                    .report-template .red {color:#E32731;} .report-template .yellow {color:#E7B428;} .report-template .green {color:#30BD21;}
                    .report-template .text-justify {text-align:justify;}
                    .report-template .border-bottom {border-top:1px solid rgba(0,0,0,0.1); padding-top:10px;}
                    .report-template .underline {border-bottom:1px solid black;}
                    .report-template small {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:18px;}
                    .report-template .leftside {left:15px!important; right:inherit; position:absolute;}
                    .report-template .disclaimer {padding-top:30px;}
                    .report-template .cover-logo {padding:90px 0px 0px; margin:0 auto;}
                    .report-template .cover-for {padding-top:90px; padding-bottom:120px;}
                    .report-template .white {color:white;}
                    .report-template #chart {width:600px; margin:0 auto;}
                    .report-template .chart {margin-left:-45px;}
                    .report-template .container {
                        max-width: 100%;
                        padding: 40px;
                    }

                    @media screen and (max-width:867px){
                        /*body {background-image:none; background-color:white;}*/
                        .page-container {padding:32px; height:auto; min-height:800px; width:100%;}
                        .img-container-1 img, .img-container-2 img, .report-logo {display:none;}
                        .img-container-1, .img-container-2 {
                            position: absolute;
                            height:90%;
                            width:90%;
                        }
                        #chart {width:100%; height:100%; margin-left:15px;}
                        .highcharts-yaxis-title, .highcharts-xaxis-title {display:none;}
                        .cover-for, .cover-logo {padding:0px;}
                        h1 {line-height:1em; font-size:3.231em;}
                        h3 {font-size: 1.616em;}
                        h1, h2, h3, h4 {-webkit-print-color-adjust: exact;}
                    }
                </style>
                <script>
                    $(function () {
                        $('#chart3').highcharts({
                            chart: {
                                type: 'bar',
                                style: {
                                    fontFamily: 'Avenir Next LT Pro',
                                    fontSize:'16px',
                                    align: 'center'
                                },
                                showInLegend: false,
                                title: {
                                    text: null
                                },
                                height: 250,
                                spacingLeft: 15
                            },
                            title: {
                                text: null
                            },
                            xAxis: {
                                categories: ['Honesty-Humility', 'Emotional Control', 'Extraversion', 'Agreeableness', 'Conscientiousness', 'Openness'],
                                labels: {
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            yAxis: {
                                min: 1,
                                max: 5,
                                text: null,
                                tickInterval: 0.5,
                                labels: {
                                    overflow: 'justify',
                                    style: {
                                        fontSize:'16px'
                                    }
                                },
                                title: {
                                    text: null
                                }
                            },
                            tooltip: {
                                enabled: false
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true,
                                        x: -50,
                                        style: {
                                            fontFamily: 'Avenir Next LT Pro',
                                            textShadow: false,
                                            color:'#ffffff',
                                            fontSize:'16px',
                                            align: 'center'
                                        }
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                showInLegend: false,
                                enableMouseTracking: false,
                                name: 'HEXACO',
                                color: '#e77928',
                                data: [
                                    3.15,
                                    3.15,
                                    3.15,
                                    3.15,
                                    3.15,
                                    3.15,
                                ]
                            }]
                        });
                    });
                </script>

                {{-- Page 1 --}}
                <div class="page-container" id="1">
                    <div class="container">

                        {{-- Logo --}}
                        <div class="row">
                            <div class="col-xs-2 visible-xs"></div>
                            <div class="col-xs-8 col-sm-8 col-sm-offset-2 text-center">
                                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                                <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                            </div>
                        </div>

                        {{-- User --}}
                        <div class="row">
                            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                                <br><br><br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                                <h3>Report for user:</h3>
                                <h4>{{ $user->name }}</h4>
                                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                            </div>
                        </div>

                        {{-- Overview --}}
                        <div class="row">
                            <div class="col-sm-10">
                                <h5>Overview</h5>
                                <p>
                                    <strong>Assessment:</strong> {{ $assessment->name }}
                                    @if ($assignment->job_id)
                                        <i> (for {{ $assignment->job->name }})</i>
                                    @endif
                                </p>
                                <?php
                                    if (! $assignment->target && $assignment->custom_fields && $assignment->custom_fields['type'][0] == "name")
                                    	$assignment->target = \App\User::where('name', $assignment->custom_fields['value'][0])->first();
                                ?>
                                @if ($assignment->target)
                                    <p><strong>Target User:</strong> {{ $assignment->target->name }}</p>
                                @endif
                                <p><strong>Date Completed:</strong> {{ $assignment->completed_at->toDayDateTimeString() }} </p>
                                <p>
                                    This report reviews the assessment {{ $assessment->name }}.
                                    @if ($assessment->name == 'AOE-Ability')
                                        On this assessment, {{ $user->name }} answered {{ $assignment->score }} questions correctly out of a total of {{ count($questions) }}.
                                    @endif
                                    You can view the specific details for this assessment below.
                                </p>
                            </div>
                        </div>
                        {{--<div class="row"><div class="col-sm-12"><hr></div></div>--}}


                        {{--<div class="row disclaimer">--}}
                            {{--<div class="col-xs-10 col-sm-10">--}}
                                {{--<h6 class="small">--}}
                                    {{--AOE Science offers the most scientifically valid candidate assessments. AOE uses the latest Talent Evidence from the scientific literature, their own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions.--}}
                                {{--</h6>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-2 col-sm-2 text-right report-logo">--}}
                                {{--<img class="img-responsive" src="/wp/wp-content/themes/aoe/images/report-logo-1.png">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>

                {{-- Page 2 --}}
                <div class="page-container" id="2">
                    <div class="container">

                        {{-- Heading --}}
                        <div class="row text-center">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-xs-4 visible-xs"></div>
                                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                                        @if ($assessment->logo)
                                            <img class="img-responsive" src="{{ show_image($assessment->logo) }}">
                                        @else
                                            <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                                        @endif
                                    </div>
                                </div>
                                <h1>{{ $assessment->name }}</h1>
                            </div>
                            <div class="col-sm-12 text-justify">
                                {{--<h5>Description</h5>--}}
                                {!! $assessment->description !!}
                            </div>
                        </div>
                        <br/>
                        <br/>

                        <div class="row">
                            <div class="col-xs-1">
                                <h3>#</h3>
                            </div>
                            <div class="col-xs-6">
                                <h3>Question</h3>
                            </div>
                            <div class="col-xs-4">
                                <h3>Answer</h3>
                            </div>
                            <div class="col-xs-1">
                                <h3>Score</h3>
                            </div>
                        </div>

                        @if (! $answers->isEmpty())
                            @foreach ($questions as $question)
                                @include('dashboard.assignments.partials._answerdetail', ['question' => $question])
                            @endforeach
                        @else
                            <p>No questions were answered for this assessment.</p>
                        @endif

                        {{--<div class="row">--}}
                            {{--<div class="col-xs-4">--}}
                                {{--<h3>Personality Dimension</h3>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-8">--}}
                                {{--<h3>Personality Dimension Description</h3>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row border-bottom">--}}
                            {{--<div class="col-xs-4">--}}
                                {{--<p>Honesty-Humility</p>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-8">--}}
                                {{--<p>Tendency to focus on fairness, sincerity, modesty, and greed avoidance.</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    </div>
                </div>

                <p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>

            </div>
        </div>
    </div>

@stop