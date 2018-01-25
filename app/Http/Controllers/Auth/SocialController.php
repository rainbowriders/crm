<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Response;
use Request;
use Input;
use Google_Client;
use Google_Service_Plus_Person;
use Google_Service_Plus;
use App\Users;
use App\UserAccount;
use App\Accounts;
use JWTAuth;
use Socialite;
use GuzzleHttp;
use App\Activity;

class SocialController extends Controller
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

    public function google(Request $request) {

        $data = Input::all();
        $client = new Google_Client();
        $client->setApplicationName('Simple CRM');
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
        $client->authenticate($data['code']);

        $plus = new Google_Service_Plus($client);
        $userInfo = $this->getGoogleUserInfo($plus->people->get('me'));
        if($userInfo) {
            $user = Users::where('email', $userInfo['email'])->with('currency')->with('language')->with('accounts')->first();
            if($user) {
                $token = JWTAuth::fromUser($user);
            } else {
                $newUser = $this->createUserFromSocials($userInfo['email'], $userInfo['name']);

                $user = Users::with('currency')->with('language')->with('accounts')->find($newUser->id);

                $token = JWTAuth::fromUser($user);
            }
		Activity::create(['user_id' => $user->id]);

            return Response::json(array('token' => $token, 'user' => $user), 200);
        }

        return false;

    }
    
    private function getGoogleUserInfo(Google_Service_Plus_Person $gPerson) {
        return array(
            'name' => $gPerson->getDisplayName(),
            'email' => $gPerson->getEmails()[0]->getValue(),
        );
    }
    
    public function facebook() {
        $token = Input::get('token');

        $fbUser = Socialite::driver('facebook')->userFromToken($token);

        if($fbUser) {
            $user = Users::where('email', $fbUser->getEmail())->with('currency')->with('language')->with('accounts')->first();
            if($user) {
                $token = JWTAuth::fromUser($user);

            } else {
                $newUser = $this->createUserFromSocials($fbUser->getEmail(), $fbUser->getName());


                $user = Users::with('currency')->with('language')->with('accounts')->find($newUser->id);

                $token = JWTAuth::fromUser($user);
            }
		Activity::create(['user_id' => $user->id]);
            return Response::json(array('token' => $token, 'user' => $user), 200);

        }
	return false;
    }

    public function twitter() {

        $link = Socialite::driver('twitter')->redirect();



        return Response::json(array('token' => 'asd', 'oauth_token' => str_replace('https://api.twitter.com/oauth/authenticate?oauth_token=', '', $link)), 200);
    }

    public function linked() {

        $code = Input::get('code');
        $clientId = config('services.linkedin.client_id');
        $clientSecret = config('services.linkedin.client_secret');
        $redirectUri = config('services.linkedin.redirect');

        $client = new GuzzleHttp\Client();

        $url = 'https://www.linkedin.com/uas/oauth2/accessToken?grant_type=authorization_code&code=' . $code . '&redirect_uri=' . $redirectUri . '&client_id=' . $clientId . '&client_secret=' . $clientSecret;

        $request = $client->request('POST', $url);

        $response = json_decode($request->getBody(), true);

        $linkedUser = Socialite::driver('linkedin')->userFromToken($response['access_token']);
        if($linkedUser) {
            $user = Users::where('email', $linkedUser->getEmail())->with('currency')->with('language')->with('accounts')->first();
            if($user) {
                $token = JWTAuth::fromUser($user);
            } else {
                $newUser = $this->createUserFromSocials($linkedUser->getEmail(), $linkedUser->getName());


                $user = Users::with('currency')->with('language')->with('accounts')->find($newUser->id);

                $token = JWTAuth::fromUser($user);
            }
	    Activity::create(['user_id' => $user->id]);
            return Response::json(array('token' => $token, 'user' => $user), 200);
        }

        return false;
    }
    
    public function addCompanyFromSocial() {

        $user = $this->getAuthenticatedUser();
        $account = Accounts::create(array(
            'owner'     => Input::get('name'),
            'creator'   => $user->id,
        ));

        $userAccount = UserAccount::create(array(
            'userID'    => $user->id,
            'accountID' =>$account->id,
        ));



        return Response::json(array('user' => Users::with('currency')->with('language')->with('accounts')->find($user->id)));
    }

    private function createUserFromSocials($email, $name) {

        $newUser = new Users;
        $newUser->email = $email;
        $newUser->name = $name;
        $newUser->languageID = 1;
        $newUser->currencyID = 1;
        $newUser->confirmed = 1;
        $newUser->save();

        return $newUser;

    }
}
