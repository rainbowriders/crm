<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model {

	protected $table = 'languages';
	public $timestamps = false;
	protected $fillable = array('name', 'code');

}
