<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Users;
use App\Languages;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class API_LanguagesController extends Controller {


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
        $accountID = $user['accountID'];

        $languages = Languages::get();

        return $languages;
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
        $accountID = $user['accountID'];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $deal = Deals::with('organisation')->with('stage')->with('contact')->with('user')->with(['comments' => function($query) {
                        $query->with('user');
                    }])->with(['logs' => function($query) {
                        $query->with('user');
                    }])->find($id);

        return $deal;
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
        //
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
