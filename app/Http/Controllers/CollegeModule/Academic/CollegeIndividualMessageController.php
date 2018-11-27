<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeIndividualMessage;
use App\Models\CollegeDept;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeIndividualMessageController extends Controller
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
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateMessage = [
        'department' => 'required',
        'year' => 'required',
    ];

    /**
     *  show list
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $date = date('Y-m-d');
        $messages = CollegeIndividualMessage::getIndividualMessagesByCollegeIdByDate($loginUser->college_id,$date);
        $collegeDepartments = CollegeDept::where('college_id', $loginUser->college_id)->get();
        if(is_object($collegeDepartments) && false == $collegeDepartments->isEmpty()){
            foreach($collegeDepartments as $collegeDepartment){
                $departments[$collegeDepartment->id] = $collegeDepartment->name;
            }
        }
        $collegeYears = [
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
            ];
    	return view('collegeModule.individualMessage.list', compact('messages','departments','collegeYears','date'));
    }

    /**
     *  show create  UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $messages = [];
        $departments = [];
        $loginUser = Auth::user();
		$individualMessage = '';
        $years = range(1,4);
        $departments = CollegeDept::where('college_id', $loginUser->college_id)->get();
        $students = [];
		return view('collegeModule.individualMessage.create', compact('individualMessage','departments','years','students','messages'));
    }

    /**
     *  store
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateMessage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $allUsersMessages = $request->except(['_token','department','year']);
        $collegeDeptId   = InputSanitise::inputInt($request->get('department'));
        $year   = InputSanitise::inputInt($request->get('year'));
        $allMessagesString = '';
        $studentsData = [];
        if(count($allUsersMessages) > 0){
            foreach($allUsersMessages as $userId => $message){
                if(!empty($message)){
                    if(empty($allMessagesString)){
                        $allMessagesString = $userId.':'.$message;
                    } else {
                        $allMessagesString .= ','.$userId.':'.$message;
                    }
                    $studentsData[$userId] = $message;
                }
            }
        }
        if(empty($allMessagesString) && 0 == count($studentsData)){
            return redirect()->back()->withErrors('please enter message atleast one user');
        }
        DB::beginTransaction();
        try
        {
            $message = CollegeIndividualMessage::addIndividualMessage($allMessagesString,$collegeDeptId,$year);
            if(is_object($message)){
                $this->sendCollegeIndividualMessages($studentsData);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageIndividualMessage')->with('message', 'Message created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageIndividualMessage');
    }

    /**
     *  edit
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$individualMessage = CollegeIndividualMessage::find($id);
    		if(is_object($individualMessage)){
                $messages = [];
                $loginUser = Auth::user();
                $years = range(1,4);
                $departments = CollegeDept::where('college_id', $loginUser->college_id)->get();
                $students = User::getAllUsersByCollegeIdByDeptIdByYearByUserType(Auth::user()->college_id,$individualMessage->college_dept_id, $individualMessage->year, User::Student);
                $allMessages = explode(',', $individualMessage->messages);
                if(count($allMessages) > 0){
                    foreach($allMessages as $userMessages){
                        $arrMsg = explode(':', $userMessages);
                        $messages[$arrMsg[0]] = $arrMsg[1];
                    }
                }
                return view('collegeModule.individualMessage.create', compact('individualMessage','departments','years','students','messages'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageIndividualMessage');
    }

    /**
     *  delete
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $messageId = InputSanitise::inputInt($request->get('message_id'));
        if(isset($messageId)){
    		$individualMessage = CollegeIndividualMessage::find($messageId);
    		if(is_object($individualMessage)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($individualMessage->created_by == $loginUser->id || 4 == $loginUser->user_type || 5 == $loginUser->user_type || 6 == $loginUser->user_type){
            			$individualMessage->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageIndividualMessage')->with('message', 'Message deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageIndividualMessage');
    }

    /**
     *
     */
    protected function getCollegeStudentsByDeptIdByYear(Request $request){
        return User::getAllUsersByCollegeIdByDeptIdByYearByUserType(Auth::user()->college_id,$request->department, $request->year, User::Student);
    }

    protected function getIndividualMessagesByDate(Request $request){
        $result = [];
        $departments = [];
        $loginUser = Auth::user();
        $date = $request->get('date');
         $collegeDepartments = CollegeDept::where('college_id', $loginUser->college_id)->get();
        if(is_object($collegeDepartments) && false == $collegeDepartments->isEmpty()){
            foreach($collegeDepartments as $collegeDepartment){
                $departments[$collegeDepartment->id] = $collegeDepartment->name;
            }
        }
        $messages = CollegeIndividualMessage::getIndividualMessagesByCollegeIdByDate($loginUser->college_id,$date);
        if(is_object($messages) && false == $messages->isEmpty()){
            foreach($messages as $messageObj){
                $result[] = [
                    'id' => $messageObj->id,
                    'department' => $departments[$messageObj->college_dept_id],
                    'year' => $messageObj->year,
                    'date' => date('Y-m-d h:i:s a', strtotime($messageObj->created_at)),
                ];
            }
        }
        return $result;
    }

    protected function sendCollegeIndividualMessages($studentsData){
        $college = Auth::user()->college;
        if( is_object($college) && 1 == $college->individual_sms){
            InputSanitise::sendCollegeIndividualSms($studentsData,$college);
        }
        return;
    }
}