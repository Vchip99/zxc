<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeTimeTable;
use App\Models\CollegeDept;
use App\Models\CollegeNotice;
use App\Models\CollegeClassExam;
use App\Models\CollegeSubject;
use App\Models\CollegeHoliday;
use App\Models\CollegeExtraClass;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeCalenderController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
        });
    }

    /**
     *  show list of College Calendar
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$collegeCalendar = CollegeTimeTable::getCollegeCalendar();
    	return view('collegeModule.collegeCalendar.list', compact('collegeCalendar'));
    }

    /**
     *  show create College Calendar
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $existingCollegeCalendar = CollegeTimeTable::getCollegeCalendar();
        if(is_object($existingCollegeCalendar)){
            return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender');
        }
        $assignedDepts = [];
        $loginUser = Auth::guard('web')->user();
		$collegeCalendar = new CollegeTimeTable;
		return view('collegeModule.collegeCalendar.create', compact('collegeCalendar'));
    }

    /**
     *  store College Calendar
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        DB::beginTransaction();
        try
        {
            $collegeCalendar = CollegeTimeTable::addOrUpdateCollegeTimeTable($request);
            if(is_object($collegeCalendar)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender')->with('message', 'College Calendar created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender');
    }

    /**
     *  edit College Calendar
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeCalendar = CollegeTimeTable::find($id);
    		if(is_object($collegeCalendar)){
                return view('collegeModule.collegeCalendar.create', compact('collegeCalendar'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender');
    }

    /**
     *  update College Calendar
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$collegeCalendarId = InputSanitise::inputInt($request->get('time_table_id'));
    	if(isset($collegeCalendarId)){
            DB::beginTransaction();
            try
            {
                $collegeCalendar = CollegeTimeTable::addOrUpdateCollegeTimeTable($request, true);
                if(is_object($collegeCalendar)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender')->with('message', 'College Calendar updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender');
    }

    /**
     *  delete College Calendar
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeCalendarId = InputSanitise::inputInt($request->get('time_table_id'));
    	if(isset($collegeCalendarId)){
    		$collegeCalendar = CollegeTimeTable::find($collegeCalendarId);
    		if(is_object($collegeCalendar)){
                DB::beginTransaction();
                try
                {
                    unlink($collegeCalendar->image_path);
        			$collegeCalendar->delete();
                    DB::commit();
        			return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender')->with('message', 'College Calendar deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCalender');
    }

    protected function myCalendar($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $calendarData = [];
        $allCollegeDepts = [];
        $allSubjects = [];
        $dayColours = '';
        $loginUser = Auth::guard('web')->user();

        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
        $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id]['name'] = $subject->name;
                $allSubjects[$subject->id]['college_dept_ids'] = $subject->college_dept_ids;
                $allSubjects[$subject->id]['years'] = $subject->years;
            }
        }

        if(User::Student == $loginUser->user_type){
            $emergencyNotices = CollegeNotice::getCollegeEmergencyNoticesByCollegeIdByDeptId($loginUser->college_id,$loginUser->college_dept_id);
        } else {
            $emergencyNotices = CollegeNotice::getCollegeEmergencyNoticesByCollegeIdByUser($loginUser->college_id);
        }
        if(is_object($emergencyNotices) && false == $emergencyNotices->isEmpty()){
            foreach($emergencyNotices as $notice){
                if(!isset($results[$notice->date])){
                    $results[$notice->date] = [
                        'start' => $notice->date,
                        'color' => 'yellow',
                    ];
                }
                $collegeDeptName = '';
                $noticeDepts = explode(',',$notice->college_dept_ids);
                foreach($noticeDepts as $index => $noticeDept){
                    if(0 == $index){
                        $collegeDeptName .= $allCollegeDepts[$noticeDept];
                    } else {
                        $collegeDeptName .= ','.$allCollegeDepts[$noticeDept];
                    }
                }
                $yearStr = '';
                $noticeYears = explode(',',$notice->years);
                foreach($noticeYears as $index => $noticeYear){
                    if(0 == $index){
                        if(1 == $noticeYear){
                            $yearStr ='First ';
                        } elseif(2 == $noticeYear) {
                            $yearStr ='Second ';
                        } elseif(3 == $noticeYear) {
                            $yearStr ='Third ';
                        }else {
                            $yearStr ='Fourth ';
                        }
                    } else {
                        if(1 == $noticeYear){
                            $yearStr .= ','.'First ';
                        } elseif(2 == $noticeYear) {
                            $yearStr .= ','.'Second ';
                        } elseif(3 == $noticeYear) {
                            $yearStr .= ','.'Third ';
                        }else {
                            $yearStr .= ','.'Fourth ';
                        }
                    }
                }
                $calendarData[$notice->date]['emergency_notices'][] = [
                    'title' => $notice->notice,
                    'dept' => $collegeDeptName,
                    'year' => $yearStr,
                ];
            }
        }

        if(User::Student == $loginUser->user_type){
            $exams = CollegeClassExam::getCollegeClassExamsByCollegeIdByDeptId($loginUser->college_id,$loginUser->college_dept_id);
        } else {
            $exams = CollegeClassExam::getCollegeClassExamsByCollegeIdByUser($loginUser->college_id);
        }
        if(is_object($exams) && false == $exams->isEmpty()){
            foreach($exams as $exam){
                if(!isset($results[$exam->date])){
                    $results[$exam->date] = [
                        'start' => $exam->date,
                        'color' => 'red',
                    ];
                }
                $collegeDeptName = '';
                $examDepts = explode(',',$exam->college_dept_ids);
                foreach($examDepts as $index => $examDept){
                    if(0 == $index){
                        $collegeDeptName .= $allCollegeDepts[$examDept];
                    } else {
                        $collegeDeptName .= ','.$allCollegeDepts[$examDept];
                    }
                }
                $yearStr = '';
                $examYears = explode(',',$exam->years);
                foreach($examYears as $index => $examYear){
                    if(0 == $index){
                        if(1 == $examYear){
                            $yearStr ='First';
                        } elseif(2 == $examYear) {
                            $yearStr ='Second';
                        } elseif(3 == $examYear) {
                            $yearStr ='Third';
                        }else {
                            $yearStr ='Fourth';
                        }
                    } else {
                        if(1 == $examYear){
                            $yearStr .= ','.'First';
                        } elseif(2 == $examYear) {
                            $yearStr .= ','.'Second';
                        } elseif(3 == $examYear) {
                            $yearStr .= ','.'Third';
                        }else {
                            $yearStr .= ','.'Fourth';
                        }
                    }
                }
                if(1 == $exam->exam_type){
                    $examType = 'Online';
                } else {
                    $examType = 'Offline';
                }
                $calendarData[$exam->date]['exams'][] = [
                    'subject' => $allSubjects[$exam->college_subject_id],
                    'topic' => $exam->topic,
                    'from' => $exam->from_time,
                    'to' => $exam->to_time,
                    'dept' => $collegeDeptName,
                    'year' => $yearStr,
                    'marks' => $exam->marks,
                    'type' => $examType,
                ];
            }
        }

        $holidays = CollegeHoliday::getCollegeHolidaysByCollegeId($loginUser->college_id);
        if(is_object($holidays) && false == $holidays->isEmpty()){
            foreach($holidays as $holiday){
                if(!isset($results[$holiday->date])){
                    $results[$holiday->date] = [
                        'start' => $holiday->date,
                        'color' => 'green',
                    ];
                }
                $calendarData[$holiday->date]['holiday'][] = [
                    'title' => ($holiday->note)?:'Holiday'
                ];
            }
        }

        if(User::Student == $loginUser->user_type){
            $notices = CollegeNotice::getCollegeNoticesByCollegeIdByDeptId($loginUser->college_id,$loginUser->college_dept_id);
        } else {
            $notices = CollegeNotice::getCollegeNoticesByCollegeIdByUser($loginUser->college_id);
        }
        if(is_object($notices) && false == $notices->isEmpty()){
            foreach($notices as $notice){
                if(!isset($results[$notice->date])){
                    $results[$notice->date] = [
                        'start' => $notice->date,
                        'color' => 'blue',
                    ];
                }
                $collegeDeptName = '';
                $noticeDepts = explode(',',$notice->college_dept_ids);
                foreach($noticeDepts as $index => $noticeDept){
                    if(0 == $index){
                        $collegeDeptName .= $allCollegeDepts[$noticeDept];
                    } else {
                        $collegeDeptName .= ','.$allCollegeDepts[$noticeDept];
                    }
                }
                $yearStr = '';
                $noticeYears = explode(',',$notice->years);
                foreach($noticeYears as $index => $noticeYear){
                    if(0 == $index){
                        if(1 == $noticeYear){
                            $yearStr ='First ';
                        } elseif(2 == $noticeYear) {
                            $yearStr ='Second ';
                        } elseif(3 == $noticeYear) {
                            $yearStr ='Third ';
                        }else {
                            $yearStr ='Fourth ';
                        }
                    } else {
                        if(1 == $noticeYear){
                            $yearStr .= ','.'First ';
                        } elseif(2 == $noticeYear) {
                            $yearStr .= ','.'Second ';
                        } elseif(3 == $noticeYear) {
                            $yearStr .= ','.'Third ';
                        }else {
                            $yearStr .= ','.'Fourth ';
                        }
                    }
                }
                $calendarData[$notice->date]['notices'][] = [
                    'title' => $notice->notice,
                    'dept' => $collegeDeptName,
                    'year' => $yearStr,
                ];
            }
        }

        if(User::Student == $loginUser->user_type){
            $extraClasses = CollegeExtraClass::getCollegeExtraClassesByCollegeIdByDeptId($loginUser->college_id,$loginUser->college_dept_id);
        } else {
            $extraClasses = CollegeExtraClass::getCollegeExtraClassesByCollegeIdByUser($loginUser->college_id);
        }
        if(is_object($extraClasses) && false == $extraClasses->isEmpty()){
            foreach($extraClasses as $class){
                if(!isset($results[$class->date])){
                    $results[$class->date] = [
                        'start' => $class->date,
                        'color' => '#e6004e',
                    ];
                }
                $collegeDeptName = '';
                $classDepts = explode(',',$class->college_dept_ids);
                foreach($classDepts as $index => $classDept){
                    if(0 == $index){
                        $collegeDeptName .= $allCollegeDepts[$classDept];
                    } else {
                        $collegeDeptName .= ','.$allCollegeDepts[$classDept];
                    }
                }
                $yearStr = '';
                $classYears = explode(',',$class->years);
                foreach($classYears as $index => $classYear){
                    if(0 == $index){
                        if(1 == $classYear){
                            $yearStr ='First';
                        } elseif(2 == $classYear) {
                            $yearStr ='Second';
                        } elseif(3 == $classYear) {
                            $yearStr ='Third';
                        }else {
                            $yearStr ='Fourth';
                        }
                    } else {
                        if(1 == $classYear){
                            $yearStr .= ','.'First';
                        } elseif(2 == $classYear) {
                            $yearStr .= ','.'Second';
                        } elseif(3 == $classYear) {
                            $yearStr .= ','.'Third';
                        }else {
                            $yearStr .= ','.'Fourth';
                        }
                    }
                }
                $calendarData[$class->date]['classes'][] = [
                    'subject' => $allSubjects[$class->college_subject_id],
                    'topic' => $class->topic,
                    'from' => $class->from_time,
                    'to' => $class->to_time,
                    'dept' => $collegeDeptName,
                    'year' => $yearStr,
                ];
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
        return view('dashboard.myCalendar', compact('loginUser','dayColours','calendarData'));
    }
}