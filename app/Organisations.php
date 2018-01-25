<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Accounts;

class Organisations extends Model {

	protected $table = 'organisations';
	public $timestamps = true;
	protected $fillable = array('name', 'accountID', 'address', 'zip', 'city');

    // public function account()
    // {
    //     return $this->belongsTo('App\Accounts','accountID');
    // }
}
