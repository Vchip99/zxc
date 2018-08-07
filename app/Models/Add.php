<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class Add extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company', 'logo', 'tag_line', 'website_url', 'email', 'phone', 'show_page_id', 'start_date', 'end_date','is_payment_done'];

    /**
     *  create/update virtualPlacementDrive
     */
    protected static function addOrUpdateAd(Request $request, $isUpdate = false){
        $name = $request->get('name');
        $tagLine = $request->get('tag_line');
        $email = $request->get('email');
        $selectedPage = $request->get('selected_page');
        $websiteUrl = $request->get('website_url');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $phone = $request->get('phone');

    	$newAd = new static;
    	$newAd->company = $name;
    	$newAd->tag_line = $tagLine;
        $newAd->show_page_id = $selectedPage;
        $newAd->website_url = $websiteUrl;
        $newAd->email = $email;
    	$newAd->start_date = $startDate;
        $newAd->end_date = $endDate;
        $newAd->phone = $phone;
        if($request->exists('logo')){
            $logoImage = $request->file('logo')->getClientOriginalName();
            $advertisementFolderPath = "advertisements";
            if(!is_dir($advertisementFolderPath)){
                mkdir($advertisementFolderPath, 0777, true);
            }
            $logoImagePath = $advertisementFolderPath ."/". $logoImage;
            if(file_exists($logoImagePath)){
                unlink($logoImagePath);
            } elseif(!empty($newAd->id) && file_exists($newAd->logo)){
                unlink($newAd->logo);
            }
            $request->file('logo')->move($advertisementFolderPath, $logoImage);
            $newAd->logo = $logoImagePath;
        }

    	$newAd->save();
    	return $newAd;
    }

    protected static function getAdds($url,$date){
        return DB::table('adds')->join('advertisement_pages', 'advertisement_pages.id', '=', 'adds.show_page_id')
            ->where('adds.is_payment_done', 1)
            ->where('advertisement_pages.url', $url)
            ->whereRaw('"'.$date.'" between `start_date` and `end_date`')
            ->select('adds.*')
            ->get();
    }
}
