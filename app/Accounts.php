<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Users;

class Accounts extends Model {

	protected $table = 'accounts';
	public $timestamps = true;
	protected $fillable = array('owner','creator');

	public function users()
	{
			return $this->belongsToMany('App\Users','user_account','accountID','userID');
	}

}
