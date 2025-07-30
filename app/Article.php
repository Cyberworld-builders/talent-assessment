<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Article extends Model
{
    protected $fillable = [
    	'title',
    	'body',
    	'published_at'
    ];
    protected $dates = ['published_at']; // Makes it a Carbon instance

    // query scope
    public function scopePublished($query)
    {
    	$query->where('published_at', '<=', Carbon::now());
    }

    // query scope
    public function scopeUnpublished($query)
    {
    	$query->where('published_at', '>', Carbon::now());
    }

    // mutator setNameAttribute
    public function setPublishedAtAttribute($date)
    {
    	$this->attributes['published_at'] = Carbon::parse($date);
    }

    // ability to retrieve $article->user
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}