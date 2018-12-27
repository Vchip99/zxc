<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class MentorArea extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update area
     */
    protected static function addOrUpdateMentorArea( Request $request, $isUpdate=false){
        $areaName = InputSanitise::inputString($request->get('area'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        if( $isUpdate && isset($areaId)){
            $area = static::find($areaId);
            if(!is_object($area)){
            	return 'false';
            }
        } else{
            $area = new static;
        }
        $area->name = $areaName;
        $area->save();
        return $area;
    }

    protected static function isMentorAreaExist(Request $request){
        $area = InputSanitise::inputString($request->get('area'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        $result = static::where('name', '=',$area);
        if(!empty($areaId)){
            $result->where('id', '!=', $areaId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getMentorAreasWithPagination(){
    	return static::paginate();
    }
}
