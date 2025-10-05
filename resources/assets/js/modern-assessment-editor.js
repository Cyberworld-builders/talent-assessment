jQuery(document).ready(function($) {
    
    // Initialize the modern assessment editor
    console.log('Modern Assessment Editor initialized');
    
    // Field counter for numbering
    let fieldCounter = 1;
    
    // Initialize drag and drop
    initDragAndDrop();
    
    // Add field button click handler
    $('#add-field-btn').on('click', function() {
        showFieldTypeModal();
    });
    
    // Field type selection
    $(document).on('click', '.field-type-option', function() {
        const fieldType = $(this).data('type');
        addNewField(fieldType);
        $('#field-type-modal').modal('hide');
    });
    
    // Field actions
    $(document).on('click', '.edit-field', function() {
        const $field = $(this).closest('.field-item');
        editField($field);
    });
    
    // Edit modal handlers
    $(document).on('change', '#edit-field-type', function() {
        const fieldType = $(this).val();
        toggleEditFields(fieldType);
    });
    
    // Initialize CKEditor when modal is shown
    $('#field-edit-modal').on('shown.bs.modal', function() {
        if (typeof CKEDITOR !== 'undefined' && !CKEDITOR.instances['edit-field-content']) {
            CKEDITOR.replace('edit-field-content', {
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize', 'Source'] }
                ],
                height: 200,
                width: '100%'
            });
        }
    });
    
    // Destroy CKEditor when modal is hidden
    $('#field-edit-modal').on('hidden.bs.modal', function() {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['edit-field-content']) {
            CKEDITOR.instances['edit-field-content'].destroy();
        }
    });
    
    $(document).on('click', '#add-anchor-option', function() {
        addAnchorOption();
    });
    
    $(document).on('click', '.remove-anchor', function() {
        $(this).closest('.anchor-option').remove();
    });
    
    $(document).on('click', '#save-field-edit', function() {
        saveFieldEdit();
    });
    
    $(document).on('click', '.duplicate-field', function() {
        const $field = $(this).closest('.field-item');
        duplicateField($field);
    });
    
    $(document).on('click', '.remove-field', function() {
        const $field = $(this).closest('.field-item');
        removeField($field);
    });
    
    // Form submission
    $('form').on('submit', function(e) {
        e.preventDefault();
        saveAssessment();
    });
    
    // Initialize field numbering
    updateFieldNumbers();
    
    /**
     * Initialize drag and drop functionality
     */
    function initDragAndDrop() {
        $('#field-list').sortable({
            handle: '.drag-handle',
            placeholder: 'field-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            start: function(e, ui) {
                ui.placeholder.html('<div style="padding: 20px; text-align: center; color: #7f8c8d; border: 2px dashed #bdc3c7; border-radius: 8px; background: #f8f9fa;"><i class="fa-arrows"></i> Drop field here</div>');
            },
            stop: function(e, ui) {
                updateFieldNumbers();
            }
        });
    }
    
    /**
     * Show field type selection modal
     */
    function showFieldTypeModal() {
        $('#field-type-modal').modal('show');
    }
    
    /**
     * Add a new field of the specified type
     */
    function addNewField(fieldType) {
        const $template = $('#field-template-' + fieldType);
        const $newField = $template.clone();
        
        // Update field number
        $newField.find('.field-number').text(fieldCounter);
        $newField.find('input[name="field_number[]"]').val(fieldCounter);
        
        // Remove template ID and add to field list
        $newField.removeAttr('id');
        $newField.appendTo('#field-list');
        
        // Update counter
        fieldCounter++;
        
        // Hide empty state if it exists
        $('.empty-state').hide();
        
        // Add animation
        $newField.hide().fadeIn(300);
        
        // For multiple choice fields, automatically open edit modal to configure options
        if (fieldType == 1) {
            setTimeout(function() {
                editField($newField);
            }, 350); // Wait for fade-in animation to complete
        }
        
        console.log('Added new field of type:', fieldType);
    }
    
    /**
     * Edit a field
     */
    function editField($field) {
        const fieldType = $field.data('type');
        const fieldContent = $field.find('input[name="field_content[]"]').val();
        const fieldDimension = $field.find('input[name="field_dimension[]"]').val() || '';
        const fieldAnchors = $field.find('input[name="field_anchors[]"]').val();
        const fieldId = $field.find('input[name="field_id[]"]').val() || '';
        
        // Store reference to the field being edited
        window.currentEditingField = $field;
        
        // Populate edit modal
        $('#edit-field-type').val(fieldType);
        $('#edit-field-dimension').val(fieldDimension);
        
        // Handle anchors for multiple choice
        if (fieldType == 1 && fieldAnchors) {
            try {
                const anchors = JSON.parse(fieldAnchors);
                populateAnchors(anchors);
            } catch (e) {
                console.error('Error parsing anchors:', e);
            }
        }
        
        // Toggle fields based on type
        toggleEditFields(fieldType);
        
        // Show modal first, then set content after CKEditor is initialized
        $('#field-edit-modal').modal('show');
        
        // Set content after a short delay to ensure CKEditor is ready
        setTimeout(function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['edit-field-content']) {
                CKEDITOR.instances['edit-field-content'].setData(fieldContent);
            } else {
                $('#edit-field-content').val(fieldContent);
            }
        }, 500);
    }
    
    /**
     * Toggle edit fields based on field type
     */
    function toggleEditFields(fieldType) {
        const $anchorsGroup = $('#edit-anchors-group');
        const $dimensionGroup = $('#edit-dimension-group');
        
        // Show/hide anchors for multiple choice
        if (fieldType == 1) {
            $anchorsGroup.show();
        } else {
            $anchorsGroup.hide();
        }
        
        // Show/hide dimension for non-description fields
        if (fieldType == 2) {
            $dimensionGroup.hide();
        } else {
            $dimensionGroup.show();
        }
    }
    
    /**
     * Add anchor option
     */
    function addAnchorOption() {
        const $container = $('#edit-anchors-container');
        const optionCount = $container.find('.anchor-option').length;
        
        const $option = $(`
            <div class="anchor-option">
                <input type="text" placeholder="Option text" class="anchor-text" value="">
                <input type="number" placeholder="Value" class="anchor-value" value="${optionCount + 1}">
                <button type="button" class="remove-anchor">
                    <i class="fa-trash"></i>
                </button>
            </div>
        `);
        
        $container.append($option);
    }
    
    /**
     * Populate anchors from existing data
     */
    function populateAnchors(anchors) {
        const $container = $('#edit-anchors-container');
        $container.empty();
        
        if (anchors && anchors.length > 0) {
            anchors.forEach(anchor => {
                const $option = $(`
                    <div class="anchor-option">
                        <input type="text" placeholder="Option text" class="anchor-text" value="${anchor.tag || ''}">
                        <input type="number" placeholder="Value" class="anchor-value" value="${anchor.value || ''}">
                        <button type="button" class="remove-anchor">
                            <i class="fa-trash"></i>
                        </button>
                    </div>
                `);
                $container.append($option);
            });
        } else {
            // Add default options
            addAnchorOption();
            addAnchorOption();
        }
    }
    
    /**
     * Save field edit
     */
    function saveFieldEdit() {
        const $field = window.currentEditingField;
        if (!$field) return;
        
        const fieldType = $('#edit-field-type').val();
        const fieldDimension = $('#edit-field-dimension').val();
        const practiceQuestion = $('#edit-practice-question').is(':checked');
        
        // Get content from CKEditor or fallback to textarea
        let fieldContent;
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['edit-field-content']) {
            fieldContent = CKEDITOR.instances['edit-field-content'].getData();
        } else {
            fieldContent = $('#edit-field-content').val();
        }
        
        // Update field data
        $field.attr('data-type', fieldType);
        $field.find('input[name="field_type[]"]').val(fieldType);
        $field.find('input[name="field_content[]"]').val(fieldContent);
        $field.find('input[name="field_dimension[]"]').val(fieldDimension);
        
        // Handle anchors for multiple choice
        if (fieldType == 1) {
            const anchors = collectAnchors();
            $field.find('input[name="field_anchors[]"]').val(JSON.stringify(anchors));
        } else {
            $field.find('input[name="field_anchors[]"]').val('[]');
        }
        
        // Update field display
        updateFieldDisplay($field, fieldType, fieldContent, fieldDimension);
        
        // Close modal
        $('#field-edit-modal').modal('hide');
        
        console.log('Field updated successfully');
    }
    
    /**
     * Collect anchor options from form
     */
    function collectAnchors() {
        const anchors = [];
        $('#edit-anchors-container .anchor-option').each(function() {
            const text = $(this).find('.anchor-text').val();
            const value = $(this).find('.anchor-value').val();
            
            if (text && value) {
                anchors.push({
                    tag: text,
                    value: parseInt(value)
                });
            }
        });
        return anchors;
    }
    
    /**
     * Update field display after editing
     */
    function updateFieldDisplay($field, fieldType, content, dimension) {
        const $fieldType = $field.find('.field-type');
        const $fieldPreview = $field.find('.field-preview');
        
        // Update field type display
        const typeNames = {
            '1': 'Multiple Choice',
            '2': 'Description',
            '3': 'Text Input',
            '4': 'Letters',
            '5': 'Equation'
        };
        $fieldType.text(typeNames[fieldType] || 'Unknown');
        
        // Update preview content
        if (fieldType == 1) {
            // Multiple Choice
            const anchors = collectAnchors();
            let anchorsHtml = '';
            if (anchors.length > 0) {
                anchorsHtml = '<div style="margin-top: 10px;"><small style="color: #7f8c8d; font-weight: 600;">Anchors:</small><div style="margin-top: 5px;">';
                anchors.forEach(anchor => {
                    anchorsHtml += `<div style="margin: 3px 0; padding: 6px 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; display: inline-block; margin-right: 8px; font-size: 13px;"><i class="fa-circle-o" style="margin-right: 5px; color: #6c757d;"></i>${anchor.tag}</div>`;
                });
                anchorsHtml += '</div></div>';
            }
            
            $fieldPreview.html(`
                <strong>Multiple Choice Question:</strong>
                <div style="margin-top: 8px;">
                    <div style="margin-bottom: 5px; font-weight: 500;">${content}</div>
                    ${anchorsHtml}
                </div>
            `);
        } else if (fieldType == 2) {
            // Description
            $fieldPreview.html(`
                <strong>Description:</strong>
                <div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;">
                    ${content}
                </div>
            `);
        } else if (fieldType == 3) {
            // Text Input
            $fieldPreview.html(`
                <strong>Text Input:</strong>
                <div style="margin-top: 8px;">
                    <div style="margin-bottom: 5px; font-weight: 500;">${content}</div>
                    <div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
                        <input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%; color: #6c757d; font-style: italic;" disabled>
                    </div>
                </div>
            `);
        } else {
            // Other types
            $fieldPreview.html(`
                <strong>${typeNames[fieldType]}:</strong>
                <div style="margin-top: 8px;">
                    ${content}
                </div>
            `);
        }
    }
    
    /**
     * Duplicate a field
     */
    function duplicateField($field) {
        const $duplicate = $field.clone();
        
        // Update field number
        $duplicate.find('.field-number').text(fieldCounter);
        $duplicate.find('input[name="field_number[]"]').val(fieldCounter);
        
        // Insert after original field
        $field.after($duplicate);
        
        // Update counter
        fieldCounter++;
        
        // Add animation
        $duplicate.hide().fadeIn(300);
        
        console.log('Duplicated field');
    }
    
    /**
     * Remove a field
     */
    function removeField($field) {
        if (confirm('Are you sure you want to remove this field?')) {
            $field.fadeOut(300, function() {
                $(this).remove();
                updateFieldNumbers();
                
                // Show empty state if no fields left
                if ($('#field-list .field-item').length === 0) {
                    $('.empty-state').show();
                }
            });
        }
    }
    
    /**
     * Update field numbers after reordering
     */
    function updateFieldNumbers() {
        $('#field-list .field-item').each(function(index) {
            const newNumber = index + 1;
            $(this).find('.field-number').text(newNumber);
            $(this).find('input[name="field_number[]"]').val(newNumber);
        });
    }
    
    /**
     * Save the assessment
     */
    function saveAssessment() {
        const formData = new FormData($('form')[0]);
        
        // Collect field data
        const fields = [];
        $('#field-list .field-item').each(function(index) {
            const $field = $(this);
            const fieldData = {
                id: $field.find('input[name="field_id[]"]').val() || null,
                type: $field.find('input[name="field_type[]"]').val(),
                content: $field.find('input[name="field_content[]"]').val(),
                number: $field.find('input[name="field_number[]"]').val(),
                anchors: $field.find('input[name="field_anchors[]"]').val() || null,
                dimension_id: $field.find('input[name="field_dimension[]"]').val() || null
            };
            fields.push(fieldData);
        });
        
        formData.append('questions', JSON.stringify(fields));
        formData.append('deleted_questions', JSON.stringify([]));
        
        // Show loading state
        const $submitBtn = $('input[type="submit"]');
        const originalText = $submitBtn.val();
        $submitBtn.val('Saving...').prop('disabled', true);
        
        // Submit form
        $.ajax({
            url: $('form').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Assessment saved successfully:', response);
                
                // Show success message
                showNotification('Assessment saved successfully!', 'success');
                
                // Reset button
                $submitBtn.val(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error saving assessment:', error);
                
                // Show error message
                showNotification('Error saving assessment. Please try again.', 'error');
                
                // Reset button
                $submitBtn.val(originalText).prop('disabled', false);
            }
        });
    }
    
    /**
     * Show notification
     */
    function showNotification(message, type) {
        // Simple notification - could be enhanced with a proper notification system
        const $notification = $('<div class="notification notification-' + type + '" style="position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 4px; color: white; z-index: 9999; background: ' + (type === 'success' ? '#27ae60' : '#e74c3c') + ';">' + message + '</div>');
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Add some CSS for the placeholder
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .field-placeholder {
                background: #f8f9fa !important;
                border: 2px dashed #bdc3c7 !important;
                border-radius: 8px !important;
                margin-bottom: 15px !important;
            }
            .field-types-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-top: 20px;
            }
            .field-type-option {
                padding: 20px;
                border: 2px solid #e9ecef;
                border-radius: 8px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .field-type-option:hover {
                border-color: #667eea;
                background: #f8f9fa;
            }
            .field-type-option i {
                font-size: 24px;
                color: #667eea;
                margin-bottom: 10px;
            }
            .field-type-option h4 {
                margin: 10px 0 5px 0;
                color: #2c3e50;
            }
            .field-type-option p {
                margin: 0;
                color: #7f8c8d;
                font-size: 12px;
            }
        `)
        .appendTo('head');
    
});
