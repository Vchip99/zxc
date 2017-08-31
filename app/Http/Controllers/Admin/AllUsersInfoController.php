<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator, Session, Auth, DB, Redirect;
use Illuminate\Support\Facades\Route;
use App\Models\Admin;
use App\Models\SubDomainHome;
use App\Models\College;
use App\Models\CollegeDept;
use App\Models\User;
use App\Models\Score;
use App\Models\CourseCourse;
use App\Models\TestCategory;
use App\Models\CourseCategory;
use App\Models\TestSubjectPaper;
use Excel;

class AllUsersInfoController extends Controller
{
    /**
     * check user is admin or not, if not then redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

    protected function allUsers(){
        $colleges = College::all();
    	return view('allUsers.allUsers', compact('colleges'));
    }

    public function getDepartments(Request $request){
        $collegeId = $request->get('college');
        return CollegeDept::where('college_id', $collegeId)->get();
    }

    protected function showOtherStudents(){
        return User::showOtherStudents();
    }

    protected function deleteStudent(Request $request){
        $result = [];
        DB::beginTransaction();
        try
        {
            $deleteStudent = User::deleteStudent($request);
            if('true' == $deleteStudent){
                DB::commit();
                $result['delete_student'] = 'true';
            }

        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        if('other' == $request->college_id){
            $result['students'] = User::showOtherStudents();
        } else {
            $result['students'] = User::searchUsers($request);
        }
        return $result;
    }

    protected function changeUserApproveStatus(Request $request){
        DB::beginTransaction();
        try
        {
            User::changeUserApproveStatus($request);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return User::searchUsers($request);
    }

    protected function searchUsers(Request $request){
        return User::searchUsers($request);
    }

    protected function userTestResults($id=NULL){
        $results = [];
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('admin_selected_user');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $collegeDepts = CollegeDept::where('college_id', $selectedStudent->college_id)->get();
            $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            $results = Score::where('user_id', $id)->get();
            Session::set('admin_selected_user', $id);
            Session::set('admin_selected_user_type', $selectedStudent->user_type);
        }
        $colleges = College::all();
        $categories = TestCategory::all();
        $barchartLimits = range(100, 0, 10);
        return view('allUsers.userTestResults', compact('colleges', 'categories','collegeDepts', 'students', 'results', 'selectedStudent','barchartLimits'));
    }

    protected function showUserTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $scores = Score::getScoreByCollegeIdByDeptIdByFilters($request->college,$request->department,$request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank($request->college);
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        if($request->student > 0){
            Session::set('admin_selected_user', $request->student);
            Session::set('admin_selected_user_type', $request->user_type);
        }
        return $result;
    }

    protected function userCourses($id=NULL){
        $results = [];
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('admin_selected_user');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $collegeDepts = CollegeDept::where('college_id', $selectedStudent->college_id)->get();
            $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            $courses = CourseCourse::getRegisteredOnlineCourses($id);
            Session::set('admin_selected_user', $id);
            Session::set('admin_selected_user_type', $selectedStudent->user_type);
        }
        $colleges = College::all();
        $categories = CourseCategory::all();
        return view('allUsers.userCourses', compact('colleges', 'categories','collegeDepts', 'students', 'courses', 'selectedStudent'));
    }

    protected function showUserCourses(Request $request){
        Session::set('admin_selected_user', $request->student);
        return CourseCourse::getOnlineCoursesByUserIdByCategoryBySubCategory($request->student,$request->category,$request->subcategory);
    }

    protected function userPlacement($id=NULL){
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('admin_selected_user');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $collegeDepts = CollegeDept::where('college_id', $selectedStudent->college_id)->get();
            $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            Session::set('admin_selected_user', $id);
            Session::set('admin_selected_user_type', $selectedStudent->user_type);
        }
        $colleges = College::all();
        return view('allUsers.userPlacement', compact('colleges', 'collegeDepts', 'students', 'selectedStudent'));
    }

    protected function getStudentById(Request $request){
        Session::set('admin_selected_user', $request->student);
        return User::getStudentById($request->student);
    }

    protected function userVideo($id=NULL){
        $students = [];
        $collegeDepts = [];
        $selectedStudent = '';
        if(empty($id)){
            $id = Session::get('admin_selected_user');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $collegeDepts = CollegeDept::where('college_id', $selectedStudent->college_id)->get();
            $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            Session::set('admin_selected_user', $id);
            Session::set('admin_selected_user_type', $selectedStudent->user_type);
        }
        $colleges = College::all();
        return view('allUsers.userVideo', compact('colleges', 'collegeDepts', 'students', 'selectedStudent'));
    }

    protected function updateStudentVideo(Request $request){
        $student = User::find($request->student);
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
            Session::set('admin_selected_user', $student->id);
            Session::set('admin_selected_user_type', $student->user_type);
            return Redirect::to('admin/userVideo')->with('message', 'User updated successfully.');
        }
        return Redirect::to('admin/userVideo');
    }

    protected function unapproveUsers(){
        $colleges = $colleges = College::all();
        $upApproveUsers = User::unApproveUsers('all');
        return view('allUsers.unapproveUsers', compact('colleges', 'upApproveUsers'));
    }

    protected function unapproveUsersByCollegeId(Request $request){
        $collegeNames = [];
        $result = [];
        $colleges = College::all();
        if(is_object($colleges) && false == $colleges->isEmpty()){
            foreach($colleges as $college){
                $collegeNames[$college->id] = $college->name;
            }
        }
        $result['users'] = User::unApproveUsers($request->get('college_id'));;
        $result['colleges'] = $collegeNames;
        return $result;
    }

    protected function approveUser(Request $request){
        $collegeNames = [];
        $result = [];
        User::changeUserApproveStatus($request);
        $colleges = College::all();
        if(is_object($colleges) && false == $colleges->isEmpty()){
            foreach($colleges as $college){
                $collegeNames[$college->id] = $college->name;
            }
        }
        $result['users'] = User::unApproveUsers($request->get('selected_college_id'));
        $result['colleges'] = $collegeNames;
        return $result;
    }

    protected function allTestResults(Request $request){
        $colleges = College::all();
        $categories = TestCategory::all();
        $scores =[];
        return view('allUsers.allTestResults', compact('colleges', 'categories', 'scores'));
    }

    protected function getAllTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $colleges = [];
        $departments = [];
        $scores = Score::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank($request->college);
                $marks[$score->id] = $score->totalMarks();
                if(is_object($score->user->college) && $score->user->college->id > 0){
                  $colleges[$score->id] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $colleges[$score->id] = $score->user->other_source;
                }else{
                    $colleges[$score->id] = 'Client';
                }
                if(is_object($score->user->department) && $score->user->department->id > 0){
                  $departments[$score->id] = $score->user->department->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $departments[$score->id] = 'Other';
                }else{
                    $departments[$score->id] = 'Client';
                }
            }
        }
        $results['scores'] = $scores;
        $results['ranks'] = $ranks;
        $results['marks'] = $marks;
        $results['colleges'] = $colleges;
        $results['departments'] = $departments;
        return $results;
    }

    protected function downloadExcelResult(Request $request){
        $resultArray[] = ['Sr. No.','Name','college','Department','Marks', 'Rank'];
        $scores = Score::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $index => $score){
                $result = [];
                $result['Sr. No.'] = $index +1;
                $result['Name'] = $score->user->name;

                if(is_object($score->user->college) && $score->user->college->id > 0){
                    $result['college'] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $result['college'] = $score->user->other_source;
                }else{
                    $result['college'] = 'Client';
                }

                if(is_object($score->user->college) && $score->user->college->id > 0){
                    $result['Department'] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $result['Department'] = $score->user->other_source;
                }else{
                    $result['Department'] = 'Client';
                }
                $totalMarks = $score->totalMarks()['totalMarks'];
                $result['Marks'] = (string) $score->test_score.'/'.$totalMarks;
                $result['Rank'] = (string) $score->rank($request->college);

                $resultArray[] = $result;
            }
        }
        if($request->get('college') > 0){
            $collegeName = College::find($request->get('college'))->name;
        } else {
            $collegeName = $request->get('college');
        }

        if($request->get('paper') > 0){
            $paperName = TestSubjectPaper::find($request->get('paper'))->name;
        } else {
            $paperName = $request->get('paper');
        }

        $collegeResult = $collegeName.'_'.$paperName.'_result';
        $sheetName = $paperName.' Test Result';
        return \Excel::create($collegeResult, function($excel) use ($sheetName,$resultArray) {
            $excel->sheet($sheetName , function($sheet) use ($resultArray)
            {
                $sheet->fromArray($resultArray);
            });
        })->download('xls');
    }
}

