<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientOnlineCourse;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientScore;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientUserPurchasedCourse;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Models\ClientOnlineTestSubCategory;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Auth, Redirect, View, DB, Session;
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

    protected function allUsers(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $clientusers = Clientuser::where('client_id', $clientId)->get();
        $courses = ClientOnlineCourse::getCourseAssocaitedWithVideos();
        $userPurchasedCourses = ClientUserPurchasedCourse::getClientUserCourses($clientId);
        $userPurchasedTestSubCategories = ClientUserPurchasedTestSubCategory::getClientUserTestSubCategories($clientId);
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);
        // dd($testSubCategories);
        return view('client.allUsers.allUsers', compact('clientusers', 'courses', 'userPurchasedCourses', 'userPurchasedTestSubCategories', 'testSubCategories'));
    }

    protected function searchUsers(Request $request){
        return Clientuser::searchUsers($request);
    }

    protected function deleteStudent(Request $request){
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

    protected function userTestResults($subdomain,$id=NULL){
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
        return view('client.allUsers.userTestResults', compact('students', 'results', 'selectedStudent','barchartLimits'));
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

    protected function userCourses($subdomain,$id=NULL){
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
        return view('client.allUsers.userCourses', compact('students', 'courses', 'selectedStudent'));
    }

    protected function showUserCourses(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return ClientOnlineCourse::getRegisteredOnlineCoursesByUserId($studentId);
    }

    protected function userPlacement($subdomain,$id=NULL){
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
        return view('client.allUsers.userPlacement', compact('students', 'selectedStudent'));
    }

    protected function getStudentById(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        Session::set('client_selected_user', $studentId);
        return Clientuser::getStudentById($studentId);
    }

    protected function userVideo($subdomain,$id=NULL){
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
        return view('client.allUsers.userVideo', compact('students', 'selectedStudent'));
    }

    protected function updateUserVideo(Request $request){
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
            Session::set('client_selected_user', $studentId);
            return Redirect::to('userVideo')->with('message', 'User updated successfully.');
        }
        return Redirect::to('userVideo');
    }

    protected function allTestResults(Request $request){
        $scores =[];
        $categories = ClientOnlineTestCategory::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.allUsers.allTestResults', compact('scores', 'categories'));
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
        return ClientUserPurchasedCourse::changeClientUserCourseStatus($request);
    }

    protected function changeClientUserTestSubCategoryStatus(Request $request){
        return ClientUserPurchasedTestSubCategory::changeClientUserTestSubCategoryStatus($request);
    }

}