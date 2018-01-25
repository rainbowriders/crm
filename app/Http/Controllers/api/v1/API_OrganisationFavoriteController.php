<?php

namespace App\Http\Controllers\api\v1;

use Request;
use Response;
use Input;
use JWTAuth;
use App\OrganisationFavorite;
use App\Organisations;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;

class API_OrganisationFavoriteController extends Controller
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
		$organisation = organisations::find($id);
		$isExistorganisation = organisationFavorite::where('userID', $user->id)->where('organisationID', $organisation->id)->get();
		
		if(count($isExistorganisation) > 0)
		{
			return Response::json(array('error' => 'You already have this organisation in favorites!'), 404);
		}

		if($organisation && $user)
		{
			$organisationFavorite = organisationFavorite::create([
							'userID' => $user->id,
							'organisationID' => $organisation->id
						]);

			return Response::json(array('success' => 'Successefully added the organisation in favorites!'), 200);
		}
		else 
		{
			return Response::json(array('error'=> 'Unauthorized user, or invalid organisation!'), 500);
		}
		
	}

	public function delete($id)
	{
		$user = $this->getAuthenticatedUser();
		$organisation = organisations::find($id);
		$isExistorganisation = organisationFavorite::where('userID', $user->id)->where('organisationID', $organisation->id)->get();
		
		$favorite = organisationFavorite::find( $isExistorganisation[0]['id']);

		if(count($isExistorganisation)>0)
		{
			$favorite->delete();
			return Response::json(array('success' => 'Successefully remove the organisation from favorites!'), 200);
		}
		else
		{
			return Response::json(array('error' => 'The organisation is not in your favorites!'), 404);
		}
	}
}