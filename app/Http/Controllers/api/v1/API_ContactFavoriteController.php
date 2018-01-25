<?php

namespace App\Http\Controllers\api\v1;

use Request;
use Response;
use Input;
use JWTAuth;
use App\ContactFavorite;
use App\Contacts;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;

class API_ContactFavoriteController extends Controller
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
		$contact = Contacts::find($id);
		$isExistContact = contactFavorite::where('userID', $user->id)->where('contactID', $contact->id)->get();
		
		if(count($isExistContact) > 0)
		{
			return Response::json(array('error' => 'You already have this contact in favorites!'), 404);
		}

		if($contact && $user)
		{
			$contactFavorite = contactFavorite::create([
							'userID' => $user->id,
							'contactID' => $contact->id
						]);

			return Response::json(array('success' => 'Successefully added the contact in favorites!'), 200);
		}
		else 
		{
			return Response::json(array('error'=> 'Unauthorized user, or invalid contact!'), 500);
		}
		
	}

	public function delete($id)
	{
		$user = $this->getAuthenticatedUser();
		$contact = contacts::find($id);
		$isExistContact = contactFavorite::where('userID', $user->id)->where('contactID', $contact->id)->get();
		
		$favorite = contactFavorite::find( $isExistContact[0]['id']);

		if(count($isExistContact)>0)
		{
			$favorite->delete();
			return Response::json(array('success' => 'Successefully remove the contact from favorites!'), 200);
		}
		else
		{
			return Response::json(array('error' => 'The contact is not in your favorites!'), 404);
		}
	}
}