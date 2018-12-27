<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\MentorArea;
use App\Models\MentorSkill;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class MentorSkillController extends Controller
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
    protected $validateSkill = [
        'area' => 'required|string',
        'skill' => 'required|string',
    ];

    /**
     *  show list of skills
     */
    protected function show(){
        $areaNames = [];
    	$mentorAreas = MentorArea::all();
        if(is_object($mentorAreas) && false == $mentorAreas->isEmpty()){
            foreach($mentorAreas as $mentorArea){
                $areaNames[$mentorArea->id] = $mentorArea->name;
            }
        }
        $skills = MentorSkill::getMentorSkillsWithPagination();
    	return view('mentorSkill.list', compact('areaNames','skills'));
    }

    /**
     *  show create skill UI
     */
    protected function create(){
		$areaNames = [];
        $mentorAreas = MentorArea::all();
        if(is_object($mentorAreas) && false == $mentorAreas->isEmpty()){
            foreach($mentorAreas as $mentorArea){
                $areaNames[$mentorArea->id] = $mentorArea->name;
            }
        }
        $skill = new MentorSkill;
		return view('mentorSkill.create', compact('areaNames','skill'));
    }

    /**
     *  store skill
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateSkill);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $skill = MentorSkill::addOrUpdateMentorSkill($request);
            if(is_object($skill)){
                DB::commit();
                return Redirect::to('admin/manageMentorSkill')->with('message', 'Skill created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageMentorSkill');
    }

    /**
     *  edit skill
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$skill = MentorSkill::find($id);
    		if(is_object($skill)){
                $areaNames = [];
                $mentorAreas = MentorArea::all();
                if(is_object($mentorAreas) && false == $mentorAreas->isEmpty()){
                    foreach($mentorAreas as $mentorArea){
                        $areaNames[$mentorArea->id] = $mentorArea->name;
                    }
                }
                return view('mentorSkill.create', compact('areaNames','skill'));
    		}
    	}
    	return Redirect::to('admin/manageMentorSkill');
    }

    /**
     *  update skill
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateSkill);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$skillId = InputSanitise::inputInt($request->get('skill_id'));
    	if(isset($skillId)){
            DB::beginTransaction();
            try
            {
                $skill = MentorSkill::addOrUpdateMentorSkill($request, true);
                if(is_object($skill)){
                    DB::commit();
                    return Redirect::to('admin/manageMentorSkill')->with('message', 'Skill updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('admin/manageMentorSkill');
    }

    /**
     *  delete skill
     */
    protected function delete(Request $request){
    	$skillId = InputSanitise::inputInt($request->get('skill_id'));
    	if(isset($skillId)){
    		 $skill = MentorSkill::find($skillId);
    		if(is_object($skill)){
                DB::beginTransaction();
                try
                {
        			$skill->delete();
                    DB::commit();
        			return Redirect::to('admin/manageMentorSkill')->with('message', 'Area deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageMentorSkill');
    }

    protected function isMentorSkillExist(Request $request){
        return MentorSkill::isMentorSkillExist($request);
    }
}
