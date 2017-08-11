<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\Designation;
use App\Models\ZeroToHero;

class Area extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'designation_id'];

    /**
     *  add/update Area
     */
    protected static function addOrUpdateArea( Request $request, $isUpdate=false){
        $areaName = InputSanitise::inputString($request->get('area'));
        $designationId   = InputSanitise::inputInt($request->get('designation'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
        if( $isUpdate && isset($areaId)){
            $area = static::find($areaId);
            if(!is_object($area)){
            	return Redirect::to('admin/manageArea');
            }
        } else{
            $area = new static;
        }
        $area->name = $areaName;
        $area->designation_id = $designationId;
        $area->save();
        return $area;
    }

    public function designation(){
    	return $this->belongsTo(Designation::class, 'designation_id');
    }

    protected static function getAreasByDesignation($designationId){
    	return static::where('designation_id', $designationId)->get();
    }

    public function heros(){
        return $this->hasMany(ZeroToHero::class, 'area_id');
    }
}