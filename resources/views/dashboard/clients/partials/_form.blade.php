@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            {{-- Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of the client.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Address --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('address', 'Address', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Global office address of the client.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('address', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Logo --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('logo', 'Logo URL', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Client logo. This will show up in the header of any white-labeled assessments assigned to this client's users.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit and $client->logo)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ show_image($client->logo) }}" style="max-width:200px;" />
                        </div>
                    @endif
                    {!! Form::file('logo', ['id' => 'logo']) !!}
                </div>
            </div>

            {{-- Background --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('background', 'Background URL', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Client background image. This will show up in the header of any white-labeled assessments assigned to this client's users.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit and $client->background)
                        <div style="margin-bottom: 10px;">
                            <img src="{{ show_image($client->background) }}" style="max-width:200px;" />
                        </div>
                    @endif
                    {!! Form::file('background', ['id' => 'background']) !!}
                </div>
            </div>

            {{-- Primary Color --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('primary_color', 'Primary Color', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The primary color of the client. This will affect the look of white-labeled assessments and Client Dashboard.</p>
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

            {{-- Accent Color --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('accent_color', 'Accent Color', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The secondary color of the client. This will affect the look of white-labeled assessments and Client Dashboard.</p>
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

            {{-- Require Profile --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('require_profile', 'Require users to complete their profile?', ['class' => 'control-label']) !!}
                    <p class="small text-muted">If set to Yes, upon initial login, users will be asked to fill out their personal information as well as specify an email and change their password.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('require_profile', [
                        0 => 'No',
                        1 => 'Yes'
                    ], null, ['class' => 'form-control input-lg', 'id' => 'require_profile']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#require_profile").select2().on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
            </div>

            {{-- Show Research Questions --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('require_research', 'Show research questions?', ['class' => 'control-label']) !!}
                    <p class="small text-muted">If set to Yes, upon initial login, users will be asked to fill out optional research questions.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('require_research', [
                        0 => 'No',
                        1 => 'Yes'
                    ], null, ['class' => 'form-control input-lg', 'id' => 'require_research']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#require_research").select2().on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
            </div>

            {{-- Whitelabel --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('whitelabel', 'Whitelabel client-assigned assessments?', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Specify whether all assessments assigned by a Client Admin will be white-labeled or not.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('whitelabel', [
                        0 => 'No',
                        1 => 'Yes'
                    ], null, ['class' => 'form-control input-lg', 'id' => 'whitelabel']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#whitelabel").select2().on('select2-open', function()
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
    <div class="clearfix"></div>
</div><br><br>

@section('scripts')
    <script src="{{ asset('assets/js/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
@stop