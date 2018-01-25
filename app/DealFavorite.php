<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class DealFavorite extends Model {

	protected $table = 'deal_favorite';

	protected $fillable = ['userID', 'dealID'];

}
