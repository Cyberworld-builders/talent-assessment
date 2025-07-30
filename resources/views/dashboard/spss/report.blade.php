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
        .cover-for {padding-top:90px; padding-bottom:120px;}
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
            $('#chart2').highcharts({
                chart: {
                    type: 'areaspline',
                    height: 430,
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
                    text: 'Confidence: {{ $confidence }}',
                    align: 'left',
                    verticalAlign: 'top',
                    style: {fontSize: '16px', lineHeight:'23px'},
                    floating: true,
                    y:7
                },
                subtitle: {
                    text: 'Rank: {{ strtoupper($rank) }}',
                    floating: true,
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        @if ($rank == 'a')
                        color: '#8dc63f',
                        @elseif ($rank == 'b')
                        color: '#b9c945',
                        @elseif ($rank == 'c')
                        color: '#ffba00',
                        @elseif ($rank == 'd')
                        color: '#d36e30',
                        @elseif ($rank == 'f')
                        color: '#cc3f44',
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
                        text: 'Distribution',
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
                        value: {{ $value }},
                        width: 1,
                        zIndex: 5,
                        label: {
                            text: 'Rank',
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
                        [1,10],
                        [2,40],
                        [3,100],
                        [4,40],
                        [5,10]
                    ],
                    zoneAxis: 'x',
                    fillOpacity:1,
                    zones: [
                        {value: 1.5, color: '#cc3f44'},
                        {value: 2.5, color: '#d36e30'},
                        {value: 3.5, color: '#ffba00'},
                        {value: 4.5, color: '#b9c945'},
                        {value: 4.5, color: '#8dc63f'},
                        {value: 6, color: '#8dc63f'},
                    ],
                },{
                    name: 'Caution',
                    color: '#E7B428',
                },{
                    name: 'Pursue',
                    color: '#30BD21',
                }]
            });
        });
    </script>
</head>
<body>

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
                <h3>Candidate Profile for:</h3>
                <h4>{{ $user->name }}</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>

        {{-- Overview --}}
        <div class="row">
            <div class="col-sm-10">
                <h5>Overview</h5>
                <p>This report has been generated via the Decision Tree matrix for the job {{ $job->name }}. It covers {{ $user->name }}'s viability for the position of {{ $job->name }} using the following factors: {{ implode(', ', $factors) }}.</p>
            </div>
        </div>
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
                            <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                    </div>
                </div>
                <h1>Scores</h1>
            </div>
            <div class="col-sm-12 text-justify">
                {{--<h5>Description</h5>--}}
            </div>
        </div>
        <br/>

        <p>Here are the raw scores for {{ $user->name }} that were used as factors in the Decision Tree matrix.</p>

        @foreach ($scores as $category => $score)
            <div class="row">
                <div class="col-xs-3">
                    <h5>{{ $category }}</h5>
                </div>
                <div class="col-xs-1 underline text-center">
                    <h5>{{ $score }}</h5>
                </div>
            </div>
        @endforeach

    </div>
</div>

{{-- Page 3 --}}
<div class="page-container" id="3">
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                    </div>
                </div>
                <h1>Distribution</h1>
            </div>
            <div class="col-sm-12 text-justify">
                {{--<h5>Description</h5>--}}
            </div>
        </div>
        <br/>

        <p>This is the distribution for {{ $user->name }} based on his/her performance.</p>

        <!--Chart-->
        <div class="row">
            <div class="chart">
                <div id="chart2"></div>
            </div>
        </div>

    </div>
</div>

<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>
</body>
</html>