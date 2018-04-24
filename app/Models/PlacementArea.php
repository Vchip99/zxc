<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class PlacementArea extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update placementArea
     */
    protected static function addOrUpdatePlacementArea( Request $request, $isUpdate=false){
        $area = InputSanitise::inputString($request->get('area'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        if( $isUpdate && isset($areaId)){
            $placementArea = static::find($areaId);
            if(!is_object($placementArea)){
            	return Redirect::to('admin/managePlacementArea');
            }
        } else{
            $placementArea = new static;
        }
        $placementArea->name = $area;
        $placementArea->save();
        return $placementArea;
    }

    protected static function getPlacementAreas(){
        return static::join('placement_companies', 'placement_companies.placement_area_id', '=', 'placement_areas.id')
            ->join('company_details', 'company_details.placement_area_id', '=', 'placement_areas.id')
            ->join('placement_processes', 'placement_processes.placement_area_id', '=', 'placement_areas.id')
            ->select('placement_areas.id', 'placement_areas.*')->groupBy('placement_areas.id')->get();

    }

    protected static function isPlacementAreaExist(Request $request){
        $area = InputSanitise::inputString($request->get('area'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        $result = static::where('name', $area);
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
}
