<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Accounts;
use App\Organisations;
use App\OrganisationFavorite;
use App\Deals;
use App\Users;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DateTime;

class API_OrganisationsController extends Controller {


    use SoftDeletes;
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
            $pattern = '%'.$data['pattern'].'%';
        }
        if(array_key_exists('start', $data))
        {
            $startCount = $data['start'];
        }

        if(array_key_exists('pattern', $data))
        {
            //return organisations from search engine
            $organisations = Organisations::where('accountID', $accountID)->orderBy('name')
            ->where('name', 'like', $pattern)
            ->get(array('name', 'address', 'zip', 'city', 'id'));
            
            $result = $this->handleOrganisation($organisations, $user->id);
            if(array_key_exists('favorites', $data))
            {
                $favoriteContainer = array();
                for ($i=0; $i < count($result); $i++) { 
                    if($result[$i]['isFavorite'] === true)
                    {
                        array_push($favoriteContainer, $result[$i]);
                    }
                }

                return $favoriteContainer;
            }
            return $result;
        }
        else
        {
            //return organisations from all data
            $organisationsCount = Organisations::where('accountID',$accountID)->count();
            $organisations = Organisations::orderBy('name')->limit(100)->where('accountID',$accountID)
            ->skip($startCount)
            ->get(array('name', 'address', 'zip', 'city', 'id'));
            $result = $this->handleOrganisation($organisations, $user->id);
            if(array_key_exists('favorites', $data))
            {
                $favoriteContainer = array();
                for ($i=0; $i < count($result); $i++) { 
                    if($result[$i]['isFavorite'] === true)
                    {
                        array_push($favoriteContainer, $result[$i]);
                    }
                }

                return array('count'=> $organisationsCount,'organisations' => $favoriteContainer);
            }

            
            return array('count'=> $organisationsCount,'organisations' => $result);
        }
    }

    private function handleOrganisation($organisations, $userId)
    {   
        $result = array();
        for ($i=0; $i < count($organisations); $i++) { 
            $currOrganisation = $organisations[$i];
            $isFavorite = OrganisationFavorite::where('organisationID', $currOrganisation['id'])
              ->where('userID', $userId)->get();
            if(count($isFavorite) > 0)
            {
              $currOrganisation['isFavorite'] = true;
            }
            else
            {
              $currOrganisation['isFavorite'] = false;
            }
            array_push($result, $currOrganisation);
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

        $data = array();
        $data['name'] = Input::get('name');
        $data['address'] = Input::get('address');
        $data['zip'] = Input::get('zip');
        $data['city'] = Input::get('city');
        $data['accountID'] = $accountID;

        $neworganisation = Organisations::firstOrCreate($data);
        $neworganisation->save();

        $organisationID = $neworganisation->id;

        $organisation = Organisations::find($organisationID);

        return Response::json(array(
                    'error' => false,
                    'organisation' => $organisation), 200
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

        $updateorganisation = Organisations::find($id);

        $updateorganisation->name = Input::get('name');
        $updateorganisation->address = Input::get('address');
        $updateorganisation->zip = Input::get('zip');
        $updateorganisation->city = Input::get('city');

        $updateorganisation->save();

        $organisation = Organisations::find($id);

        return Response::json(array(
                    'error' => false,
                    'organisation' => $organisation), 200
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
        $organisation = Organisations::find($id);
        $contacts = Contacts::where('organisationID',$id)->get();
        $deals = Deals::where('organisationID', $id)->get();

        foreach ($contacts as $key => $value) {
            $value->organisation()->dissociate();
            $value->save();
        }

        foreach ($deals as $key => $value) {
            $value->organisation()->dissociate();
            $value->save();
        }

        $organisation->delete();
        return Response::json(array(
                    'error' => false,
                    'organisation' => $organisation), 200
        );
    }

}
