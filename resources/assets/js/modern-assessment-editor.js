jQuery(document).ready(function($) {
    
    // Initialize the modern assessment editor
    console.log('Modern Assessment Editor initialized - v2.0 with modal delete confirmation');
    
    // Field counter for numbering
    let fieldCounter = 1;
    
    // Deletion state management
    let isDeleting = false;
    
    // Initialize drag and drop
    initDragAndDrop();
    
    // Add field button click handler
    $('#add-field-btn').on('click', function() {
        showFieldTypeModal();
    });
    
    
    // Confirm delete button
    $('#confirm-delete').on('click', function() {
        console.log('Delete button clicked');
        confirmFieldDeletion();
    });
    
    // Reset modal when it's hidden (but only if not currently deleting)
    $('#delete-confirmation-modal').on('hidden.bs.modal', function() {
        if (!isDeleting) {
            resetDeleteModal();
        }
    });
    
    // Reset modal when it's shown (but only clear status, don't reset everything)
    $('#delete-confirmation-modal').on('show.bs.modal', function() {
        // Only clear status messages, don't reset button or field info
        $('#delete-status').hide().removeClass('success error info').empty();
    });
    
    /**
     * Reset delete modal to clean state
     */
    function resetDeleteModal() {
        // Reset button state
        $('#confirm-delete').prop('disabled', false).text('Delete Field');
        
        // Clear status
        $('#delete-status').hide().removeClass('success error info').empty();
        
        // Clear field info
        $('#delete-field-info').empty();
        
        // Don't clear global reference here - it should persist until deletion is complete
        
        // Remove any aria-hidden attributes that might be causing issues
        $('#delete-confirmation-modal').removeAttr('aria-hidden');
        
        // Clean up any lingering event listeners
        $('#delete-confirmation-modal').off('hidden.bs.modal');
    }
    
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
                    { name: 'insert', items: ['Table'] },
                    { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize', 'Source'] }
                ],
                height: 200,
                width: '100%',
                allowedContent: true,
                extraAllowedContent: 'table tbody tr td th thead tfoot colgroup col',
                removePlugins: 'elementspath',
                resize_enabled: false
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
     * Create HTML for a new field based on type
     */
    function createFieldHtml(fieldType, fieldNumber) {
        const fieldTypes = {
            1: { name: 'Multiple Choice', icon: 'fa-list-ul' },
            2: { name: 'Description', icon: 'fa-paragraph' },
            3: { name: 'Text Input', icon: 'fa-edit' },
            4: { name: 'Letters', icon: 'fa-font' },
            5: { name: 'Equation', icon: 'fa-calculator' },
            6: { name: 'Math and Letters', icon: 'fa-superscript' },
            7: { name: 'Square Sequence', icon: 'fa-th' },
            8: { name: 'Symmetry', icon: 'fa-mirror' },
            9: { name: 'Square Symmetry', icon: 'fa-th-large' },
            10: { name: 'Instructions', icon: 'fa-info-circle' },
            11: { name: 'Slider', icon: 'fa-sliders' }
        };
        
        const typeInfo = fieldTypes[fieldType] || { name: 'Unknown', icon: 'fa-question' };
        
        return `
            <div class="field-item" data-field-type="${fieldType}" data-id="">
                <div class="field-header">
                    <div class="field-header-left">
                        <div class="drag-handle">
                            <i class="fa fa-grip-vertical"></i>
                        </div>
                        <div class="field-number">${fieldNumber}</div>
                        <div class="field-type-badge">
                            <i class="fa ${typeInfo.icon}"></i>
                            <span>${typeInfo.name}</span>
                        </div>
                    </div>
                    <div class="field-actions">
                        <button type="button" class="field-action-btn edit-field" title="Edit Field">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="field-action-btn remove-field" title="Remove Field">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="field-content">
                    <div class="field-preview">
                        <div class="field-dimension">
                            <span class="dimension-label">Dimension:</span>
                            <span class="dimension-value">Not assigned</span>
                        </div>
                        <div class="field-text">
                            <span class="field-label">Question:</span>
                            <span class="field-value">Click to edit question content</span>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="field_type[]" value="${fieldType}">
                <input type="hidden" name="field_number[]" value="${fieldNumber}">
                <input type="hidden" name="field_id[]" value="">
                <input type="hidden" name="field_content[]" value="">
                <input type="hidden" name="field_dimension[]" value="">
                <input type="hidden" name="field_anchors[]" value="[]">
            </div>
        `;
    }

    /**
     * Add a new field of the specified type
     */
    function addNewField(fieldType) {
        console.log('Adding new field of type:', fieldType);
        
        // Create new field HTML based on type
        const fieldHtml = createFieldHtml(fieldType, fieldCounter);
        const $newField = $(fieldHtml);
        
        // Add to field list
        $newField.appendTo('#field-list');
        
        // Update counter
        fieldCounter++;
        
        // Hide empty state if it exists
        $('.empty-state').hide();
        
        // Add animation
        $newField.hide().fadeIn(300);
        
        // Automatically open edit modal for all field types
        setTimeout(function() {
            editField($newField);
        }, 350); // Wait for fade-in animation to complete
        
        console.log('Field added successfully');
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
        
        // Debug: Log the dimension value being loaded
        console.log('Loading dimension:', fieldDimension);
        console.log('Dimension select value after setting:', $('#edit-field-dimension').val());
        
        // Additional debugging - check if dimension field is being reset
        setTimeout(function() {
            console.log('Dimension select value after timeout:', $('#edit-field-dimension').val());
            console.log('Dimension select element:', $('#edit-field-dimension'));
            console.log('Dimension select options:', $('#edit-field-dimension option').map(function() { 
                return $(this).val() + ': ' + $(this).text(); 
            }).get());
        }, 100);
        
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
        
        // Debug: Log all dimension-related values
        console.log('Field type:', fieldType);
        console.log('Dimension value from select:', fieldDimension);
        console.log('Dimension select element:', $('#edit-field-dimension'));
        console.log('Dimension select options:', $('#edit-field-dimension option').map(function() { return $(this).val() + ': ' + $(this).text(); }).get());
        
        // Additional debugging - check if dimension field is being cleared
        console.log('Dimension field before save:', $('#edit-field-dimension').val());
        console.log('Dimension field element exists:', $('#edit-field-dimension').length > 0);
        
        // Get content from CKEditor or fallback to textarea
        let fieldContent;
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['edit-field-content']) {
            fieldContent = CKEDITOR.instances['edit-field-content'].getData();
        } else {
            fieldContent = $('#edit-field-content').val();
        }
        
        // Validation with visual feedback
        let hasErrors = false;
        let errorMessage = '';
        
        if (!fieldContent || fieldContent.trim() === '') {
            hasErrors = true;
            errorMessage += 'Please enter field content.\n';
            $('#edit-field-content').addClass('error-border');
        } else {
            $('#edit-field-content').removeClass('error-border');
        }
        
        // For non-description fields, dimension is required
        if (fieldType != 2 && (!fieldDimension || fieldDimension === '')) {
            hasErrors = true;
            errorMessage += 'Please select a dimension for this field.\n';
            $('#edit-field-dimension').addClass('error-border');
        } else {
            $('#edit-field-dimension').removeClass('error-border');
        }
        
        if (hasErrors) {
            // Show error in modal
            showModalError(errorMessage);
            return;
        }
        
        // Update field data
        $field.attr('data-type', fieldType);
        $field.find('input[name="field_type[]"]').val(fieldType);
        $field.find('input[name="field_content[]"]').val(fieldContent);
        $field.find('input[name="field_dimension[]"]').val(fieldDimension);
        
        // Debug: Log the dimension value being saved
        console.log('Saving dimension:', fieldDimension);
        console.log('Hidden field value after setting:', $field.find('input[name="field_dimension[]"]').val());
        
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
        
        // Get dimension name for display
        let dimensionName = '';
        if (dimension && dimension !== '') {
            // Try to get dimension name from the select options
            const $dimensionSelect = $('#edit-field-dimension');
            const selectedOption = $dimensionSelect.find('option[value="' + dimension + '"]');
            dimensionName = selectedOption.text() || 'Unknown Dimension';
            
            // If we can't find it in the current select, try to get it from the original data
            if (dimensionName === 'Unknown Dimension') {
                // Look for dimension data in the page
                const $dimensionData = $('input[name="dimension_data"]');
                if ($dimensionData.length > 0) {
                    try {
                        const dimensions = JSON.parse($dimensionData.val());
                        const foundDimension = dimensions.find(d => d.id == dimension);
                        if (foundDimension) {
                            dimensionName = foundDimension.name;
                        }
                    } catch (e) {
                        console.log('Could not parse dimension data');
                    }
                }
            }
        }
        
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
                    ${dimensionName ? `<div style="margin-top: 8px;"><span class="dimension-indicator"><i class="fa-tag"></i> ${dimensionName}</span></div>` : ''}
                </div>
            `);
        } else if (fieldType == 2) {
            // Description
            $fieldPreview.html(`
                <strong>Description:</strong>
                <div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;">
                    ${content}
                    ${dimensionName ? `<div style="margin-top: 8px;"><span class="dimension-indicator"><i class="fa-tag"></i> ${dimensionName}</span></div>` : ''}
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
                    ${dimensionName ? `<div style="margin-top: 8px;"><span class="dimension-indicator"><i class="fa-tag"></i> ${dimensionName}</span></div>` : ''}
                </div>
            `);
        } else {
            // Other types
            $fieldPreview.html(`
                <strong>${typeNames[fieldType]}:</strong>
                <div style="margin-top: 8px;">
                    ${content}
                    ${dimensionName ? `<div style="margin-top: 8px;"><span class="dimension-indicator"><i class="fa-tag"></i> ${dimensionName}</span></div>` : ''}
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
        // Store reference to the field being deleted
        window.fieldToDelete = $field;
        
        // Get field information for display
        const fieldType = $field.data('type');
        const fieldContent = $field.find('input[name="field_content[]"]').val();
        const fieldId = $field.find('input[name="field_id[]"]').val();
        const fieldNumber = $field.find('.field-number').text();
        
        // Get field type name
        const typeNames = {
            '1': 'Multiple Choice',
            '2': 'Description', 
            '3': 'Text Input',
            '4': 'Letters',
            '5': 'Equation'
        };
        
        // Populate modal with field information
        $('#delete-field-info').html(`
            <h5>Field #${fieldNumber}: ${typeNames[fieldType] || 'Unknown'}</h5>
            <p><strong>Content:</strong> ${fieldContent ? fieldContent.substring(0, 100) + (fieldContent.length > 100 ? '...' : '') : 'No content'}</p>
            <p><strong>Field ID:</strong> ${fieldId || 'New field (not saved yet)'}</p>
        `);
        
        // Show the modal (reset will be handled by show.bs.modal event)
        $('#delete-confirmation-modal').modal('show');
    }
    
    /**
     * Confirm field deletion
     */
    function confirmFieldDeletion() {
        console.log('confirmFieldDeletion called');
        
        // Prevent multiple simultaneous deletions
        if (isDeleting) {
            console.log('Already deleting, ignoring request');
            return;
        }
        
        const $field = window.fieldToDelete;
        if (!$field) {
            console.log('No field to delete');
            return;
        }
        console.log('Field to delete:', $field);
        
        // Set deletion flag
        isDeleting = true;
        
        const fieldId = $field.find('input[name="field_id[]"]').val();
        
        // Show loading status
        $('#delete-status').show().removeClass('success error info').addClass('info').html('Deleting field...');
        $('#confirm-delete').prop('disabled', true).text('Deleting...');
        
        // If field has an ID, delete it from the database immediately
        if (fieldId && fieldId !== '') {
            console.log('Deleting field with ID:', fieldId);
            console.log('CSRF Token:', $('meta[name="csrf_token"]').attr('content'));
            
            $.ajax({
                url: '/dashboard/assessments/delete-question/' + fieldId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(response) {
                    console.log('Field deleted from database:', response);
                    $('#delete-status').removeClass('info').addClass('success').html('Field deleted successfully from database.');
                    
                    // Close modal and remove field immediately
                    setTimeout(() => {
                        $('#delete-confirmation-modal').modal('hide');
                        
                        // Remove field after modal closes
                        setTimeout(() => {
                            $field.fadeOut(300, function() {
                                $(this).remove();
                                updateFieldNumbers();
                                
                                // Show empty state if no fields left
                                if ($('#field-list .field-item').length === 0) {
                                    $('.empty-state').show();
                                }
                                
                                // Reset deletion flag
                                isDeleting = false;
                                window.fieldToDelete = null;
                            });
                        }, 300);
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting field:', error);
                    console.error('Response:', xhr.responseText);
                    console.error('Status:', xhr.status);
                    
                    $('#delete-status').removeClass('info').addClass('error').html(`
                        <strong>Delete Failed:</strong><br>
                        Status: ${xhr.status}<br>
                        Error: ${error}<br>
                        Response: ${xhr.responseText}
                    `);
                    $('#confirm-delete').prop('disabled', false).text('Delete Field');
                    
                    // Reset deletion flag on error
                    isDeleting = false;
                }
            });
        } else {
            // New field (no ID), just remove from DOM
            $('#delete-status').removeClass('info').addClass('success').html('New field removed from form.');
            
            setTimeout(() => {
                $('#delete-confirmation-modal').modal('hide');
                
                // Remove field after modal closes
                setTimeout(() => {
                    $field.fadeOut(300, function() {
                        $(this).remove();
                        updateFieldNumbers();
                        
                        // Show empty state if no fields left
                        if ($('#field-list .field-item').length === 0) {
                            $('.empty-state').show();
                        }
                        
                        // Reset deletion flag
                        isDeleting = false;
                        window.fieldToDelete = null;
                    });
                }, 300);
            }, 1000);
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
     * Sanitize HTML content to remove scripts and event handlers
     */
    function sanitizeHtml(html) {
        if (!html) return '';
        
        // Remove script tags and their content
        let sanitized = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi, '');
        
        // Remove event handlers (onclick, onload, etc.)
        sanitized = sanitized.replace(/on\w+\s*=\s*["\'][^"\']*["\']/gi, '');
        
        return sanitized;
    }

    /**
     * Save the assessment
     */
    function saveAssessment() {
        // Validate all fields before submission
        let hasErrors = false;
        let errorMessage = '';
        
        $('#field-list .field-item').each(function(index) {
            const $field = $(this);
            const fieldType = $field.find('input[name="field_type[]"]').val();
            const fieldContent = $field.find('input[name="field_content[]"]').val();
            const fieldDimension = $field.find('input[name="field_dimension[]"]').val();
            
            // Check content
            if (!fieldContent || fieldContent.trim() === '') {
                hasErrors = true;
                errorMessage += `Field ${index + 1}: Please enter content.\n`;
            }
            
            // Check dimension for non-description fields
            if (fieldType != 2 && (!fieldDimension || fieldDimension === '')) {
                hasErrors = true;
                errorMessage += `Field ${index + 1}: Please select a dimension.\n`;
            }
        });
        
        if (hasErrors) {
            // Show error as toast notification
            showToast('error', 'Validation Error', errorMessage.trim());
            return;
        }
        
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
                
                // Check if response is JSON
                if (typeof response === 'object' && response.success) {
                    // Show success message
                    showNotification(response.message || 'Assessment saved successfully!', 'success');
                } else {
                    // Handle HTML response (fallback)
                    showNotification('Assessment saved successfully!', 'success');
                }
                
                // Reset button
                $submitBtn.val(originalText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error saving assessment:', error);
                console.error('Response:', xhr.responseText);
                
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
    
    /**
     * Show modal error
     */
    function showModalError(message) {
        // Remove any existing error messages
        $('.modal-error').remove();
        
        // Add error message to modal header
        const $errorDiv = $('<div class="modal-error" style="background: #f8d7da; color: #721c24; padding: 10px 15px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 4px; font-size: 14px;">' + message + '</div>');
        $('#field-edit-modal .modal-header').after($errorDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $errorDiv.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Show toast notification
     */
    function showToast(type, title, message) {
        // Use existing notification system but with better styling
        const bgColor = type === 'error' ? '#e74c3c' : type === 'success' ? '#27ae60' : '#3498db';
        const $toast = $(`
            <div class="toast-notification" style="
                position: fixed; 
                top: 20px; 
                right: 20px; 
                padding: 15px 20px; 
                border-radius: 6px; 
                color: white; 
                z-index: 10000; 
                background: ${bgColor};
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                max-width: 400px;
                font-size: 14px;
            ">
                <strong>${title}</strong><br>
                ${message}
            </div>
        `);
        
        $('body').append($toast);
        
        setTimeout(function() {
            $toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
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
            .error-border {
                border: 2px solid #e74c3c !important;
                box-shadow: 0 0 5px rgba(231, 76, 60, 0.3) !important;
            }
            .modal-error {
                animation: slideDown 0.3s ease-out;
            }
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
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
