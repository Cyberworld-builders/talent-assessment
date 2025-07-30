@extends('dashboard.dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('assets/js/uikit/uikit.css') }}">--}}
    <style type="text/css">
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
        .remove-row-button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            color: #bbbbbb;
        }
        .remove-row-button:hover {
            color: #eee;
        }
    </style>
@stop

{{--@section('sidebar-class')--}}
{{--collapsed--}}
{{--@stop--}}

@section('content')

    <div class="row">
        <div class="col-sm-12">

            {{-- Title --}}
            <h1>Edit Function</h1><br/>

            <!-- Errors -->
            @include('errors.list')

            {!! Form::model($function, ['method' => 'PATCH', 'action' => ['FunctionsController@update', $function->id]]) !!}
                @include('dashboard.functions.form', ['button_name' => 'Save Changes'])
            {!! Form::close() !!}

            <!-- Scripts -->
            {{--<script src="{{ asset('js/create-assessment-form.js') }}"></script>--}}
            <script>
                (function($){
                    $(document).ready(function(){

                        // Set headers for AJAX
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                            }
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

                        $('.dimension-fields').on('click', '.remove-row-button', function(){
                            $(this).closest('.panel').remove();
                        });

                        // Remove cached values from user add form
                        function clear_dimension_add_form()
                        {
                            $('.dimension-add-form').val('');
                            $('.dimension-add-form .select2-search-choice').remove();
                            $('.dimension-add-form .select2-input').attr('placeholder', 'Add New Dimension').width(380);
                        }
                        clear_dimension_add_form();

                        // User add form
                        $('.dimension-add-form').on('change', function(){
                            var dim_id = $(this).val();

                            console.log(dim_id[0]);

                            // Add dimension
                            add_dimension(dim_id[0]);

                            clear_dimension_add_form();
                        });

                        function add_dimension(dim_id)
                        {
                            var data = {
                                'id': dim_id,
                            };
                            var url = '/dashboard/functions/add-dimension';

                            $.ajax({
                                type: 'post',
                                url: url,
                                data: data,
                                dataType: 'json',
                                success: function (data) {
                                    console.log('response: ');
                                    console.log(data);
                                    $dimension = $('.templates .dimension').clone();
                                    $dimension.find('#dimension-name').text(data.name);
                                    $('.dimension-fields').append($dimension);
                                },
                                error: function (data) {
                                    console.log(data.status + ' ' + data.statusText);
                                    $('html').prepend(data.responseText);
                                }
                            });
//
//                                    if (data['user'])
//                                    {
//                                        var user = data['user'];
//                                        $dimension = $('.templates .dimension').clone();
//                                        $('.dimension-fields').append($dimension);
//                                        $user_add_form.find('input[name="user[]"]').val(user['id']);
//                                        $user_add_form.find('#user-name').text(user['name']);
//                                        $user_add_form.find('#user-username-email').text(user['username'] + ' (' + user['email'] + ')');

//                                        $assessment_tabs = $user_add_form.find('.assessment-tabs');
//                                        if (data['assessments'])
//                                        {
//                                            for (var i = 0; i < data['assessments'].length; i += 1)
//                                            {
//                                                var assessment = data['assessments'][i];
//                                                $assessment_tab = $('.templates .assessment').clone();
//                                                $assessment_tab.find('#assessment-name').text(assessment['name']);
//                                                $assessment_tab.find('#assessment-logo').attr('src', assessment['logo']);
//
//                                                if (assessment['custom_fields'])
//                                                {
//                                                    $custom_fields_field = $assessment_tab.find('.custom-fields-field');
//                                                    $custom_fields = $('.templates .custom-fields').clone();
//                                                    $custom_fields_field.append($custom_fields);
//                                                    $custom_fields_tabs = $custom_fields.find('.custom-fields-tabs');
//
//                                                    for (var j = 0; j < assessment['custom_fields']['tag'].length; j += 1)
//                                                    {
//                                                        var tag = assessment['custom_fields']['tag'][j];
//                                                        $custom_field = $('.templates .custom-field').clone();
//                                                        $custom_field.find('#custom-tag').text('[' + tag + ']');
//                                                        $custom_fields_tabs.append($custom_field);
//                                                    }
//                                                }
//                                                else
//                                                    $assessment_tab.find('.right-arrow').remove();
//
//                                                if (i > 0)
//                                                    $assessment_tabs.append('<hr/>');
//                                                $assessment_tabs.append($assessment_tab);
//                                            }
//                                        }
//
//                                        $('.user-forms').append($user_add_form);
//                                    }
//                                },
//                                error: function (data) {
//                                    console.log(data.status + ' ' + data.statusText);
//                                    $('html').prepend(data.responseText);
//                                }
//                            });
                        }
                    });
                })(jQuery);
            </script>

        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    {{--<script src="{{ asset('assets/js/uikit/js/uikit.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/uikit/js/addons/nestable.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>--}}
@stop