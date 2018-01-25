<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Accounts;
use App\Currencies;
use App\Languages;

class Users extends Model implements AuthenticatableContract, CanResetPasswordContract {

        use Authenticatable, CanResetPassword;

	protected $table = 'users';
	public $timestamps = true;
	protected $fillable = array('email', 'password', 'accountID', 'name', 'languageID', 'currencyID', 'confirmed', 'confirmation_code', 'remember_token', 'last_login');
        protected $hidden = ['password', 'password_reset_token'];

    public function accounts()
    {
        return $this->belongsToMany('App\Accounts','user_account','userID','accountID');
    }

    public function currency()
    {
    return $this->belongsTo('App\Currencies','currencyID');
    }

    public function language()
    {
    return $this->belongsTo('App\Languages','languageID');
    }

}
