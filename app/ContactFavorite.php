<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactFavorite extends Model {

	protected $table = 'contact_favorite';

	protected $fillable = ['userID', 'contactID'];

}
