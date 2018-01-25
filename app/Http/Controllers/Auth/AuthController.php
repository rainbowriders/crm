<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Users;
use App\Accounts;
use App\UserAccount;
use Request;
use Response;
use Input;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Hash;
use JWTAuth;
use Mail;
use Authenticatable, CanResetPassword;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use DateTime;
use App\Activity;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

    public function login()
    {
    	if(Input::get('remember_token'))
    	{
    		$user = Users::where('remember_token', Input::get('remember_token'))->with('currency')->with('language')->with('accounts')->first();
			$user->last_login = new DateTime();
			$user->save();
			Activity::create(['user_id' => $user->id]);
    		$token = JWTAuth::fromUser($user);
    		return Response::json(array('token' => $token, 'user' => $user));
    	}
        $forlogin = array(
            'email' => Input::get('email'),
            'password' => Input::get('password')
        );
        $user = Users::where('email', '=', $forlogin['email'])->with('currency')->with('language')->with('accounts')->first();
        if (!$user)
        {

          return Response::json(array('msg' => 'Wrong email'), 401);
        }
				else if ($user['confirmed']==0){
					return Response::json(array('msg' => 'Your account is not yet verified. Check your email for the verification email.'), 401);
				}
        else{

            try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($forlogin)) {

					return Response::json(array('msg' => 'Wrong password'),401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return Response::json(array('msg' => 'Something went wrong, please try again.'),500);
        }

        // all good so return the token
        //return response()->json(compact('token'));

            //$token = JWTAuth::attempt($forlogin);
        	if(Input::get('rememberMe') === '1')
        	{
    			$remember_token = $this->generateRandomRememberToken();
	        	$user->remember_token = $remember_token;
        	}
        	else
        	{
        		$user->remember_token = null;
        	}
			$user->last_login = new DateTime();
			$user->save();
			Activity::create(['user_id' => $user->id]);
            return Response::json(array('token' => $token, 'user' => $user));
        }


    }

    protected function generateRandomRememberToken()
    {	
    	$remember_token = str_random(12) . time() . str_random(12);
    	$user = Users::where('remember_token', $remember_token)->first();
    	if($user)
    	{
    		return $this->generateRandomRememberToken();
    	}
    	return $remember_token;
    }

    public function signup(Request $request)
    {
        $input = Request::only('email','password','name','company', 'invitation_code');

				if(!$input['invitation_code']){
					$rules = array('email' => 'required|unique:users,email',
	                       'password' => 'required');
	        $validator = \Validator::make($input, $rules);
	        if ($validator->fails()) {
	            return Response::json(array('success'=>'false', 'error'=>$validator->messages()), 400);
	        }
	        else
	        {
	            $confirmation_code = str_random(32);
							$user = new Users;
	            $user->email = $input['email'];
	            $user->password = Hash::make($input['password']);
	            $user->name = $input['name'];
				$user->confirmed ='0';
				$user->confirmation_code = $confirmation_code;
	            $user->save();

				$userID = $user->id;

				$account = new Accounts;
				$account->owner = $input['company'];
				$account->creator = $userID;
				$account->save();

				$user_account = new UserAccount;
				$user_account->userID = $userID;
				$user_account->accountID = $account->id;
				$user_account->save();

				Mail::send('emails.confirmation', ['confirmation_code' => $confirmation_code], function($message) use ($input)
				{
					$message->to($input['email'], $input['name'])
									->from('crm@rainbowriders.dk', 'Rainbow CRM')
									->subject('Rainbow CRM: Account confirmation');
				});

			return Response::json(array('msg' => 'Account created! Check your email to confirm the account.'),200);

	        }
				}
				else{
					$rules = array('password' => 'required', 'invitation_code'=>'required');
	        $validator = \Validator::make($input, $rules);
	        if ($validator->fails()) {
	            return Response::json(array('success'=>'false', 'error'=>$validator->messages()), 401);
	        }
	        else
	        {
	            $user = Users::where('email',$input['email'])->first();
				$user->password = Hash::make($input['password']);
	            $user->name = $input['name'];
				$user->confirmed ='1';
	            $user->save();

				$userID = $user->id;

				$user = Users::where('email',$input['email'])->with('currency')->with('language')->with('accounts')->first();
    			$token = JWTAuth::fromUser($user);
    			return Response::json(array('token' => $token, 'user' => $user));

	        }
				}

    }

	public function confirm($id)
    {
		$confirmation_code = $id;

        $user = Users::where('confirmation_code', '=', $confirmation_code)->first();
		$user->confirmed = '1';
		$user->save();
		return $user;
		// $forlogin = array(
  //       	'email' => $user['email'],
		// 	'confirmed' => '1'
  //       );


   //      if (!$user)
   //      {

   //          return Response::json(array('msg' => 'No user with this confirmation code.'), 401);
   //      }
   //      else{

   //          try {
   //          // attempt to verify the credentials and create a token for the user
   //          if (! $token = JWTAuth::fromUser($user)) {

			// 				return Response::json(array('msg' => 'No such user'),401);
   //          }
   //      } catch (JWTException $e) {
   //          // something went wrong whilst attempting to encode the token
   //          return Response::json(array('msg' => 'Something went wrong, please try again.'),500);
   //      }

   //      // all good so return the token
   //      //return response()->json(compact('token'));

   //          //$token = JWTAuth::attempt($forlogin);
   //          //return Response::json(array('token' => $token, 'user' => $user));
			// return redirect('confirm');
   //      }


    }

}
