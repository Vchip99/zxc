<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Models\Client;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB,Mail,Session;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('client.guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        // return view('subdomain.auth.passwords.email');
        return view('client.clientLogin.forgotpassword');
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('clients');
    }

    /**
     *  send password reset mail
     */
    protected function sendPasswordResetLink(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $client = Client::where('email', $email)->first();
            if(!empty($client)){
                $this->sendResetLinkEmail($request);
                return Redirect::to('client/login');
            }
            return Redirect::to('client/login')->withErrors(['email' => 'given email is not exist. please enter correct email.']);
        } else {
            return back()->withErrors(['email' => 'please enter email.']);
        }
    }
}
