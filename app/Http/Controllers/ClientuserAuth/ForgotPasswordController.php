<?php

namespace App\Http\Controllers\ClientuserAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Models\ClientHomePage;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB,Mail;
use App\Models\Clientuser;
use App\Models\Client;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    protected $connection = 'mysql2';
    protected $broker = 'clientusers';

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
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm(Request $request)
    {
        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(is_object($subdomain)){
            view::share('subdomain', $subdomain);
            $client = Client::where('subdomain', $subdomain->subdomain)->first();
            if(is_object($client)){
                view::share('client', $client);
            }
        } else {
            return Redirect::to('/');
        }
        return view('clientuser.auth.passwords.email');
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('clientusers');
    }

    /**
     *  send password reset mail
     */
    protected function sendPasswordResetLink(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $user = Clientuser::where('email', $email)->first();
            if(!empty($user)){

                $this->sendResetLinkEmail($request);
                return Redirect::to('/');
            }
            return Redirect::to('/')->withErrors(['email' => 'given email is not exist. please enter correct email.']);
        }
    }
}
