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
use App\Models\ClientInstituteCourse;
use App\Models\ClientUserInstituteCourse;
use App\Models\Client;
use App\Models\ClientScore;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use Auth, Redirect, View, DB, Session;

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

    protected function allUsers(){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        return view('client.allUsers.allUsers', compact('instituteCourses'));
    }

    protected function searchUsers(Request $request){
        return Clientuser::searchUsers($request);
    }

    protected function changeClientPermissionStatus(Request $request){

        return ClientUserInstituteCourse::changeClientPermissionStatus($request);
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

    protected function userTestResults($subdomain,$id=NULL, $course=NULL){
        $results = [];
        $students = [];
        $collegeDepts = [];
        $courseId = 0;
        $selectedStudent = '';
        $id = json_decode($id);
        $courseId = json_decode($course);
        if(empty($id)){
            $id = Session::get('client_selected_user');
            $courseId = Session::get('client_selected_course');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $instituteCourses = ClientInstituteCourse::where('client_id', $selectedStudent->client_id)->get();
            if($courseId > 0){
                $students = Clientuser::getAllStudentsByClientIdByCourseId($selectedStudent->client_id,$courseId);
                $results = ClientScore::where('client_user_id', $id)->where('client_institute_course_id', $courseId)->get();
                Session::set('client_selected_course', $courseId);
            }
            Session::set('client_selected_user', $id);
        } else {
            $clientId = Auth::guard('client')->user()->id;
            $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        }
        $barchartLimits = range(100, 0, 10);
        return view('client.allUsers.userTestResults', compact('instituteCourses', 'courseId', 'students', 'results', 'selectedStudent','barchartLimits'));
    }

    protected function showUserTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $scores = ClientScore::getClientScoreByUserIdByScoreId($studentId,$courseId);
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
            Session::set('client_selected_course', $courseId);
        }
        return $result;
    }

    protected function userCourses($subdomain,$id=NULL, $courseId=NULL){
        $students = [];
        $courses = [];
        $courseId = 0;
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
            $courseId = Session::get('client_selected_course');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $instituteCourses = ClientInstituteCourse::where('client_id', $selectedStudent->client_id)->get();
            if($courseId > 0){
                $students = Clientuser::getAllStudentsByClientIdByCourseId($selectedStudent->client_id,$courseId);
                $courses = ClientOnlineCourse::getRegisteredOnlineCoursesByCourseidByUserId($courseId,$id);
                Session::set('client_selected_course', $courseId);
            }
            Session::set('client_selected_user', $id);
        } else {
            $clientId = Auth::guard('client')->user()->id;
            $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        }
        return view('client.allUsers.userCourses', compact('instituteCourses', 'students', 'courseId','courses', 'selectedStudent'));
    }

    protected function showUserCourses(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        Session::set('client_selected_user', $studentId);
        Session::set('client_selected_course', $courseId);
        return ClientOnlineCourse::getRegisteredOnlineCoursesByCourseidByUserId($courseId,$studentId);
    }

    protected function userPlacement($subdomain,$id=NULL, $courseId=NULL){
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
            $courseId = Session::get('client_selected_course');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $instituteCourses = ClientInstituteCourse::where('client_id', $selectedStudent->client_id)->get();
            if($courseId > 0){
                $students = Clientuser::getAllStudentsByClientIdByCourseId($selectedStudent->client_id,$courseId);
                Session::set('client_selected_course', $courseId);
            }
            Session::set('client_selected_user', $id);
        } else {
            $clientId = Auth::guard('client')->user()->id;
            $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        }
        return view('client.allUsers.userPlacement', compact('instituteCourses', 'students', 'courseId', 'selectedStudent'));
    }

    protected function getStudentById(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        Session::set('client_selected_user', $studentId);
        Session::set('client_selected_course', $courseId);
        return Clientuser::getStudentById($studentId);
    }

    protected function userVideo($subdomain,$id=NULL, $courseId=NULL){
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('client_selected_user');
            $courseId = Session::get('client_selected_course');
        }
        if($id > 0){
            $selectedStudent = Clientuser::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $instituteCourses = ClientInstituteCourse::where('client_id', $selectedStudent->client_id)->get();
            if($courseId > 0){
                $students = Clientuser::getAllStudentsByClientIdByCourseId($selectedStudent->client_id,$courseId);
                Session::set('client_selected_course', $courseId);
            }
            Session::set('client_selected_user', $id);
        } else {
            $clientId = Auth::guard('client')->user()->id;
            $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        }
        return view('client.allUsers.userVideo', compact('instituteCourses', 'students', 'courseId', 'selectedStudent'));
    }

    protected function updateUserVideo(Request $request){
        $studentId = InputSanitise::inputInt($request->get('student_id'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
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
            Session::set('client_selected_course', $courseId);
            return Redirect::to('userVideo')->with('message', 'User updated successfully.');
        }
        return Redirect::to('userVideo');
    }

    protected function allTestResults(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
        $scores =[];
        return view('client.allUsers.allTestResults', compact('instituteCourses', 'scores'));
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

}