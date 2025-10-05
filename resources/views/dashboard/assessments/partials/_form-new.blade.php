@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">
    <style>
        /* Modern Assessment Editor Styles */
        .modern-editor {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .editor-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .editor-title {
            margin: 0;
            font-size: 24px;
            font-weight: 300;
        }
        
        .editor-subtitle {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .editor-content {
            padding: 30px;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .form-group-modern {
            margin-bottom: 25px;
        }
        
        .form-label-modern {
            display: block;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control-modern {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-help {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Assessment Fields Section */
        .fields-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .fields-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .fields-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .add-field-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-field-btn:hover {
            background: #229954;
            transform: translateY(-1px);
        }
        
        /* Field List */
        .field-list {
            min-height: 200px;
            border: 2px dashed #bdc3c7;
            border-radius: 8px;
            padding: 20px;
            background: white;
        }
        
        .field-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: move;
        }
        
        .field-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .field-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .field-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .field-type {
            background: #ecf0f1;
            color: #7f8c8d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .field-actions {
            display: flex;
            gap: 10px;
        }
        
        .field-action-btn {
            background: none;
            border: 1px solid #bdc3c7;
            color: #7f8c8d;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .field-action-btn:hover {
            background: #e74c3c;
            border-color: #e74c3c;
            color: white;
        }
        
        .field-content {
            margin-top: 15px;
        }
        
        .field-preview {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            font-size: 14px;
            color: #2c3e50;
        }
        
        /* Drag Handle */
        .drag-handle {
            background: #bdc3c7;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            margin-right: 15px;
        }
        
        .drag-handle:hover {
            background: #95a5a6;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #95a5a6;
        }
        
        .empty-state p {
            margin: 0;
            font-size: 14px;
        }
        
        /* Edit Modal Styles */
        .modal-lg {
            max-width: 800px;
        }
        
        .anchor-option {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .anchor-option input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .anchor-option .remove-anchor {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .anchor-option .remove-anchor:hover {
            background: #c0392b;
        }
        
        .form-group-modern {
            margin-bottom: 20px;
        }
        
        .form-label-modern {
            display: block;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control-modern {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-help {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .editor-content {
                padding: 20px;
            }
            
            .field-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .field-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
@stop

<div class="modern-editor">
    <div class="editor-header">
        <h1 class="editor-title">{{ $assessment->name }}</h1>
        <p class="editor-subtitle">Modern Assessment Editor - Drag & Drop Interface</p>
    </div>
    
    <div class="editor-content">
        <!-- Basic Information Section -->
        <div class="form-section">
            <h3 class="section-title">Basic Information</h3>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="name">Assessment Name</label>
                {!! Form::text('name', null, ['class' => 'form-control-modern', 'id' => 'name', 'placeholder' => 'Enter assessment name']) !!}
                <div class="form-help">The name that will appear in the assessment list</div>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="description">Description</label>
                {!! Form::textarea('description', null, ['class' => 'form-control-modern', 'id' => 'description', 'rows' => 4, 'placeholder' => 'Enter assessment description']) !!}
                <div class="form-help">Description that will be shown to users before they start the assessment</div>
            </div>
        </div>
        
        <!-- Assessment Fields Section -->
        <div class="fields-section">
            <div class="fields-header">
                <h3 class="fields-title">Assessment Fields</h3>
                <button type="button" class="add-field-btn" id="add-field-btn">
                    <i class="fa-plus"></i> Add Field
                </button>
            </div>
            
            <div class="field-list" id="field-list">
                @if (!empty($questions))
                    @foreach ($questions as $question)
                        @include('dashboard.assessments.partials._field-item-new', $question)
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fa-file-text-o"></i>
                        <h3>No Fields Yet</h3>
                        <p>Click "Add Field" to start building your assessment</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="form-section">
            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ url('/dashboard/assessments/'.$assessment->id) }}" class="btn btn-default" style="padding: 12px 20px;">
                    <i class="fa-eye"></i> Preview
                </a>
                {!! Form::submit($button_name, ['class' => 'btn btn-primary', 'style' => 'padding: 12px 20px;']) !!}
            </div>
        </div>
    </div>
</div>

<!-- Field Type Modal -->
<div class="modal fade" id="field-type-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select Field Type</h4>
            </div>
            <div class="modal-body">
                <div class="field-types-grid">
                    <div class="field-type-option" data-type="1">
                        <i class="fa-list-ul"></i>
                        <h4>Multiple Choice</h4>
                        <p>Single or multiple selection from options</p>
                        <small style="color: #7f8c8d; font-style: italic;">Edit modal will open to configure answer options</small>
                    </div>
                    <div class="field-type-option" data-type="2">
                        <i class="fa-paragraph"></i>
                        <h4>Description</h4>
                        <p>Text content for instructions or information</p>
                    </div>
                    <div class="field-type-option" data-type="3">
                        <i class="fa-edit"></i>
                        <h4>Text Input</h4>
                        <p>Single line text input field</p>
                    </div>
                    <div class="field-type-option" data-type="4">
                        <i class="fa-font"></i>
                        <h4>Letters</h4>
                        <p>Letter-based input field</p>
                    </div>
                    <div class="field-type-option" data-type="5">
                        <i class="fa-calculator"></i>
                        <h4>Equation</h4>
                        <p>Mathematical equation input</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Edit Modal -->
<div class="modal fade" id="field-edit-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Field</h4>
            </div>
            <div class="modal-body">
                <form id="field-edit-form">
                    <!-- Field Type Selection -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">Field Type</label>
                        <select class="form-control-modern" id="edit-field-type">
                            <option value="1">Multiple Choice</option>
                            <option value="2">Description</option>
                            <option value="3">Text Input</option>
                            <option value="4">Letters</option>
                            <option value="5">Equation</option>
                        </select>
                    </div>
                    
                           <!-- Field Content -->
                           <div class="form-group-modern">
                               <label class="form-label-modern">Field Content</label>
                               <textarea class="form-control-modern" id="edit-field-content" rows="4" placeholder="Enter field content..."></textarea>
                               <div class="form-help">Use the rich text editor above to format your content. For multiple choice questions, this is the question text.</div>
                           </div>
                    
                    <!-- Dimension Assignment -->
                    <div class="form-group-modern" id="edit-dimension-group">
                        <label class="form-label-modern">Dimension</label>
                        <select class="form-control-modern" id="edit-field-dimension">
                            <option value="">No Dimension</option>
                            @foreach ($dimensions as $dimension)
                                <option value="{{ $dimension->id }}">{{ $dimension->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-help">Assign this field to a specific dimension for scoring purposes.</div>
                    </div>
                    
                    <!-- Multiple Choice Options -->
                    <div class="form-group-modern" id="edit-anchors-group" style="display: none;">
                        <label class="form-label-modern">Anchors</label>
                        <div id="edit-anchors-container">
                            <!-- Dynamic anchor options will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="add-anchor-option">
                            <i class="fa-plus"></i> Add Option
                        </button>
                        <div class="form-help">Add answer options for multiple choice questions.</div>
                    </div>
                    
                    <!-- Practice Question Toggle -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <input type="checkbox" id="edit-practice-question" style="margin-right: 8px;">
                            Practice Question
                        </label>
                        <div class="form-help">Mark this as a practice question (won't count toward final score).</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-field-edit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/modern-assessment-editor.js') }}"></script>

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
@stop
