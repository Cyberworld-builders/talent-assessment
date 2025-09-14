@extends('dashboard.dashboard')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Feedback Libraries</h4>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div class="list-group" style="margin-bottom: 0;">
                    <a href="#" class="list-group-item active" data-library="involved-360">
                        <i class="fa fa-circle-o"></i> Involved-360
                    </a>
                    <a href="#" class="list-group-item" data-library="involved-leader">
                        <i class="fa fa-circle-o"></i> Involved-Leader
                    </a>
                    <a href="#" class="list-group-item" data-library="involved-blockers">
                        <i class="fa fa-circle-o"></i> Involved-Blockers
                    </a>
                    <a href="#" class="list-group-item" data-library="involved-me">
                        <i class="fa fa-circle-o"></i> Involved-Me
                    </a>
                    <a href="#" class="list-group-item" data-library="involved-me-peak">
                        <i class="fa fa-circle-o"></i> Involved-Me Peak Week
                    </a>
                    <a href="#" class="list-group-item" data-library="david-codes">
                        <i class="fa fa-circle-o"></i> David Codes
                    </a>
                    <a href="#" class="list-group-item" data-library="custom">
                        <i class="fa fa-circle-o"></i> Custom
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <label for="library-name">Library Name</label>
                        <p class="text-muted small">The name of this feedback library.</p>
                        <input type="text" class="form-control" id="library-name" value="Leader & Blocker Combined" placeholder="Enter library name">
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success" id="save-feedback-btn">
                            <i class="fa fa-save"></i> Save Feedback
                        </button>
                        <button class="btn btn-default" id="import-feedback">
                            <i class="fa fa-upload"></i> Import Feedback
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <!-- Involved-360 Content -->
                <div id="involved-360-content" class="library-content">
                    <h3>Involved-360</h3>
                    <p class="text-muted">Feedback for Involved-360 dimensions.</p>
                    
                    <!-- Creative Problem Solving Dimension -->
                    <div class="dimension-section">
                        <h4>Creative Problem Solving</h4>
                        <div class="feedback-entries">
                            <div class="feedback-entry">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Performance Level</label>
                                        <select class="form-control performance-level">
                                            <option value="overall">Overall</option>
                                            <option value="high">High</option>
                                            <option value="medium">Medium</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label>Feedback</label>
                                        <textarea class="form-control feedback-text" rows="3" placeholder="Enter feedback for this performance level"></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-danger btn-sm remove-feedback" title="Remove">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm add-feedback" data-dimension="creative-problem-solving">
                            <i class="fa fa-plus"></i> Add Feedback
                        </button>
                    </div>
                    
                    <!-- Leadership Adaptability Dimension -->
                    <div class="dimension-section">
                        <h4>Leadership Adaptability</h4>
                        <div class="feedback-entries">
                            <!-- No feedback entries yet -->
                        </div>
                        <button class="btn btn-primary btn-sm add-feedback" data-dimension="leadership-adaptability">
                            <i class="fa fa-plus"></i> Add Feedback
                        </button>
                    </div>
                    
                    <!-- Collaboration Dimension -->
                    <div class="dimension-section">
                        <h4>Collaboration</h4>
                        <div class="feedback-entries">
                            <!-- No feedback entries yet -->
                        </div>
                        <button class="btn btn-primary btn-sm add-feedback" data-dimension="collaboration">
                            <i class="fa fa-plus"></i> Add Feedback
                        </button>
                    </div>
                </div>
                
                <!-- Other library content will be loaded dynamically -->
                <div id="other-content" class="library-content" style="display: none;">
                    <h3 id="current-library-name">Select a library</h3>
                    <p class="text-muted">Choose a feedback library from the sidebar to manage its content.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Feedback Modal -->
<div class="modal fade" id="addFeedbackModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Feedback</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="modal-performance-level">Performance Level</label>
                    <select class="form-control" id="modal-performance-level">
                        <option value="overall">Overall</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal-feedback-text">Feedback</label>
                    <textarea class="form-control" id="modal-feedback-text" rows="4" placeholder="Enter feedback for this performance level"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-feedback">Save Feedback</button>
            </div>
        </div>
    </div>
</div>

<style>
.dimension-section {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    background-color: #fafafa;
}

