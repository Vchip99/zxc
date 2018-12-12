<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use DB,Auth;

class StudyMaterialSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_category_id', 'course_sub_category_id','name','admin_id'];

    /**
     *  add/update subject
     */
    protected static function addOrUpdateStudyMaterialSubject( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
                return 'false';
            }
        } else {
            $subject = new static;
        }
        $subject->name = $subjectName;
        $subject->course_category_id = $categoryId;
        $subject->course_sub_category_id = $subcategoryId;
        $subject->admin_id = Auth::guard('admin')->user()->id;
        $subject->save();
        return $subject;
    }

    protected static function isStudyMaterialSubjectExist($request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        $result = static::join('course_categories', 'course_categories.id','=','study_material_subjects.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_subjects.course_sub_category_id')
            ->where('study_material_subjects.course_category_id', $categoryId)
            ->where('study_material_subjects.course_sub_category_id', $subcategoryId)
            ->where('study_material_subjects.name', $subjectName);
        if(!empty($subjectId)){
            $result->where('study_material_subjects.id', '!=', $subjectId);
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
     *  get category
     */
    public function category(){
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     *  get sub category
     */
    public function subcategory(){
        return $this->belongsTo(CourseSubCategory::class, 'course_sub_category_id');
    }

    protected static function getStudyMaterialSubjectsByCategoryIdBySubCategoryId($categoryId,$subcategoryId){
    	return static::where('course_category_id',$categoryId)
            ->where('course_sub_category_id',$subcategoryId)
            ->where('admin_id',Auth::guard('admin')->user()->id)
            ->get();
    }

    protected static function getStudyMaterialSubjectsByCategoryIdBySubCategoryIdForList($categoryId,$subcategoryId){
        return static::where('course_category_id',$categoryId)
            ->where('course_sub_category_id',$subcategoryId)
            ->get();
    }

    protected static function deleteStudyMaterialSubjectsBySubCategoryId($subCategoryId){
        $subjects =  static::where('course_sub_category_id', $subCategoryId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $subject->delete();
            }
        }
        return;
    }

    protected static function deleteStudyMaterialSubjectsByCategoryId($categoryId){
        $subjects =  static::where('course_category_id', $categoryId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $subject->delete();
            }
        }
        return;
    }

    protected static function getStudyMaterialSubjectsWithPagination(){
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            return static::where('admin_id',Auth::guard('admin')->user()->id)->paginate();
        } else {
            return static::paginate();
        }
    }
}
