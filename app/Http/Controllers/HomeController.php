<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailToAdmin;
use App\Mail\ContactUs;
use App\Models\Category;
use App\Models\TestCategory;
use App\Models\Client;
use App\Models\User;
use App\Models\SubscriedUser;
use App\Models\College;
use App\Models\CollegeDept;
use App\Models\Designation;
use App\Models\ZeroToHero;
use App\Models\Area;
use App\Models\Notification;
use App\Models\ReadNotification;
use View,DB,Session,Redirect, Auth,Validator,Cache,URL;
use App\Mail\EmailVerification;
use App\Mail\NewRegisteration;
use App\Mail\SubscribedUserVerification;
use App\Mail\WelcomeClient;
use App\Mail\PaymentGatewayErrors;
use App\Mail\VirtualPlacementQuery;
use App\Libraries\InputSanitise;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\Plan;
use App\Models\ClientPlan;
use App\Models\Payment;
use App\Models\InstamojoDetail;
use App\Models\UserBasedAuthentication;
use App\Models\VirtualPlacementDrive;
use App\Models\Add;
use App\Models\AdvertisementPage;
use DateTime;
use App\Models\AdvertisementPayment;
use App\Models\WebdevelopmentPayment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the signup.
     *
     * @return \Illuminate\Http\Response
     */
    public function signup(){
        $colleges = College::all();
        return view('header.signup', compact('colleges'));
    }

    public function getDepartments(Request $request){
        $collegeId = $request->get('college');
        return CollegeDept::where('college_id', $collegeId)->get();
    }

    public function virtualplacementdrive(){
        $virtualplacementdrive = Cache::remember('vchip:virtualplacementdrive',60, function() {
            return VirtualPlacementDrive::first();
        });
        return view('virtualPlacementDrive.virtualplacementdrive', compact('virtualplacementdrive'));
    }

    protected function virtualplacementquery(Request $request){
        // send mail to admin
        Mail::to('vchipdesigng8@gmail.com')->send(new VirtualPlacementQuery($request->all()));
        return redirect()->back()->with('message', 'Mail sent successfully. we will reply asap.');
    }

    /**
     *  show home
     */
    public function home(Request $request){
        return view('layouts.home');
    }

    public function register(){
        $categories = Category::all();
        return view('auth.register', compact('categories'));
    }

    /**
     *  show webinar
     */
    protected function webinar(){
        return view('webinar.webinar');
    }

    /**
     *  show webinar error
     */
    protected function webinarerror(){
        return view('webinar.webinarerror');
    }

    /**
     *  show vEducation
     */
    protected function vEducation(){
        return view('webinar.vEducation');
    }

    /**
     *  show vConnect
     */
    protected function vConnect(){
        return view('webinar.vConnect');
    }

    /**
     *  show vPendrive
     */
    protected function vPendrive(){
        return view('webinar.vPendrive');
    }

    /**
     *  show vCloud
     */
    protected function vCloud(){
        return view('webinar.vCloud');
    }

    /**
     *  show live video
     */
    protected function liveVideo(){
        return view('webinar.liveVideo');
    }

    /**
     *  show career
     */
    protected function career(){
        return view('more.career');
    }

    /**
     *  show partners
     */
    protected function ourpartner(){
        return view('more.ourpartner');
    }

    /**
     *  show contact us
     */
    protected function contactus(){
        return view('more.contactus');
    }

    /**
     * erp
     */
    protected function erp(){
        return view('services.erp');
    }

    /**
     * educationalPlatform
     */
    protected function educationalPlatform(){
        return view('services.educationalPlatform');
    }

    /**
     * digitalMarketing
     */
    protected function digitalMarketing(){
        return view('services.digitalMarketing');
    }

    /**
     * pricing
     */
    protected function pricing(){
        return view('services.pricing');
    }

    /**
     * webdevelopment
     */
    protected function webdevelopment(){
        return view('services.webdevelopment');
    }

    /**
     * webdevelopment
     */
    protected function getWebdevelopment(){
        return view('services.getWebdevelopment');
    }

    protected function validateWebdevelopment(array $data){
        return Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email|max:255',
            'domains' => 'required',
            'phone' => 'required'
        ]);
    }

    /**
     * us
     */
    protected function us(){
        return view('more.us');
    }

    protected function termsandconditions(){
        return view('more.termsAndConditions');
    }

    protected function privacypolicy(){
        return view('more.privacypolicy');
    }

    protected function faq(){
        return view('more.faq');
    }

    /**
     *  show career
     */
    protected function heros(Request $request,$id=NULL){
        $courses = [];
        $designations = Cache::remember('vchip:heros:designations',60, function() {
            return Designation::all();
        });
        $heros = Cache::remember('vchip:heros:heros',60, function() {
            return ZeroToHero::all();
        });
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $currentUser = $loginUser->id;
            if($id > 0 ){
                DB::beginTransaction();
                try
                {
                    $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINZEROTOHERO,$id,$currentUser);
                    if(is_object($readNotification)){
                        DB::commit();
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('zerotohero.heros', compact('designations', 'heros', 'id', 'ads'));
    }

    protected function sendMail(Request $request){
        try
        {
            Mail::to('vchipdesigng8@gmail.com')->send(new MailToAdmin($request));
            return redirect()->back()->with('message', 'Mail sent successfully! We will came back to you asap.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function sendContactUsMail(Request $request){
        try
        {
            Mail::to('vchipdesigng8@gmail.com')->send(new ContactUs($request));
            return redirect()->back()->with('message', 'Mail sent successfully! We will came back to you asap.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function verifyAccount(){
        return view('layouts.verify_account');
    }

    protected function verifyEmail(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $user = User::where('email', $email)->where('verified', 0)->first();
            if(is_object($user)){
                DB::beginTransaction();
                try
                {
                    $user->verified = 0;
                    $user->email_token = str_random(60);
                    $user->save();

                    $email = new EmailVerification(new User(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($email);
                    DB::commit();
                    if("profile" == array_reverse(explode('/', URL::previous()))[0]){
                        return redirect('profile')->with('message', 'Verification email sent successfully. please check email and verify.');
                    } else {
                        return redirect('/')->with('message', 'Verify your email for your account activation.');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
            return redirect()->back()->withErrors(['Email id does not exist or your account is already verified.']);
        }
        return redirect()->back()->withErrors(['Please enter email id.']);
    }

    protected function subscribedUser(Request $request){
        $user = SubscriedUser::where('email', $request->get('email'))->first();
        if(!is_object($user)){
            DB::beginTransaction();
            try
            {
                $subscriedUser = SubscriedUser::create([
                    'verified' => 0,
                    'email' => $request->get('email'),
                    'email_token' => str_random(60)
                ]);

                $email = new SubscribedUserVerification(new SubscriedUser(['email_token' => $subscriedUser->email_token, 'email' => $subscriedUser->email]));
                Mail::to($subscriedUser->email)->send($email);
                DB::commit();
                return redirect('/')->with('message', 'Verify your email for your account subscription.');
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return redirect()->back()->withErrors(['You are already subscribed.']);
    }

    // Get the user who has the same token and change his/her status to verified i.e. 1
    public function verifySubscriedUser($token){
        // The verified method has been added to the user model and chained here
        // for better readability
        $subscriedUser = SubscriedUser::where('email_token',$token)->where('verified', 0)->first();
        if(is_object($subscriedUser)){
            $subscriedUser->verified();
            return redirect('login')->with('message', 'Your subscription has been verified successfully.');
        } else {
            return redirect('/')->withErrors(['You are already subscribed.']);
        }
    }

    protected function getAreasByDesignation(Request $request){
        $designationId   = InputSanitise::inputInt($request->get('designation_id'));
        return Cache::remember('vchip:heros:areas:designationId-'.$designationId,60, function() use ($designationId){
            return Area::getAreasByDesignation($designationId);
        });
    }

    protected function getHerosBySearchArray(Request $request){
        return ZeroToHero::getHerosBySearchArray($request);
    }

    protected function getHeroByDesignationByArea(Request $request){
        return ZeroToHero::getHeroByDesignationByArea($request);
    }

    protected function createAd(Request $request){
        $subPageArr = [];
        $advertisementPages = [];
        $subPages = AdvertisementPage::where('parent_page', '>', 0)->get();
        if(is_object($subPages) && false == $subPages->isEmpty()){
            foreach($subPages as $subPage){
                $subPageArr[$subPage->parent_page][] = $subPage;
            }
        }
        $mainPages = AdvertisementPage::where('parent_page', 0)->get();
        if(is_object($mainPages) && false == $mainPages->isEmpty()){
            foreach($mainPages as $mainPage){
                $advertisementPages[] = [
                                            'id' => $mainPage->id,
                                            'name' => $mainPage->name,
                                            'parent_page' => $mainPage->parent_page
                                        ];
                if(isset($subPageArr[$mainPage->id])){
                    foreach($subPageArr[$mainPage->id] as $subPage){
                        $advertisementPages[] = [
                                            'id' => $subPage->id,
                                            'name' => '&nbsp;&nbsp;&nbsp;• &nbsp;'.$subPage->name,
                                            'parent_page' => $subPage->parent_page
                                        ];
                    }
                }
            }
        }

        return view('createAdd.createAdd', compact('advertisementPages'));
    }

    protected function checkStartDate(Request $request){
        $date = $request->get('date');
        return DB::table('adds')
            ->where('is_payment_done', 1)
            ->where('show_page_id', $request->get('selected_page'))
            ->whereRaw('"'.$date.'" between `start_date` and `End_date`')
            ->count();
    }

    protected function checkDateSlot(Request $request){
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $pageId = $request->get('selected_page');
        $results = DB::table("adds")
        ->where('is_payment_done', 1)
        ->where('show_page_id', $request->get('selected_page'))
        ->where(function ($query) use ($startDate,$endDate) {
            $query->Where(function ($query) use ($startDate) {
                    $query->where('start_date', '<=', $startDate);
                    $query->where('end_date', '>=', $startDate);
                })
                ->orWhereBetween('start_date', [$startDate,$endDate])
                ->orWhere(function ($query) use ($endDate) {
                    $query->where('start_date', '<=', $endDate);
                    $query->where('end_date', '>=', $endDate);
                })
                ->orWhereBetween('end_date', [$startDate,$endDate]);
        })
        ->get();
        $output = [];
        if(is_object($results)){
            if(3 <= count($results)){
                $output['status'] = false;
            } else {
                $output['status'] = true;
                $adPage = AdvertisementPage::find($pageId);
                $dateDiff = date_diff( new DateTime($endDate), new DateTime($startDate));
                $days = $dateDiff->d + 1;
                $output['price'] = $adPage->price * $days;
            }
            $output['start_date'] = $startDate;
            $output['end_date'] = $endDate;
        }
        return $output;
    }

    protected function validateAdvertisement(array $data){
        return Validator::make($data, [
            'name' => 'required',
            'selected_page' => 'required',
            'email' => 'required|email|max:255',
            'tag_line' => 'required',
            'website_url' => 'required',
            'logo' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'phone' => 'required'
        ]);
    }

    protected function doAdvertisementPayment(Request $request){
        // Laravel validation
        $validator = $this->validateAdvertisement($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }
        $name = $request->get('name');
        $email = $request->get('email');
        $selectedPage = $request->get('selected_page');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $phone = $request->get('phone');
        DB::beginTransaction();
        try
        {
            $advertisement = Add::addOrUpdateAd($request);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect('createAd')->withErrors('something went wrong.');
        }

        $adPage = AdvertisementPage::find($selectedPage);
        if(!is_object($adPage)){
            return redirect('createAd');
        }
        $dateDiff = date_diff( new DateTime($endDate), new DateTime($startDate));
        $days = $dateDiff->d + 1;
        $price = $adPage->price * $days;

        Session::put('ad_id', $advertisement->id);
        Session::save();

        $adName = substr('Ad by '.$name, 0, 29) ;

        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => trim($adName),
                "amount" => $price,
                "buyer_name" => $name,
                "phone" => $phone,
                "send_email" => true,
                "send_sms" => false,
                "email" => $email,
                'allow_repeated_payments' => false,
                "redirect_url" => url('thankyouadvertisement'),
                "webhook" => url('webhookAdvertisement')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();
        }
        catch (Exception $e) {
            return redirect('createAd')->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyouadvertisement(Request $request){
        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        $payid = $request->get('payment_request_id');

        try {
            $response = $api->paymentRequestStatus($payid);

            if( 'Credit' == $response['payments'][0]['status']){
                // create a client
                $paymentRequestId = $response['id'];
                $paymentId = $response['payments'][0]['payment_id'];
                $email = $response['payments'][0]['buyer_email'];
                $status = $response['payments'][0]['status'];
                $adId = Session::get('ad_id');
                DB::connection('mysql')->beginTransaction();
                try
                {
                    $paymentArray = [
                                        'add_id' => $adId,
                                        'email' => $email,
                                        'payment_id' => $paymentId,
                                        'payment_request_id' => $paymentRequestId,
                                        'status' => $status
                                    ];
                    AdvertisementPayment::addPayment($paymentArray);
                    $addObj = Add::find($adId);
                    if(is_object($addObj)){
                        $addObj->is_payment_done = 1;
                        $addObj->save();
                    }
                    DB::commit();
                }
                catch(Exception $e)
                {
                    DB::connection('mysql')->rollback();
                    return redirect('/')->withErrors([$e->getMessage()]);
                }
                Session::remove('ad_id');
                return redirect('createAd')->with('message', 'your advertisement has been created successfully.');
            } else {
                $adId = Session::get('ad_id');
                DB::connection('mysql')->beginTransaction();
                try {
                    $advertisement = Add::find($adId);
                    if(file_exists($advertisement->logo)){
                        unlink($advertisement->logo);
                    }
                    $advertisement->delete();
                    Session::remove('ad_id');
                    DB::commit();
                    return redirect('createAd')->with('message', 'Payment is failed.');
                }
                catch(Exception $e)
                {
                    DB::connection('mysql')->rollback();
                    return redirect('createAd')->withErrors([$e->getMessage()]);
                }
            }
        }
        catch (Exception $e) {
            return redirect('createAd')->withErrors([$e->getMessage()]);
        }
    }

    public function webhookAdvertisement(Request $request){
        $data = $request->all();
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        ksort($data, SORT_STRING | SORT_FLAG_CASE);

        if('local' == \Config::get('app.env')){
            $mac_calculated = hash_hmac("sha1", implode("|", $data), "aa7af601d8f946c49653c14e6d88d6c6");
        } else {
            $mac_calculated = hash_hmac("sha1", implode("|", $data), "adc79e762cf240f49022176bd21f20ce");
        }
        if($mac_provided == $mac_calculated){
            $to = 'vchipdesign@gmail.com';
            $subject = 'Advertisement Payment Request ' .$data['buyer_name'].'';
            $message = "<h1>Payment Details</h1>";
            $message .= "<hr>";
            $message .= '<p><b>Payment Id:</b> '.$data['payment_id'].'</p>';
            $message .= '<p><b>Payment Status:</b> '.$data['status'].'</p>';
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<hr>";
            $message .= '<p><b>Name:</b> '.$data['buyer_name'].'</p>';
            $message .= '<p><b>Email:</b> '.$data['buyer'].'</p>';
            $message .= "<hr>";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            mail($to, $subject, $message, $headers);
        }
    }

    protected function showAddCalendar(Request $request){
        $data = '';
        if((int)$request->get('page') > 0){
            $data = Add::where('show_page_id', $request->get('page'))->where('is_payment_done', 1)->get();
        } else {
            $data = Add::where('is_payment_done', 1)->get();
        }
        $events = [];
        if(is_object($data) && $data->count()) {
            foreach ($data as $key => $value) {
                $events[] = array(
                    "title" => $value->company,
                    "start" => $value->start_date,
                    "end" =>   date('Y-m-d', strtotime("+1 day", strtotime($value->end_date))),
                    "color" => "#f05050",
                );
            }
        }
        return $events;
    }

    protected function sendVchipUserSignUpOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::sendOtp($mobile);
    }

    protected function sendVchipUserSignInOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::sendOtp($mobile);
    }

    protected function collegeLogin($college){
        if(is_object(Auth::user())){
            return redirect('/');
        }
        $collegeUrl = InputSanitise::inputString($college);
        if(!empty($collegeUrl)){
            if('other' == $collegeUrl){
                return view('header.college', compact('college'));
            } else {
                $college = College::whereNotNull('url')->where('url',$collegeUrl)->first();
                if($college){
                    return view('header.college', compact('college'));
                }
            }
        }
        return redirect('/');
    }
}
