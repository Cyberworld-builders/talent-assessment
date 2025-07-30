<html moznomarginboxes="" mozdisallowselectionprint="">
<head>
    <meta name="viewport" content="width=device-width">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/wp/wp-content/themes/aoe/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="/wp/wp-content/themes/aoe/reports/reports.css">
    <style>
        body {
            background-image:none;
            background-color:#ddd;
        }
        h1, h2, h3, h4, h5, a, a:hover {
            color:#970E7E;
        }
        #chart2 {
            width:600px;
            margin:0 auto
        }
        .small {
            font-size:13px;
            line-height:18px;
        }
    </style>
    <script>
        $(function () {
            $('#chart').highcharts({
                chart: {
                    type: 'areaspline',
                    height:430,
                    borderRadius: 0,
                    spacingRight:0,
                    plotBackgroundColor:'rgba(0,0,0,0)',
                    backgroundColor:'rgba(0,0,0,0)',
                    spacingLeft:0,
                    style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'}
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    x: 0,
                    y: 0,
                    floating: false,
                    borderWidth: 0,
                    reversed: false,
                    symbolRadius:20,
                    symbolWidth:10,
                    symbolHeight:10,
                    itemMarginBottom:7,
                    padding:0,
                },
                title: {
                    text: 'Raw Score: {{ $scores['Aptitude']['Score'] }}<br>Percentile Score: {{ $scores['Aptitude']['Percentile'] }}%',
                    align: 'left',
                    verticalAlign: 'top',
                    style: {fontSize: '16px', lineHeight:'23px'},
                    floating: true,
                    y: 7
                },
                subtitle: {
                    @if ($scores['Aptitude']['Division'] == 5)
                    text: 'Not Recommended',
                    @elseif ($scores['Aptitude']['Division'] == 3)
                    text: 'Caution',
                    @elseif ($scores['Aptitude']['Division'] == 1)
                    text: 'Pursue',
                    @endif
                    floating: true,
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        @if ($scores['Aptitude']['Division'] == 5)
                        color: '#E32731',
                        @elseif ($scores['Aptitude']['Division'] == 3)
                        color: '#E7B428',
                        @elseif ($scores['Aptitude']['Division'] == 1)
                        color: '#30BD21',
                        @endif
                        fontWeight:'bold',
                        lineHeight:'45px'
                    },
                    y: 52
                },
                credits:false,
                tooltip: {enabled: false},
                plotOptions: {
                    allowPointSelect:false,
                    showInLegend: false,
                    areaspline: {
                        marker: {
                            enabled: false,
                            hover:false,
                            states: {hover: {enabled: false}}
                        }
                    }
                },
                xAxis: {
                    title: {
                        text: 'Raw Score',
                        style: {fontSize: '16px', color:'#222222'}
                    },
                    allowDecimals:false,
                    crosshair:false,
                    lineColor:"#000000",
                    lineWidth:1,
                    tickColor: '#000000',
                    tickWidth: 1,
                    // floor: 17,
                    // ceiling: 36,
                    tickInterval:1,
                    plotLines: [{
                        color: 'rgba(0,0,0,0.25)',
                        dashStyle: 'longdash',
                        value: {{ $scores['Aptitude']['Score'] }},
                        width: 1,
                        zIndex: 5,
                        label: {
                            text: 'Your Score',
                            align: 'center',
                            verticalAlign: 'middle',
                        }
                    }]
                },
                yAxis: {
                    title: {
                        text:null,
                        style: {fontSize: '16px'}
                    },
                    lineWidth:0,
                    gridLineColor: 'transparent',
                    tickInterval: 10,
                    labels: {enabled:false},
                    categories: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100]
                },
                series: [{
                    color: '#E32731',
                    name: 'Not Recommended',
                    borderRadius:0,
                    marker: {enabled: false},
                    data: [
                        [0,0],
                        [8,20],
                        [20,100],
                        [33,20],
                        [37,0]
                    ],
                    zoneAxis: 'x',
                    fillOpacity:1,
                    zones: [
//                        {value: 26, color:'#E32731'},
//                        {value: 32, color:'#E7B428'},
//                        {value: 41, color:'#30BD21'}
                        @foreach($scores['Aptitude']['Zones']['value'] as $i => $zoneValue)
                            <?php
                                $value = $scores['Aptitude']['Zones']['value'][$i];
                                $color = $scores['Aptitude']['Zones']['color'][$i];
                                echo '{value: '.$value.', color:"'.$color.'"},';
                            ?>
                        @endforeach
                    ],
                },{
                    name: 'Caution',
                    color: '#E7B428',
                },{
                    name: 'Pursue',
                    color: '#30BD21',
                }]
            });
            $('#chart2').highcharts({
                chart: {
                    type: 'areaspline',
                    height:430,
                    borderRadius: 0,
                    spacingRight:0,
                    plotBackgroundColor:'rgba(0,0,0,0)',
                    backgroundColor:'rgba(0,0,0,0)',
                    spacingLeft:0,
                    style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'}
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    x: 0,
                    y: 0,
                    floating: false,
                    borderWidth: 0,
                    reversed: false,
                    symbolRadius:20,
                    symbolWidth:10,
                    symbolHeight:10,
                    itemMarginBottom:7,
                    padding:0,
                },
                title: {
                    text: 'Raw Score: {{ $scores['Ability']['Score'] }}<br>Percentile Score: {{ $scores['Ability']['Percentile'] }}%',
                    align: 'left',
                    verticalAlign: 'top',
                    style: {fontSize: '16px', lineHeight:'23px'},
                    floating: true,
                    y:7
                },
                subtitle: {
                    @if ($scores['Ability']['Division'] == 5)
                    text: 'Not Recommended',
                    @elseif ($scores['Ability']['Division'] == 3)
                    text: 'Caution',
                    @elseif ($scores['Ability']['Division'] == 1)
                    text: 'Pursue',
                    @endif
                    floating: true,
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        @if ($scores['Ability']['Division'] == 5)
                        color: '#E32731',
                        @elseif ($scores['Ability']['Division'] == 3)
                        color: '#E7B428',
                        @elseif ($scores['Ability']['Division'] == 1)
                        color: '#30BD21',
                        @endif
                        fontWeight:'bold',
                        lineHeight:'45px'
                    },
                    y: 52
                },
                credits:false,
                tooltip: {enabled: false},
                plotOptions: {
                    allowPointSelect:false,
                    showInLegend: false,
                    areaspline: {
                        marker: {
                            enabled: false,
                            hover:false,
                            states: {hover: {enabled: false}}
                        }
                    }
                },
                xAxis: {
                    title: {
                        text: 'Raw Score',
                        style: {fontSize: '16px', color:'#222222'}
                    },
                    allowDecimals:false,
                    crosshair:false,
                    lineColor:"#000000",
                    lineWidth:1,
                    tickColor: '#000000',
                    tickWidth: 1,
                    // floor: 10,
                    // ceiling: 55,
                    tickInterval:1,
                    plotLines: [{
                        color: 'rgba(0,0,0,0.25)',
                        dashStyle: 'longdash',
                        value: {{ $scores['Ability']['Score'] }},
                        width: 1,
                        zIndex: 5,
                        label: {
                            text: 'Your Score',
                            align: 'center',
                            verticalAlign: 'middle',
                        }
                    }]
                },
                yAxis: {
                    title: {
                        text:null,
                        style: {fontSize: '16px'}
                    },
                    lineWidth:0,
                    gridLineColor: 'transparent',
                    tickInterval: 10,
                    labels: {enabled:false},
                    categories: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100]
                },
                series: [{
                    color: '#E32731',
                    name: 'Not Recommended',
                    borderRadius:0,
                    marker: {enabled: false},
                    data: [
                        [0,0],
                        [10,20],
                        [25,100],
                        [40,20],
                        [50,0]
                    ],
                    zoneAxis: 'x',
                    fillOpacity:1,
                    zones: [
//                        {value: 20, color:'#E32731'},
//                        {value: 26, color:'#E7B428'},
//                        {value: 50, color:'#30BD21'}
                        @foreach($scores['Ability']['Zones']['value'] as $i => $zoneValue)
                            <?php
                                $value = $scores['Ability']['Zones']['value'][$i];
                                $color = $scores['Ability']['Zones']['color'][$i];
                                echo '{value: '.$value.', color:"'.$color.'"},';
                            ?>
                        @endforeach
                    ],
                },{
                    name: 'Caution',
                    color: '#E7B428',
                },{
                    name: 'Pursue',
                    color: '#30BD21',
                }]
            });
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
                        {{ $scores['Personality']['Honesty-Humility'] }},
                        {{ $scores['Personality']['Emotional Control'] }},
                        {{ $scores['Personality']['Extraversion'] }},
                        {{ $scores['Personality']['Agreeableness'] }},
                        {{ $scores['Personality']['Conscientiousness'] }},
                        {{ $scores['Personality']['Openness'] }}
                    ]
                }]
            });
        });
    </script>
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
                <img class="img-responsive text-center cover-logo" src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
            </div>
        </div>
        <!--Candidate-->
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <br><br><br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <h3>Candidate Profile for:</h3>
                <h4>{{ $user->name }}</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>
        <!--Overview-->
        <div class="row">
            <div class="col-sm-10">
                <h5>Overview</h5>
                <p>
                    This report provides a recommendation for {{ $user->name }}, who applied for a {{ $job->name }} position.
                    This report covers {{ $job->name }} aptitude, reasoning, and personality. This report provides
                    evidence for the candidate's likelihood of success in {{ $job->name }} related positions.
                </p>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>
        <!--Disclaimer-->
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6 class="small">
                    AOE Science offers the most scientifically valid candidate assessments. AOE uses the latest Talent Evidence from the scientific literature, their own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions.
                </h6>
            </div>
            <div class="col-xs-2 col-sm-2 text-right report-logo">
                <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/report-logo-1.png">
            </div>
        </div>
    </div>
