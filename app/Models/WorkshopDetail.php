<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\WorkshopCategory;
use App\Models\WorkshopVideo;

class WorkshopDetail extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'workshop_category_id','workshop_image', 'author', 'author_introduction', 'author_image', 'description', 'certified', 'start_date', 'end_date'];


    /**
     *  create/update WorkshopDetails
     */
    protected static function addOrUpdateWorkshopDetails(Request $request, $isUpdate = false){
    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $workshopName = InputSanitise::inputString($request->get('workshop'));
        $author = InputSanitise::inputString($request->get('author'));
        $authorIntroduction = InputSanitise::inputString($request->get('author_introduction'));
        $description = InputSanitise::inputString($request->get('description'));
        $certified = InputSanitise::inputString($request->get('certified'));
        $workshopId = InputSanitise::inputString($request->get('workshop_id'));
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

    	if( $isUpdate && !empty($workshopId)){
    		$workshopDetails = static::find($workshopId);
    		if(!is_object($workshopDetails)){
    			return Redirect::to('admin/manageWorkshopDetails');
    		}
    	} else {
    		$workshopDetails = new static;
    	}
    	$workshopDetails->name = $workshopName;
    	$workshopDetails->workshop_category_id = $categoryId;
    	$workshopDetails->author = $author;
        $workshopDetails->author_introduction = $authorIntroduction;
    	$workshopDetails->description = $description;
        $workshopDetails->certified = $certified;
        if($request->exists('author_image')){
            $authorImage = $request->file('author_image')->getClientOriginalName();
            $workshopImageFolder = "workshopImages/";
            $workshopFolderPath = $workshopImageFolder.str_replace(' ', '_', $workshopName);
            if(!is_dir($workshopFolderPath)){
                mkdir($workshopFolderPath, 0777, true);
            }
            $authorImagePath = $workshopFolderPath ."/". $authorImage;
            if(file_exists($authorImagePath)){
                unlink($authorImagePath);
            } elseif(!empty($workshopDetails->id) && file_exists($workshopDetails->author_image)){
                unlink($workshopDetails->author_image);
            }
            $request->file('author_image')->move($workshopFolderPath, $authorImage);
            $workshopDetails->author_image = $authorImagePath;
        }

        if($request->exists('workshop_image')){
            $workshopImage = $request->file('workshop_image')->getClientOriginalName();
            $workshopImageFolder = "workshopImages/";

            $workshopFolderPath = $workshopImageFolder.str_replace(' ', '_', $workshopName);
            if(!is_dir($workshopFolderPath)){
                mkdir($workshopFolderPath, 0777, true);
            }
            $workshopImagePath = $workshopFolderPath ."/". $workshopImage;
            if(file_exists($workshopImagePath)){
                unlink($workshopImagePath);
            } elseif(!empty($workshopDetails->id) && file_exists($workshopDetails->workshop_image)){
                unlink($workshopDetails->workshop_image);
            }
            $request->file('workshop_image')->move($workshopFolderPath, $workshopImage);
            $workshopDetails->workshop_image = $workshopImagePath;
        }

        $workshopDetails->start_date = $start_date;
        $workshopDetails->end_date = $end_date;
    	$workshopDetails->save();
    	return $workshopDetails;
    }

    public function category(){
    	return $this->belongsTo(WorkshopCategory::class, 'workshop_category_id');
    }

    public function workshopVideos(){
        return $this->hasMany(WorkshopVideo::class, 'workshop_details_id');
    }

    protected function getWorkshopsByCategory($categoryId){
    	return static::where('workshop_category_id', $categoryId)->get();
    }
}
