{{-- 360 Report Template --}}
<?php
    global $page;
    global $i;
?>

{{-- Cover Page --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="container">
        {{-- Logo --}}
        <div class="row">
            <div class="col-xs-2 visible-xs"></div>
            <div class="col-xs-8 col-sm-8 col-sm-offset-2 text-center">
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <img class="img-responsive text-center cover-logo" src="{{ asset('assets/images/involved-talent-logo.png') }}">
            </div>
        </div>

        {{-- Candidate --}}
        <div class="row">
            <div class="col-sm-5 col-sm-offset-7 text-right cover-for">
                <br><br><br class="hidden-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
                <h3>360 Report for:</h3>
                <h4>@include('dashboard.reports.partials._name')</h4>
                <br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs"><br class="visible-xs">
            </div>
        </div>

        {{-- Overview --}}
        <div class="row">
            <div class="col-sm-10">
                <h5>Overview</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'This 360-degree assessment provides comprehensive feedback from multiple sources including self-assessment, direct reports, supervisors, peers, and others. The report covers key competencies and provides evidence for leadership development opportunities.'
                ])
                <?php $i++; ?>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><hr></div></div>

        {{-- Disclaimer --}}
        <div class="row disclaimer">
            <div class="col-xs-10 col-sm-10">
                <h6 class="small">
                    Involved Talent offers the most scientifically valid 360-degree assessments. We use the latest research and evidence-based approaches to provide comprehensive leadership development insights.
                </h6>
            </div>
            <div class="col-xs-2 col-sm-2 text-right report-logo">
                <img class="img-responsive" src="{{ asset('assets/images/involved-talent-logo.png') }}">
            </div>
        </div>
    </div>
</div>

{{-- Competency Overview --}}
<?php $page++; ?>
<div class="page-container" id="{{ $page }}">
    <div class="container">
        {{-- Heading --}}
        <div class="row text-center">
            <div class="col-sm-12">
                <h1>360-Degree Assessment</h1>
                <h4>for @include('dashboard.reports.partials._name')</h4>
            </div>
            <div class="col-sm-12 text-justify">
                <h5>Core Competencies</h5>
                @include('dashboard.reports.partials._field', [
                    'field' => 'This report evaluates performance across nine key leadership competencies. Each competency is assessed through multiple perspectives to provide a comprehensive view of strengths and development opportunities.'
                ])
                <?php $i++; ?>
            </div>
        </div>

        {{-- Competency List --}}
        <div class="row">
            <div class="col-sm-12">
                <h5>Competencies Assessed:</h5>
                <ul>
                    <li>Creative Problem Solving</li>
                    <li>Leadership Adaptability</li>
                    <li>Collaboration</li>
                    <li>Self-Development</li>
                    <li>Performance Management</li>
                    <li>Strategic Thinking</li>
                    <li>Communication</li>
                    <li>Change Management</li>
                    <li>Team Development</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Custom Fields Support --}}
@if (isset($fields) && is_array($fields))
    @foreach ($fields as $field)
        <?php $page++; ?>
        <div class="page-container" id="{{ $page }}">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h3>{{ $field['title'] ?? 'Section' }}</h3>
                        @include('dashboard.reports.partials._field', [
                            'field' => $field['content'] ?? 'Content goes here...'
                        ])
                        <?php $i++; ?>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif