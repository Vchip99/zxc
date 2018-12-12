<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;
use App\Models\SubscriedUser;
use App\Models\User;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientPlan;
use App\Models\WebdevelopmentPayment;
use App\Models\PayableClientSubCategory;
use App\Models\ClientLoginActivity;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientBatch;
use App\Models\TestSubjectPaper;
use App\Models\CourseCourse;
use App\Models\VkitProject;
use App\Models\AdminReceipt;
use App\Mail\MailToSubscribedUser;
use Auth,Hash,Session,Redirect,Validator,DB;
use App\Libraries\InputSanitise;


class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('admin');
    }

    protected $validateAdminReceipt = [
        'receipt_by' => 'required',
        'address' => 'required'
    ];

    protected function home(){
        return view('admin.home');
    }

    protected function writeMail(){
        return view('admin.writeMail');
    }

    protected function sendSubscribedMails(Request $request){
        $mailContent = $request->get('mail_content');
        if(empty($mailContent)){
            return redirect()->back();
        }
        $dom = new \DOMDocument();
        $dom->loadHTML($mailContent);
        $imgs = $dom->getElementsByTagName("img");
        foreach($imgs as $img){
            $src = $request->root().$img->getAttribute('src');
            $img->setAttribute( 'src' , $src );
        }
        $body = $dom->saveHTML();

        try
        {
            $subscriedUsers = SubscriedUser::where('verified', 1)->select('email')->get()->toArray();
            $subscriedUsers = implode(',', array_column($subscriedUsers, 'email'));
            $mailSubject = 'Hello Vchip User';
            Mail::bcc($subscriedUsers)->queue(new MailToSubscribedUser($body,$mailSubject));
            return redirect()->back()->with('message', 'Mail will be sent successfully to all subscribed users.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function manageClients(){
        $clients = User::getClients();
        return view('admin.clients', compact('clients'));
    }

    protected function changeClientPermissionStatus(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $client = Client::changeClientPermissionStatus($request);
            if(is_object($client)){
                DB::connection('mysql2')->commit();
            } else {
                DB::connection('mysql2')->rollback();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }

        return User::getClients();
    }

    protected function deleteClient(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $client = Client::find($request->client_id);
            if(is_object($client)){
                Clientuser::deleteAllClientUsersInfoByClientId($client->id);
                $client->deleteOtherInfoByClient($client);
                $purchasedSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($client->id);
                    if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
                        foreach($purchasedSubCategories as $purchasedSubCategory){
                            $purchasedSubCategory->end_date = date('Y-m-d');
                            $purchasedSubCategory->save();
                        }
                    }
                $client->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('admin/manageClients')->with('message', 'Client deleted successfully');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }
    }

    protected function manageClientHistory(){
        $clients = User::getClients();
        return view('admin.clientHistory', compact('clients'));
    }

    protected function getClientHistory(Request $request){
        $result = [];
        $total = 0;
        if($request->client_id > 0){
            $clientPlans = ClientPlan::where('client_id', $request->client_id)->orderBy('id', 'desc')->get();
        } else {
            $clientPlans = ClientPlan::orderBy('id', 'desc')->get();
        }
        if(is_object($clientPlans) && false == $clientPlans->isEmpty()){
            foreach($clientPlans as $clientPlan){
                $result['plans'][]= [
                                'client' => $clientPlan->client(),
                                'start_date' => $clientPlan->start_date,
                                'plan' => $clientPlan->plan->name,
                                'end_date' => $clientPlan->end_date,
                                'final_amount' => $clientPlan->final_amount,
                                'payment_status' => $clientPlan->payment_status,
                                'plan_id' => $clientPlan->plan_id,
                            ];
                $total += $clientPlan->final_amount;
            }
        } else {
            $result['plans'] = [];
        }
        if($request->client_id > 0){
            $purchasedSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdForAdmin($request->client_id);
        } else {
            $purchasedSubCategories = PayableClientSubCategory::getAllPayableSubCategory();
        }

        if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
            foreach($purchasedSubCategories as $purchasedSubCategory){
                $result['purchasedSubCategories'][]= [
                                'client' => $purchasedSubCategory->clientName(),
                                'start_date' => $purchasedSubCategory->start_date,
                                'end_date' => $purchasedSubCategory->end_date,
                                'sub_category' => $purchasedSubCategory->sub_category,
                                'price' => $purchasedSubCategory->admin_price,
                            ];
                $total += $purchasedSubCategory->admin_price;
            }
        } else {
            $result['purchasedSubCategories'] = [];
        }
        $result['total'] = $total;
        return $result;
    }

    protected function manageWebDevelopments(){
        $totalSum = 0;
        $webDevelopments = WebdevelopmentPayment::all();
        if(is_object($webDevelopments) && false == $webDevelopments->isEmpty()){
            foreach($webDevelopments as $webDevelopment){
                $totalSum = $totalSum + $webDevelopment->price;
            }
        }
        return view('webDevelopment.webDevelopment', compact('webDevelopments', 'totalSum'));
    }

    protected function manageClientPaidSms(){
        $clients = User::getClients();
        return view('admin.sms', compact('clients'));
    }

    protected function clientPurchaseSms(Request $request){
        $smsCount = $request->get('sms_count');
        $total = $request->get('total');
        $clientId = $request->get('client');

        if($smsCount > 0 ){
            $client = Client::find($clientId);
            if(is_object($client)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $startDate = date('Y-m-d');
                    $endDate = '2050-01-01';
                    $smsArray = [
                                    'client_id' => $client->id,
                                    'total' => $total,
                                    'payment_id' => ' ',
                                    'payment_request_id' => ' ',
                                    'purcahsed_sms' => $smsCount,
                                    'start_date' => $startDate,
                                    'end_date' => $endDate,
                                ];
                    PayableClientSubCategory::addClientPurchasedSms($smsArray);
                    $client->debit_sms_count = ($client->debit_sms_count + $smsCount) - $client->credit_sms_count;
                    $client->credit_sms_count = 0;
                    $client->save();
                    DB::connection('mysql2')->commit();
                    return redirect('admin/manageClientPaidSms')->with('message', 'Sms purcahsed for client successfully.');
                }
                catch(Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect('admin/manageClientPaidSms')->withErrors([$e->getMessage()]);
                }
            }
        }
        return redirect('admin/manageClientPaidSms');
    }

    protected function clientsActivity(){
        $calendarData = [];
        $loginClientArr = [];
        $loginDatesArr = [];
        $loginClientNames = [];
        $loginDates = '';
        $clientActivities = ClientLoginActivity::all();
        if(is_object($clientActivities) && false == $clientActivities->isEmpty()){
            foreach($clientActivities as $clientActivity){
                $dateStr = date('Y-m-d',strtotime($clientActivity->login_time));
                if(!isset($loginDatesArr[$dateStr])){
                    $loginDatesArr[$dateStr] = 'green';
                }
                if(!empty($clientActivity->login_time) && !empty($clientActivity->logout_time)){
                    $datetime1 = new \DateTime($clientActivity->login_time);
                    $datetime2 = new \DateTime($clientActivity->logout_time);
                    $interval = $datetime1->diff($datetime2);
                    $totalTime = $interval->format('%h:%i:%s');
                } else {
                    $totalTime = '';
                }
                if(!isset($calendarData[$dateStr][$clientActivity->client_id])){
                    $calendarData[$dateStr][$clientActivity->client_id]['log_in_out_time'][] = [
                        'client_id' => $clientActivity->client_id,
                        'session_id' => $clientActivity->session_id,
                        'login_time' => $clientActivity->login_time,
                        'logout_time' => $clientActivity->logout_time,
                        'total_time' => $totalTime,
                    ];
                } else {
                    $calendarData[$dateStr][$clientActivity->client_id]['log_in_out_time'][] = [
                        'client_id' => $clientActivity->client_id,
                        'session_id' => $clientActivity->session_id,
                        'login_time' => $clientActivity->login_time,
                        'logout_time' => $clientActivity->logout_time,
                        'total_time' => $totalTime,
                    ];
                }
                if(!isset($loginClientArr[$clientActivity->client_id])){
                    $loginClientArr[$clientActivity->client_id] = $clientActivity->client_id;
                }
            }
            $searchDate = array_keys($calendarData)[0];
            if(!empty($searchDate)){
                $clientCourses = ClientOnlineCourse::getClientOnlineCoursesByUpdatedDate($searchDate);
                if(is_object($clientCourses) && false == $clientCourses->isEmpty()){
                    foreach($clientCourses as $clientCourse){
                        $dateStr = date('Y-m-d',strtotime($clientCourse->updated_at));
                        if(!isset($calendarData[$dateStr][$clientCourse->client_id])){
                            $calendarData[$dateStr][$clientCourse->client_id]['courses'][] = [
                                'course' => $clientCourse->name,
                            ];
                        } else {
                            $calendarData[$dateStr][$clientCourse->client_id]['courses'][] = [
                                'course' => $clientCourse->name,
                            ];
                        }
                    }
                }

                $clientSubcategories = ClientOnlineTestSubCategory::getClientOnlineTestSubCategoriesByUpdatedDate($searchDate);
                if(is_object($clientSubcategories) && false == $clientSubcategories->isEmpty()){
                    foreach($clientSubcategories as $clientSubcategory){
                        $dateStr = date('Y-m-d',strtotime($clientSubcategory->updated_at));
                        if(!isset($calendarData[$dateStr][$clientSubcategory->client_id])){
                            $calendarData[$dateStr][$clientSubcategory->client_id]['subcategories'][] = [
                                'subcategory' => $clientSubcategory->name,
                            ];
                        } else {
                            $calendarData[$dateStr][$clientSubcategory->client_id]['subcategories'][] = [
                                'subcategory' => $clientSubcategory->name,
                            ];
                        }
                    }
                }

                $clientAssignments = ClientAssignmentQuestion::getClientAssignmentQuestionsByUpdatedDate($searchDate);
                if(is_object($clientAssignments) && false == $clientAssignments->isEmpty()){
                    foreach($clientAssignments as $clientAssignment){
                        $dateStr = date('Y-m-d',strtotime($clientAssignment->updated_at));
                        if(!isset($calendarData[$dateStr][$clientAssignment->client_id])){
                            $calendarData[$dateStr][$clientAssignment->client_id]['assignments'][] = [
                                'assignment' => $clientAssignment->question,
                            ];
                        } else {
                            $calendarData[$dateStr][$clientAssignment->client_id]['assignments'][] = [
                                'assignment' => $clientAssignment->question,
                            ];
                        }
                    }
                }

                $clientPayableSubCategories = PayableClientSubCategory::getPayableClientSubCategoryByUpdatedDate($searchDate);
                if(is_object($clientPayableSubCategories) && false == $clientPayableSubCategories->isEmpty()){
                    foreach($clientPayableSubCategories as $clientPayableSubCategory){
                        $dateStr = date('Y-m-d',strtotime($clientPayableSubCategory->updated_at));
                        if(!isset($calendarData[$dateStr][$clientPayableSubCategory->client_id])){
                            $calendarData[$dateStr][$clientPayableSubCategory->client_id]['payables'][] = [
                                'payable' => $clientPayableSubCategory->sub_category,
                            ];
                        } else {
                            $calendarData[$dateStr][$clientPayableSubCategory->client_id]['payables'][] = [
                                'payable' => $clientPayableSubCategory->sub_category,
                            ];
                        }
                    }
                }

                $clientBatches = ClientBatch::getClientBatchesByUpdatedDate($searchDate);
                if(is_object($clientBatches) && false == $clientBatches->isEmpty()){
                    foreach($clientBatches as $clientBatch){
                        $dateStr = date('Y-m-d',strtotime($clientBatch->updated_at));
                        if(!isset($calendarData[$dateStr][$clientBatch->client_id])){
                            $calendarData[$dateStr][$clientBatch->client_id]['batches'][] = [
                                'batch' => $clientBatch->name,
                            ];
                        } else {
                            $calendarData[$dateStr][$clientBatch->client_id]['batches'][] = [
                                'batch' => $clientBatch->name,
                            ];
                        }
                    }
                }
            }
        }
        if(count($loginClientArr) > 0){
            $clients = Client::find($loginClientArr);
            if(is_object($clients) && false == $clients->isEmpty()){
                foreach($clients as $client){
                    $loginClientNames[$client->id] = $client->name;
                }
            }
        }
        if(count($loginDatesArr) > 0){
            foreach($loginDatesArr as $date => $color){
                if(empty($loginDates)){
                    $loginDates = $date.':'.$color;
                } else {
                    $loginDates .= ','.$date.':'.$color;
                }
            }
        }
        return view('admin.clientsActivity', compact('calendarData','loginDates','loginClientNames'));
    }

    protected function manageAdminPayments(){
        $results = [];
        $userIds = [];
        $adminIds = [];
        $adminNames = [];
        $userNames = [];
        $total = 0;
        $registerdPapers = TestSubjectPaper::getPurchasedPapers();
        if(is_object($registerdPapers) && false == $registerdPapers->isEmpty()){
            foreach($registerdPapers as $registerdPaper){
                $results[] = [
                    'id' => $registerdPaper->id,
                    'type' => 'Paper',
                    'name' => $registerdPaper->name,
                    'price' => $registerdPaper->price,
                    'category' => $registerdPaper->category,
                    'subcategory' => $registerdPaper->subcategory,
                    'subject' => $registerdPaper->subject,
                    'user_id' => $registerdPaper->user_id,
                    'updated_at' => $registerdPaper->updated_at,
                    'admin_id' => $registerdPaper->created_by,
                ];
                $total += $registerdPaper->price;
                $userIds[] = $registerdPaper->user_id;
                $adminIds[] = $registerdPaper->created_by;
            }
        }
        $registerdCourses = CourseCourse::getPurchasedCourses();
        if(is_object($registerdCourses) && false == $registerdCourses->isEmpty()){
            foreach($registerdCourses as $registerdCourse){
                $results[] = [
                    'id' => $registerdCourse->id,
                    'type' => 'Course',
                    'name' => $registerdCourse->name,
                    'price' => $registerdCourse->price,
                    'category' => $registerdCourse->category,
                    'subcategory' => $registerdCourse->subcategory,
                    'subject' => '-',
                    'user_id' => $registerdCourse->user_id,
                    'updated_at' => $registerdCourse->updated_at,
                    'admin_id' => $registerdCourse->admin_id,
                ];
                $total += $registerdCourse->price;
                $userIds[] = $registerdCourse->user_id;
                $adminIds[] = $registerdCourse->admin_id;
            }
        }
        $purchasedProjects = VkitProject::getPurchasedVkitProjects();
        if(is_object($purchasedProjects) && false == $purchasedProjects->isEmpty()){
            foreach($purchasedProjects as $purchasedProject){
                $results[] = [
                    'id' => $purchasedProject->id,
                    'type' => 'Vkit Project',
                    'name' => $purchasedProject->name,
                    'price' => $purchasedProject->price,
                    'category' => $purchasedProject->category,
                    'subcategory' => '-',
                    'subject' => '-',
                    'user_id' => $purchasedProject->user_id,
                    'updated_at' => $purchasedProject->updated_at,
                    'admin_id' => $purchasedProject->created_by,
                ];
                $total += $purchasedProject->price;
                $userIds[] = $purchasedProject->user_id;
                $adminIds[] = $purchasedProject->created_by;
            }
        }
        if(count($adminIds) > 0){
            $admins = Admin::find(array_unique($adminIds));
            if(is_object($admins) && false == $admins->isEmpty()){
                foreach($admins as $admin){
                    $adminNames[$admin->id] = $admin->name;
                }
            }
        }
        if(count($userIds) > 0){
            $users = User::find(array_unique($userIds));
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = $user->name;
                }
            }
        }
        return view('admin.adminPayment',compact('results','total','adminNames','userNames'));
    }

    protected function getAdminPaymentsById(Request $request){
        $results = [];
        $userIds = [];
        $adminIds = [];
        $adminNames = [];
        $userNames = [];
        $total = 0;
        $registerdPapers = TestSubjectPaper::getPurchasedPapers($request->get('admin_id'));
        if(is_object($registerdPapers) && false == $registerdPapers->isEmpty()){
            foreach($registerdPapers as $registerdPaper){
                $results['payments'][] = [
                    'id' => $registerdPaper->id,
                    'type' => 'Paper',
                    'name' => $registerdPaper->name,
                    'price' => $registerdPaper->price,
                    'category' => $registerdPaper->category,
                    'subcategory' => $registerdPaper->subcategory,
                    'subject' => $registerdPaper->subject,
                    'user_id' => $registerdPaper->user_id,
                    'updated_at' => date('Y-m-d H:i:s', strtotime($registerdPaper->updated_at)),
                    'admin_id' => $registerdPaper->created_by,
                ];
                $total += $registerdPaper->price;
                $userIds[] = $registerdPaper->user_id;
                $adminIds[] = $registerdPaper->created_by;
            }
        }
        $registerdCourses = CourseCourse::getPurchasedCourses($request->get('admin_id'));
        if(is_object($registerdCourses) && false == $registerdCourses->isEmpty()){
            foreach($registerdCourses as $registerdCourse){
                $results['payments'][] = [
                    'id' => $registerdCourse->id,
                    'type' => 'Course',
                    'name' => $registerdCourse->name,
                    'price' => $registerdCourse->price,
                    'category' => $registerdCourse->category,
                    'subcategory' => $registerdCourse->subcategory,
                    'subject' => '-',
                    'user_id' => $registerdCourse->user_id,
                    'updated_at' => date('Y-m-d H:i:s', strtotime($registerdCourse->updated_at)),
                    'admin_id' => $registerdCourse->admin_id,
                ];
                $total += $registerdCourse->price;
                $userIds[] = $registerdCourse->user_id;
                $adminIds[] = $registerdCourse->admin_id;
            }
        }
        $purchasedProjects = VkitProject::getPurchasedVkitProjects($request->get('admin_id'));
        if(is_object($purchasedProjects) && false == $purchasedProjects->isEmpty()){
            foreach($purchasedProjects as $purchasedProject){
                $results['payments'][] = [
                    'id' => $purchasedProject->id,
                    'type' => 'Vkit Project',
                    'name' => $purchasedProject->name,
                    'price' => $purchasedProject->price,
                    'category' => $purchasedProject->category,
                    'subcategory' => '-',
                    'subject' => '-',
                    'user_id' => $purchasedProject->user_id,
                    'updated_at' => date('Y-m-d H:i:s', strtotime($purchasedProject->updated_at)),
                    'admin_id' => $purchasedProject->created_by,
                ];
                $total += $purchasedProject->price;
                $userIds[] = $purchasedProject->user_id;
                $adminIds[] = $purchasedProject->created_by;
            }
        }
        if(count($adminIds) > 0){
            $admins = Admin::find(array_unique($adminIds));
            if(is_object($admins) && false == $admins->isEmpty()){
                foreach($admins as $admin){
                    $adminNames[$admin->id] = $admin->name;
                }
            }
        }
        if(count($userIds) > 0){
            $users = User::find(array_unique($userIds));
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = $user->name;
                }
            }
        }
        $results['admins'] = $adminNames;
        $results['users'] = $userNames;
        $results['total'] = $total;
        return $results;
    }

    protected function showReceipt(){
        $receipt = AdminReceipt::first();
        if(!is_object($receipt)){
            $receipt = new AdminReceipt;
        }
        return view('admin.receipt', compact('receipt'));
    }

    /**
     *  store receipt
     */
    protected function storeReceipt(Request $request){
        $v = Validator::make($request->all(), $this->validateAdminReceipt);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $receipt = AdminReceipt::addOrUpdateAdminReceipt($request);
            if(is_object($receipt)){
                DB::commit();
                return Redirect::to('admin/manageReceipt')->with('message', 'Receipt created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong while create receipt.');
        }
        return Redirect::to('admin/manageReceipt');
    }

    /**
     *  update receipt
     */
    protected function updateReceipt(Request $request){
        $v = Validator::make($request->all(), $this->validateAdminReceipt);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $receiptId = InputSanitise::inputInt($request->get('receipt_id'));
        if(isset($receiptId)){
            DB::beginTransaction();
            try
            {
                $receipt = AdminReceipt::addOrUpdateAdminReceipt($request, true);
                if(is_object($receipt)){
                    DB::commit();
                    return Redirect::to('admin/manageReceipt')->with('message', 'Receipt updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong while update receipt.');
            }
        }
        return Redirect::to('admin/manageReceipt');
    }

    protected function showOnlineReceipt($type,$id){
        $onlineReceipt = '';
        if('paper' == $type){
            $registerdPaper = TestSubjectPaper::getPurchasedPaperById($id);
            if(is_object($registerdPaper)){
                $onlineReceipt= [
                        'id' => $registerdPaper->id,
                        'user' => $registerdPaper->getUser(),
                        'user_id' => $registerdPaper->user_id,
                        'type' => 'Paper',
                        'name' => $registerdPaper->name,
                        'amount' => $registerdPaper->price,
                        'date' => $registerdPaper->updated_at,
                    ];
            }
        } elseif('course' == $type){
            $registerdCourse = CourseCourse::getPurchasedCourseById($id);
            if(is_object($registerdCourse)){
                $onlineReceipt= [
                        'id' => $registerdCourse->id,
                        'user' => $registerdCourse->getUser(),
                        'user_id' => $registerdCourse->user_id,
                        'type' => 'Course',
                        'name' => $registerdCourse->name,
                        'amount' => $registerdCourse->price,
                        'date' => $registerdCourse->updated_at,
                    ];
            }
        } elseif('vkit' == $type){
            $registerdProject = VkitProject::getPurchasedVkitProjectById($id);
            if(is_object($registerdProject)){
                $onlineReceipt= [
                        'id' => $registerdProject->id,
                        'user' => $registerdProject->getUser(),
                        'user_id' => $registerdProject->user_id,
                        'type' => 'Vkit',
                        'name' => $registerdProject->name,
                        'amount' => $registerdProject->price,
                        'date' => $registerdProject->updated_at,
                    ];
            }
        } else {
            return Redirect::to('admin/manageAdminPayments');
        }

        $adminReceipt = AdminReceipt::first();
        if(is_object($adminReceipt)){
            $receiptBy = $adminReceipt->receipt_by;
            $address = $adminReceipt->address;
            $gstin = $adminReceipt->gstin;
            $cin = $adminReceipt->cin;
            $pan = $adminReceipt->pan;
            $isGstTestApplied = $adminReceipt->is_gst_test_applied;
            $isGstCourseApplied = $adminReceipt->is_gst_course_applied;
            $isGstVkitApplied = $adminReceipt->is_gst_vkit_applied;
            $hsnSac = (!empty($adminReceipt->hsn_sac))?$adminReceipt->hsn_sac:'NA';
        } else {
            $receiptBy = 'VCHIP TECHNOLOGY PVT LTD';
            $address = '';
            $gstin = '';
            $cin = '';
            $pan = '';
            $isGstTestApplied = 0;
            $isGstCourseApplied = 0;
            $isGstVkitApplied = 0;
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
                'is_gst_test_applied' => $isGstTestApplied,
                'is_gst_course_applied' => $isGstCourseApplied,
                'is_gst_vkit_applied' => $isGstVkitApplied,
                'receipt_by' => $receiptBy,
                'date' => date('d-m-Y',strtotime($onlineReceipt['date'])),
                'gstin' => $gstin,
                'cin' =>  $cin,
                'pan' =>  $pan,
                'address' =>  $address,
                'hsnSac' =>  $hsnSac
            ];

            $html = $this->createOnlinePdfHtml($onlineReceiptArr);
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','tempDir' => __DIR__ . '/../mpdfFont']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html, 2);
            return  $mpdf->Output("receipt-".$onlineReceiptArr['receipt_id'].".pdf", "I");
        }
        return Redirect::to('admin/manageAdminPayments');
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
        if(!empty($onlineReceiptArr['gstin']) && !empty($onlineReceiptArr['cin']) && !empty($onlineReceiptArr['pan']) && (('Paper' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_test_applied']) || ('Course' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_course_applied']) || ('Vkit' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_vkit_applied']))){
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
        if(!empty($onlineReceiptArr['gstin']) && !empty($onlineReceiptArr['cin']) && !empty($onlineReceiptArr['pan']) && (('Paper' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_test_applied']) || ('Course' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_course_applied']) || ('Vkit' == $onlineReceiptArr['type'] && 1 == $onlineReceiptArr['is_gst_vkit_applied']))){
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
}