<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Libraries\InputSanitise;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientOnlineCourse;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\User;
use App\Models\ClientScore;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientUserPurchasedCourse;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\PayableClientSubCategory;
use App\Models\ClientBatch;
use Auth, Redirect, View, DB, Session, Validator, Hash,Cache;
use Excel;

class ClientUsersInfoController extends BaseController
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

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateUpdatePassword = [
        'old_password' => 'required',
        'password' => 'required|different:old_password|confirmed',
        'password_confirmation' => 'required|same:password',
    ];

    protected function allUsers($subdomainName,Request $request){
        $purchasedPayableSubCategories = [];
        $clientPurchasedSubCategories = [];
        $clientId = Auth::guard('client')->user()->id;
        $clientusers = Clientuser::getAllStudentsByClientId($clientId);
        $courses = ClientOnlineCourse::getPaidCourseAssocaitedWithVideos($subdomainName);
        $userPurchasedCourses = ClientUserPurchasedCourse::getClientUserCourses($clientId);
        $userPurchasedTestSubCategories = ClientUserPurchasedTestSubCategory::getClientUserTestSubCategories($clientId);
        $testSubCategories = ClientOnlineTestSubCategory::showPaidSubCategoriesAssociatedWithQuestion($request);
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($clientId);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        }
        if(count(array_keys($purchasedPayableSubCategories)) > 0){
            $clientPurchasedSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
        }
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.allUsers.allUsers', compact('clientusers', 'courses', 'userPurchasedCourses', 'userPurchasedTestSubCategories', 'testSubCategories', 'clientPurchasedSubCategories', 'purchasedPayableSubCategories', 'subdomainName','batches'));
    }

    protected function searchUsers($subdomainName,Request $request){
        return Clientuser::searchUsers($request);
    }

    protected function getStudentsByBatchId($subdomainName,Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $batchId = $request->get('batch_id');
        return Clientuser::getStudentsByClientIdByBatchId($clientId,$batchId);
    }

    protected function deleteStudent($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $deleteStudent = Clientuser::deleteStudent($request);

            if('true' == $deleteStudent){
                DB::connection('mysql2')->commit();
            }

        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return Clientuser::searchUsers($request);
    }

    protected function changeClientUserApproveStatus(Request $request){
        $approveStatus = 'false';
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $approveStatus = Clientuser::changeClientUserApproveStatus($request);

            if('true' == $approveStatus){
                DB::connection('mysql2')->commit();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return $approveStatus;
    }

    protected function userTestResults($subdomainName,Request $request,$id=NULL){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $results = [];
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        $id = json_decode($id);
        if(empty($id)){
            $id = Session::get('client_selected_user');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $students = Clientuser::getAllStudentsByClientId($selectedStudent->client_id);
            $results = ClientScore::where('client_user_id', $id)->get();
            Session::set('client_selected_user', $id);
        } else {
            $students = Clientuser::getAllStudentsByClientId($clientId);
        }

        $barchartLimits = range(100, 0, 10);
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.allUsers.userTestResults', compact('students', 'results', 'selectedStudent','barchartLimits', 'subdomainName','loginUser','batches'));
    }

    protected function showUserTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        $scores = ClientScore::getClientScoreByUserId($studentId);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank();
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        if($request->get('student_id') > 0){
            Session::set('client_selected_user', $studentId);
        }
        return $result;
    }

    protected function userCourses($subdomainName,Request $request,$id=NULL){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];

        $students = [];
        $courses = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
        }

        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $students = Clientuser::getAllStudentsByClientId($selectedStudent->client_id);
            $courses = ClientOnlineCourse::getRegisteredOnlineCoursesByUserId($id);
            Session::set('client_selected_user', $id);
        } else {
            $students = Clientuser::getAllStudentsByClientId($clientId);
        }
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.allUsers.userCourses', compact('students', 'courses', 'selectedStudent', 'subdomainName','loginUser','batches'));
    }

    protected function showUserCourses(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return ClientOnlineCourse::getRegisteredOnlineCoursesByUserId($studentId);
    }

    protected function userPlacement($subdomainName,Request $request,$id=NULL){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $students = Clientuser::getAllStudentsByClientId($selectedStudent->client_id);
            Session::set('client_selected_user', $id);
        } else {
            $students = Clientuser::getAllStudentsByClientId($clientId);
        }
        $batches = ClientBatch::getBatchesByClientId($clientId);
        return view('client.allUsers.userPlacement', compact('students', 'selectedStudent', 'subdomainName','loginUser','batches'));
    }

    protected function getStudentById(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return Clientuser::getStudentById($studentId);
    }

    protected function userVideo($subdomainName,Request $request,$id=NULL){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $students = Clientuser::getAllStudentsByClientId($selectedStudent->client_id);
            Session::set('client_selected_user', $id);
        } else {
            $students = Clientuser::getAllStudentsByClientId($clientId);
        }
        return view('client.allUsers.userVideo', compact('students', 'selectedStudent', 'subdomainName','loginUser'));
    }

    protected function updateUserVideo(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $studentId = InputSanitise::inputInt($request->get('student_id'));
            $student = Clientuser::getStudentById($studentId);
            if(is_object($student)){
                $dom = new \DOMDocument;
                $dom->loadHTML($request->recorded_video);
                $iframes = $dom->getElementsByTagName('iframe');
                foreach ($iframes as $iframe) {
                    $url =  '?enablejsapi=1';
                    if (strpos($iframe->getAttribute('src'), $url) === false) {
                        $iframe->setAttribute('src', $iframe->getAttribute('src').$url);
                    }
                }
                $html = $dom->saveHTML();
                $body = explode('<body>', $html);
                $body = explode('</body>', $body[1]);

                $student->recorded_video = $body[0];
                $student->save();
                DB::connection('mysql2')->commit();
                Session::set('client_selected_user', $studentId);
                return Redirect::to('userVideo')->with('message', 'User updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return Redirect::to('userVideo');
        }
        return Redirect::to('userVideo');
    }

    protected function allTestResults($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $scores =[];
        $categories = ClientOnlineTestCategory::where('client_id', $clientId)->get();
        return view('client.allUsers.allTestResults', compact('scores', 'categories', 'subdomainName','loginUser'));
    }

    protected function getAllTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $scores = ClientScore::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank();
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        return $result;
    }

    protected function downloadExcelResult(Request $request){
        if($request->get('paper') > 0){
            $paperName = ClientOnlineTestSubjectPaper::find($request->get('paper'))->name;
        } else {
            $paperName = $request->get('paper');
        }
        $sheetName = substr($paperName, 0, 25);
        $resultArray[] = ['Test Series Result:',$sheetName, '',''];
        $resultArray[] = [];
        // Define the Excel spreadsheet headers
        $resultArray[] = ['Sr. No.','Name','Marks', 'Rank'];
        $scores = ClientScore::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $index => $score){
                $result = [];
                $result['Sr. No.'] = $index +1;
                $result['Name'] = $score->user->name;

                $totalMarks = $score->totalMarks()['totalMarks'];
                $result['Marks'] = (string) $score->test_score.'/'.$totalMarks;
                $result['Rank'] = (string) $score->rank($request->college);

                $resultArray[] = $result;
            }
        }
        if($request->get('subject') > 0){
            $subjectName = ClientOnlineTestSubject::find($request->get('subject'))->name;
        } else {
            $subjectName = $request->get('subject');
        }

        $downloadResult = substr($subjectName.'_'.$paperName.'_result', 0, 25);
        return \Excel::create($downloadResult, function($excel) use ($sheetName,$resultArray) {
            $excel->sheet($sheetName , function($sheet) use ($resultArray)
            {
                $sheet->fromArray($resultArray);
            });
        })->download('xls');
    }

    protected function changeClientUserCourseStatus(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $status = ClientUserPurchasedCourse::changeClientUserCourseStatus($request);
            DB::connection('mysql2')->commit();
            return $status;
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return;
        }
        return;
    }

    protected function changeClientUserTestSubCategoryStatus(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $status = ClientUserPurchasedTestSubCategory::changeClientUserTestSubCategoryStatus($request);
            DB::connection('mysql2')->commit();
            return $status;
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return;
        }
        return;
    }

    protected function profile($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        return view('client.clientLogin.profile', compact('subdomainName'));
    }

    protected function updateClientProfile(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            Client::updateClientProfile($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('myprofile')->with('message', 'Client profile updated successfully!');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return redirect('myprofile');
    }

    protected function updateClientPassword( Request $request){
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
            $user = Auth::guard('client')->user();
            $hashedPassword = $user->password;
            if(Hash::check($oldPassword, $hashedPassword)){
                $user->password = bcrypt($newPassword);
                $user->save();
                DB::connection('mysql2')->commit();
                Auth::logout();
                return Redirect::to('client/login')->with('message', 'Password updated successfully. please login with new password.');
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

    protected function manageSettings($subdomainName){
        $loginUser = Auth::guard('client')->user();
        return view('client.clientLogin.settings', compact('subdomainName','loginUser'));
    }

    protected function toggleNonVerifiedEmailStatus(){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $status = Client::toggleNonVerifiedEmailStatus();
            DB::connection('mysql2')->commit();
            return $status;
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return;
        }
        return;
    }

    protected function addUsers($subdomainName,Request $request){
        return view('client.allUsers.addUsers', compact('subdomainName'));
    }

    protected function addMobileUser($subdomainName,Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientId = Auth::guard('client')->user()->id;
                Clientuser::addMobileUser($request,$clientId,Clientuser::Student);
                DB::connection('mysql2')->commit();
                if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('addUsers')->with('message', 'Mobile user added successfully.');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        } else {
            return Redirect::to('addUsers')->withErrors('Entered wrong otp.');
        }
    }

    protected function addEmailUser($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $clientId = Auth::guard('client')->user()->id;
            $result = Clientuser::addEmailUser($request,$clientId,Clientuser::Student);
            if('true' == $result['status']){
                DB::connection('mysql2')->commit();
                if(isset($result['duplicate_email']) && count($result['duplicate_email']) > 0){
                    $emailStr = implode(',', $result['duplicate_email']);
                    return Redirect::to('addUsers')->withErrors('Following Email id/User id user are not added-'.$emailStr);
                } else {
                    return Redirect::to('addUsers')->with('message', 'Email user added successfully.');
                }
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('addUsers');
    }

    protected function uploadClientUsers($subdomainName, Request $request){
        if($request->hasFile('users')){
            $path = $request->file('users')->getRealPath();
            $users = \Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                            $reader->formatDates(false);
                        })->get();
            $loginUser = Auth::guard('client')->user();

            if($users->count()){
                $allUsers = [];
                foreach ($users as $key => $user) {
                    if(!empty($user->name) && !empty($user->emailuser_id) && !empty($user->password)){
                        $allUsers[] = [
                            'name' => $user->name,
                            'phone' => ($user->phone)?$user->phone:'',
                            'email' => $user->emailuser_id,
                            'password' => $user->password,
                            'client_id' => $loginUser->id,
                            'client_approve' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                if(count($allUsers) > 0){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $result = [];
                        foreach($allUsers as $insertData){
                            $existingUser = Clientuser::where('email',$insertData['email'])->where('client_id',$insertData['client_id'])->first();
                            if(!is_object($existingUser)){
                                $user = new Clientuser;
                                $user->name = $insertData['name'];
                                $user->email = $insertData['email'];
                                $user->phone = $insertData['phone'];
                                $user->password = bcrypt($insertData['password']);
                                $user->client_id = $insertData['client_id'];
                                $user->verified = 0;
                                $user->client_approve = $insertData['client_approve'];
                                if(filter_var($insertData['email'], FILTER_VALIDATE_EMAIL)){
                                    $user->email_token = str_random(60);
                                } else {
                                    $user->email_token = '';
                                }
                                $user->user_type = Clientuser::Student;
                                $user->save();
                                if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                                    $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                                    Mail::to($user->email)->send($clientUserEmail);
                                }
                            } else {
                                $result['duplicate_email'][] = $insertData['email'];
                            }
                        }
                        DB::connection('mysql2')->commit();
                        if(isset($result['duplicate_email']) && count($result['duplicate_email']) > 0){
                            $emailStr = implode(',', $result['duplicate_email']);
                            return Redirect::to('addUsers')->withErrors('Following Email id/User id user are not added-'.$emailStr);
                        } else {
                            return Redirect::to('addUsers')->with('message', 'Users added successfully!');
                        }
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return redirect()->back()->withErrors('something went wrong while upload users.');
                    }
                }
            }
        }
        return Redirect::to('addUsers');
    }

    protected function changeClientSetting($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            Client::changeClientSetting($request);
            DB::connection('mysql2')->commit();
            return 'true';
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return 'false';
        }
        return 'false';
    }
}