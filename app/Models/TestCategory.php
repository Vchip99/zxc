<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\TestSubCategory;
use DB;

class TestCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update test category
     */
    protected static function addOrUpdateCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return Redirect::to('admin/manageCategory');
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->save();
        return $category;
    }

    /**
     *  return all test categories
     */
    protected static function getAllTestCategories(){
    	return TestCategory::all();
    }

    /**
     * return test categopries registered subject papers
     */
    protected static function getTestCategoriesByRegisteredSubjectPapersByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        return DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->join('register_papers', 'register_papers.test_subject_paper_id', 'test_subject_papers.id')
                ->join('users', 'users.id', '=', 'register_papers.user_id')
                ->where('register_papers.user_id', $userId)
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesAssociatedWithQuestion(){
        return DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->join('questions', 'questions.category_id', 'test_categories.id')
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
    }

    protected static function getTestCategoriesAssociatedWithPapers(){
        return DB::table('test_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_category_id', 'test_categories.id')
                ->select('test_categories.id', 'test_categories.name')->groupBy('test_categories.id')->get();
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