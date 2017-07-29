<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Session;

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
        if ($this->guard()->attempt($credentials, $request->has('remember'))) {

            if(is_object(Auth::guard('clientuser')->user())){
                $isValidUser = Clientuser::verifyUserWithSubdomain($request, $this->guard('clientuser')->user()->id);
            } else if( is_object(Auth::guard('client')->user()) && Auth::guard('client')->user()->subdomain != (string) $request->getHost() ){
                $isValidUser = 'false';
            }
            if( 'false' == $isValidUser ){
                $this->guard()->logout();
                Session::flush();
                Session::regenerate();
                return redirect()->back()->withErrors('Given credential doesnot match with subdomain.');
            }
            return $this->sendLoginResponse($request);
        } else if($this->guard()->attempt(['email' => $request->email,'password' => $request->password], $request->has('remember'))){
            $this->guard()->logout();
            Session::flush();
            Session::regenerate();
            return $this->sendFailedLoginResponse($request, 'true');
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
                        'verified' => 1,
                        'admin_approve' => 1,
                    ];
            } else {
                return [
                        'email' => $request->email,
                        'password' => $request->password,
                        'verified' => 1,
                        'client_approve' => 1,
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
        return redirect()->intended($this->redirectPath())->with('message', 'Welcome '. $user->name);
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
                $userNotVerify = User::where('email', $request->email)->where('verified', 0)->first();
                if(is_object($userNotVerify)){
                    $errorMessage = 'Please verify your account and then login';
                    return redirect()->back()->withErrors([$errorMessage, 'verify_email']);
                } else {
                    $adminNotapprove = User::where('email', $request->email)->where('verified', 1)->where('admin_approve', 0)->first();
                    if(is_object($adminNotapprove)){
                        $errorMessage = 'Your account is not approve. you can contact at info@vchiptech.com to approve your account.';
                    }
                    return redirect()->back()->withErrors([$errorMessage]);
                }
            } else {

                $userNotVerify = Clientuser::where('email', $request->email)->where('verified', 0)->first();
                if(is_object($userNotVerify)){
                    $errorMessage = 'Please verify your account and then login';
                    return redirect()->back()->withErrors([$errorMessage, 'verify_email']);
                } else {
                    $adminNotapprove = Clientuser::where('email', $request->email)->where('verified', 1)->where('client_approve', 0)->first();
                    if(is_object($adminNotapprove)){
                        $client = Clientuser::getClientByClientUserEmail($request, $request->email);
                        if(is_object($client)){
                            $errorMessage = 'Your account is not approve. you can contact at '.$client->email.' to approve your account.';
                        }
                    }
                    return redirect()->back()->withErrors([$errorMessage]);
                }
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
