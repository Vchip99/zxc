<?php

namespace App\Http\Controllers\ClientuserAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientUnAuthorisedUser;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Clientuser;
use Session,Cache,Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('clientuser.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('clientuser.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('clientuser');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout($subdomainName,Request $request)
    {
        Cache::forget($subdomainName.':online_user-' . $this->guard('clientuser')->user()->id);
        $this->guard()->logout();

        Session::flush();

        Session::regenerate();

        return redirect('/');
    }

    public function clientUserLogin(Request $request){
        if($this->guard('clientuser')->attempt($this->credentials($request))){
            // if free plan and user is not in first 10 user then dont allow to login
            $clientUser = Auth::guard('clientuser')->user();
            if(1 == $clientUser->client->plan_id){
                if( 'false' == $clientUser::isInBetweenFirstTen()){
                    $data['name'] = $clientUser->name;
                    $data['email'] = $clientUser->email;
                    $data['client'] = $clientUser->client->name;
                    // send mail to client
                    Mail::to($clientUser->client->email)->send(new ClientUnAuthorisedUser($data));

                    $this->guard('clientuser')->logout();
                    Session::flush();
                    Session::regenerate();
                    return 'Try after some time.';
                }
            }
            $request->session()->regenerate();
            return 'true';
        } else {
            return 'false';
        }
    }
}
