<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\TestSubCategory;
use DB,Auth;

class TestCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','category_for'];

    /**
     *  add/update test category
     */
    protected static function addOrUpdateCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $categoryFor = InputSanitise::inputInt($request->get('category_for'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return 'false';
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->category_for = $categoryFor;
        $category->save();
        return $category;
    }

    /**
     *  return all test categories
     */
    protected static function getAllTestCategories(){
    	return static::all();
    }

    /**
     * return test categopries registered subject papers
     */
    protected static function getTestCategoriesByRegisteredSubjectPapersByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        return DB::table('test_categories')
                ->join('test_sub_categories', 'test_sub_categories.test_category_id', '=', 'test_categories.id')
                ->join('test_subjects', 'test_subjects.test_category_id' , '=', 'test_categories.id' )
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->join('register_papers', 'register_papers.test_subject_paper_id', 'test_subject_papers.id')
                ->join('users', 'users.id', '=', 'register_papers.user_id')
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->where('register_papers.user_id', $userId)
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesAssociatedWithQuestion(){
        return DB::table('test_categories')
                ->join('test_sub_categories', 'test_sub_categories.test_category_id', '=', 'test_categories.id')
                ->join('test_subjects', 'test_subjects.test_category_id' , '=', 'test_categories.id' )
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->join('questions', 'questions.category_id', 'test_categories.id')
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getCompanyCategoriesAssociatedWithQuestion(){
        return DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->join('questions', 'questions.category_id', 'test_categories.id')
                ->where('category_for', 0)
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesByCollegeIdByDeptIdAssociatedWithQuestion($collegeId,$deptId=NULL){
        $result =  DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', '=','test_categories.id')
                ->join('questions', 'questions.category_id', 'test_categories.id')
                ->where('test_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('test_categories.college_dept_id', $deptId);
        }
        return $result->where('test_categories.category_for', 1)
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesByCollegeIdByDeptIdAssociatedWithPapers($collegeId,$deptId=NULL){
        $result =  DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', '=','test_categories.id')
                ->where('test_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('test_categories.college_dept_id', $deptId);
        }
        return $result->where('test_categories.category_for', 1)
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $result = static::where('college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_dept_id', $deptId);
        }
        return $result->paginate();
    }

    public function subcategories(){
        return $this->hasMany(TestSubCategory::class, 'test_category_id');
    }

    protected static function isTestCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', $category);
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