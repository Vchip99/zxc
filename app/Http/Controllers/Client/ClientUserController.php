<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentGatewayErrors;
use App\Mail\PaymentReceived;
use App\Mail\ClientUserEmailVerification;
use App\Models\ClientHomePage;
use App\Models\Client;
use Illuminate\Http\Request;
use View,DB,Session,Redirect, Auth,Validator,Cache,LRedis,Hash;
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
use App\Models\PayableClientSubCategory;
use App\Models\RegisterClientOnlineCourses;
use App\Models\RegisterClientOnlinePaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientUserAttendance;
use App\Models\ClientBatch;
use App\Models\ClientOfflinePaperMark;
use App\Models\ClientMessage;
use App\Models\ClientOfflinePayment;
use App\Models\ClientUploadTransaction;
use App\Models\ClientClass;
use App\Models\ClientHoliday;
use App\Models\ClientExam;
use App\Models\ClientNotice;
use App\Models\ClientIndividualMessage;
use App\Libraries\InputSanitise;
use App\Models\ClientDiscussionCategory;
use App\Models\ClientDiscussionPost;
use App\Models\ClientDiscussionComment;
use App\Models\ClientDiscussionSubComment;
use App\Models\ClientDiscussionLike;
use App\Models\ClientGalleryType;
use App\Models\ClientGalleryImage;
use DateTime;

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

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateAddEmail = [
        'email' => 'required|max:255',
        'password' => 'required',
        'confirm_password' => 'required|same:password'
    ];

    protected $validateUpdatePassword = [
        'old_password' => 'required',
        'password' => 'required|different:old_password',
        'confirm_password' => 'required|same:password',
    ];

    protected $validateUpload = [
        'batch' => 'required',
        'comment' => 'required',
        'photo' => 'required'
    ];

    protected function showClientUserDashBoard(Request $request){
        return view('clientuser.dashboard.dashboard');
    }

    protected function myCourses($subdomainName,Request $request){
        $categoryIds = [];
        $categories = [];
        $courseVideoCount = 0;
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($clientUserId);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->category_id;
            }
            $categories = ClientOnlineCategory::find($categoryIds);
            $userPurchasedCourses = ClientUserPurchasedCourse::getUserPurchasedCourses($clientId, $clientUserId);
        }

        return view('clientuser.dashboard.myCourses', compact('courses', 'categories', 'userPurchasedCourses'));
    }

    protected function myCertificate(){
        return view('clientuser.dashboard.myCertificate');
    }

    protected function myTest($subdomainName,Request $request){
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $categoryIds         = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $results = ClientOnlineTestSubjectPaper::getRegisteredSubjectPapersByUserId($clientUserId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $testSubjects = ClientOnlineTestSubject::getSubjectsByIds($testSubjectIds);
        }
        $testCategories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($clientUserId);
        if(is_object($testCategories) && false == $testCategories->isEmpty()){
            foreach($testCategories as $testCategory){
                $categoryIds[] = $testCategory->id;
            }
        }
        $payableTestCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithPayableSubCategory($request,$categoryIds);
        $alreadyGivenPapers = ClientScore::getClientUserTestScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $clientUserId);
        $currentDate = date('Y-m-d H:i:s');
        return view('clientuser.dashboard.myTest', compact('testSubjects', 'testSubjectPapers', 'testCategories','currentDate', 'alreadyGivenPapers', 'payableTestCategories'));
    }

    /**
     *  return sub categories by categoryId by userid by client
     */
    public function getClientUserTestSubcategoriesBycategoryId($subdomainName,Request $request){
        if($request->ajax()){
            $purchasedSubCategoryIds = [];
            $id = InputSanitise::inputInt($request->get('id'));
            $userId = InputSanitise::inputInt($request->get('userId'));
            $loginUser = Auth::guard('clientuser')->user();
            $clientId = $loginUser->client_id;
            $purchasedSubCategories = ClientUserPurchasedTestSubCategory::ClientUserPurchasedTestSubCategoryByCategory($clientId, $userId,$id);
            if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
                foreach($purchasedSubCategories as $purchasedSubCategory){
                    $purchasedSubCategoryIds[] = $purchasedSubCategory->test_sub_category_id;
                }
            }
            $result['subcategories'] = ClientOnlineTestSubCategory::find($purchasedSubCategoryIds);
            return $result;
        }
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

    protected function myCourseResults($subdomainName,Request $request){
        $categoryIds = [];
        $categories = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($clientUserId);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->category_id;
            }
            $categories = ClientOnlineCategory::find($categoryIds);
        }
        return view('clientuser.dashboard.myCourseResult', compact('courses', 'categories'));
    }

    protected function getCourseByCatIdBySubCatIdByUserId($subdomainName,Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        $result['courses'] = ClientOnlineCourse::getRegisteredOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId, $userId);
        return $result;
    }

    protected function myTestResults($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $categories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($clientUserId);

        $results = ClientScore::getClientScoreByUserId($clientUserId);

        $barchartLimits = range(100, 0, 10);
        return view('clientuser.dashboard.myTestResults', compact('categories','results','barchartLimits'));
    }

    protected function showUserTestResultsByCategoryBySubcategoryByUserId($subdomainName,Request $request){
        return ClientScore::getUserTestResultsByCategoryBySubcategoryByUserId($request);
    }

    protected function profile(Request $request){
        $loginUser = Auth::guard('clientuser')->user();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        }
        $clientResult = InputSanitise::checkUserClient($request, Auth::guard('clientuser')->user());
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $userScores = ClientScore::where('client_user_id', $loginUser->id)->get();
        $obtainedScore = 0;
        $totalScore = 0;
        if(is_object($userScores) && false == $userScores->isEmpty()){
            foreach($userScores as $userScore){
                $obtainedScore += $userScore->test_score;
                $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByClientId($userScore->category_id,$userScore->subcat_id,$userScore->subject_id,$userScore->paper_id,$loginUser->client_id);
                if(is_object($questions) && false == $questions->isEmpty()){
                    foreach($questions as $question){
                        $totalScore += $question->positive_marks;
                    }
                }
            }
        }
        return view('clientuser.dashboard.profile', compact('loginUser', 'obtainedScore', 'totalScore'));
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

    protected function clientMessages($subdomainName,Request $request){
        $sortIds = [];
        $allIds = [];
        $testCourseIds = [];
        $onlineVideoIds = [];
        $testSubjectPapersIds = [];
        $idsImploded = '';
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $selectedYear = $request->get('year');
        $selectedMonth = $request->get('month');
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }

        $readNotificationIds = ClientReadNotification::getReadNotificationIdsByUser($selectedYear,$selectedMonth);
        if($selectedYear > 0 || $selectedMonth > 0){
            $queryFirst = ClientNotification::where('client_id', $clientId)
                        ->where('notification_module', 2)
                        ->where('created_by',0)->where('created_to',0);
            if($selectedYear > 0){
                $queryFirst->whereYear('created_at', $selectedYear);
            }
            if($selectedMonth > 0){
                $queryFirst->whereMonth('created_at', $selectedMonth);
            }

        } else {
            $queryFirst = ClientNotification::where('client_id', $clientId)
                        ->where('notification_module', 2)
                        ->where('created_by',0)->where('created_to',0);
        }

        if($selectedYear > 0 || $selectedMonth > 0){
            $querySecond = ClientNotification::where('client_id', $clientId)
                        ->where('notification_module', 1)
                        ->where('created_by',0)->where('created_to',0);
            if($selectedYear > 0){
                $querySecond->whereYear('created_at', $selectedYear);
            }
            if($selectedMonth > 0){
                $querySecond->whereMonth('created_at', $selectedMonth);
            }
            $allAdminNotifications = $querySecond->union($queryFirst)->orderBy('id', 'desc')->get();
        } else {
            $allAdminNotifications = ClientNotification::where('client_id', $clientId)
                        ->where('notification_module', 1)
                        ->where('created_by',0)->where('created_to',0)
                        ->union($queryFirst)->orderBy('id', 'desc')->get();
        }
        if(is_object($allAdminNotifications) && false == $allAdminNotifications->isEmpty()){
            foreach($allAdminNotifications as $allAdminNotification) {
                if(!in_array($allAdminNotification->id, $readNotificationIds)){
                    $sortIds[] = $allAdminNotification->id;
                }
            }
            $allIds = array_merge($sortIds, $readNotificationIds);
            $idsImploded = "'" . implode("','", $allIds) . "'";
        }

        $result =  ClientNotification::where('client_id', $clientId);
        if( count($allIds) > 0 && !empty($idsImploded)){
            $result->whereIn('id', $allIds)->orderByRaw("FIELD(`id`,$idsImploded)");
        }
        if($selectedYear > 0 || $selectedMonth > 0){
            $result->where('created_by',0)->where('created_to',0);
            if($selectedYear > 0){
                $result->whereYear('created_at', $selectedYear);
            }
            if($selectedMonth > 0){
                $result->whereMonth('created_at', $selectedMonth);
            }
            $notifications = $result->orderBy('id', 'desc')->paginate(50);
        } else {
            $notifications = $result->where('created_by',0)->where('created_to',0)->orderBy('id', 'desc')->paginate(50);
        }
        $years = range(2017, 2030);
        $months = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
        return view('clientuser.dashboard.adminNotifications', compact('notifications', 'readNotificationIds', 'years', 'months','selectedYear', 'selectedMonth'));
    }

    protected function myNotifications($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientNotification::readUserNotifications($clientUserId);
            DB::connection('mysql2')->commit();
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        if(empty($request->getQueryString())){
            $page = 'page=1';
        } else {
            $page = $request->getQueryString();
        }

        $notifications = ClientNotification::where('client_id', $clientId)->where('created_to', $clientUserId)->orderBy('id', 'desc')->paginate();
        return view('clientuser.dashboard.notifications', compact('notifications'));
    }

    protected function myAssignments($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $assignments = ClientAssignmentQuestion::where('client_id', $clientId)->where('question', '!=', '')->select('client_assignment_questions.*')->paginate();
        $subjects = ClientAssignmentSubject::getAssignmentSubjectsAssociatedWithAssignmentByClientId($clientId);
        if($clientUser->unchecked_assignments > 0){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientUser->unchecked_assignments = 0;
                $clientUser->save();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return view('clientuser.dashboard.myAssignmentList', compact('assignments', 'subjects'));
    }

    protected function myAssignDocuments($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $assignments = ClientAssignmentQuestion::where('client_id', $clientId)->where('question','')->select('client_assignment_questions.*')->paginate();
        $subjects = ClientAssignmentSubject::getAssignmentSubjectsAssociatedWithDocumentByClientId($clientId);
        if($clientUser->unchecked_assignments > 0){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientUser->unchecked_assignments = 0;
                $clientUser->save();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return view('clientuser.dashboard.myAssignDocumentList', compact('assignments', 'subjects'));
    }

    protected function getAssignmentSubjectsByCourse(Request $request){
        return ClientAssignmentSubject::getAssignmentSubjectsByCourse($request->institute_course_id);
    }

    protected function getAssignmentTopicsBySubject($subdomainName, Request $request){
        $subjectId = $request->subject_id;
        return ClientAssignmentTopic::getAssignmentTopicsBySubject($subjectId);
    }

    protected function getAssignmentTopicsForDocumentsBySubjectForUser($subdomainName, Request $request){
        $subjectId = $request->subject_id;
        return ClientAssignmentTopic::getAssignmentTopicsForDocumentsBySubjectForUser($subjectId);
    }

    protected function getAssignments($subdomainName, Request $request){
        $results = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $subject = $request->subject;
        $topic = $request->topic;

        $query = ClientAssignmentQuestion::where('client_id', $clientId);
        if($subject > 0){
            $query->where('client_assignment_subject_id', $subject);
        }
        if($topic > 0){
            $query->where('client_assignment_topic_id', $topic);
        }
        $assignments = $query->get();
        if(is_object($assignments) and false == $assignments->isEmpty()){
            foreach($assignments as $index => $assignment){
                $results[$index]['id'] = $assignment->id;
                $results[$index]['question'] = mb_strimwidth($assignment->question, 0, 400, "...");
                $results[$index]['subject'] = $assignment->subject->name;
                $results[$index]['topic'] = $assignment->topic->name;
            }
        }
        return $results;
    }

    protected function getAssignDocuments($subdomainName, Request $request){
        $results = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $subject = $request->subject;
        $topic = $request->topic;

        $query = ClientAssignmentQuestion::where('client_id', $clientId)->where('question','');
        if($subject > 0){
            $query->where('client_assignment_subject_id', $subject);
        }
        if($topic > 0){
            $query->where('client_assignment_topic_id', $topic);
        }
        $assignments = $query->get();
        if(is_object($assignments) and false == $assignments->isEmpty()){
            foreach($assignments as $index => $assignment){
                $results[$index]['id'] = $assignment->id;
                $results[$index]['attached_link_str'] = $assignment->attached_link;
                $results[$index]['attached_link'] = basename($assignment->attached_link);
                $results[$index]['subject'] = $assignment->subject->name;
                $results[$index]['topic'] = $assignment->topic->name;
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
        Session::put('client_course', $clientCourse->name);
        Session::save();
        $loginUser = Auth::guard('clientuser')->user();
        if(!empty($loginUser->email) && filter_var($loginUser->email, FILTER_VALIDATE_EMAIL)){
            $email = $loginUser->email;
        } else {
            $email = 'vchipdesigng8@gmail.com';
        }

        if(1 == preg_match('/^[0-9]{10}+$/', $loginUser->phone)){
            $phone = $loginUser->phone;
        } else {
            $phone = '7722078597';
        }
        $client = $loginUser->client;
        if('local' == \Config::get('app.env')){
            $redirectUrl = url('redirectCoursePayment');
            $webhookUrl = url('webhook');
        } else {
            $redirectUrl = 'https://'.trim($client->subdomain).'/redirectCoursePayment';
            $webhookUrl = 'https://'.trim($client->subdomain).'/webhook';
        }

        $purchasePostFields = [
                                'purpose' => 'purchase '. $clientCourse->name,
                                'amount'  =>   $clientCourse->price,
                                'buyer_name' => $loginUser->name,
                                'email'  => $email,
                                'phone'  => $phone,
                                'send_email' => 'True',
                                'send_sms' => 'False',
                                'redirect_url' => $redirectUrl,
                                'webhook'   => $webhookUrl,
                                'allow_repeated_payments' => 'False'
                            ];

        $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', $loginUser->client_id)->first();

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
          $instamojoErrors .= (string)$err;
        } else {
            $result = json_decode($response);
            if(is_object($result) && !empty($result->longurl)){
                header("Location: $result->longurl");
                exit();
            } else {
                if(is_object($result) && false == $result->success){
                    $instamojoErrors .= (string)$result->message;
                    if(!empty($instamojoErrors)){
                        Mail::to('vchipdesigng8@gmail.com')->send(new PaymentGatewayErrors($instamojoErrors));
                    }
                    return Redirect::to('online-courses')->withErrors([$result->message]);
                } else {
                    return Redirect::to('online-courses')->withErrors(['some thing went wrong. please try after some time.']);
                }
            }
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
            $loginUser = Auth::guard('clientuser')->user();
            $userId = $loginUser->id;
            $clientId = $loginUser->client_id;
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
                        $clientCourse = Session::get('client_course');
                        $newUserCourse = new ClientUserPurchasedCourse;
                        $newUserCourse->user_id = $userId;
                        $newUserCourse->course_id = $clientCourseId;
                        $newUserCourse->client_id = $clientId;
                        $newUserCourse->payment_id = $paymentId;
                        $newUserCourse->price = $result->amount;
                        $newUserCourse->course = $clientCourse;
                        $newUserCourse->save();

                        $registerCourse = new RegisterClientOnlineCourses;
                        $registerCourse->client_user_id = $userId;
                        $registerCourse->client_online_course_id = $clientCourseId;
                        $registerCourse->client_id = $clientId;
                        $registerCourse->save();

                        $clientUserPayment = new ClientUserPayment;
                        $clientUserPayment->client_id = $clientId;
                        $clientUserPayment->clientuser_id = $userId;
                        $clientUserPayment->payment_request_id = $paymentRequestId;
                        $clientUserPayment->payment_id = $paymentId;
                        $clientUserPayment->save();
                        DB::connection('mysql2')->commit();
                        Session::remove('client_course_id');
                        Session::remove('client_course');

                        // mail to client
                        $to = $loginUser->client->email;
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
        $loginUser = Auth::guard('clientuser')->user();
        if(0 == $subCategory->client_id && 0 == $subCategory->category_id){
            $selectedPayableSubCategory = PayableClientSubCategory::getPayableSubCategoryByClientIdBySubCategoryId($loginUser->client_id, $subCategory->id);
            if(!is_object($selectedPayableSubCategory)){
                return redirect()->back()->withErrors('something went wrong.');
            }
            $categoryId = $selectedPayableSubCategory->category_id;
            $price = $selectedPayableSubCategory->client_user_price;
        } else {
            $categoryId = $subCategory->category_id;
            $price = $subCategory->price;
        }
        Session::put('client_test_sub_category_id', $subCategory->id);
        Session::put('client_test_category_id', $categoryId);
        Session::put('client_test_sub_category', $subCategory->name);
        Session::save();
        if(!empty($loginUser->email) && filter_var($loginUser->email, FILTER_VALIDATE_EMAIL)){
            $email = $loginUser->email;
        } else {
            $email = 'vchipdesigng8@gmail.com';
        }

        if(1 == preg_match('/^[0-9]{10}+$/', $loginUser->phone)){
            $phone = $loginUser->phone;
        } else {
            $phone = '7722078597';
        }
        $client = $loginUser->client;
        if('local' == \Config::get('app.env')){
            $redirectUrl = url('redirectTestSubCategoryPayment');
            $webhookUrl = url('webhook');
        } else {
            $redirectUrl = 'https://'.trim($client->subdomain).'/redirectTestSubCategoryPayment';
            $webhookUrl = 'https://'.trim($client->subdomain).'/webhook';
        }
        $purchasePostFields = [
                                'purpose' => 'purchase '. $subCategory->name,
                                'amount'  =>   $price,
                                'buyer_name' => $loginUser->name,
                                'email'  => $email,
                                'phone'  => $phone,
                                'send_email' => 'True',
                                'send_sms' => 'False',
                                'redirect_url' => $redirectUrl,
                                'webhook'   => $webhookUrl,
                                'allow_repeated_payments' => 'False'
                            ];

        $clientUserAuth = UserBasedAuthentication::where('vchip_client_id', $loginUser->client_id)->first();

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
                if(is_object($result) && false == $result->success){
                    return Redirect::to('online-tests')->withErrors([$result->message]);
                } else {
                    return Redirect::to('online-tests')->withErrors(['some thing went wrong. please try after some time.']);
                }
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
            $loginUser = Auth::guard('clientuser')->user();
            $userId = $loginUser->id;
            $clientId = $loginUser->client_id;
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
                        $clientSubCategoryId = Session::get('client_test_sub_category_id');
                        $clientCategoryId = Session::get('client_test_category_id');
                        $clientSubCategory = Session::get('client_test_sub_category');

                        $newTestSubCategory = new ClientUserPurchasedTestSubCategory;
                        $newTestSubCategory->user_id = $userId;
                        $newTestSubCategory->test_category_id = $clientCategoryId;
                        $newTestSubCategory->test_sub_category_id = $clientSubCategoryId;
                        $newTestSubCategory->client_id = $clientId;
                        $newTestSubCategory->payment_id = $paymentId;
                        $newTestSubCategory->price = $result->amount;
                        $newTestSubCategory->test_sub_category = $clientSubCategory;
                        $newTestSubCategory->save();

                        $clientUserPayment = new ClientUserPayment;
                        $clientUserPayment->client_id = $clientId;
                        $clientUserPayment->clientuser_id = $userId;
                        $clientUserPayment->payment_request_id = $paymentRequestId;
                        $clientUserPayment->payment_id = $paymentId;
                        $clientUserPayment->save();

                        $subCategoryPapers = ClientOnlineTestSubjectPaper::getPapersBySubCategoryId($clientSubCategoryId);
                        if(is_object($subCategoryPapers) && false == $subCategoryPapers->isEmpty()){
                            foreach($subCategoryPapers as $subCategoryPaper){
                                $registeredTestPaper = RegisterClientOnlinePaper::firstOrNew(['client_user_id' => $userId, 'client_paper_id' => $subCategoryPaper->id, 'client_id' =>  $clientId]);
                                if(is_object($registeredTestPaper) && empty($registeredTestPaper->id)){
                                    $registeredTestPaper->save();
                                }
                            }
                        }

                        DB::connection('mysql2')->commit();
                        Session::remove('client_test_sub_category_id');
                        Session::remove('client_test_category_id');
                        Session::remove('client_test_sub_category');

                        // mail to client
                        $to = $loginUser->client->email;
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

    protected function myAttendance($subdomainName,Request $request){
        $selectedYear = json_decode($request->get('year'));
        $selectedBatch = json_decode($request->get('batch'));

        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $batches = [];
        $userFirstBatchId = 0;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        if(!empty($clientUser->batch_ids)){
            $userBatchIds = explode(',', $clientUser->batch_ids);
            sort($userBatchIds);
            $userFirstBatchId = $userBatchIds[0];
            if(count($userBatchIds) > 0){
                $batches = ClientBatch::find($userBatchIds);
            }
        }

        if(!empty($selectedYear) && !empty($selectedBatch)){
            $result = $this->getAttendanceByBatchByYearByUserByClient($selectedBatch,$selectedYear,$clientUserId,$clientId);
            $defaultDate = $selectedYear.'-'.date('m').'-'.date('d');
        } else {
            $result = $this->getAttendanceByBatchByYearByUserByClient($userFirstBatchId,date('Y'),$clientUserId,$clientId);
            $defaultDate = date('Y-m-d');
        }
        $attendanceStats = implode(',', $result['attendanceStats']);
        $allPresentDates = implode(',', $result['allPresentDates']);
        $allAbsentDates = implode(',', $result['allAbsentDates']);
        $currnetYear = date('Y');
        return view('clientuser.dashboard.myAttendance', compact('batches','currnetYear','selectedYear','selectedBatch', 'userFirstBatchId', 'allPresentDates', 'allAbsentDates','attendanceStats','defaultDate'));
    }

    protected function getAttendanceByBatchByYearByUserByClient($batch,$year,$clientUserId,$clientId){
        $attendanceCount = [];
        $result = [];
        if($batch > 0 && $year > 0){
            $allAttendance = ClientUserAttendance::where('client_batch_id','=', $batch)->whereYear('attendance_date', $year)->where('client_id', $clientId)->orderBy('attendance_date')->get();

            if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
                foreach($allAttendance as $attendance){
                    $studentIds = explode(',', $attendance->student_ids);
                    $month =(int) explode('-', $attendance->attendance_date)[1];
                    // $date = explode('-', $attendance->attendance_date)[2];
                    if(in_array($clientUserId, $studentIds)){
                        $attendanceCount[$month]['present_date'][$attendance->id] = $attendance->attendance_date;
                    } else {
                        $attendanceCount[$month]['absent_date'][$attendance->id] = $attendance->attendance_date;
                    }
                    $attendanceCount[$month]['attendance_date'][$attendance->id] = $attendance->attendance_date;
                }
            }
        }
        $allAbsentDates = [];
        $allPresentDates = [];
        $attendanceStats = [];
        if(count($attendanceCount) > 0){
            foreach($attendanceCount as $month => $arr) {
                if(isset($arr['present_date'])){
                    $presentDates = $arr['present_date'];
                } else {
                    $presentDates = [];
                }
                $attendanceDates = $arr['attendance_date'];
                $noOfPresentDays = count($presentDates);
                $noOfAttendanceDays = count($attendanceDates);
                $noOfAbsentDays = $noOfAttendanceDays - $noOfPresentDays;
                $firstDate = $year.'-0'.$month.'-01';
                $attendanceStats[] = $firstDate.':'.$noOfPresentDays.'-'.$noOfAbsentDays.'-'.$noOfAttendanceDays;
                foreach( $attendanceDates as $id => $date) {
                    if(isset($presentDates[$id])){
                        $allPresentDates[] = $date;
                    } else {
                        $allAbsentDates[] = $date;
                    }
                }
            }
        }
        $result['allPresentDates'] = $allPresentDates;
        $result['allAbsentDates'] = $allAbsentDates;
        $result['attendanceStats'] = $attendanceStats;
        return $result;
    }

    protected function myOfflineTestResults($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $batches = [];
        if(!empty($clientUser->batch_ids)){
            $userBatchIds = explode(',', $clientUser->batch_ids);
            $userFirstBatchId = $userBatchIds[0];
            if(count($userBatchIds) > 0){
                $batches = ClientBatch::find($userBatchIds);
            }
        }
        $results = ClientOfflinePaperMark::getOfflinePaperMarksByUserIdByClientId($clientUserId,$clientId);
        return view('clientuser.dashboard.myOfflineTestResults', compact('batches','results'));
    }

    protected function showUserOfflineTestResultsByBatchIdByUserId($subdomainName,Request $request){
        $results = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $batchId = $request->get('batch_id');
        $offlineMarks = ClientOfflinePaperMark::getOfflinePaperMarksByBatchIdByUserIdByClientId($batchId,$clientUserId,$clientId);
        if(is_object($offlineMarks) && false == $offlineMarks->isEmpty()){
            foreach($offlineMarks as $offlineMark){
                $results['scores'][] = [
                    'id' => $offlineMark->id,
                    'batch' => $offlineMark->batch->name,
                    'paper' => $offlineMark->paper->name,
                    'marks' => trim($offlineMark->marks),
                    'total_marks' => $offlineMark->total_marks,
                ];
                if('' != trim($offlineMark->marks)){
                    $results['ranks'][$offlineMark->id] = $offlineMark->rank();
                } else {
                    $results['ranks'][$offlineMark->id] = 'absent';
                }

            }
        }
        return $results;
    }

    protected function myMessage($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $batchIds = [];
        $groupMessages = [];
        $individualMessages = [];
        $myMessages = [];
        if(!empty($clientUser->batch_ids)){
            $batchIds = explode(',', $clientUser->batch_ids);
            $batches = ClientBatch::getBatchesByClientIdByBatchIds($clientId,$batchIds);
            if(is_object($batches) && false == $batches->isEmpty()){
                foreach($batches as $batche){
                    $batchNames[$batche->id] = $batche->name;
                }
            }
        } else {
            $batchIds = [0];
        }
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        if(count($batchIds) > 0){
            $groupMessages = ClientMessage::getMessagesByBatchIdsByClientId($batchIds,$clientId);
            if(is_object($groupMessages) && false == $groupMessages->isEmpty()){
                foreach($groupMessages as $groupMessage){
                    $myMessages[date('Y-m-d h:i:s a', strtotime($groupMessage->updated_at))] = [
                        'id' => $groupMessage->id,
                        'message' => $groupMessage->message,
                        'photo' => $groupMessage->photo,
                        'date' => date('Y-m-d h:i:s a', strtotime($groupMessage->updated_at)),
                        'is_group_message' => 1
                    ];
                }
            }
            $individualMessages = ClientIndividualMessage::getIndividualMessagesByClientIdByBatchIds($clientId,$batchIds);
            if(is_object($individualMessages) && false == $individualMessages->isEmpty()){
                foreach($individualMessages as $individualMessage){
                    if(!empty($individualMessage->messages)){
                        $allMessages = explode(',', $individualMessage->messages);
                        if(count($allMessages) > 0){
                            foreach($allMessages as $userMessages){
                                $arrMsg = explode(':', $userMessages);
                                if($clientUserId == $arrMsg[0]){
                                    $myMessages[date('Y-m-d h:i:s a',strtotime($individualMessage->updated_at))] = [
                                        'id' => $individualMessage->id,
                                        'message' => $arrMsg[1],
                                        'batch' => $batchNames[$individualMessage->client_batch_id],
                                        'date' => date('Y-m-d h:i:s a',strtotime($individualMessage->updated_at)),
                                        'is_group_message' => 0];
                                }
                            }
                        }
                    }
                }
            }
            if(count($myMessages) > 0){
                krsort($myMessages);
            }
        }
        if($clientUser->unread_messages > 0){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientUser->unread_messages = 0;
                $clientUser->save();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return view('clientuser.dashboard.myMessage', compact('myMessages'));
    }

    protected function myEvent($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $batchIds = [];
        $events = [];
        $individualMessages = [];
        if(!empty($clientUser->batch_ids)){
            $batchIds = explode(',', $clientUser->batch_ids);
        } else {
            $batchIds = [0];
        }

        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        if(count($batchIds) > 0){
            $events = ClientMessage::getEventsByBatchIdsByClientId($batchIds,$clientId);
        }

        return view('clientuser.dashboard.myEvent', compact('events'));
    }

    protected function addEmail($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateAddEmail);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        if(!empty($request->get('email'))){
            $checkEmail = Clientuser::where('email', $request->get('email'))->where('client_id', $clientId)->where('id', '!=', $clientUserId)->first();
            if(is_object($checkEmail)){
                return Redirect::to('profile')->withErrors('The email id '.$request->get('email').' is already exist.');
            }
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $user = Clientuser::addEmail($request);
            if(is_object($user)){
                DB::connection('mysql2')->commit();
                if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                    // send mail
                    $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($clientUserEmail);
                    return Redirect::to('profile')->with('message', 'Verification email sent successfully. please check email and verify.');
                } else {
                    return Redirect::to('profile')->with('message', 'Email added successfully!');
                }
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('profile');
    }

    protected function verifyEmail($subdomainName,Request $request){
        $user = Auth::guard('clientuser')->user();
        if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
            // After creating the user send an email with the random token generated in the create method above
            $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
            Mail::to($user->email)->send($clientUserEmail);
            return Redirect::to('profile')->with('message', 'Verification email sent successfully. please check email and verify.');
        }
        return Redirect::to('profile');
    }

    protected function updatePassword( Request $request){
        $v = Validator::make($request->all(), $this->validateUpdatePassword);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $oldPassword = $request->get('old_password');
            $newPassword = $request->get('password');
            $user = Auth::guard('clientuser')->user();
            $hashedPassword = $user->password;
            if(Hash::check($oldPassword, $hashedPassword)){
                $user->password = bcrypt($newPassword);
                $user->save();
                DB::connection('mysql2')->commit();
                Auth::logout();
                return Redirect::to('/')->with('message', 'Password updated successfully. please login with new password.');
            } else {
                return redirect()->back()->withErrors('please enter correct old password.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }

        return redirect('/');
    }

    protected function sendClientUserOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::checkMobileAndSendOpt($request,$mobile);
    }

    protected function updateMobile(Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                Clientuser::updateMobile($request);
                DB::connection('mysql2')->commit();
                if(Cache::has($userMobile) && Cache::has('mobile')){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('profile')->with('message', 'Mobile number updated successfully.');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        } else {
            return Redirect::to('profile')->withErrors('Entered wrong otp.');
        }
    }

    protected function verifyMobile(Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                Clientuser::verifyMobile($request);
                DB::connection('mysql2')->commit();
                if(Cache::has($userMobile) && Cache::has('mobile')){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('profile')->with('message', 'Mobile number verified successfully.');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        } else {
            return Redirect::to('profile')->withErrors('Entered wrong otp.');
        }
    }

    protected function myOfflinePayments($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $batches = [];
        if(!empty($clientUser->batch_ids)){
            $userBatchIds = explode(',', $clientUser->batch_ids);
            $userFirstBatchId = $userBatchIds[0];
            if(count($userBatchIds) > 0){
                $batches = ClientBatch::find($userBatchIds);
            }
        }
        $payments = ClientOfflinePayment::where('client_id',$clientId)->where('clientuser_id', $clientUserId)->orderBy('id', 'desc')->get();
        return view('clientuser.dashboard.myOfflinePayments', compact('batches','payments'));
    }

    protected function getOfflinePaymentsByBatchIdByUserId(Request $request){
        $batchId = $request->get('batch_id');
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $result = [];
        $payments = ClientOfflinePayment::where('client_id',$clientId)->where('clientuser_id', $clientUserId)->where('client_batch_id', $batchId)->orderBy('id', 'desc')->get();
        if(is_object($payments) && false == $payments->isEmpty()){
            foreach($payments as $payment){
                $result[] = [
                    'id' => $payment->id,
                    'batch' => $payment->batch->name,
                    'date' => date('Y-m-d',strtotime($payment->created_at)),
                    'amount' => $payment->amount,
                ];
            }
        }
        return $result;
    }

    protected function myOnlinePayments($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $userPurchasedCourses = ClientUserPurchasedCourse::getClientUserPurchasedCourses($clientId, $clientUserId);
        $userPurchasedSubCategories = ClientUserPurchasedTestSubCategory::getClientUserPurchasedTestSubCategories($clientId, $clientUserId);
        return view('clientuser.dashboard.myOnlinePayments', compact('userPurchasedCourses','userPurchasedSubCategories'));
    }

    protected function uploadedTransactions($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $transactions = ClientUploadTransaction::where('client_id',$clientId)->where('clientuser_id', $clientUserId)->orderBy('id', 'desc')->get();
        return view('clientuser.dashboard.uploadTransactionList', compact('transactions'));
    }

    protected function createUploadTransaction($subdomainName,Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $clientResult = InputSanitise::checkUserClient($request, $clientUser);
        if( !is_object($clientResult)){
            return Redirect::away($clientResult);
        }
        $batches = [];
        if(!empty($clientUser->batch_ids)){
            $userBatchIds = explode(',', $clientUser->batch_ids);
            $userFirstBatchId = $userBatchIds[0];
            if(count($userBatchIds) > 0){
                $batches = ClientBatch::find($userBatchIds);
            }
        }
        return view('clientuser.dashboard.uploadTransactionCreate', compact('batches'));
    }

    protected function storeUploadTransaction($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateUpload);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $transaction = ClientUploadTransaction::addUploadTransaction($request);
            if(is_object($transaction)){
                DB::connection('mysql2')->commit();
                return Redirect::to('uploadedTransactions')->with('message', 'Upload Transaction successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while creating upload transaction.');
        }
        return Redirect::to('uploadedTransactions');
    }

    protected function myCalendar(){
        $results = [];
        $calendarData = [];
        $allBatches = [];
        $dayColours = '';
        $clientUser = Auth::guard('clientuser')->user();
        if(!is_object($clientUser)){
            return Redirect::to('/');
        }
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $userBatches = [];
        if(1 == $clientUser->user_type){
            if(!empty($clientUser->batch_ids)){
                $userBatches = explode(',', $clientUser->batch_ids);
                array_push($userBatches, '0');
            } else {
                $userBatches = [0];
            }
        }
        $batches = ClientBatch::where('client_id', $clientId)->get();
        if(is_object($batches) && false == $batches->isEmpty()){
            $allBatches[0] = 'All';
            foreach($batches as $batch){
                $allBatches[$batch->id] = $batch->name;
            }
        }
        if(2 == $clientUser->user_type){
            $emergencyNotices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 1)->get();
            if(is_object($emergencyNotices) && false == $emergencyNotices->isEmpty()){
                foreach($emergencyNotices as $notice){
                    if(!isset($results[$notice->date])){
                        $results[$notice->date] = [
                            'start' => $notice->date,
                            'color' => 'yellow',
                        ];
                    }
                    $calendarData[$notice->date]['emergency_notices'][] = [
                        'title' => $notice->notice,
                        'batch' => $allBatches[$notice->client_batch_id]
                    ];
                }
            }
        } else if(1 == $clientUser->user_type){
            if(count($userBatches) > 0){
                $emergencyNotices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 1)->whereIn('client_batch_id', $userBatches )->get();
                if(is_object($emergencyNotices) && false == $emergencyNotices->isEmpty()){
                    foreach($emergencyNotices as $notice){
                        if(!isset($results[$notice->date])){
                            $results[$notice->date] = [
                                'start' => $notice->date,
                                'color' => 'yellow',
                            ];
                        }
                        $calendarData[$notice->date]['emergency_notices'][] = [
                            'title' => $notice->notice,
                            'batch' => $allBatches[$notice->client_batch_id]
                        ];
                    }
                }
            }
        }

        if(2 == $clientUser->user_type){
            $exams = ClientExam::where('client_id', $clientId )->get();
            if(is_object($exams) && false == $exams->isEmpty()){
                foreach($exams as $exam){
                    if(!isset($results[$exam->date])){
                        $results[$exam->date] = [
                            'start' => $exam->date,
                            'color' => 'red',
                        ];
                    }
                    $calendarData[$exam->date]['exams'][] = [
                        'title' => $exam->name,
                        'subject' => $exam->subject,
                        'topic' => $exam->topic,
                        'from' => $exam->from_time,
                        'to' => $exam->to_time,
                        'batch' => $allBatches[$exam->client_batch_id],
                        'marks' => $exam->marks,
                        'exam_type' => $exam->exam_type
                    ];
                }
            }
        } else if(1 == $clientUser->user_type){
            if(count($userBatches) > 0){
                $exams = ClientExam::where('client_id', $clientId )->whereIn('client_batch_id', $userBatches )->get();
                if(is_object($exams) && false == $exams->isEmpty()){
                    foreach($exams as $exam){
                        if(!isset($results[$exam->date])){
                            $results[$exam->date] = [
                                'start' => $exam->date,
                                'color' => 'red',
                            ];
                        }
                        $calendarData[$exam->date]['exams'][] = [
                            'title' => $exam->name,
                            'subject' => $exam->subject,
                            'topic' => $exam->topic,
                            'from' => $exam->from_time,
                            'to' => $exam->to_time,
                            'batch' => $allBatches[$exam->client_batch_id],
                            'marks' => $exam->marks,
                            'exam_type' => $exam->exam_type
                        ];
                    }
                }
            }
        }
        if(2 == $clientUser->user_type){
            $holidays = ClientHoliday::where('client_id', $clientId )->get();
            if(is_object($holidays) && false == $holidays->isEmpty()){
                foreach($holidays as $holiday){
                    if(!isset($results[$holiday->date])){
                        $results[$holiday->date] = [
                            'start' => $holiday->date,
                            'color' => 'green',
                        ];
                    }
                    $calendarData[$holiday->date]['holiday'][] = [
                        'title' => ($holiday->note)?:'Holiday',
                        'batch' => $allBatches[$holiday->client_batch_id]
                    ];
                }
            }
        } else if(1 == $clientUser->user_type){
            if(count($userBatches) > 0){
                $holidays = ClientHoliday::where('client_id', $clientId )->whereIn('client_batch_id', $userBatches )->get();
                if(is_object($holidays) && false == $holidays->isEmpty()){
                    foreach($holidays as $holiday){
                        if(!isset($results[$holiday->date])){
                            $results[$holiday->date] = [
                                'start' => $holiday->date,
                                'color' => 'green',
                            ];
                        }
                        $calendarData[$holiday->date]['holiday'][] = [
                            'title' => ($holiday->note)?:'Holiday',
                            'batch' => $allBatches[$holiday->client_batch_id]
                        ];
                    }
                }
            }
        }
        if(2 == $clientUser->user_type){
            $notices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 0)->get();
            if(is_object($notices) && false == $notices->isEmpty()){
                foreach($notices as $notice){
                    if(!isset($results[$notice->date])){
                        $results[$notice->date] = [
                            'start' => $notice->date,
                            'color' => 'blue',
                        ];
                    }
                    $calendarData[$notice->date]['notices'][] = [
                        'title' => $notice->notice,
                        'batch' => $allBatches[$notice->client_batch_id]
                    ];
                }
            }
        } else if(1 == $clientUser->user_type){
            if(count($userBatches) > 0){
                $notices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 0)->whereIn('client_batch_id', $userBatches )->get();
                if(is_object($notices) && false == $notices->isEmpty()){
                    foreach($notices as $notice){
                        if(!isset($results[$notice->date])){
                            $results[$notice->date] = [
                                'start' => $notice->date,
                                'color' => 'blue',
                            ];
                        }
                        $calendarData[$notice->date]['notices'][] = [
                            'title' => $notice->notice,
                            'batch' => $allBatches[$notice->client_batch_id]
                        ];
                    }
                }
            }
        }

        if(2 == $clientUser->user_type){
            $classes = ClientClass::where('client_id', $clientId )->where('clientuser_id', $clientUserId )->get();
            if(is_object($classes) && false == $classes->isEmpty()){
                foreach($classes as $class){
                    if(!isset($results[$class->date])){
                        $results[$class->date] = [
                            'start' => $class->date,
                            'color' => '#e6004e',
                        ];
                    }
                    $calendarData[$class->date]['classes'][] = [
                        'subject' => $class->subject,
                        'topic' => $class->topic,
                        'from' => $class->from_time,
                        'to' => $class->to_time,
                        'batch' => $allBatches[$class->client_batch_id]
                    ];
                }
            }
        } else if(1 == $clientUser->user_type){
            if(count($userBatches) > 0){
                $classes = ClientClass::where('client_id', $clientId )->whereIn('client_batch_id', $userBatches )->get();
                if(is_object($classes) && false == $classes->isEmpty()){
                    foreach($classes as $class){
                        if(!isset($results[$class->date])){
                            $results[$class->date] = [
                                'start' => $class->date,
                                'color' => '#e6004e',
                            ];
                        }
                        $calendarData[$class->date]['classes'][] = [
                            'subject' => $class->subject,
                            'topic' => $class->topic,
                            'from' => $class->from_time,
                            'to' => $class->to_time,
                            'batch' => $allBatches[$class->client_batch_id]
                        ];
                    }
                }
            }
        }

        if(count($results) > 0){
            foreach($results as $result){
                if(empty($dayColours)){
                    $dayColours = $result['start'].':'.$result['color'];
                } else {
                    $dayColours .= ','.$result['start'].':'.$result['color'];
                }
            }
        }
        return view('clientuser.dashboard.myCalendar', compact('clientUser','dayColours','calendarData'));
    }

    protected function myParent(){
        $loginUser = Auth::guard('clientuser')->user();
        return view('clientuser.dashboard.myParent',compact('loginUser'));
    }

    protected function sendClientUserParentAddOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::checkMobileAndSendOptForParent($request,$mobile);
    }

    protected function addParent(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $user = Clientuser::addParent($request);
            if(is_object($user)){
                DB::connection('mysql2')->commit();
                return Redirect::to('myParent')->with('message', 'Parent Updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('myParent');
    }

    protected function myIndividualMessage(Request $request){
        $clientUser = Auth::guard('clientuser')->user();
        if(!is_object($clientUser)){
            return Redirect::to('/');
        }
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        $userBatches = [];
        $myMessages = [];
        $batchNames = [];
        if(!empty($clientUser->batch_ids)){
            $userBatches = explode(',', $clientUser->batch_ids);
            $batches = ClientBatch::getBatchesByClientIdByBatchIds($clientId,$userBatches);
            if(is_object($batches) && false == $batches->isEmpty()){
                foreach($batches as $batche){
                    $batchNames[$batche->id] = $batche->name;
                }
            }
        }
        if(count($userBatches) > 0){
            $individualMessages = ClientIndividualMessage::getIndividualMessagesByClientIdByBatchIds($clientId,$userBatches);
            if(is_object($individualMessages) && false == $individualMessages->isEmpty()){
                foreach($individualMessages as $individualMessage){
                    if(!empty($individualMessage->messages)){
                        $allMessages = explode(',', $individualMessage->messages);
                        if(count($allMessages) > 0){
                            foreach($allMessages as $userMessages){
                                $arrMsg = explode(':', $userMessages);
                                if($clientUserId == $arrMsg[0]){
                                    $myMessages[] = [ 'message' => $arrMsg[1],'batch' => $batchNames[$individualMessage->client_batch_id], 'date' => date('Y-m-d h:i:s a',strtotime($individualMessage->created_at))];
                                }
                            }
                        }
                    }
                }
            }
        }
        return view('clientuser.dashboard.myIndividualMessage', compact('myMessages'));
    }

    protected function myDiscussion($subdomainName){
        $currentUser = Auth::guard('clientuser')->user();
        if(!is_object($currentUser)){
            return Redirect::to('/');
        }
        $discussionCategories = ClientDiscussionCategory::getCategoriesByClient();
        $posts = ClientDiscussionPost::getPostsByClient();
        $isClient = 0;
        $likesCount = ClientDiscussionLike::getPostLikes();
        $commentLikesCount = ClientDiscussionLike::getCommentLikes();
        $subcommentLikesCount = ClientDiscussionLike::getSubCommentLikes();
        return view('client.discussion.discussion', compact('subdomainName','posts','discussionCategories','currentUser','isClient','likesCount','commentLikesCount','subcommentLikesCount'));
    }

    protected function myQuestions($subdomainName,Request $request){
        $currentUser = Auth::guard('clientuser')->user();
        if(!is_object($currentUser)){
            return Redirect::to('/');
        }
        $posts = ClientDiscussionPost::where('client_id',$currentUser->client_id)->where('clientuser_id',$currentUser->id)->orderBy('id','desc')->get();
        $isClient = 0;
        $discussionCategories = ClientDiscussionCategory::getCategoriesByClient();
        $likesCount = ClientDiscussionLike::getPostLikes();
        return view('client.discussion.myQuestions', compact('subdomainName','posts','currentUser','discussionCategories','likesCount','isClient'));
    }

    protected function myReplies($subdomainName,Request $request){
        $currentUser = Auth::guard('clientuser')->user();
        if(!is_object($currentUser)){
            return Redirect::to('/');
        }
        $postIds = [];
        $discussionComments = ClientDiscussionComment::where('client_id',$currentUser->client_id)->where('clientuser_id',$currentUser->id)->select('Client_discussion_post_id')->get();
        if(false == $discussionComments->isEmpty()){
            foreach($discussionComments as $discussionComment){
                $postIds[]= $discussionComment->Client_discussion_post_id;
            }
            $postIds = array_unique($postIds);
        }

        $posts = ClientDiscussionPost::where('client_id',$currentUser->client_id)->whereIn('id', $postIds)->orderBy('id','desc')->get();
        $isClient = 0;
        $likesCount = ClientDiscussionLike::getPostLikes();
        $commentLikesCount = ClientDiscussionLike::getCommentLikes();
        $subcommentLikesCount = ClientDiscussionLike::getSubCommentLikes();
        return view('client.discussion.myReplies', compact('subdomainName','posts','currentUser','isClient','likesCount','commentLikesCount','subcommentLikesCount'));
    }

    protected function myGallery(Request $request){
        $currentUser = Auth::guard('clientuser')->user();
        if(!is_object($currentUser)){
            return Redirect::to('/');
        }
        $galleryImages = [];
        $clientGalleryTypes = [];
        $clientGalleryImages = ClientGalleryImage::where('client_id', $currentUser->client_id)->get();
        if(is_object($clientGalleryImages) && false == $clientGalleryImages->isEmpty()){
            foreach($clientGalleryImages as $clientGalleryImage){
                $galleryImages[$clientGalleryImage->client_gallery_type_id] = $clientGalleryImage->images;
            }
            if(count($galleryImages) > 0){
                $galleryTypeIds = array_keys($galleryImages);
                $clientGalleryTypes = ClientGalleryType::find(array_unique($galleryTypeIds));
            }
        }
        return view('clientuser.dashboard.myGallery', compact('galleryImages','clientGalleryTypes'));
    }
}