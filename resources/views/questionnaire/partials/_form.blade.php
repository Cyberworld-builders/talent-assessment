@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        .assignment h2 {
            text-transform: none;
            font-size: 40px;
        }
        .assignment h3 {
            text-transform: none;
            margin-top: 11px;
        }
        .assignment .questions .row {
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            padding-bottom: 16px;
        }
        .assignment .questions .row.no-border {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .assignment .questions .iswitch {
            width: 28px;
            height: 28px;
            margin-top: 0;
            position: relative;
            top: 3px;
            border: 2px solid #e4e4e4;
            box-shadow: none;
            outline: none !important;
            border-radius: 0 !important;
        }
        .assignment .questions .iswitch.iswitch-small {
            width: 20px;
            height: 20px;
        }
        .assignment .questions .tab-pane {
            background: #fff;
            border: 1px solid #eee;
        }
        .assignment .questions .table {
            margin-bottom: 0;
            border-bottom: 0;
        }
        .assignment .handle {
            left: 0%;
            position: absolute;
            color: #03244A;
            font-family: 'Roboto Condensed', 'Arial Narrow', Arial, sans-serif;
            font-size: 20px;
            padding-top: 32px;
            width: 15px;
            z-index: 10;
            cursor: grab;
            text-align: center;
        }
        .assignment .handle:before {
            content: '';
            position: absolute;
            width: 15px;
            height: 30px;
            background: #E77928;
            border-radius: 3px;
            border: 1px solid #ddd;
            z-index: 1;
            top: -11px;
            left: 0px;
        }
        .assignment .rating-container {
            position: relative;
        }
        .assignment .rating {
            height: 8px;
            background: #efefef;
            border-radius: 2px;
        }
    </style>
@stop

@if (!$page or $page == 1)

    <h2>Position Information</h2>
    <div class="description">
        <strong>Instructions: </strong>Please type or select your responses for the following information about your position in this organization. Fields marked with <span class="text-danger">*</span> are required.
    </div>

    <div class="questions">
        <div class="row">
            <div class="col-sm-3">
                <h3>Your Name: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::text('name', $jaq->name, ['class' => 'form-control input-lg', 'data-validate' => 'required']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Position Title on Org Chart: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('position', $analysis->position, $jaq->position, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Current Job Code:</h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('job_code', $analysis->job_code, $jaq->job_code, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Department Name:</h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('department_name', $analysis->department_name, $jaq->department_name, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Supervisor's Name:</h3>
            </div>
            <div class="col-sm-9">
                {!! Form::text('supervisor_name', $jaq->supervisor_name, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Supervisor's Title:</h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('supervisor_title', $analysis->supervisor_title, $jaq->supervisor_title, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Location:</h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('location', $analysis->location, $jaq->location, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Description of Position: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::textarea('position_desc', $jaq->position_desc, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="pull-right">
        <a href="?page=2">
            {!! Form::button('Continue', ['class' => 'btn btn-black btn-lg']) !!}
        </a>
    </div>
    <div style="clear:both;"></div>
@endif

@if ($page == 2)
    <h2>Duties and Responsibilities</h2>
    <div class="description">
        <strong>Instructions: </strong>The following is a list of MAJOR job duties and responsibilities of your position. You may edit the responses to make them more accurate. If there are additional duties/responsibilities not listed, please type them in the open spaces below. Please check the box to the right if the duty is relevant to your position (including any you may have typed).
    </div>
    <div class="questions">
        <div class="tab-content">
            <div class="tab-pane active">
                <form>
                <table class="table table-hover members-table middle-align">
                    <thead>
                    <tr>
                        <th>Task</th>
                        <th>Relevant to position?</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{--@foreach ($analysis->tasks[$jaq->position] as $i => $task)--}}
                            {{--@include('questionnaire.partials._task', [--}}
                                {{--'i' => $i,--}}
                                {{--'task' => $task,--}}
                            {{--])--}}
                        {{--@endforeach--}}
                        @foreach ($jaq->tasks as $i => $task)
                            @include('questionnaire.partials._task', [
                                'i' => $i,
                                'task' => $task,
                            ])
                        @endforeach
                        @for ($j = $i + 1; $j < 8; $j++)
                            @include('questionnaire.partials._task', [
                                'i' => $j,
                                'task' => null,
                            ])
                        @endfor
                    </tbody>
                </table>
                </form>
            </div>
        </div>
        <div class="row">
            <br>
            <div class="col-sm-12">
                <button class="btn btn-small add-task"><i class="fa-plus"></i> Add Task</button>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="pull-right">
        <a href="?page=3">
            {!! Form::button('Continue', ['class' => 'btn btn-black btn-lg']) !!}
        </a>
    </div>
    <div style="clear:both;"></div>
@endif

@if ($page == 3)
    <h2>Minimum Qualifications</h2>
    <div class="description">
        <strong>Instructions: </strong>Please type or select your responses for the following information about your position in this organization.
    </div>
    <div class="questions">
        <div class="row">
            <div class="col-sm-3">
                <h3>Minimum Level of Education Required: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('min_education', [
                    0 => 'No Degree',
                    1 => 'High School Degree',
                    2 => 'Vocational/Technical Degree',
                    3 => 'Associates Degree',
                    4 => 'Bachelors Degree',
                    5 => 'Masters Degree',
                    6 => 'Doctorate Degree (e.g. PhD, JD, MD)',
                    7 => 'License or Certification (e.g. CPA, PHR, etc.)',
                ], $jaq->min_education, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Preferred Level of Education: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('preferred_education', [
                    0 => 'No Degree',
                    1 => 'High School Degree',
                    2 => 'Vocational/Technical Degree',
                    3 => 'Associates Degree',
                    4 => 'Bachelors Degree',
                    5 => 'Masters Degree',
                    6 => 'Doctorate Degree (e.g. PhD, JD, MD)',
                    7 => 'License or Certification (e.g. CPA, PHR, etc.)',
                ], $jaq->preferred_education, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Minimum Level of Experience Required: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('min_experience', [
                    0 => 'No Experience',
                    1 => 'Up to 1 Year Experience',
                    2 => '2 - 4 Years Experience',
                    3 => '5 - 7 Years Experience',
                    4 => '8 - 10 Years Experience',
                    5 => 'Greater than 10 Years Experience',
                ], $jaq->min_experience, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Preferred Level of Experience: <span class="text-danger">*</span></h3>
            </div>
            <div class="col-sm-9">
                {!! Form::select('preferred_experience', [
                    0 => 'No Experience',
                    1 => 'Up to 1 Year Experience',
                    2 => '2 - 4 Years Experience',
                    3 => '5 - 7 Years Experience',
                    4 => '8 - 10 Years Experience',
                    5 => 'Greater than 10 Years Experience',
                ], $jaq->preferred_experience, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <h3>Additional Requirements: <span class="text-danger">*</span></h3>
                <p>Please list any additional requirements (e.g., forklift operator license).</p>
            </div>
            <div class="col-sm-9">
                {!! Form::textarea('additional_requirements', $jaq->additional_requirements, ['class' => 'form-control input-lg']) !!}
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="pull-right">
        <a href="?page=4">
            {!! Form::button('Continue', ['class' => 'btn btn-black btn-lg']) !!}
        </a>
    </div>
    <div style="clear:both;"></div>
@endif

@if ($page == 4)
    <h2>Knowledge, Skills, and Abilities (KSAs)</h2>
    <div class="description">
        <strong>Instructions: </strong>The following is a list of knowledge, skills, and abilities (KSAs) related to THIS position. These KSAs should be characteristics that differentiate exceptional from poor performance or “Key Attributes” that people possess that lead to successful performance. You may edit the responses to make them more accurate. If there are additional KSAs not listed, please type them in the open spaces below. Please check the box to the right if the KSA is relevant to your position (including any you may have typed).
    </div>
    <div class="questions">
        <div class="tab-content">
            <div class="tab-pane active">
                <form>
                <table class="table table-hover members-table middle-align">
                    <thead>
                    <tr>
                        <th>Knowledge / Skill / Ability</th>
                        <th>Relevant to position?</th>
                    </tr>
                    </thead>
                    <tbody>
                        {{--@foreach ($analysis->ksas[$jaq->position] as $i => $ksa)--}}
                            {{--@include('questionnaire.partials._ksa', [--}}
                                {{--'i' => $i,--}}
                                {{--'ksa' => $ksa,--}}
                            {{--])--}}
                        {{--@endforeach--}}
                        @foreach ($jaq->ksas as $i => $ksa)
                            @include('questionnaire.partials._ksa', [
                                'i' => $i,
                                'ksa' => $ksa,
                            ])
                        @endforeach
                        @for ($j = $i + 1; $j < 8; $j++)
                            @include('questionnaire.partials._ksa', [
                                'i' => $j,
                                'ksa' => null,
                            ])
                        @endfor
                    </tbody>
                </table>
                </form>
            </div>
        </div>
        <div class="row">
            <br>
            <div class="col-sm-12">
                <button class="btn btn-small add-ksa"><i class="fa-plus"></i> Add KSA</button>
            </div>
        </div>
    </div>
    <br><br><br>
    <div class="pull-right">
        <a href="?page=5">
            {!! Form::button('Continue', ['class' => 'btn btn-black btn-lg']) !!}
        </a>
    </div>
    <div style="clear:both;"></div>
@endif

@if ($page == 5)
    <h2>Task – KSA Linkages</h2>
    <div class="description">
        <strong>Instructions: </strong>The following is a list of major tasks that you identified as relevant for your position. To the right is a list of KSAs you have identified as relevant for your position. For each task, please check the box for each relevant KSA. You may select multiple KSAs for each task.
    </div>
    <div class="questions">
        <form>
        <?php $count = 0; ?>
        @foreach ($jaq->tasks as $i => $task)
            <?php if (! $task['relevant']) continue ?>
            <?php $count++ ?>
            <div class="row">
                <div class="col-sm-3">
                    <h3>Task #{{ $count }}</h3>
                    <p>{{ $task['task'] }}</p>
                </div>
                <div class="col-sm-9">
                    @foreach ($jaq->ksas as $j => $ksa)
                        <?php if (! $ksa['relevant']) continue ?>
                        <div class="well">
                            <div class="row no-border">
                                <div class="col-sm-1" style="margin-top: 10px;">

                                    @if ($jaq->ksa_linkages and array_key_exists($i, $jaq->ksa_linkages) and array_key_exists($j, $jaq->ksa_linkages[$i]))
                                        {!! Form::checkbox('ksa_linkages['.$i.']['.$j.']', true, $jaq->ksa_linkages[$i][$j], ['class' => 'iswitch iswitch-secondary array-input']) !!}
                                    @else
                                        {!! Form::checkbox('ksa_linkages['.$i.']['.$j.']', true, null, ['class' => 'iswitch iswitch-secondary array-input']) !!}
                                    @endif
                                </div>
                                <div class="col-sm-11">
                                    <h3>{{ $ksa['name'] }}</h3>
                                    <p>{{ $ksa['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </form>
    </div>
    <br><br><br>
    <div class="pull-right">
        <a href="?page=6">
            {!! Form::button('Continue', ['class' => 'btn btn-black btn-lg']) !!}
        </a>
    </div>
    <div style="clear:both;"></div>
@endif

@if ($page == 6)
    <h2>Importance Ratings</h2>
    <div class="description">
        <strong>Instructions: </strong>The following is a list of factors related to this position. For each one, on a scale of 1 to 5, rate how important you feel the factor is for achieving overall success and proficiency for this particular position.
    </div>
    <div class="questions">
        @if ($jaq->ratings)
            @foreach ($analysis->ratings[$jaq->position] as $i => $rating)
                <div class="row">
                    <div class="col-sm-3">
                        <h3>Factor #{{ $i + 1 }}</h3>
                        <p>{{ $rating['description'] }}</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="rating-container">
                            <div class="rating">
                                @if (array_key_exists($i, $jaq->ratings))
                                    {!! Form::hidden('ratings['.$i.']', $jaq->ratings[$i], ['class' => 'form-control input-lg']) !!}
                                @else
                                    {!! Form::hidden('ratings['.$i.']', 3, ['class' => 'form-control input-lg']) !!}
                                @endif
                                <div class="handle ui-slider-handle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @foreach ($analysis->ratings[0] as $i => $rating)
                <div class="row">
                    <div class="col-sm-3">
                        <h3>Factor #{{ $i + 1 }}</h3>
                        <p>{{ $rating['description'] }}</p>
                    </div>
                    <div class="col-sm-9">
                        <div class="rating-container">
                            <div class="rating">
                                {!! Form::hidden('ratings['.$i.']', 3, ['class' => 'form-control input-lg']) !!}
                                <div class="handle ui-slider-handle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="pull-right">
        {!! Form::button('Submit Responses', ['class' => 'btn btn-black btn-lg', 'id' => 'complete']) !!}
    </div>
    <div style="clear:both;"></div>
@endif

{{-- Templates --}}
<div class="templates" style="display:none;">
    <div class="task-template">
        @include('questionnaire.partials._task', [
            'i' => 0,
            'task' => null,
        ])
    </div>
    <div class="ksa-template">
        @include('questionnaire.partials._ksa', [
            'i' => 0,
            'ksa' => null,
        ])
    </div>
</div>

<script>
    (function($){
        $(document).ready(function(){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            // Check for answers
            $('.questions').on('change', 'input, select, textarea', function(){
                var name = $(this).attr('name');
                var val = $(this).val();
                if ($(this).attr('type') == 'checkbox')
                    val = $(this)[0].checked;
                var is_array = $(this).hasClass('array-input');

                if (is_array)
                    val = $('form').serializeControls();

//                console.log(name);
//                console.log(val);

                // Save in the database
                var data = {
                    'name': name,
                    'value': val,
                    'complete': 0
                };
                var url = window.location.pathname;

                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                    },
                    error: function(data) {
                        console.log('An error has occurred.');
                        $('html').prepend(data.responseText);
                    }
                });
            });

            // Complete the questionnaire
            $('#complete').on('click', function(e){
                e.preventDefault();

                var data = {
                    'complete': 1
                };
                var url = window.location.pathname;

                $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        if (data['redirect'])
                            window.location = url;
                    },
                    error: function(data) {
                        console.log('An error has occurred.');
                        $('html').prepend(data.responseText);
                    }
                });
            });

            // Auto-size textarea boxes
            $('.autosize').autosize();

            // Add a task
            $('.questions').on('click', '.add-task', function(e){
                e.preventDefault();

                $stage = $(this).closest('.questions').find('tbody');
                var index = parseInt($stage.find('tr:last-child').attr('data-index')) + 1;
                if (isNaN(index))
                    index = 0;

                // Populate them with data
                $template = $('<tr/>');
                $template.attr('data-index', index);

                // Setup textarea
                $textarea = $('.task-template textarea').clone();
                $textarea.attr('name', 'tasks['+index+'][task]');
                $textareaTd = $('<td/>').append($textarea);

                // Setup checkbox
                $checkbox = $('.task-template input[type="checkbox"]').clone();
                $checkbox.attr('name', 'tasks['+index+'][relevant]');
                $checkboxTd = $('<td/>').append($checkbox);

                // Put everything together
                $template.append($textareaTd).append($checkboxTd);

                // Append the new user form to the DOM
                $stage.append($template);

                // Auto-size textarea boxes
                $('.autosize').autosize();
            });

            // Add a KSA
            $('.questions').on('click', '.add-ksa', function(e){
                e.preventDefault();

                $stage = $(this).closest('.questions').find('tbody');
                var index = parseInt($stage.find('tr:last-child').attr('data-index')) + 1;
                if (isNaN(index))
                    index = 0;

                // Populate them with data
                $template = $('<tr/>');
                $template.attr('data-index', index);

                // Setup text inputs
                $inputs = $('.ksa-template .row').clone();
                $inputs.find('input').attr('name', 'ksas['+index+'][name]');
                $inputs.find('textarea').attr('name', 'ksas['+index+'][description]');
                $inputsTd = $('<td/>').append($inputs);

                // Setup checkbox
                $checkbox = $('.ksa-template input[type="checkbox"]').clone();
                $checkbox.attr('name', 'ksas['+index+'][relevant]');
                $checkboxTd = $('<td/>').append($checkbox);

                // Put everything together
                $template.append($inputsTd).append($checkboxTd);

                // Append the new user form to the DOM
                $stage.append($template);

                // Auto-size textarea boxes
                $('.autosize').autosize();
            });

            // Slider
            $('.rating').each(function(){
                var handle = $(this).find('.handle');
                var input = $(this).find('input');
                $(this).slider({
                    value: input.val(),
                    min: 1,
                    max: 5,
                    step: 0.01,
                    create: function() {
                        handle.text(input.val());
                    },
                    slide: function(event, ui) {
                        handle.text(ui.value);
                    },
                    stop: function(event, ui) {
                        input.val(ui.value).trigger('change');
                    }
                });
            });
        });

        $.fn.serializeControls = function() {
            var data = {};

            function buildInputObject(arr, val) {
                if (arr.length < 1)
                    return val;
                var objkey = arr[0];
                if (objkey.slice(-1) == "]") {
                    objkey = objkey.slice(0,-1);
                }
                var result = {};
                if (arr.length == 1) {
                    result[objkey] = val;
                } else {
                    arr.shift();
                    var nestedVal = buildInputObject(arr,val);
                    result[objkey] = nestedVal;
                }
                return result;
            }

            $.each(this.serializeArray(), function() {
                var val = this.value;
                var c = this.name.split("[");
                var a = buildInputObject(c, val);
                $.extend(true, data, a);
            });

            return data;
        }
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
    <script src="{{ asset('js/autosize.js') }}"></script>
@stop