</div>
<!--Page 2-->
<div class="page-container" id="2">
    <div class="img-container-1">
        <small>Page 2</small>
    </div>
    <div class="container">
        <!--Headline-->
        <div class="row text-center">
            <div class="col-sm-12">
                <br><br>
                <h1>Table of Contents</h1>
                <br><br>
            </div>
        </div>
        <!--Table of Contents-->
        <div class="row">
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>{{ $job->name }} Aptitude Report</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#3"><small>Page 3</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>{{ $job->name }} Recommendation</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#4"><small>Page 4</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>Reasoning Report</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#5"><small>Page 5</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>Reasoning Ability Evaluation</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#6"><small>Page 6</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>Personality Report</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#7"><small>Page 8</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>Personality Fit Recommendation</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#12"><small>Page 12</small></a>
            </div>
        </div>
    </div>
</div>
<!--Page 3-->
<div class="page-container" id="3">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 3</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
                        <br>
                    </div>
                </div>
                <h1>{{ $job->name }} Aptitude Report</h1>
                <h4>for {{ $user->name }}</h4>
                <br>
            </div>
            <div class="col-sm-12 text-justify">
                <p>The {{ $job->name }} Aptitude Test is an excellent indicator of one's aptitude for a {{ $job->name }} at Evonik.</p>
                <p>Below, we present graphical results for {{ $user->name }} followed by recommendations for this job based on the {{ $job->name }} Aptitude Test.
                    The raw score is shown on the graph below. The graph represents percentiles for {{ $job->name }} positions so you can see this
                    candidate's standing relative to the norms for this test.</p>
            </div>
        </div>
        <!--Chart-->
        <div class="row">
            <div class="chart">
                <div id="chart"></div>
            </div>
        </div>
    </div>
