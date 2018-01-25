<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Organisations;
use App\Deals;
use App\Users;
use App\Accounts;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class API_UsersController extends Controller {


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

        $accounts = Accounts::with('users')->where('accounts.id',$accountID)->get();

        return $accounts[0]['users'];
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

        $data = array();
        $data['name'] = Input::get('name');
        $data['email'] = Input::get('email');
        $data['phone'] = Input::get('phone');
        $data['organisationID'] = Input::get('organisationID');

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

        $user = Users::with('accounts')->with('currency')->with('language')->where('id', $userID)->first();

        return $user;

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

        $updateduser = Users::find($id);

        $updateduser->languageID = Input::get('languageID');
        $updateduser->currencyID = Input::get('currencyID');

        $updateduser->save();

        $user = Users::with('currency')->with('language')->find($id);

        return Response::json(array(
                    'error' => false,
                    'user' => $user), 200
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

        $wing = Wings::find($id);
        $wing->delete();
        return Response::json(array(
                    'error' => false,
                    'wing' => $wing), 200
        );
    }

}
