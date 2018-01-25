<?php

namespace App;

use Sofa\Revisionable\Laravel\RevisionableTrait; // trait
use Sofa\Revisionable\Revisionable; // interface

use Illuminate\Database\Eloquent\Model;
use App\Contacts;
use App\Organisations;
use App\Users;
use App\Stages;
use App\Currencies;
use App\Comments;
use App\Logs;

class Deals extends \Eloquent implements Revisionable {

    use RevisionableTrait;

        protected $revisionable = [
        'title', 'organisationID', 'userID', 'contactID', 'stageID', 'currencyID', 'status', 'value'
        ];

	protected $table = 'deals';
	public $timestamps = true;
	protected $fillable = array('title', 'organisationID', 'accountID', 'userID', 'contactID', 'stageID', 'currencyID', 'status', 'value');

        protected $revisionPresenter = 'App\Presenters\Revisions\Deals';

            public function contact()
    {
        return $this->belongsTo('App\Contacts','contactID');
    }

            public function organisation()
    {
        return $this->belongsTo('App\Organisations','organisationID');
    }

            public function user()
    {
        return $this->belongsTo('App\Users','userID');
    }



            public function stage()
    {
        return $this->belongsTo('App\Stages','stageID');
    }

    public function currency()
    {
    return $this->belongsTo('App\Currencies','currencyID');
    }

            public function comments()
    {
        return $this->hasMany('App\Comments','dealID','id');
    }

            public function logs()
    {
        return $this->hasMany('App\Logs','dealID','id');
    }
    }
