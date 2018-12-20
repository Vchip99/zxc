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
use App\Models\ClientScore;
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
        // login using mobile
        $userMobile = $request->get('mobile');
        $loginOtp = $request->get('login_otp');
        $email = $request->get('email');
        $password = $request->get('password');
        $serverOtp = Cache::get($userMobile);

        $category = $request->get('category');
        $subcategory = $request->get('subcategory');
        $subject = $request->get('subject');
        $paper = $request->get('paper');
        if($userMobile && $loginOtp && !$email && !$password){
            if($loginOtp == $serverOtp){
                $client = Client::where('subdomain', $request->getHost())->first();
                $clientUser = Clientuser::where('number_verified', 1)->where('phone','=', $userMobile)->whereNotNull('phone')->where('client_id', $client->id)->where('client_approve', 1)->first();
                if(!is_object($clientUser)){
                    return Redirect::to('/')->withErrors('User does not exists or not client approve.');
                }
                Auth::guard('clientuser')->login($clientUser);
                if(1 == $client->plan_id){
                    if( 'false' == $clientUser::isInBetweenFirstTen()){
                        $data['name'] = $clientUser->name;
                        $data['email'] = $clientUser->email;
                        $data['client'] = $client->name;
                        // send mail to client
                        Mail::to($client->email)->send(new ClientUnAuthorisedUser($data));

                        $this->guard('clientuser')->logout();
                        Session::flush();
                        Session::regenerate();
                        return 'Try after some time.';
                    }
                }
                if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                if($category > 0 && $subcategory > 0 && $subject > 0 && $paper > 0){
                    $userScore = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($category,$subcategory,$paper,$subject,$clientUser->id);
                    if(is_object($userScore)){
                        return 'testGiven';
                    }
                    return 'startExam';
                }
                return 'true';
            } else {
                return 'Entered wrong otp';
            }
        } else {
            if($this->guard('clientuser')->attempt($this->credentials($request))){
                // if free plan and user is not in first 10 user then dont allow to login
                $clientUser = Auth::guard('clientuser')->user();
                $client = $clientUser->client;
                if(1 == $client->plan_id){
                    if( 'false' == $clientUser::isInBetweenFirstTen()){
                        $data['name'] = $clientUser->name;
                        $data['email'] = $clientUser->email;
                        $data['client'] = $client->name;
                        // send mail to client
                        Mail::to($client->email)->send(new ClientUnAuthorisedUser($data));

                        $this->guard('clientuser')->logout();
                        Session::flush();
                        Session::regenerate();
                        return 'Try after some time.';
                    }
                }
                if($category > 0 && $subcategory > 0 && $subject > 0 && $paper > 0){
                    $userScore = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($category,$subcategory,$paper,$subject,$clientUser->id);
                    if(is_object($userScore)){
                        return 'testGiven';
                    }
                    return 'startExam';
                }
                return 'true';
            } else {
                return 'false';
            }
        }
    }
}
