@extends('dashboard.dashboard')

@section('styles')
    <style type="text/css">
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .modal-body .users {
            margin-top: 20px;
            border-bottom: 2px solid #eee;
            overflow-y: scroll;
            max-height: 308px;
        }
        .modal-body .users .user {
            border: 2px solid #eee;
            border-bottom: none;
            padding: 5px 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .modal-body .users .user.selected {
            background: #eee;
            border-color: #ddd;
        }
        .modal-body .controls {
            margin-top: 10px;
            padding-left: 5px;
        }
        .modal-body .controls a {
            font-weight: bold;
            font-size: 11px;
            color: #999;
            cursor: pointer;
        }
        .modal-body .controls a:hover {
            color: #8dc63f;
        }
        #cke_1_contents {
            height: auto !important;
        }
        .form-wizard > .tabs > li.active a:after {
            background-color: #8dc63f;
        }
    </style>
@stop

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: Assignments</h1>
            <p class="description">View all assignments of all users for this client.</p>
        </div>
    </div>

    <div class="row">

        {{-- Sub Navigation --}}
        @include('dashboard.clients.partials._subnav', ['active' => 'Assignments'])

        @include('dashboard.clients.partials._assignments_by_date')
    </div>

    {{-- Users Modal --}}
    <div class="modal fade" id="modal-user-select">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Select Users</h4>
                </div>

                <div class="modal-body">
                    <div id="rootwizard" class="form-wizard">
                        <ul class="tabs">
                            <li class="active">
                                <a href="#tab1" data-toggle="tab">Customize Email</a>
                            </li>
                            <li>
                                <a href="#tab2" data-toggle="tab">Select Users</a>
                            </li>
                        </ul>
                        <div class="progress-indicator">
                            <span></span>
                        </div>
                        <div class="tab-content">

                            <!-- Tabs Content -->
                            <div class="tab-pane active" id="tab1">
                                {!! Form::label('email-subject', 'Subject', ['class' => 'control-label']) !!}
                                {!! Form::text('email-subject', null, ['class' => 'form-control input-lg', 'placeholder' => 'New assessments have been assigned to you']) !!}
                                <br/>
                                {!! Form::label('email-body', 'Body', ['class' => 'control-label']) !!}
                                {!! Form::hidden('email-body', $emailBody) !!}
                                <textarea id="Editor" class="form-control input-lg"></textarea>
                            </div>
                            <div class="tab-pane" id="tab2">
                                Select which users you wish to send an email to:
                                <div class="users"></div>
                                <div class="controls">
                                    <a class="select-all"><i class="fa-chevron-up"></i> Select All</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <a class="deselect-all"><i class="fa-times"></i> Deselect All</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-small-font btn-orange ok-button" disabled>Send Emails</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/adapters/jquery.js') }}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            $('.send-emails').on('click', function()
            {
                var dates = <?php echo json_encode($dates) ?>;
                var date = $(this).attr('data-date');
                $modal = $('#modal-user-select');

                // Setup users list
                var usersHtml = '';
                for (var i = 0; i < dates[date]['users'].length; i += 1)
                {
                    var user = dates[date]['users'][i];
                    usersHtml += '<div class="user" data-id="'+user['id']+'">'+user['name']+'</div>';
                }
                $modal.find('.modal-body .users').html(usersHtml);

                // Setup editor
                var current_content = $('input[name="email-body"]').val();
                $textarea = $modal.find('textarea').html(current_content);
                if (! CKEDITOR.instances.Editor)
                    $editor = $textarea.ckeditor();
                else
                    CKEDITOR.instances.Editor.setData(current_content);

                // Show the modal
                $modal.modal('show');

                // Send emails
                $modal.on('click', '.ok-button', function(){
                    var users = [];
                    $('.user.selected', $modal).each(function() {
                        users.push($(this).attr('data-id'));
                    });
                    var message = CKEDITOR.instances.Editor.document.getBody().getHtml();
                    var subject = $('input[name="email-subject"]').val();

                    // Send the emails
                    var data = {
                        users: users,
                        message: message,
                        subject: subject,
                        date: date
                    };
                    var url = window.location.pathname + '/send-assignment-email';

                    $.ajax({
                        type: 'post',
                        url: url,
                        data: data,
                        dataType: 'json',
                        success: function (data) {
                            //console.log(data);
                            if (data['success'])
                            {
                                toastr.success('Email sent successfully!', "Success", opts);
                            }
                        },
                        error: function (data) {
                            console.log(data.status + ' ' + data.statusText);
                            $('html').prepend(data.responseText);
                            toastr.error('An error has occurred.', "Error", opts);
                        }
                    });

                    $modal.modal('hide');
                });

                // Select users
                $modal.on('click', '.user', function(){
                    if ($(this).hasClass('selected'))
                        $(this).removeClass('selected');
                    else
                        $(this).addClass('selected');

                    // Disable send button if no users are selected
                    if ($('.user.selected', $modal).length)
                        $('.ok-button', $modal).removeAttr('disabled');
                    else
                        $('.ok-button', $modal).attr('disabled', '');
                });

                // Select all users
                $modal.on('click', '.select-all', function(){
                    $('.user', $modal).addClass('selected');
                    $('.ok-button', $modal).removeAttr('disabled');
                });

                // De-select all users
                $modal.on('click', '.deselect-all', function(){
                    $('.user', $modal).removeClass('selected');
                    $('.ok-button', $modal).attr('disabled', '');
                });

                // Unbind click events when modal is hidden
                $modal.on('hidden.bs.modal', function() {
                    $modal.off('click');
                });
            });

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var assessment = $(this).attr('data-assessment');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete the assignment for '+assessment+' for '+name+'?'))
                    form.submit();
            });

            // Server-sent Events
            var es;

            $('#download-all-data').on('click', function()
            {
                var url = '/dashboard/assignments/download/{{ $client->id }}';
                es = new EventSource(url);

                // Add a cancel option
                $cancel = $('<a id="cancel-download"><i class="fa-times"></i> Cancel</a>');
                $('#progress-text').after($cancel);

                // Listen for messages
                es.addEventListener('message', function(e) {
                    var result = JSON.parse(e.data);

                    // Completed
                    if (result.i == -1)
                    {
                        es.close();
                        $('#progress-text').text('');
                        $('#progress-bar').css('width', '0%');
                        $('#cancel-download').remove();
                        window.location = '/download/' + result.message.file;
                    }

                    // Update progress
                    else
                    {
                        $('#progress-text').text('Preparing data... ' + result.message.toFixed(2) + '%');
                        $('#progress-bar').css('width', result.message.toFixed(2) + '%');
                    }
                });

                // Error
                es.addEventListener('error', function(e) {
                    console.log(e);
                    alert('Error occurred');
                    es.close();
                });
            });

            // Cancel download
            $('.progress-status').on('click', '#cancel-download', function(){
                es.close();
                $('#progress-text').text('');
                $('#progress-bar').css('width', '0%');
                $(this).remove();
            });

            // Toaster Options
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
        });
    </script>

@stop