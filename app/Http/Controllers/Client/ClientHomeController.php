<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth, Redirect, View, DB,Mail,Cache;
use Illuminate\Http\RedirectResponse;
use App\Models\ClientHomePage;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientChatMessage;
use App\Mail\ClientUserEmailVerification;
use App\Libraries\InputSanitise;

class ClientHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(is_object($subdomain)){
            view::share('subdomain', $subdomain);
            $client = Client::where('subdomain', $subdomain->subdomain)->first();
            if(is_object($client)){
                view::share('client', $client);
            }
        }
    }

    public function adminHome($subdomainName,Request $request){
        return view('client.home',compact('subdomainName'));
    }

    protected function clientHome($subdomainName,Request $request){

        if('local' == \Config::get('app.env')){
            $onlineClientUrl = 'online.localvchip.com';
        } else {
            $onlineClientUrl = 'online.vchipedu.com';
        }
        if( $onlineClientUrl == $request->getHost()){
            return view('client.online.digitaleducation');

        } else {
            $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();

            if(is_object($subdomain)){
                $loginUser = Auth::guard('clientuser')->user();
                if( is_object($loginUser) && $subdomain->client_id != $loginUser->client_id){
                    if('local' == \Config::get('app.env')){
                        return Redirect::away('http://'.$loginUser->client->subdomain);
                    } else {
                        return Redirect::away('https://'.$loginUser->client->subdomain);
                    }
                }
                $hostName = $subdomain->subdomain;
                $onlineCourses = ClientOnlineCourse::getCurrentCoursesByClient($hostName);

                $defaultCourse = ClientOnlineCourse::where('name', 'How to use course')->first();

                $defaultTest = ClientOnlineCourse::where('name', 'How to use test')->first();

                $onlineTestSubcategories = ClientOnlineTestSubCategory::getCurrentSubCategoriesAssociatedWithQuestion($hostName);

                $testimonials = ClientTestimonial::getClientTestimonials($hostName);

                $clientTeam = ClientTeam::getClientTeam($hostName);

                $clientCustomers = ClientCustomer::getClientCustomer($hostName);

                return view('client.front.home', compact('subdomain', 'defaultCourse', 'defaultTest', 'onlineCourses', 'onlineTestSubcategories', 'testimonials', 'clientTeam', 'clientCustomers', 'subdomainName'));
            } else {
                if('local' == \Config::get('app.env')){
                    return Redirect::away('https://localvchip.com');
                } else {
                    return Redirect::away('https://vchipedu.com/');
                }
            }
        }
    }

    protected function verifyAccount(){
        return view('client.verify_account');
    }

    protected function verifyClientEmail(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $client = InputSanitise::getCurrentClient($request);
            $user = Clientuser::join('clients', 'clients.id', '=', 'clientusers.client_id')
                ->where('clients.subdomain', $client)
                ->where('clientusers.email', $email)->where('clientusers.verified', 0)->select('clientusers.*')->first();

            if(is_object($user)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $user->verified = 0;
                    $user->email_token = str_random(60);
                    $user->save();

                    $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($clientUserEmail);
                    DB::connection('mysql2')->commit();
                    return redirect('/')->with('message', 'Verify your email for your account activation.');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
            return redirect()->back()->withErrors(['Email id does not exist or your account is already verified.']);
        }
        return redirect()->back()->withErrors(['Please enter email id.']);
    }

    protected function sendClientUserSignUpOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::sendOtp($mobile);
    }
    protected function sendClientUserSignInOtp(Request $request){
        $mobile = $request->get('mobile');
        $result = [];
        if(!empty($mobile)){
            $client = Client::where('subdomain', $request->getHost())->first();
            if(is_object($client)){
                $clientUsers = Clientuser::where('phone','=', $mobile)->whereNotNull('phone')->where('client_id', $client->id)->get();
                if(is_object($clientUsers) && $clientUsers->count() > 0){
                    if(1 == $clientUsers->count()){
                        if(is_object($clientUsers[0]) && 0 == $clientUsers[0]->number_verified){
                            $result['status'] = 'error';
                            $result['message'] = 'Your mobile no is not verified.Please login with Email-Id and Password or contact at info@vchiptech.com';
                        } else {
                            $result['status'] = 'success';
                            InputSanitise::sendOtp($mobile);
                        }
                    } else {
                        $unVerifiedCount = 0;
                        $verifiedCount = 0;
                        foreach($clientUsers as $clientUser){
                            if(0 == $clientUser->number_verified){
                                $unVerifiedCount++;
                            } else {
                                $verifiedCount++;
                            }
                        }
                        if(1 == $verifiedCount){
                            $result['status'] = 'success';
                            InputSanitise::sendOtp($mobile);
                        } else {
                            if($verifiedCount > 0){
                                $result['status'] = 'error';
                                $result['message'] = $verifiedCount.' users have this no. and all users are verified this no.so can not login.';
                            } else {
                                $result['status'] = 'error';
                                $result['message'] = 'Your mobile no is not verified.Please login with Email-Id and Password or contact at info@vchiptech.com';
                            }
                        }
                    }
                } else {
                    $result['status'] = 'error';
                    $result['message'] = 'Entered mobile no. does not exists in our records.';
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Please enter mobile no';
        }
        return $result;
    }

}