<?php

namespace App\Http\Controllers\UserAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Session,Redirect,Cache;
use Illuminate\Http\Request;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        // return view('auth.login');
        return view('layouts.home');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return \Auth::guard('web');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Cache::forget('vchip:online_user-' . $this->guard('user')->user()->id);
        if('ceo@vchiptech.com' == $this->guard('user')->user()->email){
            Cache::forget('vchip:chatAdminLive');
        }
        $this->guard()->logout();

        Session::flush();

        Session::regenerate();

        return redirect('/home');
    }

    public function userLogin(Request $request){
        if($this->guard('user')->attempt($this->credentials($request))){
            if('ceo@vchiptech.com' == $this->guard('user')->user()->email){
                Cache::put('vchip:chatAdminLive', true, 60);
            }
            $request->session()->regenerate();
            return 'true';
        } else {
            return 'false';
        }
    }

}
