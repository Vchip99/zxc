<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Cache;
use App\Libraries\InputSanitise;
use App\Models\OfflineWorkshopCategory;
use App\Models\OfflineWorkshopComponent;
use Intervention\Image\ImageManagerStatic as Image;

class OfflineWorkshopDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'offline_workshop_category_id','about', 'about_image', 'benefits', 'benefits_image', 'duration', 'topics', 'projects', 'prerequisite', 'attendees', 'learn_reason'];

    /**
     *  create/update WorkshopDetails
     */
    protected static function addOrUpdateWorkshopDetails(Request $request, $isUpdate = false){
        $components = [];
        $addComponents = [];
        $updateComponents = [];

    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $workshopName = InputSanitise::inputString($request->get('workshop'));
        $workshopId = InputSanitise::inputString($request->get('workshop_id'));
        $about = $request->get('about');
        $benefits = $request->get('benefits');
        $duration = $request->get('duration');
        $topics = $request->get('topics');
        $projects = $request->get('projects');
        $prerequisite = $request->get('prerequisite');
        $attendees = $request->get('attendees');
        $learnReason = $request->get('learn_reason');

    	if( $isUpdate && !empty($workshopId)){
    		$workshopDetails = static::find($workshopId);
    		if(!is_object($workshopDetails)){
    			return 'false';
    		}
    	} else {
    		$workshopDetails = new static;
    	}
    	$workshopDetails->name = $workshopName;
    	$workshopDetails->offline_workshop_category_id = $categoryId;
    	$workshopDetails->about = $about;
        $workshopDetails->benefits = $benefits;
    	$workshopDetails->duration = $duration;
        $workshopDetails->topics = $topics;
        $workshopDetails->projects = $projects;
        $workshopDetails->prerequisite = $prerequisite;
    	$workshopDetails->attendees = $attendees;
        $workshopDetails->learn_reason = $learnReason;
        if($request->exists('about_image')){
            $authorImage = $request->file('about_image')->getClientOriginalName();
            $workshopImageFolder = "offlineWorkshopImages/";
            $workshopFolderPath = $workshopImageFolder.str_replace(' ', '_', $workshopName);
            if(!is_dir($workshopFolderPath)){
                mkdir($workshopFolderPath, 0777, true);
            }
            $authorImagePath = $workshopFolderPath ."/". $authorImage;
            if(file_exists($authorImagePath)){
                unlink($authorImagePath);
            } elseif(!empty($workshopDetails->id) && file_exists($workshopDetails->about_image)){
                unlink($workshopDetails->about_image);
            }
            $request->file('about_image')->move($workshopFolderPath, $authorImage);
            $workshopDetails->about_image = $authorImagePath;
            if(in_array($request->file('about_image')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($workshopDetails->about_image);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
        }

        if($request->exists('benefits_image')){
            $workshopImage = $request->file('benefits_image')->getClientOriginalName();
            $workshopImageFolder = "offlineWorkshopImages/";

            $workshopFolderPath = $workshopImageFolder.str_replace(' ', '_', $workshopName);
            if(!is_dir($workshopFolderPath)){
                mkdir($workshopFolderPath, 0777, true);
            }
            $workshopImagePath = $workshopFolderPath ."/". $workshopImage;
            if(file_exists($workshopImagePath)){
                unlink($workshopImagePath);
            } elseif(!empty($workshopDetails->id) && file_exists($workshopDetails->benefits_image)){
                unlink($workshopDetails->benefits_image);
            }
            $request->file('benefits_image')->move($workshopFolderPath, $workshopImage);
            $workshopDetails->benefits_image = $workshopImagePath;
            if(in_array($request->file('benefits_image')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($workshopDetails->benefits_image);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
        }
    	$workshopDetails->save();

        if( $isUpdate && isset($workshopId)){
            $components = $request->except('_token','_method', 'category', 'workshop','workshop_id','about', 'about_image', 'benefits', 'benefits_image', 'duration', 'topics', 'projects', 'prerequisite', 'attendees', 'learn_reason', 'component_count', 'category_text');

            if(count($components) > 0){
                foreach($components as $index => $component){
                    $explodes = explode('_', $index);
                    if('new' == $explodes[0]){
                        $addComponents[] = $explodes[1];
                    } else {
                        $updateComponents[$explodes[1]][$explodes[0]] = $component;
                    }
                }
            }
            if(count($updateComponents) > 0){
                $components = $components = OfflineWorkshopComponent::where('offline_workshop_id', $workshopDetails->id)->get();
                if(is_object($components) && false == $components->isEmpty()){
                    // update or delete
                    foreach($components as $component){
                        if(false == in_array($component->id, $addComponents)){
                            if(isset($updateComponents[$component->id])){
                                $component->name = $updateComponents[$component->id]['component'];
                                $component->quantity = $updateComponents[$component->id]['quantity'];
                                $component->save();
                            } else {
                                $component->delete();
                            }
                        }
                    }
                }
                // add new
                foreach($updateComponents as $index => $updateComponent){
                    if(true == in_array($index, $addComponents)){
                        $newComponent = new OfflineWorkshopComponent;
                        $newComponent->name = $updateComponent['component'];
                        $newComponent->quantity = $updateComponent['quantity'];
                        $newComponent->offline_workshop_id = $workshopDetails->id;
                        $newComponent->save();
                    }
                }
            }
        } else {
            $componentCount = InputSanitise::inputInt($request->get('component_count'));
            if($componentCount > 0){
                for($i=1; $i<=$componentCount; $i++){
                    $component = $request->get('component_'.$i);
                    $quantity = $request->get('quantity_'.$i);
                    if(!empty($component) && !empty($quantity)){
                        $components[] = [
                                    'name' => $component,
                                    'quantity' => $quantity,
                                    'offline_workshop_id' => $workshopDetails->id
                                ];
                    }
                }
                if(count($components) > 0){
                    DB::table('offline_workshop_components')->insert($components);
                }
            }
        }

    	return $workshopDetails;
    }

    public function category(){
    	return $this->belongsTo(OfflineWorkshopCategory::class, 'offline_workshop_category_id');
    }

    protected static function getOfflineWorkshopsByCategory(Request $request){
        $data = [];
        $categoryId = $request->id;
        $results = Cache::remember('vchip:workshops:workshops:cat-'.$categoryId,30, function() use ($categoryId){
            return static::where('offline_workshop_category_id', $categoryId)->get();
        });
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $data[] = [
                            'id' => $result->id,
                            'name' => $result->name,
                            'about' => $result->about,
                            'about_image' => $result->about_image,
                        ];
            }
        }
        return $data;
    }

    protected static function isOfflineWorkshopExist(Request $request){
        $workshop = InputSanitise::inputString($request->get('workshop'));
        $workshopId   = InputSanitise::inputInt($request->get('workshop_id'));
        $category   = InputSanitise::inputInt($request->get('category'));
        $result = static::where('offline_workshop_category_id', $category)->where('name', '=',$workshop);
        if(!empty($workshopId)){
            $result->where('id', '!=', $workshopId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
