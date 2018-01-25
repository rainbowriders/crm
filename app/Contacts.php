<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Organisations;
use App\Accounts;

class Contacts extends Model {

	protected $table = 'contacts';
	public $timestamps = true;
	protected $fillable = array('email', 'accountID', 'organisationID', 'name', 'phone');

    public function organisation()
    {
        return $this->belongsTo('App\Organisations','organisationID');
    }

	// public function account()
	// {
	// 	return $this->belongsTo('App\Accounts','accountID');
	// }

}
