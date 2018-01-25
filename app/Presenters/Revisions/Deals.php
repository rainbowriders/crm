<?php namespace App\Presenters\Revisions;

use Sofa\Revisionable\Laravel\Presenter;

class Deals extends Presenter {
    protected $labels = [
        'title' => 'title',
        'organisationID' => 'organisation',
        'userID' => 'owner',
        'contactID' => 'contact person',
        'stageID' => 'stage',
        'currencyID' => 'currency',
        'status' => 'status',
        'value' => 'value'
    ];
    protected $passThrough = [
        'organisationID' => 'organisation.name',
        'userID'   => 'user.name',
        'contactID' => 'contact.name',
        'stageID'        => 'stage.title',
        'currencyID'        => 'currency.name'
    ];
    protected $actions = [
        'created'  => 'created',
        'updated'  => 'updated',
        'deleted'  => 'deleted',
        'restored' => 'restored',
    ];
    public function __call($method, $parameters)
    {
        $value = parent::__call($method, $parameters);
        if(count($parameters) > 0 && method_exists($this, $parameters[0])) {
            return $this->$parameters[0]($value);
        }
        return $value;
    }
}
