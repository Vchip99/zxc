<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth, DB;
use App\Libraries\InputSanitise;

class Skill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update Skill
     */
    protected static function addOrUpdateSkill( Request $request, $isUpdate=false){
        $skillName = InputSanitise::inputString($request->get('name'));
        $skillId = InputSanitise::inputInt($request->get('skill_id'));

        if( $isUpdate && isset($skillId)){
            $skill = static::find($skillId);
            if(!is_object($skill)){
                return 'false';
            }
        } else{
            $skill = new static;
        }
        $skill->name = $skillName;
        $skill->save();
        return $skill;
    }

    protected static function isSkillExist(Request $request){
        $skillName = InputSanitise::inputString($request->get('name'));
        $skillId = InputSanitise::inputInt($request->get('skill_id'));
        $result = static::where('name', $skillName);
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
}
