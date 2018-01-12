<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentGatewayErrors;
use App\Mail\PaymentReceived;
use App\Models\ClientHomePage;
use App\Models\Client;
use Illuminate\Http\Request;
use View,DB,Session,Redirect, Auth,Validator;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineVideo;
use App\Models\ClientScore;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientUserPurchasedCourse;
use App\Models\UserBasedAuthentication;
use App\Models\ClientUserPayment;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Libraries\InputSanitise;

class ClientUserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('clientuser');
        $client = Client::where('subdomain', $request->getHost())->first();
        if(is_object($client)){
            view::share('client', $client);
        }
    }

    protected function showClientUserDashBoard(Request $request){
        return view('clientuser.dashboard.dashboard');
    }

    protected function myCourses(){
        $categoryIds = [];
        $categories = [];
        $courseVideoCount = 0;
        $userId = Auth::guard('clientuser')->user()->id;
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($userId);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->category_id;
            }
            $categories = ClientOnlineCategory::find($categoryIds);
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $userPurchasedCourses = ClientUserPurchasedCourse::getUserPurchasedCourses($clientId, $userId);
        }

        return view('clientuser.dashboard.myCourses', compact('courses', 'categories', 'userPurchasedCourses'));
    }

    protected function myCertificate(){
        return view('clientuser.dashboard.myCertificate');
    }

    protected function myTest(){
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $results = ClientOnlineTestSubjectPaper::getRegisteredSubjectPapersByUserId($userId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $testSubjects = ClientOnlineTestSubject::getSubjectsByIds($results['subjectIds']);
        }
        $testCategories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($userId);
        $alreadyGivenPapers = ClientScore::getClientUserTestScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId);
        $currentDate = date('Y-m-d H:i:s');
        return view('clientuser.dashboard.myTest', compact('testSubjects', 'testSubjectPapers', 'testCategories','currentDate', 'alreadyGivenPapers'));
    }

    protected function getVideoCount($courses){
        $courseIds = [];
         if(false == $courses->isEmpty()){
            foreach($courses as $course){
                $courseIds[] = $course->id;
            }
            $courseIds = array_unique($courseIds);
        }
        return ClientOnlineVideo::getCoursevideoCount($courseIds);
    }

    protected function myCourseResults(){
        $categoryIds = [];
        $categories = [];
        $user = Auth::guard('clientuser')->user();

        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($user->id);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->category_id;
            }
            $categories = ClientOnlineCategory::find($categoryIds);
        }
        return view('clientuser.dashboard.myCourseResult', compact('courses', 'categories'));
    }

    protected function getCourseByCatIdBySubCatIdByUserId(Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        $result['courses'] = ClientOnlineCourse::getRegisteredOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId, $userId);
        return $result;
    }

    protected function myTestResults(){
        $user = Auth::guard('clientuser')->user();
        $categories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($user->id);
        // $results = ClientScore::where('client_user_id', $user->id)->get();
        $results = ClientScore::getClientScoreByUserId($user->id);
        $barchartLimits = range(100, 0, 10);
        return view('clientuser.dashboard.myTestResults', compact('categories','results','barchartLimits'));
    }

    protected function showUserTestResultsByCategoryBySubcategoryByUserId(Request $request){

        return ClientScore::getUserTestResultsByCategoryBySubcategoryByUserId($request);
    }

    protected function profile(){
        return view('clientuser.dashboard.profile');
    }

    protected function updateProfile(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $user = Clientuser::updateUser($request);
            if(is_object($user)){
                DB::connection('mysql2')->commit();
                return Redirect::to('profile')->with('message', 'Profile updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('profile');
    }

    protected function clientMessages(Request $request){
        $sortIds = [];
        $allIds = [];
        $testCourseIds = [];
        $onlineVideoIds = [];
        $testSubjectPapersIds = [];
        $idsImploded = '';
        $selectedYear = !empty($request->get('year'))?$request->get('year'): date('Y');
        $selectedMonth = !empty($request->get('month'))?$request->get('month'): date('m');

        $readNotificationIds = ClientReadNotification::getReadNotificationIdsByUser($selectedYear,$selectedMonth);

        $queryForTestPapers = ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)
                        ->where('notification_module', 2)
                        ->where('created_by',0)->where('created_to',0)
                        ->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth);

        $allAdminNotifications = ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)
                        ->where('notification_module', 1)
                        ->where('created_by',0)->where('created_to',0)
                        ->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)
                        ->union($queryForTestPapers)->orderBy('id', 'desc')->get();

        if(is_object($allAdminNotifications) && false == $allAdminNotifications->isEmpty()){
            foreach ($allAdminNotifications as $allAdminNotification) {
                if(!in_array($allAdminNotification->id, $readNotificationIds)){
                    $sortIds[] = $allAdminNotification->id;
                }
            }
            $allIds = array_merge($sortIds, $readNotificationIds);
            $idsImploded = "'" . implode("','", $allIds) . "'";
        }
        $result =  ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id);
        if( count($allIds) > 0 && !empty($idsImploded)){
            $result->whereIn('id', $allIds)->orderByRaw("FIELD(`id`,$idsImploded)");
        }
        $notifications = $result->where('created_by',0)->where('created_to',0)->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)->orderBy('id', 'desc')->paginate();
        $years = range(2017, 2030);
        $months = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
        return view('clientuser.dashboard.adminNotifications', compact('notifications', 'readNotificationIds', 'years', 'months','selectedYear', 'selectedMonth'));
    }


    protected function myNotifications(){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientNotification::readUserNotifications(Auth::guard('clientuser')->user()->id);
            DB::connection('mysql2')->commit();
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        $notifications =  ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('created_to', Auth::guard('clientuser')->user()->id)->orderBy('id', 'desc')->paginate();
        return view('clientuser.dashboard.notifications', compact('notifications'));
    }

    protected function myAssignments(){
        $assignments = ClientAssignmentQuestion::where('client_id', Auth::guard('clientuser')->user()->client_id)
                ->select('client_assignment_questions.*')->paginate();
        $subjects = ClientOnlineTestSubject::where('client_id', Auth::guard('clientuser')->user()->client_id)->get();
        return view('clientuser.dashboard.myAssignmentList', compact('assignments', 'subjects'));
    }

    protected function getAssignmentSubjectsByCourse(Request $request){
        return ClientAssignmentSubject::getAssignmentSubjectsByCourse($request->institute_course_id);
    }

    protected function getAssignmentTopicsBySubject(Request $request){
        return ClientAssignmentTopic::getAssignmentTopicsBySubject($request->subject_id);
    }

    protected function getAssignments(Request $request){
        $results = [];
        $query = ClientAssignmentQuestion::where('client_id', Auth::guard('clientuser')->user()->client_id);
        if($request->subject > 0){
            $query->where('client_assignment_subject_id', $request->subject);
        }
        if($request->topic > 0){
            $query->where('client_assignment_topic_id', $request->topic);
        }
        $assignments = $query->get();
        if(is_object($assignments) and false == $assignments->isEmpty()){
            foreach($assignments as $assignment){
                $results[$assignment->id]['id'] = $assignment->id;
                $results[$assignment->id]['question'] = mb_strimwidth($assignment->question, 0, 400, "...");
                $results[$assignment->id]['subject'] = $assignment->subject->name;
                $results[$assignment->id]['topic'] = $assignment->topic->name;
            }
        }
        return $results;
    }

    protected function doAssignment($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        $assignment = ClientAssignmentQuestion::find($id);
        $user = Auth::guard('clientuser')->user();
        $answers = ClientAssignmentAnswer::where('client_id', $user->client_id)->where('student_id', $user->id)->where('client_assignment_question_id', $assignment->id)->get();
        return view('clientuser.dashboard.myAssignmentDetails', compact('assignment', 'answers'));
    }

    protected function createAssignmentAnswer(Request $request){
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $answer = $request->get('answer');
        if(empty($answer) && false == $request->exists('attached_link')){
            return Redirect::to('doAssignment/'.$questionId);
        }

        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientAssignmentAnswer::addAssignmentAnswer($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('doAssignment/'.$questionId)->with('message', 'Assignment updated successfully.');

        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('doAssignment/'.$questionId);
    }

    protected function purchaseCourse($subdomain, $courseId){
        $instamojoErrors = '';
        $clientCourse = ClientOnlineCourse::find($courseId);
        if(!is_object($clientCourse)){
            return redirect()->back()->withErrors('something went wrong.');
        }
        Session::put('client_course_id', $clientCourse->id);
        Session::save();
        $purchasePostFields = [
                                'purpose' => 'purchase '. $clientCourse->name,
                                'amount'  =>   $clientCourse->price,
                                'buyer_name' => Auth::guard('clientuser')->user()->name,
                                'email'  => Auth::guard('clientuser')->user()->email,
                                'phone'  => Auth::guard('clientuser')->user()->phone,
                                'send_email' => 'True',
                                'send_sms' => 'False',
                                'redirect_url' => url('redirectCoursePayment'),
                                'webhook'   => url('webhook'),
                                'allow_repeated_payments' => 'False'
                            ];

        $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', Auth::guard('clientuser')->user()->client_id)->first();

        if(!is_object($clientUserAuth)){
            return redirect()->back()->withErrors('something went wrong.');
        }
        if('local' == \Config::get('app.env')){
            $paymentRequestUrl = "https://test.instamojo.com/v2/payment_requests/";
        } else {
            $paymentRequestUrl = "https://api.instamojo.com/v2/payment_requests/";
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $paymentRequestUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 60,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $purchasePostFields,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$clientUserAuth->access_token,
            "cache-control: no-cache",
            "content-type: multipart/form-data"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          $instamojoErrors = (string)$err;
        } else {
            $result = json_decode($response);
            header("Location: $result->longurl");
            exit();
        }
        if(!empty($instamojoErrors)){
            Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoErrors));
            return Redirect::to('online-courses')->withErrors(['some thing went wrong. please try after some time.']);
        }
    }

    protected function redirectCoursePayment(Request $request){
        $paymentRequestId = $request->get('payment_request_id');
        $paymentId = $request->get('payment_id');
        if(!empty($paymentRequestId) && !empty($paymentId)){
            $userId = Auth::guard('clientuser')->user()->id;
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', $clientId)->first();

            if(!is_object($clientUserAuth)){
                return redirect()->back()->withErrors('something went wrong.');
            }

            if('local' == \Config::get('app.env')){
                $userPaymentUrl = "https://test.instamojo.com/v2/payments/".$paymentId."/";
            } else {
                $userPaymentUrl = "https://api.instamojo.com/v2/payments/".$paymentId."/";
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $userPaymentUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$clientUserAuth->access_token,
                "cache-control: no-cache"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors((string)$err));
            } else {
                $result = json_decode($response);

                if( 'true' == $result->status){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $clientCourseId = Session::get('client_course_id');
                        $newUserCourse = new ClientUserPurchasedCourse;
                        $newUserCourse->user_id = $userId;
                        $newUserCourse->course_id = $clientCourseId;
                        $newUserCourse->client_id = $clientId;
                        $newUserCourse->payment_id = $paymentId;
                        $newUserCourse->price = $result->amount;
                        $newUserCourse->save();

                        $clientUserPayment = new ClientUserPayment;
                        $clientUserPayment->client_id = $clientId;
                        $clientUserPayment->clientuser_id = $userId;
                        $clientUserPayment->payment_request_id = $paymentRequestId;
                        $clientUserPayment->payment_id = $paymentId;
                        $clientUserPayment->save();
                        DB::connection('mysql2')->commit();
                        Session::remove('client_course_id');

                        // mail to client
                        $to = Auth::guard('clientuser')->user()->client->email;
                        $subject = 'Payment on your website:'. $result->title.' by '.$result->name.'';
                        $message = "<h1>Payment Details</h1>";
                        $message .= "<hr>";
                        $message .= '<p><b>Payment Id:</b> '.$result->id.'</p>';
                        $message .= '<p><b>Payment Status:</b> '.$result->status.'</p>';
                        $message .= '<p><b>Amount:</b> '.$result->amount.'</p>';
                        $message .= "<hr>";
                        $message .= '<p><b>Name:</b> '.$result->name.'</p>';
                        $message .= '<p><b>Email:</b> '.$result->email.'</p>';
                        $message .= '<p><b>Phone:</b> '.$result->phone.'</p>';
                        $message .= "<hr>";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        // send email
                        mail($to, $subject, $message, $headers);
                        return redirect('online-courses')->with('message', 'Thank you for paying. Your Payment has been successfully processed.');
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return Redirect::to('online-courses');
                    }
                }
            }
        }
        return Redirect::to('online-courses');
    }

    public function webhook(Request $request){
        $data = $request->all();
        ksort($data, SORT_STRING | SORT_FLAG_CASE);
        $clientUser = Clientuser::where('email',$data['buyer'])->first();
        if(is_object($clientUser)){
            $to = $clientUser->client->email;
            $subject = 'Payment on your website:'. $data['purpose'].' by '.$data['buyer_name'].'';
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

            // send email
            // mail($to, $subject, $message, $headers);
            Mail::to($to)->send(new PaymentReceived($message));
        } else {
            $to = 'vchipdesign@gmail.com';
            $subject = 'Payment on client website:'. $data['purpose'].' by '.$data['buyer_name'].'';
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
            // mail($to, $subject, $message, $headers);
            Mail::to($to)->send(new PaymentReceived($message));
        }
    }

    protected function purchaseTestSubCategory($subdomain, $subCategoryId){
        $instamojoErrors = '';
        $subCategory = ClientOnlineTestSubCategory::find($subCategoryId);
        if(!is_object($subCategory)){
            return redirect()->back()->withErrors('something went wrong.');
        }

        Session::put('client_sub_category_id', $subCategory->id);
        Session::put('client_category_id', $subCategory->category_id);
        Session::save();
        $purchasePostFields = [
                                'purpose' => 'purchase '. $subCategory->name,
                                'amount'  =>   $subCategory->price,
                                'buyer_name' => Auth::guard('clientuser')->user()->name,
                                'email'  => Auth::guard('clientuser')->user()->email,
                                'phone'  => Auth::guard('clientuser')->user()->phone,
                                'send_email' => 'True',
                                'send_sms' => 'False',
                                'redirect_url' => url('redirectTestSubCategoryPayment'),
                                'webhook'   => url('webhook'),
                                'allow_repeated_payments' => 'False'
                            ];

        $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', Auth::guard('clientuser')->user()->client_id)->first();

        if(!is_object($clientUserAuth)){
            return redirect()->back()->withErrors('something went wrong.');
        }
        if('local' == \Config::get('app.env')){
            $paymentRequestUrl = "https://test.instamojo.com/v2/payment_requests/";
        } else {
            $paymentRequestUrl = "https://api.instamojo.com/v2/payment_requests/";
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $paymentRequestUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 60,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $purchasePostFields,
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$clientUserAuth->access_token,
            "cache-control: no-cache",
            "content-type: multipart/form-data"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          $instamojoErrors = (string)$err;
        } else {
            $result = json_decode($response);
            if(is_object($result) && !empty($result->longurl)){
                header("Location: $result->longurl");
                exit();
            } else {
                return Redirect::to('online-tests')->withErrors(['some thing went wrong. please try after some time.']);
            }
        }
        if(!empty($instamojoErrors)){
            Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoErrors));
            return Redirect::to('online-tests')->withErrors(['some thing went wrong. please try after some time.']);
        }
    }

    protected function redirectTestSubCategoryPayment(Request $request){

        $paymentRequestId = $request->get('payment_request_id');
        $paymentId = $request->get('payment_id');
        if(!empty($paymentRequestId) && !empty($paymentId)){
            $userId = Auth::guard('clientuser')->user()->id;
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', $clientId)->first();

            if(!is_object($clientUserAuth)){
                return redirect()->back()->withErrors('something went wrong.');
            }
            if('local' == \Config::get('app.env')){
                $paymentUrl = "https://test.instamojo.com/v2/payments/".$paymentId."/";
            } else {
                $paymentUrl = "https://api.instamojo.com/v2/payments/".$paymentId."/";
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $paymentUrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$clientUserAuth->access_token,
                "cache-control: no-cache"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors((string)$err));
            } else {
                $result = json_decode($response);
                if( 'true' == $result->status){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $clientSubCategoryId = Session::get('client_sub_category_id');
                        $clientCategoryId = Session::get('client_category_id');
                        $newTestSubCategory = new ClientUserPurchasedTestSubCategory;
                        $newTestSubCategory->user_id = $userId;
                        $newTestSubCategory->test_category_id = $clientCategoryId;
                        $newTestSubCategory->test_sub_category_id = $clientSubCategoryId;
                        $newTestSubCategory->client_id = $clientId;
                        $newTestSubCategory->payment_id = $paymentId;
                        $newTestSubCategory->price = $result->amount;
                        $newTestSubCategory->save();

                        $clientUserPayment = new ClientUserPayment;
                        $clientUserPayment->client_id = $clientId;
                        $clientUserPayment->clientuser_id = $userId;
                        $clientUserPayment->payment_request_id = $paymentRequestId;
                        $clientUserPayment->payment_id = $paymentId;
                        $clientUserPayment->save();
                        DB::connection('mysql2')->commit();
                        Session::remove('client_sub_category_id');
                        Session::remove('client_category_id');

                        // mail to client
                        $to = Auth::guard('clientuser')->user()->client->email;
                        $subject = 'Payment on your website:'. $result->title.' by '.$result->name.'';
                        $message = "<h1>Payment Details</h1>";
                        $message .= "<hr>";
                        $message .= '<p><b>Payment Id:</b> '.$result->id.'</p>';
                        $message .= '<p><b>Payment Status:</b> '.$result->status.'</p>';
                        $message .= '<p><b>Amount:</b> '.$result->amount.'</p>';
                        $message .= "<hr>";
                        $message .= '<p><b>Name:</b> '.$result->name.'</p>';
                        $message .= '<p><b>Email:</b> '.$result->email.'</p>';
                        $message .= '<p><b>Phone:</b> '.$result->phone.'</p>';
                        $message .= "<hr>";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        // send email
                        // mail($to, $subject, $message, $headers);
                        Mail::to($to)->send(new PaymentReceived($message));
                        return redirect('online-tests')->with('message', 'Thank you for paying. Your Payment has been successfully processed.');
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return Redirect::to('online-tests');
                    }
                }
            }
        }
        return Redirect::to('online-tests');
    }
}
