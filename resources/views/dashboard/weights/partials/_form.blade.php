@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">--}}
    <style type="text/css">
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
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <h3>Dimension Weights</h3><br/>

            @if (! $assessment->dimensions->isEmpty())
                @foreach ($assessment->dimensions as $dimension)
                    <?php if ($dimension->isChild()) continue; ?>

                    <!-- Dimension Weight -->
                    <div class="row">
                        <div class="col-sm-3">
                            {!! Form::label('weight[]', $dimension->name, ['class' => 'control-label']) !!}
                            <p class="small text-muted">Set custom weighting for this dimension.</p>
                        </div>
                        <div class="col-sm-9">
                            <div class="input-group" style="max-width: 120px;">
                                {!! Form::text('weight[]', ($edit ? $weight->weights[$dimension->id] : 0), ['class' => 'form-control input-lg']) !!}
                                {!! Form::hidden('dimension[]', $dimension->id) !!}
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>

                @endforeach

                <!-- Total Check -->
                <div class="row">
                    <div class="col-sm-3">
                        {!! Form::label('total', 'Total Check', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The total of all dimension weights must be equal to 100%.</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="input-group has-error" style="max-width: 120px;">
                            {!! Form::text('total', 0, ['class' => 'form-control input-lg', 'readonly', 'style' => 'border-color:#e4e4e4;']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
            @else
                There are no dimensions to set weights for.
            @endif

            <br/><br/><h3>Score Divisions</h3><br/>
            <p class="small text-muted" style="margin-top: -19px;margin-bottom: 25px;">
                @if (! $assessment->dimensions->isEmpty())
                    Set what average weighted score the user must get across all dimensions to fall into these divisions.<br/>
                    If you do not wish to use a specific division, just leave the values blank to exclude it.
                @else
                    Set what total raw score the user must get to fall into these divisions.<br/>
                    If you do not wish to use a specific division, just leave the values blank to exclude it.
                @endif
            </p>

            {{--<div class="row">--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('num_of_divisions', 'Number Of Divisions', ['class' => 'control-label']) !!}--}}
                    {{--<p class="small text-muted">The number of divisions the score will be compared against. The score will fall into one of these divisions.</p>--}}
                {{--</div>--}}
                {{--<div class="col-sm-9">--}}
                    {{--{!! Form::select('num_of_divisions', [--}}
                        {{--3 => '3 Divisions',--}}
                        {{--5 => '5 Divisions'--}}
                    {{--], null, ['class' => 'form-control input-lg', 'id' => 'num_of_divisions']) !!}--}}
                    {{--<script type="text/javascript">--}}
                        {{--jQuery(document).ready(function($)--}}
                        {{--{--}}
                            {{--$("#num_of_divisions").select2().on('select2-open', function()--}}
                            {{--{--}}
                                {{--// Adding Custom Scrollbar--}}
                                {{--$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();--}}
                            {{--});--}}
                        {{--});--}}
                    {{--</script>--}}
                {{--</div>--}}
            {{--</div>--}}

            <!-- High Fit -->
            <div class="row">
                <div class="col-sm-3">
                    <span class="status green"></span>
                    {!! Form::label('division[]', 'High Fit', ['class' => 'control-label']) !!}
                    {!! Form::hidden('division[]', 'High Fit') !!}
                    <p class="small text-muted">Set the score range that the user must get to fall into this division.</p>
                </div>
                <div class="col-sm-9">
                    <div style="margin-bottom: 6px;">Equal to or Above</div>
                    {!! Form::text('division_min[]', ($edit ? $weight->divisions[0]['min'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    {!! Form::hidden('division_max[]', null) !!}
                </div>
            </div>

            <!-- Moderate To High Fit -->
            <div class="row">
                <div class="col-sm-3">
                    <span class="status lime"></span>
                    {!! Form::label('division[]', 'Moderate-To-High Fit', ['class' => 'control-label']) !!}
                    {!! Form::hidden('division[]', 'Moderate-To-High Fit') !!}
                    <p class="small text-muted">Set the score range that the user must get to fall into this division.</p>
                </div>
                <div class="col-sm-9">
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Equal to or Above</div>
                        {!! Form::text('division_min[]', ($edit ? $weight->divisions[1]['min'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Below</div>
                        {!! Form::text('division_max[]', ($edit ? $weight->divisions[1]['max'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                </div>
            </div>

            <!-- Moderate Fit -->
            <div class="row">
                <div class="col-sm-3">
                    <span class="status yellow"></span>
                    {!! Form::label('division[]', 'Moderate Fit', ['class' => 'control-label']) !!}
                    {!! Form::hidden('division[]', 'Moderate Fit') !!}
                    <p class="small text-muted">Set the score range that the user must get to fall into this division.</p>
                </div>
                <div class="col-sm-9">
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Equal to or Above</div>
                        {!! Form::text('division_min[]', ($edit ? $weight->divisions[2]['min'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Below</div>
                        {!! Form::text('division_max[]', ($edit ? $weight->divisions[2]['max'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                </div>
            </div>

            <!-- Moderate To Low Fit -->
            <div class="row">
                <div class="col-sm-3">
                    <span class="status orange"></span>
                    {!! Form::label('division[]', 'Moderate-To-Low Fit', ['class' => 'control-label']) !!}
                    {!! Form::hidden('division[]', 'Moderate-To-Low Fit') !!}
                    <p class="small text-muted">Set the score range that the user must get to fall into this division.</p>
                </div>
                <div class="col-sm-9">
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Equal to or Above</div>
                        {!! Form::text('division_min[]', ($edit ? $weight->divisions[3]['min'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                    <div style="display: inline-block;">
                        <div style="margin-bottom: 6px;">Below</div>
                        {!! Form::text('division_max[]', ($edit ? $weight->divisions[3]['max'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                    </div>
                </div>
            </div>

            <!-- Low Fit -->
            <div class="row">
                <div class="col-sm-3">
                    <span class="status red"></span>
                    {!! Form::label('division[]', 'Low Fit', ['class' => 'control-label']) !!}
                    {!! Form::hidden('division[]', 'Low Fit') !!}
                    <p class="small text-muted">Set the score range that the user must get to fall into this division.</p>
                </div>
                <div class="col-sm-9">
                    <div style="margin-bottom: 6px;">Below</div>
                    {!! Form::hidden('division_min[]', null) !!}
                    {!! Form::text('division_max[]', ($edit ? $weight->divisions[4]['max'] : null), ['class' => 'form-control input-lg', 'style' => 'max-width:120px']) !!}
                </div>
            </div>

            {{--<div class="row">--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('description', 'Description', ['class' => 'control-label']) !!}--}}
                    {{--<p class="small text-muted">An optional description to describe what this job is all about.</p>--}}
                {{--</div>--}}
                {{--<div class="col-sm-9">--}}
                    {{--{!! Form::textarea('description', null, ['class' => 'form-control input-lg']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('active', 'Job Status', ['class' => 'control-label']) !!}--}}
                    {{--<p class="small text-muted">Open jobs can accept new applicants. Closed jobs cannot receive any new applicants, but all assessments that have already been assigned to existing applicants will still be recorded until all outstanding assessments are completed.</p>--}}
                {{--</div>--}}
                {{--<div class="col-sm-9">--}}
                    {{--{!! Form::select('active', [--}}
                        {{--0 => 'Closed',--}}
                        {{--1 => 'Open'--}}
                    {{--], null, ['class' => 'form-control input-lg', 'id' => 'active']) !!}--}}
                    {{--<script type="text/javascript">--}}
                        {{--jQuery(document).ready(function($)--}}
                        {{--{--}}
                            {{--$("#active").select2().on('select2-open', function()--}}
                            {{--{--}}
                                {{--// Adding Custom Scrollbar--}}
                                {{--$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();--}}
                            {{--});--}}
                        {{--});--}}
                    {{--</script>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row">--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::label('assessments[]', 'Assessments Applicable To This Job', ['class' => 'control-label']) !!}--}}
                    {{--<p class="small text-muted">This controls which assessments can be assigned to the applicants of this job.</p>--}}
                {{--</div>--}}
                {{--<div class="col-sm-9">--}}
                    {{--{!! Form::select('assessments[]', $assessmentsArray, null, ['class' => 'form-control input-lg', 'id' => 'assessments', 'multiple']) !!}--}}
                    {{--<script type="text/javascript">--}}
                        {{--jQuery(document).ready(function($)--}}
                        {{--{--}}
                            {{--$("#assessments").select2({--}}
                                {{--placeholder: 'Select Assessments',--}}
                                {{--allowClear: true--}}
                            {{--}).on('select2-open', function()--}}
                            {{--{--}}
                                {{--// Adding Custom Scrollbar--}}
                                {{--$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();--}}
                            {{--});--}}
                        {{--});--}}
                    {{--</script>--}}
                {{--</div>--}}
            {{--</div>--}}

        </div>
    </div>
</div>

<!-- Submit Field -->
<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    <div class="clearfix"></div>
</div>

<script>
    (function($){
        $(document).ready(function(){

            // Checkboxes
            $('input.icheck').iCheck({
                checkboxClass: 'icheckbox_square-aero',
                radioClass: 'iradio_square-aero'
            });

            // Reveal hidden fields
            $('.reveal-field').on('ifChecked', function(event){
                $('.'+$(this).attr('data-field-to-reveal')).slideDown();
            }).on('ifUnchecked', function(){
                $('.'+$(this).attr('data-field-to-reveal')).slideUp();
            });

            // Check for fields that should already be revealed
            $('.reveal-field').each(function(){
                if ($(this).is(':checked')) {
                    $('.'+$(this).attr('data-field-to-reveal')).show();
                }
            });

            // Dimension weights must be numbers
            $('input[name="weight[]"]').on('change', function(){
                if (isNaN($(this).val()))
                    $(this).val(0);

                // Calculate total on every change
                calculate_total();
            });

            // Division ranges must be numbers
            $('input[name="division_min[]"], input[name="division_max[]"]').on('change', function(){
                if (isNaN($(this).val()))
                    $(this).val('');
            });

            // Calculate the total right on page load
            calculate_total();

            // Calculating the total
            function calculate_total()
            {
                var total = 0;
                $('input[name="weight[]"]').each(function(){
                    total += parseInt($(this).val());
                    $('input[name="total"]').val(total);
                });

                // Change color of total input field to indicate proper percentage
                if (total == 100)
                    $('input[name="total"]').closest('.input-group').removeClass('has-error').addClass('has-success');
                else
                    $('input[name="total"]').closest('.input-group').removeClass('has-success').addClass('has-error');
            }
        });
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>--}}
@stop