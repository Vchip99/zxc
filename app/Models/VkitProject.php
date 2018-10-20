<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CollegeCategory;
use App\Models\VkitProjectComment;
use App\Models\VkitProjectLike;
use App\Models\RegisterProject;
use DB,Auth;
use Intervention\Image\ImageManagerStatic as Image;

class VkitProject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'author', 'introduction', 'category_id', 'gateway', 'microcontroller', 'front_image_path', 'header_image_path', 'project_pdf_path', 'date', 'description','created_for','created_by'];

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
                return 'false';
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
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $vkitProject->created_for = 0;
            $vkitProject->created_by = Auth::user()->id;
        }
        $vkitProject->save();
        return $vkitProject;

    }

    /**
     *  return projects by categoryId
     */
    protected static function getVkitProjectsByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('vkit_projects')->where('created_for', 1)->where('category_id', $categoryId)->get();
    }

    protected static function getRegisteredVkitProjectsByUserIdByCategoryId($userId, $categoryId){
        $userId = InputSanitise::inputInt($userId);
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('vkit_projects')
                ->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->where('vkit_projects.created_for', 1)
                ->where('vkit_projects.category_id', $categoryId)
                ->where('register_projects.user_id', $userId)
                ->select('vkit_projects.id','vkit_projects.name','vkit_projects.front_image_path','vkit_projects.author','vkit_projects.introduction','vkit_projects.category_id')
                ->get();
    }

    protected static function getVkitProjectsByCollegeIdByDeptIdWithPagination($collegeId, $deptId=NULL){
        $deptId = InputSanitise::inputInt($deptId);
        $collegeId = InputSanitise::inputInt($collegeId);
        $result = static::join('users','users.id','=','vkit_projects.created_by')
            ->join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
            ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->where('vkit_projects.created_for', 0)->select('vkit_projects.*','college_categories.name as category','users.name as user')
            ->groupBy('vkit_projects.id')
            ->paginate();
    }

    protected static function getVkitProjectsByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','vkit_projects.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('vkit_projects.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('vkit_projects.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('vkit_projects.created_for', 0)->select('vkit_projects.*','college_categories.name as category','users.name as user')
            ->groupBy('vkit_projects.id')
            ->paginate();
    }

    protected static function getVkitProjectsWithPagination(){
        return static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')->select('vkit_projects.*','vkit_categories.name as category')->where('vkit_projects.created_for', 1)->paginate();
    }

    protected static function getVkitProjectsByCollegeIdByDeptId($collegeId, $deptId=NULL){
        $deptId = InputSanitise::inputInt($deptId);
        $collegeId = InputSanitise::inputInt($collegeId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
                ->where('college_categories.college_id', $collegeId)
                ->where('vkit_projects.created_for',0);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->select('vkit_projects.*','college_categories.name as category')
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
    public function collegeCategory(){
        return $this->belongsTo(CollegeCategory::class, 'category_id');
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

    protected static function isVkitProjectExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $projectName = InputSanitise::inputString($request->get('project'));
        $projectId = InputSanitise::inputInt($request->get('project_id'));

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories','college_categories.id','=','vkit_projects.category_id')
                ->where('vkit_projects.category_id', $categoryId)->where('vkit_projects.name', $projectName);
            if(!empty($projectId)){
                $result->where('vkit_projects.id', '!=', $projectId);
            }
            $result->where('vkit_projects.created_for', 0)->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('vkit_categories','vkit_categories.id','=','vkit_projects.category_id')
                ->where('vkit_projects.category_id', $categoryId)->where('vkit_projects.name', $projectName)->where('vkit_projects.created_for', 1);
            if(!empty($projectId)){
                $result->where('vkit_projects.id', '!=', $projectId);
            }
        }

        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    /**
     *  return projects by categoryId
     */
    protected static function getCollegeVkitProjectsByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return static::join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
            ->where('vkit_projects.created_for', 0)
            ->where('vkit_projects.category_id', $categoryId)
            ->select('vkit_projects.*')->groupBy('vkit_projects.id')
            ->get();
    }
    /**
     *  return projects by categoryId
     */
    protected static function getCollegeVkitProjectsById($id){
        $id = InputSanitise::inputInt($id);
        return DB::table('vkit_projects')->join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
            ->where('vkit_projects.created_for', 0)
            ->where('vkit_projects.id', $id)
            ->select('vkit_projects.*')->groupBy('vkit_projects.id')
            ->first();
    }

    /**
     *  return projects by categoryId
     */
    protected static function getVkitProjectsById($id){
        $id = InputSanitise::inputInt($id);
        return DB::table('vkit_projects')->join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
            ->where('vkit_projects.created_for', 1)
            ->where('vkit_projects.id', $id)
            ->select('vkit_projects.*')->groupBy('vkit_projects.id')
            ->first();
    }

    protected static function deleteCollegeProjectsByUserId($userId){
        $projects =  static::where('created_by', $userId)->get();
        if(is_object($projects) && false == $projects->isEmpty()){
            foreach($projects as $project){
                $project->deleteCommantsAndSubComments();
                $project->deleteRegisteredProjects();
                $project->deleteProjectImageFolder();
                $project->delete();
            }
        }
        return;
    }
}