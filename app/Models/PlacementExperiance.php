<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect, Auth, Cache;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\User;

class PlacementExperiance extends Model
{
    public $timestamps = false;

    /**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'title', 'question', 'user_id', 'created_at'];

    /**
     *  add/update placementArea
     */
    protected static function createPlacementExperiance(Request $request){
        $title = $request->get('title');
        $question = $request->get('question');
        $areaId   = InputSanitise::inputInt($request->get('area'));
        $companyId   = InputSanitise::inputInt($request->get('company_id'));

        $placementExperiance = new static;
        $placementExperiance->placement_area_id = $areaId;
        $placementExperiance->placement_company_id = $companyId;
        $placementExperiance->title = $title;
        $placementExperiance->question = $question;
        $placementExperiance->user_id = Auth::user()->id;
		$placementExperiance->created_at = date('Y-m-d H:i:s');
        $placementExperiance->save();
        return $placementExperiance;
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    protected static function deletePlacementExperiancesByCompanyId($companyId){
        $placementExperiances = static::where('placement_company_id', $companyId)->get();
        if(is_object($placementExperiances) && false == $placementExperiances->isEmpty()){
            foreach($placementExperiances as $placementExperiance){
                $placementExperiance->delete();
            }
        }
        return;
    }
}
