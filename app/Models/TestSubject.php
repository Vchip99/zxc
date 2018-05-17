<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Libraries\InputSanitise;
use DB;

class TestSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'test_sub_category_id'];

    /**
     *  add/update subject
     */
    protected static function addOrUpdateSubject( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('name'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        if( $isUpdate && isset($subjectId)){
            $testSubject = static::find($subjectId);
            if(!is_object($testSubject)){
                return Redirect::to('admin/manageVkitCategory');
            }
        } else{
            $testSubject = new static;
        }
        $testSubject->name = $subjectName;
        $testSubject->test_category_id = $categoryId;
        $testSubject->test_sub_category_id = $subcategoryId;
        $testSubject->save();
        return $testSubject;
    }

    /**
     *  return subjects  associated with questions by categoryId by sub categoryId
     */
    protected static function getSubjectsByCatIdBySubcatid($catId, $subcatId){
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
    	$testSubjects = [];
    	$subjects = DB::table('test_subjects')
    					->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
    					->join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                        ->join('test_subject_papers', function($join){
                            $join->on('test_subject_papers.test_subject_id', '=', 'test_subjects.id');
                            $join->on('test_subject_papers.test_sub_category_id', '=', 'test_sub_categories.id');
                            $join->on('test_subject_papers.test_category_id', '=', 'test_categories.id');
                        })
    					->join('questions', 'questions.subject_id', 'test_subjects.id')
                        ->where('test_sub_categories.test_category_id', $catId)
    					->where('test_subjects.test_sub_category_id', $subcatId)
                        ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
    					->select('test_subjects.id','test_subjects.*')
                        ->groupBy('test_subjects.id')
    					->get();
    	foreach($subjects as $subject){
            $testSubjects[] = $subject;
        }
		return $testSubjects;
    }

    /**
     *  return subjects by categoryId by sub categoryId for admin
     */
    protected static function getSubjectsByCatIdBySubcatidForAdmin($catId, $subcatId){
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $testSubjects = [];
        $subjects = DB::table('test_subjects')
                        ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                        ->join('test_categories', 'test_categories.id', '=', 'test_sub_categories.test_category_id')
                        ->where('test_sub_categories.test_category_id', $catId)
                        ->where('test_subjects.test_sub_category_id', $subcatId)
                        ->select('test_subjects.id','test_subjects.*')
                        ->groupBy('test_subjects.id')
                        ->get();
        foreach($subjects as $subject){
            $testSubjects[] = $subject;
        }
        return $testSubjects;
    }

    /**
     *  return all subjects
     */
    protected static function getAllSubjects(){
    	$testSubjects = [];
    	$subjects = DB::table('test_subjects')
    					->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
    					->select('test_subjects.*')
    					->get();
    	foreach($subjects as $subject){
            $testSubjects[$subject->test_sub_category_id][] = $subject;
        }
    	return $testSubjects;
    }

    protected static function getRegisteredSubjectsByCatIdBySubcatIdByUserId($catId, $subcatId,$userId){
        return DB::table('test_subjects')
                        ->join('test_subject_papers', 'test_subject_papers.test_subject_id', '=', 'test_subjects.id')
                        ->join('register_papers', 'register_papers.test_subject_paper_id', '=', 'test_subject_papers.id')
                        ->where('register_papers.user_id', $userId)
                        ->where('test_subject_papers.test_category_id', $catId)
                        ->where('test_subject_papers.test_sub_category_id', $subcatId)
                        ->select('test_subjects.id', 'test_subjects.name')
                        ->groupBy('test_subjects.id')
                        ->get();
    }

    protected static function getSubjectsByIds($ids){
        return DB::table('test_subjects')->whereIn('id', $ids)
                        ->select('test_subjects.*')
                        ->get();
    }

    /**
     *  get category of subject
     */
    public function category(){
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    /**
     *  get category of subject
     */
    public function subcategory(){
        return $this->belongsTo(TestSubCategory::class, 'test_sub_category_id');
    }

    public function papers(){
        return $this->hasMany(TestSubjectPaper::class, 'test_subject_id');
    }

    protected static function isTestSubjectExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $result = static::where('test_category_id', $categoryId)->where('test_sub_category_id', $subcategoryId)->where('name', $subjectName);
        if(!empty($subjectId)){
            $result->where('id', '!=', $subjectId);
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
