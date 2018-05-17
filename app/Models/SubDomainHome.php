<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class SubDomainHome extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['subdomain','home_content_value','home_about_value','home_about_content','home_vission_content','home_mission_content','home_online_name','home_online_content','home_test_value','home_test_content'];

    public $timestamps = false;

    protected static function addSubDomainHome(Request $request){
    	$subdomain = $request->subdomain;
    	$homeContentValue = $request->home_content_value;
    	$homeAboutValue = $request->home_about_value;
    	$homeAboutContent = $request->home_about_content;
    	$homeVissionContent = $request->home_vission_content;
    	$homeMissionContent = $request->home_mission_content;
    	$homeOnlineName = $request->home_online_name;
    	$homeOnlineContent = $request->home_online_content;
    	$homeTestValue = $request->home_test_value;
    	$homeTestContent = $request->home_test_content;

		$subDomainHome = static::where('subdomain', $subdomain)->first();

		if(!is_object($subDomainHome)){
    		$subDomainHome = new static;
    		$subDomainHome->subdomain = $subdomain;
    	}
    	$subDomainHome->home_content_value = $homeContentValue;
    	$subDomainHome->home_about_value = $homeAboutValue;
    	$subDomainHome->home_about_content = $homeAboutContent;
    	$subDomainHome->home_vission_content = $homeVissionContent;
    	$subDomainHome->home_mission_content = $homeMissionContent;
    	$subDomainHome->home_online_name = $homeOnlineName;
    	$subDomainHome->home_online_content = $homeOnlineContent;
    	$subDomainHome->home_test_value = $homeTestValue;
    	$subDomainHome->home_test_content = $homeTestContent;
    	$subDomainHome->save();

    	return $subDomainHome;
    }
}
