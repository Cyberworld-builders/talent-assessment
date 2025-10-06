// Vue.js Assessment Editor
console.log('Vue Assessment Editor initialized');

// Field Item Component
Vue.component('field-item', {
    props: ['field', 'index', 'dimensions'],
    template: `
        <div class="field-item" :data-type="field.type" :data-id="field.id || ''">
            <div class="field-header">
                <div class="field-header-left">
                    <div class="drag-handle">
                        <i class="fa-bars"></i>
                    </div>
                    <div class="field-number">{{ index + 1 }}</div>
                </div>
                <div class="field-actions">
                    <button type="button" class="field-action-btn" @click="$emit('edit', field)" title="Edit Field">
                        <i class="fa-edit"></i> Edit
                    </button>
                    <button type="button" class="field-action-btn" @click="$emit('duplicate', field)" title="Duplicate Field">
                        <i class="fa-copy"></i> Duplicate
                    </button>
                    <button type="button" class="field-action-btn" @click="$emit('delete', field)" title="Remove Field">
                        <i class="fa-trash"></i> Remove
                    </button>
                </div>
            </div>
            
            <div class="field-content">
                <div class="field-preview">
                    <div v-if="field.type == 2" class="description-preview">
                        <strong>Description:</strong>
                        <div style="margin-top: 8px; padding: 10px; background: #e8f4fd; border-left: 3px solid #3498db; border-radius: 3px;" v-html="field.content"></div>
                        <div v-if="getDimensionName(field.dimension_id)" style="margin-top: 8px;">
                            <span class="dimension-indicator">
                                <i class="fa-tag"></i> {{ getDimensionName(field.dimension_id) }}
                            </span>
                        </div>
                    </div>
                    
                    <div v-else-if="field.type == 1" class="multiple-choice-preview">
                        <strong>Multiple Choice Question:</strong>
                        <div style="margin-top: 8px;">
                            <div style="margin-bottom: 5px; font-weight: 500;" v-html="field.content"></div>
                            <div v-if="field.anchors && field.anchors.length > 0" style="margin-top: 10px;">
                                <small style="color: #7f8c8d; font-weight: 600;">Anchors:</small>
                                <div style="margin-top: 5px;">
                                    <div v-for="anchor in field.anchors" :key="anchor.tag" style="margin: 3px 0; padding: 6px 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; display: inline-block; margin-right: 8px; font-size: 13px;">
                                        <i class="fa-circle-o" style="margin-right: 5px; color: #6c757d;"></i>{{ anchor.tag }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="getDimensionName(field.dimension_id)" style="margin-top: 8px;">
                                <span class="dimension-indicator">
                                    <i class="fa-tag"></i> {{ getDimensionName(field.dimension_id) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else-if="field.type == 3" class="text-input-preview">
                        <strong>Text Input:</strong>
                        <div style="margin-top: 8px;">
                            <div style="margin-bottom: 5px; font-weight: 500;" v-html="field.content"></div>
                            <div style="margin-top: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
                                <input type="text" placeholder="User will type here..." style="border: none; background: none; width: 100%; color: #6c757d; font-style: italic;" disabled>
                            </div>
                            <div v-if="getDimensionName(field.dimension_id)" style="margin-top: 8px;">
                                <span class="dimension-indicator">
                                    <i class="fa-tag"></i> {{ getDimensionName(field.dimension_id) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="other-field-preview">
                        <strong>{{ getFieldTypeName(field.type) }}:</strong>
                        <div style="margin-top: 8px;" v-html="field.content"></div>
                        <div v-if="getDimensionName(field.dimension_id)" style="margin-top: 8px;">
                            <span class="dimension-indicator">
                                <i class="fa-tag"></i> {{ getDimensionName(field.dimension_id) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    methods: {
        getDimensionName(dimensionId) {
            if (!dimensionId) return null;
            const dimension = this.dimensions.find(d => d.id == dimensionId);
            return dimension ? dimension.name : null;
        },
        getFieldTypeName(type) {
            const typeNames = {
                '1': 'Multiple Choice',
                '2': 'Description',
                '3': 'Text Input',
                '4': 'Letters',
                '5': 'Equation'
            };
            return typeNames[type] || 'Unknown';
        }
    }
});

// Field Edit Modal Component
Vue.component('field-edit-modal', {
    props: ['show', 'field', 'dimensions'],
    template: `
        <div v-if="show" class="modal fade" id="field-edit-modal" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click="$emit('close')">&times;</button>
                        <h4 class="modal-title">Edit Field</h4>
                    </div>
                    <div class="modal-body">
                        <div id="field-edit-form">
                            <!-- Field Type Selection -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">Field Type</label>
                                <select class="form-control-modern" v-model="editField.type">
                                    <option value="1">Multiple Choice</option>
                                    <option value="2">Description</option>
                                    <option value="3">Text Input</option>
                                    <option value="4">Letters</option>
                                    <option value="5">Equation</option>
                                </select>
                            </div>
                            
                            <!-- Field Content -->
                            <div class="form-group-modern">
                                <label class="form-label-modern">Field Content <span style="color: #e74c3c;">*</span></label>
                                <textarea class="form-control-modern" v-model="editField.content" rows="4" placeholder="Enter field content..."></textarea>
                                <div class="form-help">Use the rich text editor above to format your content. For multiple choice questions, this is the question text.</div>
                            </div>
                            
                            <!-- Dimension Assignment -->
                            <div class="form-group-modern" id="edit-dimension-group">
                                <label class="form-label-modern">Dimension <span style="color: #e74c3c;">*</span></label>
                                <select class="form-control-modern" v-model="editField.dimension_id">
                                    <option value="">No Dimension</option>
                                    <option v-for="dimension in dimensions" :key="dimension.id" :value="dimension.id">{{ dimension.name }}</option>
                                </select>
                            </div>
                            
                            <!-- Practice Question -->
                            <div class="form-group-modern">
                                <label class="checkbox-inline">
                                    <input type="checkbox" v-model="editField.practice_question" style="margin-right: 8px;">
                                    Practice Question
                                </label>
                                <div class="form-help">Mark this as a practice question (won't count toward final score).</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" @click="$emit('close')">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="saveField">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    data() {
        return {
            editField: {}
        }
    },
    watch: {
        field: {
            handler(newField) {
                if (newField) {
                    this.editField = { ...newField };
                }
            },
            immediate: true
        }
    },
    methods: {
        saveField() {
            // Validate required fields
            if (!this.editField.content || this.editField.content.trim() === '') {
                alert('Please enter field content.');
                return;
            }
            
            if (this.editField.type != 2 && (!this.editField.dimension_id || this.editField.dimension_id === '')) {
                alert('Please select a dimension.');
                return;
            }
            
            // Emit the updated field
            this.$emit('save', this.editField);
        }
    }
});

