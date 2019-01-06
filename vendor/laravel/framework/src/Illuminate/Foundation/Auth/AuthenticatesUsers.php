<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientUnAuthorisedUser;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\User;
use App\Models\Mentor;
use App\Models\ClientLoginActivity;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Session,Cache,Redirect;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // login using mobile
        $userMobile = $request->get('mobile');
        $loginOtp = $request->get('login_otp');
        if(!empty($request->route()->getParameter('client')) && !empty($userMobile) && !empty($loginOtp)){
            $serverOtp = Cache::get($userMobile);
            if($loginOtp == $serverOtp){
                // mentor login
                if($request->is('mentor/login')){
                    $mentor = Mentor::where('mobile','=', $userMobile)->whereNotNull('mobile')->where('admin_approve', 1)->first();
                    if(!is_object($mentor)){
                        return Redirect::to('/')->withErrors('Mentor does not exists or not admin approve.');
                    }
                    Auth::guard('mentor')->login($mentor);
                    return Redirect::to('mentor/profile')->with('message', 'Welcome '. $mentor->name);
                } else {
                    // client user login
                    $client = Client::where('subdomain', $request->getHost())->first();
                    $clientUser = Clientuser::where('number_verified', 1)->where('phone','=', $userMobile)->whereNotNull('phone')->where('client_id', $client->id)->where('client_approve', 1)->first();
                    if(!is_object($clientUser)){
                        return Redirect::to('/')->withErrors('User does not exists or not client approve.');
                    }
                    Auth::guard('clientuser')->login($clientUser);
                    if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                        Cache::forget($userMobile);
                        Cache::forget('mobile-'.$userMobile);
                    }
                    return redirect()->back()->with('message', 'Welcome '. $clientUser->name);
                }
            } else {
                return redirect()->back()->withErrors('Entered otp is wrong.');
            }
        } else if(empty($request->route()->getParameter('client')) && !empty($userMobile) && !empty($loginOtp)){
            $serverOtp = Cache::get($userMobile);
            // vchip user login
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
                    if($this->guard('user')->user()->college_id > 0){
                        if(1 == $request->get('signin_using_college') && !empty($request->get('college_url')) && $request->get('college_id') != $this->guard('user')->user()->college_id){
                            $userCollege = $this->guard('user')->user()->college;
                            $this->guard('user')->logout();
                            Session::flush();
                            Session::regenerate();

                            return redirect('college/'.$request->get('college_url'))->withErrors(['You are belong to '.$userCollege->name.' college and your college url is <a href="'.url('college/'.$userCollege->url).'">'.url('college/'.$userCollege->url).'</a>. Please login using given url']);
                        }
                        $collegeUrl = $this->guard('user')->user()->college->url;
                        if(empty($collegeUrl)){
                            $collegeUrl = 'vchipedu';
                        }
                    } else {
                        if(1 == $request->get('signin_using_college') && !empty($request->get('college_url')) && $request->get('college_id') != $this->guard('user')->user()->college_id){
                            $userCollege = $this->guard('user')->user()->college;
                            $this->guard('user')->logout();
                            Session::flush();
                            Session::regenerate();

                            return redirect('college/'.$request->get('college_url'))->withErrors(['You are belong to Other college and your college url is <a href="'.url('college/other').'">'.url('college/other').'</a>. Please login using given url']);
                        }
                        $collegeUrl = 'other';
                    }
                    Session::put('college_user_url',$collegeUrl);
                    if(1 == $request->get('signin_using_college') && !empty($request->get('college_url'))){
                        $collegeUrlStr = 'college/'.$collegeUrl.'/profile';
                        return redirect($collegeUrlStr)->with('message', 'Welcome '. $user->name);
                    } else {
                        return redirect()->back()->with('message', 'Welcome '. $user->name);
                    }
                }
                return redirect()->back()->with('message', 'Welcome '. $user->name);
            } else {
                return redirect()->back()->withErrors('Entered otp is wrong.');
            }
        }
        // login using email
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $credentials = $this->credentials($request);
        $isValidUser = 'true';

        if(empty($request->route()->getParameter('client'))){
            // admin login
            if ($request->is('admin/login')) {
                if (!$this->guard('admin')->attempt($credentials, $request->has('remember'))) {
                    $isValidUser = 'false';
                }
            } else {
                // vhip user login
                if (!$this->guard('user')->attempt($credentials, $request->has('remember'))) {
                    if (! $lockedOut) {
                        $this->incrementLoginAttempts($request);
                    }
                    return $this->sendFailedLoginResponse($request, 'true');
                }
            }
            if( 'true' == $isValidUser ){
                if(is_object($this->guard('user')->user())){
                    if($this->guard('user')->user()->college_id > 0){
                        if(1 == $request->get('signin_using_college') && !empty($request->get('college_url')) && $request->get('college_id') != $this->guard('user')->user()->college_id){
                            $userCollege = $this->guard('user')->user()->college;
                            $this->guard('user')->logout();
                            Session::flush();
                            Session::regenerate();

                            return redirect('college/'.$request->get('college_url'))->withErrors(['You are belong to '.$userCollege->name.' college and your college url is <a href="'.url('college/'.$userCollege->url).'">'.url('college/'.$userCollege->url).'</a>. Please login using given url']);
                        }
                        $collegeUrl = $this->guard('user')->user()->college->url;
                        if(empty($collegeUrl)){
                            $collegeUrl = 'vchipedu';
                        }
                    } else {
                        if(1 == $request->get('signin_using_college') && !empty($request->get('college_url')) && $request->get('college_id') != $this->guard('user')->user()->college_id){
                            $userCollege = $this->guard('user')->user()->college;
                            $this->guard('user')->logout();
                            Session::flush();
                            Session::regenerate();

                            return redirect('college/'.$request->get('college_url'))->withErrors(['You are belong to Other college and your college url is <a href="'.url('college/other').'">'.url('college/other').'</a>. Please login using given url']);
                        }
                        $collegeUrl = 'other';
                    }
                    Session::put('college_user_url',$collegeUrl);
                }
                return $this->sendLoginResponse($request);
            }
        } else {
            // client login
            if($request->is('client/login')){
                if($this->guard('client')->attempt($credentials, $request->has('remember'))) {
                    $sessionId = str_random(10);
                    if(!Session::has('client_session_id')){
                        Session::put('client_session_id',$sessionId);
                    }
                    ClientLoginActivity::addOrUpdateClientLoginActivity($sessionId, false);
                    return $this->sendLoginResponse($request);
                } else {
                    if (! $lockedOut) {
                        $this->incrementLoginAttempts($request);
                    }
                    return redirect()->back()->withErrors('Given credential doesnot match with subdomain.');
                }
            } else {
                // mentor login
                if('mentor' == $request->route()->getParameter('client') && $request->is('mentor/login') && $this->guard('mentor')->attempt($credentials)){
                    // mentor login
                    $mentor = Auth::guard('mentor')->user();
                    if(0 == $mentor->admin_approve){
                        $this->guard('mentor')->logout();
                        Session::flush();
                        Session::regenerate();
                        if (! $lockedOut) {
                            $this->incrementLoginAttempts($request);
                        }
                        $errorMessage = 'Your account is not approve. you can contact at info@vchiptech.com to approve your account.';
                        return redirect()->back()->withErrors([$errorMessage]);
                    } elseif(0 == $mentor->verified){
                        $this->guard('mentor')->logout();
                        Session::flush();
                        Session::regenerate();
                        if (! $lockedOut) {
                            $this->incrementLoginAttempts($request);
                        }
                        $errorMessage = 'Please verify your account and then login';
                        return redirect()->back()->withErrors([$errorMessage]);
                    }
                    return $this->sendLoginResponse($request);
                } else if($this->guard('clientuser')->attempt($credentials, $request->has('remember'))) {
                    // client user login
                    $clientUser = Auth::guard('clientuser')->user();
                    if(0 == $clientUser->client_approve){
                        $this->guard('clientuser')->logout();
                        Session::flush();
                        Session::regenerate();
                        if (! $lockedOut) {
                            $this->incrementLoginAttempts($request);
                        }
                        return $this->sendFailedLoginResponse($request, 'true');
                    } elseif(0 == $clientUser->verified){
                        if(0 == $clientUser->verified){
                            if(1 == $clientUser->client->allow_non_verified_email){
                                return $this->sendLoginResponse($request);
                            }
                        }
                        $this->guard('clientuser')->logout();
                        Session::flush();
                        Session::regenerate();
                        if (! $lockedOut) {
                            $this->incrementLoginAttempts($request);
                        }
                        return $this->sendFailedLoginResponse($request, 'true');
                    } else {
                        // if free plan and user is not in first 10 user then dont allow to login
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
                                if (! $lockedOut) {
                                    $this->incrementLoginAttempts($request);
                                }
                                return redirect()->back()->withErrors(['Try after some time.']);
                            }
                        }
                    }
                    return $this->sendLoginResponse($request);
                } else {
                    if (! $lockedOut) {
                        $this->incrementLoginAttempts($request);
                    }
                    return redirect()->back()->withErrors('Given credential doesnot match with subdomain.');
                }
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if (! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if(empty($request->route()->getParameter('client'))){
            if ($request->is('admin/login')) {
                return [
                    'email' => $request->email,
                    'password' => $request->password
                ];
            } else {
                return [
                    'email' => $request->email,
                    'password' => $request->password,
                    'verified' => 1,
                    'admin_approve' => 1,
                ];
            }
        } else {
            if($request->is('client/login')){
                return [
                        'email' => $request->email,
                        'password' => $request->password,
                        'admin_approve' => 1,
                        'subdomain' => (string) $request->getHost(),
                    ];
            } else if($request->is('mentor/login')){
                return [
                    'email' => $request->email,
                    'password' => $request->password
                ];
            } else {
                $client = Client::where('subdomain', $request->getHost())->first();
                return [
                        'email' => $request->email,
                        'password' => $request->password,
                        'client_id' => $client->id,
                    ];
            }
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if('ceo@vchiptech.com' == $user->email){
            Cache::put('vchip:chatAdminLive', true, 60);
        }
        if(1 == $request->get('signin_using_college') && !empty($request->get('college_url'))){
            if(!Session::has('college_user_url')){
                Session::put('college_user_url',$request->get('college_url'));
            }
            $collegeUrl = 'college/'.$request->get('college_url').'/profile';
            return redirect($collegeUrl)->with('message', 'Welcome '. $user->name);
        } else {
            return redirect()->intended($this->redirectPath())->with('message', 'Welcome '. $user->name);
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request, $notVirified = false)
    {
        if( 'true' == $notVirified ){
            $errorMessage = Lang::get('auth.failed');
            if(empty($request->route()->getParameter('client'))){
                $adminNotapprove = User::where('email', $request->email)->where('admin_approve', 0)->first();
                if(is_object($adminNotapprove)){
                    $errorMessage = 'Your account is not approve. you can contact at info@vchiptech.com to approve your account.';
                } else {
                    $userNotVerify = User::where('email', $request->email)->where('verified', 0)->where('admin_approve', 1)->first();
                    if(is_object($userNotVerify)){
                        $errorMessage = 'Please verify your account and then login';
                        return redirect()->back()->withErrors([$errorMessage, 'verify_email']);
                    }
                }
                return redirect()->back()->withErrors([$errorMessage]);
            } else {
                $client = Client::where('subdomain', $request->getHost())->first();
                $adminNotapprove = Clientuser::where('email', $request->email)->where('client_id', $client->id)->where('client_approve', 0)->first();
                if(is_object($adminNotapprove)){
                    if(is_object($client)){
                        $errorMessage = 'Your account is not approve. you can contact at '.$client->email.' to approve your account.';
                        return redirect()->back()->withErrors([$errorMessage]);
                    }
                } else {
                    $userNotVerify = Clientuser::where('email', $request->email)->where('client_id', $client->id)->where('verified', 0)->where('client_approve', 1)->first();
                    if(is_object($userNotVerify)){
                        $errorMessage = 'Please verify your account and then login';
                        return redirect()->back()->withErrors([$errorMessage, 'verify_email']);
                    }
                }
                return redirect()->back()->withErrors([$errorMessage]);
            }
        } else {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => Lang::get('auth.failed'),
                ]);
        }
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
