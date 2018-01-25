<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Accounts;
use App\Organisations;
use App\Deals;
use App\Users;
use Mail;
use Hash;
use App\Comments;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class API_ResetPasswordController extends Controller {

	public function sendLink()
	{
		$user = Users::where('email', Input::get('email'))->first();

		if(!$user)
		{
			return Response::json(array('error' => 'Invalid email address!'), 404);
		}
		else
		{
			$user->password_reset_token = $this->generateUniqueToken($user);
			$user->save();


          Mail::send('emails.send-password-reset-link', ['user' => $user], function($message) use($user)
          {
            $message->to($user->email)
                    ->from('crm@rainbowriders.dk', 'Rainbow CRM')
                    ->subject('Password reset email');
          });
          return Response::json(array('success' => 'We send email with link to reset your password! Please check your email address!'), 200);
		}
	}

	public function resetPassword()
	{
		$user = Users::where('password_reset_token', Input::get('token'))->first();
		if(!$user)
		{
			return Response::json(array('error' => 'INVALID_TOKEN'), 404);
		}
		else 
		{
			$password = Hash::make(Input::get('password'));
			$user->password = $password;
			$user->password_reset_token = $this->generateUniqueToken($user);
			$user->save();
			return Response::json(array('success' => 'PASSWORD_CHANGED_SUCCESSFULLY'), 200);
		}
	}


	private function generateUniqueToken($user)
	{
		$password_reset_token = str_random(12) . time() . str_random(12);
    	$user = Users::where('password_reset_token', $password_reset_token)->first();
    	if($user)
    	{
    		return $this->generateRandomRememberToken();
    	}
    	return $password_reset_token;
	}

}
