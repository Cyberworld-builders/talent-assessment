@if (! $task)
    <div class="description">
        @if (! $preview)
            @if ($assessment->translation() && $assessment->translation()->description)
                {!! custom_fields($assignment->id, $assessment->translation()->description) !!}
            @else
                {!! custom_fields($assignment->id, $assessment->description) !!}
            @endif
        @else
            {!! $assessment->description !!}
        @endif
    </div>

    <h1>{{ translate('Questions') }}</h1>

    @if (! empty($questions))
        @foreach ($questions as $question)

            <div class="question-container">
                <p>
                    {{-- Page Number --}}
                    @if ($question->showPageNumber())
                        <strong>{{ $question->number }}.</strong>&nbsp;
                    @endif

                    {{-- Question Content --}}
                    @if ($question->showContent())
                        @if (! $preview)
                            @if ($assessment->translation() && $question->translated($assessment->translation()->id)->content)
                                {!! custom_fields($assignment->id, $question->translated($assessment->translation()->id)->content) !!}
                            @else
                                {!! custom_fields($assignment->id, $question->content) !!}
                            @endif
                        @else
                            {!! $question->content !!}
                        @endif
                    @endif
                </p>
                <br/>

                {{-- Multiple Choice --}}
                @if ($question->type == 1)
                    @if (! empty($question->anchors))
                        <div class="btn-group btn-group-justified" data-toggle="buttons">
							<?php
							if (! empty($id))
								$answer = $question->answerFromAssignment($id);
							?>

                            @foreach ($question->anchors as $i => $anchor)
								<?php
								$checked = false;

								if (!empty($id) && $answer) {
									if ($answer->value == $i) $checked = true;
								}
								?>
                                <label class="btn btn-gray">
                                    <input class="question" type="radio" name="question[{{ $question->number }}]" id="{{ $question->id }}" value="{{ $i }}" <?php echo ($checked) ? 'checked="checked"' : '' ?> />
                                    @if ($assessment->translation() && $question->translated($assessment->translation()->id)->anchors[$i])
                                        {!! $question->translated($assessment->translation()->id)->anchors[$i] !!}
                                    @else
                                        {{ $anchor['tag'] }}
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @endif
                @endif

                {{-- Text Input --}}
                @if ($question->type == 3)
					<?php
					$answer = '';
					if (! empty($id))
					{
						$answer = $question->answerFromAssignment($id);
						if ($answer)
							$answer = $answer->value;
					}
					?>
                    <textarea class="question form-control input-lg" rows="7" name="question[{{ $question->number }}]" id="{{ $question->id }}">{{ $answer }}</textarea>
                @endif

                {{-- Slider --}}
                @if ($question->type == 11)
					<?php
					$answer = '';
					if (! empty($id))
					{
						$answer = $question->answerFromAssignment($id);
						if ($answer)
							$answer = $answer->value;
					}
					?>
                    <div class="row">
                        <div class="col-sm-2">
                            <p class="rating-description">Not Important</p>
                        </div>
                        <div class="col-sm-8">
                            <div class="rating-container">
                                <div class="rating">
                                    {!! Form::hidden('question['.$question->number.']', ($answer ? $answer : 3), ['id' => $question->id, 'class' => 'question']) !!}
                                    <div class="handle ui-slider-handle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <p class="rating-description">Critically Important</p>
                        </div>
                    </div>
                @endif
            </div>

        @endforeach
    @endif
@endif

<script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    jQuery(document).ready(function($){

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
</script>

{{-- WM Questions --}}
@if ($task)
    <div id="comp" class="container"></div>
    <script src="{{ asset('js/react.js') }}"></script>
    <script src="{{ asset('js/JSXTransformer.js') }}"></script>
    <script src="{{ asset('js/marked.min.js') }}"></script>
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            showProcessingMessages: false,
            tex2jax: {inlineMath: [['`','`']]}
        });
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/mathjax/MathJax.js?config=AM_HTMLorMML') }}"></script>
    <script type="text/javascript">
        var saveUrl = '{{ url('assignment/wm/save') }}';

        // Set headers for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });

        // Setup translations
        var translatedTerms = null;
		<?php
			$terms = null;
			$language = null;
			$user = \Auth::user();
            if ($user)
			    $language = $user->language();
			if ($user && $language && $language->code != 'en')
			{
				$languageHelper = new \App\Language;
				$terms = $languageHelper->getTerms($language->code);
			}
			//$languageHelper = new \App\Language;
			//$terms = $languageHelper->getTerms('es');
        ?>
        @if ($terms)
            translatedTerms = {!! json_encode($terms) !!};
        @endif
        //console.log(translatedTerms);

        // Translation function
        function translate(string)
        {
            if (!translatedTerms)
                return string;

            if (!translatedTerms[string])
                return string;

            return translatedTerms[string];
        }

        //console.log(translate('Please make sure that this information is accurate.'));
    </script>
    <script type="text/jsx" src="{{ asset('js/wm.js') }}"></script>
    {{--<script type="text/jsx" src="http://dev.aoescience.com/js/wm.js"></script>--}}
    <script type="text/jsx">
        var task = {!! json_encode($task) !!};
        React.render(React.createElement(Runner, {
            taskId: 1,
            task: task,
            assignmentId: {!! ($preview || !$user) ? 0 : $assignment->id !!},
            preview: {!! ($preview || !$user) ? 'true' : 'false' !!},
        }), document.getElementById('comp'));
    </script>
@endif

@if (! $task)
    <!-- Pagination -->
    @if ($assessment->paginate)
        @include('assignment.partials._pagination', ['paginator' => $questions])
    @endif
@endif

<!-- Submit Field -->
@if (! $assessment->paginate || ! $questions->hasMorePages())
    <div class="form-group" {!! ($task) ? 'style="display:none;"' : '' !!}>
        <br/>
        <div class="pull-right">
            @if (isset($assignment) && $assignment->id == 123456789)
                {!! Form::open(['url' => 'assessment/sample/'.$assignment->short_name.'/complete']) !!}
                <input type="submit" class="btn btn-black btn-lg" value="{{ translate('Submit Answers') }}"/>
                {!! Form::close() !!}
                {{--<a href="/assessment/sample/{{ $assignment->short_name }}/complete" class="btn btn-black btn-lg" style="padding: 20px 30px;">{{ translate('Submit Answers') }}</a>--}}
            @else
                {!! Form::button(translate('Submit Answers'), ['class' => 'btn btn-black btn-lg', 'id' => 'complete']) !!}
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
@endif

@if (! $task)
    <!-- Countdown Timer -->
    @if ($assessment->timed)
        <div id="countdown">
            <div>
                <span class="time">{{ $assessment->time_limit }}:00</span>
            </div>
        </div>
    @endif
@endif