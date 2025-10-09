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
            margin-left: 40px;
            margin-right: 20px;
        }
        
        /* Tab Alignment */
        .nav-tabs {
            margin-left: 40px;
            margin-right: 20px;
            border-bottom: 1px solid #ddd;
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
        
        .field-actions {
            display: flex;
            gap: 10px;
            align-items: center;
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
        
        .sort-fields-btn {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sort-fields-btn:hover {
            background: #7f8c8d;
            transform: translateY(-1px);
        }
        
        .dimension-indicator {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            box-shadow: none;
            border: none;
        }
        
        .dimension-indicator i {
            display: none;
        }
        
        /* Delete Confirmation Modal */
        .delete-confirmation-content {
            text-align: center;
        }
        
        .field-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            text-align: left;
        }
        
        .field-info h5 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        
        .field-info p {
            margin: 0;
            color: #6c757d;
            font-size: 13px;
        }
        
        .delete-status {
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
        }
        
        .delete-status.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .delete-status.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .delete-status.info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
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
        
        .field-header-left {
            display: flex;
            align-items: center;
        }
        
        .field-type-badge {
            margin-left: 10px;
        }
        
        .field-actions {
            display: flex;
            gap: 10px;
        }
        
        .field-content {
            margin-top: 15px;
        }
        
        .field-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            font-size: 14px;
            color: #2c3e50;
            line-height: 1.5;
        }
        
        .field-dimension {
            margin-top: 10px;
            padding: 8px 12px;
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 4px;
            font-size: 12px;
            color: #1976d2;
        }
        
        .add-field-section {
            margin-top: 20px;
            text-align: center;
        }
        
        .add-field-section .btn {
            margin: 0 10px;
        }
        
        /* Field Types Grid */
        .field-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        
        .field-type-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
        }
        
        .field-type-option:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        
        .field-type-option i {
            font-size: 32px;
            color: #667eea;
            margin-bottom: 10px;
            display: block;
        }
        
        .field-type-option h4 {
            margin: 10px 0 5px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .field-type-option p {
            color: #7f8c8d;
            font-size: 13px;
            margin: 0;
        }
        
        .field-type-option small {
            display: block;
            margin-top: 8px;
            font-size: 11px;
        }
        
        .field-number {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #dee2e6;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 12px;
            cursor: default;
            user-select: none;
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
        
        /* Modal Fixes */
        .modal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1055 !important;
        }
        
        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1050 !important;
            width: 100vw !important;
            height: 100vh !important;
        }
        
        .modal-dialog {
            position: relative !important;
            margin: 30px auto !important;
            max-height: calc(100vh - 60px) !important;
            overflow: hidden !important;
            z-index: 1060 !important;
        }
        
        .modal-content {
            max-height: calc(100vh - 60px) !important;
            display: flex !important;
            flex-direction: column !important;
            z-index: 1065 !important;
        }
        
        .modal-body {
            overflow-y: auto !important;
            flex: 1 !important;
            max-height: calc(100vh - 200px) !important;
        }
        
        .modal-header,
        .modal-footer {
            flex-shrink: 0 !important;
        }
        
        /* Ensure modal backdrop covers entire viewport */
        body.modal-open {
            overflow: hidden !important;
        }
        
        /* Fix for long content in modal body */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Make form fields narrower to fit modal better */
        .modal-body .form-group input[type="text"],
        .modal-body .form-group input[type="number"],
        .modal-body .form-group textarea,
        .modal-body .form-group select {
            max-width: 100% !important;
            width: 100% !important;
        }
        
        .modal-body .form-group .col-sm-6,
        .modal-body .form-group .col-sm-4,
        .modal-body .form-group .col-sm-3 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
        
        /* Make anchor input fields more compact */
        .modal-body .anchor-option {
            display: flex !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
            align-items: center !important;
        }
        
        .modal-body .anchor-option input[type="text"] {
            flex: 2 !important;
            min-width: 0 !important;
            max-width: 200px !important;
        }
        
        .modal-body .anchor-option input[type="number"] {
            flex: 1 !important;
            min-width: 0 !important;
            max-width: 80px !important;
        }
        
        .modal-body .anchor-option .remove-anchor {
            flex-shrink: 0 !important;
            padding: 6px 8px !important;
            margin-left: 5px !important;
        }
        
        /* Ensure modal content doesn't overflow */
        .modal-body {
            overflow-x: hidden !important;
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
            
            .modal-dialog {
                margin: 10px auto !important;
                max-height: calc(100vh - 20px) !important;
            }
            
            .modal-content {
                max-height: calc(100vh - 20px) !important;
            }
            
            .modal-body {
                max-height: calc(100vh - 160px) !important;
            }
        }
        
        /* Table styling for rich text content */
        .field-preview table,
        .field-content table,
        .description-preview table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .field-preview table td,
        .field-preview table th,
        .field-content table td,
        .field-content table th,
        .description-preview table td,
        .description-preview table th {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .field-preview table th,
        .field-content table th,
        .description-preview table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .field-preview table tr:nth-child(even),
        .field-content table tr:nth-child(even),
        .description-preview table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .field-preview table tr:hover,
        .field-content table tr:hover,
        .description-preview table tr:hover {
            background-color: #e9ecef;
        }
    </style>
@stop

<div class="modern-editor">
    <div class="editor-header">
        <h1 class="editor-title">{{ $assessment->name }}</h1>
        <p class="editor-subtitle">Modern Assessment Editor - Drag & Drop Interface</p>
    </div>
    
    <div class="editor-content">
        <!-- Hidden dimension data for JavaScript -->
        {!! Form::hidden('dimension_data', json_encode($assessment->dimensions->toArray())) !!}
        
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
        
        <!-- jQuery Assessment Editor -->
        <div class="form-section">
            <h3 class="section-title">Assessment Fields</h3>
            <p class="section-description">Add and organize your assessment fields. Drag to reorder.</p>
            
            <!-- Field List -->
            <div class="field-list" id="field-list">
                @if(isset($questions) && count($questions) > 0)
                    @foreach($questions as $index => $question)
                        <div class="field-item" data-field-type="{{ $question['type'] }}" data-id="{{ $question['id'] ?? '' }}">
                            <div class="field-header">
                                <div class="field-header-left">
                                    <div class="drag-handle">
                                        <i class="fa-bars"></i>
                                    </div>
                                    <div class="field-number">{{ $index + 1 }}</div>
                                </div>
                                <div class="field-actions">
                                    <button type="button" class="field-action-btn edit-field" title="Edit Field">
                                        <i class="fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="field-action-btn duplicate-field" title="Duplicate Field">
                                        <i class="fa-copy"></i> Duplicate
                                    </button>
                                    <button type="button" class="field-action-btn remove-field" title="Remove Field">
                                        <i class="fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                            
                            <div class="field-content">
                                <div class="field-preview">
                                    @if ($question['type'] == 2)
                                        <!-- Description Field -->
                                        <div class="description-preview">
                                            <strong>Description:</strong>
                                            <div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;">
                                                {!! preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $question['content'])) !!}
                                            </div>
                                        </div>
                                    @elseif ($question['type'] == 1)
                                        <!-- Multiple Choice Field -->
                                        <div class="multiple-choice-preview">
                                            <strong>Multiple Choice Question:</strong>
                                            <div style="margin-top: 8px;">
                                                <div style="margin-bottom: 5px; font-weight: 500;">{!! $question['content'] !!}</div>
                                                @if (isset($question['anchors']) && !empty($question['anchors']))
                                                    <div style="margin-top: 10px;">
                                                        <small style="color: #7f8c8d; font-weight: 600;">Anchors:</small>
                                                        <div style="margin-top: 5px;">
                                                            @foreach ($question['anchors'] as $anchor)
                                                                <div style="margin: 3px 0; padding: 6px 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; display: inline-block; margin-right: 8px; font-size: 13px;">
                                                                    <i class="fa-circle-o" style="margin-right: 5px; color: #6c757d;"></i>{{ $anchor['tag'] }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif ($question['type'] == 3)
                                        <!-- Text Input Field -->
                                        <div class="text-input-preview">
                                            <strong>Text Input:</strong>
                                            <div style="margin-top: 8px;">
                                                <div style="margin-bottom: 5px; font-weight: 500;">{!! $question['content'] !!}</div>
                                                <div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
                                                    <input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%; color: #6c757d; font-style: italic;" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Other Field Types -->
                                        <div class="other-field-preview">
                                            <strong>{{ \App\Question::getTypeDescription($question['type']) }}:</strong>
                                            <div style="margin-top: 8px;">
                                                {!! preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $question['content'])) !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Hidden form fields for submission -->
                            @if ($question['id'] ?? false)
                                <input type="hidden" name="field_id[]" value="{{ $question['id'] }}">
                            @endif
                            <input type="hidden" name="field_type[]" value="{{ $question['type'] }}">
                            <input type="hidden" name="field_content[]" value="{{ $question['content'] }}">
                            <input type="hidden" name="field_number[]" value="{{ $index + 1 }}">
                            @if (isset($question['anchors']))
                                <input type="hidden" name="field_anchors[]" value="{{ json_encode($question['anchors']) }}">
                            @endif
                            @if (isset($question['dimension_id']))
                                <input type="hidden" name="field_dimension[]" value="{{ $question['dimension_id'] }}">
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fa-file-text-o"></i>
                        <h3>No Fields Yet</h3>
                        <p>Click "Add Field" to start building your assessment</p>
                    </div>
                @endif
            </div>
            
            <!-- Add Field Button -->
            <div class="add-field-section">
                <button type="button" class="btn btn-primary btn-lg" id="add-field-btn">
                    <i class="fa-plus"></i> Add Field
                </button>
                <button type="button" class="btn btn-secondary" id="sort-fields-btn">
                    <i class="fa-sort"></i> Sort by Dimension
                </button>
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

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/modern-assessment-editor.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>
    <script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>
    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
