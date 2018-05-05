<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instamojo;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Redirect,View,DB,Mail,Session,Validator,Cache,File;
use Illuminate\Http\RedirectResponse;
use App\Mail\PaymentReceived;
use App\Mail\ContactUs;
use App\Mail\EmailVerification;
use App\Mail\NewRegisteration;
use App\Mail\WelcomeClient;
use App\Mail\PaymentGatewayErrors;
use App\Libraries\InputSanitise;
use App\Models\WebdevelopmentPayment;
use App\Models\Plan;
use App\Models\Client;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\ClientPlan;
use App\Models\Payment;
use App\Models\InstamojoDetail;
use App\Models\UserBasedAuthentication;

class OnlineClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

    }

    public function digitaleducation(){
        return view('client.online.digitaleducation');
    }

    public function webdevelopment(){
        return view('client.online.webdevelopment');
    }

    public function digitalmarketing(){
        return view('client.online.digitalmarketing');
    }

    public function pricing(){
        $allPlan = [];
        $plans = Plan::all();
        if(is_object($plans) && false == $plans->isEmpty()){
            foreach($plans as $plan){
                $allPlan[$plan->id] = $plan;
            }
        }
        return view('client.online.pricing', compact('allPlan'));
    }

    public function getWebdevelopment(){
        return view('client.online.getWebdevelopment');
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
        $price = 4999;

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
                $to = 'vchipdesign@gmail.com';
                $subject = 'Web development Payment Request By:' .$name.'';
                $message = "<h1>Payment Details</h1>";
                $message .= "<hr>";
                $message .= '<p><b>Payment Id:</b> '.$paymentId.'</p>';
                $message .= '<p><b>Payment Status:</b> '.$status.'</p>';
                $message .= '<p><b>Amount:</b> '.$price.'</p>';
                $message .= "<hr>";
                $message .= '<p><b>Name:</b> '.$name.'</p>';
                $message .= '<p><b>Email:</b> '.$email.'</p>';
                $message .= '<p><b>Domains:</b> '.$domains.'</p>';
                $message .= "<hr>";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                // send email
                // mail($to, $subject, $message, $headers);
                Mail::to($to)->send(new PaymentReceived($message,$subject));
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
            $subject = 'Web development Payment Request By:' .$data['buyer_name'].'';
            $message = "<h1>Payment Details</h1>";
            $message .= "<hr>";
            $message .= '<p><b>Payment Id:</b> '.$data['payment_id'].'</p>';
            $message .= '<p><b>Payment Status:</b> '.$data['status'].'</p>';
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<hr>";
            $message .= '<p><b>Name:</b> '.$data['buyer_name'].'</p>';
            $message .= '<p><b>Email:</b> '.$data['buyer'].'</p>';
            $message .= "<hr>";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            Mail::to($to)->send(new PaymentReceived($message,$subject));
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

    /**
     * Show the clientsignup.
     *
     * @return show form
     */
    public function clientsignup($subdomain,$planId){
        $plan = Plan::find($planId);
        if(is_object($plan)){
            return view('header.clientsignup', compact('plan'));
        }
        return redirect('/');
    }

    public function isCLientExists($subdomain,Request $request){
        return Client::isCLientExists($request);
    }

    protected function validateClient(array $data){
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|regex:/^[1-9]{1}[0-9]{9}$/',
            'email' => 'required|email|max:255|unique:mysql2.clients',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);
    }

    /**
     * client payment redirect
     */
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
                "redirect_url" => url('thankyouclient'),
                "webhook" => url('webhookclient')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();

        }
        catch (Exception $e) {
            return redirect('pricing')->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyouclient(Request $request){
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
                        'photo' => '/images/user1.png',
                    ]);

                    if( !is_object($client)){
                        DB::connection('mysql2')->rollback();
                        return redirect('pricing')->withErrors('Something went wrong.');
                    }

                    ClientHomePage::addClientHomePage($client);
                    ClientTestimonial::addTestimonials($client);
                    ClientTeam::addTeam($client);
                    ClientCustomer::addCustomer($client);

                    $hostArr = explode('.', $client->subdomain);
                    // create client/subdomain dir in kcfinder upload dir
                    $path = public_path().'/templateEditor/kcfinder/upload/images/'. $hostArr[0];
                    if(!is_dir($path)){
                        File::makeDirectory($path, $mode = 0777, true, true);
                    }

                    DB::connection('mysql')->beginTransaction();
                    try
                    {
                        $instamojoAuthErrors = '';
                        $instamojoAuthErrorCount = 0;
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
                                    $instamojoAuthErrorCount++;
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
                                            $instamojoAuthErrors.= '--------application_auth_error--------</br>';
                                            $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                            $instamojoAuthErrors.= 'applicationPostFields -'.serialize($applicationPostFields).'</br>';
                                            $instamojoAuthErrorCount++;
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
                                $instamojoAuthErrorCount++;
                            } else {
                                $results = json_decode($response, true);

                                if(!empty($results['id'])){
                                    $userAuth  = new UserBasedAuthentication;
                                    $userAuth->vchip_client_id = $client->id;
                                    $userAuth->instamojo_client_id = $results['id'];
                                    $userAuth->save();

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
                                        $instamojoAuthErrorCount++;
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
                                                $instamojoAuthErrors.= '--------user_auth_error--------</br>';
                                                $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                                $instamojoAuthErrors.= 'userAuthPostFields -'.serialize($userAuthPostFields).'</br>';
                                                $instamojoAuthErrorCount++;
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
                                } else {
                                    if(count($results) > 0){
                                        $instamojoAuthErrors.= '--------signup_error--------</br>';
                                        $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                        $instamojoAuthErrors.= 'applicationAccessToken -'.serialize($applicationAccessToken).'</br>';
                                        $instamojoAuthErrors.= 'signupPostFields -'.serialize($signupPostFields).'</br>';
                                        $instamojoAuthErrorCount++;
                                    }
                                }
                            }
                        }
                    }
                    catch(Exception $e)
                    {
                        DB::connection('mysql')->rollback();
                        DB::connection('mysql2')->rollback();
                        return redirect('pricing')->withErrors([$e->getMessage()]);
                    }
                    DB::connection('mysql')->commit();
                    DB::connection('mysql2')->commit();

                    $data['name'] = $client->name;
                    $data['email'] = $client->email;
                    $data['subdomain'] = 'https://'.$client->subdomain;
                    $data['domain'] = '';

                    // send mail to admin after new registration
                    Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
                    Mail::to($client->email)->send(new WelcomeClient($data));

                    if($instamojoAuthErrorCount > 0){
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

    public function webhookclient(Request $request){
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
            $subject = 'Subdomain Payment Request By: ' .$data['buyer_name'].'</br>';
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
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            // mail($to, $subject, $message, $headers);
            Mail::to($to)->send(new PaymentReceived($message,$subject));

            $to = $data['buyer'];
            $subject = 'Your transaction with Vchipedu on '. date('Y-m-d').' is successful';
            $message = "<h1>Dear ".$data['buyer']."</h1></br>";
            $message .= "Thank you for paying. Your Payment has been successfully processed.</br>";
            $message .= "<hr>";
            $message .= "<h1>Payment Details</h1>";
            $message .= '<p><b>Payment Id:</b> '.$data['payment_id'].'</p>';
            $message .= '<p><b>Payment Status:</b> '.$data['status'].'</p>';
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<p>Thank</p>";
            $message .= "<p>Vchipedu</p>";

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            // mail($to, $subject, $message, $headers);
            Mail::to($to)->send(new PaymentReceived($message,$subject));
        }
    }

    /**
     * client free registration
     */
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
                'photo' => '/images/user1.png',
            ]);

            if( !is_object($client)){
                DB::connection('mysql2')->rollback();
                return redirect('pricing')->withErrors('Something went wrong.');
            }

            ClientHomePage::addClientHomePage($client);
            ClientTestimonial::addTestimonials($client);
            ClientTeam::addTeam($client);
            ClientCustomer::addCustomer($client);

            $hostArr = explode('.', $client->subdomain);
            // create client/subdomain dir in kcfinder upload dir
            $path = public_path().'/templateEditor/kcfinder/upload/images/'. $hostArr[0];
            if(!is_dir($path)){
                File::makeDirectory($path, $mode = 0777, true, true);
            }

            DB::connection('mysql')->beginTransaction();
            try
            {
                $instamojoAuthErrors = '';
                $instamojoAuthErrorCount = 0;
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
                            $instamojoAuthErrorCount++;
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
                                    $instamojoAuthErrors.= '--------application_auth_error--------</br>';
                                    $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                    $instamojoAuthErrors.= 'applicationPostFields -'.serialize($applicationPostFields).'</br>';
                                    $instamojoAuthErrorCount++;
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
                        $instamojoAuthErrorCount++;
                    } else {
                        $results = json_decode($response, true);

                        if(!empty($results['id'])){
                            $userAuth  = new UserBasedAuthentication;
                            $userAuth->vchip_client_id = $client->id;
                            $userAuth->instamojo_client_id = $results['id'];
                            $userAuth->save();

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
                                $instamojoAuthErrorCount++;
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
                                        $instamojoAuthErrors.= '--------user_auth_error--------</br>';
                                        $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                        $instamojoAuthErrors.= 'userAuthPostFields -'.serialize($userAuthPostFields).'</br>';
                                        $instamojoAuthErrorCount++;
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

                        } else {
                            if(count($results) > 0){
                                $instamojoAuthErrors.= '--------signup_error--------</br>';
                                $instamojoAuthErrors.= 'user -'.$client->email.'->'.serialize($results).'</br>';
                                $instamojoAuthErrors.= 'applicationAccessToken -'.serialize($applicationAccessToken).'</br>';
                                $instamojoAuthErrors.= 'signupPostFields -'.serialize($signupPostFields).'</br>';
                                $instamojoAuthErrorCount++;
                            }
                        }
                    }
                }
            }
            catch(Exception $e)
            {
                DB::connection('mysql')->rollback();
                DB::connection('mysql2')->rollback();
                return redirect('pricing')->withErrors([$e->getMessage()]);
            }
            DB::connection('mysql')->commit();
            DB::connection('mysql2')->commit();

            $data['name'] = $client->name;
            $data['email'] = $client->email;
            $data['subdomain'] = 'https://'.$client->subdomain;
            $data['domain'] = '';

            // send mail to admin after new registration
            Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));
            Mail::to($client->email)->send(new WelcomeClient($data));

            if($instamojoAuthErrorCount > 0){
                Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoAuthErrors));
            }

            return redirect('pricing')->with('message', 'Please check your email and login to your web site.');
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect('pricing')->withErrors([$e->getMessage()]);
        }
    }

}