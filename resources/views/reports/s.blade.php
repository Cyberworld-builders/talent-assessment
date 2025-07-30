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
            /*background-image:none;*/
            /*background-color:#ddd;*/
            background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
            background-size: 100% 100%;
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
                <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
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
                <p>This report provides a recommendation for {{ $user->name }}, who applied for a {{ $job->name }} position. This report covers {{ $job->name }} safety, and provides evidence for the candidate's likelihood of success in {{ $job->name }} related positions.</p>
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

<!--S-->
<div class="page-container" id="13">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 13</small>
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
<!--S-->
<div class="page-container" id="14">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 14</small>
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
<!--S-->
<div class="page-container" id="15">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 15</small>
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