@stop

<!-- Field Edit Modal -->
<div class="modal fade" id="field-edit-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Field</h4>
            </div>
            <div class="modal-body">
                <div id="field-edit-form">
                    <!-- Field Type Display (Read-only) -->
                    <div class="form-group-modern">
                        <label class="form-label-modern">Field Type</label>
                        <div class="form-control-modern" id="edit-field-type-display" style="background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 12px 15px; border-radius: 6px; color: #6c757d;">
                            <i class="fa fa-info-circle" style="margin-right: 8px;"></i>
                            <span id="edit-field-type-text">Loading...</span>
                        </div>
                        <div class="form-help">Field type cannot be changed. To change the field type, delete this field and create a new one.</div>
                    </div>
                    
                           <!-- Field Content -->
                           <div class="form-group-modern">
                               <label class="form-label-modern">Field Content <span style="color: #e74c3c;">*</span></label>
                               <textarea class="form-control-modern" id="edit-field-content" name="edit-field-content" rows="4" placeholder="Enter field content..."></textarea>
                               <div class="form-help">Use the rich text editor above to format your content. For multiple choice questions, this is the question text.</div>
                           </div>
                    
                    <!-- Dimension Assignment -->
                    <div class="form-group-modern" id="edit-dimension-group">
                        <label class="form-label-modern">Dimension <span style="color: #e74c3c;">*</span></label>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-field-edit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete-confirmation-modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Field</h4>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation-content">
                    <p>Are you sure you want to delete this field?</p>
                    <div class="field-info" id="delete-field-info">
                        <!-- Field information will be populated here -->
                    </div>
                    <div class="delete-status" id="delete-status" style="display: none;">
                        <!-- Status messages will appear here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete Field</button>
            </div>
        </div>
    </div>
</div>

