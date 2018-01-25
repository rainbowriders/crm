<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Accounts;
use App\Organisations;
use App\Deals;
use App\Users;
use App\Comments;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class API_DeleteOrganisationsController extends Controller {

    public function deleteOrganisations()
    {
    	$organisations = Organisations::all();
        $result = array();
        foreach ($organisations as $o) {
            $deal = Deals::where('organisationID', $o['id'])->first();
            if(!$deal)
            {
                $contacts = Contacts::where('organisationID', $o['id'])->get();
                array_push($result, $o);
                foreach ($contacts as $key => $value) {
                    $value->organisation()->dissociate();
                    $value->save();
                }
            }
        }
        

        foreach ($result as $d) 
        {
            $d->delete();
        }

        return $result;
    }
       
}
