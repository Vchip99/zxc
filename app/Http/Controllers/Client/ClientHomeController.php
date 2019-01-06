<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth, Redirect, View, DB,Mail,Cache,Session;
use Illuminate\Http\RedirectResponse;
use App\Models\ClientHomePage;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\Mentor;
use App\Models\MentorRating;
use App\Models\User;
use App\Models\ClientChatMessage;
use App\Models\ClientGalleryType;
use App\Models\ClientGalleryImage;
use App\Mail\ClientUserEmailVerification;
use App\Mail\UnAuthorisedUser;
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
            $mentorUrl = 'mentor.localvchip.com';
        } else {
            $onlineClientUrl = 'online.vchipedu.com';
            $mentorUrl = 'mentor.vchipedu.com';
        }
        if( $onlineClientUrl == $request->getHost()){
            return view('client.online.digitaleducation');
        } else if( $mentorUrl == $request->getHost()){
            if(is_object(Auth::guard('mentor')->user())){
                return Redirect::to('mentor/profile');
            }
            $mentors = Mentor::all();
            $reviewData = [];
            $ratingUsers = [];
            $userNames = [];
            $allRatings = MentorRating::all();
            if(is_object($allRatings) && false == $allRatings->isEmpty()){
                foreach($allRatings as $rating){
                    $reviewData[$rating->mentor_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                    $ratingUsers[] = $rating->user_id;
                }
                foreach($reviewData as $dataId => $rating){
                    $ratingSum = 0.0;
                    foreach($rating as $userRatings){
                        foreach($userRatings as $userId => $userRating){
                            $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                        }
                        $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                    }
                }
            }
            if(count($ratingUsers) > 0){
                $users = User::find($ratingUsers);
                if(is_object($users) && false == $users->isEmpty()){
                    foreach($users as $user){
                        $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                    }
                }
            }
            return view('mentor.front.home', compact('mentors','reviewData','userNames'));
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
                    return Redirect::away('http://localvchip.com');
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
        return InputSanitise::checkMobileAndSendOpt($request,$mobile);
    }

    protected function sendClientUserSignInOtp(Request $request){
        $mobile = $request->get('mobile');
        $result = [];
        if(!empty($mobile)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $client = Client::where('subdomain', $request->getHost())->first();
                if(is_object($client)){
                    $clientUsers = Clientuser::where('phone','=', $mobile)->whereNotNull('phone')->where('client_id', $client->id)->get();
                    if(is_object($clientUsers) && $clientUsers->count() > 0){
                        if(1 == $clientUsers->count()){
                            if(is_object($clientUsers[0]) && 0 == $clientUsers[0]->number_verified){
                                $result['status'] = 'error';
                                $result['message'] = 'Your mobile no is not verified.Please login with Email-Id and Password or contact at info@vchiptech.com';
                                $data['phone'] = $mobile;
                                $data['client'] = $client->name;

                                // send mail to info@vchiptech.com
                                Mail::to('info@vchiptech.com')->send(new UnAuthorisedUser($data));
                            } else {
                                $result['status'] = 'success';
                                $result['message'] = InputSanitise::sendOtp($mobile);
                                InputSanitise::setSmsCountStats($client,4);
                                $client->save();
                                DB::connection('mysql2')->commit();
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
                                $result['message'] = InputSanitise::sendOtp($mobile);
                                InputSanitise::setSmsCountStats($client,4);
                                $client->save();
                                DB::connection('mysql2')->commit();
                            } else {
                                if($verifiedCount > 0){
                                    $result['status'] = 'error';
                                    $result['message'] = $verifiedCount.' users have this no. and all users are verified this no.so can not login.';
                                } else {
                                    $result['status'] = 'error';
                                    $result['message'] = 'Your mobile no is not verified.Please login with Email-Id and Password or contact at info@vchiptech.com';
                                    $data['phone'] = $mobile;
                                    $data['client'] = $client->name;

                                    // send mail to info@vchiptech.com
                                    Mail::to('info@vchiptech.com')->send(new UnAuthorisedUser($data));
                                }
                            }
                        }
                    } else {
                        $result['status'] = 'error';
                        $result['message'] = 'Entered mobile no. does not exists in our records.';
                    }
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Please enter mobile no';
        }
        return $result;
    }

    protected function parentLogin(){
        return view('client.front.parentLogin');
    }

    protected function sendClientUserParentSignInOtp(Request $request){
        $mobile = $request->get('mobile');
        $result = [];
        if(!empty($mobile)){
            $client = Client::where('subdomain', $request->getHost())->first();
            if(is_object($client)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $parent = Clientuser::where('parent_phone','=', $mobile)->whereNotNull('parent_phone')->where('client_id', $client->id)->first();
                    if(is_object($parent)){
                        $result['status'] = 'success';
                        $result['message'] = InputSanitise::sendOtp($mobile);
                        InputSanitise::setSmsCountStats($client,4);
                        $client->save();
                        DB::connection('mysql2')->commit();
                    } else {
                        $result['status'] = 'error';
                        $result['message'] = 'Entered mobile no. does not exists in our records.';
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Please enter mobile no';
        }
        return $result;
    }

    protected function loginParent(Request $request){
        $userMobile = $request->get('mobile');
        $loginOtp = $request->get('login_otp');
        if(!empty($request->route()->getParameter('client')) && !empty($userMobile) && !empty($loginOtp)){
            $serverOtp = Cache::get($userMobile);
            if($loginOtp == $serverOtp){
                $client = Client::where('subdomain', $request->getHost())->first();
                $cluentUser = Clientuser::where('parent_phone','=', $userMobile)->whereNotNull('parent_phone')->where('client_id', $client->id)->where('client_approve', 1)->first();
                if(!is_object($cluentUser)){
                    return Redirect::to('/')->withErrors('User does not exists or not client approve.');
                }
                Auth::guard('clientuser')->login($cluentUser);
                if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                    Session::put('parent_'.$userMobile, $userMobile);
                }
                return Redirect::to('profile')->with('message', 'Welcome '. $cluentUser->parent_name);
            } else {
                return redirect()->back()->withErrors('Entered otp is wrong.');
            }
        }
    }

    protected function gallery($subdomainName,Request $request){
        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(!is_object($subdomain)){
            return Request::to('/');
        }
        $galleryImages = [];
        $clientGalleryTypes = [];
        $clientGalleryImages = ClientGalleryImage::where('client_id', $subdomain->client_id)->get();
        if(is_object($clientGalleryImages) && false == $clientGalleryImages->isEmpty()){
            foreach($clientGalleryImages as $clientGalleryImage){
                $galleryImages[$clientGalleryImage->client_gallery_type_id] = $clientGalleryImage->images;
            }
            if(count($galleryImages) > 0){
                $galleryTypeIds = array_keys($galleryImages);
                $clientGalleryTypes = ClientGalleryType::find(array_unique($galleryTypeIds));
            }
        }
        return view('client.galleryImage.gallery', compact('galleryImages','clientGalleryTypes','subdomainName'));
    }
}