<?php

namespace App\Http\Controllers\api\v1;

use Request;
use Response;
use Input;
use JWTAuth;
use App\DealFavorite;
use App\Deals;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;


class API_DealFavoriteController extends Controller
{
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

	public function create($id)
	{
		$user = $this->getAuthenticatedUser();
		$deal = Deals::find($id);
		$isExistDeal = DealFavorite::where('userID', $user->id)->where('dealID', $deal->id)->get();
		
		if(count($isExistDeal) > 0)
		{
			return Response::json(array('error' => 'You already have this deal in favorites!'), 404);
		}

		if($deal && $user)
		{
			$dealFavorite = DealFavorite::create([
							'userID' => $user->id,
							'dealID' => $deal->id
						]);

			return Response::json(array('success' => 'Successefully added the deal in favorites!'), 200);
		}
		else 
		{
			return Response::json(array('error'=> 'Unauthorized user, or invalid deal!'), 500);
		}
		
	}

	public function delete($id)
	{
		$user = $this->getAuthenticatedUser();
		$deal = Deals::find($id);
		$isExistDeal = DealFavorite::where('userID', $user->id)->where('dealID', $deal->id)->get();
		
		$favorite = DealFavorite::find( $isExistDeal[0]['id']);

		if(count($isExistDeal)>0)
		{
			$favorite->delete();
			return Response::json(array('success' => 'Successefully remove the deal from favorites!'), 200);
		}
		else
		{
			return Response::json(array('error' => 'The deal is not in your favorites!'), 404);
		}
	}
}