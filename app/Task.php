<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class Task extends Model
{
//    protected $fillable = [
//    	'name',
//    	'completed',
//    	'created_at',
//    	'due_date'
//    ];

//    public function complete()
//    {
//    	$this->completed = true;
//    	$this->completed_at = Carbon\Carbon::now();
//    }
//
//    public function uncomplete()
//    {
//    	$this->completed = false;
//    	$this->completed_at = '';
//    }

	public $blocks = [];
	public $instructs = [];
	public $struct = [];
}
