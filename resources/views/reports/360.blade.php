<html moznomarginboxes="" mozdisallowselectionprint="">
<head>
    <meta name="viewport" content="width=device-width">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/assets/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="/assets/css/reports.css">
    <style>
        body {
            background-size: cover;
        }
        h4.small-title {font-size: 20px;}
        .par {font-family:Avenir Next LT Pro Medium;}
        .expectations {border-left: 5px solid #eee;}
        .cover-for {padding-top: 90px; padding-bottom: 60px;}
    </style>
</head>
<body>

{{-- Cover --}}
<?php $page = 1; ?>
<div class="page-container" id="{{ $page }}">

    {{-- Cover Shapes --}}
    @if ($user->client->whitelabel && $user->client->id == 29)
        <img class="cover-shapes" src="{{ asset('assets/images/angela-cover-shapes.png') }}" />
    @else
        <img class="cover-shapes" src="{{ asset('assets/images/report-cover-shapes.png') }}" />
    @endif
    
    {{-- Container --}}
    <div class="page-wrapper">

        {{-- Header --}}
        @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

        {{-- Title --}}
        <div class="cover-title">
            <span>Involved-360</span>
            <span class="report">Report</span>
            <span class="for"><strong>for</strong> {{ $user->name }}</span>
        </div>

        {{-- Disclaimer --}}
        <div class="cover-disclaimer">
            @if ($user->client->whitelabel && $user->client->id == 29)
                <img src="{{ asset('assets/images/powered-by-involved-medium-gray.png') }}" />
            @else
                <img src="{{ asset('assets/images/logo-tagline.png') }}" />
            @endif
        </div>
    </div>
</div>

{{-- Overview --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">

    {{-- Container --}}
    <div class="page-wrapper">

        {{-- Header --}}
        @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

        {{-- Title --}}
        <div class="page-title">
            @if ($user->client->whitelabel && $user->client->id == 29)
            @else
                <img src="{{ asset('assets/images/badge.png') }}" />
            @endif
            Involved-360
        </div>
        
        {{-- Content --}}
        <div class="page-content">
            <p>This is your Involved-360 report. This report should be used as a critical piece of your overall leadership development at {{ $user->client->name }}. Stakeholders (e.g., supervisor, peers, subordinates, customers) familiar with your work completed the 360-evaluation to provide you an analytically robust picture of your strengths and improvement opportunities. Additionally, each of your raters was asked to provide qualitative feedback, which can greatly augment your quantitative scores. Taken together, this report provides you a wealth of information to not only significantly develop your own leadership, but also drive critical business outcomes. Each individual competency score is presented with corresponding rater feedback and suggestions. Your scores are compared to (1) norms for similar jobs/positions and (2) the average of your colleagues that have also recently completed the 360-feedback survey at {{ $user->client->name }}. Anchoring your scores with industry norms and your company averages provides a much more accurate representation of where your scores stand and provides enhanced motivation to accelerate your leadership involvement.</p>
        </div>

        {{-- Footer --}}
        @include('reports.partials._footer', [$page])
    </div>
</div>

@foreach ($scores as $dimensionName => $dimension)

    {{-- Competency Scores --}}
    <?php $page++; ?>
    <div class="page-container" id="{{ $page }}">

        {{-- Container --}}
        <div class="page-wrapper">

            {{-- Header --}}
            @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

            {{-- Title --}}
            <div class="page-title alt">
                <span class="subtitle">
                    @if ($user->client->whitelabel && $user->client->id == 29)
                        <img src="{{ asset('assets/images/angela-triangle.png') }}" />
                    @else
                        <img src="{{ asset('assets/images/triangle.png') }}" />
                    @endif
                    Competency:
                </span>
                <span>
                    {{ $dimensionName }}
                </span>
            </div>
            
            {{-- Content --}}
            <div class="page-content">
                <p>
                    {{ $scores[$dimensionName]['Definition'] }}
                </p>

                <div class="chart">
                    <div class="title">
                        Your Current Scores By Ratee Source<br/>
                        <span><img src="{{ asset('assets/images/triangle-orange.png') }}" /> Indicates Significant Growth Opportunity</span>
                    </div>

                    <div class="score">
                        {{ number_format($scores[$dimensionName]['Score']['Total'], 1) }}
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

                            @foreach ($scores[$dimensionName]['Score'] as $category => $score)
                                <div class="graph-row">
                                    <div class="ratee">{{ $category }}</div>

                                    <div class="bar">
                                        <div class="inner {{ (isset($scores[$dimensionName]['Flagged'][$category]) && $scores[$dimensionName]['Flagged'][$category] ? 'flagged' : '') }}" style="width:{{ isset($scores[$dimensionName]['Percent'][$category]) ? $scores[$dimensionName]['Percent'][$category] : ($scores[$dimensionName]['Score'][$category] / 5 * 100) }}%;">
                                            {{ number_format($scores[$dimensionName]['Score'][$category], 1) }}
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="norms">
                    <div class="norm-group industry">
                        <div class="norm">{{ number_format(isset($scores[$dimensionName]['Norm']) ? $scores[$dimensionName]['Norm'] : 3.5, 2) }}</div>
                        <div class="norm-label">Industry Norm for<br/><span>{{ isset($scores[$dimensionName]['Industry']) ? $scores[$dimensionName]['Industry'] : 'Similar Roles' }}</span></div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="norm-group group">
                        <div class="norm">{{ number_format(isset($scores[$dimensionName]['Group Average']) ? $scores[$dimensionName]['Group Average'] : $scores[$dimensionName]['Score']['Total'], 2) }}</div>
                        <div class="norm-label">Avg Score<br/><span>For This Group</span></div>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

            {{-- Footer --}}
            @include('reports.partials._footer', [$page])
        </div>
    </div>

    {{-- Feedback --}}
    <?php
        $wordCount = 0;
        $counter = 0;
        $break = false;
    ?>
    <?php $page++; ?>
    <div class="page-container" id="{{ $page }}">

        {{-- Container --}}
        <div class="page-wrapper">

            {{-- Header --}}
            @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

            {{-- Title --}}
            <div class="page-title alt2">
                Developmental<br/><span>Feedback</span>
            </div>

            {{-- Sub-title --}}
            <div class="page-subtitle">
                @if ($user->client->whitelabel && $user->client->id == 29)
                    <img src="{{ asset('assets/images/angela-triangle.png') }}" />
                @else
                    <img src="{{ asset('assets/images/triangle-orange-large.png') }}" />
                @endif
                For: <span>{{ $dimensionName }}</span>
            </div>
            
            {{-- Content --}}
            <div class="page-content">
                <div class="feedbacks">
                    <?php $i = 0; ?>
                    @foreach($scores[$dimensionName]['Feedback'] as $type => $feedbacks)
                        <?php $i++; ?>
                        <div class="feedback">
                            <div class="number">0{{ $i }}</div>
                            <div class="type">
                                <h3>{{ $type }}</h3>
                                @foreach ($feedbacks as $c => $feedback)
                                    <?php 
                                        $wordCount += str_word_count($feedback); 
                                        if ($wordCount > 180) {
                                            $break = true;
                                            $counter = $c;
                                            break;
                                        }
                                    ?>
                                    <p>{{ $feedback }}</p>
                                @endforeach
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            @include('reports.partials._footer', [$page])
        </div>
    </div>

    <?php
        $wordCount = 0;
        $counter2 = 0;
        $breakAgain = false;
    ?>
    @if ($break)
    <?php $page++; ?>
    <div class="page-container" id="{{ $page }}">

        {{-- Container --}}
        <div class="page-wrapper">

            {{-- Header --}}
            @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

            {{-- Content --}}
            <div class="page-content">
                <div class="feedbacks">
                    <div class="feedback">
                        <div class="number">02</div>
                        <div class="type">
                            <h3>Others</h3>
                            @foreach ($scores[$dimensionName]['Feedback']['Others'] as $c => $feedback)
                                <?php 
                                    if ($c < $counter) {
                                        continue;
                                    }
                                    $wordCount += str_word_count($feedback);
                                    if ($wordCount > 280 && $c > $counter) {
                                        $breakAgain = true;
                                        $counter2 = $c;
                                        break;
                                    }
                                ?>
                                <p>{{ $feedback }}</p>
                            @endforeach
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            @include('reports.partials._footer', [$page])
        </div>
    </div>
    @endif

    @if ($breakAgain)
    <?php $page++; ?>
    <div class="page-container" id="{{ $page }}">

        {{-- Container --}}
        <div class="page-wrapper">

            {{-- Header --}}
            @include('reports.partials._header', [$page, 'logo' => 'involve-360-logo-small.png'])

            {{-- Content --}}
            <div class="page-content">
                <div class="feedbacks">
                    <div class="feedback">
                        <div class="number">02</div>
                        <div class="type">
                            <h3>Others</h3>
                            @foreach ($scores[$dimensionName]['Feedback']['Others'] as $c => $feedback)
                                <?php 
                                    if ($c < $counter2) {
                                        continue;
                                    }
                                ?>
                                <p>{{ $feedback }}</p>
                            @endforeach
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            @include('reports.partials._footer', [$page])
        </div>
    </div>
    @endif
@endforeach

</body>
</html>