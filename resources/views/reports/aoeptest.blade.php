<html>
<head>
    <meta name="viewport" content="width=device-width">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/wp/wp-content/themes/aoe/js/highcharts.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="all" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    {{--<link rel="stylesheet" type="text/css" media="all" href="/wp-content/themes/aoe/reports/reports.css">--}}
    <style type="text/css">
        body {
            -webkit-print-color-adjust: exact;
            background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
            background-size: 100% 100%;
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

        @media screen and (max-width:480px) {.cell-center {text-align:center;} .underline {border-bottom:0px;} #invisible-12 {width:100%;} #invisible-4 {width:16.666%;} #invisible-0 {display:none;} .col-xs-8 {width:100%;} .col-xs-4 {width:66.666%;}}

        @font-face {
            font-family: 'Bebas Neue';
            src: url('/wp/wp-content/themes/aoe/fonts/bebasneue_regular-webfont.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/bebasneue_regular-webfont.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/bebasneue_regular-webfont.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/bebasneue_regular-webfont.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/bebasneue_regular-webfont.svg#Bebas Neue') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Didot';
            src: url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Roman.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Roman.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Roman.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Roman.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Roman.svg#Didot') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Didot Italic';
            src: url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Italic.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Italic.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Italic.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Italic.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/DidotLTStd-Italic.svg#Didot Italic') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Avenir Next LT Pro';
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Regular.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Regular.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Regular.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Regular.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Regular.svg#Avenir Next LT Pro') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Avenir Next LT Pro Medium';
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Medium.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Medium.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Medium.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Medium.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Medium.svg#Avenir Next LT Pro Medium') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Avenir Next LT Pro Bold';
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Bold.eot');
            src: url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Bold.eot?#iefix') format('embedded-opentype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Bold.woff') format('woff'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Bold.ttf') format('truetype'),
            url('/wp/wp-content/themes/aoe/fonts/AvenirNextLTPro-Bold.svg#Avenir Next LT Pro Bold') format('svg');
            font-weight: normal;
            font-style: normal;
        }
        @media print {
            header, footer {display:none;}
            @page {margin: 0.25mm;  /* this affects the margin in the printer settings */}
            .page-container:nth-child(odd) {-webkit-print-color-adjust: exact; background-image:url('/wp/wp-content/themes/aoe/images/aoe-science_logo.png'); background-repeat:no-repeat; background-position:96.25% 98.25%; background-size:96px; background-origin:content-box;}
            .page-container:nth-child(even) {-webkit-print-color-adjust: exact; background-image:url('/wp/wp-content/themes/aoe/images/aoe-science_logo.png'); background-repeat:no-repeat; background-position:4.25% 98.25%; background-size:96px; background-origin:content-box;}
            .page-container:nth-child(1) {-webkit-print-color-adjust: exact; background-image:none;}
            .page-container {margin:0px; padding:0px; height:auto; width:100%;}
            .cover-logo {padding-bottom:30px; padding-top:0px;}
            .cover-for {padding-top:0px; padding-bottom:0px;}
            .white {display:none;}
            .disclaimer .img-responsive {width:100px;}
            p {font-family:'Avenir Next LT Pro'; font-size:16px; line-height:23px; margin-bottom:16px;}
            h1, h2, h3, h4 {-webkit-print-color-adjust: exact;}
        }

        @page{
            margin-left: 0.635cm;
            margin-right: 0.635cm;
            margin-top: 0.635cm;
            margin-bottom: 0.635cm;
        }
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
                <p>
                    This report provides a fit profile for {{ $user->name }}, who applied for the position in {{ $user->job }}.
                    It covers personality which focuses on typical, everyday behaviors one can expect to see from {{ $user->name }}.
                    These behaviors provide evidence for the candidate's likelihood of success in {{ $user->job }} related positions.</p>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>

        <!--Disclaimer-->
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6>
                    The AOE Group, LLC offers the most scientifically valid candidate assessments.
                    AOE uses the latest Talent Evidence from the scientific literature, their own research, and
                    the needs of organizations to arrive at Evidence-Based Talent Solutions.
                </h6>
            </div>
            <div class="col-xs-2 col-sm-2 text-right">
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
                <h3>Personality Report</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#5"><small>Page 3</small></a>
            </div>
            <div class="col-xs-9 col-sm-10 text-left">
                <h3>Personality Fit Indicator</h3>
            </div>
            <div class="col-xs-3 col-sm-2 text-right">
                <a href="#10"><small>Page 8</small></a>
            </div>
        </div>
    </div>
</div>

<!--Page 5-->
<div class="page-container" id="3">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 3</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>The AOE-P is based on the HEXACO model of personality, which is the latest scientifically
                    valid model of individual personality. The HEXACO model is valid across many different
                    cultures and incorporates the most up-to-date and accurate framework for assessing personality.</p>
                <p>Personality is a very good indicator of what people will do on a typical day of work. The
                    AOE-P is the first HEXACO-based assessment for application to organizations. The theoretical
                    and empirical evidence shows that there are six major dimensions to personality. These are described as:</p>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div id="invisible-0" class="col-xs-4">
                <h3>Personality Dimension</h3>
            </div>
            <div id="invisible-0" class="col-xs-8">
                <h3>Personality Dimension Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Honesty-Humility</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on fairness, sincerity, modesty, and greed avoidance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Emotional Control</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on controlling your emotions, withstanding failures, setbacks and stresses, and maintaining and/or quickly regaining your composure.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Extraversion</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on social boldness, sociability, liveliness, and social self-esteem.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Agreeableness</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on flexibility, gentleness, forgiveness, and patience.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Conscientiousness</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on achievement, organization, perfectionism, and prudence.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Openness</strong></p>
            </div>
            <div class="col-xs-8">
                <p>Tendency to focus on inquisitiveness, aesthetic apreciation, unconventionality, and creativity.</p>
            </div>
        </div>
    </div>
</div>

<!--Page 6-->
<div class="page-container" id="4">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 4</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-P-01.png">
                    </div>
                </div>
                <h1>Scoring and Importance</h1>
                <h5>of the AOE-P for Accounting/Financing</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <p>AOE's vast experience in research, predictive analytics, and content validation reveals that for
                    Accounting and Finance related positions all six dimensions of AOE-P are desired.
                    Of these, Conscientiousness is a strongly desired characteristic that connects to performance.
                    Conscientiousness encompasses prudence, organization, detail orientation, and achievement.
                    Honesty and Humility is also strongly desired - encompassing fairness, sincerity, modesty,
                    and greed avoidance. Although still important, some characteristics, such as Openness and
                    Agreeableness, may not factor as heavily for Accounting and Finance related positions.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="chart2"></div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Personality Factor Scores</h5>
                <p>For each of the major 6 dimensions there are 4 sub-factors totalling 24 unique indicators of
                    personality. The sub-factor dimensions are described below with the candidate's score for each.
                    Of particular importance for {{ $user->job }} related positions, factors of Conscientiousness
                    and Honesty-Humility are weighted heavily in the AOE-P evaluation. AOE's proprietary
                    algorithms take such differences into account to arrive at an overall Job-Fit Recommendation,
                    which is presented below after the sub-factor scores.</p>
            </div>
        </div>
    </div>
</div>

<!--Page 7-->
<div class="page-container" id="5">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 5</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Honesty-Humility</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Sincerity</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Sincerity'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be genuine in interpersonal relations. Low scorers use flattery and are often seen as 'fake', whereas high scorers are viewed as being sincere and do not manipulate others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Fairness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Fairness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to avoid unfair action, fraud, and corruption. Low scorers might cheat or steal; high scorers are unlikely to take advantage of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Greed Avoidance</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Greed Avoidance'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be uninterested in material goods or social status. Low scorers want to enjoy and to display wealth and privilege; high scorers are uninterested in material goods or social status.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Modesty</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Modesty'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be modest and unassuming. Low scorers consider themselves as superior and entitled; high scorers see themselves as ordinary people.</h6>
            </div>
        </div>

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Emotional Control</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Fearlessness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Fearlessness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be brave/fearless. Low scorers are extremely fearful for physical harm; high scorers are relatively tough, brave, and not overly sensitive to physical injury.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Composure</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Composure'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to remain calm and collected at work. Low scorers worry excessively, even with minor issues; high scorers remain calm, even about major issues.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Independence</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Independence'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to work independently without much emotional support from others. Low scorers want encouragement and/or comfort from others; high scorers are self-assured and able to effectively deal with problems.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Stoical</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Stoical'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to accept what happens without showing strong emotion. Low scorers show strong emotions and have strong emotional attachments; high scorers show little emotion and have weak emotional attachments.</h6>
            </div>
        </div>
    </div>
</div>

<!--Page 8-->
<div class="page-container" id="6">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 6</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Extraversion</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Social Self-Esteem</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Social Self-Esteem'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to have positive self-regard, particularly at work. High scorers have self-respect and see themselves as likeable; low scorers tend to feel worthless and unpopular.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Social Boldness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Social Boldness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be comfortable and confident in work situations. Low scorers are typically shy or awkward, particularly in leadership positions or in large settings; high scorers are comfortable leading groups and communicating with a variety of people.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Sociability</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Sociability'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to enjoy social situations and interacting with a variety of individuals. Low scorers prefer solitary activities and work tasks; high scorers enjoy talking, visiting, and interacting with others at work.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Liveliness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Liveliness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be optimistic, enthusiastic, and full of energy. Low scorers are generally not overly cheerful or dynamic; high scorers are generally enthusiastic and in high spirits.</h6>
            </div>
        </div>

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Agreeableness</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Forgiveness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Forgiveness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to forgive and even trustful to those who may have caused harm. Low scorers might "hold a grudge" against those that have done one wrong; high scorers can forgive and are willing to work towards re-establishing friendly relations.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Gentleness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Gentleness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be mild mannered and gentle in dealings with others. Low scorers are generally critical of others; high scorers tend not to be judgemental of others.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Flexibility</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Flexibility'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to compromise and cooperate with others. Low scorers are viewed as stubborn and likely argumentative; high scorers are accommodating to suggestions and generally flexible.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Patience</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Patience'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be patient and remain calm. Low scorers tend to get angry or upset easily; high scorers generally are more tolerant before possibly getting angry or upset.</h6>
            </div>
        </div>
    </div>
</div>

<!--Page 9-->
<div class="page-container" id="7">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small class="leftside">Scale: 1=Low 5=High</small>
        <small>Page 7</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Conscientiousness</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Organization</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Organization'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to seek order and organize one's surroundings. Low scorers are generally sloppy and hap-hazard; high scorers are generally well-organized and prefer a structured approach to tasks.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Achievement</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Achievement'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to work hard. Low scorers lack self-discipline and are not strongly motivated to achieve; high scorers are strongly motivated to achieve due to a strong "work ethic."</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Prudence</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Prudence'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be detailed oriented. Low scorers are tolerant of errors in their work; high scorers check carefully for mistakes and potential improvements.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Patience</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Patience'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to carefully think things through and avoid impulses. Low scorers follow impulses and do not consider consequences; high scorers consider multiple options and are generally careful and self-controlled.</h6>
            </div>
        </div>

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <br><br>
                <h2>Primary Dimension: Openness</h2>
            </div>
        </div>
        <div id="invisible-0" class="row">
            <div class="col-xs-4">
                <p>Factor</p>
            </div>
            <div class="col-xs-1 text-center">
                <p>Score</p>
            </div>
            <div class="col-xs-7">
                <p>Description</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Aesthetic Appreciation</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Aesthetic Appreciation'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to enjoy and see beauty in art, physical surroundings, and in nature. Low scorers don't care for art, aesthetics, or natural wonders; high scorers have a deep appreciation for a variety of art forms (e.g., nature, physical space).</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Inquisitiveness</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Inquisitiveness'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendecy to be inquisitive. Low scorers are generally not curious; high scorers are curious and prefer to know how things work or came to be.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Creativity</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Creativity'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to be innovative and experimental. Low scorers have little inclination for original thought, whereas high scorers actively seek new solutions to problems and express themselves.</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <h5>Unconventionality</h5>
            </div>
            <div id="invisible-4" class="col-xs-1 underline text-center">
                <h5>{{ $scores['Unconventionality'] }}</h5>
            </div>
            <div id="invisible-12" class="col-xs-7">
                <h6>Tendency to accept the unusual and different. Low scorers avoid things that are out of the ordinary; high scorers are open to out-of-the-ordinary ideas.</h6>
            </div>
        </div>
    </div>
</div>

<!--Page 10-->
<div class="page-container" id="8">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page 8</small>
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
            <div class="col-sm-12 cell-center">
                <p>Given these AOE-P results, in conjunction with job and industry profiles for this job function, the potential fit for {{ $user->name }} is:</p>
                <br><br>
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                    @if ($division == 1)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/low.png">
                    @elseif ($division == 2)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-low.png">
                    @elseif ($division == 3)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid.png">
                    @elseif ($division == 4)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/mid-to-high.png">
                    @elseif ($division == 5)
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/high.png">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>
<script type="text/javascript">
    $(function () {

        $('#chart2').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:250,
                spacingLeft: 15
            },
            title: {
                text: null
            },
            xAxis: {
                categories: ['Honesty-Humility', 'Emotional Control', 'Extraversion', 'Agreeableness', 'Conscientiousness', 'Openness'],
                labels: {style: {fontSize:'16px'}},
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 1,
                max: 5,
                text:null,
                tickInterval: 0.5,
                labels: {
                    overflow: 'justify',
                    style: {fontSize:'16px'}
                },
                title: {
                    text: null
                }
            },
            tooltip: {enabled: false},
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        x: -50,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'16px', align: 'center'}
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
                color: '#9FAAC5',
                data: [
                    {{ $scores['Honesty-Humility'] }},
                    {{ $scores['Emotional Control'] }},
                    {{ $scores['Extraversion'] }},
                    {{ $scores['Agreeableness'] }},
                    {{ $scores['Conscientiousness'] }},
                    {{ $scores['Openness'] }}
                ]
//                data: [4.12, 3.94, 4.34, 3.86, 4.19, 4.12]
            }]
        });
    });
</script>
</body>
</html>