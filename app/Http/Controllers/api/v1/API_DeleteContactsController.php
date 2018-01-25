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

class API_DeleteContactsController extends Controller {

    public function deleteContacts()
    {
    	$contacts = Contacts::all();
    	$result = array();
    	foreach ($contacts as $contact) {
    		$deal = Deals::where('contactID', $contact['id'])->first();
    		if(!$deal)
    		{
    			array_push($result, $contact);
    		}
    	}
    
    	foreach ($result as $c) {
    		$c->delete();
    	}

    	return $result;
    }
       
}
