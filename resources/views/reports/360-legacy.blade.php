<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <title>Involved Talent : Report for {{ $user->name }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/fonts/linecons/css/linecons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/report-styles.css') }}">
    
    <script src="{{ asset('assets/js/jquery-1.11.1.min.js') }}"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="page-body report">

{{-- Cover Page --}}
<div class="page-container" id="1">
    <img class="cover-shapes" src="{{ asset('assets/images/report-cover-shapes.png') }}" />
    
    <div class="page-wrapper">
        <div class="page-header">
            <img class="logo right" src="{{ asset('assets/images/involve-360-logo-small.png') }}">
            <div class="line"></div>
            <div class="clearfix"></div>
        </div>
        
        <div class="cover-title">
            <span>Involved-360</span>
            <span class="report">Report</span>
            <span class="for"><strong>for</strong> {{ $user->name }}</span>
        </div>

        <div class="cover-disclaimer">
            <img src="{{ asset('assets/images/logo-tagline.png') }}" />
        </div>
    </div>
</div>

{{-- Introduction Page --}}
<div class="page-container" id="2">
    <div class="page-wrapper">
        <div class="page-header">
            <img class="logo" src="{{ asset('assets/images/logo-small.png') }}">
            <div class="line"></div>
            <div class="clearfix"></div>
        </div>
        
        <div class="page-title">
            <img src="{{ asset('assets/images/badge.png') }}" />
            Involved-360
        </div>
        
        <div class="page-content">
            <p>
                This is your Involved-360 report. This report should be used as a critical piece of your overall leadership development at {{ $user->client->name ?? 'your organization' }}.
            </p>
            <p>
                Stakeholders (e.g., supervisor, peers, subordinates, customers) familiar with your work completed the 360-evaluation to provide you an analytically robust picture of your strengths and improvement opportunities. Additionally, each of your raters was asked to provide qualitative feedback, which can greatly augment your quantitative scores. Taken together, this report provides you a wealth of information to not only significantly develop your own leadership, but also drive critical business outcomes.
            </p>
            <p>
                Each individual competency score is presented with corresponding rater feedback and suggestions. Your scores are compared to (1) norms for similar jobs/positions and (2) the average of your colleagues that have also recently completed the 360-feedback survey at {{ $user->client->name ?? 'your organization' }}. Anchoring your scores with industry norms and your company averages provides a much more accurate representation of where your scores stand and provides enhanced motivation to accelerate your leadership involvement.
            </p>
        </div>

        <div class="page-footer">
            Page 2
        </div>
    </div>
</div>

{{-- Competency Pages --}}
@if (isset($scores) && is_array($scores))
    <?php $competencyIndex = 0; ?>
    @foreach ($scores as $dimensionName => $dimensionData)
        <?php $pageNumber = 3 + ($competencyIndex * 2); $competencyIndex++; ?>
        
        {{-- Competency Score Page --}}
        <div class="page-container" id="{{ $pageNumber }}">
            <div class="page-wrapper">
                <div class="page-header">
                    <img class="logo right" src="{{ asset('assets/images/involve-360-logo-small.png') }}">
                    <div class="line"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="page-title alt">
                    <span class="subtitle">
                        <img src="{{ asset('assets/images/triangle.png') }}" />
                        Competency:
                    </span>
                    <span>{{ $dimensionName }}</span>
                </div>
                
                <div class="page-content">
                    <p>
                        {{ $dimensionData['Definition'] ?? 'Competency description not available.' }}
                    </p>

                    <div class="chart">
                        <div class="title">
                            Your Current Scores By Ratee Source<br/>
                            <span><img src="{{ asset('assets/images/triangle-orange.png') }}" /> Indicates Significant Growth Opportunity</span>
                        </div>

                        <div class="score">
                            {{ isset($dimensionData['Score']['Total']) ? number_format($dimensionData['Score']['Total'], 1) : '0.0' }}
                            <span>out of 5</span>
                        </div>

                        <div class="bars">
                            <div class="graph">
                                <div class="graph-lines">
                                    <div class="line"><span>0</span></div>
                                    <div class="line one"><span>1</span></div>
                                    <div class="line two"><span>2</span></div>
                                    <div class="line three"><span>3</span></div>
                                    <div class="line four"><span>4</span></div>
                                    <div class="line five"><span>5</span></div>
                                    <div class="clearfix"></div>
                                </div>

                                @if (isset($dimensionData['Score']))
                                    @foreach ($dimensionData['Score'] as $raterType => $score)
                                        @if ($raterType !== 'Total')
                                            <div class="graph-row">
                                                <div class="ratee">{{ $raterType }}</div>
                                                <div class="bar">
                                                    <div class="inner" style="width:{{ ($score / 5) * 100 }}%;">
                                                        {{ number_format($score, 1) }}
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="clearfix"></div>
                    </div>

                    <div class="norms">
                        <div class="norm-group industry">
                            <div class="norm">{{ isset($dimensionData['Industry']) ? number_format($dimensionData['Industry'], 2) : '0.00' }}</div>
                            <div class="norm-label">Industry Norm for<br/><span>{{ $user->client->industry ?? 'No Industry Set' }}</span></div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="norm-group group">
                            <div class="norm">{{ isset($dimensionData['Group Average']) ? number_format($dimensionData['Group Average'], 2) : '0.00' }}</div>
                            <div class="norm-label">Avg Score<br/><span>For This Group</span></div>
                        </div>

                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="page-footer">
                    Page {{ $pageNumber }}
                </div>
            </div>
        </div>

        {{-- Feedback Page --}}
        <div class="page-container" id="{{ $pageNumber + 1 }}">
            <div class="page-wrapper">
                <div class="page-header">
                    <img class="logo" src="{{ asset('assets/images/logo-small.png') }}">
                    <div class="line"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="page-title alt2">
                    Developmental<br/><span>Feedback</span>
                </div>

                <div class="page-subtitle">
                    <img src="{{ asset('assets/images/triangle-orange-large.png') }}" />
                    For: <span>{{ $dimensionName }}</span>
                </div>
                
                <div class="page-content">
                    <div class="feedbacks">
                        @if (isset($dimensionData['Feedback']) && is_array($dimensionData['Feedback']))
                            <?php $feedbackIndex = 1; ?>
                            @foreach ($dimensionData['Feedback'] as $feedbackType => $feedbackArray)
                                @if (!empty($feedbackArray) && is_array($feedbackArray))
                                    <div class="feedback">
                                        <div class="number">{{ str_pad($feedbackIndex, 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="type">
                                            <h3>{{ $feedbackType }}</h3>
                                            @foreach ($feedbackArray as $feedbackText)
                                                @if (!empty($feedbackText))
                                                    <p>{{ $feedbackText }}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php $feedbackIndex++; ?>
                                @endif
                            @endforeach
                        @else
                            <div class="feedback">
                                <div class="number">01</div>
                                <div class="type">
                                    <h3>No Feedback Available</h3>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="page-footer">
                    Page {{ $pageNumber + 1 }}
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Bottom Scripts -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/TweenMax.min.js') }}"></script>
<script src="{{ asset('assets/js/resizeable.js') }}"></script>
<script src="{{ asset('assets/js/xenon-api.js') }}"></script>
<script src="{{ asset('assets/js/xenon-toggles.js') }}"></script>
<script src="{{ asset('assets/js/toastr/toastr.min.js') }}"></script>

<!-- JavaScripts initializations and stuff -->
<script src="{{ asset('assets/js/xenon-custom.js') }}"></script>

<!-- Scripts -->
<script type="text/javascript">
    jQuery(document).ready(function($){
        // Status Messages
        var opts = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-right",
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
        console.log(opts);
    });
</script>

</body>
</html>
