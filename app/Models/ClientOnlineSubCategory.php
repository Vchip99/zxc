<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineCourse;
use App\Models\ClientInstituteCourse;

class ClientOnlineSubCategory extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'client_id', 'client_institute_course_id'];

    /**
     *  create/update course sub category
     */
    protected static function addOrUpdateClientOnlineSubCategory( Request $request, $isUpdate=false){

    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	$subCategoryName = InputSanitise::inputString($request->get('subcategory'));

        if( $isUpdate && isset($subCategoryId)){
            $subcategory = static::find($subCategoryId);
            if(!is_object($subcategory)){
            	return Redirect::to('manageOnlineSubCategory');
            }
        } else{
            $subcategory = new static;
        }
        $subcategory->name = $subCategoryName;
		$subcategory->category_id = $categoryId;
		$subcategory->client_id = Auth::guard('client')->user()->id;
        $subcategory->client_institute_course_id = $instituteCourseId;
		$subcategory->save();

        return $subcategory;
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(ClientOnlineCategory::class, 'category_id');
    }

    public function courses(){
        return $this->hasMany(ClientOnlineCourse::class, 'sub_category_id');
    }

    protected static function getOnlineSubCategoriesByCategoryId($id, Request $request){
        if(is_object(Auth::guard('client')->user())){
            return DB::connection('mysql2')->table('client_online_sub_categories')
                        ->where('category_id', $id)
                        ->where('client_id', Auth::guard('client')->user()->id)
                        ->select('client_online_sub_categories.*')
                        ->get();
        } else {
            $client = InputSanitise::getCurrentClient($request);
            return DB::connection('mysql2')->table('client_online_sub_categories')
                        ->join('clients', 'clients.id', '=', 'client_online_sub_categories.client_id')
                        ->where('client_online_sub_categories.category_id', $id)
                        ->where('clients.subdomain', $client)
                        ->select('client_online_sub_categories.*')
                        ->get();
        }
    }

    protected static function showSubCategories(Request $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients','clients.id', '=', 'client_online_sub_categories.client_id')
                    ->join('client_institute_courses', 'client_institute_courses.id', '=', 'client_online_sub_categories.client_institute_course_id');
                if(!empty($clientId)){
                    $result->where('clients.id', $clientId);
                } else {
                    $result->where('clients.subdomain', $client);
                }
            return  $result->select('client_online_sub_categories.*')->get();
    }

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }
}