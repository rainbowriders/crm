<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Users;

class Logs extends Model {

	protected $table = 'logs';
	public $timestamps = true;
	protected $fillable = array('userID', 'action', 'old', 'new');

            public function user()
    {
        return $this->belongsTo('App\Users','userID');
    }         
}