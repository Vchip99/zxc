<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\VkitCategory;
use App\Models\VkitProjectComment;
use App\Models\VkitProjectLike;
use App\Models\RegisterProject;
use DB;
use Intervention\Image\ImageManagerStatic as Image;

class VkitProject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'author', 'introduction', 'category_id', 'gateway', 'microcontroller', 'front_image_path', 'header_image_path', 'project_pdf_path', 'date', 'description'];

    /**
     *  add/update project
     */
  	protected static function addOrUpdateProject( Request $request, $isUpdate=false){

        $projectName = InputSanitise::inputString($request->get('project'));
        $projectAuthor = InputSanitise::inputString($request->get('author'));
        $projectIntroduction = InputSanitise::inputString($request->get('introduction'));
        $projectCategoryId = InputSanitise::inputInt($request->get('category_id'));
        $projectGateway = InputSanitise::inputInt($request->get('gateway'));
        $projectMicrocontroller = InputSanitise::inputInt($request->get('microcontroller'));

        $projectDate = strip_tags(trim($request->get('date')));
        $projectDescription = trim($request->get('description'));
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        if( $isUpdate && isset($projectId)){
            $vkitProject = static::find($projectId);
            if(!is_object($vkitProject)){
                return Redirect::to('admin/manageVkitProject');
            }
        } else{
            $vkitProject = new static;
        }

        $projectFolderPath = public_path()."/projectStorage/".str_replace(' ', '_', $projectName);
        if(!is_dir($projectFolderPath)){
        	mkdir($projectFolderPath, 0755);
        }
        if($request->exists('front_image')){
	        $projectFrontImage = $request->file('front_image')->getClientOriginalName();
	        $projectFrontImagePath = $projectFolderPath."/".$projectFrontImage;
	        if(file_exists($projectFrontImagePath)){
	        	unlink($projectFrontImagePath);
	        } elseif(!empty($vkitProject->id) && file_exists($vkitProject->front_image_path)){
                unlink($vkitProject->front_image_path);
            }
	        $request->file('front_image')->move($projectFolderPath, $projectFrontImage);
            $dbFrontImagePath = "projectStorage/".str_replace(' ', '_', $projectName)."/".$projectFrontImage;
	    }
	    if($request->exists('header_image')){
	        $projectHeaderImage = $request->file('header_image')->getClientOriginalName();
	        $projectHeaderImagePath = $projectFolderPath."/".$projectHeaderImage;
	        if(file_exists($projectHeaderImagePath)){
	        	unlink($projectHeaderImagePath);
	        } elseif(!empty($vkitProject->id) && file_exists($vkitProject->header_image_path)){
                unlink($vkitProject->header_image_path);
            }
	        $request->file('header_image')->move($projectFolderPath, $projectHeaderImage);
            $dbHeaderImagePath = "projectStorage/".str_replace(' ', '_', $projectName)."/".$projectHeaderImage;
	    }

        if($request->exists('pdf')){
	     	$projectPdf = $request->file('pdf')->getClientOriginalName();
	        $projectPdfPath = $projectFolderPath."/".$projectPdf;
	        if(file_exists($projectPdfPath)){
	        	unlink($projectPdfPath);
	        }
	        $request->file('pdf')->move($projectFolderPath, $projectPdf);
            $dbPdfPath = "projectStorage/".str_replace(' ', '_', $projectName)."/".$projectPdf;
	    }

        $vkitProject->name = $projectName;
        $vkitProject->author = $projectAuthor;
        $vkitProject->introduction = $projectIntroduction;
        $vkitProject->category_id = $projectCategoryId;
        $vkitProject->gateway = $projectGateway;
        $vkitProject->microcontroller = $projectMicrocontroller;
        if(isset($dbFrontImagePath)){
            $vkitProject->front_image_path = $dbFrontImagePath;
             // open image
            $img = Image::make($vkitProject->front_image_path);
            // enable interlacing
            $img->interlace();
            // save image interlaced
            $img->save();
        }
        if(isset($dbHeaderImagePath)){
            $vkitProject->header_image_path = $dbHeaderImagePath;
             // open image
            $img = Image::make($vkitProject->header_image_path);
            // enable interlacing
            $img->interlace();
            // save image interlaced
            $img->save();
        }
        if(isset($dbPdfPath)){
            $vkitProject->project_pdf_path = $dbPdfPath;
        }
        $vkitProject->date = $projectDate;
        $vkitProject->description = $projectDescription;
        $vkitProject->save();
        return $vkitProject;

    }

    /**
     *  return projects by categoryId
     */
    protected static function getVkitProjectsByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('vkit_projects')->where('category_id', $categoryId)->get();
    }

    protected static function getRegisteredVkitProjectsByUserIdByCategoryId($userId, $categoryId){
        $userId = InputSanitise::inputInt($userId);
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('vkit_projects')
                ->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->where('vkit_projects.category_id', $categoryId)
                ->where('register_projects.user_id', $userId)
                ->select('vkit_projects.*')
                ->get();
    }

    /**
     *  return projects by filter array
     */
    protected static function getVkitProjectsBySearchArray(Request $request){
        $searchFilter = json_decode($request->get('arr'),true);
        $gateway = $searchFilter['gateway'];
        $microcontroller = $searchFilter['microcontroller'];
        $upcoming = InputSanitise::inputInt($searchFilter['upcoming']);
        $categoryId = InputSanitise::inputInt($searchFilter['categoryId']);

        $results = DB::table('vkit_projects');

        if(count($gateway) > 0){
            $results->whereIn('gateway', $gateway);
        }
        if(count($microcontroller) > 0){
            $results->whereIn('microcontroller', $microcontroller);
        }
        if( 1  == $upcoming ){
            $currentDate = date('Y-m-d');
            $nextDate = date('Y-m-d', strtotime("+30 days"));
            $results->whereBetween('date',[$currentDate,$nextDate]);
        }
        if(!empty($categoryId)){
            $results->where('category_id', $categoryId);
        }
        return $results->get();
    }

     /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(VkitCategory::class, 'category_id');
    }

    public function comments(){
        return $this->hasMany(VkitProjectComment::class, 'vkit_project_id');
    }

    public function deleteLikes(){
        return $this->hasMany(VkitProjectLike::class, 'vkit_project_id');
    }

    public function deleteCommantsAndSubComments(){
        if(is_object($this->comments) && false == $this->comments->isEmpty()){
            foreach($this->comments as $comment){
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                            foreach($subcomment->deleteLikes as $subcommentLike){
                                $subcommentLike->delete();
                            }
                        }
                        $subcomment->delete();
                    }
                }
                if(is_object($comment->deleteLikes) && false == $comment->deleteLikes->isEmpty()){
                    foreach($comment->deleteLikes as $commentLike){
                        $commentLike->delete();
                    }
                }
                $comment->delete();
            }
        }
        if(is_object($this->deleteLikes) && false == $this->deleteLikes->isEmpty()){
            foreach($this->deleteLikes as $videoLike){
                $videoLike->delete();
            }
        }
    }

    public function deleteRegisteredProjects(){
        $registeredPtojects = RegisterProject::where('project_id', $this->id)->get();
        if(is_object($registeredPtojects) && false == $registeredPtojects->isEmpty()){
            foreach($registeredPtojects as $registeredPtoject){
                $registeredPtoject->delete();
            }
        }
    }

    public function deleteProjectImageFolder(){
        $projectImageFolder = "projectStorage/".str_replace(' ', '_', $this->name);
        if(is_dir($projectImageFolder)){
            InputSanitise::delFolder($projectImageFolder);
        }
    }

}