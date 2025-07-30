jQuery(document).ready(function($){

    // Set headers for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
        }
    });

    // Check for questions that are already answered
    $('.question').each(function(){
        if ($(this).is(':checked'))
            $(this).parent('.btn').addClass('active');
    });

    // Save Answer
    $('.question').on('change', function(e){
        e.preventDefault();

        var id = $(this).attr('id');
        var value = $(this).val();

        var data = {
            'complete': 0,
            'question_id': id,
            'value': value
        };

        var url = window.location.pathname;

        console.log('id: '+id);
        console.log(url);
        console.log(data);

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
                //toastr.success("Asnwer recorded successfully.", "Success", opts);
            },
            error: function(data) {

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

    // Complete Assessment
    $('#complete').on('click', function(e){
        e.preventDefault();

        var data = {
            'complete': 1
        };

        var url = window.location.pathname;

        console.log(url);
        console.log(data);

        $(this).attr('disabled', 'disabled').html('Submitting.. <i class="fa-spinner fa-spin"></i>');

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
                //toastr.success("Asnwer recorded successfully.", "Success", opts);
                if (data['reload']) {
                    window.location.reload();
                }
            },
            error: function(data) {
                console.log(data.status+' '+data.statusText);
                $('html').prepend(data.responseText);

                //var errors = data.responseJSON;
                //
                //for (var i in errors)
                //{
                //    if (! errors.hasOwnProperty(i)) continue;
                //
                //    var opts = {
                //        "closeButton": true,
                //        "debug": false,
                //        "positionClass": "toast-top-right",
                //        "onclick": null,
                //        "showDuration": "300",
                //        "hideDuration": "1000",
                //        "timeOut": "5000",
                //        "extendedTimeOut": "1000",
                //        "showEasing": "swing",
                //        "hideEasing": "linear",
                //        "showMethod": "fadeIn",
                //        "hideMethod": "fadeOut"
                //    };
                //
                //    toastr.error(errors[i], "Error", opts);
                //}
            }
        });
    });
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
