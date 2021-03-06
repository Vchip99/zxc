<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\TestSubject;
use App\Models\TestCategory;
use App\Models\CollegeCategory;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use DB,File,Auth;
use Intervention\Image\ImageManagerStatic as Image;

class TestSubCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'image_path','created_for','created_by','created_by_name','admin_approve','price'];

    /**
     *  add/update sub category
     */
    protected static function addOrUpdateSubCategory( Request $request, $isUpdate=false){
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $name = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputString($request->get('price'));

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
            if(in_array($request->file('image_path')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($testSubcategory->image_path);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
        }
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $testSubcategory->created_for = 0;
            $testSubcategory->created_by = Auth::user()->id;
            $testSubcategory->created_by_name = Auth::user()->name;
        } else {
            $testSubcategory->created_for = 1;
            $testSubcategory->created_by = Auth::guard('admin')->user()->id;
            $testSubcategory->created_by_name = Auth::guard('admin')->user()->name;
        }
        $testSubcategory->price = $price;
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
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return DB::table('test_sub_categories')
                ->join('test_subjects', 'test_subjects.test_sub_category_id', '=', 'test_sub_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path', 'test_sub_categories.price','test_sub_categories.test_category_id')
                ->groupBy('test_sub_categories.id')->get();
        } else {
            return DB::table('test_sub_categories')
                ->join('test_subjects', 'test_subjects.test_sub_category_id', '=', 'test_sub_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->where('test_sub_categories.admin_approve', 1)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path', 'test_sub_categories.price','test_sub_categories.test_category_id')
                ->groupBy('test_sub_categories.id')->get();
        }
    }

    /**
     *  return test sub categories  by categoryId
     */
    protected static function getSubcategoriesByCategoryIdForAdmin($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.created_for', 1)
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->where('test_sub_categories.created_by', Auth::guard('admin')->user()->id)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path', 'test_sub_categories.price')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return test sub categories  by categoryId
     */
    protected static function getSubcategoriesByCategoryIdForAdminForList($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.created_for', 1)
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return test sub categories  by categoryId
     */
    protected static function getCollegeSubcategoriesByCategoryIdForAdmin($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return static::join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.created_for', 0)
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return test sub categories  by categoryId
     */
    protected static function getCollegeSubCategoriesByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)
                ->where('test_sub_categories.created_for', 0)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return test sub categories  by collegeId
     */
    protected static function getSubcategoriesByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_sub_categories.*','college_categories.college_dept_id', 'college_categories.name as category')
                ->groupBy('test_sub_categories.id')->paginate();
    }

    /**
     *  return test sub categories
     */
    protected static function getSubcategoriesWithPagination(){
        $result = static::join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
            ->join('admins','admins.id','=','test_sub_categories.created_by')
            ->where('test_sub_categories.created_for', 1);
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('test_sub_categories.created_by', Auth::guard('admin')->user()->id);
        }
        return $result->select('test_sub_categories.*','test_categories.name as category','admins.name as admin')
            ->groupBy('test_sub_categories.id')->paginate();
    }

    /**
     *  return test sub categories  by collegeId
     */
    protected static function getSubcategoriesByCollegeIdByDeptId($collegeId,$deptId){
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        return static::join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_categories.college_id', $collegeId)
                ->where('test_categories.college_dept_id', $deptId)
                ->select('test_sub_categories.*')
                ->groupBy('test_sub_categories.id')->get();
    }

    /**
     *  return subcategories by category for admin
     */
    protected static function getSubjectSubcategoriesByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        return DB::table('test_sub_categories')
                ->join('test_categories','test_categories.id', '=', 'test_sub_categories.test_category_id')
                ->where('test_sub_categories.created_for', 1)
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

    /**
     *  get category of sub category
     */
    public function collegeCategory(){
        return $this->belongsTo(CollegeCategory::class, 'test_category_id');
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
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return DB::table('test_sub_categories')
                ->join('test_subjects', 'test_subjects.test_sub_category_id', '=', 'test_sub_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path', 'test_sub_categories.price','test_sub_categories.test_category_id')
                ->groupBy('test_sub_categories.id')->get();
        } else {
            return DB::table('test_sub_categories')
                ->join('test_subjects', 'test_subjects.test_sub_category_id', '=', 'test_sub_categories.id')
                ->join('test_subject_papers', 'test_subject_papers.test_sub_category_id', 'test_sub_categories.id')
                ->join('questions', 'questions.subcat_id', 'test_sub_categories.id')
                ->join('test_categories', function($join){
                    $join->on('test_categories.id', '=', 'test_sub_categories.test_category_id');
                    $join->on('test_categories.id', '=', 'questions.category_id');
                })
                ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                ->where('test_categories.category_for', 1)
                ->where('test_sub_categories.created_for', 1)
                ->where('test_sub_categories.admin_approve', 1)
                ->select('test_sub_categories.id', 'test_sub_categories.name', 'test_sub_categories.image_path', 'test_sub_categories.price','test_sub_categories.test_category_id')
                ->groupBy('test_sub_categories.id')->get();
        }
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
        $result = static::join('test_categories', 'test_categories.id','=','test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)->where('test_sub_categories.name', $subCategoryName);
        if(!empty($subcategoryId)){
            $result->where('test_sub_categories.id', '!=', $subcategoryId);
        }
        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)->where('test_sub_categories.name', $subCategoryName);
            if(!empty($subcategoryId)){
                $result->where('test_sub_categories.id', '!=', $subcategoryId);
            }
            $result->where('test_sub_categories.created_for', 0)->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('test_categories', 'test_categories.id','=','test_sub_categories.test_category_id')
                ->where('test_sub_categories.test_category_id', $categoryId)->where('test_sub_categories.name', $subCategoryName)->where('test_sub_categories.created_for', 1);
            if(!empty($subcategoryId)){
                $result->where('test_sub_categories.id', '!=', $subcategoryId);
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
     *  return sub admin sub categories
     */
    protected static function getSubAdminSubcategoriesWithPagination(){
        return static::join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
            ->join('admins','admins.id','=', 'test_sub_categories.created_by')
            ->where('test_sub_categories.created_for', 1)
            ->where('test_sub_categories.created_by','!=', 1)
            ->select('test_sub_categories.*','test_categories.name as category','admins.name as admin')
            ->groupBy('test_sub_categories.id')->paginate();
    }

    /**
     *  return sub admin sub categories
     */
    protected static function getSubAdminSubCategories($adminId){
        return static::join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
            ->join('admins','admins.id','=', 'test_sub_categories.created_by')
            ->where('test_sub_categories.created_for', 1)
            ->where('test_sub_categories.created_by', $adminId)
            ->select('test_sub_categories.*','test_categories.name as category','admins.name as admin')
            ->groupBy('test_sub_categories.id')->get();
    }

    protected static function changeSubAdminSubCategoryApproval($request){
        $subcategoryId = $request->get('sub_category_id');
        $subcategory = static::find($subcategoryId);
        if(is_object($subcategory)){
            if(1 == $subcategory->admin_approve){
                $subcategory->admin_approve = 0;
            } else {
                $subcategory->admin_approve = 1;
            }
            $subcategory->save();
            return 'true';
        }
        return 'false';
    }

    protected static function deleteSubAdminSubCategoriesAndSubjectsAndPapersAndQuestionsByAdminId($adminId){
        $subCategories = static::where('created_by', $adminId)->where('created_for', 1)->get();
        if(is_object($subCategories) && false == $subCategories->isEmpty()){
            foreach($subCategories as $testSubcategory){
                if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                    foreach($testSubcategory->subjects as $subject){
                        if(true == is_object($subject->papers) && false == $subject->papers->isEmpty()){
                            foreach($subject->papers as $paper){
                                if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                    foreach($paper->questions as $question){
                                        UserSolution::deleteUserSolutionsByQuestionId($question->id);
                                        $question->delete();
                                    }
                                }
                                Score::deleteUserScoresByPaperId($paper->id);
                                PaperSection::deletePaperSectionsByPaperId($paper->id);
                                $paper->deleteRegisteredPaper();
                                $paper->delete();
                            }
                        }
                        $subject->delete();
                    }
                }
                $testSubcategory->deleteSubCategoryImageFolder();
                $testSubcategory->delete();
            }
        }
    }
}
