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
use App\Models\PayableClientSubCategory;
use App\Models\ClientChatMessage;
use App\Models\ClientReceipt;
use App\Models\ClientOfflinePayment;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
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
        // $this->middleware('client');
        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();

        if(is_object($subdomain)){
            view::share('subdomain', $subdomain);
            $client = Client::where('subdomain', $subdomain->subdomain)->first();
            if(is_object($client)){
                view::share('client', $client);
            }
        }
    }

    protected function showDashBoard($subdomainName){
        return view('client.dashboard',compact('subdomainName'));
    }

    protected function manageClientHome($subdomainName){
        $loginUser = Auth::guard('client')->user();
        if(!is_object($loginUser)){
            return Redirect::to('myprofile');
        }
        $onlineCourses = ClientOnlineCourse::getCurrentCoursesByClient($loginUser->subdomain);
        $defaultCourse = ClientOnlineCourse::where('name', 'How to use course')->first();
        $defaultTest = ClientOnlineCourse::where('name', 'How to use test')->first();

        $onlineTestSubcategories = ClientOnlineTestSubCategory::getCurrentSubCategoriesAssociatedWithQuestion($loginUser->subdomain);
        $subdomain = ClientHomePage::where('subdomain', $loginUser->subdomain)->first();
        $testimonials = ClientTestimonial::where('client_id', $loginUser->id)->get();
        $clientTeam = ClientTeam::where('client_id', $loginUser->id)->get();
        $clientCustomers = ClientCustomer::where('client_id', $loginUser->id)->get();
        return view('client.home', compact('subdomain', 'testimonials', 'clientTeam', 'clientCustomers','onlineCourses', 'defaultCourse', 'defaultTest', 'onlineTestSubcategories', 'subdomainName'));
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

    protected function managePlans($subdomainName){
        $allPlan = [];
        $plans = Plan::all();
        if(is_object($plans) && false == $plans->isEmpty()){
            foreach($plans as $plan){
                $allPlan[$plan->id] = $plan;
            }
        }
        $existingAmount = 0;
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $currentPlan = ClientPlan::getClientPlanByPlanId($loginUser->plan_id);
            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;
                $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                if('Credit' == $currentPlan->payment_status){
                    if($planTotalDays > 0){
                        $existingAmount = -floor((($planTotalDays - $days)/$planTotalDays)*$currentPlan->plan_amount);
                    } else {
                        $existingAmount = -$currentPlan->plan_amount;
                    }
                } else {
                    $existingAmount = +ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                }
            }
            return view('client.plansAndBilling.plans', compact('allPlan', 'subdomainName','existingAmount'));
        }
        return redirect('/');
    }

    protected function manageBillings($subdomainName){
        $dueDate = '';
        $clientPlan = ClientPlan::getLastClientPlanForBill();
        if(is_object($clientPlan) && ('Credit' != $clientPlan->payment_status || 'free' != $clientPlan->payment_status)){
            $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($clientPlan->end_date)));
        }
        return view('client.plansAndBilling.billing', compact('clientPlan', 'dueDate', 'subdomainName'));
    }

    protected function manageUserPayments($subdomainName){
        $clientUsers = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        return view('client.plansAndBilling.userPayments', compact('clientUsers', 'subdomainName'));
    }

    protected function manageHistory($subdomainName){
        $client = Auth::guard('client')->user();
        $clientPlans = ClientPlan::where('client_id', $client->id)->orderBy('id')->get();
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdForAdmin($client->id);
        return view('client.plansAndBilling.history', compact('clientPlans','payableSubCategories', 'subdomainName'));
    }

    protected function getClientUserPayments(Request $request){
        $results = [];
        $total = 0;
        $loginUser = Auth::guard('client')->user();
        $userCourses = ClientUserPurchasedCourse::getClientUserPurchasedCourses($loginUser->id, $request->get('client_user_id'));
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                $results['purchased'][] = [
                                'id' => $userCourse->id,
                                'user' => $userCourse->clientUser(),
                                'type' => 'Course',
                                'name' => $userCourse->course,
                                'amount' => $userCourse->price,
                                'date' => $userCourse->updated_at
                            ];
                $total += $userCourse->price;
            }
        }

        $userTestSubCategories = ClientUserPurchasedTestSubCategory::getClientUserPurchasedTestSubCategories($loginUser->id, $request->get('client_user_id'));
        if(is_object($userTestSubCategories) && false == $userTestSubCategories->isEmpty()){
            foreach($userTestSubCategories as $userTestSubCategory){
                $results['purchased'][] = [
                                'id' => $userTestSubCategory->id,
                                'user' => $userTestSubCategory->clientUser(),
                                'type' => 'SubCategory',
                                'name' => $userTestSubCategory->test_sub_category,
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
        $loginUser = Auth::guard('client')->user();
        $name = $loginUser->name;
        $phone = $loginUser->phone;
        $email = $loginUser->email;

        $planType = $request->get('plan_type');
        $planId = (int)$request->get('plan_id');
        $planMonthlyOrYearly = $request->get('plan_'.$planType.'_'.$planId);
        $total = $request->get('total');
        $duration = $request->get('duration');

        $plan = Plan::find($planId);
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::getClientPlanByPlanId(Auth::guard('client')->user()->plan_id);
            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;
                $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                if('Credit' == $currentPlan->payment_status){
                    if(1 == $planMonthlyOrYearly){
                        // yearly
                        $finalAmount = $plan->amount * $duration;
                    } else {
                        //monthly
                        $finalAmount = $plan->monthly_amount * $duration;
                    }
                } else {
                    if($planTotalDays > 0){
                        if(1 == $planMonthlyOrYearly){
                            // yearly
                            $calculatedPlanTotal = $plan->amount * $duration;
                            $finalAmount = $calculatedPlanTotal + ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                        } else {
                            // monthly
                            $calculatedPlanTotal = $plan->monthly_amount * $duration;
                            $finalAmount = $calculatedPlanTotal + ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                        }
                    } else {
                        if(1 == $planMonthlyOrYearly){
                            // yearly
                            $finalAmount = $plan->amount * $duration;
                        } else {
                            // monthly
                            $finalAmount = $plan->monthly_amount * $duration;
                        }
                    }
                }
            }
            if( $total != $finalAmount ){
                return redirect('managePlans')->withErrors('final amount calculation is wrong for continue plan.');
            } elseif( 0 >= $finalAmount ){
                return redirect('managePlans')->withErrors('something went wrong for final amount for continue plan.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }

        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::put('client_selected_plan_type', $planMonthlyOrYearly);
        Session::put('client_selected_plan_duration', $duration);
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
        $loginUser = Auth::guard('client')->user();
        $name = $loginUser->name;
        $phone = $loginUser->phone;
        $email = $loginUser->email;

        $planType = $request->get('plan_type');
        $planId = (int)$request->get('plan_id');
        $planMonthlyOrYearly = $request->get('plan_'.$planType.'_'.$planId);
        $total = $request->get('total');
        $duration = $request->get('duration');
        $plan = Plan::find($planId);
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::getClientPlanByPlanId(Auth::guard('client')->user()->plan_id);
            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;
                $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                if('Credit' == $currentPlan->payment_status){
                    if(1 == $planMonthlyOrYearly){
                        // yearly
                        $finalAmount = $plan->amount * $duration;
                    } else {
                        //monthly
                        $finalAmount = $plan->monthly_amount * $duration;
                    }
                } else {
                    if($planTotalDays > 0){
                        if(1 == $planMonthlyOrYearly){
                            // yearly
                            $calculatedPlanTotal = $plan->amount * $duration;
                            $finalAmount = $calculatedPlanTotal + ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                        } else {
                            // monthly
                            $calculatedPlanTotal = $plan->monthly_amount * $duration;
                            $finalAmount = $calculatedPlanTotal + ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                        }
                    } else {
                        if(1 == $planMonthlyOrYearly){
                            // yearly
                            $finalAmount = $plan->amount * $duration;
                        } else {
                            // monthly
                            $finalAmount = $plan->monthly_amount * $duration;
                        }
                    }
                }
            }
            if( $total != $finalAmount ){
                return redirect('managePlans')->withErrors('final amount calculation is wrong for degrade plan.');
            } elseif( 0 >= $finalAmount ){
                return redirect('managePlans')->withErrors('something went wrong for final amount for degrade plan.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }
        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::put('client_selected_plan_type', $planMonthlyOrYearly);
        Session::put('client_selected_plan_duration', $duration);
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
        $loginUser = Auth::guard('client')->user();
        $name = $loginUser->name;
        $phone = $loginUser->phone;
        $email = $loginUser->email;
        $planType = $request->get('plan_type');
        $planId = (int)$request->get('plan_id');
        $planMonthlyOrYearly = $request->get('plan_'.$planType.'_'.$planId);
        $planTotal = $request->get('plan_total');
        $existingPlanTotal = $request->get('existing_plan_total');
        $total = $request->get('total');
        $duration = $request->get('duration');
        $plan = Plan::find($planId);
        $finalAmount = 0;
        if(is_object($plan)){
            $currentPlan = ClientPlan::getClientPlanByPlanId($loginUser->plan_id);
            if(is_object($currentPlan)){
                $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                $days = $dateDiff->d + 1;
                $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                if('Credit' == $currentPlan->payment_status){
                    if(1 == $planMonthlyOrYearly){
                        // yearly
                        $calculatedPlanTotal = $plan->amount * $duration;
                        if($planTotalDays > 0){
                            $finalAmount = $calculatedPlanTotal -floor((($planTotalDays - $days)/$planTotalDays)*$currentPlan->plan_amount);
                        } else {
                            $finalAmount = $calculatedPlanTotal -$currentPlan->plan_amount;
                        }
                    } else {
                        // monthly
                        $calculatedPlanTotal = $plan->monthly_amount * $duration;
                        if($planTotalDays > 0){
                            $finalAmount = $calculatedPlanTotal -floor((($planTotalDays - $days)/$planTotalDays)*$currentPlan->plan_amount);
                        } else {
                            $finalAmount = $calculatedPlanTotal -$currentPlan->plan_amount;
                        }
                    }
                } else {
                    if(1 == $planMonthlyOrYearly){
                        // yearly
                        $calculatedPlanTotal = $plan->amount * $duration;
                    } else {
                        // monthly
                        $calculatedPlanTotal = $plan->monthly_amount * $duration;
                    }
                    $finalAmount = $calculatedPlanTotal + ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                }
                if($finalAmount < 10){
                    $finalAmount = 10;
                }
            }
            if( $total != $finalAmount ){
                return redirect('managePlans')->withErrors('final amount calculation is wrong.');
            }
        } else {
            return redirect('managePlans')->withErrors('something went wrong.');
        }

        $purpose = 'register for '.$plan->name;
        Session::put('client_selected_plan_id', $plan->id);
        Session::put('client_selected_plan_price', $finalAmount);
        Session::put('client_selected_plan_type', $planMonthlyOrYearly);
        Session::put('client_selected_plan_duration', $duration);
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
                $loginUser = Auth::guard('client')->user();
                $name = $loginUser->name;
                $phone = $loginUser->phone;
                $email = $loginUser->email;
                $status = $response['payments'][0]['status'];
                $planId = Session::get('client_selected_plan_id');
                $finalAmount = Session::get('client_selected_plan_price');
                $planType = Session::get('client_selected_plan_type');
                $planDuration = Session::get('client_selected_plan_duration');

                DB::connection('mysql')->beginTransaction();
                try
                {
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $client = Client::find($loginUser->id);
                        if( is_object($client)){
                            // for degrade
                            if($planId < $client->plan_id){
                                $currentPlan = ClientPlan::getLastClientPlan();
                                if(is_object($currentPlan)){
                                    if('Credit' == $currentPlan->payment_status){
                                        $planAmount = $finalAmount;
                                        $currentPlan->degrade_plan = 1;
                                    } else {
                                        $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                                        $days = $dateDiff->d + 1;
                                        $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                                        if($planTotalDays > 0){
                                            $currentPlan->final_amount = ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                                        } else {
                                            $currentPlan->final_amount = $currentPlan->plan_amount;
                                        }
                                        $currentPlan->payment_status = 'Credit';
                                        if(date('Y-m-d', strtotime('-1 day')) > $currentPlan->start_date){
                                            $currentPlan->end_date = date('Y-m-d', strtotime('-1 day'));
                                            $currentPlan->degrade_plan = 1;
                                        } else {
                                            $currentPlan->end_date = $currentPlan->start_date;
                                            $currentPlan->degrade_plan = 0;
                                        }
                                        if($planTotalDays > 0){
                                            $finalAmount = $finalAmount - ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                                        } else {
                                            $finalAmount = $finalAmount;
                                        }
                                        $planAmount = $finalAmount;

                                        $client->plan_id = $planId;
                                        $client->save();
                                        DB::connection('mysql2')->commit();
                                    }
                                    $currentPlan->save();

                                    // add new record for degrade
                                    $startDate = date('Y-m-d', strtotime('+1 day', strtotime($currentPlan->end_date)));
                                    if(1 == $planType){
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' years', strtotime($startDate)));
                                    } else {
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' months', strtotime($startDate)));
                                    }
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
                                    if(1 == $planType){
                                        $planPrice = $plan->amount * $planDuration;
                                    } else {
                                        $planPrice = $plan->monthly_amount * $planDuration;
                                    }
                                } else {
                                    $planPrice = $finalAmount;
                                }

                                $currentPlan = ClientPlan::getClientPlanByPlanId($lastPlanId);
                                if(is_object($currentPlan)){
                                    // -1 day for last plan
                                    $dateDiff = date_diff( new DateTime(date('Y-m-d')), new DateTime($currentPlan->start_date));
                                    $days = $dateDiff->d  + 1;
                                    $planTotalDays = date_diff(new DateTime($currentPlan->end_date),new DateTime($currentPlan->start_date))->days;
                                    if(date('Y-m-d', strtotime('-1 day')) > $currentPlan->start_date){
                                        $currentPlan->end_date = date('Y-m-d', strtotime('-1 day'));
                                    } else {
                                        $currentPlan->end_date = $currentPlan->start_date;
                                    }

                                    if($planTotalDays > 0){
                                        $currentPlan->final_amount = ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                                    } else {
                                        $currentPlan->final_amount = $currentPlan->final_amount;
                                    }

                                    if(('Credit' != $currentPlan->payment_status || 'free' != $currentPlan->payment_status) && 0 > $currentPlan->plan_amount){
                                        $finalAmount = $finalAmount - ceil(($days*$currentPlan->plan_amount)/$planTotalDays);
                                        $currentPlan->payment_status = 'Credit';
                                    }
                                    $currentPlan->save();

                                    // add new record for continue
                                    $startDate = date('Y-m-d');
                                    if(1 == $planType){
                                        // yearly
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' years'));
                                    } else {
                                        //monthly
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' months'));
                                    }
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

                                $currentPlan = ClientPlan::getLastClientPlan();
                                if(is_object($currentPlan)){
                                    // add new record for degrade
                                    $startDate = date('Y-m-d', strtotime('+1 day', strtotime($currentPlan->end_date)));
                                    if(1 == $planType){
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' years', strtotime($startDate)));
                                    } else {
                                        $endDate = date('Y-m-d', strtotime('+'.$planDuration.' months', strtotime($startDate)));
                                    }
                                    $clientPlanArray = [
                                                        'client_id' => $client->id,
                                                        'plan_id' => $planId,
                                                        'plan_amount' => $finalAmount,
                                                        'final_amount' => $finalAmount,
                                                        'start_date' => $startDate,
                                                        'end_date' => $endDate,
                                                        'payment_status' => $status,
                                                        'degrade_plan' => 0
                                                    ];
                                    $clientPlan = ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                                    if(is_object($clientPlan)){
                                        $paymentArray = [
                                                            'client_plan_id' => $currentPlan->id,
                                                            'payment_id' => $paymentId,
                                                            'payment_request_id' => $paymentRequestId,
                                                            'status' => $status
                                                        ];
                                        Payment::addPayment($paymentArray);
                                    }
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

    protected function manageBankDetails($subdomainName){
        $bankDetail = BankDetail::where('client_id', Auth::guard('client')->user()->id)->first();
        if(!is_object($bankDetail)){
            $bankDetail = new BankDetail;
        }
        return view('client.plansAndBilling.bankDetails', compact('bankDetail', 'subdomainName'));
    }

    protected function updateBankDetails(Request $request){
        $instamojoErrors = '';
        $loginUser = Auth::guard('client')->user();
        $userAuth = UserBasedAuthentication::where('vchip_client_id', $loginUser->id)->first();
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
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        BankDetail::updateBankDetails($request);
                        DB::connection('mysql2')->commit();
                        return redirect('manageBankDetails')->with('message', 'Bank Details updated successfully.');
                    }
                    catch(Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return redirect('manageBankDetails')->withErrors([$e->getMessage()]);
                    }
                } else {
                    if(count($results) > 0){
                        $instamojoErrors.= '--------application_auth_error--------';
                        foreach($results as $key => $result){
                            $instamojoErrors.= 'user -'.$loginUser->email.'->'.$key.'->'.$result[0];
                        }
                    }
                }
            }
            if(!empty($instamojoErrors)){
                Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoErrors));
                return redirect('manageBankDetails')->withErrors(['error while update bank detials']);
            }
        }
        return redirect('manageBankDetails');
    }

    protected function searchContact($subDomainName, Request $request){
        return Clientuser::searchContact($subDomainName,$request);
    }

    protected function allChatMessages($subdomainName){
        $result = ClientChatMessage::showClientChatUsers($subdomainName);
        if(isset($result['chatusers'])){
            $users = $result['chatusers'];
        } else {
            $users = [];
        }
        if(isset($result['unreadCount'])){
            $unreadCount = $result['unreadCount'];
        }
        $onlineUsers = $result['onlineUsers'];
        return view('client.allChatMessages', compact('users', 'unreadCount', 'onlineUsers', 'subdomainName'));
    }

    protected function dashboardPrivateChat(Request $request){
        return ClientChatMessage::privatechat($request);
    }

    protected function dashboardSendMessage(Request $request){
        return ClientChatMessage::sendMessage($request);
    }

    protected function getContacts($subdomainName){
        return ClientChatMessage::showClientChatUsers($subdomainName);
    }

    protected function showPurchaseSms($subdomainName){
        return view('client.plansAndBilling.sms', compact('subdomainName'));
    }

    protected function clientPurchaseSms(Request $request){
        $smsCount = $request->get('sms_count');
        $total = $request->get('total');
        if(!empty($smsCount) && !empty($total)){
            if(!(($total/150) == ($smsCount/1000))){
                return redirect()->back()->withErrors('something went wrong in sms calculation.');
            } else {
                $loginUser = Auth::guard('client')->user();
                $name = $loginUser->name;
                $phone = $loginUser->phone;
                $email = $loginUser->email;

                $purpose = 'purchase '.$smsCount.' sms';
                Session::put('client_purchase_sms', $smsCount);
                Session::put('client_total', $total);
                Session::save();
                if('local' == \Config::get('app.env')){
                    $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
                } else {
                    $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
                }
                try {
                    $response = $api->paymentRequestCreate(array(
                        "purpose" => $purpose,
                        "amount" => $total,
                        "buyer_name" => $name,
                        "phone" => $phone,
                        "send_email" => true,
                        "send_sms" => true,
                        "email" => $email,
                        'allow_repeated_payments' => false,
                        "redirect_url" => url('thankyouClientPurchaseSms'),
                        "webhook" => url('webhookClientPurchaseSms')
                        ));

                    $pay_ulr = $response['longurl'];
                    header("Location: $pay_ulr");
                    exit();
                }
                catch (Exception $e) {
                    return redirect('managePurchaseSms')->withErrors([$e->getMessage()]);
                }
            }
        }
        return redirect()->back();
    }

    protected function thankyouClientPurchaseSms(Request $request){
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
                $loginUser = Auth::guard('client')->user();
                $name = $loginUser->name;
                $phone = $loginUser->phone;
                $email = $loginUser->email;
                $status = $response['payments'][0]['status'];
                $purchasedSms = Session::get('client_purchase_sms');
                $total = Session::get('client_total');
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $startDate = date('Y-m-d');
                    $endDate = '2050-01-01';
                    $client = Client::find($loginUser->id);
                    if( is_object($client)){
                        $smsArray = [
                                        'client_id' => $client->id,
                                        'total' => $total,
                                        'payment_id' => $paymentId,
                                        'payment_request_id' => $paymentRequestId,
                                        'purcahsed_sms' => $purchasedSms,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                    ];
                        PayableClientSubCategory::addClientPurchasedSms($smsArray);
                        $client->debit_sms_count = ($client->debit_sms_count + $purchasedSms) - $client->credit_sms_count;
                        $client->credit_sms_count = 0;
                        $client->save();
                        DB::connection('mysql2')->commit();
                        return redirect('managePurchaseSms')->with('message', 'Thank you for purcahseing sms.');
                    }
                }
                catch(Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect('managePurchaseSms')->withErrors([$e->getMessage()]);
                }
            }
        }
        catch (Exception $e) {
            return redirect('managePurchaseSms')->withErrors([$e->getMessage()]);
        }
        return redirect('managePurchaseSms');
    }

    protected function webhookClientPurchaseSms(Request $request){
        return;
    }

    protected function onlineReceiptShow($subdomainName,$type,$id){
        if(is_object(Auth::guard('client')->user())){
            $loginUser = Auth::guard('client')->user();
            $clientId = $loginUser->id;
            $clientName = $loginUser->name;
        } else {
            $loginUser = Auth::guard('clientuser')->user();
            $client = $loginUser->client;
            $clientId = $client->id;
            $clientName = $client->name;
        }
        $onlineReceipt = '';
        if('Course' == $type){
            $userCourse = ClientUserPurchasedCourse::getUserPurchasedCourseByClientIdById($clientId, $id);
            if(is_object($userCourse)){
                $onlineReceipt= [
                        'id' => $userCourse->id,
                        'user' => $userCourse->clientUser(),
                        'user_id' => $userCourse->user_id,
                        'type' => 'Course',
                        'name' => $userCourse->course,
                        'amount' => $userCourse->price,
                        'date' => (!empty($userCourse->updated_at))?$userCourse->updated_at:date('Y-m-d'),
                    ];
            }
        } elseif('SubCategory' == $type){
            $userTestSubCategory = ClientUserPurchasedTestSubCategory::getUserPurchasedTestSubCategoryByClientIdById($clientId, $id);
            if(is_object($userTestSubCategory)){
                $onlineReceipt = [
                        'id' => $userTestSubCategory->id,
                        'user' => $userTestSubCategory->clientUser(),
                        'user_id' => $userTestSubCategory->user_id,
                        'type' => 'SubCategory',
                        'name' => $userTestSubCategory->test_sub_category,
                        'amount' => $userTestSubCategory->price,
                        'date' => (!empty($userTestSubCategory->updated_at))?$userTestSubCategory->updated_at:date('Y-m-d'),
                    ];
            }
        } else {
            return Redirect::to('manageUserPayments');
        }
        $clientReceipt = ClientReceipt::find($clientId);
        if(is_object($clientReceipt)){
            if(1 == $clientReceipt->is_same_details){
                $onLineReceiptBy = $clientReceipt->offline_receipt_by;
                $onLineGstin = $clientReceipt->offline_gstin;
                $onLineCin = $clientReceipt->offline_cin;
                $onLinePan = $clientReceipt->offline_pan;
                $isOnlineGstApplied = $clientReceipt->is_offline_gst_applied;
                $onLineAddress = $clientReceipt->offline_address;
            } else {
                $onLineReceiptBy = $clientReceipt->online_receipt_by;
                $onLineGstin = $clientReceipt->online_gstin;
                $onLineCin = $clientReceipt->online_cin;
                $onLinePan = $clientReceipt->online_pan;
                $isOnlineGstApplied = $clientReceipt->is_online_gst_applied;
                $onLineAddress = $clientReceipt->online_address;
            }
            $hsnSac = (!empty($clientReceipt->hsn_sac))?$clientReceipt->hsn_sac:'NA';
        } else {
            $onLineGstin = '';
            $onLineReceiptBy = $clientName;
            $onLineCin = '';
            $onLinePan = '';
            $isOnlineGstApplied = 0;
            $onLineAddress = '';
            $hsnSac = 'NA';
        }

        if(is_array($onlineReceipt)){
            $onlineReceiptArr = [
                'receipt_id' => $onlineReceipt['id'],
                'name' => $onlineReceipt['user'],
                'user_id' => $onlineReceipt['user_id'],
                'batch' => $onlineReceipt['name'],
                'amount' => $onlineReceipt['amount'],
                'type' => $onlineReceipt['type'],
                'is_online_gst_applied' => $isOnlineGstApplied,
                'receipt_by' => $onLineReceiptBy,
                'date' => date('d-m-Y',strtotime($onlineReceipt['date'])),
                'gstin' => $onLineGstin,
                'cin' =>  $onLineCin,
                'pan' =>  $onLinePan,
                'address' =>  $onLineAddress,
                'hsnSac' =>  $hsnSac
            ];

            $html = $this->createOnlinePdfHtml($onlineReceiptArr);
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','tempDir' => __DIR__ . '/../mpdfFont']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html, 2);
            return  $mpdf->Output("receipt-".$onlineReceiptArr['receipt_id'].".pdf", "I");
        }
        return Redirect::to('manageUserPayments');
    }

    protected function createOnlinePdfHtml($onlineReceiptArr){
        $numberFormatter = new \NumberFormatter( locale_get_default(), \NumberFormatter::SPELLOUT );
        $amountInWords = $numberFormatter->format($onlineReceiptArr['amount']);

        $tbl = '<table border="1" cellpadding="0" cellspacing="0" width="100%">';
        $tbl .= '<thead>
                <tr>
                    <td colspan="12" align="center"><b>Tax Invoice</b></td>
                </tr>
            </thead>
            <tr>
                <td colspan="6" align="center"><h3>&nbsp;<u>'.$onlineReceiptArr['receipt_by'].'</u></h3><br/>&nbsp;'.$onlineReceiptArr['address'].'</td>
                <td colspan="6" align="center">Receipt No: Online '.$onlineReceiptArr['type'].'-'.$onlineReceiptArr['receipt_id'].'<br/>Date: '.$onlineReceiptArr['date'].'</td>
            </tr>';
        if(1 == $onlineReceiptArr['is_online_gst_applied']){
            $tbl .= '
                    <tr>
                        <td colspan="4" align="center">GSTIN: '.$onlineReceiptArr['gstin'].'</td>
                        <td colspan="3" align="center">CIN: '.$onlineReceiptArr['cin'].'</td>
                        <td colspan="5" align="center">PAN: '.$onlineReceiptArr['pan'].'</td>
                    </tr>';
        }
        $tbl .= '
            <tr>
                <td colspan="6" align="left">&nbsp;&nbsp;Billed To: '.$onlineReceiptArr['name'].' '.'<br> &nbsp;&nbsp;User Id: '.$onlineReceiptArr['user_id'].'<br> &nbsp;&nbsp;State Code: </td>
                <td colspan="6" align="left">&nbsp;&nbsp;Shipped To: '.$onlineReceiptArr['name'].' '.'<br> &nbsp;&nbsp;User Id: '.$onlineReceiptArr['user_id'].'<br> &nbsp;&nbsp;State Code: </td>
            </tr>
            <tr>
                <td colspan="12" align="left">&nbsp;&nbsp;Remarks:<br></td>
            </tr>';
        if(1 == $onlineReceiptArr['is_online_gst_applied']){
                $tbl .= '<tr>
                    <td colspan="1" align="center">Sr.No</td>
                    <td colspan="2" align="center">Service Supplied</td>
                    <td colspan="1" align="center">HSN/<br>SAC</td>
                    <td colspan="1" align="center">Qty</td>
                    <td colspan="1" align="center">Unit</td>
                    <td colspan="1" align="center">Rate Per Item</td>
                    <td colspan="2" align="center">Taxable Value (Rs.)</td>
                    <td colspan="3" align="center">'.round($onlineReceiptArr['amount']/1.18,2).'</td>
                </tr>
                <tr>
                    <td rowspan="3" align="center">1</td>
                    <td rowspan="3" colspan="2" align="center">Coaching for '.$onlineReceiptArr['batch'].'</td>
                    <td rowspan="3" colspan="1" align="center">'.$onlineReceiptArr['hsnSac'].'</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">'.round($onlineReceiptArr['amount']/1.18,2).'</td>
                    <td colspan="2" align="center">Add:CGST @9% (Rs.)</td>
                    <td colspan="3" align="center">'.round(($onlineReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Add:SGST @9% (Rs.)</td>
                    <td colspan="3" align="center">'.round(($onlineReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Total(Rs.)</td>
                    <td colspan="3" align="center">'.$onlineReceiptArr['amount'].'</td>
                </tr>';
        } else {
            $tbl .= '<tr>
                <td colspan="1" align="center">Sr.No</td>
                <td colspan="6" align="center">Service Supplied</td>
                <td colspan="1" align="center">Qty</td>
                <td colspan="1" align="center">Unit</td>
                <td colspan="3" align="center">Total</td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td colspan="6" align="center">Coaching for '.$onlineReceiptArr['batch'].'</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="3" align="center">'.$onlineReceiptArr['amount'].'</td>
            </tr>';
        }
        $tbl .= '<tr>
                <td colspan="12" align="left" style="text-transform:uppercase;">&nbsp;&nbsp;Ruppes: '.$amountInWords.' only </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="left"><br/><br/><br/>&nbsp;&nbsp;Customer Signature</td>
                        <td colspan="6" align="right"><br/><br/><br/>Authorised Signature &nbsp;&nbsp;</td>
                    </tr>';
        $tbl .= '
                </table>';
        return $tbl;
    }

    /**
     *  offline payment receipt
     */
    protected function offlineReceipt($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            if(is_object(Auth::guard('client')->user())){
                $loginUser = Auth::guard('client')->user();
                $clientId = $loginUser->id;
                $clientName = $loginUser->name;
            } else {
                $loginUser = Auth::guard('clientuser')->user();
                $client = $loginUser->client;
                $clientId = $client->id;
                $clientName = $client->name;
            }
            $payment = ClientOfflinePayment::where('id',$id)->where('client_id',$clientId)->first();
            if(!is_object($payment)){
                return Redirect::to('manageOfflinePayments');
            }
            $clientReceipt = ClientReceipt::find($clientId);
            if(is_object($clientReceipt)){
                $offLineReceiptBy = $clientReceipt->offline_receipt_by;
                $offLineGstin = $clientReceipt->offline_gstin;
                $offLineCin = $clientReceipt->offline_cin;
                $offLinePan = $clientReceipt->offline_pan;
                $isOfflineGstApplied = $clientReceipt->is_offline_gst_applied;
                $offLineAddress = $clientReceipt->offline_address;
                $hsnSac = (!empty($clientReceipt->hsn_sac))?$clientReceipt->hsn_sac:'NA';
            } else {
                $offLineReceiptBy = $clientName;
                $offLineGstin = '';
                $offLineCin = '';
                $offLinePan = '';
                $isOfflineGstApplied = 0;
                $offLineAddress = '';
                $hsnSac = 'NA';
            }
            if(is_object($payment)){
                $paymentUser = $payment->user;
                $userBatch = $payment->batch;
                $offlineReceiptArr = [
                    'receipt_id' => $payment->id,
                    'name' => $paymentUser->name,
                    'user_id' => $payment->clientuser_id,
                    'batch' => $userBatch->name,
                    'amount' => $payment->amount,
                    'is_offline_gst_applied' => $isOfflineGstApplied,
                    'receipt_by' => $offLineReceiptBy,
                    'date' => date('d-m-Y',strtotime($payment->created_at)),
                    'gstin' => $offLineGstin,
                    'cin' =>  $offLineCin,
                    'pan' =>  $offLinePan,
                    'address' =>  $offLineAddress,
                    'hsnSac' =>  $hsnSac
                ];

                $html = $this->createOfflinePdfHtml($offlineReceiptArr);
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','tempDir' => __DIR__ . '/../mpdfFont']);
                $mpdf->autoScriptToLang = true;
                $mpdf->autoLangToFont = true;
                $mpdf->WriteHTML($html, 2);
                return  $mpdf->Output("receipt-".$payment->id.".pdf", "I");
            }
        }
        return Redirect::to('manageOfflinePayments');
    }

    protected function createOfflinePdfHtml($offlineReceiptArr){
        $numberFormatter = new \NumberFormatter( locale_get_default(), \NumberFormatter::SPELLOUT );
        $amountInWords = $numberFormatter->format($offlineReceiptArr['amount']);

        $tbl = '<table border="1" cellpadding="0" cellspacing="0" width="100%">';
        $tbl .= '<thead>
                <tr>
                    <td colspan="12" align="center"><b>Tax Invoice</b></td>
                </tr>
            </thead>
            <tr>
                <td colspan="6" align="center"><h3>&nbsp;<u>'.$offlineReceiptArr['receipt_by'].'</u></h3><br/>&nbsp;'.$offlineReceiptArr['address'].'</td>
                <td colspan="6" align="center">Receipt No: Offline Receipt-'.$offlineReceiptArr['receipt_id'].'<br/>Date: '.$offlineReceiptArr['date'].'</td>
            </tr>';
        if(1 == $offlineReceiptArr['is_offline_gst_applied']){
            $tbl .= '
                    <tr>
                        <td colspan="4" align="center">GSTIN: '.$offlineReceiptArr['gstin'].'</td>
                        <td colspan="3" align="center">CIN: '.$offlineReceiptArr['cin'].'</td>
                        <td colspan="5" align="center">PAN: '.$offlineReceiptArr['pan'].'</td>
                    </tr>';
        }
        $tbl .= '
            <tr>
                <td colspan="6" align="left">&nbsp;&nbsp;Billed To: '.$offlineReceiptArr['name'].' '.'<br> &nbsp;User Id: '.$offlineReceiptArr['user_id'].'<br> &nbsp;State Code: </td>
                <td colspan="6" align="left">&nbsp;&nbsp;Shipped To: '.$offlineReceiptArr['name'].' '.'<br> &nbsp;User Id: '.$offlineReceiptArr['user_id'].'<br> &nbsp;State Code: </td>
            </tr>
            <tr>
                <td colspan="12" align="left">&nbsp;&nbsp;Remarks:<br></td>
            </tr>';
        if(1 == $offlineReceiptArr['is_offline_gst_applied']){
                $tbl .= '<tr>
                    <td colspan="1" align="center">Sr.No</td>
                    <td colspan="2" align="center">Service Supplied</td>
                    <td colspan="1" align="center">HSN/<br>SAC</td>
                    <td colspan="1" align="center">Qty</td>
                    <td colspan="1" align="center">Unit</td>
                    <td colspan="1" align="center">Rate Per Item</td>
                    <td colspan="2" align="center">Taxable Value (Rs.)</td>
                    <td colspan="3" align="center">'.round($offlineReceiptArr['amount']/1.18,2).'</td>
                </tr>
                <tr>
                    <td rowspan="3" align="center">1</td>
                    <td rowspan="3" colspan="2" align="center">Coaching for '.$offlineReceiptArr['batch'].'</td>
                    <td rowspan="3" colspan="1" align="center">'.$offlineReceiptArr['hsnSac'].'</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">NA</td>
                    <td rowspan="3" colspan="1" align="center">'.round($offlineReceiptArr['amount']/1.18,2).'</td>
                    <td colspan="2" align="center">Add:CGST @9% (Rs.)</td>
                    <td colspan="3" align="center">'.round(($offlineReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Add:SGST @9% (Rs.)</td>
                    <td colspan="3" align="center">'.round(($offlineReceiptArr['amount']/1.18) * 0.09,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">Total(Rs.)</td>
                    <td colspan="3" align="center">'.$offlineReceiptArr['amount'].'</td>
                </tr>';
        } else {
            $tbl .= '<tr>
                <td colspan="1" align="center">Sr.No</td>
                <td colspan="6" align="center">Service Supplied</td>
                <td colspan="1" align="center">Qty</td>
                <td colspan="1" align="center">Unit</td>
                <td colspan="3" align="center">Total</td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td colspan="6" align="center">Coaching for '.$offlineReceiptArr['batch'].'</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="1" align="center">NA</td>
                <td colspan="3" align="center">'.$offlineReceiptArr['amount'].'</td>
            </tr>';
        }
        $tbl .= '<tr>
                <td colspan="12" align="left" style="text-transform:uppercase;">&nbsp;&nbsp;Ruppes: '.$amountInWords.' only </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="left"><br/><br/><br/>&nbsp;&nbsp;Customer Signature</td>
                        <td colspan="6" align="right"><br/><br/><br/>Authorised Signature &nbsp;&nbsp;</td>
                    </tr>';
        $tbl .= '
                </table>';
        return $tbl;
    }
}