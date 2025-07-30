@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@stop

<style>
    html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
        padding: 6px 9px 6px 21px;
    }
    html .select2-container.select2-container-multi .select2-choices {
        padding: 4px;
    }
    .status {
        display: inline-block;
        vertical-align: middle;
        background: #b7b7b7;
        margin-right: 5px;
        position: relative;
        top: -1px;
        width: 8px;
        height: 8px;
        -webkit-border-radius: 8px;
        -webkit-background-clip: padding-box;
        -moz-border-radius: 8px;
        -moz-background-clip: padding;
        border-radius: 8px;
        background-clip: padding-box;
        -webkit-transition: all 220ms ease-in-out;
        -moz-transition: all 220ms ease-in-out;
        -o-transition: all 220ms ease-in-out;
        transition: all 220ms ease-in-out;
    }
    .status.green {
        background-color: #8dc63f;
    }
    .status.lime {
        background-color: #b9c945;
    }
    .status.yellow {
        background-color: #ffba00;
    }
    .status.orange {
        background-color: #d36e30;
    }
    .status.red {
        background-color: #cc3f44;
    }
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
    h3, h2 {
        font-family: "Bebas Neue";
        font-size: 20px;
    }
    h2 {
        font-size: 24px;
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
    .control-label.division-label {
        font-size: 12px;
        font-weight: normal;
    }
    .division-box {
        opacity: .6;
        -webkit-transition: all 220ms ease-in-out;
        -moz-transition: all 220ms ease-in-out;
        -o-transition: all 220ms ease-in-out;
        transition: all 220ms ease-in-out;
    }
    .division-box.active {
        opacity: 1;
    }
    .member-form-inputs .row:before {
        display: none;
    }
    .assessment {
        border: 2px solid #eee;
        padding: 20px;
    }
    .assessment .title {
        margin-top: 0px;
        display: block;
        background: #eee;
        margin: -20px -20px 0 -20px;
        padding: 15px 20px;
    }
    .member-form-inputs label[for="weight[]"] {
        font-size: 12px;
    }
</style>

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="row">
            <div class="member-form-inputs">

                {{-- Sidebar --}}
                <div class="col-sm-3">
                    @include('dashboard.reports.partials._sidenav', ['active' => 'weighting'])
                </div>

                {{-- Form --}}
                <div class="col-sm-9">

                    @foreach ($assessments as $j => $assessment)
						<?php if ($j > 0) echo '<br/><br/>'; ?>
                        <div class="assessment">

                            {{-- Title --}}
                            <h2 class="title">
                                @if ($assessment->logo)
                                    <img style="max-height: 25px;" src="{{ $assessment->logo }}" />
                                @endif
                                {{ $assessment->name }}
                            </h2>

                            @if ($assessment->id == get_global('leader'))
                                <br/><br/>
                                <div class="alert alert-default" style="opacity: 0.5;">Weights and divisions are not applicable for this assessment</div>
                                </div>
                                <?php continue; ?>
                            @endif

                            {{-- Dimension Weights --}}
                            <h3>Dimension Weights</h3>
                            @if (! $assessment->dimensions->isEmpty())
                                <div class="row weights">
                                    @foreach ($assessment->dimensions as $i => $dimension)
                                        <?php if ($dimension->isChild()) continue; ?>
                                        <?php if ($i != 0 && $i % 6 == 0) echo '</div><div class="row">'; ?>

                                        <div class="col-sm-2">
                                            {!! Form::label('weight['.$assessment->id.'][]', $dimension->name, ['class' => 'control-label']) !!}

                                            <div class="input-group" style="max-width: 120px;">
                                                @if ($report->weights && property_exists(json_decode($report->weights), $assessment->id) && json_decode($report->weights)->{$assessment->id})
                                                    {!! Form::text('weight['.$assessment->id.'][]', json_decode($report->weights)->{$assessment->id}->{$dimension->id}, ['class' => 'form-control input-lg']) !!}
                                                @else
                                                    {!! Form::text('weight['.$assessment->id.'][]', 0, ['class' => 'form-control input-lg']) !!}
                                                @endif
                                                {!! Form::hidden('dimension['.$assessment->id.'][]', $dimension->id) !!}
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Totals Check --}}
                                <hr/>
                                <div class="row">
                                    <div class="col-sm-6"></div>
                                    <div class="col-sm-4">
                                        <p class="small text-muted">The total of all dimension weights must be equal to 100%.</p>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="input-group has-error" style="max-width: 120px;">
                                            {!! Form::text('total['.$assessment->id.']', 0, ['class' => 'form-control input-lg', 'readonly', 'style' => 'border-color:#e4e4e4;']) !!}
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-default" style="opacity: 0.5;">There are no dimensions to set weights for</div>
                            @endif

                            {{-- Score Divisions --}}
                            <h3>Score Divisions</h3>
                            <p class="small text-muted">
                                @if (! $assessment->dimensions->isEmpty())
                                    Set what average weighted score the user must get across all dimensions (or get higher than) to fall into these divisions.<br/>
                                    If you do not wish to use a specific division, just leave the values blank to exclude it.
                                @else
                                    Set what total raw score the user must get (or get higher than) to fall into these divisions.<br/>
                                    If you do not wish to use a specific division, just leave the values blank to exclude it.
                                @endif
                            </p><br/>

                            <div class="row">
                                <div class="col-sm-2 division-box active">
                                    <span class="status red"></span>
                                    {!! Form::label('divisions['.$assessment->id.'][]', 'Low', ['class' => 'control-label division-label']) !!}
                                    @if ($report->divisions && property_exists(json_decode($report->divisions), $assessment->id) && json_decode($report->divisions)->{$assessment->id})
                                        {!! Form::text('divisions['.$assessment->id.'][]', json_decode($report->divisions)->{$assessment->id}[0], ['class' => 'form-control input-lg', 'readonly']) !!}
                                    @else
                                        {!! Form::text('divisions['.$assessment->id.'][]', 0, ['class' => 'form-control input-lg', 'readonly']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-2 division-box">
                                    <span class="status orange"></span>
                                    {!! Form::label('divisions['.$assessment->id.'][]', 'Moderate-to-Low', ['class' => 'control-label division-label']) !!}
                                    @if ($report->divisions && property_exists(json_decode($report->divisions), $assessment->id) && json_decode($report->divisions)->{$assessment->id})
                                        {!! Form::text('divisions['.$assessment->id.'][]', json_decode($report->divisions)->{$assessment->id}[1], ['class' => 'form-control input-lg']) !!}
                                    @else
                                        {!! Form::text('divisions['.$assessment->id.'][]', null, ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-2 division-box">
                                    <span class="status yellow"></span>
                                    {!! Form::label('divisions['.$assessment->id.'][]', 'Moderate', ['class' => 'control-label division-label']) !!}
                                    @if ($report->divisions && property_exists(json_decode($report->divisions), $assessment->id) && json_decode($report->divisions)->{$assessment->id})
                                        {!! Form::text('divisions['.$assessment->id.'][]', json_decode($report->divisions)->{$assessment->id}[2], ['class' => 'form-control input-lg']) !!}
                                    @else
                                        {!! Form::text('divisions['.$assessment->id.'][]', null, ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-2 division-box">
                                    <span class="status lime"></span>
                                    {!! Form::label('divisions['.$assessment->id.'][]', 'Moderate-to-High', ['class' => 'control-label division-label']) !!}
                                    @if ($report->divisions && property_exists(json_decode($report->divisions), $assessment->id) && json_decode($report->divisions)->{$assessment->id})
                                        {!! Form::text('divisions['.$assessment->id.'][]', json_decode($report->divisions)->{$assessment->id}[3], ['class' => 'form-control input-lg']) !!}
                                    @else
                                        {!! Form::text('divisions['.$assessment->id.'][]', null, ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-2 division-box">
                                    <span class="status green"></span>
                                    {!! Form::label('divisions['.$assessment->id.'][]', 'High', ['class' => 'control-label division-label']) !!}
                                    <div class="input-group">
                                        @if ($report->divisions && property_exists(json_decode($report->divisions), $assessment->id) && json_decode($report->divisions)->{$assessment->id})
                                            {!! Form::text('divisions['.$assessment->id.'][]', json_decode($report->divisions)->{$assessment->id}[4], ['class' => 'form-control input-lg']) !!}
                                        @else
                                            {!! Form::text('divisions['.$assessment->id.'][]', null, ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                                        @endif
                                        <span class="input-group-addon"><i class="fa-chevron-right"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                        </div>
                    @endforeach

                    <br/><br/>
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

            @foreach ($assessments as $assessment)

                // Show active divisions when filled in
                $('input[name="divisions[{{ $assessment->id }}][]"]').on('change', function() {
                    if ($(this).val() == '')
                        $(this).closest('.division-box').removeClass('active');
                    else
                        $(this).closest('.division-box').addClass('active');
                });
                $('input[name="divisions[{{ $assessment->id }}][]"]').each(function() {
                    if ($(this).val() == '')
                        $(this).closest('.division-box').removeClass('active');
                    else
                        $(this).closest('.division-box').addClass('active');
                });

                // Dimension weights must be numbers
                $('input[name="weight[{{ $assessment->id }}][]"]').on('change', function(){
                    if (isNaN($(this).val()))
                        $(this).val(0);

                    // Calculate total on every change
                    calculate_total();
                });

                // Division ranges must be numbers
                $('input[name="division[{{ $assessment->id }}][]"], input[name="division[{{ $assessment->id }}][]"]').on('change', function(){
                    if (isNaN($(this).val()))
                        $(this).val('');
                });
            @endforeach

            // Calculate the total right on page load
            calculate_total();

            // Calculating the total
            function calculate_total()
            {
                @foreach ($assessments as $assessment)
                    var total = 0;
                    $('input[name="weight[{{ $assessment->id }}][]"]').each(function(){
                        total += parseInt($(this).val());
                        $('input[name="total[{{ $assessment->id }}]"]').val(total);
                    });

                    // Change color of total input field to indicate proper percentage
                    if (total == 100)
                        $('input[name="total[{{ $assessment->id }}]"]').closest('.input-group').removeClass('has-error').addClass('has-success');
                    else
                        $('input[name="total[{{ $assessment->id }}]"]').closest('.input-group').removeClass('has-success').addClass('has-error');
                @endforeach
            }

            // Make sure dimension weights equal out to 100 before allowing a submit
            $('input[type="submit"]').on('click', function(e) {
                e.preventDefault();
                var totals = $('.weights').length;
                var checks = 0;

                @foreach ($assessments as $assessment)
                    var total = 0;
                    $('input[name="weight[{{ $assessment->id }}][]"]').each(function(){
                        total += parseInt($(this).val());
                        $('input[name="total[{{ $assessment->id }}]"]').val(total);
                    });

                    // Change color of total input field to indicate proper percentage
                    if (total == 100)
                        checks += 1;
                @endforeach

                if (checks == totals)
                    $('#form').submit();
                else
                {
                    var opts = {
                        "closeButton": true,
                        "debug": false,
                        "positionClass": "toast-top-right",
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr.error("The total for all dimension weights must equal 100%", "Error", opts);
                }
            });
        });
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
@stop