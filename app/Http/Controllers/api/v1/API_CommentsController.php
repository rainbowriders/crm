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

class API_CommentsController extends Controller {

    
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
        
        $comments = Comments::with('user')->where('accountID',$accountID)->get();

        return $comments;
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

        $data = array();
        $data['dealID'] = Input::get('dealID');
        $data['text'] = Input::get('text');
        $data['accountID'] = $accountID;
        $data['userID'] = $userID;

        $newcomment = Comments::create($data);
        $newcomment->save();
        
        $commentID = $newcomment->id;

        $comment = Comments::with('user')->find($commentID);
        $deal = Deals::find($comment->dealID);

        $deal->updated_at = $comment->created_at;
        $deal->save();
        return Response::json(array(
                    'error' => false,
                    'comment' => $comment), 200
        );
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
        
        // $user = $this->getAuthenticatedUser();
        // $userID = $user['id'];
        // $accountID = $user['accountID'];
        
        // $updatedeal = Deals::find($id);
        
        // $updatedeal->title = Input::get('title');
        // $updatedeal->value = Input::get('value');
        // $updatedeal->stageID = Input::get('stageID');
        // $updatedeal->status = Input::get('status');
        // $updatedeal->contactID = Input::get('contactID');
        // $updatedeal->organisationID = Input::get('organisationID');
        // $updatedeal->accountID = $accountID;
        // $updatedeal->userID = $userID;
        
        // $updatedeal->save();

        // $deal = Deals::with('organisation')->with('contact')->with('user')->with('stage')->find($id);

        // return Response::json(array(
        //             'error' => false,
        //             'deal' => $deal), 200
        // );
        $comment = Comments::find($id);
        $comment['text'] = Input::get('text');
        $comment->save();

        return $comment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $user = $this->getAuthenticatedUser();
        $comment = Comments::find($id);
        $comment->delete();
        return $comment;
    }

}