</div>
<!--Page 4-->
<div class="page-container" id="4">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 4</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
                        <br>
                    </div>
                </div>
                <h1>{{ $job->name }} Recommendation</h1>
                <h4>for {{ $user->name }}</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Raw Score</h3>
                <p>The raw score for {{ $user->name }} is noted in the graph to provide an overall snapshot of how this score compares on the full range of possible raw scores.</p>
                <p>{{ $user->name }} answered {{ $scores['Aptitude']['Score'] }} out of {{ $scores['Aptitude']['Total'] }} questions correct on the
                    {{ $job->name }} Aptitude Test which is {{ $scores['Aptitude']['Accuracy'] }}% accuracy.)</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Percentile Score</h3>

            @if ($scores['Aptitude']['Division'] == 5)

                <!-- Not Recommended Feedback -->
                    <p>Comparing {{ $user->name }} to other people taking this assessment, {{ $user->name }}'s percentile score is {{ $scores['Aptitude']['Percentile'] }}%,
                        meaning that {{ $user->name }}'s score is equal to or better than {{ $scores['Aptitude']['Percentile'] }}% of all others taking this assessment.
                        The score suggests {{ $user->name }} lacks a level of understanding for mechanical reasoning, math, and tools necessary to learn the job of {{ $job->name }}
                        based on this score.</p>

            @elseif ($scores['Aptitude']['Division'] == 3)

                <!-- Caution Feedback -->
                    <p>Comparing {{ $user->name }} to other people taking this assessment, {{ $user->name }}'s percentile score is {{ $scores['Aptitude']['Percentile'] }}%,
                        meaning that {{ $user->name }}'s score is equal to or better than {{ $scores['Aptitude']['Percentile'] }}% of all others taking this assessment.
                        The score suggests {{ $user->name }} has some of the raw knowledge necessary for the {{ $job->name }} position.
                        {{ $user->name }} can probably learn a good bit of {{ $job->name }} knowledge once on the job but some caution is recommended based on this score.</p>

            @elseif ($scores['Aptitude']['Division'] == 1)

                <!-- Pursue Feedback -->
                    <p>The score suggests {{ $user->name }} has the essential knowledge of mechanical reasoning, math, and tools to learn the job of {{ $job->name }} based on this score.</p>

                @endif

                <div id="invisible-0" class="col-xs-3"></div>
                <div id="invisible-12" class="col-xs-6">
                    <br><br>
                    @if ($scores['Aptitude']['Division'] == 5)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/not recommended.png">
                    @elseif ($scores['Aptitude']['Division'] == 3)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/caution.png">
                    @elseif ($scores['Aptitude']['Division'] == 1)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/pursue.png">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--Page 5-->
