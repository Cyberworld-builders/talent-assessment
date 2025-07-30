{{-- Multiple Choice Answer --}}
@if ($question->type == 1)
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            @if ($assignment->target)
                <p>{!! do_shortcodes(['name' => $assignment->target->name], $question->content) !!}</p>
            @else
                <p>{!! $question->content !!}</p>
            @endif
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->questionText() }}</p>
            @else
                <p class="text-danger">Not answered</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->questionScore() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Description --}}
@if ($question->type == 2)
    <div class="row border-bottom">

        {{-- Question --}}
        <div class="col-xs-12">
            <p>{!! $question->content !!}</p>
        </div>
    </div>
@endif

{{-- Text Input --}}
@if ($question->type == 3)
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p>{!! $question->content !!}</p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-5">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->value }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Letter Sequence --}}
@if ($question->type == 4)
    <?php
        // Get and sanitize
	    $options = [];
	    foreach (unserialize($question->answerFromAssignment($assignment->id)->value)['options'] as $i => $option)
        {
			$option = preg_replace('/(\v|\s)+/', ' ', $option);
			$option = str_replace(' ', '', $option);
            $options[$i] = $option;
        }
	    $answers = [];
        foreach (unserialize($question->answerFromAssignment($assignment->id)->value)['response'] as $i => $response)
        {
            $response = preg_replace('/(\v|\s)+/', ' ', $response);
            $response = str_replace(' ', '', $response);
            $answers[$i] = $response;
        }
	    $response = implode(', ', $answers);
        $question->content = preg_replace('/(\v|\s)+/', ' ', $question->content);
        $question->content = str_replace(' ', '', $question->content);
        $correct = explode(',', $question->content);
    ?>
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p>
                @foreach ($options as $letter)
                    @if (in_array($letter, $correct))
                        <?php $key = array_search($letter, $correct) + 1; ?>
                        <strong>{{ trim($letter) }}</strong> <sup>{{ $key }}</sup>
                    @else
                        {!! trim($letter) !!}
                    @endif
                @endforeach
            </p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $response }}</p>
            @else
                <p class="text-danger">Not answered</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Math Equation --}}
@if ($question->type == 5)
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p>{!! $question->content !!}</p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ int_to_boolean_string($question->answerFromAssignment($assignment->id)->value) }}</p>
            @else
                <p class="text-danger">Not answered</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Math and Letters --}}
@if ($question->type == 6)
    <?php
        $question_content = json_decode($question->content);
	    $letter_options = unserialize($question->answerFromAssignment($assignment->id)->value)['letters']['options'];
	    $correct_letters = $question_content->letters;
    ?>
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            {{-- Math --}}
            <p>
                @foreach ($question_content->equations as $equation)
                    {!! $equation !!} <br/>
                @endforeach
            </p>
            {{-- Letters --}}
            <p>
                @foreach ($letter_options as $letter)
                    @if (in_array($letter, $correct_letters))
						<?php $key = array_search($letter, $correct_letters) + 1; ?>
                        <strong>{{ trim($letter) }}</strong> <sup>{{ $key }}</sup>
                    @else
                        {!! trim($letter) !!}
                    @endif
                @endforeach
            </p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($question->answer_exists($assignment->id, $user->id))
                {{-- Math --}}
                <p>
                    @foreach (unserialize($question->answerFromAssignment($assignment->id)->value)['equations'] as $equation)
                        {{ int_to_boolean_string($equation['response']) }} <br/>
                    @endforeach
                </p>
                {{-- Letters --}}
                <p>
                    {{ implode(', ', unserialize($question->answerFromAssignment($assignment->id)->value)['letters']['response']) }}
                </p>
            @else
                <p class="text-danger">Not answered</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Square Sequence --}}
