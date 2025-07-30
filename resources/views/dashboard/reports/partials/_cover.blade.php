{{-- Cover --}}
<div class="page-container" id="{{ $page }}">
    <div class="container">

        {{-- Logo --}}
        <div class="row">
            <div class="col-xs-2 visible-xs"></div>
            <div class="col-xs-8 col-sm-8 col-sm-offset-2 text-center">
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <img class="img-responsive text-center cover-logo" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
            </div>
        </div>

        {{-- Candidate --}}
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <br><br><br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <h3>Candidate Profile for:</h3>
                <h4>@include('dashboard.reports.partials._name')</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>

        {{-- Overview --}}
        <div class="row">
            <div class="col-sm-10">
                <h5>Overview</h5>
                <?php
                    if ($assessments[0]->id == get_global('personality'))
                    	$intro = 'This report provides a recommendation for [name], who applied for a [job] position. This report covers [job] personality and provides evidence for the candidate\'s likelihood of success in [job] related positions.';
                    if ($assessments[0]->id == get_global('ospan') || $assessments[0]->id == get_global('sspan'))
                    	$intro = 'This report provides a recommendation for [name], who applied for a [job] position. This report covers [job]  working memory, which focuses on memory, attention, and information processing. This report provides evidence for the candidate\'s likelihood of success in [job] related positions.';
                ?>
                @include('dashboard.reports.partials._field', [
                    'cover' => true,
                    'field' => $intro
                ])
				<?php $i++; ?>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>

        {{-- Disclaimer --}}
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6 class="small">
                    AOE Science offers the most scientifically valid candidate assessments. AOE uses the latest Talent Evidence from the scientific literature,
                    their own research, and the needs of organizations to arrive at Evidence-Based Talent Solutions.
                </h6>
            </div>
            <div class="col-xs-2 col-sm-2 text-right report-logo">
                <img class="img-responsive" src="{{ $baseUrl }}/wp/wp-content/themes/aoe/images/report-logo-1.png">
            </div>
        </div>
    </div>
</div>