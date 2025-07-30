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
            background:url('/wp/wp-content/themes/aoe/images/aoe-group_home-banner.jpg') fixed no-repeat;
            background-size: 100% 100%;
        }
        /*ol li, ul li, p {*/
            /*font-size: 14px;*/
            /*line-height: 20px;*/
            /*margin-bottom: 14px;*/
            /*font-family: Avenir Next LT Pro;*/
        /*}*/
        h5 {
            font-size: 17px;
        }
        ol li hr {
            margin-left: -18px;
        }
        .disclaimer {
            padding-top: 20px;
        }
        .disclaimer h6 {
            font-family: 'Avenir Next LT Pro';
            font-size: 14px;
            font-weight: normal;
            line-height: 20px;
            margin-top: 0px;
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
                <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
            </div>
        </div>

        <!--Candidate-->
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <h3>Leader Involvement Profile for:</h3>
                <h4>{{ $user->name }}</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>

        <!--Overview-->
        <div class="row">
            <div class="col-sm-12">
                <h5>Overview</h5>
                <p>AOE-L measures specific leadership behaviors which are desirable for leading change and inspiring employees. This report provides a leadership profile with actionable feedback for {{ $user->name }}. The purpose of this report is to provide actionable feedback to achieve increased team effectiveness and improve management skills which positively impacts business results.</p>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>

        <!--Disclaimer-->
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6>
                    AOE Science offers the most scientifically valid candidate assessments. AOE uses the latest Talent Evidence from the scientific literature, their own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions.
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
    <div class="img-container-2">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
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
            <div class="col-xs-8 col-sm-10 text-left">
                <h3>Introduction</h3>
            </div>
            <div class="col-xs-4 col-sm-2 text-right">
                <a href="#3"><small>Page 3</small></a>
            </div>
            <div class="col-xs-8 col-sm-10 text-left">
                <h3>Overall Scores</h3>
            </div>
            <div class="col-xs-4 col-sm-2 text-right">
                <a href="#5"><small>Page 5</small></a>
            </div>
            <div class="col-xs-8 col-sm-10 text-left">
                <h3>Dimension Scores With Feedback</h3>
            </div>
            <div class="col-xs-4 col-sm-2 text-right">
                <a href="#6"><small>Page 6</small></a>
            </div>
        </div>
    </div>
</div>

<!--Page 3-->
<div class="page-container" id="3">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                <p>The present report provides detailed feedback on the extent to which {{ $user->name }} displays specific leader behaviors. The leader behaviors can be directed toward individuals, a work unit, team, or department. This report groups specific leadership qualities into dimensions with corresponding sub-dimensions.</p>
                <h5>Understanding Your Report</h5>
                <p>Leader involvement refers to how well leaders in your organization engage and empower their employees to take control over their own work. AOE-L is designed to provide a detailed summary of how involved leaders are in engaging their direct reports. High Involvement Leadership (HIL) exists when employees throughout an organization (1) have power to act and make decisions, (2) have the information, (3) and the knowledge, (4) along with the healthy relationship needed to use their power effectively, (5) and are rewarded for doing so.</p>
                <p>It is critical that all 5 dimensions are present at the same time. If one dimension is missing, involvement will be low. Your leadership data is presented along with the overall average leadership data for your entire organization. This allows you to see how well you are doing on Involvement compared to all other leaders at your company.</p>
                <br>
                <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/HIL-flow.png">
            </div>
        </div>
    </div>
</div>

<!--Page 4-->
<div class="page-container" id="4">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-2">
                <h3>Dimension</h3>
            </div>
            <div class="col-xs-6">
                <h3>Sub-Dimension</h3>
            </div>
            <div class="col-xs-4">
                <h3>Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-2">
                <p>Power</p>
            </div>
            <div class="col-xs-6">
                <p>- Communication Empowerment<br>
                    - Autonomy</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Empowers employees to make decisions about how to carry out their work; decision-making latitude and autonomy.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-2">
                <p>Information</p>
            </div>
            <div class="col-xs-6">
                <p>- Information (General)<br>
                    - Communication with Upper Management<br>
                    - Feedback</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Providing accurate and necessary information to employees which better enables them to do their jobs effectively.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-2">
                <p>Rewards</p>
            </div>
            <div class="col-xs-6">
            </div>
            <div class="col-xs-4">
                <p>Recognition and rewards for strong performance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-2">
                <p>Knowledge</p>
            </div>
            <div class="col-xs-6">
                <p>- Knowledge Empowerment<br>
                    - Mentoring<br>
                    - Knowledge Acquisition</p>
            </div>
            <div class="col-xs-4">
                <p>Providing employees with the relevant performance feedback and training.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-2">
                <p>Relationships</p>
            </div>
            <div class="col-xs-6">
                <p>- Conflict Management<br>
                    - Teamwork<br>
                    - Communication<br>
                    - Respect</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Builds relationships and maintains harmony in the workgroup.</p>
            </div>
        </div>

    </div>
</div>

<!--Page 5-->
<div class="page-container" id="5">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
                <h1>Overall Scores</h1>
                <h5>for {{ $user->name }}</h5><br>
                <div id="chart3"></div>
                <h5 class="text-left">Interpreting Your Scores</h5>
                <p class="text-justify">A key aspect of HIL is to get you engaged with engaging your employees. While reflecting on this statement and your HIL feedback report, think about what you can do to better involve your employees. We believe the recommendations below will kick-start improvement for your work group by helping get all of your team, including yourself, more involved in your work. These are merely our suggestions.  You do not necessarily need to use any of these suggestions but instead can reflect on what could work for your specific situation. For example, ask yourself: what are three steps you can take right now to improve upon your HIL?</p>
            </div>
        </div>
    </div>
</div>

<!--Page 6-->
<div class="page-container" id="6">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <h1>Power</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="chart11"></div>
                <div id="chart8"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($strengths['Power']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($strengths['Power'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!--Page 7-->
@if (! empty($opportunities['Power']))
    <div class="page-container" id="7">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Power</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for power.</p>
                </div>
            </div>
            @foreach ($opportunities['Power'] as $i => $opportunity)
				<?php if ($i >= 2) break; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 8-->
<div class="page-container" id="8">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <h1>Information</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="chart12"></div>
                <div id="chart5"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($strengths['Information']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($strengths['Information'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!--Page 9-->
@if (! empty($opportunities['Information']))
    <div class="page-container" id="9">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Information</h1>
                    <h5>Overall Opportunities and Recommended Action Steps</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for power.</p>
                </div>
            </div>
            @foreach ($opportunities['Information'] as $i => $opportunity)
				<?php if ($i >= 2) break; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 10-->
@if (count($opportunities['Information']) > 2)
    <div class="page-container" id="10">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Information</h1>
                    <h5>Overall Opportunities and Recommended Action Steps</h5>
                </div>
            </div>
            @foreach ($opportunities['Information'] as $i => $opportunity)
				<?php if ($i < 2) continue; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 11-->
<div class="page-container" id="11">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row">
            <div class="col-sm-12 text-center">
                <h1>Reward</h1>
                <h5>Score Interpretation & Feedback</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-justify">
                <div id="chart7"></div>
                @if (! empty($strengths['Rewards']))
                    <h5>Overall Strengths</h5>
                    @foreach ($strengths['Rewards'] as $strength)
                        {!! $strength !!}
                    @endforeach
                @endif
            </div>

            @if (! empty($opportunities['Rewards']))
                <div class="col-xs-12 text-justify">
                    <h5>Overall Opportunities</h5>
                    {!! $opportunities['Rewards'][0]['Description'] !!}
                </div>
                <div class="col-xs-12 text-justify">
                    <h5>Potential Action Steps:</h5>
                    <ol>
                        {!! $opportunities['Rewards'][0]['Action Steps'][0] !!}
                        {!! $opportunities['Rewards'][0]['Action Steps'][1] !!}
                        <li><hr>Come up with your own action steps:</li>
                    </ol>
                </div>
            @endif

        </div>
    </div>
</div>

<!--Page 12-->
<div class="page-container" id="12">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <div class="col-sm-12 ">
                <h1>Knowledge</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="chart13"></div>
                <div id="chart6"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($strengths['Knowledge']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($strengths['Knowledge'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!--Page 13-->
@if (! empty($opportunities['Knowledge']))
    <div class="page-container" id="13">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Knowledge</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for power.</p>
                </div>
            </div>
            @foreach ($opportunities['Knowledge'] as $i => $opportunity)
				<?php if ($i >= 2) break; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 14-->
@if (count($opportunities['Knowledge']) > 2)
    <div class="page-container" id="14">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Knowledge</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            @foreach ($opportunities['Knowledge'] as $i => $opportunity)
				<?php if ($i < 2) continue; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 15-->
<div class="page-container" id="15">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
		<?php $page++; ?>
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        <!--Heading-->
        <div class="row text-center">
            <h1>Relationships</h1>
            <h5>Score Interpretation & Feedback</h5>
            <div id="chart10"></div>
            <div id="chart9"></div>
        </div>
        <div class="col-sm-12 text-justify">
            @if (! empty($strengths['Relationships']))
                <h5>Overall Strengths</h5>
                <ul>
                    @foreach ($strengths['Relationships'] as $strength)
                        {!! $strength !!}
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<!--Page 16-->
@if (! empty($opportunities['Relationships']))
    <div class="page-container" id="16">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Relationships</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for power.</p>
                </div>
            </div>
            @foreach ($opportunities['Relationships'] as $i => $opportunity)
				<?php if ($i >= 2) break; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!--Page 17-->
@if (count($opportunities['Relationships']) > 2)
    <div class="page-container" id="17">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
			<?php $page++; ?>
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            <!--Heading-->
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Relationships</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            @foreach ($opportunities['Relationships'] as $i => $opportunity)
				<?php if ($i < 2) continue; ?>
                <div class="row">
                    <div class="col-xs-12 text-justify">
                        <h5>Opportunity #{{ ($i + 1) }}: {{ $opportunity['Title'] }}</h5>
                        {!! $opportunity['Description'] !!}
                        <h5>Potential Action Steps:</h5>
                        <ol>
                            {!! $opportunity['Action Steps'][0] !!}
                            {!! $opportunity['Action Steps'][1] !!}
                            <li><hr>Come up with your own action steps:</li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<p class="text-center white">Powered by <a href="http://aoescience.com/">AOE Science</a></p>

<script>
    $(function () {
        $('#chart10').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:190,
                spacingLeft: 15
            },
            title: {
                text: 'Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Overall Score'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Top Relationships Score',
                color: '#02244a',
//                data: [4.2]
                data: [{{ $scores['Top']['Relationships'] }}]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Relationships Score',
//                data: [4.8]
                data: [{{ $scores['Average']['Relationships'] }}]
            },
            @if ($scores['Scorers'] >= 1)
            {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Your Relationships Score',
                color: '#e77928',
                data: [{{ $scores['Main']['Relationships'] }}]
            }
            @endif
            ]
        });

        $('#chart9').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:400,
                spacingLeft: 35
            },
            title: {
                text: 'Sub-Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Conflict Management', 'Teamwork', 'Communication', 'Respect'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#02244a',
                name: 'Top Score',
//                data: [4.94, 4.34, 2.1, 3.3]
                data: [
                    {{ $scores['Top']['Conflict Management'] }},
                    {{ $scores['Top']['Teamwork'] }},
                    {{ $scores['Top']['Communication'] }},
                    {{ $scores['Top']['Respect'] }},
                ]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Score',
//                data: [2.1, 4.2, 1.3, 2.1]
            data: [
            {{ $scores['Average']['Conflict Management'] }},
            {{ $scores['Average']['Teamwork'] }},
            {{ $scores['Average']['Communication'] }},
            {{ $scores['Average']['Respect'] }},
        ]
    },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    color: '#e77928',
                    name: 'Your Score',
                    data: [
                        {{ $scores['Main']['Conflict Management'] }},
                        {{ $scores['Main']['Teamwork'] }},
                        {{ $scores['Main']['Communication'] }},
                        {{ $scores['Main']['Respect'] }},
                    ]
                }
                @endif
            ]
        });

        // Power Score
        $('#chart11').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:190,
                spacingLeft: 15
            },
            title: {
                text: 'Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Overall Score'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Top Power Score',
                color: '#02244a',
//                data: [4.2]
                data: [{{ $scores['Top']['Power'] }}]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Power Score',
//                data: [4.8]
                data: [{{ $scores['Average']['Power'] }}]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    name: 'Your Power Score',
                    color: '#e77928',
                    data: [{{ $scores['Main']['Power'] }}]
                }
                @endif
            ]
        });

        $('#chart8').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:260,
                spacingLeft: 35
            },
            title: {
                text: 'Sub-Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Communication Empowerment', 'Autonomy' ],
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
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#02244a',
                name: 'Top Score',