@if ($question->type == 7)
    <?php
        $question_content = json_decode($question->content);
        $answer = unserialize($question->answerFromAssignment($assignment->id)->value);
    ?>
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p>
                @for ($i = 0; $i < 4; $i++)
                    @for ($j = 0; $j < 4; $j++)
                        <?php $num = 0; ?>
                        @foreach ($question_content as $k => $coordinates)
                            <?php
                                if ($coordinates[0] == $j && $coordinates[1] == $i)
                                {
                                    $num = $k + 1;
                                    break;
                                }
                            ?>
                        @endforeach
                        @if ($num)
                            <strong>{{ $num }}</strong>
                        @else
                            0
                        @endif
                        &nbsp;&nbsp;&nbsp;
                    @endfor
                    <br/>
                @endfor
            </p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            <p>
                @for ($i = 0; $i < 4; $i++)
                    @for ($j = 0; $j < 4; $j++)
						<?php $num = 0; ?>
                        @foreach ($answer as $k => $coordinates)
							<?php
							if ($coordinates[0] == $j && $coordinates[1] == $i)
							{
								$num = $k + 1;
								break;
							}
							?>
                        @endforeach
                        @if ($num)
                            <strong>{{ $num }}</strong>
                        @else
                            0
                        @endif
                        &nbsp;&nbsp;&nbsp;
                    @endfor
                    <br/>
                @endfor
            </p>
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Symmetry --}}
@if ($question->type == 8)
	<?php
	    $question_content = json_decode($question->content);
	    $answerFromAssignment = $question->answerFromAssignment($assignment->id);
	    if ($answerFromAssignment)
	        $answer = $answerFromAssignment->value;
	    else
	    	$answer = null;
	?>
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p style="line-height:0px;">
                @for ($i = 0; $i < 8; $i++)
                    @for ($j = 0; $j < 8; $j++)
						<?php $num = 0; ?>
                        @foreach ($question_content as $k => $coordinates)
							<?php
							if ($coordinates[0] == $j && $coordinates[1] == $i)
							{
								$num = $k + 1;
								break;
							}
							?>
                        @endforeach
                        @if ($num)
                            <i class="fa-square" style="display:inline-block;width:13px;"></i>
                        @else
                            <i class="fa-square-o" style="display:inline-block;width:13px;"></i>
                        @endif
                    @endfor
                    <br/>
                @endfor
            </p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($answer)
                <p>{{ int_to_boolean_string($answer) }}</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Square Symmetry --}}
@if ($question->type == 9)
    <?php
        $question_content = json_decode($question->content);
        $answerFromAssignment = $question->answerFromAssignment($assignment->id);
        if ($answerFromAssignment)
            $answer = unserialize($answerFromAssignment->value);
        else
            $answer = null;
    ?>
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            {{-- Square --}}
            <p>
                @for ($i = 0; $i < 4; $i++)
                    @for ($j = 0; $j < 4; $j++)
						<?php $num = 0; ?>
                        @foreach ($question_content->squares as $k => $coordinates)
							<?php
							if ($coordinates[0] == $j && $coordinates[1] == $i)
							{
								$num = $k + 1;
								break;
							}
							?>
                        @endforeach
                        @if ($num)
                            <strong>{{ $num }}</strong>
                        @else
                            0
                        @endif
                        &nbsp;&nbsp;&nbsp;
                    @endfor
                    <br/>
                @endfor
            </p>
            {{-- Symmetry --}}
            @foreach ($question_content->symmetries as $symmetry)
                <p style="line-height:0px;">
                    @for ($i = 0; $i < 8; $i++)
                        @for ($j = 0; $j < 8; $j++)
                            <?php $num = 0; ?>
                            @foreach ($symmetry as $k => $coordinates)
                                <?php
                                if ($coordinates[0] == $j && $coordinates[1] == $i)
                                {
                                    $num = $k + 1;
                                    break;
                                }
                                ?>
                            @endforeach
                            @if ($num)
                                <i class="fa-square" style="display:inline-block;width:13px;"></i>
                            @else
                                <i class="fa-square-o" style="display:inline-block;width:13px;"></i>
                            @endif
                        @endfor
                        <br/>
                    @endfor
                </p>
            @endforeach
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            {{-- Square --}}
            <p>
                @if ($answer)
                    @for ($i = 0; $i < 4; $i++)
                        @for ($j = 0; $j < 4; $j++)
                            <?php $num = 0; ?>
                            @foreach ($answer['squares']['response'] as $k => $coordinates)
                                <?php
                                if ($coordinates[0] == $j && $coordinates[1] == $i)
                                {
                                    $num = $k + 1;
                                    break;
                                }
                                ?>
                            @endforeach
                            @if ($num)
                                <strong>{{ $num }}</strong>
                            @else
                                0
                            @endif
                            &nbsp;&nbsp;&nbsp;
                        @endfor
                        <br/>
                    @endfor
                @endif
            </p>
            {{-- Symmetry --}}
            @if ($answer)
                @foreach ($answer['symmetries'] as $symmetry)
                    <p style="height:130px;">{{ int_to_boolean_string($symmetry['response']) }}</p>
                @endforeach
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->scoreWm() }}</p>
            @endif
        </div>
    </div>
@endif

{{-- Slider Answer --}}
@if ($question->type == 11)
    <div class="row border-bottom">

        {{-- Number --}}
        <div class="col-xs-1">
            <p><strong>{{ $question->number }}</strong></p>
        </div>

        {{-- Question --}}
        <div class="col-xs-6">
            <p>{!! $question->content !!}</p>
        </div>

        {{-- Answer --}}
        <div class="col-xs-4">
            @if ($question->answer_exists($assignment->id, $user->id))
                <p>{{ $question->answerFromAssignment($assignment->id)->value }}</p>
            @else
                <p class="text-danger">Not answered</p>
            @endif
        </div>

        {{-- Score --}}
        <div class="col-xs-1">
            <p>N/A</p>
        </div>
    </div>
@endif