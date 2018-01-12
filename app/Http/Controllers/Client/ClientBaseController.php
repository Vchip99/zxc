<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewRegisteration;
use App\Mail\WelcomeClient;
use App\Mail\PaymentGatewayErrors;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientCustomer;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientPlan;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\BankDetail;
use App\Models\UserBasedAuthentication;
use App\Models\ClientUserPurchasedCourse;
use App\Models\ClientUserPurchasedTestSubCategory;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB, Session;
use App\Http\Controllers\Instamojo;
use DateTime;

class ClientBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('client');
        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(is_object($subdomain)){
            view::share('subdomain', $subdomain);
            $client = Client::where('subdomain', $subdomain->subdomain)->first();
            if(is_object($client)){
                view::share('client', $client);
            }
        }
    }

    protected function showDashBoard(){
        return view('client.dashboard');
    }

    protected function manageClientHome(){
        $onlineCourses = ClientOnlineCourse::getCurrentCoursesByClient(Auth::guard('client')->user()->subdomain);
        $defaultCourse = ClientOnlineCourse::where('name', 'How to use course')->first();
        $defaultTest = ClientOnlineCourse::where('name', 'How to use test')->first();

        $onlineTestSubcategories = ClientOnlineTestSubCategory::getCurrentSubCategoriesAssociatedWithQuestion(Auth::guard('client')->user()->subdomain);
        $subdomain = ClientHomePage::where('subdomain', Auth::guard('client')->user()->subdomain)->first();
        $testimonials = ClientTestimonial::where('client_id', Auth::guard('client')->user()->id)->get();
        $clientTeam = ClientTeam::where('client_id', Auth::guard('client')->user()->id)->get();
        $clientCustomers = ClientCustomer::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.home', compact('subdomain', 'testimonials', 'clientTeam', 'clientCustomers','onlineCourses', 'defaultCourse', 'defaultTest', 'onlineTestSubcategories'));
    }

    protected function updateClientHome(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientHomePage::updateClientHomePage($request);
            ClientTestimonial::updateTestimonials($request);
            ClientTeam::updateTeam($request);
            ClientCustomer::updateCustomer($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('manageClientHome');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function managePlans(){
        return view('client.plansAndBilling.plans', compact('isBillPaid'));
    }

    protected function manageBillings(){
        $dueDate = '';
        $clientPlan = ClientPlan::getLastClientPlanForBill();
        if('Credit' != $clientPlan->payment_status || 'free' != $clientPlan->payment_status){
            $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($clientPlan->end_date)));
        }
        return view('client.plansAndBilling.billing', compact('clientPlan', 'dueDate'));
    }

    protected function manageUserPayments(){
        $clientUsers = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        return view('client.plansAndBilling.userPayments', compact('clientUsers'));
    }

    protected function manageHistory(){
        $clientPlans = ClientPlan::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.plansAndBilling.history', compact('clientPlans'));
    }

    protected function getClientUserPayments(Request $request){
        $results = [];
        $total = 0;
        $userCourses = ClientUserPurchasedCourse::getClientUserPurchasedCourses(Auth::guard('client')->user()->id, $request->get('client_user_id'));
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                $results['purchased'][] = [
                                'type' => 'Course',
                                'name' => $userCourse->course->name,
                                'amount' => $userCourse->price,
                                'date' => $userCourse->updated_at
                            ];
                $total += $userCourse->price;
            }
        }

        $userTestSubCategories = ClientUserPurchasedTestSubCategory::getClientUserPurchasedTestSubCategories(Auth::guard('client')->user()->id, $request->get('client_user_id'));
        if(is_object($userTestSubCategories) && false == $userTestSubCategories->isEmpty()){
            foreach($userTestSubCategories as $userTestSubCategory){
                $results['purchased'][] = [
                                'type' => 'SubCategory',
                                'name' => $userTestSubCategory->testSubCategory->name,
                                'amount' => $userTestSubCategory->price,
                                'date' => $userTestSubCategory->updated_at
                            ];
                $total += $userTestSubCategory->price;
            }
        }

        $results['total'] = $total;
        return $results;
    }

    protected function continuePayment(Request $request){
        $name = Auth::guard('client')->user()->name;
        $phone = Auth::guard('client')->user()->phone;
        $email = Auth::guard('client')->user()->email;
        $plan = Plan::find($request->get('plan_id'));
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::find($request->get('client_plan_id'));
            if(is_object($currentPlan)){
                $finalAmount = $currentPlan->final_amount;
            }

            if( 0 >= $finalAmount ){
                return redirect('managePlans')->withErrors('something went wrong.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }

        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::save();

        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => $purpose,
                "amount" => $finalAmount,
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
            return redirect('managePlans')->withErrors([$e->getMessage()]);
        }
    }

    protected function degradePayment(Request $request){
        $name = Auth::guard('client')->user()->name;
        $phone = Auth::guard('client')->user()->phone;
        $email = Auth::guard('client')->user()->email;
        $plan = Plan::find($request->get('plan_id'));
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::getLastClientPlanByPlanId(Auth::guard('client')->user()->plan_id);

            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;

                if('Credit' == $currentPlan->payment_status){
                    $finalAmount = $plan->amount;
                } else {
                    $finalAmount = $plan->amount + ceil(($days*$currentPlan->plan_amount)/365);

                }
            }

            if( 0 >= $finalAmount ){
                return redirect('managePlans')->withErrors('something went wrong.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }
        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::save();

        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => $purpose,
                "amount" => $finalAmount,
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
            return redirect('managePlans')->withErrors([$e->getMessage()]);
        }
    }

    protected function upgradePayment(Request $request){
        $name = Auth::guard('client')->user()->name;
        $phone = Auth::guard('client')->user()->phone;
        $email = Auth::guard('client')->user()->email;
        $plan = Plan::find($request->get('plan_id'));
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::getLastClientPlanByPlanId(Auth::guard('client')->user()->plan_id);
            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;
                if('Credit' == $currentPlan->payment_status){
                    $finalAmount = $plan->amount - floor(((365 - $days)/365)*$currentPlan->plan_amount);
                } else {
                    $finalAmount = $plan->amount + ceil(($days*$currentPlan->plan_amount)/365);
                }
            }
            if( 0 >= $finalAmount ){
                return redirect('managePlans')->withErrors('something went wrong.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }

        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::save();
        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => $purpose,
                "amount" => $finalAmount,
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
            return redirect('managePlans')->withErrors([$e->getMessage()]);
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

                $paymentRequestId = $response['id'];
                $paymentId = $response['payments'][0]['payment_id'];
                $name = Auth::guard('client')->user()->name;
                $phone = Auth::guard('client')->user()->phone;
                $email = Auth::guard('client')->user()->email;
                $status = $response['payments'][0]['status'];
                $planId = Session::get('client_selected_plan_id');
                $finalAmount = Session::get('client_selected_plan_price');

                DB::connection('mysql')->beginTransaction();
                try
                {
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $client = Client::find(Auth::guard('client')->user()->id);
                        if( is_object($client)){
                            // for degrade
                            if($planId < $client->plan_id){
                                $currentPlan = ClientPlan::getLastClientPlanByPlanId($client->plan_id);
                                if(is_object($currentPlan)){
                                    if('Credit' == $currentPlan->payment_status){
                                        $planAmount = $finalAmount;
                                        $currentPlan->degrade_plan = 1;
                                    } else {
                                        $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                                        $days = $dateDiff->d + 1;
                                        $currentPlan->final_amount = ceil(($days*$currentPlan->plan_amount)/365);
                                        $currentPlan->payment_status = 'Credit';
                                        if(date('Y-m-d', strtotime('-1 day')) > $currentPlan->start_date){
                                            $currentPlan->end_date = date('Y-m-d', strtotime('-1 day'));
                                            $currentPlan->degrade_plan = 1;
                                        } else {
                                            $currentPlan->end_date = $currentPlan->start_date;
                                            $currentPlan->degrade_plan = 0;
                                        }

                                        $finalAmount = $finalAmount - ceil(($days*$currentPlan->plan_amount)/365);
                                        $planAmount = $finalAmount;

                                        $client->plan_id = $planId;
                                        $client->save();
                                        DB::connection('mysql2')->commit();
                                    }
                                    $currentPlan->save();

                                    // add new record for degrade
                                    $startDate = date('Y-m-d', strtotime('+1 day', strtotime($currentPlan->end_date)));
                                    $endDate = date('Y-m-d', strtotime('+1 year', strtotime($startDate)));
                                    $clientPlanArray = [
                                                        'client_id' => $client->id,
                                                        'plan_id' => $planId,
                                                        'plan_amount' => $planAmount,
                                                        'final_amount' => $finalAmount,
                                                        'start_date' => $startDate,
                                                        'end_date' => $endDate,
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
                            } else if($planId > $client->plan_id){
                                // upgrade
                                $lastPlanId = $client->plan_id;

                                $client->plan_id = $planId;
                                $client->save();
                                DB::connection('mysql2')->commit();

                                $plan = Plan::find($planId);
                                if(is_object($plan)){
                                    $planPrice = $plan->amount;
                                } else {
                                    $planPrice = $finalAmount;
                                }

                                $currentPlan = ClientPlan::getLastClientPlanByPlanId($lastPlanId);
                                if(is_object($currentPlan)){
                                    // -1 day for last plan
                                    $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                                    $days = $dateDiff->d  + 1;
                                    if(date('Y-m-d', strtotime('-1 day')) > $currentPlan->start_date){
                                        $currentPlan->end_date = date('Y-m-d', strtotime('-1 day'));
                                    } else {
                                        $currentPlan->end_date = $currentPlan->start_date;
                                    }
                                    $currentPlan->final_amount = ceil(($days*$currentPlan->plan_amount)/365);

                                    if(('Credit' != $currentPlan->payment_status || 'free' != $currentPlan->payment_status) && 0 > $currentPlan->plan_amount){
                                        $finalAmount = $finalAmount - ceil(($days*$currentPlan->plan_amount)/365);
                                        $currentPlan->payment_status = 'Credit';
                                    }
                                    $currentPlan->save();

                                    // add new record for continue
                                    $startDate = date('Y-m-d');
                                    $endDate = date('Y-m-d', strtotime('+1 year'));
                                    $clientPlanArray = [
                                                        'client_id' => $client->id,
                                                        'plan_id' => $planId,
                                                        'plan_amount' => $planPrice,
                                                        'final_amount' => $finalAmount,
                                                        'start_date' => $startDate,
                                                        'end_date' => $endDate,
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
                            } else {
                                // continue or bill payment

                                $plan = Plan::find($planId);
                                if(is_object($plan)){
                                    $planPrice = $plan->amount;
                                } else {
                                    $planPrice = $finalAmount;
                                }

                                $currentPlan = ClientPlan::getLastClientPlanByPlanId($planId);
                                if(is_object($currentPlan)){
                                    // -1 day for last plan
                                    $currentPlan->payment_status = 'Credit';
                                    $currentPlan->save();

                                    $paymentArray = [
                                                        'client_plan_id' => $currentPlan->id,
                                                        'payment_id' => $paymentId,
                                                        'payment_request_id' => $paymentRequestId,
                                                        'status' => $status
                                                    ];
                                    Payment::addPayment($paymentArray);
                                    DB::connection('mysql')->commit();
                                }
                            }
                        }
                    }
                    catch(Exception $e)
                    {
                        DB::connection('mysql')->rollback();
                        DB::connection('mysql2')->rollback();
                        return redirect('managePlans')->withErrors([$e->getMessage()]);
                    }

                    return redirect('managePlans')->with('message', 'Thank you for paying. Your Payment has been successfully processed.');
                }
                catch(Exception $e)
                {
                    DB::connection('mysql')->rollback();
                    DB::connection('mysql2')->rollback();
                    return redirect('managePlans')->withErrors([$e->getMessage()]);
                }
            }
        }
        catch (Exception $e) {
            return redirect('managePlans')->withErrors([$e->getMessage()]);
        }
    }

    protected function webhook(Request $request){
        $data = $_POST;
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
            $message .= '<p><b>ID:</b> '.$data['payment_id'].'</p>';
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
            $message .= '<p><b>Amount:</b> '.$data['amount'].'</p>';
            $message .= "<p>Thank</p>";
            $message .= "<p>Vchipedu</p>";

            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            // send email
            mail($to, $subject, $message, $headers);
        }
    }

    protected function deactivatePlan(){
        DB::connection('mysql')->beginTransaction();
        try
        {
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $currentPlan = ClientPlan::getLastClientPlanForBill();
                if('' == $currentPlan->payment_status){
                    $currentPlan->payment_status = 'Deactivate';
                    if(date('Y-m-d', strtotime('-1 day')) > $currentPlan->start_date){
                        $currentPlan->end_date = date('Y-m-d', strtotime('-1 day'));
                    } else {
                        $currentPlan->end_date = $currentPlan->start_date;
                    }
                    $currentPlan->save();

                    $client = Client::find(Auth::guard('client')->user()->id);
                    if( is_object($client)){
                        $client->plan_id = 1;
                        $client->save();
                        DB::connection('mysql2')->commit();
                    }
                    // add new record
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime('+1 year'));
                    $clientPlanArray = [
                                        'client_id' => $client->id,
                                        'plan_id' => 1,
                                        'plan_amount' => 0,
                                        'final_amount' => 0,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'payment_status' => 'free',
                                        'degrade_plan' => 0
                                    ];
                    ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                    DB::connection('mysql')->commit();
                    return redirect('managePlans')->with('message', 'Your are successfully de-activate plan.');
                }
            }
            catch(Exception $e)
            {
                DB::connection('mysql')->rollback();
                DB::connection('mysql2')->rollback();
                return redirect('managePlans')->withErrors([$e->getMessage()]);
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql')->rollback();
            DB::connection('mysql2')->rollback();
            return redirect('managePlans')->withErrors([$e->getMessage()]);
        }
    }

    protected function manageBankDetails(){
        $bankDetail = BankDetail::where('client_id', Auth::guard('client')->user()->id)->first();
        if(!is_object($bankDetail)){
            $bankDetail = new BankDetail;
        }
        return view('client.plansAndBilling.bankDetails', compact('bankDetail'));
    }

    protected function updateBankDetails(Request $request){
        $instamojoErrors = '';
        $userAuth = UserBasedAuthentication::where('vchip_client_id', Auth::guard('client')->user()->id)->first();
        if(is_object($userAuth)){
            $instamojoClientId = $userAuth->instamojo_client_id;
            $userAccessToken = $userAuth->access_token;
            $userTokenType = $userAuth->token_type;

            $postFields = [
                            'account_holder_name' => $request->account_holder_name,
                            'account_number' => $request->account_number,
                            'ifsc_code' => $request->ifsc_code
                        ];
            if('local' == \Config::get('app.env')){
                $bankUrl = "https://test.instamojo.com/v2/users/".$instamojoClientId."/inrbankaccount/";
            } else {
                $bankUrl = "https://api.instamojo.com/v2/users/".$instamojoClientId."/inrbankaccount/";
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $bankUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 60,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "PUT",
              CURLOPT_POSTFIELDS => $postFields,
              CURLOPT_HTTPHEADER => array(
                "authorization: ".$userTokenType." ".$userAccessToken,
                "cache-control: no-cache",
                "content-type: multipart/form-data"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $instamojoErrors .= 'bank_details_error'. (string) $err;
            } else {
                $results = json_decode($response, true);
                if(!empty($results['account_number']) && !empty($results['ifsc_code'])){
                    BankDetail::updateBankDetails($request);
                } else {
                    if(count($results) > 0){
                        $instamojoErrors.= '--------application_auth_error--------';
                        foreach($results as $key => $result){
                            $instamojoErrors.= 'user -'.Auth::guard('client')->user()->email.'->'.$key.'->'.$result[0];
                        }
                    }
                }
            }
            if(!empty($instamojoErrors)){
                Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoErrors));
            }
        }
        return redirect('manageBankDetails');
    }
}