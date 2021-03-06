<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientOnlineSubCategory;

class ClientOnlineCategory extends Model
{
	protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id','created_by'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateOnlineCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));

        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return 'false';
            }
        } else{
            $category = new static;
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];

        $category->name = $categoryName;
        $category->client_id = $clientId;
        $category->created_by = $createdBy;
        $category->save();
        return $category;
    }

    protected static function getCategoriesAssocaitedWithVideos($subdomain){
        return DB::connection('mysql2')->table('client_online_categories')
                    ->join('client_online_courses', 'client_online_courses.category_id', '=', 'client_online_categories.id')
                    ->join('client_online_videos', 'client_online_videos.course_id', '=', 'client_online_courses.id')
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_courses.client_id');
                        $join->on('clients.id', '=', 'client_online_videos.client_id');
                    })
                    ->where('clients.subdomain', $subdomain)
                    ->where('client_online_courses.release_date','<=', date('Y-m-d H:i:s'))
                    ->select('client_online_categories.id', 'client_online_categories.name')
                    ->groupBy('client_online_categories.id')
                    ->get();
    }

    protected static function showCategories(Request $request){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_categories.client_id');
                });
                if(!empty($clientId)){
                    $result->where('clients.id', $clientId);
                } else {
                    $result->where('clients.subdomain', $client);
                }
            return  $result->select('client_online_categories.*')->get();
    }

    public function subcategories(){
        return $this->hasMany(ClientOnlineSubCategory::class, 'category_id');
    }

    protected static function getCategoriesByInstituteCourseId($id){
        return static::where('client_institute_course_id', $id)->get();
    }

    protected static function deleteClientOnlineCategoriesByClientId($clientId){
        $categories = static::where('client_id', $clientId)->get();
        if(is_object($categories) && false == $categories->isEmpty()){
            foreach($categories as $category){
                $category->delete();
            }
        }
    }

    protected static function isClientCourseCategoryExist(Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('client_id', $clientId)->where('name', '=',$categoryName);
        if(!empty($categoryId)){
            $result->where('id', '!=', $categoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function assignClientOnlineCategoriesToClientByClientIdByTeacherId($clientId,$teacherId){
        $categories = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($categories) && false == $categories->isEmpty()){
            foreach($categories as $category){
                $category->created_by = 0;
                $category->save();
            }
        }
    }
}
