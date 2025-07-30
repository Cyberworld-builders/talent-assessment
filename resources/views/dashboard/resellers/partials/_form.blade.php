@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <!-- Name -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of the reseller.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            @if ($edit)

                <!-- Database -->
                <div class="row">
                    <div class="col-sm-3">
                        {!! Form::label('db_name', 'Database Name', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The database for this reseller.</p>
                    </div>
                    <div class="col-sm-9">
                        {!! Form::text('db_name', null, ['class' => 'form-control input-lg', 'readonly']) !!}
                    </div>
                </div>

                <!-- Database Password -->
                {{--<div class="row">--}}
                    {{--<div class="col-sm-3">--}}
                        {{--{!! Form::label('db_pass', 'Database Password', ['class' => 'control-label']) !!}--}}
                        {{--<p class="small text-muted">The database password for this reseller.</p>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-9">--}}
                        {{--{!! Form::text('db_pass', null, ['class' => 'form-control input-lg', 'readonly']) !!}--}}
                    {{--</div>--}}
                {{--</div>--}}

            @endif

            <!-- Logo -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('logo', 'Logo URL', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Reseller logo. This will customize the look of this reseller's dashboard.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit and $reseller->logo)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ show_image($reseller->logo) }}" style="max-width:200px;" />
                        </div>
                    @endif
                    {!! Form::file('logo', ['id' => 'logo']) !!}
                </div>
            </div>

            <!-- Background -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('background', 'Background URL', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Reseller background image. This will customize the look of this reseller's dashboard.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit and $reseller->background)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ show_image($reseller->background) }}" style="max-width:200px;" />
                        </div>
                    @endif
                    {!! Form::file('background', ['id' => 'background']) !!}
                </div>
            </div>

            <!-- Primary Color -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('primary_color', 'Primary Color', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The primary color of the reseller. This will affect the look of assessments and the reseller's dashboard.</p>
                </div>
                <div class="col-sm-9">
                    <div class="input-group" style="max-width: 200px;">
                        {!! Form::input('text', 'primary_color', null, ['class' => 'form-control input-lg colorpicker colorpicker-element', 'data-format' => 'hex', 'placeholder' => '#2D2E30']) !!}
                        <div class="input-group-addon">
                            <i class="color-preview" style="background-color: rgb(45, 46, 48);"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accent Color -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('accent_color', 'Accent Color', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The secondary color of the reseller. This will affect the look of assessments and the reseller's dashboard.</p>
                </div>
                <div class="col-sm-9">
                    <div class="input-group" style="max-width: 200px;">
                        {!! Form::input('text', 'accent_color', null, ['class' => 'form-control input-lg colorpicker colorpicker-element', 'data-format' => 'hex', 'placeholder' => '#FFBA00']) !!}
                        <div class="input-group-addon">
                            <i class="color-preview" style="background-color: rgb(255, 186, 0);"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessments -->
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('assessments[]', 'Assessments Available to this Reseller', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This controls which assessments the Reseller will have access to. Assessments will be assignable, but the Reseller cannot review or modify them.</p>
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

    });
</script>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name , ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    <div class="clearfix"></div>
</div><br><br>

@section('scripts')
    <script src="{{ asset('assets/js/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
@stop