<div class="page-container" id="5">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 5</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-A.png">
                    </div>
                </div>
                <h1>Reasoning Report</h1>
                <h4>for {{ $user->name }}</h4>
            </div>
            <div class="col-sm-12 text-justify">
                <p>
                    The AOE-A is a scientifically valid, excellent indicator of a person's maximal performance level
                    as assessed by reasoning ability. Reasoning Ability has been used for years in many industries as
                    an indicator of the likelihood of job success.
                </p>
                <p>
                    Theoretical and empirical evidence clearly shows that reasoning ability is a fantastic way to assess what a
                    person can-do, at a maximal level in a given job. Reasoning Ability results provide evidence of a person's
                    ability to solve problems, learn new skills, and think critically.
                </p>
                <p>
                    Below, we present graphical results for {{ $user->name }} followed by AOE-A potential for this job.
                    The raw score is shown on the graph below. The graph represents percentiles so you can see this
                    candidate's standing relative to the norms for this test.
                </p>
            </div>
        </div>
        <!--Chart-->
        <div class="row">
            <div class="chart">
                <div id="chart2"></div>
            </div>
        </div>
    </div>
</div>
<!--Page 6-->
<div class="page-container" id="6">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 6</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-A.png">
                    </div>
                </div>
                <h1>Reasoning Ability Evaluation</h1>
                <h4>for {{ $user->name }}</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Raw Score</h3>
                <p>The raw score for {{ $user->name }} is noted in the graph to provide an overall snapshot of how this score compares on the full range of possible raw scores.</p>
                <p>{{ $user->name }} answered {{ $scores['Ability']['Score'] }} questions correct on the AOE-A which is {{ $scores['Ability']['Accuracy'] }}% accuracy.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Percentile Score</h3>
            @if ($scores['Ability']['Division'] == 5)

                <!-- Not Recommended Feedback -->
                    <p>Comparing {{ $user->name }} to other people taking this assessment, {{ $user->name }}'s percentile score is {{ $scores['Ability']['Percentile'] }}%, meaning that {{ $user->name }}'s
                        score is equal to or better than {{ $scores['Ability']['Percentile'] }}% of all others taking this assessment. The score suggests {{ $user->name }} lacks a level
                        of understanding for numerical reasoning, thinking about and using data, mathematical calculations,
                        understanding written language, and verbal reasoning necessary to learn the job of {{ $job->name }}.</p>

            @elseif ($scores['Ability']['Division'] == 3)

                <!-- Caution Feedback -->
                    <p>Comparing {{ $user->name }} to other people taking this assessment, {{ $user->name }}'s percentile score is {{ $scores['Ability']['Percentile'] }}%,
                        'meaning that {{ $user->name }}'s score is equal to or better than {{ $scores['Ability']['Percentile'] }}% of all others taking this assessment.</p>
                    <p>The score for {{ $user->name }} suggests moderate strengths with numerical reasoning, thinking about and using data,
                        mathematical calculations, understanding written language, and verbal reasoning. Overall, the score for {{ $user->name }}
                        indicates moderate capability to learn and apply new job knowledge. {{ $user->name }} has some potential for success in this
                        job based on the AOE-A score, but caution is recommended.</p>

            @elseif ($scores['Ability']['Division'] == 1)

                <!-- Pursue Feedback -->
                    <p>The score suggests {{ $user->name }} has the essential knowledge of numerical reasoning, thinking about and using data,
                        mathematical calculations, understanding written language, and verbal reasoning to learn the job of {{ $job->name }}.

                @endif
                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                    <br><br>
                    @if ($scores['Ability']['Division'] == 5)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/not recommended.png">
                    @elseif ($scores['Ability']['Division'] == 3)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/caution.png">
                    @elseif ($scores['Ability']['Division'] == 1)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/pursue.png">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--Page 7-->
