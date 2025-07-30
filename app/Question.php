<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
	protected $fillable = [
		'id',
		'content',
		'number',
		'type',
		'dimension_id',
		'anchors',
		'practice'
	];

	/**
	 * Serialize anchors when saved in storage.
	 *
	 * @param $value
	 */
	public function setAnchorsAttribute($value)
	{
		$this->attributes['anchors'] = serialize($value);
	}

	/**
	 * Un-serialize anchors when retrieved from storage.
	 *
	 * @return mixed
	 */
	public function getAnchorsAttribute()
	{
		return unserialize(clean_non_ascii_characters($this->attributes['anchors']));
	}

	/**
	 * Get the assessment to which this question belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function assessment()
	{
		return $this->belongsTo('App\Assessment');
	}

	/**
	 * Get the dimension to which this question belongs to.
	 *
	 * @return mixed
	 */
	public function dimension()
	{
		return Dimension::find($this->dimension_id);
	}

	/**
	 * Get the dimension code for this specific question.
	 *
	 * @return mixed|string
	 */
	public function dimension_code()
	{
		if (! $this->dimension())
			return $this->number;

		$dimension_code = '';

		if ($this->dimension()->parent_exists())
			$dimension_code .= $this->dimension()->getParent()->code;

		$dimension_code .= $this->dimension()->code;
		$dimension_code .= $this->number;

		return $dimension_code;
	}

	/**
	 * Get the answer submitted for this question for a specific assignment.
	 *
	 * @param $assignment_id
	 * @return mixed
	 */
	public function answerFromAssignment($assignment_id)
	{
		$assignment = Assignment::find($assignment_id);
		if (! $assignment)
			return null;

		return $assignment->answers()->where('question_id', $this->id)->first();
	}

	/**
	 * Get specific user's response to this question for a specific assignment
	 * !! needs to be refactored
	 *
	 * @param $assignment_id
	 * @param $user_id
	 * @return mixed
	 */
	public function getAnswer($assignment_id, $user_id)
	{
		//$i = Answer::all()->where('assignment_id', $assignment_id)->where('question_id', $this->id)->first()->value;

		$assignment = Assignment::findOrFail($assignment_id);

		$answer = $assignment->answers()->where('question_id', $this->id)->get()->first();

		$i = $answer->value;

		$anchors = $this->anchors;

//		if (empty($i))
//			return 'No Answer Given';

		return $anchors[$i];
	}

	/**
	 * Check if an answer exists for this question.
	 *
	 * @param $assignment_id
	 * @param $user_id
	 * @return bool
	 */
	public function answer_exists($assignment_id, $user_id)
	{
		$assignment = Assignment::findOrFail($assignment_id);

		$answer = $assignment->answers()->where('question_id', $this->id)->get()->first();

		if (! $answer)
			return false;

		return true;
	}

	/**
	 * Get the translated version of this question for a specific translation.
	 *
	 * @param $translation_id
	 * @return mixed
	 */
	public function translated($translation_id)
	{
		return TranslatedQuestion::where('question_id', $this->id)->where('translation_id', $translation_id)->first();
	}

	/**
	 * Return all the question types for this system.
	 *
	 * @return array
	 */
	public static function types()
	{
		return [
			1 => [
				'name' => 'Multiple Choice',
				'slug' => 'choice',
				'icon' => 'fa-list-ul',
				'description' => '',
				'default' => 'This is a sample question',
				'show_page' => true,
				'show_content' => true,
			],
			2 => [
				'name' => 'Description',
				'slug' => 'desc',
				'icon' => 'fa-align-left',
				'description' => '',
				'default' => 'This is a description',
				'show_page' => false,
				'show_content' => true,
			],
			10 => [
				'name' => 'Instructions',
				'slug' => 'instruct',
				'icon' => 'fa-list-alt',
				'description' => '',
				'default' => '{"text":"Here are some instructions. Click HERE to edit this text.","next":"Continue"}',
				'show_page' => false,
				'show_content' => false,
			],
			3 => [
				'name' => 'Text Input',
				'slug' => 'input',
				'icon' => 'fa-square-o',
				'description' => '',
				'default' => 'This is a question asking for input',
				'show_page' => true,
				'show_content' => true,
			],
			4 => [
				'name' => 'Letter Sequence',
				'slug' => 'ls',
				'icon' => 'fa-font',
				'description' => 'A sequence of letters. For example: <code>X,Y,Z</code>',
				'default' => 'X,Y,Z',
				'show_page' => false,
				'show_content' => false,
			],
			5 => [
				'name' => 'Math Equation',
				'slug' => 'eq',
				'icon' => 'fa-superscript',
				'description' => 'An equation. For example: <code>(2*2)+2=2</code>',
				'default' => '(2*2)+2=2',
				'show_page' => false,
				'show_content' => false,
			],
			6 => [
				'name' => 'Math and Letters',
				'slug' => 'eqls',
				'icon' => 'fa-list-ol',
				'description' => 'A sequence of letters, with an equation for each one. For example: <code>X,Y,Z</code> and <code>(2*2)+2=2, (4/2)-1=1, 1+2=3</code>',
				'default' => 'X,Y,Z',
				'show_page' => false,
				'show_content' => false,
			],
			7 => [
				'name' => 'Square Sequence',
				'slug' => 'sq',
				'icon' => 'fa-th-large',
				'description' => '',
				'default' => '',
				'show_page' => false,
				'show_content' => false,
			],
			8 => [
				'name' => 'Symmetry',
				'slug' => 'sy',
				'icon' => 'fa-columns',
				'description' => '',
				'default' => '',
				'show_page' => false,
				'show_content' => false,
			],
			9 => [
				'name' => 'Symmetry Squares',
				'slug' => 'sysq',
				'icon' => 'fa-th',
				'description' => '',
				'default' => '',
				'show_page' => false,
				'show_content' => false,
			],
			11 => [
				'name' => 'Slider',
				'slug' => 'slider',
				'icon' => 'fa-sliders',
				'description' => '',
				'default' => 'This is a slider question',
				'show_page' => true,
				'show_content' => true,
			],
		];
	}

	/**
	 * Get the slug of the type for this question.
	 *
	 * @return bool
	 */
	public function getTypeSlug()
	{
		foreach ($this->types() as $typeId => $typeArray)
			if ($this->type == $typeId)
				return $typeArray['slug'];

		return false;
	}

	/**
	 * Get the description for a specific question type.
	 *
	 * @param $type
	 * @return bool
	 */
	public static function getTypeDescription($type)
	{
		foreach (Question::types() as $typeId => $typeArray)
			if ($type == $typeId)
				return $typeArray['description'];

		return false;
	}

	/**
	 * Check if this question is of a WM type.
	 *
	 * @param int|array $except
	 * @return bool
	 */
	public function isWMType($except = null)
	{
		$wm_types = [3,4,5,6,7,8,9,10];

		$exceptions = [];
		if (is_array($except))
			$exceptions = $except;
		else
			$exceptions = [$except];

		if (in_array($this->type, $wm_types) && !in_array($this->type, $exceptions))
			return true;

		return false;
	}

	/**
	 * Check if a specific question type is a WM question.
	 *
	 * @param $type
	 * @return bool
	 */
	public static function checkIfWMType($type)
	{
		$wm_types = [4,5,6,7,8,9,10];

		if (in_array($type, $wm_types))
			return true;

		return false;
	}

	/**
	 * Check if this question has text that can be translated. Dynamic questions are excluded.
	 *
	 * @return bool
	 */
	public function isTranslatable()
	{
		$types = [1,2,3,10];

		if (in_array($this->type, $types))
			return true;

		return false;
	}

	/**
	 * Check if this question needs to show the page number out beside it.
	 *
	 * @return bool
	 */
	public function showPageNumber()
	{
		$types = Question::types();
		foreach ($types as $typeId => $typeArray)
			if ($this->type == $typeId)
				return $typeArray['show_page'];

		return true;
	}

	/**
	 * Check if this question needs to show what content was entered in the question box.
	 *
	 * @return bool
	 */
	public function showContent()
	{
		$types = Question::types();
		foreach ($types as $typeId => $typeArray)
			if ($this->type == $typeId)
				return $typeArray['show_content'];

		return true;
	}

	/**
	 * Get question content, formatted in plain text for an excel document.
	 *
	 * @return mixed|string
	 */
	public function getContentForExcel()
	{
		$content = $this->content;

		// OSpan
		if ($this->type == 6)
		{
			$c = json_decode($this->content);
			$content = implode(', ', $c->equations) . ', ' . implode(', ', $c->letters);
		}

		// SSpan
		if ($this->type == 7) $content = 'Square Sequence';
		if ($this->type == 8) $content = 'Symmetry';
		if ($this->type == 9) $content = 'Symmetry Squares';

		return $content;
	}

	/**
	 * Return only questions that can be answered (ignore descriptions).
	 *
	 * @param $query
	 */
//	public function scopeAnswerable($query)
//	{
//		$query->where('type', '!=', 2);
//	}

	/**
	 * Return sorted alphabetically by dimension id.
	 *
	 * @param $query
	 */
//	public function scopeSorted($query)
//	{
//		$query->sortBy(function($query) {
//			return $query->dimension_id;
//		});
//	}
}