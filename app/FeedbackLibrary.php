<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackLibrary extends Model
{
    protected $fillable = [
        'name',
        'feedback',
        'client_id'
    ];

    protected $table = 'feedback_libraries';

    /**
     * Get the client to which this feedback library belongs.
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * JSON encode feedback when saved in storage.
     */
    public function setFeedbackAttribute($value)
    {
        $this->attributes['feedback'] = json_encode($value);
    }

    /**
     * JSON decode feedback when retrieved from storage.
     */
    public function getFeedbackAttribute()
    {
        return json_decode($this->attributes['feedback'], true);
    }
}
