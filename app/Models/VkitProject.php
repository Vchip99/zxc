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
    protected $fillable = ['name', 'author', 'introduction', 'category_id', 'gateway', 'microcontroller', 'front_image_path', 'header_image_path', 'project_pdf_path', 'date', 'description','created_for','created_by','price','items','admin_approve'];

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
        $price = trim($request->get('price'));
        $items = trim($request->get('items'));
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
            if(in_array($request->file('front_image')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                 // open image
                $img = Image::make($vkitProject->front_image_path);
                // enable interlacing
                $img->interlace();
                // save image interlaced
                $img->save();
            }
        }
        if(isset($dbHeaderImagePath)){
            $vkitProject->header_image_path = $dbHeaderImagePath;
            if(in_array($request->file('header_image')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                 // open image
                $img = Image::make($vkitProject->header_image_path);
                // enable interlacing
                $img->interlace();
                // save image interlaced
                $img->save();
            }
        }
        if(isset($dbPdfPath)){
            $vkitProject->project_pdf_path = $dbPdfPath;
        }
        $vkitProject->date = $projectDate;
        $vkitProject->description = $projectDescription;
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $vkitProject->created_for = 0;
            $vkitProject->created_by = Auth::user()->id;
        } else {
            $vkitProject->created_for = 1;
            $vkitProject->created_by = Auth::guard('admin')->user()->id;
        }
        $vkitProject->price = $price;
        $vkitProject->items = $items;
        $vkitProject->save();
        return $vkitProject;
    }

    /**
     *  return projects by categoryId
     */
    protected static function getVkitProjectsByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return DB::table('vkit_projects')->where('created_for', 1)->where('category_id', $categoryId)->get();
        } else {
            return DB::table('vkit_projects')->where('created_for', 1)->where('admin_approve', 1)->where('category_id', $categoryId)->get();
        }
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

    protected static function getVchipFavouriteVkitProjectsByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        return DB::table('vkit_projects')
                ->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->where('vkit_projects.created_for', 1)
                ->where('register_projects.user_id', $userId)
                ->select('vkit_projects.id','vkit_projects.name','vkit_projects.front_image_path','vkit_projects.author','vkit_projects.introduction','vkit_projects.category_id')
                ->get();
    }

    protected static function getCollegeFavouriteVkitProjectsByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        $collegeId = Auth::user()->college_id;
        if($collegeId > 0){
            return DB::table('vkit_projects')
                ->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->join('college_categories', 'college_categories.id', '=', 'vkit_projects.category_id')
                ->where('vkit_projects.created_for', 0)
                ->where('register_projects.user_id', $userId)
                ->where('college_categories.college_id', $collegeId)
                ->select('vkit_projects.id','vkit_projects.name','vkit_projects.front_image_path','vkit_projects.author','vkit_projects.introduction','vkit_projects.category_id')
                ->get();
        }
        return;
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

    protected static function getVkitProjectsWithPaginationForAdmin(){
        $result = static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
            ->join('admins','admins.id','=', 'vkit_projects.created_by')
            ->where('vkit_projects.created_for', 1);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('vkit_projects.created_by', Auth::guard('admin')->user()->id);
        }
        return $result->select('vkit_projects.*','vkit_categories.name as category','admins.name as admin')
                ->groupBy('vkit_projects.id')->paginate();
    }

    protected static function getVkitProjectsWithPagination(){
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                    ->where('vkit_projects.created_for', 1)
                    ->select('vkit_projects.*','vkit_categories.name as category')
                    ->groupBy('vkit_projects.id')->paginate();
        } else {
            $result = static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->where('vkit_projects.created_for', 1)
                ->where('vkit_projects.admin_approve', 1);
            if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
                $result->where('vkit_projects.created_by', Auth::guard('admin')->user()->id);
            }
            return $result->select('vkit_projects.*','vkit_categories.name as category')
                ->groupBy('vkit_projects.id')->paginate();
        }
    }

    protected static function getPurchasedVkitProjects($adminId = NULL){
        $result = static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->join('register_projects','register_projects.project_id','=','vkit_projects.id')
                ->where('vkit_projects.created_for', 1)
                ->where('register_projects.price','>',0);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('vkit_projects.created_by', Auth::guard('admin')->user()->id);
        } else {
            if($adminId > 0){
                $result->where('vkit_projects.created_by', $adminId);
            }
        }
        return $result->select('register_projects.id','register_projects.price','register_projects.user_id','vkit_categories.name as category','vkit_projects.created_by','vkit_projects.name','register_projects.updated_at')->groupBy('register_projects.id')->get();
    }

    protected static function getPurchasedVkitProjectById($projectId = NULL){
        $result = static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->join('register_projects','register_projects.project_id','=','vkit_projects.id')
                ->where('vkit_projects.created_for', 1)
                ->where('register_projects.price','>',0)
                ->whereNotNull('register_projects.payment_id')
                ->whereNotNull('register_projects.payment_request_id')
                ->where('register_projects.id',$projectId);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('vkit_projects.created_by', Auth::guard('admin')->user()->id);
        }
        return $result->select('register_projects.id','register_projects.price','register_projects.user_id','vkit_projects.name','register_projects.updated_at')->first();
    }

    protected static function getVchipVkitProjects(){
        return static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')->select('vkit_projects.*','vkit_categories.name as category')->where('vkit_projects.created_for', 1)->get();
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
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            $results = DB::table('vkit_projects');
        } else {
            $results = DB::table('vkit_projects')->where('admin_approve', 1);
        }

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
        $registeredProjects = RegisterProject::where('project_id', $this->id)->get();
        if(is_object($registeredProjects) && false == $registeredProjects->isEmpty()){
            foreach($registeredProjects as $registeredProject){
                $registeredProject->delete();
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

    /**
     *  get user
     */
    public function getUser(){
        $user = User::find($this->user_id);
        if(is_object($user)){
            return $user->name;
        }
        return;
    }

    protected static function getSubAdminProjectsWithPagination(){
        return static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
            ->join('admins','admins.id','=', 'vkit_projects.created_by')
            ->where('vkit_projects.created_for', 1)
            ->where('vkit_projects.created_by','!=', 1)
            ->select('vkit_projects.*','vkit_categories.name as category','admins.name as admin')
                ->groupBy('vkit_projects.id')->paginate();
    }

    protected static function getSubAdminProjects($adminId){
        return static::join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
            ->join('admins','admins.id','=', 'vkit_projects.created_by')
            ->where('vkit_projects.created_for', 1)
            ->where('vkit_projects.created_by', $adminId)
            ->select('vkit_projects.*','vkit_categories.name as category','admins.name as admin')
                ->groupBy('vkit_projects.id')->get();
    }

    protected static function changeSubAdminProjectApproval($request){
        $projectId = $request->get('project_id');
        $project = static::find($projectId);
        if(is_object($project)){
            if(1 == $project->admin_approve){
                $project->admin_approve = 0;
            } else {
                $project->admin_approve = 1;
            }
            $project->save();
            return 'true';
        }
        return 'false';
    }

    protected static function deleteSubAdminProjectsByAdminId($adminId){
        $projects = static::where('created_by', $adminId)->where('created_for', 1)->get();
        if(is_object($projects) && false == $projects->isEmpty()){
            foreach($projects as $project){
                $project->deleteCommantsAndSubComments();
                $project->deleteRegisteredProjects();
                $project->deleteProjectImageFolder();
                $project->delete();
            }
        }
    }
}
