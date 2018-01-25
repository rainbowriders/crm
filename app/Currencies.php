<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currencies extends Model {

	protected $table = 'currencies';
	public $timestamps = false;
	protected $fillable = array('name', 'sign');

}
