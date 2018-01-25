<?php 

namespace App\Presenters\Revisions;

use Sofa\Revisionable\Laravel\Presenter;

class Comments extends Presenter {
    protected $labels = [
        'text' => 'text',
        'userID' => 'owner'
    ];
    protected $passThrough = [
        'userID'   => 'user.name',
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