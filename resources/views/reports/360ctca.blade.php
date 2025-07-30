<html><head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252">
    <meta name="viewport" content="width=device-width">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/wp/wp-content/themes/aoe/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="/wp/wp-content/themes/aoe/reports/reports.css">
    <style>
        h4.small-title {font-size: 20px;}
        .par {font-family:Avenir Next LT Pro Medium;}
        .expectations {border-left: 5px solid #eee;}
        .cover-for {padding-top: 90px; padding-bottom: 60px;}
        @if ($user->client->id == 22)
            .cover-for {padding-top: 90px; padding-bottom: 00px;}
        @endif
        #score-360 {background:url('/wp/wp-content/themes/aoe/images/360-gradient.jpg') center center no-repeat; background-size:contain; height:50px;}
        .score-bar {
            background: #02244a;
            height: 60px;
            position: absolute;
            width: 4px;
            border-radius: 20px;
            top: -3px;
            /*outline: 3px solid rgba(255,255,255,0.5);*/
            left: 50%;
            z-index: 10;
        }
        .score-bar-background {
            max-width: 100%;
            position: relative;
            top: 4px;
        }
        .score-bar:before {
            /*content: "4.3";*/
            color: white;
            position: absolute;
            top: 7px;
            font-family: bebas neue;
            font-size: 32px;
            left: -45px;
        }
        #score-360-sm {height:30px; margin-top:20px;}
        #score-360-sm .score-bar {
            background: #02244a;
            height: 32px;
            position: absolute;
            width: 2px;
            border-radius: 20px;
            top: -2px;
            /* outline: 3px solid rgba(255,255,255,0.5); */
            left: 50%;
            z-index: 10;
        }
        #score-360-sm .score-bar:before {
            /*content: "Supervisor: 4.3";*/
            color: white;
            position: absolute;
            top: 4px;
            font-family: bebas neue;
            font-size: 16px;
            left: -156px;
            text-align: right;
            width: 150px;
        }
        #other-scores {margin-top: 10px;}

        @foreach ($scores as $dimensionName => $dimension)
            #score-360 .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }} { left: {{ ($dimension['Score']['Total'] - 1) * 25 }}%; }
        #score-360 .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }}:before { content: "{{ number_format($dimension['Score']['Total'], 2) }}"; }
        @endforeach

        @foreach ($scores as $dimensionName => $dimension)
            @foreach ($dimension['Score'] as $category => $score)
                #score-360-sm .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }}.{{ strtolower(str_replace(' ', '-', $category)) }} { left: {{ ($score - 1) * 25 }}%; }
        #score-360-sm .score-bar.{{ strtolower(str_replace(' ', '-', $dimensionName)) }}.{{ strtolower(str_replace(' ', '-', $category)) }}:before { content: "{{ $category }}: {{ number_format($score, 2) }}"; }
        @endforeach
        @endforeach
    </style>
</head>
<body>
<!--Page 1-->
<div class="page-container" id="1">
    <div class="container">
        <!--Logo-->
        <div class="row">
            <div class="col-xs-2 visible-xs"></div>
            <div class="col-xs-8 col-sm-8 col-sm-offset-2 text-center">
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                @if ($user->client->id == 22)
                    <img class="img-responsive text-center cover-logo" style="padding:0px 0px 0px;" src="/wp/wp-content/themes/aoe/images/ctca-logo.png">
                @else
                    <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                @endif
            </div>
        </div>
        <!--Candidate-->
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <h3>360 Report for:</h3>
                <h4>{{ $user->name }}</h4>
            </div>
        </div>
        <!--Overview-->
        <div class="row">
            <div class="col-sm-12">
                <h5>Overview</h5>
                <p>The present report describes the results of a 360
                    survey for use as a developmental tool at SRMC. Other stakeholders who
                    know your job were asked to complete the appraisal form.  These would be
                    a mix of supervisors, co-workers, and direct reports. Each respondent
                    completed the 360 appraisal form. The six (6) performance dimensions are presented below along
                    with your average score. We summarized responses from an open-ended
                    question asking for specific developmental comments. The specific survey
                    used is known as a behavioral anchored rating scale because the values
                    on the rating scaled are anchored with specific behaviors. We provide a set of anchors in this
                    feedback form to help you better gauge your scores and feedback. The primary
                    advantage of this tool is that one is evaluated against a specific
                    criterion and a path towards improvement is clear.</p>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>
        <!--Disclaimer-->
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <small>
                    The AOE Group, LLC offers the most scientifically
                    valid candidate assessments. AOE uses the latest Talent Evidence from
                    the scientific literature, their own research, and the needs of
                    organizations to arrive at Evidence-Based Talent Solutions.
                </small>
            </div>
            <div class="col-xs-2 col-sm-2 text-right">
                <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/report-logo-1.png">
            </div>
        </div>
    </div>
</div>

<?php $page = 2; ?>

