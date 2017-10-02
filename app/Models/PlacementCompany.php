<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;

class PlacementCompany extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'placement_area_id'];

    /**
     *  create/update PlacementCompany
     */
    protected static function addOrUpdatePlacementCompany( Request $request, $isUpdate=false){

    	$areaId = InputSanitise::inputInt($request->get('area'));
    	$companyId = InputSanitise::inputInt($request->get('company_id'));
    	$company = InputSanitise::inputString($request->get('company'));

        if( $isUpdate && isset($companyId)){
            $placementCompany = static::find($companyId);
            if(!is_object($placementCompany)){
            	return Redirect::to('admin/managePlacementCompany');
            }
        } else{
            $placementCompany = new static;
        }
        $placementCompany->name = $company;
		$placementCompany->placement_area_id = $areaId;
		$placementCompany->save();

        return $placementCompany;
    }

    public function area(){
    	return $this->belongsTo(PlacementArea::class, 'placement_area_id');
    }

    protected static function getPlacementCompaniesByArea($areaId){
    	return static::where('placement_area_id',$areaId )->get();
    }

    protected static function getPlacementCompaniesByAreaForFront($areaId){

        return static::join('company_details', 'company_details.placement_company_id', '=', 'placement_companies.id')
            ->join('placement_processes', 'placement_processes.placement_company_id', '=', 'placement_companies.id')
            ->where('placement_companies.placement_area_id',$areaId )
            ->select('placement_companies.id', 'placement_companies.*')->groupBy('placement_companies.id')->get();
    }
}