.feedback-entry {
    margin-bottom: 15px;
    padding: 15px;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.library-content {
    display: none;
}

.library-content.active {
    display: block;
}

.list-group-item.active {
    background-color: #f5f5f5;
    border-color: #ddd;
    color: #333;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    border-radius: 0;
}

.list-group-item:hover {
    background-color: #f5f5f5;
}
</style>

<script>
$(document).ready(function() {
    // Handle sidebar navigation
    $('.list-group-item').on('click', function(e) {
        e.preventDefault();
        $('.list-group-item').removeClass('active');
        $(this).addClass('active');
        
        var library = $(this).data('library');
        $('.library-content').removeClass('active').hide();
        
        if (library === 'involved-360') {
            $('#involved-360-content').addClass('active').show();
        } else {
            $('#other-content').addClass('active').show();
            $('#current-library-name').text($(this).text().trim());
        }
    });
    
    // Handle add feedback button
    $('.add-feedback').on('click', function() {
        var dimension = $(this).data('dimension');
        $('#addFeedbackModal').data('dimension', dimension);
        $('#addFeedbackModal').modal('show');
    });
    
    // Handle save feedback from modal
    $('#save-feedback').on('click', function() {
        var dimension = $('#addFeedbackModal').data('dimension');
        var performanceLevel = $('#modal-performance-level').val();
        var feedbackText = $('#modal-feedback-text').val();
        
        if (feedbackText.trim() === '') {
            alert('Please enter feedback text');
            return;
        }
        
        // Add the feedback entry to the dimension
        addFeedbackEntry(dimension, performanceLevel, feedbackText);
        
        // Clear modal and close
        $('#modal-feedback-text').val('');
        $('#addFeedbackModal').modal('hide');
    });
    
    // Handle remove feedback
    $(document).on('click', '.remove-feedback', function() {
        $(this).closest('.feedback-entry').remove();
    });
    
    // Function to add feedback entry
    function addFeedbackEntry(dimension, performanceLevel, feedbackText) {
        var dimensionSection = $('.dimension-section').filter(function() {
            return $(this).find('.add-feedback').data('dimension') === dimension;
        });
        
        var feedbackEntries = dimensionSection.find('.feedback-entries');
        
        var entryHtml = '<div class="feedback-entry">' +
            '<div class="row">' +
                '<div class="col-md-3">' +
                    '<label>Performance Level</label>' +
                    '<select class="form-control performance-level">' +
                        '<option value="overall"' + (performanceLevel === 'overall' ? ' selected' : '') + '>Overall</option>' +
                        '<option value="high"' + (performanceLevel === 'high' ? ' selected' : '') + '>High</option>' +
                        '<option value="medium"' + (performanceLevel === 'medium' ? ' selected' : '') + '>Medium</option>' +
                        '<option value="low"' + (performanceLevel === 'low' ? ' selected' : '') + '>Low</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-8">' +
                    '<label>Feedback</label>' +
                    '<textarea class="form-control feedback-text" rows="3">' + feedbackText + '</textarea>' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<label>&nbsp;</label>' +
                    '<button class="btn btn-danger btn-sm remove-feedback" title="Remove">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        feedbackEntries.append(entryHtml);
    }
    
    // Show the first library by default
    $('#involved-360-content').addClass('active').show();
    
    // Handle save button (you can add this button to the UI)
    $('#save-feedback-btn').on('click', function() {
        saveCurrentLibrary();
    });
    
    // Function to save the current library
    function saveCurrentLibrary() {
        var libraryType = $('.list-group-item.active').data('library');
        var libraryName = $('#library-name').val();
        var dimensions = {};
        
        // Collect all dimension data
        $('.dimension-section').each(function() {
            var dimensionName = $(this).find('h4').text().toLowerCase().replace(/\s+/g, '-');
            var dimensionData = {};
            
            $(this).find('.feedback-entry').each(function() {
                var performanceLevel = $(this).find('.performance-level').val();
                var feedbackText = $(this).find('.feedback-text').val();
                
                if (feedbackText.trim() !== '') {
                    dimensionData[performanceLevel] = feedbackText;
                }
            });
            
            if (Object.keys(dimensionData).length > 0) {
                dimensions[dimensionName] = dimensionData;
            }
        });
        
        // Send to server
        $.ajax({
            url: '/dashboard/feedback/save',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf_token"]').attr('content'),
                library_type: libraryType,
                name: libraryName,
                dimensions: dimensions
            },
            success: function(response) {
                if (response.success) {
                    alert('Feedback saved successfully!');
                } else {
                    alert('Error saving feedback: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error saving feedback: ' + xhr.responseText);
            }
        });
    }
});
</script>
@endsection