@foreach ($scores as $dimensionName => $dimension)

    <!--Page 2-->
    <div class="page-container" id="2">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">
            <!--Heading-->
            <div class="row text-center">
                <div class="col-sm-12">
                    <div class="row">
                        <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                        <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                            <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/aoe-360.png">
                        </div>
                        <h1>Developmental Performance Management Systems for:</h1>
                        <h4>{{ $user->name }}</h4>
                    </div>
                </div>
                <div class="col-sm-12 text-justify">
                    <h5>Core Competencies</h5>
                    <p><strong>Developmental Performance Dimension: {{ $dimensionName }}</strong></p>
                    <p>Definition: {{ $dimension['Definition'] }}</p>
                    <div class="row">
                        <div class="col-sm-12" id="score-360">
                            <div class="row">
                                <img class="score-bar-background" src="/wp/wp-content/themes/aoe/images/360-gradient.jpg" />
                            </div>
                            <img class="score-bar {{ strtolower(str_replace(' ', '-', $dimensionName)) }}" src="/wp/wp-content/themes/aoe/images/score-bar.jpg" />
                            <div class="score-bar {{ strtolower(str_replace(' ', '-', $dimensionName)) }}"></div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
            <div id="invisible-0" class="row">
                <div class="col-sm-12">
                    <div class="col-xs-4 expectations">
                        <h3>1<span class="par">)</span> Below Expectations</h3>
						<?php shuffle($dimension['Expectations']['1']) ?>
                        <small>{{ $dimension['Expectations']['1'][0] }}</small>
                    </div>
                    <div class="col-xs-4 expectations">
                        <h3>3<span class="par">)</span> Meets Expectations</h3>
						<?php shuffle($dimension['Expectations']['3']) ?>
                        <small>{{ $dimension['Expectations']['3'][0] }}</small>
                    </div>
                    <div class="col-xs-4 expectations">
                        <h3>5<span class="par">)</span> Exceeds Expectations</h3>
						<?php shuffle($dimension['Expectations']['5']) ?>
                        <small>{{ $dimension['Expectations']['5'][0] }}</small>
                    </div>
                </div>
            </div>
            <div id="other-scores" class="row">
                @foreach ($dimension['Score'] as $category => $score)
					<?php if ($category == 'Total') continue; ?>
                    <div class="col-xs-6" id="score-360-sm">
                        <div style="position:relative;">
                            <img class="score-bar-background" src="http://aoescience.com/wp/wp-content/themes/aoe/images/360-gradient.jpg">
                            <img class="score-bar {{ strtolower(str_replace(' ', '-', $dimensionName)) }} {{ strtolower(str_replace(' ', '-', $category)) }}" src="http://aoescience.com/wp/wp-content/themes/aoe/images/score-bar.jpg">
                            <div class="score-bar {{ strtolower(str_replace(' ', '-', $dimensionName)) }} {{ strtolower(str_replace(' ', '-', $category)) }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

	<?php
	// See if we have any feedback in any of the categories
	$feedback = false;
	foreach ($dimension['Feedback'] as $feedbackCollection)
		if ($feedbackCollection)
			$feedback = true;
	?>
    @if ($feedback)
		<?php $page++; ?>
		<?php $num = 1; ?>
		<?php $strcount = 0; ?>
		<?php
		$totalCount = 0;
		foreach ($dimension['Feedback'] as $feedbacks)
			foreach ($feedbacks as $feedback)
				$totalCount++;
		?>
		<?php $numPerPage = 1; ?>

        <!--Page 3-->
        <div class="page-container" id="3">
            <div class="img-container-1">
                <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                <small>Page {{ $page }}</small>
            </div>
            <div class="container">
                <!--Heading-->
                <div class="row text-center">
                    <div class="col-sm-12">
                        <div class="row">
                            <h1>Developmental Feedback</h1>
                            <h4>For {{ $user->name }}</h4>
                        </div>
                    </div>
                </div>
                <div id="invisible-0" class="row">
                    <div class="col-xs-12">

                        @foreach ($dimension['Feedback'] as $feedbackCategory => $feedbacks)

                            @if ($feedbacks)
                                <h4 class="small-title">{{ $feedbackCategory }}</h4>
                            @endif

                            @foreach ($feedbacks as $feedback)
                                <p>{{ $num }}<span class="par">)</span> {{ $feedback }}</p>
								<?php $strcount += strlen($feedback) ?>

                                @if (($numPerPage % 7 == 0 and $num < $totalCount) or ($strcount > 1700 and $num < $totalCount))
									<?php $strcount = 0; ?>
									<?php $numPerPage = 1; ?>
                    </div>
                </div>
            </div>
        </div>

        <!--Page 3-->
        <div class="page-container" id="3">
            <div class="img-container-1">
                <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
				<?php $page++; ?>
                <small>Page {{ $page }}</small>
            </div>
            <div class="container">
                <!--Heading-->
                <div class="row text-center">
                    <div class="col-sm-12">
                        <div class="row">
                            <h1>Developmental Feedback</h1>
                            <h4>For {{ $user->name }}</h4>
                        </div>
                    </div>
                </div>
                <div id="invisible-0" class="row">
                    <div class="col-xs-12">
                        @endif
						<?php $num++; ?>
						<?php $numPerPage++; ?>
                        @endforeach
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

    @endif

	<?php $page++; ?>

@endforeach

<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>
{{--<script src="aoe-360_files/charts.html"></script>--}}

</body></html>