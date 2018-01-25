<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stages extends Model {

	protected $table = 'stages';
	public $timestamps = true;
	protected $fillable = array('title');

}