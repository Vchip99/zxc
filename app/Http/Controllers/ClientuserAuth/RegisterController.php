<?php

namespace App\Http\Controllers\ClientuserAuth;

use App\Models\Clientuser;
use App\Models\Client;
use Validator, Redirect,DB,Mail,Cache;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Mail\ClientUserEmailVerification;
use App\Mail\NewClientUserRegistration;
use App\Mail\ClientUnAuthorisedUser;
use App\Jobs\SendMail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('clientuser.guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if('mobile' == $data['signup_type']){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'phone' => 'required'
            ]);
        } else {
            return Validator::make($data, [
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'password' => 'required',
                'confirm_password' => 'required|same:password'
            ]);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Clientuser
     */
    protected function create(array $data,$clientId)
    {
        if('mobile' == $data['signup_type']){
            $numberVerified = 1;
            $email = '';
            $phone = $data['phone'];
            $emailToken = '';
        } else {
            $numberVerified = 0;
            $email = $data['email'];
            $phone = $data['phone'];
            $emailToken = str_random(60);
        }
        $clientUser = Clientuser::create([
            'name' => $data['name'],
            'email' => $email,
            'phone' => $phone,
            'client_id' => $clientId,
            'client_approve' => 1,
            'password' => bcrypt($data['password']),
            'email_token' => $emailToken,
            'number_verified' => $numberVerified,
        ]);
        return $clientUser;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('clientuser.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('clientuser');
    }

    public function register(Request $request)
    {
        if('mobile' == $request->get('signup_type')){
            $userOtp = $request->get('user_otp');
            $userPhone = $request->get('phone');
            $mobile = Cache::get('mobile-'.$userPhone);
            $serverOtp = Cache::get($mobile);
            if($userOtp != $serverOtp){
                return Redirect::to('/')->withErrors('Entered otp is wrong.');
            } else {
                if(Cache::has($mobile) && Cache::has('mobile-'.$userPhone)){
                    Cache::forget($mobile);
                    Cache::forget('mobile-'.$userPhone);
                }
            }
        }

        // Laravel validation
        $validator = $this->validator($request->all());
        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }

        $subdomain = $request->getHost();
        $client = Client::where('subdomain', $subdomain)->first();
        if(!is_object($client)){
            return Redirect::to('/');
        }
        // if free plan & user count greter than 10 then dont allow to signup.
        if(1 == $client->plan_id && 10 <= $client->userCountByClientId($client->id)){
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['client'] = $client->name;

            // send mail to client
            Mail::to($client->email)->send(new ClientUnAuthorisedUser($data));
            return Redirect::to('/')->withErrors('Try after some time');
        }
        if(!empty($request->get('email'))){
            $checkEmail = Clientuser::where('email', $request->get('email'))->where('client_id', $client->id)->first();
            if(is_object($checkEmail)){
                return Redirect::to('/')->withErrors('The email id '.$request->get('email').' is already exist.');
            }
        }

        DB::connection('mysql2')->beginTransaction();
        try
        {
            $user = $this->create($request->all(),$client->id);
            if(1 == $user->number_verified){
                // un approve number if have same number to other users with same client
                $otherUsers = Clientuser::where('phone', $user->phone)->where('client_id', $client->id)->where('id','!=', $user->id)->get();
                if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
                    foreach($otherUsers as $otherUser){
                        $otherUser->number_verified = 0;
                        $otherUser->save();
                    }
                }
            }
            if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                // After creating the user send an email with the random token generated in the create method above
                $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                Mail::to($user->email)->send($clientUserEmail);
            }
            // $client = Clientuser::getClientByClientUserEmail($request, $user->email);
            if(is_object($client)){
                $data = [
                    'name' => $user->name,
                    'email' => $user->email,
                ];
                Mail::to($client->email)->send(new NewClientUserRegistration($data));
            }
            DB::connection('mysql2')->commit();
            if('mobile' == $request->get('signup_type')){
                return redirect('/')->with('message', 'Please sign up using mobile.');
            } else {
                if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                    return redirect('/')->with('message', 'Verify your email for your account activation.');
                } else {
                    return redirect('/')->with('message', 'Please login using User Id.');
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back();
        }
    }

     // Get the user who has the same token and change his/her status to verified i.e. 1
    public function verify($subdomain, $token)
    {
        // The verified method has been added to the user model and chained here
        // for better readability
        $user = Clientuser::where('email_token',$token)->first();
        if(is_object($user)){
            $user->verified();
            return redirect('/')->with('message', 'please login with credentials.');
        } else {
            return redirect('/')->withErrors('These credentials do not exist.');
        }
    }

}
