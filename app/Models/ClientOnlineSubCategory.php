<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineCourse;

class ClientOnlineSubCategory extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'client_id'];

    /**
     *  create/update course sub category
     */
    protected static function addOrUpdateClientOnlineSubCategory( Request $request, $isUpdate=false){

    	$categoryId = InputSanitise::inputInt($request->get('category'));
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	$subCategoryName = InputSanitise::inputString($request->get('subcategory'));

        if( $isUpdate && isset($subCategoryId)){
            $subcategory = static::find($subCategoryId);
            if(!is_object($subcategory)){
            	return 'false';
            }
        } else{
            $subcategory = new static;
        }
        $subcategory->name = $subCategoryName;
		$subcategory->category_id = $categoryId;
		$subcategory->client_id = Auth::guard('client')->user()->id;
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
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            return DB::connection('mysql2')->table('client_online_sub_categories')
                        ->where('category_id', $id)
                        ->where('client_id', $loginUser->id)
                        ->select('client_online_sub_categories.*')
                        ->get();
        } else {
            $client = InputSanitise::getCurrentClient($request);
            return DB::connection('mysql2')->table('client_online_sub_categories')
                        ->join('clients', 'clients.id', '=', 'client_online_sub_categories.client_id')
                        ->where('client_online_sub_categories.category_id', $id)
                        ->where('clients.subdomain', $client)
                        ->select('client_online_sub_categories.*')
                        ->groupBy('client_online_sub_categories.id')
                        ->get();
        }
    }

    protected static function getOnlineSubCategoriesWithCourses($id, Request $request){
        $client = InputSanitise::getCurrentClient($request);
        return DB::connection('mysql2')->table('client_online_sub_categories')
                    ->join('clients', 'clients.id', '=', 'client_online_sub_categories.client_id')
                    ->join('client_online_courses', 'client_online_courses.sub_category_id', '=', 'client_online_sub_categories.id')
                    ->where('client_online_sub_categories.category_id', $id)
                    ->where('clients.subdomain', $client)
                    ->where('client_online_courses.release_date','<=', date('Y-m-d H:i'))
                    ->select('client_online_sub_categories.*')
                    ->groupBy('client_online_sub_categories.id')
                    ->get();
    }

    protected static function showSubCategories(Request $request){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients','clients.id', '=', 'client_online_sub_categories.client_id');
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_sub_categories.*')->get();
    }

    protected static function deleteClientOnlineSubCategoriesByClientId($clientId){
        $subcategories = static::where('client_id', $clientId)->get();
        if(is_object($subcategories) && false == $subcategories->isEmpty()){
            foreach($subcategories as $subcategory){
                $subcategory->delete();
            }
        }
    }

    protected static function isClientCourseSubCategoryExist(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $result = static::where('client_id', $clientId)->where('category_id', $categoryId)->where('name', '=',$subCategoryName);
        if(!empty($subCategoryId)){
            $result->where('id', '!=', $subCategoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
