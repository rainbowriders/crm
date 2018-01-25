<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Organisations;
use App\Deals;
use App\Users;
use App\Accounts;
use App\UserAccount;
use Mail;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class API_UserAccountController extends Controller {


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

      $user = $this->getAuthenticatedUser();
      $userName = $user['name'];
      $accountID = Input::get('account');

        $email = Input::get('email');
        $user = Users::where('email',$email)->first();

        $toUserName = $user['name'];

        if($user){
          if($user->confirmed == 0)
          {
            $users = Accounts::with('users')->where('accounts.id',$accountID)->get();
            $companyName = $users[0]['owner'];
            $invitation_code = str_random(32);
            $user->invitation_code = $invitation_code;
            $user->save();

            Mail::send('emails.signupinvite', ['owner' => $userName, 'company' => $companyName, 'invitation_code' => $invitation_code, 'email' => $email], function($message) use($userName, $email, $companyName, $invitation_code)
            {
              $message->to($email)
                      ->from('crm@rainbowriders.dk', 'Rainbow CRM')
                      ->subject('Rainbow CRM: Invitation from '.$userName);
            });

            return Response::json(array(
                        'error' => false,
                        'users' => $users[0]['users']), 200
            );
          }


          $userID = $user['id'];

          $data = array();
          $data['userID'] = $userID;
          $data['accountID'] = $accountID;

          $newuseraccount = UserAccount::firstOrCreate($data);
          $newuseraccount->save();

          $users = Accounts::with('users')->where('accounts.id',$accountID)->get();
          $companyName = $users[0]['owner'];
          Mail::send('emails.invite', ['owner' => $userName, 'company' => $companyName], function($message) use($userName, $email, $toUserName, $companyName)
          {
            $message->to($email, $toUserName)
                    ->from('crm@rainbowriders.dk', 'Rainbow CRM')
                    ->subject('Rainbow CRM: Invitation from '.$userName);
          });

          return Response::json(array(
                      'error' => false,
                      'users' => $users[0]['users']), 200
          );
        }
        else{

          $invitation_code = str_random(32);
          $user = new Users;
          $user->email = Input::get('email');
          $user->confirmed ='0';
          $user->invitation_code = $invitation_code;
          $user->save();

          $userID = $user->id;

          $user_account = new UserAccount;
          $user_account->userID = $userID;
          $user_account->accountID = $accountID;
          $user_account->save();

          $users = Accounts::with('users')->where('accounts.id',$accountID)->get();
          $companyName = $users[0]['owner'];

          Mail::send('emails.signupinvite', ['owner' => $userName, 'company' => $companyName, 'invitation_code' => $invitation_code, 'email' => $email], function($message) use($userName, $email, $companyName, $invitation_code)
          {
            $message->to($email)
                    ->from('crm@rainbowriders.dk', 'Rainbow CRM')
                    ->subject('Rainbow CRM: Invitation from '.$userName);
          });

          return Response::json(array(
                      'error' => false,
                      'users' => $users[0]['users']), 200
          );
        }

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
