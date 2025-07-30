@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">
    <style>
        .col-sm-6 p {
            font-size: 18px;
        }
        .col-sm-6.description p {
            font-size: 14px;
        }
        .col-sm-6.description h1,
        .col-sm-6.description h2,
        .col-sm-6.description h3 {
            font-size: 18px;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <h3>Basic Info</h3><br/>

            <!-- Language Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('language_id', 'What language is your translation?', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Only languages that haven't been translated will be available.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::select('language_id', $languages_array, null, ['class' => 'form-control input-lg']) !!}
                        {{--<script type="text/javascript">--}}
                        {{--jQuery(document).ready(function($)--}}
                        {{--{--}}
                        {{--$("#language").selectBoxIt({--}}
                        {{--showFirstOption: false,--}}
                        {{--});--}}
                        {{--});--}}
                        {{--</script>--}}
                        <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                                $("#language_id").select2({
                                    placeholder: 'Select your language...',
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

            <!-- Name Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {{--<p>{{ $assessment->name }}</p>--}}
                        {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Name of the assessment.</p>
                    </div>
                    <div class="col-sm-8">
                        {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <br/><br/><h3>Description</h3><br/>

            <!-- Description Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 description">
{{--                        {!! Form::label('desc', 'Description', ['class' => 'control-label']) !!}--}}
                        {!! $assessment->description !!}
                    </div>
                    <div class="col-sm-6">
                        {!! Form::textarea('description', null, ['class' => 'form-control input-lg edit-description-with-wysiwyg', 'rows' => '4']) !!}
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <br/><br/><h3>Questions</h3><br/>

            @foreach ($questions as $question)
                <?php if (! $question->isTranslatable()) continue; ?>
                <div class="question">

                    {{-- Question ID --}}
                    @if ($edit)
                        {!! Form::hidden('', $translated_questions->where('question_id', $question->id)->first()->id, ['id' => 'id']) !!}
                    @else
                        {!! Form::hidden('', $question->id, ['id' => 'id']) !!}
                    @endif

                    {{-- Type --}}
                    {!! Form::hidden('', $question->type, ['id' => 'type']) !!}

                    @if ($question->type == 1 or $question->type == 2 or $question->type == 3)

                        <!-- Content Field -->
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>{!! $question->content !!}</p>
                                </div>
                                <div class="col-sm-6">
                                    <div class="edit-with-wysiwyg" style="position: absolute; right: 27px; font-size: 18px; top: 12px;">
                                        <a data-original-title="Advanced Editor" data-toggle="tooltip" title="">
                                            <i class="fa-edit"></i>
                                        </a>
                                    </div>
                                    @if ($edit)
                                        {!! Form::textarea('', $translated_questions->where('question_id', $question->id)->first()->content, ['class' => 'form-control input-lg', 'rows' => '4', 'id' => 'content']) !!}
                                    @else
                                        {!! Form::textarea('', null, ['class' => 'form-control input-lg', 'rows' => '4', 'id' => 'content']) !!}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Anchors Field -->
                        @if ($question->anchors)
                            <div class="form-group anchors">
                                @foreach ($question->anchors as $i => $anchor)
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="alert alert-default anchor-original" style="margin-bottom: 3px;">{!! $anchor['tag'] !!}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            @if ($edit)
                                                {!! Form::text('', $translated_questions->where('question_id', $question->id)->first()->anchors[$i], ['class' => 'form-control input-lg anchor']) !!}
                                            @else
                                                {!! Form::text('', null, ['class' => 'form-control input-lg anchor']) !!}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    @if ($question->type == 10)
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6 description">
                                    <p>{!! json_decode($question->content)->text !!}</p><br/>
                                    <div class="btn btn-black">{!! json_decode($question->content)->next !!}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="edit-with-wysiwyg" style="position: absolute; right: 27px; font-size: 18px; top: 12px;">
                                        <a data-original-title="Advanced Editor" data-toggle="tooltip" title="">
                                            <i class="fa-edit"></i>
                                        </a>
                                    </div>
                                    @if ($edit)
                                        <h4>Text</h4>
                                        {!! Form::textarea('', json_decode($translated_questions->where('question_id', $question->id)->first()->content)->text, ['class' => 'form-control input-lg', 'rows' => '4', 'id' => 'content']) !!}
                                        <h4>Button</h4>
                                        {!! Form::text('', json_decode($translated_questions->where('question_id', $question->id)->first()->content)->next, ['class' => 'form-control input-lg', 'id' => 'button']) !!}
                                    @else
                                        <h4>Text</h4>
                                        {!! Form::textarea('', null, ['class' => 'form-control input-lg', 'rows' => '4', 'id' => 'content']) !!}
                                        <h4>Button</h4>
                                        {!! Form::text('', null, ['class' => 'form-control input-lg', 'id' => 'button']) !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach

            <!-- Submit Field -->
            <div class="form-group">
                <br/>
                <div class="pull-left">
                    {{--        {!! Form::button('Add A Question', ['class' => 'btn btn-black btn-lg btn-small-font', 'id' => 'add-question']) !!}--}}
                </div>
                <div class="pull-right">
                    {!! Form::button($button_name, ['class' => 'btn btn-primary btn-lg', 'id' => 'save']) !!}
                </div>
                {{--@if (! empty($assessment))--}}
                {{--<div class="pull-right">--}}
                {{--<a class="preview-link" href="{{ url('/dashboard/assessments/'.$assessment->id) }}">Preview</a>--}}
                {{--</div>--}}
                {{--@endif--}}
                <div class="clearfix"></div>
            </div>

        </div>
    </div>
</div>

<!-- WYSIWYG Modal -->
<div class="modal fade" id="modal-wysiwyg">
    <div class="modal-dialog" style="width: 60%;">
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

<!-- Scripts -->
<script src="{{ asset('js/translate-assessment-form.js') }}"></script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>--}}
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
@stop