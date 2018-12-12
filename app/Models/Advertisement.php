<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,File,Auth;
use App\Libraries\InputSanitise;

class Advertisement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'image', 'url'];

    /**
     *  create/update ad
     */
    protected static function addOrUpdateAdvertisement(Request $request, $isUpdate = false){
        $url = $request->get('url');
        $advertisementId = InputSanitise::inputInt($request->get('advertisement_id'));
        if(true == $isUpdate && $advertisementId > 0){
        	$newAd = static::find($advertisementId);
        	if(!is_object($newAd)){
        		return 'false';
        	}
        } else {
    		$newAd = new static;
        }
    	$newAd->url = $url;
    	$newAd->admin_id = Auth::guard('admin')->user()->id;
    	$newAd->save();
        if($request->exists('image')){
            $adImage = $request->file('image')->getClientOriginalName();
            $advertisementFolderPath = "adminAds"."/".$newAd->id;
            if(!is_dir($advertisementFolderPath)){
                File::makeDirectory($advertisementFolderPath, $mode = 0777, true, true);
            }
            $adImagePath = $advertisementFolderPath .'/'.$adImage;
            if(file_exists($adImagePath)){
                unlink($adImagePath);
            } elseif(!empty($newAd->id) && file_exists($newAd->image)){
                unlink($newAd->image);
            }
            $request->file('image')->move($advertisementFolderPath, $adImage);
            $newAd->image = $adImagePath;
            $newAd->save();
        }
    	return $newAd;
    }

    protected static function getAdvertisements(){
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            return static::where('admin_id', Auth::guard('admin')->user()->id)->paginate();
        } else {
        	return static::paginate();
        }
    }
}
