<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Accounts;
use App\Users;

class UserAccount extends Model {

	protected $table = 'user_account';
	public $timestamps = true;
	protected $fillable = array('userID', 'accountID', 'owner');

}
