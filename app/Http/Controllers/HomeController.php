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
use View,DB,Session,Redirect, Auth,Validator;
use App\Mail\EmailVerification;
use App\Mail\NewRegisteration;
use App\Mail\SubscribedUserVerification;
use App\Mail\WelcomeClient;
use App\Mail\PaymentGatewayErrors;
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

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $testCategories = TestCategory::all();
        // view::share('testCategories', $testCategories);
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

    /**
     * Show the clientsignup.
     *
     * @return \Illuminate\Http\Response
     */
    public function clientsignup($planId){
        $plan = Plan::find($planId);
        if(is_object($plan)){
            return view('header.clientsignup', compact('plan'));
        }
        return back();
    }

    public function isCLientExists(Request $request){
        return Client::isCLientExists($request);
    }

    protected function validateClient(array $data){
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|regex:/[0-9]{10}/',
            'email' => 'required|email|max:255|unique:mysql2.clients',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);
    }

    protected function doPayment(Request $request){
        // Laravel validation
        $validator = $this->validateClient($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }

        $plan = Plan::find($request->get('plan_id'));
        if(!is_object($plan)){
            return redirect('pricing');
        }

        Session::put('client_password', $request->get('password'));
        Session::put('client_subdomain', $request->get('subdomain'));
        Session::put('client_plan_id', $request->get('plan_id'));
        Session::save();
        $name = InputSanitise::inputString($request->get('name'));
        $phone = InputSanitise::inputString($request->get('phone'));
        $email = $request->get('email');
        $planPrice = $plan->amount;
        $planName = 'register for '.$plan->name;


        $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => $planName,
                "amount" => $planPrice,
                "buyer_name" => $name,
                "phone" => $phone,
                "send_email" => true,
                "send_sms" => true,
                "email" => $email,
                'allow_repeated_payments' => false,
                "redirect_url" => url('thankyou'),
                "webhook" => url('webhook')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();

        }
        catch (Exception $e) {
            return redirect('pricing')->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyou(Request $request){
        $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');

        $payid = $request->get('payment_request_id');

        try {
            $response = $api->paymentRequestStatus($payid);

            if( 'Credit' == $response['payments'][0]['status']){
                // create a client
                $paymentRequestId = $response['id'];
                $paymentId = $response['payments'][0]['payment_id'];
                $name = $response['payments'][0]['buyer_name'];
                $email = $response['payments'][0]['buyer_email'];
                $phone = $response['payments'][0]['buyer_phone'];
                $status = $response['payments'][0]['status'];
                $password = Session::get('client_password');
                $subdomain = Session::get('client_subdomain');
                $plan_id = Session::get('client_plan_id');

                $plan = Plan::find($plan_id);
                if(is_object($plan)){
                    $planId = $plan->id;
                    $planPrice = $plan->amount;
                } else {
                    $planId = 0;
                    $planPrice = 0;
                }

                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if('local' == \Config::get('app.env')){
                        $subdomain = $subdomain.'.localvchip.com';
                    } else {
                        $subdomain = $subdomain.'.vchipedu.com';
                    }
                    $client = Client::create([
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'password' => bcrypt($password),
                        'subdomain' => $subdomain,
                        'admin_approve' => 1,
                        'plan_id' => $planId,
                    ]);

                    if( !is_object($client)){
                        DB::connection('mysql2')->rollback();
                        return redirect('pricing')->withErrors('Something went wrong.');
                    }

                    ClientHomePage::addClientHomePage($client);
                    ClientTestimonial::addTestimonials($client);
                    ClientTeam::addTeam($client);
                    ClientCustomer::addCustomer($client);

                    DB::connection('mysql')->beginTransaction();
                    try
                    {
                        $instamojoAuthErrors = '';
                        // check access token for application base auth
                        $instamojoDetail = InstamojoDetail::where('client_id', '4IfB5qdRnGjcq1LqCgkHLdARUvK3oAg1FyGdnqIR')->first();

                        if(is_object($instamojoDetail)){
                            if(empty($instamojoDetail->application_base_access_token) && empty($instamojoDetail->application_base_token_type)){
                                // get & store application token
                                $applicationPostFields = [
                                                'grant_type' => 'client_credentials',
                                                'client_id' => $instamojoDetail->client_id,
                                                'client_secret' => $instamojoDetail->client_secret
                                              ];

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                  CURLOPT_URL => "https://test.instamojo.com/oauth2/token/",
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => "",
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 60,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => "POST",
                                  CURLOPT_POSTFIELDS => $applicationPostFields,
                                  CURLOPT_HTTPHEADER => array(
                                    "cache-control: no-cache",
                                    "content-type: multipart/form-data;"
                                  ),
                                ));

                                $response = curl_exec($curl);
                                $err = curl_error($curl);
                                curl_close($curl);
                                if ($err) {
                                    $instamojoAuthErrors.= 'application_auth_curl_error-' .(string)$err;
                                } else {
                                    $results = json_decode($response, true);
                                    if(!empty($results['access_token']) && !empty($results['token_type'])){
                                        $instamojoDetail->application_base_access_token = $results['access_token'];
                                        $instamojoDetail->application_base_token_type = $results['token_type'];
                                        $instamojoDetail->save();

                                        $applicationAccessToken = $instamojoDetail->application_base_access_token;
                                        $applicationTokenType = $instamojoDetail->application_base_token_type;
                                    } else {
                                        if(count($results) > 0){
                                            $instamojoAuthErrors.= '--------application_auth_error--------';
                                            foreach($results as $key => $result){
                                                $instamojoAuthErrors.= 'user -'.$client->email.'->'.$key.'->'.$result[0];
                                            }
                                        }
                                    }
                                }
                            } else {
                                $applicationAccessToken = $instamojoDetail->application_base_access_token;
                                $applicationTokenType = $instamojoDetail->application_base_token_type;
                            }

                            // sign up client
                            $signupPostFields = [
                                            'email'=> $client->email,
                                            'password'=> $client->email,
                                            'phone'=> $client->phone,
                                            'referrer'=> $instamojoDetail->referrer
                                          ];

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                              CURLOPT_URL => "https://test.instamojo.com/v2/users/",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 60,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => $signupPostFields,
                              CURLOPT_HTTPHEADER => array(
                                "authorization: Bearer ".$applicationAccessToken."",
                                "cache-control: no-cache",
                                "content-type: multipart/form-data"
                              ),
                            ));

                            $response = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);
                            if($err) {
                                $instamojoAuthErrors.= 'signup_curl_error-' .(string)$err;
                            } else {
                                $results = json_decode($response, true);

                                if(!empty($results['id'])){
                                    $userAuth  = new UserBasedAuthentication;
                                    $userAuth->vchip_client_id = $client->id;
                                    $userAuth->instamojo_client_id = $results['id'];
                                    $userAuth->save();
                                } else {
                                    if(count($results) > 0){
                                        $instamojoAuthErrors.= '--------signup_error--------';
                                        foreach($results as $key => $result){
                                            $instamojoAuthErrors.= 'user -'.$client->email.'->'.$key.'->'.$result[0];
                                        }
                                    }
                                }
                            }
                            // user based auth
                            $userAuthPostFields = [
                                            'grant_type'=>'password',
                                            'client_id' => $instamojoDetail->client_id,
                                            'client_secret' => $instamojoDetail->client_secret,
                                            'username' => $client->email,
                                            'password' => $client->email
                                          ];

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                              CURLOPT_URL => "https://test.instamojo.com/oauth2/token/",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 60,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "POST",
                              CURLOPT_POSTFIELDS => $userAuthPostFields,
                              CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache",
                                "content-type: multipart/form-data"
                              ),
                            ));

                            $response = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            if ($err) {
                                $instamojoAuthErrors.= 'user_auth_curl_error-' .(string)$err;
                            } else {
                                $results = json_decode($response, true);
                                if(!empty($results['access_token']) && !empty($results['refresh_token']) && !empty($results['token_type'])){
                                    $userAuth  = UserBasedAuthentication::where('vchip_client_id', $client->id)->first();
                                    if(is_object($userAuth)){
                                        $userAuth->access_token = $results['access_token'];
                                        $userAuth->refresh_token = $results['refresh_token'];
                                        $userAuth->token_type = $results['token_type'];
                                        $userAuth->save();
                                    }
                                } else {
                                    if(count($results) > 0){
                                        $instamojoAuthErrors.= '--------user_auth_error--------';
                                        foreach($results as $key => $result){
                                            $instamojoAuthErrors.= 'user -'.$client->email.'->'.$key.'->'.$result[0];
                                        }
                                    }
                                }
                            }
                        }


                        $clientPlanArray = [
                                                'client_id' => $client->id,
                                                'plan_id' => $planId,
                                                'plan_amount' => $planPrice,
                                                'final_amount' => $planPrice,
                                                'start_date' => date('Y-m-d'),
                                                'end_date' => date('Y-m-d', strtotime('+1 years')),
                                                'payment_status' => $status,
                                                'degrade_plan' => 0
                                            ];
                        $clientPlan = ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                        if(is_object($clientPlan)){
                            $paymentArray = [
                                                'client_plan_id' => $clientPlan->id,
                                                'payment_id' => $paymentId,
                                                'payment_request_id' => $paymentRequestId,
                                                'status' => $status
                                            ];
                            Payment::addPayment($paymentArray);
                        }
                        DB::connection('mysql')->commit();
                    }
                    catch(Exception $e)
                    {
                        DB::connection('mysql')->rollback();
                        DB::connection('mysql2')->rollback();
                        return redirect('pricing')->withErrors([$e->getMessage()]);
                    }

                    DB::connection('mysql2')->commit();

                    $data['name'] = $client->name;
                    $data['email'] = $client->email;
                    $data['subdomain'] = $client->subdomain;

                    // send mail to admin after new registration
                    Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
                    Mail::to($client->email)->send(new WelcomeClient($data));
                    if(!empty($instamojoAuthErrors)){
                        Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoAuthErrors));
                    }
                    Session::remove('client_password');
                    Session::remove('client_subdomain');
                    Session::remove('client_plan_id');

                    return redirect('pricing')->with('message', 'Please check your email and login to your web site.');
                }
                catch(Exception $e)
                {
                    DB::connection('mysql')->rollback();
                    DB::connection('mysql2')->rollback();
                    return redirect('pricing')->withErrors([$e->getMessage()]);
                }
            }
        }
        catch (Exception $e) {
            return redirect('pricing')->withErrors([$e->getMessage()]);
        }
    }

    public function webhook(Request $request){
        $data = $request->all();
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        ksort($data, SORT_STRING | SORT_FLAG_CASE);

        $mac_calculated = hash_hmac("sha1", implode("|", $data), "aa7af601d8f946c49653c14e6d88d6c6");
        if($mac_provided == $mac_calculated){
            $to = 'vchipdesign@gmail.com';
            $subject = 'Website Payment Request ' .$data['buyer_name'].'';
            $message = "<h1>Payment Details</h1>";
            $message .= "<hr>";
            $message .= '<p><b>Payment Id:</b> '.$data['payment_id'].'</p>';
            $message .= '<p><b>Payment Status:</b> '.$data['status'].'</p>';
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<hr>";
            $message .= '<p><b>Name:</b> '.$data['buyer_name'].'</p>';
            $message .= '<p><b>Email:</b> '.$data['buyer'].'</p>';
            $message .= '<p><b>Phone:</b> '.$data['buyer_phone'].'</p>';
            $message .= "<hr>";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            mail($to, $subject, $message, $headers);

            $to = $data['buyer'];
            $subject = 'Your transaction with Vchipedu on '. date('Y-m-d').' is successful';
            $message = "<h1>Dear ".$data['buyer']."</h1></br>";
            $message .= "Thank you for paying. Your Payment has been successfully processed.</br>";
            $message .= "<hr>";
            $message = "<h1>Payment Details</h1>";
            $message .= '<p><b>Payment Id:</b> '.$data['payment_id'].'</p>';
            $message .= '<p><b>Payment Status:</b> '.$data['status'].'</p>';
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<p>Thank</p>";
            $message .= "<p>Vchipedu</p>";

            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            mail($to, $subject, $message, $headers);
        }
    }

    protected function freeRegister(Request $request){
        // Laravel validation
        $validator = $this->validateClient($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }
        $name = InputSanitise::inputString($request->get('name'));
        $phone = InputSanitise::inputString($request->get('phone'));
        $email = $request->get('email');
        $plan = Plan::find($request->get('plan_id'));
        if(is_object($plan)){
            $planId = $plan->id;
            $planPrice = $plan->amount;
        } else {
            return redirect('pricing');
        }

        DB::connection('mysql2')->beginTransaction();
        try
        {
            if('local' == \Config::get('app.env')){
                $subdomain = $request->get('subdomain').'.localvchip.com';
            } else {
                $subdomain = $request->get('subdomain').'.vchipedu.com';
            }
            $client = Client::create([
                'name' => $name ,
                'phone' => $phone,
                'email' => $email,
                'password' => bcrypt($request->get('password')),
                'subdomain' => $subdomain,
                'admin_approve' => 1,
                'plan_id' => $planId,
            ]);

            if( !is_object($client)){
                DB::connection('mysql2')->rollback();
                return redirect('pricing')->withErrors('Something went wrong.');
            }

            ClientHomePage::addClientHomePage($client);
            ClientTestimonial::addTestimonials($client);
            ClientTeam::addTeam($client);
            ClientCustomer::addCustomer($client);

            DB::connection('mysql')->beginTransaction();
            try
            {
                $clientPlanArray = [
                                        'client_id' => $client->id,
                                        'plan_id' => $planId,
                                        'plan_amount' => $planPrice,
                                        'final_amount' => $planPrice,
                                        'start_date' => date('Y-m-d'),
                                        'end_date' => date('Y-m-d', strtotime('+1 years')),
                                        'payment_status' => 'free',
                                        'degrade_plan' => 0
                                    ];
                ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                DB::connection('mysql')->commit();
            }
            catch(Exception $e)
            {
                DB::connection('mysql')->rollback();
                DB::connection('mysql2')->rollback();
                return redirect('pricing')->withErrors([$e->getMessage()]);
            }

            DB::connection('mysql2')->commit();

            $data['name'] = $client->name;
            $data['email'] = $client->email;
            $data['subdomain'] = $client->subdomain;

            // send mail to admin after new registration
            Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
            Mail::to($client->email)->send(new WelcomeClient($data));

            return redirect('pricing')->with('message', 'Please check your email and login to your web site.');
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect('pricing')->withErrors([$e->getMessage()]);
        }
    }

    public function getDepartments(Request $request){
        $collegeId = $request->get('college');
        return CollegeDept::where('college_id', $collegeId)->get();
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
     * us
     */
    protected function us(){
        return view('more.us');
    }

    /**
     *  show career
     */
    protected function heros($id=NULL){
        $designations = Designation::all();
        $courses = [];
        $heros = ZeroToHero::all();
        if(is_object(Auth::user())){
            $currentUser = Auth::user()->id;
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
        return view('zerotohero.heros', compact('designations', 'heros', 'id'));
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
                    return redirect('/')->with('message', 'Verify your email for your account activation.');
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
        return Area::getAreasByDesignation($designationId);
    }

    protected function getHerosBySearchArray(Request $request){
        return ZeroToHero::getHerosBySearchArray($request);
    }

    protected function getHeroByDesignationByArea(Request $request){
        return ZeroToHero::getHeroByDesignationByArea($request);
    }

}
