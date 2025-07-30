jQuery(document).ready(function($){

    // Set headers for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        }
    });

    console.log('I am ready');

    // Fix for cached disabled attribute
    $('#save').removeAttr('disabled');

    // Initialize the CKEditor
    $('#modal-wysiwyg textarea').ckeditor();

    // Add Custom Field
    $('#add-custom-field').on('click', function(){
        $template = $('#custom-field-template').find('.row');
        $template.clone().appendTo('.custom-fields');
    });

    // Remove Custom Field
    $('.custom-fields').on('click', '#remove-custom-field', function(){
        $field = $(this).closest('.custom-field').remove();
    });

    // Edit Question
    $('.questions').on('click', '#content', function(){
        $question = $(this).closest('.question');
        $content = $question.find('#content');
        $content_edit_field = $question.find('#content-edit-field');

        if ($content_edit_field.hasClass('active'))
            return false;

        var current_content = $content.html();

        $content_edit_field.find('input').val(current_content);
        $content.hide();
        $content_edit_field.fadeIn().addClass('active').find('input').focus().select();
    });

    // WYSIWYG Description Editor
    $('.edit-description-with-wysiwyg').on('click', function(){
        var current_content = $(this).prev('textarea').val();

        $edit_field = $(this).prev('textarea');

        $modal = $('#modal-wysiwyg');
        $modal.find('.modal-title').html('Advanced Question Editor');
        $modal.find('.save-button').attr('id', 'save-description-edits');
        //$body = $modal.find('.modal-body').append('<textarea id="Editor" class="form-control input-lg ">'+current_content+'</textarea>');
        $textarea = $modal.find('textarea').html(current_content);
        if (! CKEDITOR.instances.Editor)
            $editor = $textarea.ckeditor();
        else
            CKEDITOR.instances.Editor.setData(current_content);
        $modal.modal('show');

        $modal.on('click', '#save-description-edits', function(){

            var new_content = CKEDITOR.instances.Editor.document.getBody().getHtml();

            if (new_content.trim() == '')
                new_content = 'This is a sample question';

            $edit_field.val(new_content);

            $modal.modal('hide');
        });
    });

    // Advanced Edit
    $('.questions').on('click', '.advanced-edit-question', function(){
        $question = $(this).closest('.question');
        $content = $question.find('#content');

        var current_content = $content.html();
        //console.log(current_content);

        $modal = $('#modal-wysiwyg');
        $modal.find('.modal-title').html('Advanced Question Editor');
        $modal.find('.save-button').attr('id', 'save-question-edits');
        //$body = $modal.find('.modal-body').append('<textarea id="Editor" class="form-control input-lg ">'+current_content+'</textarea>');
        $textarea = $modal.find('textarea').html(current_content);
        if (! CKEDITOR.instances.Editor)
            $editor = $textarea.ckeditor();
        else
            CKEDITOR.instances.Editor.setData(current_content);
        $modal.modal('show');

        $content_edit_field.hide().removeClass('active');
        $content.show();

        $modal.on('click', '#save-question-edits', function(){
            //$question = $(this).closest('.question');
            //$content = $question.find('#content');
            //$content_edit_field = $question.find('#content-edit-field');

            //if (!$content_edit_field.hasClass('active'))
            //    return false;

            //var new_content = $editor.find('textarea').getData();

            var new_content = CKEDITOR.instances.Editor.document.getBody().getHtml();

            var beginning_p_tags = new_content.substring(0, 3);
            if (beginning_p_tags == "<p>")
            {
                new_content = new_content.substring(3);
                new_content = new_content.slice(0, -4);
            }

            if (new_content.trim() == '')
                new_content = 'This is a sample question';

            $content.html(new_content);
            //$content_edit_field.hide().removeClass('active');
            //$content.fadeIn();

            $modal.modal('hide');
        });
    });

    // Save Question
    $('.questions').on('focusout', '#content-edit-field', function(){

        $question = $(this).closest('.question');
        $content = $question.find('#content');
        $content_edit_field = $question.find('#content-edit-field');

        // Check if we're clicking on the advanced edit button
        if ($('.advanced-edit-question', $content_edit_field).is(":hover"))
            return false;

        if (!$content_edit_field.hasClass('active'))
            return false;

        var new_content = $content_edit_field.find('input').val();

        if (new_content.trim() == '')
            new_content = 'This is a sample question';

        $content.html(new_content);
        $content_edit_field.hide().removeClass('active');
        $content.fadeIn();
    });

    // Set Question Choices
    $('.questions').on('click', '#anchors', function(){
        $question = $(this).closest('.question');

        $modal = $('#modal').clone();
        $modal.find('.modal-title').html('Edit Anchoring');
        $modal.find('.save-button').attr('id', 'save-anchoring');
        $body = $modal.find('.modal-body');
        $body.html('<div class="weight-options"></div>'
            +'<div id="add-weight-option" class="btn btn-gray"><i class="fa-plus"></i> Add Anchor</div>'
            +'&nbsp;&nbsp;&nbsp;<div class="btn-group dropdown">'
                +'<button id="question-type" type="button" class="btn btn-gray dropdown-toggle" data-toggle="dropdown"><i class="fa-plus"></i> Add From Template</button>'
                +'<ul class="question-type dropdown-menu" role="menu">'
                    +'<li class="dropdown-header">Anchor Templates</li>'
                    +'<li id="add-options-agree-disagree"><a><i class="fa-list-alt"></i> <span>Agree/Disagree 5 Levels</span></a></li>'
                    +'<li id="add-options-yes-no"><a><i class="fa-list-alt"></i> <span>Yes or No</span></a></li>'
                +'</ul>'
            +'</div>'
            +'&nbsp;&nbsp;&nbsp;<div id="reverse-score" class="btn btn-gray"><i class="fa-exchange"></i> Reverse Scoring</div>'
        ).on('click', '#add-weight-option', function(){
            add_weight_option(0, '', $body);
        }).on('click', '#add-options-agree-disagree', function(){
            add_weight_option(1, "Strongly Disagree", $body);
            add_weight_option(2, "Disagree", $body);
            add_weight_option(3, "Neither Agree Nor Disagree", $body);
            add_weight_option(4, "Agree", $body);
            add_weight_option(5, "Strongly Agree", $body);
        }).on('click', '#add-options-yes-no', function(){
            add_weight_option(1, "Yes", $body);
            add_weight_option(-1, "No", $body);
        }).on('click', '#reverse-score', function(){
            var total_values = $('.weight-options .weight-input').length;
            if (! total_values)
                return false;
            var values = [];
            var i = total_values - 1;
            $('.weight-options .weight-input').each(function(){
                var value = $('.weight-value', this).val();
                values[i] = value;
                i -= 1;
            });
            var i = 0;
            $('.weight-options .weight-input').each(function(){
                $('.weight-value', this).val(values[i]);
                i += 1;
            });
        });

        $anchors = $question.find('#anchors');
        $('.anchor', $anchors).each(function(){
            if ($(this).hasClass('disabled'))
                return false;
            var value = $(this).attr('data-value');
            var tag = $(this).html();
            add_weight_option(value, tag, $body);
        });

        //$editor.val(current_content);
        //$editor.ckeditor();

        $modal.modal('show').on('click', '#save-anchoring', function(){
            $anchors.html('');
            $('.weight-options .weight-input').each(function(){
                var value = $('.weight-value', this).val();
                var tag = $('.weight-tag', this).val();
                $anchors.append('<div class="anchor" data-value="'+value+'">'+tag+'</div>');
            });
            if ($anchors.html() == '')
                $anchors.append('<div class="anchor disabled">No Anchors Specified</div>');
            $modal.modal('hide');
        });

        //$content_edit_field.hide().removeClass('active');
        //$content.show();
        //$number.show();

        //$modal.on('show.bs.modal', function() {
            //update_tags();
        //});

        $modal.on('hidden.bs.modal', function() {
            $modal.remove();
        });
    });

    // Set Question Type
    //$('.questions').on('click', '.question-type li', function(){
    //    if ($(this).hasClass('disabled'))
    //        return false;
    //    var question_type_id = $(this).attr('data-type');
    //    var question_type_name = $('span', this).html();
    //    $question = $(this).closest('.question');
    //    $question_type = $question.find('#question-type');
    //    $question_type.attr('data-type', question_type_id).html(question_type_name);
    //});

    // Set Question Type
    $('.questions').on('click', '#question-type', function(){
        $question = $(this).closest('.question');
        $question_type_field = $question.find('#question-type-field');

        if ($question_type_field.hasClass('active'))
            return false;

        $question_type_field.fadeIn().addClass('active');

        var question_type = parseInt($question.find('#question-type').attr('data-id'));
        $question_type_field.find('.question-type').removeClass('active');
        $question_type_field.find('.question-type[data-id="'+question_type+'"]').addClass('active');

    });

    // Choose Question Type
    $('.questions').on('click', '.question-type > span', function(){
        $question_type_field = $(this).closest('#question-type-field');
        if (! $(this).parent().hasClass('active')) {
            $question = $(this).closest('.question');
            $question_type_field.find('.question-type').removeClass('active');
            $(this).parent().addClass('active');

            // Update question type
            var question_type = parseInt($question_type_field.find('.question-type.active').attr('data-id'));
            var question_type_name = $question_type_field.find('.question-type.active').attr('data-name');
            var question_type_icon = $question_type_field.find('.question-type.active').attr('data-icon');
            var question_type_description = $question_type_field.find('.question-type.active').attr('data-description');
            var question_type_default = $question_type_field.find('.question-type.active').attr('data-default');
            $question.find('#question-type').attr('data-id', question_type).html('<i class="'+question_type_icon+'"></i> '+question_type_name);
            $question.find('#description').html(question_type_description);
            $question.find('#content-edit-field input.question-edit-input').val(question_type_default);
            $question.find('#content').html(question_type_default);

            switch (question_type) {
                case 1: // multiple choice
                    $question.find('.anchors-column').show();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').show();
                    $question.find('#practice').hide();
                    $question.find('.react-comp').remove();
                    break;

                case 2: // description
                    $question.find('.anchors-column').hide();
                    $question.find('#number').hide();
                    $question.find('#dimension').hide();
                    $question.find('#content').show();
                    $question.find('#practice').hide();
                    $question.find('.react-comp').remove();
                    break;

                case 3: // text field
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').show();
                    $question.find('#practice').hide();
                    $question.find('.react-comp').remove();
                    break;

                case 4: // letters
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').show();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    break;

                case 5: // equation
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').show();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    break;

                case 6: // math and letters
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').hide();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    add_wm_widget($question, question_type);
                    break;

                case 7: // square sequence
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').hide();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    add_wm_widget($question, question_type);
                    break;

                case 8: // symmetry
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').hide();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    add_wm_widget($question, question_type);
                    break;

                case 9: // square symmetry
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').hide();
                    $question.find('#practice').show();
                    $question.find('.react-comp').remove();
                    add_wm_widget($question, question_type);
                    break;

                case 10: // instructions
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').show();
                    $question.find('#content').hide();
                    $question.find('#practice').hide();
                    $question.find('.react-comp').remove();
                    add_wm_widget($question, question_type);
                    break;

                case 11: // slider
                    $question.find('.anchors-column').hide();
                    $question.find('#number').show();
                    $question.find('#dimension').hide();
                    $question.find('#content').show();
                    $question.find('#practice').hide();
                    $question.find('.react-comp').remove();
                    break;
            }

            update_question_numbers();
        }

        $question_type_field.fadeOut(function(){
            $(this).removeClass('active');
        });
    });

    // Cancel Question Type
    $('.questions').on('click', '#cancel-question-type', function(){
        $question_type_field = $(this).closest('#question-type-field');
        $question_type_field.fadeOut().removeClass('active');
    });

    // Set Practice
    $('.questions').on('click', '#practice', function(){
        var practice = $(this).attr('data-practice');
        if (practice == 1)
            $(this).removeClass('active').attr('data-practice', 0).html('Test Question');
        else
            $(this).addClass('active').attr('data-practice', 1).html('Practice Question');
    });

    // Set Dimension
    $('.questions').on('click', '#dimension', function(){
        $question = $(this).closest('.question');
        $dimension_field = $question.find('#dimension-field');

        if ($dimension_field.hasClass('active'))
            return false;

        $dimension_field.fadeIn().addClass('active');
    });

    // Choose Dimension
    $('.questions').on('click', '.dimension > span', function(){
        $dimension_field = $(this).closest('#dimension-field');
        if (!$(this).parent().hasClass('active')) {
            $question = $(this).closest('.question');
            $('.dimension', $question).removeClass('active');
            $(this).parent().addClass('active');

            if ($(this).next('.sub-dimensions').length)
                $dimension_field.addClass('subdimension-field');
            else {
                // Update question
                var number = $question.find('#number').html();
                var dim = $dimension_field.find('.dimension.active').attr('data-code');
                //var subdim = $dimension_field.find('.sub-dimension.active').attr('data-code');
                //if (subdim)
                    //var dimension = $dimension_field.find('.sub-dimension.active').attr('data-id');
                //else
                    var dimension = $dimension_field.find('.dimension.active').attr('data-id');
                //$rflag = ($('#reverse').is(':checked') ? 'r' : '');

                $set_dimension = $question.find('#dimension');
                $set_dimension.html('Dimension: <span id="code"><span class="dim-code">'+dim+'</span><span class="reverse-flag"></span><span class="number">'+number+'</span></span>').addClass('active');
                $set_dimension.attr('data-dimension', dimension);

                $dimension_field = $(this).closest('#dimension-field');
                $dimension_field.fadeOut(function(){
                    $(this).removeClass('active');
                });
            }
        }
        else {
            $(this).parent().removeClass('active');
            $dimension_field.removeClass('subdimension-field');
        }
    });

    // Choose Sub-dimension
    $('.questions').on('click', '.sub-dimension', function(){
        $question = $(this).closest('.question');
        $('.sub-dimension', $question).removeClass('active');
        $(this).addClass('active');

        // Update question
        var number = $question.find('#number').html();
        var dim = $dimension_field.find('.dimension.active').attr('data-code');
        var subdim = $dimension_field.find('.sub-dimension.active').attr('data-code');
        if (subdim)
            var dimension = $dimension_field.find('.sub-dimension.active').attr('data-id');
        else
            var dimension = $dimension_field.find('.dimension.active').attr('data-id');
        //$rflag = ($('#reverse').is(':checked') ? 'r' : '');

        $set_dimension = $question.find('#dimension');
        $set_dimension.html('Dimension: <span id="code"><span class="dim-code">'+dim+subdim+'</span><span class="reverse-flag"></span><span class="number">'+number+'</span></span>').addClass('active');
        $set_dimension.attr('data-dimension', dimension);

        $dimension_field = $(this).closest('#dimension-field');
        $dimension_field.fadeOut(function(){
            $(this).removeClass('subdimension-field').removeClass('active');
        });
    });

    // Cancel Dimension
    $('.questions').on('click', '#cancel-dimension', function(){
        $dimension_field = $(this).closest('#dimension-field');
        $dimension_field.fadeOut().removeClass('subdimension-field').removeClass('active');
    });

    // Remove
    $('.questions').on('click', '#remove-question', function(){
        $question = $(this).closest('.question');
        $remove_field = $question.find('#remove-field');

        if ($remove_field.hasClass('active'))
            return false;

        $remove_field.fadeIn().addClass('active');
    });

    // Cancel Remove
    $('.questions').on('click', '#cancel-remove', function(){
        $remove_field = $(this).closest('#remove-field');

        if (!$remove_field.hasClass('active'))
            return false;

        $remove_field.fadeOut().removeClass('active');
    });

    // Confirm Remove
    $('.questions').on('click', '#confirm-remove', function(){
        $question = $(this).closest('.question');
        $remove_field = $question.find('#remove-field');

        if (!$remove_field.hasClass('active'))
            return false;

        $question.hide().addClass('deleted').insertAfter($('form'));

        update_question_numbers();
    });

    // Add Question
    $('#add-question').on('click', function(){
        $template = $('#question-template').find('li.list-item');
        $template.clone().appendTo('.questions');
        update_question_numbers();
    });

    // Duplicate Question
    $('.questions').on('click', '#duplicate-question', function(){
        $question = $(this).closest('.question');
        $template = $question.parent('li');
        $new_question = $template.clone().insertAfter($template);
        $new_question.find('#id').remove();
        update_question_numbers();
    });

    // Remove Row
    $('body').on('click', '#remove-weight-option', function(){
        $row = $(this).closest('.weight-input');
        $row.remove();
    });

    // Add needed class for the question template to work
    $('#question-template').find('li').addClass('uk-nestable-list-item');

    // Add weight option
    function add_weight_option(default_weight = 0, default_tag = '', $self = $(this))
    {
        //$question = $(this).closest('.question');
        //$modal = $('#modal');
        $template = $('#weight-option-template').find('.weight-input');
        $target = $self.find('.weight-options');
        $newRow = $template.clone().appendTo($target);
        $newRow.find('.bootstrap-tagsinput').remove();

        if (default_weight != 0)
            $newRow.find('.weight-value').val(default_weight);
        if (default_tag != '')
            $newRow.find('.weight-tag').val(default_tag);

        $('input[data-role="tagsinput"]', $newRow).tagsinput({
            maxTags: 1,
        });
        $('.bootstrap-tagsinput', $newRow).find('input').attr('style', '');
    }

    // Update Tags
    function update_tags()
    {
        $('#modal input[data-role="tagsinput"]').tagsinput({
            maxTags: 1,
        });
        $('#modal .bootstrap-tagsinput').find('input').attr('style', '');
    }



    // WYSYWIG Editor
    //$(".wysiwyg").each(function(i, el)
    //{
    //    var $this = $(el);
    //        //stylesheets = attrDefault($this, 'stylesheet-url', '')
    //
    //    $this.wysihtml5({
    //        size: 'white',
    //        //stylesheets: stylesheets.split(','),
    //        "html": attrDefault($this, 'html', true),
    //        "color": attrDefault($this, 'colors', true),
    //    });
    //});

    $(".questions").on('nestable-stop', function(ev)
    {
        update_question_numbers();
    });

    // Update Question Numbers
    function update_question_numbers()
    {
        var number = 1;
        $('.questions .question').each(function()
        {
            var question_type = $(this).find('#question-type').attr('data-id');

            if (question_type == 2) // Description
                return;

            $('#number', this).html(number);
            number += 1;
        });
    }

    function iterateList(items, depth)
    {
        var str = '';

        if (! depth)
            depth = 0;

        //console.log(items);

        jQuery.each(items, function(i, obj)
        {
            str += '[ID: ' + obj.itemId + ']\t' + repeat('—', depth+1) + ' ' + obj.item;
            str += '\n';

            if(obj.children)
            {
                str += iterateList(obj.children, depth+1);
            }
        });

        return str;
    }

    function repeat(s, n)
    {
        var a = [];
        while(a.length < n)
        {
            a.push(s);
        }
        return a.join('');
    }

    $('form').submit(function(){
        var formData = new FormData($(this)[0]);
        var url = window.location.pathname.replace('/create', '').replace('/edit', '');

        // Save all the questions
        var questions = [];
        var i = 0;
        $('form .question').each(function()
        {
            var id = $('#id', this).val();
            if (! id) id = 0;

            var anchors = [];
            var k = 0;
            $('#anchors .anchor', this).each(function()
            {
                if ($(this).hasClass('disabled'))
                    return false;

                var anchor = {
                    'tag': $(this).html(),
                    'value': $(this).attr('data-value')
                };

                anchors.push(anchor);
            });

            questions[i] = {
                'id': id,
                'content': $('#content', this).html(),
                'number': $('#number', this).html(),
                'anchors': anchors,
                'dimension_id': $('#dimension', this).attr('data-dimension'),
                'type': $('#question-type', this).attr('data-id'),
            };
            i += 1;
        });
        formData.append('questions', JSON.stringify(questions));

        // Track deleted questions
        var deleted_questions = [];
        var i = 0;
        $('.question.deleted').each(function()
        {
            var id = $('#id', this).val();
            if (! id) return false;

            deleted_questions[i] = {
                'id': id,
            };
            i += 1;
        });
        formData.append('deleted_questions', JSON.stringify(deleted_questions));

        // Set some other variables
        // if (! formData['paginate']) data['paginate'] = 0;
        // if (! formData['timed']) data['timed'] = 0;
        // if (! formData['translation']) data['translation'] = 0;
        // if (! formData['whitelabel']) data['whitelabel'] = 0;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                $('html').prepend(data);

                if (data['redirect'])
                    window.location.href = data['redirect'];

                if (data['reload'])
                    window.location.reload();
            },
            error: function(data) {
                // $('html').prepend(data.responseText);
                var errors = data.responseJSON;
                for (var i in errors)
                {
                    if (! errors.hasOwnProperty(i)) continue;

                    var opts = {
                        "closeButton": true,
                        "debug": false,
                        "positionClass": "toast-top-right",
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    toastr.error(errors[i], "Error", opts);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    // Save Assessment
    $('#save').on('click', function(e)
    {
        e.preventDefault();
        $(this).attr('disabled', 'disabled').html('Submitting.. <i class="fa-spinner fa-spin"></i>');

        // Save all the questions
        var questions = [];
        var i = 0;
        $('form .question').each(function()
        {
            var id = $('#id', this).val();
            if (! id) id = 0;

            var anchors = [];
            var k = 0;
            $('#anchors .anchor', this).each(function()
            {
                if ($(this).hasClass('disabled'))
                    return false;

                var anchor = {
                    'tag': $(this).html(),
                    'value': $(this).attr('data-value')
                };

                anchors.push(anchor);
            });

            var type = $('#question-type', this).attr('data-id');
            var content = $('#content', this).html();

            questions[i] = {
                'id': id,
                'content': content,
                'number': $('#number', this).html(),
                'anchors': anchors,
                'dimension_id': $('#dimension', this).attr('data-dimension'),
                'type': type,
                'practice': $('#practice', this).attr('data-practice'),
            };
            i += 1;
        });

        // Track deleted questions
        var deleted_questions = [];
        var i = 0;
        $('.question.deleted').each(function()
        {
            var id = $('#id', this).val();
            if (! id) return false;

            deleted_questions[i] = {
                'id': id,
            };
            i += 1;
        });

        // Get all our data together
        var data = $('form').serializeObject();
        if (! data['paginate']) data['paginate'] = 0;
        if (! data['timed']) data['timed'] = 0;
        if (! data['translation']) data['translation'] = 0;
        if (! data['whitelabel']) data['whitelabel'] = 0;
        data['questions'] = questions;
        data['deleted_questions'] = deleted_questions;
        // if (! data['logo']) data['logo'] = $('#logo')[0].files[0];
        // if (! data['background']) data['background'] = $('#background')[0].files[0];

        // Get our URL
        var url = window.location.pathname.replace('/create', '').replace('/edit', '');

        // Perform the request
        $.ajax({
            type: 'post',
            url: url,
            data: data,
            dataType: 'json',
            // contentType: false,
            // processData: false,
            success: function(data) {
                console.log(data);
                $("body").prepend(data);
                //var opts = {
                //    "closeButton": true,
                //    "debug": false,
                //    "positionClass": "toast-top-right",
                //    "onclick": null,
                //    "showDuration": "300",
                //    "hideDuration": "1000",
                //    "timeOut": "5000",
                //    "extendedTimeOut": "1000",
                //    "showEasing": "swing",
                //    "hideEasing": "linear",
                //    "showMethod": "fadeIn",
                //    "hideMethod": "fadeOut"
                //};
                //toastr.success("The assessment has been saved successfully!", "Success", opts);

                if (data['redirect']) {
                    setTimeout(function() {
                        window.location.replace(data['redirect']);
                    }, 1000);
                }

                if (data['reload']) {
                    window.location.reload();
                }
            },
            error: function(data) {
                $('html').prepend(data.responseText);
                var errors = data.responseJSON;
                for (var i in errors)
                {
                    if (! errors.hasOwnProperty(i)) continue;

                    var opts = {
                        "closeButton": true,
                        "debug": false,
                        "positionClass": "toast-top-right",
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };

                    toastr.error(errors[i], "Error", opts);
                }
            }
        });
    });

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

    // Reveal field by selection
    $('.reveal-field-by-selection').on('change', function(){
        $('.'+$(this).attr('data-field-to-reveal')).hide();
        $('.'+$(this).attr('data-field-to-reveal')+'.'+$(this).val()).slideDown();
    });

    // Check for fields that should already be revealed
    $('.reveal-field-by-selection').each(function(){
        $('.'+$(this).attr('data-field-to-reveal')).hide();
        $('.'+$(this).attr('data-field-to-reveal')+'.'+$(this).val()).show();
    });

    // Show sidebar sub-nav right away
    $('.sidebar-menu-under .menu-category[data-parent="Assessments"]').show();
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
