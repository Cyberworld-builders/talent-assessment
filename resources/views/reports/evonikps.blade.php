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
            /*background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;*/
            /*background-size: 100% auto;*/
        }
        #chart2 {
            width:600px;
            margin:0 auto
        }
        .page-container {
            background-color: white;
            height: 1100px;
            width: 850px;
            padding: 45px 45px;
            page-break-after: always;
            margin: 0 auto;
            margin-bottom: 15px;
        }
        .img-container-1, .img-container-2 {position: absolute; height:997px; width:760px;}
        .img-container-1 small, .img-container-2 small {top:0; right:15px; position:absolute;}
        .img-container-1 img {position:absolute; bottom:0px; right:15px; width:100px;}
        .img-container-2 img {position:absolute; bottom:0px; left:15px; width:100px;}
        h1 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:64px; color:#02244a; line-height:64px; margin-top:0px;}
        h2 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:32px; color:#02244a; line-height:45px; margin-top:0px;}
        h3 {-webkit-print-color-adjust: exact; font-family:'Bebas Neue'; font-size:23px; color:#02244a; line-height:32px; margin-top:0px;}
        .cover-for h3 {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:23px;}
        h4 {-webkit-print-color-adjust: exact; font-family:'Didot Italic'; color:#02244a; font-size:32px; line-height:32px;}
        h5 {font-family:'Avenir Next LT Pro Medium'; font-size:1.616em; line-height:1.616em; color:#02244a;}
        .yellow-block {
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
        h6 {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:23px; margin-top:0px;}
        p, ul li {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:26px; margin-bottom:16px; list-style-type:square;}
        .red {color:#E32731;} .yellow {color:#E7B428;} .green {color:#30BD21;}
        .text-justify {text-align:justify;}
        .border-bottom {border-top:1px solid rgba(0,0,0,0.1); padding-top:10px;}
        .underline {border-bottom:1px solid black;}
        small {font-family:'Avenir Next LT Pro'; font-size:13px; line-height:18px;}
        .leftside {left:15px!important; right:inherit; position:absolute;}
        .disclaimer {padding-top:30px;}
        .cover-logo {padding:90px 0px 0px; margin:0 auto;}
        .cover-for {padding-top:90px; padding-bottom:60px;}
        .white {color:white;}
        #chart {width:600px; margin:0 auto;}
        .chart {margin-left:-45px;}
        h1, h2, h3, h4, h5, a, a:hover {
            color:#970E7E;
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
                        {{ $scores['Personality']['Honesty-Humility'] }},
                        {{ $scores['Personality']['Emotional Control'] }},
                        {{ $scores['Personality']['Extraversion'] }},
                        {{ $scores['Personality']['Agreeableness'] }},
                        {{ $scores['Personality']['Conscientiousness'] }},
                        {{ $scores['Personality']['Openness'] }}
                    ]
                }]
            });
            $('#chart4').highcharts({
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
                    categories: ['Confidence', 'Focus', 'Control', 'Safety Knowledge', 'Safety Motivation', 'Risk Avoidance'],
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
                        {{ $scores['Safety']['Confidence'] }},
                        {{ $scores['Safety']['Focus'] }},
                        {{ $scores['Safety']['Control'] }},
                        {{ $scores['Safety']['Safety Knowledge'] }},
                        {{ $scores['Safety']['Safety Motivation'] }},
                        {{ $scores['Safety']['Risk-Taking'] }}
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
                {{--<p>This report provides a recommendation for {{ $user->name }}, who applied for a {{ $job->name }} position. This report covers {{ $job->name }} aptitude, cognitive ability, and personality. This report provides evidence for the candidate's likelihood of success in {{ $job->name }} related positions.</p>--}}
                <p>This report provides a recommendation for {{ $user->name }}, who applied for a {{ $job->name }} position. This report covers {{ $job->name }} personality and safety. This report provides evidence for the candidate's likelihood of success in {{ $job->name }} related positions.</p>
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
{{--<div class="page-container" id="2">--}}
{{--<div class="img-container-1">--}}
{{--<small>Page 2</small>--}}
{{--</div>--}}
{{--<div class="container">--}}
{{--<!--Headline-->--}}
{{--<div class="row text-center">--}}
{{--<div class="col-sm-12">--}}
{{--<br><br>--}}
{{--<h1>Table of Contents</h1>--}}
{{--<br><br>--}}
{{--</div>--}}
{{--</div>--}}
{{--<!--Table of Contents-->--}}
{{--<div class="row">--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>DCS Aptitude Report</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#3"><small>Page 3</small></a>--}}
{{--</div>--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>DCS Recommendation</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#4"><small>Page 4</small></a>--}}
{{--</div>--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>Cognitive Ability Report</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#5"><small>Page 5</small></a>--}}
{{--</div>--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>Cognitive Ability Evaluation</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#6"><small>Page 6</small></a>--}}
{{--</div>--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>Personality Report</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#7"><small>Page 8</small></a>--}}
{{--</div>--}}
{{--<div class="col-xs-9 col-sm-10 text-left">--}}
{{--<h3>Personality Fit Recommendation</h3>--}}
{{--</div>--}}
{{--<div class="col-xs-3 col-sm-2 text-right">--}}
{{--<a href="#12"><small>Page 12</small></a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
<!--Page 3-->

<!--Page 7-->
<div class="page-container" id="7">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 2</small>
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
        <small>Page 3</small>
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
        <small>Page 4</small>
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
        <small>Page 5</small>
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
        <small>Page 6</small>
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
        <small>Page 7</small>
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

<!--Page 13-->
<div class="page-container" id="13">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 8</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-S.png">
                    </div>
                </div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>AOE Scientists have been studying the psychological drivers of occupational safety for over two decades and have published their findings in leading scholarly journals. The AOE-S is based upon this foundational work resulting in six primary drivers of workplace safety.</p>
                <p>The AOE-S is a very good indicator of workplace safety behavior – indicating who is more likely to be involved in an accident at work. Generally speaking, those that possess appropriate job knowledge, motivation, confidence, focus, internal locos of control, and are risk averse tend to behave significantly more safe than those lacking in one or more of these aspects. Such a profile has been quantitatively linked to higher safety behavior and thereby reduced accident involvement.</p>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div id="invisible-0" class="col-xs-4">
                <h3>Safety Dimension</h3>
            </div>
            <div id="invisible-0" class="col-xs-8">
                <h3>Dimension Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Focus</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Mindful focus and awareness on current activities and avoiding irrelevant/distracting information.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Control</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to see one's self as in control rather than external forces such as fate or luck.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Confidence</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Possessing the confidence to safely and accurately complete work tasks, even in the face of competing demands or unexpected situations.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Motivation</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Motivated to perform work tasks accurately and safely as well as motivated to help others do the same.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Knowledge</strong></p>
            </div>
            <div class="col-xs-8">
                <p>A willingness and ability to learn and follow safety procedures.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Risk Avoidance</strong></p>
            </div>
            <div class="col-xs-8">
                <p>A tendency to avoid risks - risks that could speed up work and/or risks that are thrilling/exciting, but can also jeopardize safety.</p>
            </div>
        </div>
    </div>
</div>
<!--Page 14-->
<div class="page-container" id="14">
    <div class="img-container-2">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 9</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-S.png">
                    </div>
                </div>
                <h1>Scoring and Importance</h1>
                <h5>of the AOE-S for {{ $job->name }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>All six factors in the AOE-S are important for improving safety and reducing accidents. Below we present the overall scores for {{ $user->name }}. Next, using our predictive talent analytics algorithms, we provide a Safety Evaluation Recommendation for this job:</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h5>Graphical Representation of AOE-S Scores for {{ $user->name }}</h5>
                <div id="chart4"></div>
            </div>
        </div>
    </div>
</div>
<!--Page 15-->
<div class="page-container" id="15">
    <div class="img-container-1">
        <img src="https://upload.wikimedia.org/wikipedia/en/thumb/9/91/Evonik_Industries_Logo.svg/1280px-Evonik_Industries_Logo.svg.png">
        <small>Page 10</small>
    </div>
    <div class="container">
        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h1>Safety Evaluation</h1>
                <h4>for {{ $user->name }}</h4>
                <br><br>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-sm-12">
                <p>Given these AOE-S results, in conjunction with job and industry profiles for {{ $job->name }}, the safety potential for {{ $user->name }} is:</p>
                <br><br>
                <div class="col-xs-2"></div>
                {{--<div class="col-xs-8">--}}
                {{--<img class="img-responsive" src="/wp/wp-content/themes/aoe/images/gauge.png">--}}
                {{--</div>--}}
                <div class="col-xs-8">
                    @if ($scores['Safety']['Division'] == 5)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/low.png">
                    @elseif ($scores['Safety']['Division'] == 4)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-low.png">
                    @elseif ($scores['Safety']['Division'] == 3)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid.png">
                    @elseif ($scores['Safety']['Division'] == 2)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-high.png">
                    @elseif ($scores['Safety']['Division'] == 1)
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