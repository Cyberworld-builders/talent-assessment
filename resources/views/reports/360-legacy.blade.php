<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <title>Involved Talent : Report for {{ $user->name }}</title>

    @if(isset($download) && $download)
        {{-- For PDF: Inline the PDF CSS directly --}}
        <style>
        @php
            // Inline PDF CSS for better DomPDF compatibility
            $pdfCssPath = public_path('css/pdf.css');
            if (file_exists($pdfCssPath)) {
                $css = file_get_contents($pdfCssPath);
                // Remove @font-face declarations as they don't work well with DomPDF
                $css = preg_replace('/@font-face\s*\{[^}]+\}/s', '', $css);
                // Remove font-family references to custom fonts
                $css = str_replace("font-family: 'Avant Garde'", "font-family: Helvetica", $css);
                $css = str_replace("font-family: 'Avant Garde Oblique'", "font-family: Helvetica", $css);
                $css = str_replace("font-family: 'Avant Garde Demi'", "font-family: Helvetica", $css);
                $css = str_replace("font-family: 'Avant Garde Demi Oblique'", "font-family: Helvetica", $css);
                echo $css;
            }
        @endphp
        </style>
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/fonts/linecons/css/linecons.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/fonts/fontawesome/css/font-awesome.min.css') }}">
    @endif
    
    @if(!isset($download) || !$download)
    <style>
        /* Essential base styles from legacy scaffolding */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.42857143;
            color: #272842;
            background-color: #fff;
        }
        
        /* Clearfix utility */
        .clearfix:before,
        .clearfix:after {
            content: " ";
            display: table;
        }
        .clearfix:after {
            clear: both;
        }
        
        /* Image and table resets */
        img {
            border: 0;
            max-width: 100%;
            height: auto;
        }
        
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
        
        td, th {
            padding: 0;
        }
        
        /* Report-specific styles matching legacy exactly */
        body.report,
        .linc-action-plan {
            -webkit-print-color-adjust: exact;
            @if(isset($download) && $download)
                background: white;
            @else
                background: url('{{ asset('assets/images/report-background.jpg') }}') fixed no-repeat;
                background-size: cover;
            @endif
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            color: #272842;
        }
        
        body.report.with-header,
        .linc-action-plan.with-header {
            margin-top: 110px;
        }
        
        body.report .page-container,
        .linc-action-plan .page-container {
            background-color: white;
            height: 1100px;
            width: 850px;
            padding: 45px 45px;
            margin: 20px auto;
            position: relative;
            color: #272842;
            box-shadow: rgba(39, 42, 98, 0.04) 4px 5px 0px 0px;
        }
        
        body.report .page-container .page-wrapper,
        .linc-action-plan .page-container .page-wrapper {
            padding: 59px 73px;
            position: relative;
            height: 100%;
        }
        
        body.report .page-container .page-header,
        .linc-action-plan .page-container .page-header {
            margin: 0;
            border: none;
            padding: 0;
        }
        
        body.report .page-container .page-header .logo,
        .linc-action-plan .page-container .page-header .logo {
            width: 125px;
            float: left;
            margin-right: 8px;
            margin-top: -8px;
        }
        
        body.report .page-container .page-header .logo.right,
        .linc-action-plan .page-container .page-header .logo.right {
            width: 134px;
            float: right;
            margin-left: 8px;
            margin-right: 0;
            margin-top: -2px;
        }
        
        body.report .page-container .page-header .line,
        .linc-action-plan .page-container .page-header .line {
            height: 4px;
            background: #55a1d8;
            overflow: hidden;
        }
        
        body.report .page-container .page-footer,
        .linc-action-plan .page-container .page-footer {
            position: absolute;
            bottom: 0;
            width: 704px;
            margin-bottom: 59px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 9px;
            text-align: center;
            letter-spacing: 0.3px;
        }
        
        body.report .page-container p,
        .linc-action-plan .page-container p {
            letter-spacing: 0.5px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 16.8px;
            line-height: 37.05px;
            margin: 40px 0;
        }
        
        body.report .page-container p .color,
        .linc-action-plan .page-container p .color {
            color: #55a1d8;
        }
        
        body.report .page-container .page-title,
        .linc-action-plan .page-container .page-title {
            margin-top: 85px;
            text-transform: uppercase;
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            font-size: 69px;
            line-height: 60px;
            letter-spacing: -2px;
            display: inline-block;
            border-bottom: 4px solid #272842;
            margin-left: -73px;
            padding-left: 73px;
        }
        
        body.report .page-container .page-title img,
        .linc-action-plan .page-container .page-title img {
            margin-top: -11px;
        }
        
        body.report .page-container .page-title.alt,
        .linc-action-plan .page-container .page-title.alt {
            font-size: 50px;
        }
        
        body.report .page-container .page-title.alt span,
        .linc-action-plan .page-container .page-title.alt span {
            display: block;
            margin-left: 35px;
        }
        
        body.report .page-container .page-title.alt span.subtitle,
        .linc-action-plan .page-container .page-title.alt span.subtitle {
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 24px;
            letter-spacing: 0;
            line-height: 23px;
            margin-left: 0;
        }
        
        body.report .page-container .page-title.alt span.subtitle img,
        .linc-action-plan .page-container .page-title.alt span.subtitle img {
            position: relative;
            top: 3px;
            margin-right: 15px;
        }
        
        body.report .page-container .page-title.alt2,
        .linc-action-plan .page-container .page-title.alt2 {
            font-size: 34px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            letter-spacing: 0;
            margin-top: 80px;
        }
        
        body.report .page-container .page-title.alt2 span,
        .linc-action-plan .page-container .page-title.alt2 span {
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            font-size: 70px;
        }
        
        body.report .page-container .page-subtitle,
        .linc-action-plan .page-container .page-subtitle {
            text-transform: uppercase;
            margin-top: 25px;
            font-size: 24px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
        }
        
        body.report .page-container .page-subtitle span,
        .linc-action-plan .page-container .page-subtitle span {
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
        }
        
        body.report .cover-shapes,
        .linc-action-plan .cover-shapes {
            position: absolute;
            top: 226px;
            left: 0;
        }
        
        body.report .cover-title,
        .linc-action-plan .cover-title {
            margin-top: 265px;
            font-size: 36px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            padding: 0 80px;
            text-transform: uppercase;
        }
        
        body.report .cover-title span,
        .linc-action-plan .cover-title span {
            display: block;
            line-height: 28px;
        }
        
        body.report .cover-title span.report,
        .linc-action-plan .cover-title span.report {
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            font-size: 95px;
            line-height: 70px;
            text-indent: -3px;
            margin: 19px 0;
        }
        
        body.report .cover-title span.for,
        .linc-action-plan .cover-title span.for {
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 34px;
            line-height: 22px;
        }
        
        body.report .cover-title span.for strong,
        .linc-action-plan .cover-title span.for strong {
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
        }
        
        body.report .cover-disclaimer,
        .linc-action-plan .cover-disclaimer {
            position: absolute;
            bottom: 0;
            right: 0;
            margin-bottom: 59px;
            margin-right: 73px;
        }
        
        body.report .chart,
        .linc-action-plan .chart {
            width: 704px;
        }
        
        body.report .chart .title,
        .linc-action-plan .chart .title {
            font-size: 16px;
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            text-decoration: underline;
            text-align: center;
            margin-bottom: 45px;
        }
        
        body.report .chart .title span,
        .linc-action-plan .chart .title span {
            font-family: 'Avant Garde Demi Oblique', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #272940;
            text-decoration: none;
            display: inline-block;
        }
        
        body.report .chart .score,
        .linc-action-plan .chart .score {
            width: 15%;
            float: left;
            font-size: 91px;
            line-height: 60px;
            height: 230px;
            padding-top: 80px;
            text-align: center;
        }
        
        body.report .chart .score span,
        .linc-action-plan .chart .score span {
            font-size: 16px;
            display: block;
            text-align: center;
        }
        
        body.report .feedbacks .feedback,
        .linc-action-plan .feedbacks .feedback {
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
        }
        
        body.report .feedbacks .feedback .number,
        .linc-action-plan .feedbacks .feedback .number {
            width: 50px;
            color: #55a1d8;
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            font-size: 24px;
            text-align: center;
            border-right: 6px solid #55a1d8;
            padding-right: 16px;
            margin-right: 16px;
            line-height: 1.2;
            flex-shrink: 0;
        }
        
        body.report .feedbacks .feedback .type,
        .linc-action-plan .feedbacks .feedback .type {
            flex: 1;
        }
        
        body.report .feedbacks .feedback .type h3,
        .linc-action-plan .feedbacks .feedback .type h3 {
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            font-size: 24px;
            text-transform: uppercase;
            margin: 0 0 15px 0;
            line-height: 1.2;
            color: #272842;
        }
        
        body.report .feedbacks .feedback .type p,
        .linc-action-plan .feedbacks .feedback .type p {
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            margin: 0 0 10px 0;
            color: #272842;
        }
        
        /* Chart bars styling */
        body.report .chart .bars,
        .linc-action-plan .chart .bars {
            width: 80%;
            float: right;
            margin-top: 20px;
        }
        
        body.report .chart .graph-row,
        .linc-action-plan .chart .graph-row {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        body.report .chart .ratee,
        .linc-action-plan .chart .ratee {
            width: 120px;
            font-size: 14px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            text-align: left;
            margin-right: 10px;
        }
        
        body.report .chart .bar,
        .linc-action-plan .chart .bar {
            flex: 1;
            height: 25px;
            background-color: #f0f0f0;
            position: relative;
            margin-right: 30px;
        }
        
        body.report .chart .bar .inner,
        .linc-action-plan .chart .bar .inner {
            height: 100%;
            background-color: #55a1d8;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            font-size: 12px;
            color: white;
            font-weight: bold;
        }
        
        body.report .chart .graph-lines,
        .linc-action-plan .chart .graph-lines {
            position: relative;
            height: 20px;
            margin-bottom: 20px;
        }
        
        body.report .chart .graph-lines .line,
        .linc-action-plan .chart .graph-lines .line {
            position: absolute;
            height: 1px;
            background-color: #ddd;
            width: 100%;
        }
        
        body.report .chart .graph-lines .line span,
        .linc-action-plan .chart .graph-lines .line span {
            position: absolute;
            right: -25px;
            top: -8px;
            font-size: 10px;
            color: #666;
        }
        
        body.report .chart .graph-lines .line.one { top: 20%; }
        body.report .chart .graph-lines .line.two { top: 40%; }
        body.report .chart .graph-lines .line.three { top: 60%; }
        body.report .chart .graph-lines .line.four { top: 80%; }
        body.report .chart .graph-lines .line.five { top: 100%; }
        
        /* Norms styling */
        body.report .norms,
        .linc-action-plan .norms {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        body.report .norm-group,
        .linc-action-plan .norm-group {
            text-align: center;
            flex: 1;
        }
        
        body.report .norm,
        .linc-action-plan .norm {
            font-size: 24px;
            font-family: 'Avant Garde Demi', Helvetica, Arial, sans-serif;
            color: #55a1d8;
            margin-bottom: 5px;
        }
        
        body.report .norm-label,
        .linc-action-plan .norm-label {
            font-size: 12px;
            font-family: 'Avant Garde', Helvetica, Arial, sans-serif;
            color: #666;
            line-height: 1.2;
        }
    </style>
    @endif
    
    @if(!isset($download) || !$download)
        <script src="{{ asset('assets/js/jquery-1.11.1.min.js') }}"></script>
    @endif

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="page-body report">

{{-- Cover Page --}}
<div class="page-container" id="1">
    <img class="cover-shapes" src="{{ getAsset('assets/images/report-cover-shapes.png', isset($download) ? $download : false) }}" />
    
    <div class="page-wrapper">
        <div class="page-header">
            <img class="logo right" src="{{ getAsset('assets/images/involve-360-logo-small.png', isset($download) ? $download : false) }}">
            <div class="line"></div>
            <div class="clearfix"></div>
        </div>
        
        <div class="cover-title">
            <span>Involved-360</span>
            <span class="report">Report</span>
            <span class="for"><strong>for</strong> {{ $user->name }}</span>
        </div>

        <div class="cover-disclaimer">
            <img src="{{ getAsset('assets/images/logo-tagline.png', isset($download) ? $download : false) }}" />
        </div>
    </div>
</div>

{{-- Introduction Page --}}
<div class="page-container" id="2">
    <div class="page-wrapper">
        <div class="page-header">
            <img class="logo" src="{{ getAsset('assets/images/logo-small.png', isset($download) ? $download : false) }}">
            <div class="line"></div>
            <div class="clearfix"></div>
        </div>
        
        <div class="page-title">
            <img src="{{ getAsset('assets/images/badge.png', isset($download) ? $download : false) }}" />
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
                    <img class="logo right" src="{{ getAsset('assets/images/involve-360-logo-small.png', isset($download) ? $download : false) }}">
                    <div class="line"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="page-title alt">
                    <span class="subtitle">
                        <img src="{{ getAsset('assets/images/triangle.png', isset($download) ? $download : false) }}" />
                        Competency:
                    </span>
                    @if($dimensionName)<span>{{ $dimensionName }}</span>@endif
                </div>
                
                <div class="page-content">
                    <p>
                        {{ $dimensionData['Definition'] ?? 'Competency description not available.' }}
                    </p>

                    <div class="chart">
                        <div class="title">
                            Your Current Scores By Ratee Source<br/>
                            <span><img src="{{ getAsset('assets/images/triangle-orange.png', isset($download) ? $download : false) }}" /> Indicates Significant Growth Opportunity</span>
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
                                                <div class="ratee">
                                                    {{ $raterType }}
                                                </div>
                                                <div class="bar">
                                                    <div class="inner" style="width:{{ ($score / 5) * 100 }}%;">
                                                        {{ number_format($score, 1) }}
                                                    </div>
                                                    @if(($dimensionData['Industry'] ?? 0) > 0 && $score < (($dimensionData['Industry'] ?? 0) - 0.5))
                                                        <img src="{{ getAsset('assets/images/triangle-orange.png', isset($download) ? $download : false) }}" style="position: absolute; right: -20px; top: 50%; transform: translateY(-50%); width: 12px; height: 12px;" title="Significantly below industry norm" />
                                                    @endif
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
                            <div class="norm-label">Industry Norm for<br/>@if($user->client && $user->client->industry)<span>{{ $user->client->industry }}</span>@else<span>No Industry Set</span>@endif</div>
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
                    <img class="logo" src="{{ getAsset('assets/images/logo-small.png', isset($download) ? $download : false) }}">
                    <div class="line"></div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="page-title alt2">
                    Developmental<br/><span>Feedback</span>
                </div>

                <div class="page-subtitle">
                    <img src="{{ getAsset('assets/images/triangle-orange-large.png', isset($download) ? $download : false) }}" />
                    For: @if($dimensionName)<span>{{ $dimensionName }}</span>@endif
                </div>
                
                <div class="page-content">
                    <div class="feedbacks">
                        @if (isset($dimensionData['Feedback']) && is_array($dimensionData['Feedback']) && !empty(array_filter($dimensionData['Feedback'])))
                            <?php $feedbackIndex = 1; ?>
                            @foreach ($dimensionData['Feedback'] as $feedbackType => $feedbackArray)
                                @if (!empty($feedbackArray) && is_array($feedbackArray) && !empty(array_filter($feedbackArray)))
                                    <div class="feedback">
                                        <div class="number">{{ str_pad($feedbackIndex, 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="type">
                                            <h3>{{ $feedbackType }}</h3>
                                            @foreach ($feedbackArray as $feedbackText)
                                                @if (!empty(trim($feedbackText)))
                                                    <p>{{ trim($feedbackText) }}</p>
                                                @endif
                                            @endforeach
                                        </div>
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

@if(!isset($download) || !$download)
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
@endif

</body>
</html>
