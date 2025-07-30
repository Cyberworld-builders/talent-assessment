<?php
    global $page;
    global $i;
?>

{{-- Scores --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-1">
        <img src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/AOE-A.png">
                    </div>
                </div>
                <h1>Cognitive Ability Report</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
            </div>
            <div class="col-sm-12 text-justify">
                @include('dashboard.reports.partials._field', [
                    'field' => 'The AOE-A is a scientifically valid, excellent indicator of a person\'s maximal performance level as assessed by cognitive ability. Cognitive ability has been used for years in many industries as an indicator of the likelihood of job success.

Theoretical and empirical evidence clearly shows that cognitive ability is a fantastic way to assess what a person can-do, at a maximal level in a given job. Cognitive ability results provide evidence of a person\'s ability to solve problems, learn new skills, and think critically.

Below, we present graphical results for [name] followed by AOE-A potential for this job. The raw score is shown on the graph below. The graph represents percentiles so you can see this candidate\'s standing relative to the norms for this test.'
               ])
				<?php $i++; ?>
            </div>
        </div>

        {{-- Chart --}}
        <div class="row">
            <div class="chart">
                <div id="ability-chart"></div>
            </div>
        </div>
    </div>
</div>

{{-- Evaluation Page --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/AOE-A.png">
                    </div>
                </div>
                <h1>Cognitive Ability Evaluation</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Raw Score</h3>
                @include('dashboard.reports.partials._field', [
                    'field' => 'The raw score for [name] is noted in the graph to provide an overall snapshot of how this score compares on the full range of possible raw scores.

[name] answered [score] questions correct on the AOE-A which is [accuracy]% accuracy.'
                ])
				<?php $i++; ?>
            </div>
        </div>
        @if ($report->show_fit)
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Percentile Score</h3>
                @if (! isset($edit) && $scores[$assessment->id]['division'] == 1)

                    {{-- Not Recommended --}}
                    <p>
                        Comparing @include('dashboard.reports.partials._name') to other people taking this assessment, @include('dashboard.reports.partials._name')'s percentile score is @include('dashboard.reports.partials._score', ['type' => 'percentile'])%,
                        meaning that @include('dashboard.reports.partials._name')'s score is equal to or better than @include('dashboard.reports.partials._score', ['type' => 'percentile'])% of all others taking this assessment.
                        The score suggests @include('dashboard.reports.partials._name') lacks a level of understanding for numerical reasoning, thinking about and using data, mathematical calculations,
                        understanding written language, and verbal reasoning necessary to learn the job of @include('dashboard.reports.partials._job').
                    </p>
                @elseif (! isset($edit) && ($scores[$assessment->id]['division'] == 2 || $scores[$assessment->id]['division'] == 3))

                    {{-- Caution --}}
                    <p>
                        Comparing @include('dashboard.reports.partials._name') to other people taking this assessment, @include('dashboard.reports.partials._name')'s percentile score is @include('dashboard.reports.partials._score', ['type' => 'percentile'])%,
                        meaning that @include('dashboard.reports.partials._name')'s score is equal to or better than @include('dashboard.reports.partials._score', ['type' => 'percentile'])% of all others taking this assessment.
                    </p>
                    <p>
                        The score for @include('dashboard.reports.partials._name') suggests moderate strengths with numerical reasoning, thinking about and using data,
                        mathematical calculations, understanding written language, and verbal reasoning. Overall, the score for @include('dashboard.reports.partials._name')
                        indicates moderate capability to learn and apply new job knowledge. @include('dashboard.reports.partials._name') has some potential for success in this
                        job based on the AOE-A score, but caution is recommended.
                    </p>
                @elseif (! isset($edit) && ($scores[$assessment->id]['division'] == 4 || $scores[$assessment->id]['division'] == 5))

                    {{-- Pursue --}}
                    <p>
                        The score suggests @include('dashboard.reports.partials._name') has the essential knowledge of numerical reasoning, thinking about and using data,
                        mathematical calculations, understanding written language, and verbal reasoning to learn the job of @include('dashboard.reports.partials._job').
                    </p>
                @elseif (isset($edit))
                    <p>
                        The score suggests @include('dashboard.reports.partials._name') has the essential knowledge of numerical reasoning, thinking about and using data,
                        mathematical calculations, understanding written language, and verbal reasoning to learn the job of @include('dashboard.reports.partials._job').
                    </p>
                @endif
                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                    <br><br>
                    @if (! isset($edit))
                        @if ($report->score_method == 1 || ($report->score_method == 2 && $report->model_configured))
                            @if ($scores[$assessment->id]['division'] == 1)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/not recommended.png">
                            @elseif (($scores[$assessment->id]['division'] == 2 || $scores[$assessment->id]['division'] == 3))
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/caution.png">
                            @elseif (($scores[$assessment->id]['division'] == 4 || $scores[$assessment->id]['division'] == 5))
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/pursue.png">
                            @endif

                            @if ($report->score_method == 2 && $report->model_configured)
                                {{-- Model Confidence --}}
                                <div style="clear:both;"></div>
                                <div class="model-confidence" style="text-align:center;">
                                    <h2 style="display:inline-block;font-size:23px;">Model Confidence:</h2>
                                    <h4 style="display:inline-block;font-size:23px;">{{ $scores[$assessment->id]['confidence'] }}</h4>
                                </div>
                            @elseif ($report->score_method == 2 && ! $report->model_configured)
                                <div class="alert alert-danger" role="alert">
                                    Predictive model not configured
                                </div>
                            @endif
                        @endif
                    @else
                        <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/pursue.png">

                        @if ($report->score_method == 2)
                            {{-- Model Confidence --}}
                            <div style="clear:both;"></div>
                            <div class="model-confidence" style="text-align:center;">
                                <h2 style="display:inline-block;font-size:23px;">Model Confidence:</h2>
                                <h4 style="display:inline-block;font-size:23px;">100</h4>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>