<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeMessage;
use App\Models\CollegeDept;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeMessageController extends Controller
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
        'message' => 'required',
        'departments' => 'required',
    ];

    /**
     *  show list
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $messages = CollegeMessage::where('college_id', $loginUser->college_id)->orderBy('id', 'desc')->paginate();
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
    	return view('collegeModule.message.list', compact('messages','departments','collegeYears'));
    }

    /**
     *  show create  UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $departments = [];
        $loginUser = Auth::user();
		$message = new CollegeMessage;
        $years = range(1,4);
        $departments = CollegeDept::where('college_id', $loginUser->college_id)->get();
		return view('collegeModule.message.create', compact('message','departments','years'));
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
        DB::beginTransaction();
        try
        {
            $message = CollegeMessage::addOrUpdateCollegeMessage($request);
            if(is_object($message)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageMessage')->with('message', 'Message created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageMessage');
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
    		$message = CollegeMessage::find($id);
    		if(is_object($message)){
                $loginUser = Auth::user();
                $years = range(1,4);
                $departments = CollegeDept::where('college_id', $loginUser->college_id)->get();
                return view('collegeModule.message.create', compact('message','departments','years'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageMessage');
    }

    /**
     *  update
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateMessage);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$messageId = InputSanitise::inputInt($request->get('message_id'));
    	if(isset($messageId)){
            DB::beginTransaction();
            try
            {
                $message = CollegeMessage::addOrUpdateCollegeMessage($request, true);
                if(is_object($message)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageMessage')->with('message', 'Message updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageMessage');
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
    		$message = CollegeMessage::find($messageId);
    		if(is_object($message)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($message->created_by == $loginUser->id || 4 == $loginUser->user_type || 5 == $loginUser->user_type || 6 == $loginUser->user_type){
                        $dir = dirname($message->photo);
                        InputSanitise::delFolder($dir);
            			$message->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageMessage')->with('message', 'Message deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageMessage');
    }
}