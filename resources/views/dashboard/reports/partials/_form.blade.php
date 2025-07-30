@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">--}}
@stop

<style>
    .row.no-border:before {
        border:none;
        margin: 0;
        padding: 0;
        background: none;
        height: 0;
    }
    .report-page-wrapper {
        background: url("https://s3-us-west-2.amazonaws.com/aoe-uploads/images/aoe-background.jpg");
        padding: 20px;
        position: relative;
        text-align: center;
    }
    .report-page-wrapper .cover-logo {
        max-width: 30%;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 40px;
    }
    .report-page {
        background: white;
        padding: 20px;
        padding-top: 90px;
        list-style: none;
    }
    .report-page li {
        border: 1px solid #ddd;
        padding: 5px;
        margin-bottom: 2px;
        font-family: "Bebas Neue";
        font-size: 20px;
        cursor: move;
        background: white;
    }
    .reserve-box {
        border: 2px solid #ddd;
        padding: 10px;
        margin-top: 10px;
        list-style: none;
    }
    .reserve-box li {
        cursor: move;
    }
    h3 {
        font-family: "Bebas Neue";
        font-size: 20px;
    }
    .tocify .tocify-item.active > a {
        color: #333;
        font-size: 16px;
        cursor: pointer;
    }
    .tocify .tocify-item.active > a:hover {
        background: #f0f0f0;
    }
    .tocify .tocify-item > a {
        color: #333;
        font-size: 16px;
        cursor: pointer;
    }
    .tocify .tocify-item > a:hover {
        background: #f0f0f0;
        color: #777;
    }
    .tocify .tocify-item a i {
        margin-right: 5px;
    }
    input.switch:empty {
        margin-left: -3000px;
    }
    input.switch:empty ~ label {
        position: relative;
        float: left;
        line-height: 1.6em;
        text-indent: 4em;
        margin: 0.2em 0;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    input.switch:empty ~ label:before, input.switch:empty ~ label:after {
        position: absolute;
        display: block;
        top: 0;
        bottom: 0;
        left: 0;
        content: ' ';
        width: 3.6em;
        background-color: #f4f4f4;
        border-radius: 0.3em;
        box-shadow: inset 0 0em 0 rgba(0, 0, 0, 0.3);
        -webkit-transition: all 100ms ease-in;
        transition: all 100ms ease-in;
        border: 2px solid #dadada;
    }
    input.switch:empty ~ label:after {
        width: 1.4em;
        top: 0.1em;
        bottom: 0.1em;
        margin-left: 0.1em;
        background-color: #fff;
        border-radius: 0.2em;
        box-shadow: inset 0 -0.2em 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #bebebe;
    }
    input.switch:checked ~ label:before {
        border-color: #8DC63F;
        background: #D6FEA6;
    }
    input.switch:checked ~ label:after {
        margin-left: 2.1em;
        background: #8DC63F;
        border: none;
    }
</style>

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="row">
            <div class="member-form-inputs">

                {{-- Sidebar --}}
                <div class="col-sm-3">
                    @include('dashboard.reports.partials._sidenav')
                </div>

                {{-- Form --}}
                <div class="col-sm-9">

                    {{-- Name --}}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                                <p class="small text-muted">The name of this report.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Job Id --}}
                    <div class="form-group field-parent">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('job_id', 'Reporting For', ['class' => 'control-label']) !!}
                                <p class="small text-muted">What job this report pertains to. If no job is selected, the report will pertain to all assignments for this client.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('job_id', $jobsArray, null, ['class' => 'form-control input-lg', 'id' => 'job_id']) !!}
                                <script type="text/javascript">
                                    jQuery(document).ready(function($)
                                    {
                                        $("#job_id").select2().on('select2-open', function()
                                        {
                                            // Adding Custom Scrollbar
                                            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    {{-- Assessments --}}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('assessments', 'Assessments', ['class' => 'control-label']) !!}
                                <p class="small text-muted">Choose which assessments this report will report on.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::hidden('assessments', null, ['class' => 'form-control input-lg assessments']) !!}
                                <div class="row no-border">
                                    <div class="col-sm-6">
                                        <div class="report-page-wrapper">
                                            <img class="img-responsive text-center cover-logo" src="/wp/wp-content/themes/aoe/images/aoe-science_logo.png">
                                            @if ($assessments)
                                                <ul id="sortable1" class="connectedSortable report-page reports">
                                                    @if ($edit && $report->assessments)
                                                        @foreach (json_decode($report->assessments) as $assessmentId)
                                                            <?php
                                                                $assessment = \App\Assessment::find($assessmentId);
                                                                if (! $assessment) continue;
                                                            ?>
                                                            <li class="ui-state-default" data-id="{{ $assessment->id }}">{{ $assessment->name }}</li>
                                                        @endforeach
                                                    @else
                                                        <li class="ui-state-default" data-id="{{ $assessments->first()->id }}">{{ $assessments->first()->name }}</li>
                                                    @endif
                                                </ul>
                                            @else
                                                No assessments available
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h3>Available Assessments</h3>
                                        <p class="text-muted">
                                            Drag and drop assessments from this list to the report column on the left. Assessments in the report column will show up on the report.
                                        </p>
                                        @if ($assessments)
                                            <ul id="sortable2" class="connectedSortable reserve-box">
                                                @foreach ($assessments as $i => $assessment)
                                                    @if ($edit && $report->assessments)
                                                        <?php if (in_array($assessment->id, json_decode($report->assessments))) continue; ?>
                                                    @else
                                                        <?php if ($i == 0) continue; ?>
                                                    @endif
                                                    <li class="ui-state-highlight" data-id="{{ $assessment->id }}">{{ $assessment->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            No assessments available
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Scoring Method --}}
                    <div class="form-group field-parent">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('score_method', 'Scoring Method', ['class' => 'control-label']) !!}
                                <p class="small text-muted">How the scoring and divisions for this report will be determined.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('score_method', [
                                    1 => 'Custom Weighting',
                                    2 => 'Predictive Modeling'
                                ], null, ['class' => 'form-control input-lg', 'id' => 'score_method']) !!}
                                <script type="text/javascript">
                                    jQuery(document).ready(function($)
                                    {
                                        $("#score_method").select2().on('select2-open', function()
                                        {
                                            // Adding Custom Scrollbar
                                            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    {{-- Show Fit Recommendation --}}
                    <div class="form-group field-parent">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('show_fit', 'Show Recommendation', ['class' => 'control-label']) !!}
                                <p class="small text-muted">Whether to show or hide the fit recommendation at the bottom of each report.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('show_fit', [
                                    0 => 'No',
                                    1 => 'Yes'
                                ], null, ['class' => 'form-control input-lg', 'id' => 'show_fit']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Show Item-Level Data --}}
                    <div class="form-group field-parent">
                        <div class="row">
                            <div class="col-sm-4">
                                {!! Form::label('show_item_data', 'Show Item-Level Data', ['class' => 'control-label']) !!}
                                <p class="small text-muted">Whether to show or hide the item-level data for Leader reports, such as the AOE-L.</p>
                            </div>
                            <div class="col-sm-8">
                                {!! Form::select('show_item_data', [
                                    0 => 'No',
                                    1 => 'Yes'
                                ], null, ['class' => 'form-control input-lg', 'id' => 'show_item_data']) !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Submit Field --}}
<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    <div class="clearfix"></div>
</div>

{{-- Scripts --}}
<script>
    (function($){
        $(document).ready(function(){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            $("#sortable1").sortable({
                connectWith: ".connectedSortable",
                update: function(event, ui) {
                    var order = [];

                    $('#sortable1 li').each(function(e) {
                        order.push($(this).attr('data-id'));
                    });

                    //console.log(order.join());
                    //$('assessments').val(order.join());
                }
            }).disableSelection();

            $("#sortable2").sortable({
                connectWith: ".connectedSortable"
            }).disableSelection();

            $('form').submit(function(e){
                e.preventDefault();
                $('input[type="submit"]', this).attr('disabled', 'disabled').val('Saving...');

                // Get our form data
                var formData = new FormData($(this)[0]);

                // Get the order of assessments
                var assessments = [];
                $('#sortable1 li').each(function(e) {
                    assessments.push($(this).attr('data-id'));
                });

                // Append the assessments to our form data
                for (var i = 0; i < assessments.length; i++) {
                    formData.append('assessments[]', assessments[i]);
                }

                var url = window.location.pathname.replace('/create', '').replace('/edit', '');

                $.ajax({
                    type: 'post',
                    url: url,
                    data: formData,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        window.location = '/dashboard/clients/'+data['clientId']+'/reports/'+data['reportId']+'/edit';
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });
        });
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>--}}
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>--}}
@stop