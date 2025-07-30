jQuery(document).ready(function($){

    // Set headers for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        }
    });

    // Fix for cached disabled attribute
    $('#save').removeAttr('disabled');

    // Initialize the CKEditor
    $('#modal-wysiwyg textarea').ckeditor();

    // Edit Question
    // $('.questions').on('click', '#content', function(){
    //     $question = $(this).closest('.question');
    //     $content = $question.find('#content');
    //     $content_edit_field = $question.find('#content-edit-field');
    //
    //     if ($content_edit_field.hasClass('active'))
    //         return false;
    //
    //     var current_content = $content.html();
    //
    //     $content_edit_field.find('input').val(current_content);
    //     $content.hide();
    //     $content_edit_field.fadeIn().addClass('active').find('input').focus().select();
    // });

    // WYSIWYG Description Editor
    $('.edit-description-with-wysiwyg').on('click', function(){
        var current_content = $(this).val();

        $edit_field = $(this);

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

    // WYSIWYG Question Editor
    $('.edit-with-wysiwyg').on('click', function(){
        var current_content = $(this).next('textarea').val();

        $edit_field = $(this).next('textarea'); 

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

    // Detect other similar anchors
    $('.anchor').on('change', function(){
        var original_anchor = $(this).closest('.row').find('.anchor-original').html();
        var val = $(this).val();

        var i = 0;
        $('.anchor-original').not($(this).closest('.row').find('.anchor-original')).each(function(){
            if ($(this).html() == original_anchor) {
                i += 1;
            }
        });

        if (i > 0) {
            if (confirm(i+' other anchors titled '+original_anchor+' were found in this assessment. Would you like to translate all of them to '+val+'?')) {
                $('.anchor-original').not($(this).closest('.row').find('.anchor-original')).each(function(){
                    if ($(this).html() == original_anchor) {
                        $(this).closest('.row').find('.anchor').val(val);
                    }
                });
            }
        }
    });

    // Save Translation
    $('#save').on('click', function(e){
        e.preventDefault();

        if ($('#language_id').val() == '')
        {
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

            toastr.error("Language must be specified.", "Error", opts);
            return;
        }

        $(this).attr('disabled', 'disabled').html('Submitting.. <i class="fa-spinner fa-spin"></i>');

        var questions = [];
        var i = 0;

        $('form .question').each(function(){

            var id = $('#id', this).val();
            if (! id) id = 0;

            var type = $('#type', this).val();

            // Get anchors
            var anchors = [];
            if (type == 1)
            {
                var k = 0;
                $('.anchor', this).each(function()
                {
                    var anchor = $(this).val();
                    anchors.push(anchor);
                });
            }

            // Save content based on question type
            var content = '';
            if (type == 1 || type == 2 || type == 3)
                content = $('#content', this).val();

            if (type == 10)
                content = JSON.stringify({
                    text: $('#content', this).val(),
                    next: $('#button', this).val(),
                });

            // Collect our questions
            questions[i] = {
                'question_id': id,
                'content': content,
                'anchors': anchors,
            };
            i += 1;
        });

        var data = $('form').serializeObject();
        data['questions'] = questions;

        // console.log(data);

        //var url = '/dashboard/assessments';
        var url = window.location.pathname.replace('/create', '').replace('/edit', '');

        $.ajax({
            type: 'post',
            url: url,
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
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
                //var errors = data.responseJSON;
                //console.log(data.status+' '+data.statusText);
                //console.log(data);
                //for (var i in errors) {
                //    if (!errors.hasOwnProperty(i)) continue;
                //    $error = $(
                //        '<div style="display:none;" class="alert alert-danger alert-dismissible fade in" role="alert">'
                //        +'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                //        +'<i class="fa-exclamation-circle"></i> '+errors[i]+'</div>'
                //    );
                //    $('form').after($error);
                //    $error.fadeTo(2000, 500).fadeTo(1000, 0, function(){
                //        $(this).alert('close');
                //    });
                //}

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

//# sourceMappingURL=translate-assessment-form.js.map
