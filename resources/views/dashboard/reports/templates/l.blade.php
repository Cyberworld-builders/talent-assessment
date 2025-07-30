<?php
    global $page;
    global $i;

    if (isset($edit))
    {
		$scoringController = new \App\Http\Controllers\ScoringController();
		$scores = $scoringController->getScoreDefaults(get_global('leader'));
    }
?>

{{-- Report --}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        {{--<img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">--}}
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
                <h1>Leadership Report</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'The present report provides detailed feedback on the extent to which [name] displays specific leader behaviors. The leader behaviors can be directed toward individuals, a work unit, team, or department. This report groups specific leadership qualities into dimensions with corresponding sub-dimensions.'
                ])
				<?php $i++; ?>
                <h5>Understanding Your Report</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'Leader involvement refers to how well leaders in your organization engage and empower their employees to take control over their own work. AOE-L is designed to provide a detailed summary of how involved leaders are in engaging their direct reports. High Involvement Leadership (HIL) exists when employees throughout an organization (1) have power to act and make decisions, (2) have the information, (3) and the knowledge, (4) along with the healthy relationship needed to use their power effectively, (5) and are rewarded for doing so.

It is critical that all 5 dimensions are present at the same time. If one dimension is missing, involvement will be low. Your leadership data is presented along with the overall average leadership data for your entire organization. This allows you to see how well you are doing on Involvement compared to all other leaders at your company.'
                ])
				<?php $i++; ?>
                <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/HIL-flow.png">
            </div>
        </div>
    </div>
</div>

