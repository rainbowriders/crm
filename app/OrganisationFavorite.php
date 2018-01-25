<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationFavorite extends Model {

	protected $table = 'organisation_favorite';

	protected $fillable = ['userID', 'organisationID'];

}
