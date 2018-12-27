<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class MentorSkill extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mentor_area_id','name'];

    /**
     *  add/update skill
     */
    protected static function addOrUpdateMentorSkill( Request $request, $isUpdate=false){
        $skillName = InputSanitise::inputString($request->get('skill'));
        $areaId   = InputSanitise::inputInt($request->get('area'));
        $skillId   = InputSanitise::inputInt($request->get('skill_id'));
        if( $isUpdate && isset($skillId)){
            $skill = static::find($skillId);
            if(!is_object($skill)){
            	return 'false';
            }
        } else{
            $skill = new static;
        }
        $skill->mentor_area_id = $areaId;
        $skill->name = $skillName;
        $skill->save();
        return $skill;
    }

    protected static function isMentorSkillExist(Request $request){
        $skill = InputSanitise::inputString($request->get('skill'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        $skillId   = InputSanitise::inputInt($request->get('skill_id'));
        $result = static::where('name', '=',$skill)->where('mentor_area_id', '=', $areaId);
        if(!empty($skillId)){
            $result->where('id', '!=', $skillId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getMentorSkillsWithPagination(){
    	return static::paginate();
    }

    protected static function getMentorSkillsByAreaId($areaId){
        return static::where('mentor_area_id', $areaId)->select('id','name')->get();
    }
}
