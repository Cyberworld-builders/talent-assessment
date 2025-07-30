@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        html .select2-container.select2-container-multi .select2-choices li.select2-search-choice {
            padding: 11px 11px 11px 20px;
        }
        .select2-container-multi .select2-choices .select2-search-field input {
            padding: 11px;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">

        <div class="member-form-inputs">

            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of the job.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('slug', 'Job ID', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Unique identifier for this job.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('slug', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                    <p class="small text-muted">An optional description to describe what this job is all about.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::textarea('description', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('active', 'Job Status', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Open jobs can accept new applicants. Closed jobs cannot receive any new applicants, but all assessments that have already been assigned to existing applicants will still be recorded until all outstanding assessments are completed.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('active', [
                        0 => 'Closed',
                        1 => 'Open'
                    ], null, ['class' => 'form-control input-lg', 'id' => 'active']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#active").select2().on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('assessments[]', 'Assessments Applicable To This Job', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This controls which assessments can be assigned to the applicants of this job.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('assessments[]', $assessmentsArray, null, ['class' => 'form-control input-lg', 'id' => 'assessments', 'multiple']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#assessments").select2({
                                placeholder: 'Select Assessments',
                                allowClear: true
                            }).on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {

        // Set headers for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });

        // Checkboxes
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_square-aero icheck',
            radioClass: 'iradio_square-aero'
        });
    });
</script>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name , ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>

@section('scripts')
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
@stop