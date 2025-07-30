@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">--}}
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <!-- Name Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The name of this dimension.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <!-- Code Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('code', 'Dimension Code', ['class' => 'control-label']) !!}
                        <p class="small text-muted">A short code by which to identify this dimension. This code will be used when exporting user scores for this assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::text('code', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <!-- Parent Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('is_sub', 'Is this a Sub-Dimension?', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Sub-dimensions will be grouped under a larger, parent dimension.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::checkbox('is_sub', 1, false, [--}}
                            {{--'class' => 'icheck reveal-field',--}}
                            {{--'data-field-to-reveal' => 'field-parent'--}}
                        {{--]) !!}--}}
                        {!! Form::select('is_sub', [
                            0 => 'No',
                            1 => 'Yes'
                        ], null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <!-- Parent Dimension Field -->
            <div class="form-group field-parent">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('parent', 'Parent Dimension', ['class' => 'control-label']) !!}
                        <p class="small text-muted">If specified as a sub-dimension above, this controls what the parent dimension is.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::select('parent', $dimensionsArray, null, ['class' => 'form-control input-lg', 'id' => 'parent']) !!}
                        <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                                $("#parent").select2({
                                    placeholder: 'Select the parent dimension...',
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
</div>

<!-- Submit Field -->
<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    <div class="clearfix"></div>
</div>

<!-- Scripts -->
{{--<script src="{{ asset('js/create-assessment-form.js') }}"></script>--}}
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
        });
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>--}}
@stop