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

    protected function changeOtherStudentApproveStatus(Request $request){
        DB::beginTransaction();
        try
        {
            User::changeOtherStudentApproveStatus($request);
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
                $ranks[$score->id] = $score->rank();
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
        $categories = TestCategory::all();
        return view('allUsers.userCourses', compact('colleges', 'categories','collegeDepts', 'students', 'courses', 'selectedStudent'));
    }

    protected function showUserCourses(Request $request){
        Session::set('admin_selected_user', $request->student);
        return CourseCourse::getRegisteredOnlineCourses($request->student);
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
            $student->recorded_video = $request->recorded_video;
            $student->save();
            Session::set('admin_selected_user', $student->id);
            Session::set('admin_selected_user_type', $student->user_type);
        }
        return Redirect::to('admin/userVideo')->with('message', 'User updated successfully.');
    }
}
