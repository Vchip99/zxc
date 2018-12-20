<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialTopic;
use DB,Auth;

class StudyMaterialSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_category_id', 'course_sub_category_id','name','admin_id','admin_approve'];

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
        $result = static::join('course_categories','course_categories.id','=','study_material_subjects.course_category_id')
            ->join('course_sub_categories','course_sub_categories.id','=','study_material_subjects.course_sub_category_id')
            ->join('admins','admins.id','=','study_material_subjects.admin_id');
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('admin_id', Auth::guard('admin')->user()->id);
        }
        return $result->select('study_material_subjects.*','course_categories.name as category','course_sub_categories.name as subcategory','admins.name as admin')->paginate();
    }

    protected static function getSubAdminSubjectsWithPagination(){
        return static::join('course_categories','course_categories.id','=','study_material_subjects.course_category_id')
            ->join('course_sub_categories','course_sub_categories.id','=','study_material_subjects.course_sub_category_id')
            ->join('admins','admins.id','=','study_material_subjects.admin_id')
            ->where('admin_id','!=', 1)
            ->select('study_material_subjects.*','course_categories.name as category','course_sub_categories.name as subcategory','admins.name as admin')->paginate();
    }

    protected static function getSubAdminSubjects($adminId){
        return static::join('course_categories','course_categories.id','=','study_material_subjects.course_category_id')
            ->join('course_sub_categories','course_sub_categories.id','=','study_material_subjects.course_sub_category_id')
            ->join('admins','admins.id','=','study_material_subjects.admin_id')
            ->where('admin_id',$adminId)
            ->select('study_material_subjects.*','course_categories.name as category','course_sub_categories.name as subcategory','admins.name as admin')->get();
    }

    protected static function changeSubAdminSubjectApproval($request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $subject = static::find($subjectId);
        if(is_object($subject)){
            if(1 == $subject->admin_approve){
                $subject->admin_approve = 0;
            } else {
                $subject->admin_approve = 1;
            }
            $subject->save();
            return 'true';
        }
        return 'false';
    }

    protected static function deleteSubAdminStudyMaterialSubjectsByAdminId($adminId){
        $subjects =  static::where('admin_id', $adminId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                StudyMaterialTopic::deleteStudyMaterialTopicsBySubjectId($subject->id);
                $subject->delete();
            }
        }
        return;
    }
}
