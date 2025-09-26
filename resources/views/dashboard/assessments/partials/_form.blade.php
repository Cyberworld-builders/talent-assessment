@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/assessment-editor.css') }}">
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <h3>Basic Info</h3><br/>

            <!-- Name Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The name of the assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <!-- Description Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The description that will appear to users before they begin the assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::textarea('description', null, ['class' => 'form-control input-lg', 'rows' => '4']) !!}
                        <div class="btn btn-small btn-black edit-description-with-wysiwyg" style="margin-top: 10px;float:right;">Edit With WYSIWYG</div>
                    </div>
                </div>
            </div>

            <h3>Appearance</h3><br/>

            <!-- Logo Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('logo', 'Logo', ['class' => 'control-label']) !!}
                        <p class="small text-muted">This will display in the header of the assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::text('logo', null, ['class' => 'form-control input-lg']) !!}--}}
                        @if ($edit and $assessment->logo)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ show_image($assessment->logo) }}" style="max-width:200px;" />
                            </div>
                        @endif
                        {!! Form::file('logo', ['id' => 'logo']) !!}
                    </div>
                </div>
            </div>

            <!-- Background Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('background', 'Background', ['class' => 'control-label']) !!}
                        <p class="small text-muted">This will display in the background of the assessment header.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::text('background', null, ['class' => 'form-control input-lg']) !!}--}}
                        @if ($edit and $assessment->background)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ show_image($assessment->background) }}" style="max-width:200px;" />
                            </div>
                        @endif
                        {!! Form::file('background', ['id' => 'background']) !!}
                    </div>
                </div>
            </div>

            <!-- Paginate Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('paginate', 'Split questions into pages?', ['class' => 'control-label']) !!}
                        <p class="small text-muted">If checked, questions will be placed onto several pages and navigation buttons added to the footer of the assessment so users can go to next or previous page.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::checkbox('paginate', 1, null, [--}}
                            {{--'class' => 'icheck reveal-field',--}}
                            {{--'data-field-to-reveal' => 'field-items-per-page'--}}
                        {{--]) !!}--}}
                        {!! Form::select('paginate', [
                            0 => 'No',
                            1 => 'Yes'
                        ], null, [
                            'class' => 'reveal-field-by-selection form-control input-lg',
                            'data-field-to-reveal' => 'field-items-per-page',
                            'style' => 'max-width:200px;'
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Items Per Page Field -->
            <div class="form-group field-items-per-page 1">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('items_per_page', 'Questions per page', ['class' => 'control-label']) !!}
                        <p class="small text-muted">This controls how many questions will be displayed before the user has to go to the next page.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::input('number', 'items_per_page', null, ['class' => 'form-control input-lg', 'style' => 'max-width:200px;']) !!}
                    </div>
                </div>
            </div>

            <br/><br/><h3>Advanced Settings</h3><br/>

            <!-- Timed Assessment Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('timed', 'Is this a timed assessment?', ['class' => 'control-label']) !!}
                        <p class="small text-muted">If yes, users will have a limited amount of time to complete the assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::checkbox('timed', 1, null, [--}}
                            {{--'class' => 'icheck reveal-field',--}}
                            {{--'data-field-to-reveal' => 'field-time-limit'--}}
                        {{--]) !!}--}}
                        {!! Form::select('timed', [
                            0 => 'No',
                            1 => 'Yes'
                        ], null, [
                            'class' => 'reveal-field-by-selection form-control input-lg',
                            'data-field-to-reveal' => 'field-time-limit',
                            'style' => 'max-width:200px;'
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Timer Field -->
            <div class="form-group field-time-limit 1" style="display: none;">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('time_limit', 'Time limit (in minutes)', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The amount of time in minutes given to users to complete the assessment. Users will have a chance to read the assessment description before pressing the begin button, which will begin the timer.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::input('number', 'time_limit', 10, ['class' => 'form-control input-lg', 'style' => 'max-width:200px;']) !!}
                    </div>
                </div>
            </div>

            <!-- Use Custom Fields Field -->
            {{--<div class="form-group">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-sm-4">--}}
                        {{--{!! Form::label('use_custom_fields', 'Use custom fields?', ['class' => 'control-label']) !!}--}}
                        {{--<p class="small text-muted">You can use custom fields anywhere in the Assessment by using the tags you specify surrounded with square brackets. For example <i>[name]</i>.</p>--}}
                    {{--</div>--}}
                    {{--<div class="col-sm-8">--}}
                        {{--{!! Form::checkbox('use_custom_fields', 1, null, [--}}
                            {{--'class' => 'icheck reveal-field',--}}
                            {{--'data-field-to-reveal' => 'field-custom-fields'--}}
                        {{--]) !!}--}}
                        {{--{!! Form::select('use_custom_fields', [--}}
                            {{--0 => 'No',--}}
                            {{--1 => 'Yes'--}}
                        {{--], null, [--}}
                            {{--'class' => 'reveal-field-by-selection form-control input-lg',--}}
                            {{--'data-field-to-reveal' => 'field-custom-fields',--}}
                            {{--'style' => 'max-width:200px;'--}}
                        {{--]) !!}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            <div class="field-custom-fields 1" style="display: none;">
                <br/><br/><h3>Custom Fields</h3><br/>

                <!-- Custom Field -->
                <div class="form-group">
                    <div class="custom-fields">
                        @if ($edit && $assessment->custom_fields)
                            @foreach ($assessment->custom_fields['tag'] as $i => $custom_field)
                                <div class="row custom-field">
                                    <div class="col-sm-3">
                                        {!! Form::label('', 'Tag') !!}
                                        {!! Form::text('custom_fields[tag][]', $custom_field, ['class' => 'form-control input-lg']) !!}
                                    </div>
                                    <div class="col-sm-3">
                                        {!! Form::label('', 'Default Value') !!}
                                        {!! Form::text('custom_fields[default][]', $assessment->custom_fields['default'][$i], ['class' => 'form-control input-lg']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        <a id="remove-custom-field"><i class="fa-times"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <br/>
                    <button class="btn btn-gray" id="add-custom-field" type="button"><i class="fa-plus"></i> Add Custom Field</button>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('target', 'Assessment Target', ['class' => 'control-label']) !!}
                        <p class="small text-muted">The target is the User to which the scores of this assessment will apply to.</p>
                    </div>
                    <div class="col-sm-8">
                        {{--{!! Form::checkbox('use_custom_fields', 1, null, [--}}
                        {{--'class' => 'icheck reveal-field',--}}
                        {{--'data-field-to-reveal' => 'field-custom-fields'--}}
                        {{--]) !!}--}}
                        {!! Form::select('target', [
                            0 => 'Self',
                            1 => 'Other User',
                            2 => 'Group Leader'
                        ], null, [
                            'class' => 'form-control input-lg',
                            'data-field-to-reveal' => 'field-custom-fields',
                            'style' => 'max-width:200px;'
                        ]) !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Your Questions -->
<div class="section">
    <h3>Add Your Questions</h3>
</div>

<!-- Questions -->
<ul class="questions uk-nestable" data-uk-nestable="{maxDepth: 1}">
    @if (! empty($questions))
        @foreach ($questions as $question)
            @include('dashboard.assessments.partials._question', $question)
        @endforeach
    @endif
</ul>

<div id="comp"></div>

<!-- Submit Field -->
<div class="form-group">
    <br/>
    <div class="pull-left">
        {!! Form::button('Add A Question', ['class' => 'btn btn-black btn-lg btn-small-font', 'id' => 'add-question']) !!}
    </div>
    <div class="pull-right">
        {{--{!! Form::button($button_name, ['class' => 'btn btn-primary btn-lg', 'id' => 'save']) !!}--}}
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    @if (! empty($assessment))
        <div class="pull-right">
            <a class="preview-link" href="{{ url('/dashboard/assessments/'.$assessment->id) }}">Preview</a>
        </div>
    @endif
    <div class="clearfix"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal Title</h4>
            </div>

            <div class="modal-body"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-small-font btn-orange save-button">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- WYSIWYG Modal -->
<div class="modal fade" id="modal-wysiwyg">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal Title</h4>
            </div>

            <div class="modal-body"><textarea id="Editor" class="form-control input-lg ">This is a sample question</textarea></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-small-font btn-orange save-button">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Simplified Modal for Advanced Editing -->
<div id="comp"></div>

<!-- Scripts -->
<script src="{{ asset('js/assessment-editor.js') }}"></script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>
    <script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>
    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
    <!-- React dependencies removed - using simplified approach -->
    <!-- Simplified MathJax for basic math support -->
    <script type="text/javascript" src="{{ asset('assets/js/mathjax/MathJax.js?config=AM_HTMLorMML') }}"></script>
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            showProcessingMessages: false,
            tex2jax: {inlineMath: [['`','`']]}
        });
    </script>
@stop