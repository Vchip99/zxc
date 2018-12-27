<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Client;
use App\Models\Clientuser;
use Auth,Hash,DB, Redirect,Session,Validator,Input, Url;
use App\Libraries\InputSanitise;
use App\Mail\SocialiteUser;
use App\Mail\NewRegisteration;
use App\Mail\NewClientUserRegistration;
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
    	Session::flash('domainUrl',$request->server('HTTP_REFERER'));

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function subdomainRedirectToProvider(Request $request, $subdomain=NULL,$provider)
    {
        Session::put('subdomainReferer', $request->server('HTTP_REFERER'));
        Session::put('subdomainUrl', $request->server('HTTP_HOST'));
        Session::put('mentorClient', $request->route()->getParameter('client'));
        Session::save();
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
    public function handleProviderCallback(Request $request, $provider)
    {
        $state = $request->get('state');
        $request->session()->put('state',$state);
        if(true == Session::has('domainUrl')){
            $domainUrl = Session::get('domainUrl');
        } else {
            $domainUrl = '';
        }
        if(true == Session::has('subdomainUrl')){
            $subdomainUrl = Session::get('subdomainUrl');
        } else {
            $subdomainUrl = '';
        }
        if(true == Session::has('subdomainReferer')){
            $subdomainReferer = Session::get('subdomainReferer');
        } else {
            $subdomainReferer = '';
        }

    	try
        {
        	$user = Socialite::driver($provider)->user();

        }
        catch (Exception $e) {
            if(!empty($domainUrl) && empty($subdomainUrl) && empty($subdomainReferer)){
                Session::remove('domainUrl');
                if(true == Session::has('subdomainUrl')){
                    Session::remove('subdomainUrl');
                }
                if(true == Session::has('subdomainReferer')){
                    Session::remove('subdomainReferer');
                }
                return Redirect::to($domainUrl);
            } else if(!empty($subdomainUrl) && !empty($subdomainReferer) && empty($domainUrl)){
                Session::remove('subdomainUrl');
                Session::remove('subdomainReferer');
                if(true == Session::has('domainUrl')){
                    Session::remove('domainUrl');
                }

                return Redirect::to($subdomainReferer);
            } else {
                return Redirect::to('/');
            }
        }

        try
        {
            if(is_object($user)){
        	   $authUser = $this->findOrCreateUser($user, $provider);
            } else {
                $authUser = '';
            }
        }
        catch (Exception $e) {
            if(!empty($domainUrl) && empty($subdomainUrl) && empty($subdomainReferer)){
                Session::remove('domainUrl');
                if(true == Session::has('subdomainUrl')){
                    Session::remove('subdomainUrl');
                }
                if(true == Session::has('subdomainReferer')){
                    Session::remove('subdomainReferer');
                }
                return Redirect::to($domainUrl);
            } else if(!empty($subdomainUrl) && !empty($subdomainReferer) && empty($domainUrl)){
                Session::remove('subdomainUrl');
                Session::remove('subdomainReferer');
                if(true == Session::has('domainUrl')){
                    Session::remove('domainUrl');
                }

                return Redirect::to($subdomainReferer);
            } else {
                return Redirect::to('/');
            }
        }
        if((!empty($domainUrl) && empty($subdomainUrl) && empty($subdomainReferer)) || (Session::has('mentorClient') && 'mentor' == Session::get('mentorClient'))){
            if(is_object($authUser)){
                if( 0 == $authUser->admin_approve ){
                    return Redirect::to($domainUrl)->withErrors('Your account is not approve. you can contact at info@vchiptech.com to approve your account.');
                } else if( 0 == $authUser->verified ){
                    return Redirect::to($domainUrl)->withErrors('Your account is not verified. please verify your account.');
                } else {
                    if( 1 == $authUser->admin_approve && 1 == $authUser->verified){
                        Auth::login($authUser);
                        if($authUser->college_id > 0){
                            $collegeUrl = $authUser->college->url;
                        } else {
                            $collegeUrl = 'other';
                        }
                        Session::put('college_user_url',$collegeUrl);
                        Session::remove('domainUrl');
                        if(true == Session::has('subdomainUrl')){
                            Session::remove('subdomainUrl');
                        }
                        if(true == Session::has('subdomainReferer')){
                            Session::remove('subdomainReferer');
                        }

                        if(Session::has('mentorClient') && 'mentor' == Session::get('mentorClient')){
                            return Redirect::to($subdomainReferer)->with('message', 'Welcome '. $authUser->name);
                        } else {
                            return Redirect::to($domainUrl)->with('message', 'Welcome '. $authUser->name);
                        }
                    }
                }
            } else {
                if(true == Session::has('subdomainUrl')){
                    Session::remove('subdomainUrl');
                }
                if(true == Session::has('subdomainReferer')){
                    Session::remove('subdomainReferer');
                }
                if(true == Session::has('domainUrl')){
                    Session::remove('domainUrl');
                }
                return Redirect::to('/');
            }
        } else {
            if(is_object($authUser)){
                if( 0 == $authUser->client_approve ){
                    return Redirect::to($subdomainReferer)->withErrors('Your account is not approve. you can contact at '.$authUser->client->email.' to approve your account.');
                } else if( 0 == $authUser->verified ){
                    return Redirect::to($subdomainReferer)->withErrors('Your account is not verified. please verify your account.');
                } else {
                    if( 1 == $authUser->client_approve && 1 == $authUser->verified){
                        Auth::guard('clientuser')->login($authUser);
                        Session::remove('subdomainUrl');
                        Session::remove('subdomainReferer');
                        if(true == Session::has('domainUrl')){
                            Session::remove('domainUrl');
                        }
                        return Redirect::to($subdomainReferer)->with('message', 'Welcome '. $authUser->name);
                    }
                }
            } else {
                if(true == Session::has('subdomainUrl')){
                    Session::remove('subdomainUrl');
                }
                if(true == Session::has('subdomainReferer')){
                    Session::remove('subdomainReferer');
                }
                if(true == Session::has('domainUrl')){
                    Session::remove('domainUrl');
                }
                return Redirect::to($subdomainReferer);
            }
        }
        return Redirect::to('/');
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
        if((true == Session::has('domainUrl') && false == Session::has('subdomainUrl') && false == Session::has('subdomainReferer')) || (Session::has('mentorClient') && 'mentor' == Session::get('mentorClient'))){
        	$authUser = User::where('email', $user->email)->first();
            DB::beginTransaction();
            try
            {
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
                    if(empty($authUser->photo)){
                        $authUser->photo = $user->avatar_original;
                        $authUser->save();
                        DB::commit();
                    }
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
                        'photo'    => $user->avatar_original,
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
                    $data['domain'] = 'Vchipedu';
                    // send mail to user after new registration
                    Mail::to($user->email)->send(new SocialiteUser($data));
                    // send mail to admin after new registration
                    Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
                }
                return $authUser;
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'false';
            }
        } else if(true == Session::has('subdomainUrl') && true == Session::has('subdomainReferer') && false == Session::has('domainUrl')){
            $subdomain = Session::get('subdomainUrl');
            if($subdomain){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $client = Client::where('subdomain', $subdomain)->first();
                    if(is_object($client)){
                        $authUser = Clientuser::where('email', $user->email)->where('client_id', $client->id)->first();
                        if(is_object($authUser)){
                            if( 'facebook' == $provider && empty($authUser->facebook_provider_id)){
                                $authUser->facebook_provider_id = $user->id;
                                $authUser->save();
                                DB::connection('mysql2')->commit();
                            } else if( 'google' == $provider && empty($authUser->google_provider_id)){
                                $authUser->google_provider_id = $user->id;
                                $authUser->save();
                                DB::connection('mysql2')->commit();
                            }
                            if(empty($authUser->photo)){
                                $authUser->photo = $user->avatar_original;
                                $authUser->save();
                                DB::connection('mysql2')->commit();
                            }
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
                            $authUser = Clientuser::create([
                                'name'     => $user->name,
                                'email'    => $user->email,
                                'phone'    => '1234567890',
                                'password' => bcrypt($passwordStr),
                                'client_id' => $client->id,
                                'verified' => 1,
                                'client_approve' => 1,
                                'photo'    => $user->avatar_original,
                                'google_provider_id' => $googleProviderId,
                                'facebook_provider_id' => $facebookProviderId
                            ]);
                            DB::connection('mysql2')->commit();
                            $data['name'] = $user->name;
                            $data['email'] = $user->email;
                            $data['password'] = $passwordStr;
                            $data['url'] = Session::get('subdomainUrl');
                            $data['domain'] = explode('.', $subdomain)[0];
                            // send mail to user after new registration
                            Mail::to($user->email)->send(new SocialiteUser($data));
                            // send mail to client after new registration
                            $data = [
                                'name' => $user->name,
                                'email' => $user->email,
                            ];
                            Mail::to($client->email)->send(new NewClientUserRegistration($data));
                            // return $authUser;
                        }
                        return $authUser;
                    }
                    return 'false';
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return 'false';
                }
            }
            return 'false';
        }
        return 'false';
    }
}