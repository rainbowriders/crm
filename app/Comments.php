<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Users;
use Sofa\Revisionable\Laravel\RevisionableTrait; // trait
use Sofa\Revisionable\Revisionable; // interface
use App\Logs;

class Comments extends Model implements Revisionable {

	use RevisionableTrait;

    protected $revisionable = ['text', 'userID', 'dealID'];

	protected $table = 'comments';
	public $timestamps = true;
	protected $fillable = array('text', 'userID', 'dealID');

    public function user()
    {
        return $this->belongsTo('App\Users','userID');
    }         
    public function logs()
    {
        return $this->hasMany('App\Logs','dealID','id');
    }
}