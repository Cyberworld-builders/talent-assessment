<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all benchmarks for this industry.
     */
    public function benchmarks()
    {
        return $this->hasMany('App\Benchmark');
    }

    /**
     * Get all users in this industry.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}

