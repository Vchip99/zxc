<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\MentorArea;
use App\Models\MentorSkill;
use App\Models\Mentor;
use App\Models\MentorChatMessage;
use App\Models\MentorSchedule;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class MentorAreaController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateArea = [
        'area' => 'required|string',
    ];

    /**
     *  show list of area
     */
    protected function show(){
    	$areas = MentorArea::getMentorAreasWithPagination();
    	return view('mentorArea.list', compact('areas'));
    }

    /**
     *  show create area UI
     */
    protected function create(){
		$area = new MentorArea;
		return view('mentorArea.create', compact('area'));
    }

    /**
     *  store area
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateArea);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $area = MentorArea::addOrUpdateMentorArea($request);
            if(is_object($area)){
                DB::commit();
                return Redirect::to('admin/manageMentorArea')->with('message', 'Area created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageMentorArea');
    }

    /**
     *  edit area
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$area = MentorArea::find($id);
    		if(is_object($area)){
                return view('mentorArea.create', compact('area'));
    		}
    	}
    	return Redirect::to('admin/manageMentorArea');
    }

    /**
     *  update area
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateArea);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$areaId = InputSanitise::inputInt($request->get('area_id'));
    	if(isset($areaId)){
            DB::beginTransaction();
            try
            {
                $area = MentorArea::addOrUpdateMentorArea($request, true);
                if(is_object($area)){
                    DB::commit();
                    return Redirect::to('admin/manageMentorArea')->with('message', 'Area updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('admin/manageMentorArea');
    }

    /**
     *  delete area
     */
    protected function delete(Request $request){
    	$areaId = InputSanitise::inputInt($request->get('area_id'));
    	if(isset($areaId)){
    		$area = MentorArea::find($areaId);
    		if(is_object($area)){
                DB::beginTransaction();
                try
                {
        			$area->delete();
                    DB::commit();
        			return Redirect::to('admin/manageMentorArea')->with('message', 'Area deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageMentorArea');
    }

    protected function isMentorAreaExist(Request $request){
        return MentorArea::isMentorAreaExist($request);
    }

    /**
     *  show list of mentors
     */
    protected function manageMentors(){
        $areas = MentorArea::all();
        $skills = MentorSkill::all();
        $mentors = Mentor::paginate();
        return view('mentorArea.mentors', compact('mentors','areas','skills'));
    }

    /**
     *  delete Mentor
     */
    protected function deleteMentor(Request $request){
        $mentorId = InputSanitise::inputInt($request->get('mentor_id'));
        if(isset($mentorId)){
            $mentor = Mentor::find($mentorId);
            if(is_object($mentor)){
                DB::beginTransaction();
                try
                {
                    MentorChatMessage::deleteMentorChatMessagesByMentorId($mentor->id);
                    MentorSchedule::deleteMentorSchedulesByMentorId($mentor->id);
                    $mentorImages = "mentorImages/".$mentor->id;
                    if(is_dir($mentorImages)){
                        InputSanitise::delFolder($mentorImages);
                    }
                    $mentor->delete();
                    DB::commit();
                    return Redirect::to('admin/manageMentors')->with('message', 'Mentor deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageMentors');
    }

    protected function getMentorsByAreaId(Request $request){
        $result['skills'] = MentorSkill::getMentorSkillsByAreaId($request->get('area_id'));
        $result['mentors'] = Mentor::getMentorsByAreaId($request);
        return $result;
    }

    protected function changeMentorApproveStatus(Request $request){
        DB::beginTransaction();
        try
        {
            Mentor::changeMentorApproveStatus($request);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return;
    }


    protected function getMentorsByAreaIdBySkillId(Request $request){
        $result['mentors'] = Mentor::getMentorsByAreaIdBySkillId($request);
        return $result;
    }

}