{{-- Dimensions --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
            </div>
        </div>
        <br/><br/>
        <div class="row">
            <div class="col-xs-3">
                <h3>Dimension</h3>
            </div>
            <div class="col-xs-5">
                <h3>Sub-Dimension</h3>
            </div>
            <div class="col-xs-4">
                <h3>Description</h3>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-3">
                <p>Power</p>
            </div>
            <div class="col-xs-5">
                <p>- Communication Empowerment<br>
                    - Autonomy</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Empowers others to make decisions about how to carry out their work; decision-making latitude and autonomy.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-3">
                <p>Information</p>
            </div>
            <div class="col-xs-5">
                <p>- Information (General)<br>
                    - Communication with Upper Management<br>
                    - Feedback</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Providing accurate and necessary information to others which better enables them to do their work.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-3">
                <p>Rewards</p>
            </div>
            <div class="col-xs-5">
            </div>
            <div class="col-xs-4">
                <p>Recognition and rewards for strong performance.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-3">
                <p>Knowledge</p>
            </div>
            <div class="col-xs-5">
                <p>- Knowledge Empowerment<br>
                    - Mentoring<br>
                    - Knowledge Acquisition</p>
            </div>
            <div class="col-xs-4">
                <p>Providing others with the relevant performance feedback and training.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-3">
                <p>Relationships</p>
            </div>
            <div class="col-xs-5">
                <p>- Conflict Management<br>
                    - Teamwork<br>
                    - Communication<br>
                    - Respect</p>
                </ul>
            </div>
            <div class="col-xs-4">
                <p>Builds relationships and maintains harmony in groups.</p>
            </div>
        </div>

    </div>
</div>

{{-- Overall Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        {{--<img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">--}}
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-L.png">
                    </div>
                </div>
                <h1>Overall Scores</h1>
                <h5>for @include('dashboard.reports.partials._name')</h5><br>
                <div id="leader-chart"></div>
                <h5 class="text-left">Interpreting Your Scores</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'A key aspect of HIL is to get you engaged with engaging your employees. While reflecting on this statement and your HIL feedback report, think about what you can do to better involve your employees. We believe the recommendations below will kick-start improvement for your work group by helping get all of your team, including yourself, more involved in your work. These are merely our suggestions.  You do not necessarily need to use any of these suggestions but instead can reflect on what could work for your specific situation. For example, ask yourself: what are three steps you can take right now to improve upon your HIL?'
                ])
				<?php $i++; ?>
            </div>
        </div>
    </div>
</div>

{{-- Power Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <h1>Power</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="leader-chart-power"></div>
                <div id="leader-chart-power-subdimensions"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($scores['strengths']['power']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($scores['strengths']['power'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Power Opportunities --}}
@if (! empty($scores['opportunities']['power']))
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
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
            @foreach ($scores['opportunities']['power'] as $i => $opportunity)
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

{{-- Information Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <h1>Information</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="leader-chart-information"></div>
                <div id="leader-chart-information-subdimensions"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($scores['strengths']['information']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($scores['strengths']['information'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Information Opportunities --}}
@if (! empty($scores['opportunities']['information']))
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Information</h1>
                    <h5>Overall Opportunities and Recommended Action Steps</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for information.</p>
                </div>
            </div>
            @foreach ($scores['opportunities']['information'] as $i => $opportunity)
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

{{-- More Information Opportunities --}}
@if (count($scores['opportunities']['information']) > 2)
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Information</h1>
                    <h5>Overall Opportunities and Recommended Action Steps</h5>
                </div>
            </div>
            @foreach ($scores['opportunities']['information'] as $i => $opportunity)
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

{{-- Rewards Scores and Opportunities --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row">
            <div class="col-sm-12 text-center">
                <h1>Reward</h1>
                <h5>Score Interpretation & Feedback</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-justify">
                <div id="leader-chart-rewards"></div>
                @if (! empty($scores['strengths']['rewards']))
                    <h5>Overall Strengths</h5>
                    @foreach ($scores['strengths']['rewards'] as $strength)
                        {!! $strength !!}
                    @endforeach
                @endif
            </div>

            @if (! empty($scores['opportunities']['rewards']))
                <div class="col-xs-12 text-justify">
                    <h5>Overall Opportunities</h5>
                    {!! $scores['opportunities']['rewards'][0]['Description'] !!}
                </div>
                <div class="col-xs-12 text-justify">
                    <h5>Potential Action Steps:</h5>
                    <ol>
                        {!! $scores['opportunities']['rewards'][0]['Action Steps'][0] !!}
                        {!! $scores['opportunities']['rewards'][0]['Action Steps'][1] !!}
                        <li><hr>Come up with your own action steps:</li>
                    </ol>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Knowledge Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <h1>Knowledge</h1>
                <h5>Score Interpretation & Feedback</h5><br>
                <div id="leader-chart-knowledge"></div>
                <div id="leader-chart-knowledge-subdimensions"></div>
            </div>
            <div class="col-sm-12 text-justify">
                @if (! empty($scores['strengths']['knowledge']))
                    <h5>Overall Strengths</h5>
                    <ul>
                        @foreach ($scores['strengths']['knowledge'] as $strength)
                            {!! $strength !!}
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Knowledge Opportunities --}}
@if (! empty($scores['opportunities']['knowledge']))
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-2">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Knowledge</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for knowledge.</p>
                </div>
            </div>
            @foreach ($scores['opportunities']['knowledge'] as $i => $opportunity)
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

{{-- More Knowledge Opportunities --}}
@if (count($scores['opportunities']['knowledge']) > 2)
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Knowledge</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            @foreach ($scores['opportunities']['knowledge'] as $i => $opportunity)
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

{{-- Relationships Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <h1>Relationships</h1>
            <h5>Score Interpretation & Feedback</h5>
            <div id="leader-chart-relationships"></div>
            <div id="leader-chart-relationships-subdimensions"></div>
        </div>
        <div class="col-sm-12 text-justify">
            @if (! empty($scores['strengths']['relationships']))
                <h5>Overall Strengths</h5>
                <ul>
                    @foreach ($scores['strengths']['relationships'] as $strength)
                        {!! $strength !!}
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

{{-- Relationships Opportunities --}}
@if (! empty($scores['opportunities']['relationships']))
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Relationships</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-justify">
                    <p>Below, we provide the dimensions where there is an opportunity for potential improvement and specific action steps for relationships.</p>
                </div>
            </div>
            @foreach ($scores['opportunities']['relationships'] as $i => $opportunity)
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

{{-- More Relationships Opportunities --}}
@if (count($scores['opportunities']['relationships']) > 2)
	<?php $page++; ?>
    <div class="page-container" id="{{ $page }}">
        <div class="img-container-1">
            <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            <small>Page {{ $page }}</small>
        </div>
        <div class="container">

            {{-- Heading --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Relationships</h1>
                    <h5>Overall Summary Recommendations</h5>
                </div>
            </div>
            @foreach ($scores['opportunities']['relationships'] as $i => $opportunity)
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