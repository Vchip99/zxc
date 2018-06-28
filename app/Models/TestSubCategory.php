<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\TestSubject;
use App\Models\TestCategory;
use DB, File;
use Intervention\Image\ImageManagerStatic as Image;

class TestSubCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'image_path'];
    public $testSubCategories = [];

    /**
     *  add/update sub category
     */
    protected static function addOrUpdateSubCategory( Request $request, $isUpdate=false){
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $name = InputSanitise::inputString($request->get('name'));

        if( $isUpdate && isset($subcatId)){
            $testSubcategory = static::find($subcatId);
            if(!is_object($testSubcategory)){
                return 'false';
            }
        } else{
            $testSubcategory = new static;
        }
        $testSubcategory->name = $name;
        $testSubcategory->test_category_id = $catId;
        if($request->exists('image_path')){
            $subCategoryImage = $request->file('image_path')->getClientOriginalName();
            $subCategoryImageFolder = "testSubCategoryImages/";

            $subCategoryFolderPath = $subCategoryImageFolder.str_replace(' ', '_', $name);
            if(!is_dir($subCategoryFolderPath)){
                File::makeDirectory($subCategoryFolderPath, $mode = 0777, true, true);
            }
            $subCategoryImagePath = $subCategoryFolderPath ."/". $subCategoryImage;
            if(file_exists($subCategoryImagePath)){
                unlink($subCategoryImagePath);
            } elseif(!empty($testSubcategory->id) && file_exists($testSubcategory->image_path)){
                unlink($testSubcategory->image_path);
            }
            $request->file('image_path')->move($subCategoryFolderPath, $subCategoryImage);
            $testSubcategory->image_path = $subCategoryImagePath;
            // open image
            $img = Image::make($testSubcategory->image_path);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }
        $testSubcategory->save();
        return $testSubcategory;
    }

    /**
     *  return all test sub categories
     */
    protected static function getAllTestSubCategories(){
        $testSubCategories = [];
    	$subCategories = static::all();
        foreach($subCategories as $subcategory){
            $testSubCategories[$subcategory->test_category_id][$subcategory->id] = $subcategory;
        }
        return $testSubCategories;
    }

    /**
     *  return test sub categories associated with question by categoryId if less than in paper active date
     */
    protected static function getSubcategoriesByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->join('test_subjects', 'test_subjects.id', '=', 'questions.subject_id')
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return test sub categories  by categoryId
     */
    protected static function getSubcategoriesByCategoryIdForAdmin($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return subcategories by category for admin
     */
    protected static function getSubjectSubcategoriesByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_categories','test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    protected static function getTestSubCategoriesByRegisteredSubjectPapersByCategoryIdByUserId($categoryId,$userId){
        $categoryId = InputSanitise::inputInt($categoryId);
        $userId = InputSanitise::inputInt($userId);
        return DB::table('test_sub_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('register_papers', 'register_papers.test_subject_paper_id', 'test_subject_papers.id')
                ->where('register_papers.user_id', $userId)
                ->where('test_subject_papers.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')
                ->get();
    }

    protected static function getTestSubCategoriesAssociatedWithQuestion(){
        return DB::table('test_sub_categories')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->join('test_subjects', 'test_subjects.test_sub_category_id', '=', 'test_sub_categories.id')
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d'))
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    public function subjects(){
        return $this->hasMany(TestSubject::class, 'test_sub_category_id');
    }

    public function deleteSubCategoryImageFolder(){
        $subCategoryImageFolder = "testSubCategoryImages/".str_replace(' ', '_', $this->name);
        if(is_dir($subCategoryImageFolder)){
            InputSanitise::delFolder($subCategoryImageFolder);
        }
    }

    protected static function isTestSubCategoryExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $result = static::where('test_category_id', $categoryId)->where('name', $subCategoryName);
        if(!empty($subcategoryId)){
            $result->where('id', '!=', $subcategoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }
}
