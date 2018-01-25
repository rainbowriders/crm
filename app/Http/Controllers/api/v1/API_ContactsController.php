<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Organisations;
use App\Deals;
use App\Users;
use App\Accounts;
use App\ContactFavorite;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use DateTime;

class API_ContactsController extends Controller {


        private function getAuthenticatedUser() {
        try {

            if (!$user = JWTAuth::parseToken()->toUser()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }
        if($user->last_login != null) {
            $last_login = Carbon::parse($user->last_login);
            $now = Carbon::now();
            if($now->diffInDays($last_login) != 0) {
                $user->last_login = new DateTime();
                $user->save();
            }
        } else {
            $user->last_login = new DateTime();
            $user->save();
        }

            // the token is valid and we have found the user via the sub claim
        return $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $user = $this->getAuthenticatedUser();
        $userID = $user['id'];
        $accountID = Input::get('account');
        $data = Input::all();

        if(array_key_exists('pattern', $data))
        {
            $pattern = $data['pattern'];
        }
        if(array_key_exists('start', $data))
        {
            $startCount = $data['start'];
        }
        if(array_key_exists('favorites', $data))
        {
          $favorites = ContactFavorite::where('userID', $user->id)->get();
          $favoritesIDs = array();
          foreach ($favorites as $key => $value) {
            array_push($favoritesIDs, $value->contactID);
          }
          $contacts = Contacts::whereIn('id', $favoritesIDs)->with('organisation')->get();
          $result = $contacts;
          if(array_key_exists('pattern', $data))
          {
            $result = $this->filterBySearchPattern($result, $pattern);
          }
          $result = $this->handleResult($result, $user->id);

          return array('count' => count($result), 'contacts' => $result);
        }

        if(array_key_exists('pattern', $data))
        {
            //return contacts from search engine

            $contacts = Contacts::orderBy('name')
            ->with('organisation')
            ->where('name', 'like', '%'.$pattern.'%')
            ->where('accountID', $accountID)
            ->get();

            $result = $this->filterBySearchPattern($contacts, $pattern);
            $result = $this->handleResult($result, $user->id);

            return array('count' => count($result), 'contacts' => $result);
        }
        else
        {   
          //return contacts from all data

          $contactsCount = Contacts::where('accountID',$accountID)->count();
          $contacts = Contacts::orderBy('name')->limit(100)
          ->with('organisation')
          ->where('accountID', $accountID)
          ->skip($startCount)
          ->get();

          $result = $this->handleResult($contacts, $user->id);

          return array('count' => $contactsCount, 'contacts' => $result);
        }
        
    }
    //return tight view of contacts
    //
    private function handleResult($contacts, $userId)
    {
        $result = array();
        for ($i=0; $i < count($contacts); $i++) { 
            $currContact = $contacts[$i];
            $contact = array();
            $contact['id'] = $currContact['id'];
            $contact['name'] = $currContact['name'];
            $contact['email'] = $currContact['email'];
            $contact['phone'] = $currContact['phone'];
            if(count($currContact['organisation']) > 0)
            {
                $contact['organisation'] = array();
                $contact['organisation']['name'] = $currContact['organisation']['name'];
                $contact['organisation']['id'] = $currContact['organisation']['id'];
            }
            $isFavorite = ContactFavorite::where('contactID', $currContact['id'])
              ->where('userID', $userId)->get();
            if(count($isFavorite) > 0)
            {
              $contact['isFavorite'] = true;
            }
            else
            {
              $contact['isFavorite'] = false;
            }

            array_push($result, $contact);
        }

        return $result;
    }

    public function filterBySearchPattern($contacts, $pattern)
    {
        $result = array();
        $pattern = strtolower($pattern);
        for ($i=0; $i < count($contacts) ; $i++) { 
           $currContact = $contacts[$i];

            $contactName = strtolower($currContact['name']);
            if (strpos($contactName, $pattern) !== false){
                array_push($result, $currContact);
                continue;
            }
            $contactEmal = strtolower($currContact['email']);
            if (strpos($contactEmal, $pattern) !== false){
                array_push($result, $currContact);
                continue;
            }
            $organisationName = strtolower($currContact['organisation']['name']);
            if (strpos($organisationName, $pattern) !== false){
                array_push($result, $currContact);
                continue;
            }
        }

        return $result;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

      $user = $this->getAuthenticatedUser();
      $userID = $user['id'];
      $accountID = Input::get('account');

      if(Input::get('organisationID')){
        $organisationID = Input::get('organisationID');
      }
      if(Input::get('organisationName')){
        $data = array();
        $data['name'] = Input::get('organisationName');
        $data['accountID'] = $accountID;

        $neworganisation = Organisations::firstOrCreate($data);
        $neworganisation->save();

        $organisationID = $neworganisation->id;
      }

        $data = array();
        $data['name'] = Input::get('name');
        $data['email'] = Input::get('email');
        $data['phone'] = Input::get('phone');
        if(Input::get('organisationName') || Input::get('organisationID')){
          $data['organisationID'] = $organisationID;
        }
        
        $data['accountID'] = $accountID;

        $newcontact = Contacts::firstOrCreate($data);
        $newcontact->save();

        $contactID = $newcontact->id;

        $contact = Contacts::with('organisation')->find($contactID);

        return Response::json(array(
                    'error' => false,
                    'contact' => $contact), 200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {

        $user = $this->getAuthenticatedUser();
        $userID = $user['id'];
        $accountID = Input::get('account');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

      $user = $this->getAuthenticatedUser();
      $userID = $user['id'];
      $accountID = Input::get('account');

      $updatedeal = Deals::find($id);
      $organisationID = null;
      if(Input::get('organisationID')){
        $organisationID = Input::get('organisationID');
      }
      if(Input::get('organisationName')){
        $data = array();
        $data['name'] = Input::get('organisationName');
        $data['accountID'] = $accountID;

        $neworganisation = Organisations::firstOrCreate($data);
        $neworganisation->save();

        $organisationID = $neworganisation->id;
      }

        $updatecontact = Contacts::find($id);

        $updatecontact->name = Input::get('name');
        $updatecontact->email = Input::get('email');
        $updatecontact->phone = Input::get('phone');
        $updatecontact->organisationID = $organisationID;
        $updatecontact->accountID = $accountID;

        $updatecontact->save();

        $contact = Contacts::with('organisation')->find($id);

        return Response::json(array(
                    'error' => false,
                    'contact' => $contact), 200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $user = $this->getAuthenticatedUser();
        $userID = $user['userID'];
        $deals = Deals::where('contactID', $id)->get();
        $contact = Contacts::find($id);

        $contact->organisation()->dissociate();

        foreach ($deals as $key => $value) {
          $value->contact()->dissociate();
          $value->save();
        }
        $contact->save();
        $contact->delete();
        return Response::json(array(
                    'error' => false,
                    'wing' => $contact), 200
        );
    }

}
