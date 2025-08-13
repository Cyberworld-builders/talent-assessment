<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'industry_id',
        'dimension_id',
        'value'
    ];

    /**
     * Get the industry that owns this benchmark.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo('App\Industry');
    }

    /**
     * Get the dimension that owns this benchmark.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dimension()
    {
        return $this->belongsTo('App\Dimension');
    }

    /**
     * Get the assessment that this benchmark belongs to through the dimension.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function assessment()
    {
        return $this->hasOneThrough('App\Assessment', 'App\Dimension', 'id', 'id', 'dimension_id', 'assessment_id');
    }

    /**
     * Scope a query to only include benchmarks for a specific industry.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $industryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForIndustry($query, $industryId)
    {
        return $query->where('industry_id', $industryId);
    }

    /**
     * Scope a query to only include benchmarks for a specific dimension.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }

    /**
     * Scope a query to only include benchmarks for a specific assessment.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $assessmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->whereHas('dimension', function ($q) use ($assessmentId) {
            $q->where('assessment_id', $assessmentId);
        });
    }
}
