@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="assets/js/multiselect/css/multi-select.css">
    <style>
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
        .user-add-form .user-name {
            font-size: 18px;
            font-weight: bold;
            margin: 4px 0 7px 0;
        }
        .user-add-form .user-tab i {
            color: #bebebe;
            padding-right: 5px;
        }
        .remove-task, .remove-ksa, .remove-position, .remove-rating {
            position: absolute;
            top: 29px;
            left: -28px;
            cursor: pointer;
            padding: 5px;
            z-index: 10;
        }
        .remove-ksa {
            top: 64px;
        }
        .remove-rating {
            top: 64px;
        }
        .remove-position {
            top: 9px;
            left: -15px;
        }
        .remove-task:hover, .remove-ksa:hover, .remove-position:hover {
            color: #aaa;
        }
        .task, .ksa, .rating, .position, .position-col {
            position: relative;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            {{-- Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Analysis Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The name of the analysis.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Job Codes --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('job_code', 'Job Code(s)', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the job codes field.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('job_code', null, ['class' => 'form-control', 'data-role' => 'tagsinput', 'style' => 'display:none;']) !!}
                </div>
            </div>

            {{-- Department Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('department_name', 'Department Name(s)', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the department name field.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('department_name', null, ['class' => 'form-control', 'data-role' => 'tagsinput', 'style' => 'display:none;']) !!}
                </div>
            </div>

            {{-- Location --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('location', 'Location(s)', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the location field.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('location', null, ['class' => 'form-control', 'data-role' => 'tagsinput', 'style' => 'display:none;']) !!}
                </div>
            </div>

            {{-- Supervisor Title --}}
            <div class="row" style="margin-bottom:20px;">
                <div class="col-sm-3">
                    {!! Form::label('supervisor_title', 'Supervisor Title(s)', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Pre-fill the supervisor title field.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('supervisor_title', null, ['class' => 'form-control', 'data-role' => 'tagsinput', 'style' => 'display:none;']) !!}
                </div>
            </div>

            {{-- Positions --}}
            <div class="positions">
                @if ($edit and $analysis->position)
                    @foreach ($analysis->position as $i => $position)
                        @include('dashboard.analysis.partials._position', [
                            'position' => $position,
                            'index' => $i,
                            'analysis' => $analysis
                        ])
                    @endforeach
                @else
                    @include('dashboard.analysis.partials._position', [
                        'position' => '',
                        'index' => 0,
                        'analysis' => null
                    ])
                @endif
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-small add-position"><i class="fa-plus"></i> Add Another Position</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="col-sm-12">

    <!-- Add Users To Analysis Button -->
    <div class="row">
        <div class="pull-left">
            <a class="btn btn-black toggle-modal" data-toggle="add-users"><i class="fa-plus"></i> Add Users To This Analysis</a>
        </div>
    </div><br/>

    <!-- Users -->
    <div class="row">
        <div class="user-forms">
            @if ($edit and $analysis->users)
                @foreach ($analysis->users as $userId)
                    <?php $user = \App\User::find($userId); ?>
                    @if ($user)
                        @include('dashboard.analysis.partials._userform', [
                            'userId' => $user->id,
                            'user' => $user->name,
                            'username' => $user->username,
                            'email' => $user->email,
                        ])
                    @endif
                @endforeach
            @endif
        </div>
    </div>

</div>

<!-- User Form Template -->
<div class="templates" style="display:none;">
    @include('dashboard.analysis.partials._userform', [
        'userId' => '',
        'user' => '',
        'username' => '',
        'email' => '',
    ])
    <div class="position-template">
        @include('dashboard.analysis.partials._position', [
            'position' => '',
            'index' => '',
            'analysis' => null
        ])
    </div>
    <div class="task-template">
        @include('dashboard.analysis.partials._task', [
            'position' => '',
            'task' => '',
            'index' => 0
        ])
    </div>
    <div class="ksa-template">
        @include('dashboard.analysis.partials._ksa', [
            'position' => '',
            'ksa' => '',
            'description' => '',
            'index' => 0
        ])
    </div>
    <div class="rating-template">
        @include('dashboard.analysis.partials._rating', [
            'position' => '',
            'type' => '',
            'id' => '',
            'description' => '',
            'index' => 0,
        ])
    </div>
</div>

<!-- Add Users Modal -->
<div class="modal fade" id="add-users">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add Users</h4>
            </div>

            <div class="modal-body">
                {!! Form::select('users[]', $usersArray, null, ['class' => 'form-control input-lg', 'id' => 'users', 'multiple']) !!}
                <script type="text/javascript">
                    jQuery(document).ready(function($)
                    {
                        $("#users").select2({
                            placeholder: 'Select User(s)',
                            allowClear: true
                        }).on('select2-open', function()
                        {
                            // Adding Custom Scrollbar
                            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                        });
                    });
                </script>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-small-font btn-black" data-dismiss="modal">Cancel</button>
                <button type="button" id="submit" class="btn btn-small-font btn-orange save-button">Add Users</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){

        // Set headers for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });

        // Submit the form
        $('form:first').on('submit', function(e){
            var users = $('.user-forms > div').length;

            if (! users)
            {
                e.preventDefault();
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
                toastr.error("Users are required. Please add users to this analysis.", "Error", opts);
            }
        });

        // Auto-size textarea boxes
        $('.autosize').autosize();

        // Checkboxes
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_square-aero',
            radioClass: 'iradio_square-aero'
        });

        // Toggle the specified modal box
        $('.toggle-modal').on('click', function(){
            var toggle = $(this).attr('data-toggle');
            $modal = $('#'+toggle);

            // Clear any cached input in the users input field
            $('#s2id_users .select2-search-choice', $modal).remove();
            $('select[name="users[]"]', $modal).val('');

            // Show the modal, Submit modal form
            $modal.modal('show').on('click', '#submit', function(){

                // Get all user ids from the input field
                var user_ids = $('#users').val();
                var data = {
                    'ids': user_ids,
                };
                var url = '/dashboard/users/get_users_from_ids';

                // Get users from the server
                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data)
                    {
                        // Show server errors
                        $('html').prepend(data.responseText);

                        // If we have a response
                        if (data['users']){
                            var users = data['users'];
                            for (var i in users)
                            {
                                // Skip loop if the property is from prototype
                                if (! users.hasOwnProperty(i)) continue;
                                var user = users[i];

                                // Find the fields in the user template form
                                var id_field = '.user-id';
                                var name_field = '.user-name';
                                var username_field = '.user-username';
                                var email_field = '.user-email';

                                // Populate them with the server data
                                $user_add_form = $('.templates .panel').clone();
                                $user_add_form.find(id_field).val(user.id);
                                $user_add_form.find(name_field).text(user.name);
                                $user_add_form.find(username_field).text(user.username);
                                $user_add_form.find(email_field).text(user.email);

                                // Append the new user form to the DOM
                                $('.user-forms').append($user_add_form);
                            }
                            $modal.modal('hide');
                        }
                    },
                    error: function (data) {
                        console.log(data.status + ' ' + data.statusText);
                        $('html').prepend(data.responseText);
                    }
                });
            });
        });

        // Remove a row
        $('.user-forms').on('click', '.remove-row-button', function(){
            $(this).closest('.panel').remove();
        });

        // Add a task
        $('.positions').on('click', '.add-task', function(e){
            e.preventDefault();

            $stage = $(this).closest('.task-row').find('.task-col');
            var index = parseInt($stage.find('.task:last-child').attr('data-index')) + 1;
            var positionIndex = $(this).closest('.position').attr('data-index');
            if (isNaN(index))
                index = 0;

            // Populate them with data
            $template = $('.task-template .task').clone();
            $template.attr('data-index', index);
            $template.find('textarea').attr('name', 'tasks['+positionIndex+'][]');

            // Append the new user form to the DOM
            $stage.append($template);

            // Auto-size textarea boxes
            $('.autosize').autosize();
        });

        // Add a ksa
        $('.positions').on('click', '.add-ksa', function(e){
            e.preventDefault();

            $stage = $(this).closest('.ksa-row').find('.ksa-col');
            var index = parseInt($stage.find('.ksa:last-child').attr('data-index')) + 1;
            var positionIndex = $(this).closest('.position').attr('data-index');
            if (isNaN(index))
                index = 0;

            // Populate them with data
            $template = $('.ksa-template .ksa').clone();
            $template.attr('data-index', index);
            $template.find('input').attr('name', 'ksas['+positionIndex+']['+index+'][name]');
            $template.find('textarea').attr('name', 'ksas['+positionIndex+']['+index+'][description]');

            // Append the new user form to the DOM
            $stage.append($template);

            // Auto-size textarea boxes
            $('.autosize').autosize();
        });

        // Add a rating
        $('.positions').on('click', '.add-rating', function(e){
            e.preventDefault();

            $stage = $(this).closest('.rating-row').find('.rating-col');
            var index = parseInt($stage.find('.rating:last-child').attr('data-index')) + 1;
            var positionIndex = $(this).closest('.position').attr('data-index');
            if (isNaN(index))
                index = 0;

            // Populate them with data
            $template = $('.rating-template .rating').clone();
            $template.attr('data-index', index);
            $template.find('.type select').attr('name', 'ratings['+positionIndex+']['+index+'][type]');
            $template.find('.element select').attr('name', 'ratings['+positionIndex+']['+index+'][id]');
            $template.find('textarea').attr('name', 'ratings['+positionIndex+']['+index+'][description]');

            // Append the new user form to the DOM
            $stage.append($template);

            // Auto-size textarea boxes
            $('.autosize').autosize();
        });

        // Add a position
        $('.panel').on('click', '.add-position', function(e){
            e.preventDefault();

            $stage = $(this).closest('.panel').find('.positions');
            var index = parseInt($stage.find('.position:last-child').attr('data-index')) + 1;
            if (isNaN(index))
                index = 0;

            // Populate them with data
            $template = $('.position-template .position').clone();
            $template.attr('data-index', index);
            $template.find('.position-col input').attr('name', 'position['+index+']');
            $template.find('.task textarea').attr('name', 'tasks['+index+'][]');
            $template.find('.ksa input').attr('name', 'ksas['+index+'][0][name]');
            $template.find('.ksa textarea').attr('name', 'ksas['+index+'][0][description]');
            $template.find('.rating .type select').attr('name', 'ratings['+index+'][0][type]');
            $template.find('.rating .element select').attr('name', 'ratings['+index+'][0][id]');
            $template.find('.rating textarea').attr('name', 'ratings['+index+'][0][description]');

            // Append the new user form to the DOM
            $stage.append($template);

            // Auto-size textarea boxes
            $('.autosize').autosize();
        });

        // Remove task
        $('.positions').on('click', '.remove-task', function() {
            $position = $(this).closest('.position');
            $(this).closest('.task').remove();
            indexTasks($position);
        });

        // Remove ksa
        $('.positions').on('click', '.remove-ksa', function() {
            $position = $(this).closest('.position');
            $(this).closest('.ksa').remove();
            indexKsas($position);
        });

        // Remove rating
        $('.positions').on('click', '.remove-rating', function() {
            $position = $(this).closest('.position');
            $(this).closest('.rating').remove();
            indexRatings($position);
        });

        // Remove position
        $('.positions').on('click', '.remove-position', function() {
            $(this).closest('.position').remove();
            indexPositions();
        });

        // Re-index the tasks
        function indexTasks($position) {
            var index = 0;
            $('.task-col .task').each(function(){
                $(this).attr('data-index', index);
                index += 1;
            });
        }

        // Re-index the ksas
        function indexKsas($position) {
            var index = 0;
            $('.ksa-col .ksa').each(function(){
                $(this).attr('data-index', index);
                index += 1;
            });
        }

        // Re-index the ratings
        function indexRatings($position) {
            var index = 0;
            $('.rating-col .rating').each(function(){
                $(this).attr('data-index', index);
                index += 1;
            });
        }

        // Re-index the positions
        function indexPositions() {
            var index = 0;
            $('.positions .position').each(function(){
                $(this).attr('data-index', index);
                index += 1;
            });
        }

        // Store assessments and dimensions
        var assessments = {!! json_encode($assessmentsArray) !!};
        var dimensions = {!! json_encode($dimensionsArray) !!};

        // Change importance rating selection options
        $('.positions').on('change', '.rating select', function() {
            $col = $(this).closest('.select-col');

            if (! $col.hasClass('type'))
                return false;

            var val = $(this).val();
            $rating = $(this).closest('.rating');
            $('.element label', $rating).html(capFirst(val));

            $('.element select option', $rating).remove();
            if (val == 'assessment')
            {
                for (var i in assessments)
                    $('.element select', $rating).append('<option value="'+i+'">'+assessments[i]+'</option>');
            }
            else if (val == 'dimension')
            {
                for (var i in dimensions)
                    $('.element select', $rating).append('<option value="'+i+'">'+dimensions[i]+'</option>');
            }
        });
    });

    function capFirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>

<!-- Submit Button -->
<div class="form-group">

    <div style="clear:both;"></div>
    <br/>

    <div class="pull-right">
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>

@section('scripts')
    <script src="{{ asset('assets/js/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('js/autosize.js') }}"></script>
@stop