//                data: [4.94, 4.34]
                data: [
                    {{ $scores['Top']['Communication Empowerment'] }},
                    {{ $scores['Top']['Autonomy'] }}
                ]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Score',
//                data: [1.3, 2.1]
                data: [
                    {{ $scores['Average']['Communication Empowerment'] }},
                    {{ $scores['Average']['Autonomy'] }}
                ]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    color: '#e77928',
                    name: 'Your Score',
//                data: [2.3, 3.8]
                    data: [
                        {{ $scores['Main']['Communication Empowerment'] }},
                        {{ $scores['Main']['Autonomy'] }}
                    ]
                }
                @endif
            ]
        });

        $('#chart7').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:160,
                spacingLeft: 35
            },
            title: {
                text: null,
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Rewards'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#02244a',
                name: 'Top Reward Score',
//                data: [4.12]
                data: [{{ $scores['Top']['Rewards'] }}]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Reward Score',
//                data: [3.3]
                data: [{{ $scores['Average']['Rewards'] }}]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    color: '#e77928',
                    name: 'Your Reward Score',
//                data: [3.8]
                    data: [{{ $scores['Main']['Rewards'] }}]
                }
                @endif
            ]
        });

        $('#chart13').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:190,
                spacingLeft: 15
            },
            title: {
                text: 'Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Overall Score'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Top Knowledge Score',
                color: '#02244a',
//                data: [4.2]
                data: [{{ $scores['Top']['Knowledge'] }}]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Knowledge Score',
//                data: [4.8]
                data: [{{ $scores['Average']['Knowledge'] }}]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    name: 'Your Knowledge Score',
                    color: '#e77928',
//                data: [3.2]
                    data: [{{ $scores['Main']['Knowledge'] }}]
                }
                @endif
            ]
        });

        $('#chart6').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:340,
                spacingLeft:25
            },
            title: {
                text: 'Sub-Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Knowledge Empowerment', 'Mentoring', 'Knowledge Acquisition'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#02244a',
                name: 'Top Score',
//                data: [4.12, 3.94, 4.34]
                data: [
                    {{ $scores['Top']['Empowerment'] }},
                    {{ $scores['Top']['Mentoring'] }},
                    {{ $scores['Top']['Acquisition'] }}
                ]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Score',
//                data: [1.3, 3.3, 2.1]
                data: [
                    {{ $scores['Average']['Empowerment'] }},
                    {{ $scores['Average']['Mentoring'] }},
                    {{ $scores['Average']['Acquisition'] }}
                ]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    color: '#e77928',
                    name: 'Your Score',
                    data: [
                        {{ $scores['Main']['Empowerment'] }},
                        {{ $scores['Main']['Mentoring'] }},
                        {{ $scores['Main']['Acquisition'] }}
                    ]
                }
                @endif
            ]
        });

        $('#chart12').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:190,
                spacingLeft: 15
            },
            title: {
                text: 'Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Overall Score'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Top Information Score',
                color: '#02244a',
//                data: [4.2]
                data: [{{ $scores['Top']['Information'] }}]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Information Score',
//                data: [4.8]
                data: [{{ $scores['Average']['Information'] }}]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    name: 'Your Information Score',
                    color: '#e77928',
//                data: [3.2]
                    data: [{{ $scores['Main']['Information'] }}]
                }
                @endif
            ]
        });

        $('#chart5').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:350,
                spacingLeft: 35
            },
            title: {
                text: 'Sub-Dimension Score',
                style: {fontFamily: 'Bebas Neue', fontSize:'23px', align: 'center'}
            },
            xAxis: {
                categories: ['Information - General', 'Communication with Upper Mgmt.', 'Feedback'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#02244a',
                name: 'Top Score',
//                data: [4.12, 3.94, 4.34]
                data: [
                    {{ $scores['Top']['General'] }},
                    {{ $scores['Top']['Management Communication'] }},
                    {{ $scores['Top']['Feedback'] }}
                ]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Score',
//                data: [1.3, 3.3, 1.1]
                data: [
                    {{ $scores['Average']['General'] }},
                    {{ $scores['Average']['Management Communication'] }},
                    {{ $scores['Average']['Feedback'] }}
                ]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    color: '#e77928',
                    name: 'Your Score',
//                data: [2.3, 4.3, 3.8]
                    data: [
                        {{ $scores['Main']['General'] }},
                        {{ $scores['Main']['Management Communication'] }},
                        {{ $scores['Main']['Feedback'] }}
                    ]
                }
                @endif
            ]
        });

        $('#chart4').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:300,
                spacingLeft: 15
            },
            title: {
                text: null
            },
            xAxis: {
                categories: ['Sub-Dimension', 'Sub-dimension', 'Sub-dimension', 'Sub-Dimension'],
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
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Average [Dimension Goes Here] Score',
                color: '#e77928',
                data: [4.12, 3.94, 4.19, 4.12]
            }]
        });

        $('#chart3').highcharts({
            chart: {
                type: 'bar',
                style: {fontFamily: 'Avenir Next LT Pro', fontSize:'16px', align: 'center'},
                showInLegend: false,
                title: {text:null},
                height:450,
                spacingLeft: 15
            },
            title: {
                text: null
            },
            xAxis: {
                categories: ['Power', 'Information', 'Rewards', 'Knowledge', 'Relationships'],
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
                        x: -35,
                        style: {fontFamily: 'Avenir Next LT Pro', textShadow: false, color:'#ffffff', fontSize:'11px', align: 'center'}
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                name: 'Top Score',
                color: '#02244a',
//                data: [4.17, 3.12, 2.65, 2.33, 4.2]
                data: [
                    {{ $scores['Top']['Power'] }},
                    {{ $scores['Top']['Information'] }},
                    {{ $scores['Top']['Rewards'] }},
                    {{ $scores['Top']['Knowledge'] }},
                    {{ $scores['Top']['Relationships'] }},
                ]
            }, {
                enableMouseTracking: false,
                pointPadding: 0,
                groupPadding: 0.1,
                color: '#9FAAC5',
                name: 'Average Leader Score',
//                data: [1.52, 2.3, 4.3, 3.8, 4.8]
                data: [
                    {{ $scores['Average']['Power'] }},
                    {{ $scores['Average']['Information'] }},
                    {{ $scores['Average']['Rewards'] }},
                    {{ $scores['Average']['Knowledge'] }},
                    {{ $scores['Average']['Relationships'] }},
                ]
            },
                    @if ($scores['Scorers'] >= 1)
                {
                    enableMouseTracking: false,
                    pointPadding: 0,
                    groupPadding: 0.1,
                    name: 'Your Score',
                    color: '#e77928',
//                data: [2.07, 3.1, 2.5, 2.03, 3.2]
                    data: [
                        {{ $scores['Main']['Power'] }},
                        {{ $scores['Main']['Information'] }},
                        {{ $scores['Main']['Rewards'] }},
                        {{ $scores['Main']['Knowledge'] }},
                        {{ $scores['Main']['Relationships'] }},
                    ]
                }
                @endif
            ]
        });
    });
</script>
</body>
</html>