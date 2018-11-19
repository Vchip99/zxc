<?php

namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Libraries\InputSanitise;
use App\Models\CollegeSubject;
use App\Models\CollegeDept;
use App\Models\User;
use App\Models\CollegeUserAttendance;
use App\Models\CollegeOfflinePaper;
use App\Models\CollegeOfflinePaperMarks;
use App\Models\AssignmentTopic;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentAnswer;
use App\Models\CollegeExtraClass;
use App\Models\CollegeClassExam;
use App\Models\College;

class CollegeSubjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateSubject = [
        'subject' => 'required|string',
        'depts'  => 'required',
        'years'  => 'required'
    ];

    /**
     * show all subjects
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type ){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
    	    $subjects = CollegeSubject::getCollegeSubjectByCollegeIdWithPagination($loginUser->college_id);
        }

        $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($departments) && false == $departments->isEmpty()){
            foreach($departments as $department){
                $allDepts[$department->id] = $department->name;
            }
        }
    	return view('collegeModule.collegeSubject.list', compact('subjects','allDepts'));
    }

    /**
     * show UI for create subject
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $selectedDepts = [];
        $selectedYears = [];
        $loginUser = Auth::user();
    	$subject = new CollegeSubject;
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $deptIds = explode(',',$loginUser->assigned_college_depts);
            $departments = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
        } else {
            $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        }
    	return view('collegeModule.collegeSubject.create', compact('subject','departments','selectedDepts','selectedYears'));
    }

    /**
     *  store subject
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subject = CollegeSubject::addOrUpdateCollegeSubject($request);
            if(is_object($subject)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject')->with('message', 'College Subject created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject');
    }

    /**
     * edit subject
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$subjectId = InputSanitise::inputInt(json_decode($id));
    	if(isset($subjectId)){
    		$subject = CollegeSubject::find($subjectId);
    		if(is_object($subject)){
                $loginUser = Auth::user();
                if($loginUser->id == $subject->lecturer_id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
                    $selectedDepts = explode(',',$subject->college_dept_ids);
                    $selectedYears = explode(',',$subject->years);
                    return view('collegeModule.collegeSubject.create', compact('subject','departments','selectedDepts','selectedYears'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject');
    }

    /**
     * update subject
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        if(isset($subjectId)){
            DB::beginTransaction();
            try
            {
                $subject = CollegeSubject::addOrUpdateCollegeSubject($request, true);
                if(is_object($subject)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject')->with('message', 'College Subject updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject');
    }

    protected function getCollegeSubjectsByDepartmentIdByYear(Request $request){
        $department = InputSanitise::inputInt($request->get('department'));
        $year = InputSanitise::inputInt($request->get('year'));
        return CollegeSubject::getCollegeSubjectsByDepartmentIdByYear($department,$year);
    }

    protected function getAssignmentSubjectsOfGivenAssignmentByLecturer(Request $request){
        return CollegeSubject::getAssignmentSubjectsOfGivenAssignmentByLecturer($request);
    }

    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $subject = CollegeSubject::find($subjectId);
        if(is_object($subject)){
            DB::beginTransaction();
            try
            {
                $loginUser = Auth::user();
                if($loginUser->id == $subject->lecturer_id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    // delete attendance
                    CollegeUserAttendance::deleteAttendanceBySubjectId($subjectId);
                    // delete offline paper
                    CollegeOfflinePaper::deleteCollegeOfflinePapersBySubjectId($subjectId);
                    // delete offline paper marks
                    CollegeOfflinePaperMarks::deleteCollegeOfflinePaperMarksBySubjectId($subjectId);
                    // delete assignment topic
                    AssignmentTopic::deleteAssignmentTopicsBySubjectId($subjectId);
                    // delete assignment
                    $assignments = AssignmentQuestion::getAssignmentsBySubjectId($subjectId);
                    if(is_object($assignments) && false == $assignments->isEmpty()){
                        foreach($assignments as $assignment){
                            $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->get();
                            if(is_object($answers) && false == $answers->isEmpty()){
                                foreach($answers as $answer){
                                    $dir = dirname($answer->attached_link);
                                    InputSanitise::delFolder($dir);
                                    $answer->delete();
                                }
                            }
                            $dir = dirname($assignment->attached_link);
                            InputSanitise::delFolder($dir);
                            $assignment->delete();
                        }
                    }
                    // delete extra classes
                    CollegeExtraClass::deleteCollegeExtraClassesBySubjectId($subjectId);
                    // delete class exams
                    CollegeClassExam::deleteCollegeClassExamsBySubjectId($subjectId);
                    $subject->delete();
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject')->with('message', 'College Subject deleted successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeSubject');
    }

    protected function isCollegeSubjectExist(Request $request){
        return CollegeSubject::isCollegeSubjectExist($request);
    }

    protected function showAttendanceCalendar($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $result = [];
        $subjects = [];
        $selectedDepartment = '';
        $selectedCollegeYear = '';
        $selectedSubject = '';
        if($request->has('year')){
            $year = $request->get('year');
        } elseif(Session::has('selected_attendance_year')){
            $year = Session::get('selected_attendance_year');
        } else {
            $year = date('Y');
        }
        if($request->has('department')){
            $selectedDepartment = $request->get('department');
        } elseif(Session::has('selected_attendance_dept')){
            $selectedDepartment = Session::get('selected_attendance_dept');
        }
        if($request->has('college_year')){
            $selectedCollegeYear = $request->get('college_year');
        } elseif(Session::has('selected_attendance_college_year')){
            $selectedCollegeYear = Session::get('selected_attendance_college_year');
        }
        if($request->has('subject')){
            $selectedSubject = $request->get('subject');
        } elseif(Session::has('selected_attendance_subject')){
            $selectedSubject = Session::get('selected_attendance_subject');
        }
        $selectedYear = $year;
        $loginUser = Auth::user();
        if($year && $selectedDepartment > 0 && $selectedCollegeYear > 0 && $selectedSubject > 0){
            $result = $this->getCollegeStudentAttendanceByYearByDepartmentIdByCollegeYearBySubject($year,$selectedDepartment,$selectedCollegeYear,$selectedSubject);
            if($selectedCollegeYear && $selectedDepartment){
                $subjects = CollegeSubject::getCollegeSubjectsByDepartmentIdByYear($selectedDepartment,$selectedCollegeYear);
            } else {
                $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDepts($loginUser->college_id);
            }
        }
        if(isset($result['attendanceStats'])){
            $attendanceStats = implode(',', $result['attendanceStats']);
        } else {
            $attendanceStats = '';
        }
        if(!empty($selectedYear)){
            $defaultDate = $selectedYear.'-'.date('m').'-'.date('d');
        } else {
            $defaultDate = date('Y-m-d');
        }

        if(isset($result['allAttendanceDates'])){
            $allAttendanceDates = implode(',', $result['allAttendanceDates']);
        } else {
            $allAttendanceDates = '';
        }

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                $deptIds = explode(',',$loginUser->assigned_college_depts);
            $departments = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
        } else {
            $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        }

        return view('collegeModule.attendance.calendar', compact('departments','selectedYear','defaultDate','attendanceStats','allAttendanceDates','selectedDepartment','selectedCollegeYear','selectedSubject','subjects'));
    }

    protected function showAttendance($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $attendanceDate = $request->get('attendance_date');
        $selectedDepartment = $request->get('department_id');
        $selectedCollegeYear = $request->get('college_year');
        $selectedSubject = $request->get('subject_id');
        Session::put('selected_attendance_dept',$selectedDepartment);
        Session::put('selected_attendance_college_year',$selectedCollegeYear);
        Session::put('selected_attendance_subject',$selectedSubject);
        Session::put('selected_attendance_year',date('Y',strtotime($attendanceDate)));
        $allStudents = '';
        $presentStudents = [];
        $loginUser = Auth::user();
        $departments = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        $subjects = CollegeSubject::getCollegeSubjectsByDepartmentIdByYear($selectedDepartment,$selectedCollegeYear);
        $students = User::getAllUsersByCollegeIdByDeptIdByYearByUserType(Auth::user()->college_id,$selectedDepartment,$selectedCollegeYear,User::Student);
        if($students->count() > 0){
            $allStudents = implode(',', array_column($students->toArray(), 'id'));
        }
        $collegeAttendance = CollegeUserAttendance::getCollegeStudentAttendanceByDepartmentIdByYearBySubject($request);
        if(is_object($collegeAttendance)){
            $presentStudents = explode(',', $collegeAttendance->student_ids);
        }
        return view('collegeModule.attendance.attendance', compact('attendanceDate','selectedDepartment','selectedCollegeYear','selectedSubject','students','departments','subjects','allStudents','presentStudents'));
    }

    protected function markCollegeAttendance($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        DB::beginTransaction();
        try
        {
            $attendance = CollegeUserAttendance::addOrUpdateCollegeUserAttendance($request);
            if(is_object($attendance)){
                $markAttendance = InputSanitise::inputInt($request->get('mark_attendance'));
                if($request->get('students')){
                    $students = $request->get('students');
                } else {
                    $students = [];
                }
                if($request->get('all_users')){
                    $allUsers = explode(',', $request->get('all_users'));
                } else {
                    $allUsers = [];
                }
                $absentStudentIds = [];
                if(1 == $markAttendance){
                    if(count(array_diff($allUsers, $students)) > 0){
                        $absentStudentIds = array_diff($allUsers, $students);
                    }
                } else {
                    $absentStudentIds = $students;
                }
                if(count($absentStudentIds) > 0){
                    $subjectId   = InputSanitise::inputInt($request->get('subject'));
                    $subject = CollegeSubject::find($subjectId);
                    $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
                    if(is_object($college) && 1 == $college->absent_sms && is_object($subject)){
                        InputSanitise::sendCollegeAbsentSms($absentStudentIds,$attendance->attendance_date,$subject->name,$college);
                    }
                }
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeAttendance')->with('message', 'Attendance mark successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong while mark college attendance.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeAttendance');
    }

    protected function getCollegeStudentAttendanceByDepartmentIdByYearBySubject(Request $request){
        $result = [];
        $departmentId   = InputSanitise::inputInt($request->get('department_id'));
        $year   = InputSanitise::inputInt($request->get('college_year'));
        $students = User::getAllUsersByCollegeIdByDeptIdByYearByUserType(Auth::user()->college_id,$departmentId,$year,User::Student);
        $result['collegeUsers'] = $students;

        $collegeAttendance = CollegeUserAttendance::getCollegeStudentAttendanceByDepartmentIdByYearBySubject($request);
        $result['collegeAttendance'] = [];
        if(is_object($collegeAttendance)){
            $result['collegeAttendance'] = explode(',', $collegeAttendance->student_ids);
        }
        return $result;
    }

    protected function getCollegeStudentAttendanceByYearByDepartmentIdByCollegeYearBySubject($year,$selectedDepartment,$selectedCollegeYear,$selectedSubject){
        $result = [];
        $attendanceStats = [];
        $allAttendanceDates = [];
        $students = User::getAllUsersByCollegeIdByDeptIdByYearByUserType(Auth::user()->college_id,$selectedDepartment,$selectedCollegeYear,User::Student);
        if(is_object($students)){
            $allStudentCount = $students->count();
        } else {
            $allStudentCount = 0;
        }
        $collegeAttendance = CollegeUserAttendance::getCollegeStudentAttendanceByYearByDepartmentIdByCollegeYearBySubject($year,$selectedDepartment,$selectedCollegeYear,$selectedSubject);

        if(is_object($collegeAttendance) && false == $collegeAttendance->isEmpty()){
            foreach($collegeAttendance as $attendance){
                if(!empty($attendance->student_ids)){
                    $studentCount = count(explode(',',$attendance->student_ids));
                } else {
                    $studentCount = 0;
                }
                $presentCnt = $studentCount;
                if((int) $allStudentCount > (int) $studentCount){
                    $absentCnt = (int) $allStudentCount - (int) $studentCount;
                } else {
                    $absentCnt = (int) $studentCount - (int) $allStudentCount;
                }
                $attendanceStats[] = $attendance->attendance_date.':'.$presentCnt.'-'.$absentCnt;
                $allAttendanceDates[] = $attendance->attendance_date;
            }
        }
        $result['allAttendanceDates'] = $allAttendanceDates;
        $result['attendanceStats'] = $attendanceStats;
        return $result;
    }

    protected function getCollegeDepartmentsBySubjectId(Request $request){
        $result = [];
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $collegeSubject = CollegeSubject::getCollegeDepartmentsBySubjectId($subjectId);
        if(is_object($collegeSubject)){
            $deptIds =  explode(',',  $collegeSubject->college_dept_ids);
            $result['years'] =  explode(',',  $collegeSubject->years);
            if(count($deptIds) > 0){
                $result['collegeDepts'] = CollegeDept::find($deptIds);
            }
        }
        return $result;
    }

    protected function getCollegeSubjectByYear(Request $request){
        return CollegeSubject::getCollegeSubjectByYear($request->year,$request->lecturer,$request->department);
    }

    protected function getCollegeSubjectsByDeptIdByYear(Request $request){
        $loginUser = Auth::user();
        $allCollegeDepts = [];
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
        $result['depts'] = $allCollegeDepts;
        $result['subjects'] = CollegeSubject::getCollegeSubjectsByDeptIdByYear($request);
        return $result;
    }


}
