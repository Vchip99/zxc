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
        $clientusers = Clientuser::where('client_id', $clientId)->get();
        $courses = ClientOnlineCourse::getCourseAssocaitedWithVideos();
        $userPurchasedCourses = ClientUserPurchasedCourse::getClientUserCourses($clientId);
        $userPurchasedTestSubCategories = ClientUserPurchasedTestSubCategory::getClientUserTestSubCategories($clientId);
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($clientId);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        }
        if(count(array_keys($purchasedPayableSubCategories)) > 0){
            $clientPurchasedSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
        }
        return view('client.allUsers.allUsers', compact('clientusers', 'courses', 'userPurchasedCourses', 'userPurchasedTestSubCategories', 'testSubCategories', 'clientPurchasedSubCategories', 'purchasedPayableSubCategories', 'subdomainName'));
    }

    protected function searchUsers($subdomainName,Request $request){
        return Clientuser::searchUsers($request);
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

    protected function userTestResults($subdomainName,$id=NULL){
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
            $students = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        }

        $barchartLimits = range(100, 0, 10);
        return view('client.allUsers.userTestResults', compact('students', 'results', 'selectedStudent','barchartLimits', 'subdomainName'));
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

    protected function userCourses($subdomainName,$id=NULL){
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
            $students = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        }
        return view('client.allUsers.userCourses', compact('students', 'courses', 'selectedStudent', 'subdomainName'));
    }

    protected function showUserCourses(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return ClientOnlineCourse::getRegisteredOnlineCoursesByUserId($studentId);
    }

    protected function userPlacement($subdomainName,$id=NULL){
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
            $students = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        }
        return view('client.allUsers.userPlacement', compact('students', 'selectedStudent', 'subdomainName'));
    }

    protected function getStudentById(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return Clientuser::getStudentById($studentId);
    }

    protected function userVideo($subdomainName,$id=NULL){
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
            $students = Clientuser::getAllStudentsByClientId(Auth::guard('client')->user()->id);
        }
        return view('client.allUsers.userVideo', compact('students', 'selectedStudent', 'subdomainName'));
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
        $scores =[];
        $categories = ClientOnlineTestCategory::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.allUsers.allTestResults', compact('scores', 'categories', 'subdomainName'));
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
        $sheetName = $paperName;
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

        $downloadResult = $subjectName.'_'.$paperName.'_result';
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

    protected function profile($subdomainName){
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
        return view('client.clientLogin.settings', compact('subdomainName'));
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

}