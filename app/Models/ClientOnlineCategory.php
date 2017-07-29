<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientInstituteCourse;

class ClientOnlineCategory extends Model
{
	protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'client_institute_course_id'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateOnlineCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));

        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return Redirect::to('manageOnlineCategory');
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->client_id = Auth::guard('client')->user()->id;
        $category->client_institute_course_id = $instituteCourseId;
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
                    ->select('client_online_categories.id', 'client_online_categories.name')
                    ->groupBy('client_online_categories.id')
                    ->get();
    }

    protected static function showCategories(Request $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_categories.client_id');
                })
                ->join('client_institute_courses', 'client_institute_courses.id', '=', 'client_online_categories.client_institute_course_id');
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

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    protected static function getCategoriesByInstituteCourseId($id){
        return static::where('client_institute_course_id', $id)->get();
    }
}