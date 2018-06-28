<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\QuestionBankCategory;
use DB, File;

class QuestionBankSubCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'question_bank_category_id'];

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
        $testSubcategory->question_bank_category_id = $catId;
        $testSubcategory->save();
        return $testSubcategory;
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(QuestionBankCategory::class, 'question_bank_category_id');
    }

    protected static function isQuestionBankSubCategoryExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $result = static::where('question_bank_category_id', $categoryId)->where('name', $subCategoryName);
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

    protected static function getSubcategoriesByCategoryId($categoryId){
        return static::where('question_bank_category_id', $categoryId)->get();
    }
}
