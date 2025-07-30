<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
	protected $fillable = [
		'name',
		'parent',
		'code'
	];

	/**
	 * Get the assessment to which this dimension belongs to.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function assessment()
	{
		return $this->belongsTo('App\Assessment');
	}

	/**
	 * Check if this is a parent dimension.
	 *
	 * @return bool
	 */
	public function isParent()
	{
		if ($this->parent == 0 and $this->hasChildren())
			return true;

		return false;
	}

	/**
	 * Get the parent dimension.
	 *
	 * @return Dimension
	 */
	public function getParent()
	{
		return Dimension::whereId($this->parent)->first();
	}

	/**
	 * Check if this dimension has subdimensions under it.
	 *
	 * @return bool
	 */
	public function hasChildren()
	{
		if (! Dimension::all()->where('parent', $this->id)->isEmpty())
			return true;

		return false;
	}

	/**
	 * Get all the subdimensions of this dimension.
	 *
	 * @return Dimension
	 */
	public function getChildren()
	{
		return Dimension::all()->where('parent', $this->id);
	}

	/**
	 * Check if this dimension is a child dimension.
	 *
	 * @return bool
	 */
	public function isChild()
	{
		if ($this->getParent())
			return true;

		return false;
	}

	/**
	 * Check if this dimension is a child dimension.
	 *
	 * @return bool
	 */
	public function parent_exists()
	{
		if ($this->getParent())
			return true;

		return false;
	}

	/**
	 * Grab the current assessments from the master database, and copy them over.
	 *
	 * @return bool
	 */
	public static function updateResellerDimensions()
	{
		if (! \Auth::user()->isReseller() && !session('reseller'))
			return false;

		$dimensions = session('reseller')->getDimensions();

		foreach ($dimensions as $dimension)
		{
			if (Dimension::find($dimension->id))
				continue;

			$newDimension = new Dimension((array)$dimension);
			$newDimension->save();
		}

		return true;
	}
}
