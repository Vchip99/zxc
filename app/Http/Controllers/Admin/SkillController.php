<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Skill;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class SkillController extends Controller
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
    protected $validateCreateSkill = [
        'name' => 'required|string',
    ];

    /**
     * show all skill
     */
    protected function show(){
    	$skills = Skill::paginate();
    	return view('skill.list', compact('skills'));
    }

    /**
     * show UI for create skill
     */
    protected function create(){
    	$skill = new Skill;
    	return view('skill.create', compact('skill'));
    }

    /**
     *  store skill
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSkill);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $skill = Skill::addOrUpdateSkill($request);
            if(is_object($skill)){
                DB::commit();
                return Redirect::to('admin/manageSkill')->with('message', 'Skill created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageSkill');
    }

    /**
     * edit skill
     */
    protected function edit($id){
    	$skillId = InputSanitise::inputInt(json_decode($id));
    	if(isset($skillId)){
    		$skill = Skill::find($skillId);
    		if(is_object($skill)){
    			return view('skill.create', compact('skill'));
    		}
    	}
		return Redirect::to('admin/manageSkill');
    }

    /**
     * update skill
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSkill);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $skillId = InputSanitise::inputInt($request->get('skill_id'));
        if(isset($skillId)){
            DB::beginTransaction();
            try
            {
                $skill = Skill::addOrUpdateSkill($request, true);
                if(is_object($skill)){
                    DB::commit();
                    return Redirect::to('admin/manageSkill')->with('message', 'Skill updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageSkill');
    }

    /**
     * delete skill
     */
    protected function delete(Request $request){
    	$skillId = InputSanitise::inputInt($request->get('skill_id'));
    	if(isset($skillId)){
    		$skill = Skill::find($skillId);
    		if(is_object($skill)){
                DB::beginTransaction();
                try
                {
        			$skill->delete();
                    DB::commit();
                    return Redirect::to('admin/manageSkill')->with('message', 'Skill deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageSkill');
    }

    protected function isSkillExist(Request $request){
        return Skill::isSkillExist($request);
    }
}
