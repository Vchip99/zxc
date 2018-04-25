<?php

namespace App\Http\Controllers\Client\PurchaseSubCategory;

use Illuminate\Http\Request;
use App\Http\Controllers\Instamojo;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect, Validator, Session, Auth, DB,Mail;
use App\Libraries\InputSanitise;
use App\Mail\PaymentReceived;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\PayableClientSubCategory;

class PurchaseSubCategoryController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateAssignment = [
        'subject' => 'required|integer',
        'topic' => 'required',
    ];

    protected function show(Request $request){
        $purchasedSubCategories = [];
        $testCategories = ClientOnlineTestCategory::showCategories($request);
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategoriesAssociatedWithQuestion();
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId(Auth::guard('client')->user()->id);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $purchasedSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        }
        return view('client.payableSubCategory.payable', compact('testSubCategories', 'purchasedSubCategories', 'testCategories'));
    }

    /**
     *  showPayableSubcategory
     */
    protected function showPayableSubcategory($subdomain,$id,Request $request){
        $selectedSubCategory = ClientOnlineTestSubCategory::showPayableSubcategoryById(json_decode($id));

        if(!is_object($selectedSubCategory)){
            return Redirect::to('managePurchaseSubCategory');
        }
        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategoriesAssociatedWithQuestion();
        $testSubjects = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($selectedSubCategory->id);
        $testPapers = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($selectedSubCategory->id);
        $paperQuestionCount = ClientOnlineTestQuestion::getPayableQuestionsCountBySubcategoryId($selectedSubCategory->id);
        $isTestSubCategoryPurchased = false;
        return view('client.payableSubCategory.payable_details', compact('selectedSubCategory', 'testSubCategories', 'testSubjects', 'testPapers', 'isTestSubCategoryPurchased', 'paperQuestionCount', 'testCategories'));
    }

    /**
     *  store assignment
     */
    protected function getPayableSubjectsAndPapersBySubcatIdAssociatedWithQuestion(Request $request){
        $subCategoryId = $request->subcat;
        $result['subjects']  = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($subCategoryId);
        $result['papers']  = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($subCategoryId);
        $result['questionCount'] = ClientOnlineTestQuestion::getPayableQuestionsCountBySubcategoryId($subCategoryId);
        $result['isTestSubCategoryPurchased']= false;
        return $result;
    }

    /**
     * edit assignment
     */
    protected function purchasePayableSubCategory($subdomain, Request $request){

        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory_id');
        $subcatPrice = $request->get('subcat_price');
        $duration = $request->get('duration');
        $total =  $request->get('total');
        $planPrice =  $request->get('plan_'.$subcategoryId);

        if(empty($categoryId)){
            return redirect()->back()->withErrors(['please select category.']);
        }
        if(empty($subcatPrice)){
            return redirect()->back()->withErrors(['please enter sub category price.']);
        }
        if(empty($duration)){
            return redirect()->back()->withErrors(['please enter/select duration.']);
        }
        if(empty($planPrice)){
            return redirect()->back()->withErrors(['please select plan.']);
        }
        if( 0 >= $total ){
            return redirect()->back()->withErrors(['total is 0. please select plan and duration']);
        }
        $loginUser = Auth::guard('client')->user();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        }

        $selectedSubCategory = ClientOnlineTestSubCategory::find($subcategoryId);
        if(!is_object($selectedSubCategory)){
            return Redirect::to('/');
        }
        if($planPrice == $selectedSubCategory->price){
            // yearly
            $startDate =  date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+'.$duration.' year', strtotime($startDate)));
        } else {
            // monthly
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+'.$duration.' month', strtotime($startDate)));
        }

        Session::put('payable_category_id', $categoryId);
        Session::put('payable_sub_category_id', $subcategoryId);
        Session::put('sub_category_price', $subcatPrice);
        Session::put('total', $total);
        Session::put('duration', $duration);
        Session::put('plan', $planPrice);
        Session::put('start_date', $startDate);
        Session::put('end_date', $endDate);
        Session::put('client_image', $selectedSubCategory->image_path);
        Session::put('sub_category_name', $selectedSubCategory->name);
        Session::save();

        $name = $loginUser->name;
        $phone = $loginUser->phone;
        $email = $loginUser->email;
        $purpose = 'purchased '.$selectedSubCategory->name;

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
                "redirect_url" => url('thankyouPayable'),
                "webhook" => url('webhookPayable')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();

        }
        catch (Exception $e) {
            return redirect('managePayableSubCategory')->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyouPayable(Request $request){
        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        $payid = $request->get('payment_request_id');

        try {
            $response = $api->paymentRequestStatus($payid);

            if( 'Credit' == $response['payments'][0]['status']){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    // create a client
                    $paymentRequestId = $response['id'];
                    $paymentId = $response['payments'][0]['payment_id'];
                    $email = $response['payments'][0]['buyer_email'];
                    $name = $response['payments'][0]['buyer_name'];
                    $status = $response['payments'][0]['status'];

                    $categoryId = Session::get('payable_category_id');
                    $subcategoryId = Session::get('payable_sub_category_id');
                    $subcatPrice = Session::get('sub_category_price');
                    $total = Session::get('total');
                    $duration = Session::get('duration');
                    $planPrice = Session::get('plan');
                    $startDate = Session::get('start_date');
                    $endDate = Session::get('end_date');
                    $clientImage = Session::get('client_image');
                    $subCategoryName = Session::get('sub_category_name');

                    $paymentArray = [
                                        'category_id' => $categoryId,
                                        'sub_category_id' => $subcategoryId,
                                        'admin_price' => $total,
                                        'client_user_price' => $subcatPrice,
                                        'payment_id' => $paymentId,
                                        'payment_request_id' => $paymentRequestId,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'client_image' => $clientImage,
                                        'sub_category' => $subCategoryName,
                                    ];
                    PayableClientSubCategory::addPayableClientSubCategory($paymentArray);
                    DB::connection('mysql2')->commit();
                }
                catch(Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect('managePayableSubCategory')->withErrors([$e->getMessage()]);
                }

                Session::remove('payable_category_id');
                Session::remove('payable_sub_category_id');
                Session::remove('sub_category_price');
                Session::remove('total');
                Session::remove('duration');
                Session::remove('plan');
                Session::remove('start_date');
                Session::remove('end_date');
                Session::remove('client_image');
                Session::remove('sub_category_name');

                $to = $email;
                $subject = 'Your transaction with Vchipedu on '. date('Y-m-d').' is successful';
                $message = "<h1>Dear ".$name."</h1></br>";
                $message .= "Thank you for paying. Your have successfully purchased a Sub Category: ".$subCategoryName.".</br>";
                $message .= "<hr>";
                $message .= "<h1>Payment Details</h1>";
                $message .= '<p><b>Payment Id:</b> '.$paymentId.'</p>';
                $message .= '<p><b>Payment Status:</b> '.$status.'</p>';
                $message .= '<p><b>Amount:</b> '.$total.'</p>';
                $message .= "<p>Thank</p>";
                $message .= "<p>Vchipedu</p>";

                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                // send email
                // mail($to, $subject, $message, $headers);
                Mail::to($to)->send(new PaymentReceived($message,$subject));
                return redirect('managePayableSubCategory')->with('message', 'You have purchased sub category successfully.');
            }
        }
        catch (Exception $e) {
            return redirect('managePayableSubCategory')->withErrors([$e->getMessage()]);
        }
    }

    public function webhookPayable(Request $request){
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
            $subject = 'Sub Category Purchased By: ' .$data['buyer_name'].'</br>';
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
        }
    }

    protected function updatePayableSubCategory(Request $request){
        $payableSubcategoryId = $request->get('payable_subcategory_id');
        $subcategoryId = $request->get('subcategory_id');
        $subcategoryName = $request->get('subcategory_name');
        $subcatPrice = $request->get('subcat_price');

        if(empty($payableSubcategoryId) || empty($subcategoryId) || empty($subcatPrice || empty($subcategoryName))){
            return redirect()->back()->withErrors(['something went wrongsss.']);
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcategory = PayableClientSubCategory::updatePayableSubCategory($request);
            if(is_object($subcategory)){
                DB::connection('mysql2')->commit();
                return redirect('managePurchasedSubCategory')->with('message', 'Sub Category Updated Successfully.');
            }
        }
        catch (Exception $e) {
            DB::connection('mysql2')->rollback();
            return redirect('managePurchasedSubCategory')->withErrors([$e->getMessage()]);
        }
        return redirect('managePurchasedSubCategory');
    }

    protected function managePurchasedSubCategory(Request $request){
        $purchasedSubCategories = [];
        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategoriesAssociatedWithQuestion();
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId(Auth::guard('client')->user()->id);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $purchasedSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        }
        return view('client.purchaseSubCategory.purchase', compact('testSubCategories', 'purchasedSubCategories', 'testCategories'));
    }

    /**
     *  showPayableSubcategory
     */
    protected function showPurchaseSubcategory($subdomain,$id,Request $request){
        $selectedSubCategory = ClientOnlineTestSubCategory::showPayableSubcategoryById(json_decode($id));

        if(!is_object($selectedSubCategory)){
            return Redirect::to('managePurchaseSubCategory');
        }
        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategoriesAssociatedWithQuestion();
        $testSubjects = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($selectedSubCategory->id);
        $testPapers = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($selectedSubCategory->id);
        $paperQuestionCount = ClientOnlineTestQuestion::getPayableQuestionsCountBySubcategoryId($selectedSubCategory->id);
        $isTestSubCategoryPurchased = false;
        return view('client.purchaseSubCategory.purchase_details', compact('selectedSubCategory', 'testSubCategories', 'testSubjects', 'testPapers', 'isTestSubCategoryPurchased', 'paperQuestionCount', 'testCategories'));
    }
}