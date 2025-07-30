<?php
    global $page;
    global $i;
?>

{{-- Safety Report --}}
<?php $page++ ?>
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
                        <img class="img-responsive" src="/wp/wp-content/themes/aoe/images/AOE-S.png">
                    </div>
                </div>
                <h1>Safety Report</h1>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Introduction</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'AOE Scientists have been studying the psychological drivers of occupational safety for over two decades and have published their findings in leading scholarly journals. The AOE-S is based upon this foundational work resulting in six primary drivers of workplace safety.

The AOE-S is a very good indicator of workplace safety behavior – indicating who is more likely to be involved in an accident at work. Generally speaking, those that possess appropriate job knowledge, motivation, confidence, focus, internal locos of control, and are risk averse tend to behave significantly more safe than those lacking in one or more of these aspects. Such a profile has been quantitatively linked to higher safety behavior and thereby reduced accident involvement.'
                ])
				<?php $i++; ?>
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
                <p class="small">Mindful focus and awareness on current activities and avoiding irrelevant/distracting information.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Control</strong></p>
            </div>
            <div class="col-xs-8">
                <p class="small">Tendency to see one's self as in control rather than external forces such as fate or luck.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Confidence</strong></p>
            </div>
            <div class="col-xs-8">
                <p class="small">Possessing the confidence to safely and accurately complete work tasks, even in the face of competing demands or unexpected situations.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Motivation</strong></p>
            </div>
            <div class="col-xs-8">
                <p class="small">Motivated to perform work tasks accurately and safely as well as motivated to help others do the same.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Knowledge</strong></p>
            </div>
            <div class="col-xs-8">
                <p class="small">A willingness and ability to learn and follow safety procedures.</p>
            </div>
        </div>
        <div class="row border-bottom">
            <div class="col-xs-4">
                <p><strong>Risk Avoidance</strong></p>
            </div>
            <div class="col-xs-8">
                <p class="small">A tendency to avoid risks - risks that could speed up work and/or risks that are thrilling/exciting, but can also jeopardize safety.</p>
            </div>
        </div>
    </div>
</div>

{{-- Safety Scoring and Importance --}}
<?php $page++ ?>
<div class="page-container" id="{{ $page }}">
    <div class="img-container-2">
        <img src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
        <small>Page {{ $page }}</small>
    </div>
    <div class="container">

        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-xs-4 visible-xs"></div>
                    <div class="col-xs-4 col-sm-4 col-sm-offset-4">
                        <img class="img-responsive " src="/wp/wp-content/themes/aoe/images/AOE-S.png">
                    </div>
                </div>
                <h1>Scoring and Importance</h1>
                <h5>
                    of the AOE-S
                    @if (isset($edit) && isset($jobId) && $jobId)
                        for @include('dashboard.reports.partials._job')
                    @endif
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-justify">
                @include('dashboard.reports.partials._field', [
                    'field' => 'All six factors in the AOE-S are important for improving safety and reducing accidents. Below we present the overall scores for [name]. Next, using our predictive talent analytics algorithms, we provide a Safety Evaluation Recommendation for this job:'
                ])
				<?php $i++; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h5>Graphical Representation of AOE-S Scores for @include('dashboard.reports.partials._name')</h5>
                <div id="safety-chart"></div>
            </div>
        </div>
    </div>
</div>

{{-- Safety Fit Evaluation --}}
@if ($report->show_fit)
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
                <br><br>
                <h1>Safety Evaluation</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
                <br><br>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-sm-12">
                @include('dashboard.reports.partials._field', [
                    'field' => 'Given these AOE-S results, in conjunction with job and industry profiles for [job]s, the potential fit for [name] is:'
                ])
				<?php $i++; ?>
                <br><br>
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                    @if (! isset($edit))
                        @if ($report->score_method == 1 || ($report->score_method == 2 && $report->model_configured))
                            @if ($scores[$assessment->id]['division'] == 1)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/low.png">
                            @elseif ($scores[$assessment->id]['division'] == 2)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid-to-low.png">
                            @elseif ($scores[$assessment->id]['division'] == 3)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid.png">
                            @elseif ($scores[$assessment->id]['division'] == 4)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid-to-high.png">
                            @elseif ($scores[$assessment->id]['division'] == 5)
                                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/high.png">
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
                        <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/mid.png">

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
    </div>
</div>
@else
@include('dashboard.reports.partials._field', [
    'field' => 'Given these AOE-S results, in conjunction with job and industry profiles for [job]s, the potential fit for [name] is:',
    'hidden' => true,
])
<?php $i++; ?>
@endif