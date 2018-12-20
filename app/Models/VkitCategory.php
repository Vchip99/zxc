<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Auth;
use App\Models\VkitProject;

class VkitCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update category
     */
    protected static function addOrUpdateCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $vkitCategory = static::find($categoryId);
            if(!is_object($vkitCategory)){
            	return 'false';
            }
        } else{
            $vkitCategory = new static;
        }
        $vkitCategory->name = $categoryName;
        $vkitCategory->save();
        return $vkitCategory;
    }

    /**
     *  return all project categories
     */
    protected function getProjectCategoriesAssociatedWithProject(){
        return DB::table('vkit_categories')
            ->join('vkit_projects','vkit_projects.category_id', '=', 'vkit_categories.id')
            ->where('vkit_projects.admin_approve', 1)
            ->select('vkit_categories.id', 'vkit_categories.name')->groupBy('vkit_categories.id')
            ->get();
    }

    /**
     *  return all project categories
     */
    protected function getProjectCategoriesWithPagination(){
        return  static::paginate();
    }

    /**
     *  return all project categories
     */
    protected function getProjectCategories(){
        return  static::get();
    }

    // /**
    //  *  return all project categories
    //  */
    // protected function getProjectCategoriesByCollegeIdByDeptId($collegeId,$deptId=NULL){
    //     $result =  static::where('college_id', $collegeId);
    //     if($deptId != NULL){
    //         $result->where('college_dept_id', $deptId);
    //     }
    //     return $result->select('id', 'name')->get();
    // }

    public function projects(){
        return $this->hasMany(VkitProject::class, 'category_id');
    }

    protected static function isVkitCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', '=',$category);
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
}