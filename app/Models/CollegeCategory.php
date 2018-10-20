<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeCategory extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','college_id','college_dept_id','user_id','created_by_name'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateCollegeCategory( Request $request, $isUpdate=false){
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
        $category->name = $categoryName;
        $loginUser = Auth::guard('web')->user();
        $category->college_id = $loginUser->college_id;
        $category->college_dept_id = $loginUser->college_dept_id;
        $category->user_id = $loginUser->id;
        $category->created_by_name = $loginUser->name;
        $category->save();
        return $category;
    }

    protected static function isCollegeCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $loginUser = Auth::guard('web')->user();
        $result = static::where('name', '=',$category)->where('college_id', $loginUser->college_id);
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

    /**
     *  get categories by college n dept
     */
    protected static function getCollegeCategoriesByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $result = static::where('college_id', '=', $collegeId);
        if(NULL != $deptId){
            $result->where('college_dept_id', '=', $deptId);
        }
        return $result->select('college_categories.*')->paginate();
    }

    /**
     *  get categories by collegeId n deptId
     */
    protected static function getCollegeCategoriesByCollegeIdByDeptId($collegeId,$deptId=NULL){
        $result = static::where('college_id', '=', $collegeId);
        if(NULL != $deptId){
            $result->where('college_dept_id', '=', $deptId);
        }
        return $result->select('id', 'name')->get();
    }

    protected static function getTestCategoriesByCollegeIdByDeptIdAssociatedWithPapers($collegeId,$deptId=NULL){
        $result =  DB::table('college_categories')
                ->join('test_sub_categories', 'test_sub_categories.test_category_id', '=', 'college_categories.id')
                ->join('test_subjects', 'test_subjects.test_category_id', '=', 'college_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', '=','college_categories.id')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->where('test_sub_categories.created_for', 0)
                ->select('college_categories.id', 'college_categories.name')->groupBy('college_categories.id')->get();
    }

    protected static function getTestCategoriesAssociatedWithQuestion(){
        return DB::table('college_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'college_categories.id')
                ->join('questions', 'questions.category_id', 'college_categories.id')
                ->where('category_for', 1)
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->select('college_categories.id', 'college_categories.name')->groupBy('college_categories.id')->get();
    }

    protected static function getTestCategoriesByCollegeIdByDeptIdAssociatedWithQuestion($collegeId,$deptId=NULL){
        $result =  DB::table('college_categories')
                ->join('test_sub_categories', 'test_sub_categories.test_category_id', '=', 'college_categories.id')
                ->join('test_subjects', 'test_subjects.test_category_id', '=', 'college_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', '=','college_categories.id')
                ->join('questions', 'questions.category_id', 'college_categories.id')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->where('test_sub_categories.created_for', 0)
                ->select('college_categories.id', 'college_categories.name')->groupBy('college_categories.id')->get();
    }

    /**
     * return test categopries registered subject papers
     */
    protected static function getTestCategoriesByRegisteredSubjectPapersByUserId($userId,$collegeId,$collegeDeptId){
        $userId = InputSanitise::inputInt($userId);
        return DB::table('college_categories')
                ->join('test_sub_categories', 'test_sub_categories.test_category_id', '=', 'college_categories.id')
                ->join('test_subjects', 'test_subjects.test_category_id', '=', 'college_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'college_categories.id')
                // ->join('register_papers', 'register_papers.test_subject_paper_id', 'test_subject_papers.id')
                // ->join('users', 'users.id', '=', 'register_papers.user_id')
                ->where('test_sub_categories.created_for', 0)
                ->where('college_categories.college_id', $collegeId)
                // ->where('college_categories.college_dept_id', $collegeDeptId)
                // ->where('register_papers.user_id', $userId)
                ->select('college_categories.id', 'college_categories.name')->groupBy('college_categories.id')->get();
    }

    /**
     *  return all project categories
     */
    protected function getProjectCategoriesByCollegeIdByDeptId($collegeId,$deptId=NULL){
        $result =  static::join('vkit_projects', 'vkit_projects.category_id', '=', 'college_categories.id')
            ->where('vkit_projects.created_for',0)
            ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->select('college_categories.id', 'college_categories.name')->groupBy('college_categories.id')->get();
    }

}
