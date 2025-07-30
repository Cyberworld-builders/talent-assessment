<?php
    global $page;
    global $i;
?>

{{-- Scores --}}
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
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-WM.png">
                    </div>
                </div>
                <h1>Working Memory Report</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
            </div>
            <div class="col-sm-12 text-justify">
                @include('dashboard.reports.partials._field', [
                    'field' => 'At AOE, we incorporate the latest Talent Evidence from scientific literature, our own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions. The AOE-WM is one of these solutions. The AOE-WM is an excellent indicator of a person\'s maximal performance level as assessed by working memory. Working memory is the information processing mechanism responsible for: (1) attention and focus (2) storage into long-term memory, and (3) mental processing of information for problem solving and decision making. Working memory is responsible for critical thinking, mental speed, multi-tasking, learning, and reasoning.

Below is a graphical representation for [name] followed by the AOE-WM potential for this job. In selection, higher scores are better. Scores in the range of 43 to 50 indicate that the candidate is strong at multi-tasking, decision-making, problem-solving, and critical thinking.'
               ])
				<?php $i++; ?>
            </div>
        </div>

        {{-- Chart --}}
        <div class="row">
            <div class="chart">
                <div id="ospan-chart"></div>
            </div>
        </div>
    </div>
</div>

{{-- Evaluation Page --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <div class="row">
                    <div id="invisible-4" class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-WM.png">
                    </div>
                </div>
                <h1>Working Memory Evaluation</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
                <br><br>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Raw Score</h3>
                @include('dashboard.reports.partials._field', [
                    'field' => 'The raw score for [name] is noted in the graph to provide an overall snapshot of how this score compares on the full range of possible raw scores.

[name] answered [score] out of [total] questions correct on the AOE-WM which is [accuracy]% accuracy.'
                ])
				<?php $i++; ?>
            </div>
        </div>
        @if ($report->show_fit)
        <div class="row">
            <div class="col-sm-12 text-justify">
                <h3>Percentile Score</h3>
                @include('dashboard.reports.partials._field', [
                    'field' => 'Comparing [name] to other people taking this assessment, [name]\'s percentile score is [percentile]%, meaning that [name]\'s score is equal to or better than [percentile]% of all others taking this assessment.'
                ])
				<?php $i++; ?>
                @if (! isset($edit) && $scores[$assessment->id]['division'] == 1)

                    {{-- Not Recommended --}}
                    <p>
                        The score suggests @include('dashboard.reports.partials._name') lacks a level of understanding for numerical reasoning, thinking about and using data, mathematical calculations,
                        understanding written language, and verbal reasoning necessary to learn the job of @include('dashboard.reports.partials._job').
                    </p>
                @elseif (! isset($edit) && ($scores[$assessment->id]['division'] == 2 || $scores[$assessment->id]['division'] == 3))

                    {{-- Caution --}}
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
