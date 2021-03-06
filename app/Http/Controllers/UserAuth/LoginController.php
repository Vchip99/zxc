<?php

namespace App\Http\Controllers\UserAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Session,Redirect,Cache;
use App\Models\User;
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
        // login using mobile
        $userMobile = $request->get('mobile');
        $loginOtp = $request->get('login_otp');
        $email = $request->get('email');
        $password = $request->get('password');
        $serverOtp = Cache::get($userMobile);
        if($userMobile && $loginOtp && !$email && !$password){
            if($loginOtp == $serverOtp){
                $user = User::where('number_verified', 1)->whereNotNull('phone')->where('phone','=', $userMobile)->where('admin_approve', 1)->first();
                if(!is_object($user)){
                    return Redirect::to('/')->withErrors('User does not exists or not admin approve.');
                }
                $this->guard('user')->login($user);
                if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                if(is_object($this->guard('user')->user())){
                    if('ceo@vchiptech.com' == $this->guard('user')->user()->email){
                        Cache::put('vchip:chatAdminLive', true, 60);
                    }
                    $request->session()->regenerate();
                    if($this->guard('user')->user()->college_id > 0){
                        $collegeUrl = $this->guard('user')->user()->college->url;
                        if(empty($collegeUrl)){
                            $collegeUrl = 'vchipedu';
                        }
                    } else {
                        $collegeUrl = 'other';
                    }
                    Session::put('college_user_url',$collegeUrl);
                    return 'true';
                }
            } else {
                return 'false';
            }
        } else {
            if($this->guard('user')->attempt($this->credentials($request))){
                if('ceo@vchiptech.com' == $this->guard('user')->user()->email){
                    Cache::put('vchip:chatAdminLive', true, 60);
                }
                $request->session()->regenerate();
                if($this->guard('user')->user()->college_id > 0){
                    $collegeUrl = $this->guard('user')->user()->college->url;
                } else {
                    $collegeUrl = 'other';
                }
                Session::put('college_user_url',$collegeUrl);
                return 'true';
            } else {
                return 'false';
            }
        }
    }

}
