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
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
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

        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

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
                        $appTokenUrl = "https://test.instamojo.com/oauth2/token/";
                        $signUpUrl = "https://test.instamojo.com/v2/users/";
                        $userAuthUrl = "https://test.instamojo.com/oauth2/token/";
                    } else {
                        $subdomain = $subdomain.'.vchipedu.com';
                        $appTokenUrl = "https://api.instamojo.com/oauth2/token/";
                        $signUpUrl = "https://api.instamojo.com/v2/users/";
                        $userAuthUrl = "https://api.instamojo.com/oauth2/token/";
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
                        $instamojoDetail = InstamojoDetail::first();

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
                                  CURLOPT_URL => $appTokenUrl,
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
                              CURLOPT_URL => $signUpUrl,
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
                              CURLOPT_URL => $userAuthUrl,
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

        if('local' == \Config::get('app.env')){
            $mac_calculated = hash_hmac("sha1", implode("|", $data), "aa7af601d8f946c49653c14e6d88d6c6");
        } else {
            $mac_calculated = hash_hmac("sha1", implode("|", $data), "adc79e762cf240f49022176bd21f20ce");
        }
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
                $appTokenUrl = "https://test.instamojo.com/oauth2/token/";
                $signUpUrl = "https://test.instamojo.com/v2/users/";
                $userAuthUrl = "https://test.instamojo.com/oauth2/token/";
            } else {
                $subdomain = $request->get('subdomain').'.vchipedu.com';
                $appTokenUrl = "https://api.instamojo.com/oauth2/token/";
                $signUpUrl = "https://api.instamojo.com/v2/users/";
                $userAuthUrl = "https://api.instamojo.com/oauth2/token/";
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
                $instamojoAuthErrors = '';
                // check access token for application base auth
                $instamojoDetail = InstamojoDetail::first();
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
                          CURLOPT_URL => $appTokenUrl,
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
                      CURLOPT_URL => $signUpUrl,
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
                      CURLOPT_URL => $userAuthUrl,
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

    public function virtualplacementdrive(){
        $virtualplacementdrive = VirtualPlacementDrive::first();
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

    protected function doWebdevelopmentPayment(Request $request){
        // Laravel validation
        $validator = $this->validateWebdevelopment($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }
        $name = $request->get('name');
        $email = $request->get('email');
        $domains = $request->get('domains');
        $phone = $request->get('phone');
        $price = 2999;

        Session::put('web_name', $name);
        Session::put('web_email', $email);
        Session::put('web_domains', $domains);
        Session::put('web_phone', $phone);
        Session::save();

        $adName = substr('web dev for '.$name, 0, 29) ;

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
                "redirect_url" => url('thankyouwebdevelopment'),
                "webhook" => url('webhookwebdevelopment')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();
        }
        catch (Exception $e) {
            return redirect('webdevelopment')->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyouwebdevelopment(Request $request){
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
                $price = $response['payments'][0]['amount'];

                $name = Session::get('web_name');
                $email = Session::get('web_email');
                $domains = Session::get('web_domains');
                $phone = Session::get('web_phone');
                DB::connection('mysql')->beginTransaction();
                try
                {
                    $paymentArray = [
                                        'name' => $name,
                                        'email' => $email,
                                        'domains' => $domains,
                                        'phone' => $phone,
                                        'payment_id' => $paymentId,
                                        'payment_request_id' => $paymentRequestId,
                                        'status' => $status,
                                        'price' => $price
                                    ];
                    WebdevelopmentPayment::addPayment($paymentArray);
                    DB::commit();
                }
                catch(Exception $e)
                {
                    DB::connection('mysql')->rollback();
                    return redirect('webdevelopment')->withErrors([$e->getMessage()]);
                }
                Session::remove('web_name');
                Session::remove('web_email');
                Session::remove('web_domains');
                Session::remove('web_phone');
                return redirect('webdevelopment')->with('message', 'your have successfully created a web development request. we will contact you asap.');
            } else {
                return redirect('webdevelopment')->with('message', 'Payment is failed.');
            }
        }
        catch (Exception $e) {
            return redirect('webdevelopment')->withErrors([$e->getMessage()]);
        }
    }

    public function webhookwebdevelopment(Request $request){
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
            $subject = 'Web development Payment Request ' .$data['buyer_name'].'';
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

    /**
     * us
     */
    protected function us(){
        return view('more.us');
    }

    /**
     *  show career
     */
    protected function heros(Request $request,$id=NULL){
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

    protected function createAd(Request $request){
        if(!empty($request->get('page'))){
            $data = Add::where('show_page_id', $request->get('page'))->get();
            $selectedPage = $request->get('page');
        } else {
            $data = Add::all();
            $selectedPage = '';
        }
        $events = [];
        if($data->count()) {
            foreach ($data as $key => $value) {
                $events[] = \Calendar::event(
                    $value->company,
                    true,
                    new \DateTime($value->start_date),
                    new \DateTime($value->end_date.' +1 day'),
                    null,
                    // Add color and link on event
                    [
                        'color' => '#f05050',
                        // 'url' => 'pass here url and any route',
                    ]
                );
            }
        }
        $subPageArr = [];
        $advertisementPages = [];
        $calendar = \Calendar::addEvents($events);
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
        return view('createAdd.createAdd', compact('calendar', 'selectedPage', 'advertisementPages'));
    }

    protected function checkStartDate(Request $request){
        $date = $request->get('date');
        return DB::table('adds')
            ->where('show_page_id', $request->get('selected_page'))
            ->whereRaw('"'.$date.'" between `start_date` and `End_date`')
            ->count();
    }

    protected function checkDateSlot(Request $request){
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $pageId = $request->get('selected_page');
        $results = DB::table("adds")
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
}
