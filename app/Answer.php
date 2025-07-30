<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
	protected $fillable = [
		'assignment_id',
		'question_id',
		'user_id',
		'value',
		'time'
	];

	/**
	 * Get the question for which this is an answer to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function question()
	{
		return $this->belongsTo('App\Question');
	}

	/**
	 * Get the anchor to which this answer value is linked to.
	 * @return mixed
	 */
	public function questionText()
	{
		if ($this->question->type == 1)
		{
			$anchors = $this->question->anchors;
			return $anchors[$this->value]['tag'];
		}

		return false;
	}

	/**
	 * Get the anchor to which this answer value is linked to.
	 * @return mixed
	 */
	public function questionScore()
	{
		// Multiple choice
		if ($this->question->type == 1)
		{
			$anchors = $this->question->anchors;
			return $anchors[$this->value]['value'];
		}

		// WM question type
		if ($this->question->isWMType())
			return $this->scoreWm();

		return $this->value;
	}

	/**
	 * Get the anchor to which this answer value is linked to.
	 * @return mixed
	 */
	public function score()
	{
		$anchors = $this->question->anchors;

		if (array_key_exists($this->value, $anchors))
			return $anchors[$this->value]['value'];

		return false;
	}

	public function possibleWmScore()
	{
		$score = 0;

		// Letters
		if ($this->question->type == 4)
			$score++;

		// Math
		if ($this->question->type == 5)
			$score++;

		// Math and letters
		if ($this->question->type == 6)
		{
			$question = json_decode($this->question->content);
			foreach ($question->equations as $equation)
				$score++;
		}

		// Squares
		if ($this->question->type == 7)
		{
			$question = json_decode($this->question->content);
			foreach ($question as $square)
				$score++;
		}

		// Symmetry
		if ($this->question->type == 8)
			$score++;

		// Symmetry Squares
		if ($this->question->type == 9)
		{
			// Setup
			$question = json_decode($this->question->content);

			foreach ($question->squares as $square)
				$score++;
		}

		return $score;
	}

	public function scoreWm()
	{
		// Setup
		$score = 0;

		// Letters
		if ($this->question->type == 4)
		{
			// Setup and sanitize input
			$question = preg_replace('/(\v|\s)+/', ' ', $this->question->content);
			$question = str_replace(' ', '', $question);
			$answer = unserialize($this->value);
			foreach ($answer['response'] as $i => $response)
			{
				$response = preg_replace('/(\v|\s)+/', ' ', $response);
				$response = str_replace(' ', '', $response);
				$answer['response'][$i] = $response;
			}

			// Check answer
			if ($answer['response'] == explode(',', $question))
				$score++;
		}

		// Math
		if ($this->question->type == 5)
		{
			// Setup and sanitize input
			$question = preg_replace('/(\v|\s)+/', ' ', $this->question->content);
			$question = str_replace(' ', '', $question);
			$answer = $this->value;

			$c = new Calculate();
			$eq = explode('=', $question);
			$val = $c->calculate($eq[0]);
			$ans = ($val == $eq[1]);

			if ($answer == $ans)
				$score++;
		}

		// Math and letters
		if ($this->question->type == 6)
		{
			// Setup
			$question = json_decode($this->question->content);
			$answer = unserialize($this->value);

			// Score the equations and letters
			$c = new Calculate();
			foreach ($question->equations as $i => $equation)
			{
				// Setup
				$letters_correct = false;
				$equation_correct = false;

				// Solve the equation
				$eq = explode('=', $equation);
				$val = $c->calculate($eq[0]);
				$ans = ($val == $eq[1]);

				if ($answer['equations'][$i]['response'] == $ans)
					$equation_correct = true;

				if (array_key_exists($i, $answer['letters']['response']) && $answer['letters']['response'][$i] == $question->letters[$i])
					$letters_correct = true;

				if ($equation_correct && $letters_correct)
					$score++;
			}
		}

		// Squares
		if ($this->question->type == 7)
		{
			// Setup
			$question = json_decode($this->question->content);
			$answer = unserialize($this->value);

			foreach ($question as $i => $square)
			{
				if ($square == $answer[$i])
					$score++;
			}
		}

		// Symmetry
		if ($this->question->type == 8)
		{
			// Setup
			$question = json_decode($this->question->content);
			$answer = $this->value;
			$val = $this->solveSymmetry($question);

			if ($answer == $val)
				$score++;
		}

		// Symmetry Squares
		if ($this->question->type == 9)
		{
			// Setup
			$question = json_decode($this->question->content);
			$answer = unserialize($this->value);

			foreach ($question->squares as $i => $square)
			{
				// Setup
				$square_correct = false;
				$symmetry_correct = false;

				// Solve the symmetry
				$val = $this->solveSymmetry($question->symmetries[$i]);

				if (array_key_exists($i, $answer['squares']['response']) && $answer['squares']['response'][$i] == $square)
					$square_correct = true;

				if ($answer['symmetries'][$i]['response'] == $val)
					$symmetry_correct = true;

				if ($square_correct && $symmetry_correct)
					$score++;
			}
		}

		return $score;
	}

	public function solveSymmetry($symmetry)
	{
		if (! is_array($symmetry))
			return false;

		for ($i = 0; $i < count($symmetry); $i++)
            if (! $this->arrayHasPoint($symmetry, $this->getMirror($symmetry[$i])))
                return false;

        return true;
	}

	public function arrayHasPoint($array, $point)
	{
		if (!$array || !$point)
			return false;

		for ($i = 0; $i < count($array); $i++)
        	if ($array[$i] && $array[$i][0] == $point[0] && $array[$i][1] == $point[1])
            	return true;

    	return false;
	}

	public function getMirror($point)
	{
		return [7 - $point[0], $point[1]];
	}
}
