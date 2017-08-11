<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\Designation;
use App\Models\Area;

class ZeroToHero extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'designation_id', 'area_id', 'url', 'release_date'];

    /**
     *  add/update Area
     */
    protected static function addOrUpdateZeroToHero( Request $request, $isUpdate=false){
        $heroName = InputSanitise::inputString($request->get('hero'));
        $heroId   = InputSanitise::inputInt($request->get('hero_id'));
        $designationId   = InputSanitise::inputInt($request->get('designation'));
        $areaId   = InputSanitise::inputInt($request->get('area'));
        $url   = $request->get('url');
        $releaseDate   = $request->get('release_date');
        if( $isUpdate && isset($heroId)){
            $hero = static::find($heroId);
            if(!is_object($hero)){
            	return Redirect::to('admin/manageZeroToHero');
            }
        } else{
            $hero = new static;
        }
        $hero->name = $heroName;
        $hero->designation_id = $designationId;
        $hero->area_id = $areaId;
        $hero->url = $url;
        $hero->release_date = $releaseDate;
        $hero->save();
        return $hero;
    }

    public function designation(){
    	return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function area(){
    	return $this->belongsTo(Area::class, 'area_id');
    }

    protected static function getHeroByDesignationByArea($request){
    	$designationId   = InputSanitise::inputInt($request->get('designation_id'));
        $areaId   = InputSanitise::inputInt($request->get('area_id'));
    	return static::where('designation_id', $designationId)->where('area_id', $areaId)->get();
    }

    protected static function getHerosBySearchArray($request){
        $searchFilter = json_decode($request->get('arr'),true);
        $latest = $searchFilter['latest'];
        $designationId = InputSanitise::inputInt($searchFilter['designationId']);
        $areaId = InputSanitise::inputInt($searchFilter['areaId']);

        $results = DB::table('zero_to_heroes');

        if( 1 == $latest ){
            $currentDate = date('Y-m-d');
            $previousDate = date('Y-m-d', strtotime("-30 days"));
            $results->whereBetween('release_date',[$previousDate, $currentDate]);
        }

        if($designationId > 0){
            $results->where('zero_to_heroes.designation_id', $designationId);
        }
        if($areaId > 0){
            $results->where('zero_to_heroes.area_id', $areaId);
        }
        return $results->select('zero_to_heroes.*')->get();
    }
}
