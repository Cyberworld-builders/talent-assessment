@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">
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

<!-- React Modal -->
<div id="comp"></div>

<!-- Scripts -->
<script src="{{ asset('js/create-assessment-form.js') }}"></script>

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
    <script src="https://fb.me/react-0.13.3.js"></script>
    <script src="https://fb.me/JSXTransformer-0.13.3.js"></script>
    <script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML"></script>
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            showProcessingMessages: false,
            tex2jax: {inlineMath: [['`','`']]}
        });
    </script>
    <script type="text/jsx" src="{{ asset('js/create-wm.js') }}"></script>
    <script type="text/jsx">
        var WMModal = React.createClass({
            statics: {
                domId: 'probForm',
                domIdSel: '#probForm'
            },
            getInitialState: function() {
                return {
                    editContext: {mode: 1}
                };
            },
            showModal: function(newContext) {
                this.setState({
                    editContext: newContext
                }, function() {
                    $(WMModal.domIdSel).modal('show');
                });
            },
            onProbFormSave: function() {
                switch (this.state.editContext.widget.props.type) {
                    case WMWidget.types.instruct:
                        this.saveInstruct();
                        break;
                    case WMWidget.types.sq:
                        this.saveSQ();
                        break;
                    case WMWidget.types.sy:
                        this.saveSY();
                        break;
                    case WMWidget.types.sysq:
                        this.saveSYSQ();
                        break;
                }

                $(WMModal.domIdSel).modal('hide');
            },
            saveInstruct: function() {
                var text = $(WMModal.domIdSel + ' #instText').val().trim();
                var next = $(WMModal.domIdSel + ' #instNext').val().trim();
                this.state.editContext.prob.text = text;
                this.state.editContext.prob.next = next;
                this.state.editContext.widget.setState({problem: this.state.editContext.prob});
                this.state.editContext.widget.state.question.find('#content').html(JSON.stringify({text:text,next:next}));
            },
            saveSQ: function() {
                var json = $(WMModal.domIdSel + ' #squares').val().trim();
                this.state.editContext.prob.squares = JSON.parse(json);
                this.state.editContext.widget.setState({problem: this.state.editContext.prob});
                this.state.editContext.widget.state.question.find('#content').html(json);
            },
            saveSY: function() {
                var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
                this.state.editContext.prob.symmetry = JSON.parse(json);
                this.state.editContext.widget.setState({problem: this.state.editContext.prob});
                this.state.editContext.widget.state.question.find('#content').html(json);
            },
            saveSYSQ: function() {

                // Edit squares
                if (this.state.editContext.subType == 0) {
                    var json = $(WMModal.domIdSel + ' #squares').val().trim();
                    this.state.editContext.prob.squares = JSON.parse(json);
                    this.state.editContext.widget.setState({
                        problem: {
                            squares: this.state.editContext.prob.squares,
                            symmetries: this.state.editContext.prob.symmetries,
                        }
                    });
                    this.state.editContext.widget.state.question.find('#content').html(JSON.stringify(this.state.editContext.prob));
                }

                // Edit one of the symmetries
                else if (this.state.editContext.subType == 1) {
                    var json = $(ProbForm.domIdSel + ' #symmetry').val().trim();
                    this.state.editContext.prob.symmetries[this.state.editContext.key] = JSON.parse(json);
                    this.state.editContext.widget.setState({
                        problem: {
                            squares: this.state.editContext.prob.squares,
                            symmetries: this.state.editContext.prob.symmetries,
                        }
                    });
                    this.state.editContext.widget.state.question.find('#content').html(JSON.stringify(this.state.editContext.prob));
                }

                // Show a choice for length of problems
                else {
                    var length = $(ProbForm.domIdSel + ' #length').val();
                    this.state.editContext.prob = {
                        squares: SQ.makeRandomFigure(length),
                        symmetries: SYSQ.makeSymmetryFigures(length)
                    };
                    this.state.editContext.widget.setState({problem: this.state.editContext.prob});
                    this.state.editContext.widget.state.question.find('#content').html(JSON.stringify(this.state.editContext.prob));
                }
            },
            render: function() {
                return (
                    <div className="modal fade" id={WMModal.domId} tabIndex="-1" role="dialog" labelledby="myModalLabel">
                        <div className="modal-dialog" role="document">
                            <div className="modal-content">
                                <div className="modal-header">
                                    <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 className="modal-title" id="myModalLabel">Edit Problem</h4>
                                </div>
                                <WMModal.Body editContext={this.state.editContext} />
                                <div className="modal-footer">
                                    <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button type="button" className="btn btn-primary" onClick={this.onProbFormSave}>Ok</button>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            }
        });

        WMModal.Body = React.createClass({
            propTypes: {
                editContext: React.PropTypes.object.isRequired
            },
            render: function() {
                return (
                    <div className="modal-body">
                        <WMModal.ProblemForm type={this.props.editContext.widget ? this.props.editContext.widget.props.type : null} editContext={this.props.editContext}/>
                    </div>
                );
            }
        });

        WMModal.ProblemForm = React.createClass({
            propTypes: {
                type: React.PropTypes.number.isRequired,
                editContext: React.PropTypes.object.isRequired
            },
            render: function() {
                switch(this.props.type) {
                    case WMWidget.types.instruct:
                        return <InstForm.Body editContext={this.props.editContext} />
                    case WMWidget.types.sq:
                        return <ProbForm.SQPane editContext={this.props.editContext} />
                    case WMWidget.types.sy:
                        return <ProbForm.SYPane editContext={this.props.editContext}/>
                    case WMWidget.types.sysq:
                        if (this.props.editContext.subType == null)
                            return <ProbForm.SYSQPane editContext={this.props.editContext}/>
                        else if (this.props.editContext.subType == 0)
                            return <ProbForm.SQPane editContext={this.props.editContext} />
                        else if (this.props.editContext.subType == 1)
                            return <ProbForm.SYPane editContext={this.props.editContext}/>
                    default:
                        return null;
                }
            }
        });

        var WMWidget = React.createClass({
            propTypes: {
                question: React.PropTypes.object.isRequired,
                json: React.PropTypes.string.isRequired,
                type: React.PropTypes.number.isRequired
            },
            statics: {
                mode: {
                    add: 0,
                    edit: 1
                },
                types: {
                    eqls: 6,
                    sq: 7,
                    sy: 8,
                    sysq: 9,
                    instruct: 10,
                }
            },
            getInitialState: function() {
                var problem;
                var question = this.props.question;

                if (this.props.type == WMWidget.types.instruct)
                {
                    var json = JSON.parse(this.props.json);

                    if (typeof json['text'] === 'undefined')
                        json['text'] = 'Here are some instructions. Click HERE to edit this text.';
                    if (typeof json['next'] === 'undefined')
                        json['next'] = 'Continue';

                    problem = {
                        text: json['text'],
                        next: json['next']
                    };
                }

                if (this.props.type == WMWidget.types.eqls)
                {
                    var json = JSON.parse(this.props.json);
                    if (typeof json['letters'] === 'undefined')
                        json['letters'] = [];
                    if (typeof json['equations'] === 'undefined')
                        json['equations'] = [];

                    problem = {
                        letters: json['letters'],
                        equations: json['equations'],
                    }
                }

                if (this.props.type == WMWidget.types.sq)
                    problem = {squares: JSON.parse(this.props.json)};

                if (this.props.type == WMWidget.types.sy)
                    problem = {symmetry: JSON.parse(this.props.json)};

                if (this.props.type == WMWidget.types.sysq)
                {
                    var show_edit_form = false;
                    var json = JSON.parse(this.props.json);

                    if (json.length == 0)
                        show_edit_form = true;
                    if (typeof json['squares'] === 'undefined')
                        json['squares'] = [];
                    if (typeof json['symmetries'] === 'undefined')
                        json['symmetries'] = [[]];

                    problem = {
                        squares: json['squares'],
                        symmetries: json['symmetries']
                    };

                    if (show_edit_form)
                    {
                        Modal.showModal({
                            mode: WMWidget.mode.edit,
                            prob: problem,
                            widget: this,
                            question: question,
                            subType: null,
                            key: null,
                        });
                    }
                }

                return {
                    problem: problem,
                    question: question,
                    editContext: {mode: WMWidget.mode.edit},
                };
            },
            onProbEdit: function(subType = null, key = null) {
                this.setState({
                    editContext: {
                        mode: WMWidget.mode.edit,
                        prob: this.state.problem,
                        widget: this,
                        question: this.state.question,
                        subType: subType,
                        key: key,
                        inst: {
                            text: this.state.problem.text,
                            next: this.state.problem.next,
                        }
                    }
                }, function() {
                    Modal.showModal(this.state.editContext);
                });
            },
            saveProb: function(state) {
                this.state.question.find('#content').html(JSON.stringify(state));
            },
            render: function() {
                switch(this.props.type) {
                    case WMWidget.types.instruct:
                        return <WMWidget.InstructionsWidget text={this.state.problem.text} next={this.state.problem.next} onProbEdit={this.onProbEdit} />
                    case WMWidget.types.eqls:
                        return <WMWidget.MathLettersWidget letters={this.state.problem.letters} equations={this.state.problem.equations} onSaveProb={this.saveProb} />
                    case WMWidget.types.sq:
                        return <Block.Table.Row.SquaresWidget squares={this.state.problem.squares} onProbEdit={this.onProbEdit}/>
                    case WMWidget.types.sy:
                        return <Block.Table.Row.SymmetryWidget symmetry={this.state.problem.symmetry} onProbEdit={this.onProbEdit}/>
                    case WMWidget.types.sysq:
                        return <WMWidget.SymmetrySquaresWidget squares={this.state.problem.squares} symmetries={this.state.problem.symmetries} onProbEdit={this.onProbEdit}/>
                    default:
                        return null;
                }
            }
        });

        WMWidget.InstructionsWidget = React.createClass({
            propTypes: {
                text: React.PropTypes.string.isRequired,
                next: React.PropTypes.string.isRequired,
                onProbEdit: React.PropTypes.func.isRequired,
            },
            getInitialState: function() {
                return {
                    divStyle: {
                        textAlign: 'center'
                    },
                    wellStyle: {
                        marginBottom: 5
                    }
                }
            },
            render: function() {
                return (
                    <div style={this.state.divStyle} onClick={this.props.onProbEdit}>
                        <div className="well well-sm" style={this.state.wellStyle}>{this.props.text}</div>
                        <a href="#null" className="btn btn-small btn-gray">{this.props.next}</a>
                    </div>
                );
            }
        });

        WMWidget.MathLettersWidget = React.createClass({
            propTypes: {
                letters: React.PropTypes.array.isRequired,
                equations: React.PropTypes.array.isRequired,
                onSaveProb: React.PropTypes.func.isRequired,
            },
            getInitialState: function() {
                return {
                    equations: this.props.equations,
                    letters: this.props.letters,
                };
            },
            updateLetters: function(evt) {
                this.setState({
                    letters: evt.target.value.split(","),
                }, function() {
                    console.log(this.state);
                    //this.props.onSaveProb(this.state);
                });
            },
            updateEquations: function(values) {
                this.setState({
                    equations: values
                }, function() {
                    this.props.onSaveProb(this.state);
                });
            },
            render: function() {
                return (
                    <span>
                        <input value={this.state.letters.join()} className="form-control input-lg" onChange={this.updateLetters} />
                        <WMWidget.Equations letters={this.state.letters} equations={this.state.equations} onUpdate={this.updateEquations} />
                    </span>
                );
            }
        });

        WMWidget.Equations = React.createClass({
            propTypes: {
                letters: React.PropTypes.array.isRequired,
                equations: React.PropTypes.array.isRequired,
                onUpdate: React.PropTypes.func.isRequired,
            },
            storeEquations: function() {
                var values = [];

                $('input', this.refs.inputs.getDOMNode()).each(function(){
                    var value = $(this).val();
                    values.push(value);
                });

                this.props.onUpdate(values);
            },
            render: function() {
                var equations = this.props.equations;
                return (
                    <div className="equations" ref="inputs" onChange={this.storeEquations}>
                        {
                            this.props.letters.map(function(letter, index) {
                                return (
                                    <div className="input-group" key={index}>
                                        <span className="input-group-addon">{letter}</span>
                                        <input className="form-control" value={equations[index]} />
                                    </div>
                                );
                            })
                        }
                    </div>
                );
            }
        });

        WMWidget.SymmetrySquaresWidget = React.createClass({
            propTypes: {
                squares: React.PropTypes.array.isRequired,
                symmetries: React.PropTypes.array.isRequired,
                onProbEdit: React.PropTypes.func.isRequired,
            },
            getInitialState: function() {
                return {
                    style: {display: 'inline-block'}
                };
            },
            render: function() {
                var squares = <Block.Table.Row.SquaresWidget.Figure squares={this.props.squares} onProbEdit={this.props.onProbEdit.bind(null, 0)}/>

                var symmetries = this.props.symmetries.map(function(symmetry, i) {
                    return <Block.Table.Row.SymmetryWidget.Figure key={i} symmetry={symmetry} onProbEdit={this.props.onProbEdit.bind(null, 1, i)}/>
                }.bind(this));

                return (
                    <div>
                        <div style={this.state.style}>{squares}<div>&nbsp;</div></div>
                        {symmetries}
                    </div>
                );
            }
        });
    </script>
    <script type="text/jsx">
        var Modal = React.render(React.createElement(WMModal), document.getElementById('comp'));

        $('.react-comp').each(function(){
            var $question = $(this).closest('.question');
            var type = $(this).attr('data-type');
            var json = $(this).attr('data-json');
            add_wm_widget($question, type, json, $(this)[0]);
        });

        function add_wm_widget($question, type, json = null, dom_element = null)
        {
            if (!json)
                json = "[]";

            if (!dom_element)
            {
                $question.find('#content').after('<div class="react-comp" data-type="'+type+'" data-json="'+json+'"><i class="fa-spinner fa-spin"></i></div>');
                $comp = $question.find('.react-comp');
                dom_element = $comp[0];
            }

            React.render(React.createElement(WMWidget, {question: $question, json: json, type: parseInt(type)}), dom_element);
        }
    </script>
@stop