<div class="page-container" id="7">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 7</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
                <h1>Personality Report</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>The AOE-P is based on the HEXACO model of personality, which is the latest scientifically valid model of individual personality.
                    The HEXACO model is valid across many different cultures and incorporates the most up-to-date and accurate framework for assessing personality.</p>
                <p>Personality is a very good indicator of what people will do on a typical day of work. The AOE-P is the first HEXACO-based assessment
                    for application to organizations. The theoretical and empirical evidence shows that there are six major dimensions to personality. These are described as:</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h3>Personality Dimension</h3>
            </div>
            <div class="col-xs-8">
                <h3>Personality Dimension Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Honesty-Humility</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on fairness, sincerity, modesty, and greed avoidance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Emotional Control</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on controlling your emotions, withstanding failures, setbacks and stresses, and maintaining and/or quickly regaining your composure.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Extraversion</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on social boldness, sociability, liveliness, and social self-esteem.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Agreeableness</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on flexibility, gentleness, forgiveness, and patience.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Conscientiousness</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on achievement, organization, perfectionism, and prudence.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p>Openness</p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on inquisitiveness, aesthetic apreciation, unconventionality, and creativity.</p>
            </div>
        </div>
    </div>
</div>
<!--Page 8-->
<div class="page-container" id="8">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 8</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
                <h1>Scoring and Importance</h1>
                <h5>of the AOE-P for {{ $job->name }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>AOE's vast experience in research, predictive analytics, and content validation reveals that for
                    {{ $job->name }}s all six dimensions of AOE-P are desired. Of these, Conscientiousness is a strongly
                    desired characteristic that connects to performance. Conscientiousness encompasses prudence, organization,
                    detail orientation, and achievement. Honesty and Humility is also strongly desired - encompassing fairness,
                    sincerity, modesty, and greed avoidance. Emotional Control is also strongly desired for {{ $job->name }} positions.
                    Although still important, some characteristics, such as Openness and Agreeableness, may not factor as heavily
                    for {{ $job->name }} positions.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart3"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Personality Factor Scores for {{ $user->name }}</h5>
                <p>For each of the major 6 dimensions there are 4 sub-factors totalling 24 unique indicators of personality.
                    The sub-factor dimensions are described below with the candidate's score for each. Of particular importance
                    for {{ $job->name }} positions, factors of Conscientiousness, Emotional Control, and Honesty-Humility are weighted
                    heavily in the AOE-P evaluation for {{ $job->name }}s. AOE's proprietary algorithms take such differences into account
                    to arrive at an overall Job-Fit Recommendation, which is presented below after the sub-factor scores.</p>
            </div>
        </div>
    </div>
</div>
<!--Page 9-->
<div class="page-container" id="9">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 9</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Honesty-Humility</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Sincerity</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Sincerity'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be genuine in interpersonal relations. Low scorers use flattery and are often seen as 'fake', whereas high scorers are viewed as being sincere and do not manipulate others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Fairness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Fairness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to avoid unfair action, fraud, and corruption. Low scorers might cheat or steal; high scorers are unlikely to take advantage of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Greed Avoidance</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Greed Avoidance'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be uninterested in material goods or social status. Low scorers want to enjoy and to display wealth and privilege; high scorers are uninterested in material goods or social status.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Modesty</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Modesty'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be modest and unassuming. Low scorers consider themselves as superior and entitled; high scorers see themselves as ordinary people.</h6>
            </div>
        </div>
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Emotional Control</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Fearlessness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Fearlessness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be brave/fearless. Low scorers are extremely fearful of physical harm; high scorers are relatively tough, brave, and not overly sensitive to physical injury.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Composure</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Composure'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to remain calm and collected at work. Low scorers worry excessively, even with minor issues; high scorers remain calm, even with major issues.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Independence</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Independence'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to work independently without much emotional support from others. Low scorers want encouragement and/or comfort from others; high scorers are self-assured and able to effectively deal with problems.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Stoical</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Stoical'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to accept what happens without showing strong emotion. Low scorers show strong emotions and have strong emotional attachments; high scorers show little emotion and have weak emotional attachments.</h6>
            </div>
        </div>
    </div>
</div>
<!--Page 10-->
<div class="page-container" id="10">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 10</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Extraversion</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Self-Esteem</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Self-Esteem'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to have positive self-regard, particularly at work. High scorers have self-respect and see themselves as likeable; low scorers tend to feel worthless and unpopular.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Social Boldness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Social Boldness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be comfortable and confident in work situations. Low scorers are typically shy or awkward, particularly in leadership positions or large settings; high scorers are comfortable leading groups and communicating with people.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Sociability</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Sociability'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to enjoy social situations and interacting with a variety of individuals. Low scorers prefer solitary activities and work tasks; high scorers enjoy talking, visiting, and interacting with others at work.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Liveliness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Liveliness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be optimistic, enthusiastic, and full of energy. Low scorers are generally not overly cheerful or dynamic; high scorers are generally enthusiastic and in high spirits.</h6>
            </div>
        </div>
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Agreeableness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Forgiveness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Forgiveness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to forgive and even trust those who may have caused harm. Low scorers might "hold a grudge" against those who have done one wrong; high scorers can forgive and are willing to work towards re-establishing friendly relations.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Gentleness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Gentleness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be mild mannered and gentle in dealings with others. Low scorers are generally critical of others; high scorers tend not to be judgemental of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Flexibility</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Flexibility'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to compromise and cooperate with others. Low scorers are viewed as stubborn and likely are argumentative; high scorers are accommodating to suggestions and generally flexible.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Patience</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Patience'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be patient and remain calm. Low scorers tend to get angry or upset easily; high scorers generally are more tolerant before possibly getting angry or upset.</h6>
            </div>
        </div>
    </div>
</div>
<!--Page 11-->
<div class="page-container" id="11">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 11</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Conscientiousness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Organization</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Organization'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to seek order and organize one's surroundings. Low scorers are generally sloppy and haphhazard; high scorers are generally well-organized and prefer a structured approach to tasks.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Achievement</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Achievement'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to work hard. Low scorers lack self-discipline and are not strongly motivated to achieve; high scorers are strongly motivated to achieve due to a strong "work ethic."</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Prudence</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Prudence'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be detailed oriented. Low scorers are tolerant of errors in their work; high scorers check carefully for mistakes and potential improvements.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Detailed</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Detailed'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to carefully think things through and avoid impulses. Low scorers follow impulses and do not consider consequences; high scorers consider multiple options and are generally careful and self-controlled.</h6>
            </div>
        </div>
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Primary Dimension: Openness</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-8">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Aesthetic Appreciation</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Aesthetic Appreciation'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to enjoy and see beauty in art, physical surroundings, and in nature. Low scorers don't care for art, aesthetics, or natural wonders; high scorers have a deep appreciation for a variety of art forms (e.g., nature, physical space).</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Inquisitiveness</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Inquisitiveness'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendecy to be inquisitive. Low scorers are generally not curious; high scorers are curious and prefer to know how things work or came to be.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Creativity</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Creativity'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to be innovative and experimental. Low scorers have little inclination for original thought, whereas high scorers actively seek new solutions to problems and express themselves.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <h5>Unconventionality</h5>
            </div>
            <div class="col-xs-1 underline text-center">
                <h5>{{ $scores['Personality']['Unconventionality'] }}</h5>
            </div>
            <div class="col-xs-8">
                <h6>Tendency to accept the unusual and different. Low scorers avoid things that are out of the ordinary; high scorers are open to out-of-the-ordinary ideas.</h6>
            </div>
        </div>
    </div>
</div>
<!--Page 12-->
<div class="page-container" id="12">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 12</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h1>Personality Fit Evaluation</h1>
                <h4>for {{ $user->name }}</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p>Given these AOE-P results, in conjunction with job and industry profiles for {{ $job->name }}s, the potential fit for {{ $user->name }} is:</p>
                <br><br>
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                    @if ($scores['Personality']['Division'] == 5)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/low.png">
                    @elseif ($scores['Personality']['Division'] == 4)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-low.png">
                    @elseif ($scores['Personality']['Division'] == 3)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid.png">
                    @elseif ($scores['Personality']['Division'] == 2)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-high.png">
                    @elseif ($scores['Personality']['Division'] == 1)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/high.png">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>
</body>
</html>
