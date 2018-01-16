<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Auth,Hash,DB, Redirect,Session,Validator,Input, Url;
use App\Libraries\InputSanitise;
use App\Mail\SocialiteUser;
use App\Mail\NewRegisteration;
use Socialite;

class SocialiteController extends Controller
{


	/**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider(Request $request, $provider)
    {
    	Session::flash('previousUrl',$request->server('HTTP_REFERER'));
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {

    	try
        {
        	$user = Socialite::driver($provider)->user();
        }
        catch (Exception $e) {
            return Redirect::to('/');
        }

        try
        {
        	$authUser = $this->findOrCreateUser($user, $provider);
        }
        catch (Exception $e) {
        	DB::rollback();
            return Redirect::to('/');
        }

        Auth::login($authUser);
        if(Session::has('previousUrl')){
        	return Redirect::to(Session::get('previousUrl'))->with('message', 'Welcome '. $authUser->name);
        } else {
        	return back()->with('message', 'Welcome '. $authUser->name);
        }
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
    	$authUser = User::where('email', $user->email)->first();
    	if(is_object($authUser)){
    		if( 'facebook' == $provider && empty($authUser->facebook_provider_id)){
    			$authUser->facebook_provider_id = $user->id;
    			$authUser->save();
    			DB::commit();
    		} else if( 'google' == $provider && empty($authUser->google_provider_id)){
    			$authUser->google_provider_id = $user->id;
    			$authUser->save();
    			DB::commit();
    		}
    		return $authUser;
    	} else {
    		if( 'facebook' == $provider){
    			$facebookProviderId = $user->id;
    			$googleProviderId = '';
    		} else {
    			$facebookProviderId = '';
    			$googleProviderId = $user->id;
    		}
    		$data = [];
	        $passwordStr= str_random(10);
        	$authUser = User::create([
                'name'     => $user->name,
	            'email'    => $user->email,
	            'phone'	   => '1234567890',
                'password' => bcrypt($passwordStr),
                'user_type' => 2,
                'admin_approve' => 1,
                'verified' => 1,
                'degree' => 1,
                'college_id' => 'other',
                'college_dept_id' => '',
                'year' => '',
                'roll_no' => '',
                'other_source' => $provider,
                'email_token' => '',
                'google_provider_id' => $googleProviderId,
                'facebook_provider_id' => $facebookProviderId
            ]);
            DB::commit();
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['password'] = $passwordStr;
            $data['url'] = url('/');
            $data['other_source'] = $provider;
            $data['degree'] = 'Engineering';
            $data['college'] = '';
            $data['department'] = '';
            $data['year'] = '';
            $data['roll_no'] = '';
            $data['user_type'] = 'Student';

            // send mail to user after new registration
            Mail::to($user->email)->send(new SocialiteUser($data));
            // send mail to admin after new registration
            Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
            return $authUser;
    	}
    }
}