// Main Assessment Editor Component
Vue.component('assessment-editor', {
    props: ['initialFields', 'dimensions', 'assessmentId'],
    template: `
        <div class="fields-section">
            <div class="fields-header">
                <h3 class="fields-title">Assessment Fields</h3>
                <div class="field-actions">
                    <button type="button" class="sort-fields-btn" @click="sortByDimension" title="Sort fields by dimension">
                        <i class="fa-sort"></i> Sort by Dimension
                    </button>
                    <button type="button" class="add-field-btn" @click="showFieldTypeModal" id="add-field-btn">
                        <i class="fa-plus"></i> Add Field
                    </button>
                </div>
            </div>
            
            <div class="field-list" id="field-list">
                <field-item 
                    v-for="(field, index) in fields" 
                    :key="field.id || field.tempId"
                    :field="field"
                    :index="index"
                    :dimensions="dimensions"
                    @edit="editField"
                    @duplicate="duplicateField"
                    @delete="deleteField"
                />
                
                <div v-if="fields.length === 0" class="empty-state">
                    <i class="fa-file-text-o"></i>
                    <p>No fields added yet. Click "Add Field" to get started.</p>
                </div>
            </div>
            
            <!-- Field Type Selection Modal -->
            <div v-if="showFieldTypeModal" class="modal fade" id="field-type-modal" style="display: block;">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" @click="showFieldTypeModal = false">&times;</button>
                            <h4 class="modal-title">Select Field Type</h4>
                        </div>
                        <div class="modal-body">
                            <div class="field-types-grid">
                                <div class="field-type-option" @click="addField(1)">
                                    <i class="fa-list-ul"></i>
                                    <h4>Multiple Choice</h4>
                                    <p>Questions with predefined answer options</p>
                                </div>
                                <div class="field-type-option" @click="addField(2)">
                                    <i class="fa-paragraph"></i>
                                    <h4>Description</h4>
                                    <p>Text content or instructions</p>
                                </div>
                                <div class="field-type-option" @click="addField(3)">
                                    <i class="fa-keyboard-o"></i>
                                    <h4>Text Input</h4>
                                    <p>Open-ended text responses</p>
                                </div>
                                <div class="field-type-option" @click="addField(4)">
                                    <i class="fa-font"></i>
                                    <h4>Letters</h4>
                                    <p>Letter-based assessments</p>
                                </div>
                                <div class="field-type-option" @click="addField(5)">
                                    <i class="fa-calculator"></i>
                                    <h4>Equation</h4>
                                    <p>Mathematical equations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Field Edit Modal -->
            <field-edit-modal 
                :show="showEditModal"
                :field="editingField"
                :dimensions="dimensions"
                @close="showEditModal = false"
                @save="saveField"
            />
        </div>
    `,
    data() {
        return {
            fields: [...this.initialFields],
            nextTempId: 1000,
            showFieldTypeModal: false,
            showEditModal: false,
            editingField: null
        }
    },
    methods: {
        addField(type) {
            const newField = {
                tempId: this.nextTempId++,
                type: type,
                content: '',
                dimension_id: '',
                anchors: [],
                practice_question: false
            };
            this.fields.push(newField);
            this.showFieldTypeModal = false;
            
            // Auto-open edit modal for new fields
            this.editingField = newField;
            this.showEditModal = true;
        },
        
        editField(field) {
            this.editingField = field;
            this.showEditModal = true;
        },
        
        saveField(updatedField) {
            const index = this.fields.findIndex(f => 
                (f.id && f.id === updatedField.id) || 
                (f.tempId && f.tempId === updatedField.tempId)
            );
            
            if (index !== -1) {
                this.fields.splice(index, 1, updatedField);
            }
            
            this.showEditModal = false;
            this.updateFormData();
        },
        
        duplicateField(field) {
            const duplicatedField = {
                ...field,
                tempId: this.nextTempId++,
                id: null // Remove ID so it's treated as new
            };
            this.fields.push(duplicatedField);
            this.updateFormData();
        },
        
        deleteField(field) {
            if (confirm('Are you sure you want to delete this field?')) {
                if (field.id) {
                    // Delete from database
                    fetch(`/dashboard/assessments/delete-question/${field.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf_token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.fields = this.fields.filter(f => f.id !== field.id);
                            this.updateFormData();
                        } else {
                            alert('Error deleting field: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting field');
                    });
                } else {
                    // Remove from array
                    this.fields = this.fields.filter(f => f.tempId !== field.tempId);
                    this.updateFormData();
                }
            }
        },
        
        sortByDimension() {
            this.fields.sort((a, b) => {
                const nameA = this.getDimensionName(a.dimension_id) || 'Unknown';
                const nameB = this.getDimensionName(b.dimension_id) || 'Unknown';
                return nameA.localeCompare(nameB);
            });
        },
        
        getDimensionName(dimensionId) {
            if (!dimensionId) return null;
            const dimension = this.dimensions.find(d => d.id == dimensionId);
            return dimension ? dimension.name : null;
        },
        
        updateFormData() {
            // Update hidden form inputs for submission
            const formData = this.fields.map(field => ({
                id: field.id || '',
                type: field.type,
                content: field.content,
                dimension_id: field.dimension_id,
                anchors: field.anchors || []
            }));
            
            const formHtml = formData.map(field => `
                <input type="hidden" name="field_id[]" value="${field.id}">
                <input type="hidden" name="field_type[]" value="${field.type}">
                <input type="hidden" name="field_content[]" value="${field.content}">
                <input type="hidden" name="field_dimension[]" value="${field.dimension_id}">
                <input type="hidden" name="field_anchors[]" value="${JSON.stringify(field.anchors)}">
            `).join('');
            
            document.getElementById('vue-form-data').innerHTML = formHtml;
        }
    },
    mounted() {
        this.updateFormData();
    }
});

// Initialize Vue app
new Vue({
    el: '#vue-assessment-editor',
    data: {
        // Data will be populated from Blade template
    }
});
