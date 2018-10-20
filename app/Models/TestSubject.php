<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\User;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use App\Libraries\InputSanitise;
use DB,Auth;

class TestSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'test_sub_category_id','created_by'];

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
                return 'false';
            }
        } else {
            $testSubject = new static;
        }
        $testSubject->name = $subjectName;
        $testSubject->test_category_id = $categoryId;
        $testSubject->test_sub_category_id = $subcategoryId;
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $testSubject->created_by = Auth::user()->id;
        }
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
                        ->where('test_sub_categories.created_for', 1)
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
     *  return subjects  associated with questions by categoryId by sub categoryId
     */
    protected static function getCollegeSubjectsByCatIdBySubcatid($catId, $subcatId){
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $testSubjects = [];
        $subjects = static::join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                        ->join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                        ->join('test_subject_papers', function($join){
                            $join->on('test_subject_papers.test_subject_id', '=', 'test_subjects.id');
                            $join->on('test_subject_papers.test_sub_category_id', '=', 'test_sub_categories.id');
                            $join->on('test_subject_papers.test_category_id', '=', 'college_categories.id');
                        })
                        ->join('questions', 'questions.subject_id', 'test_subjects.id')
                        ->where('test_sub_categories.test_category_id', $catId)
                        ->where('test_subjects.test_sub_category_id', $subcatId)
                        ->where('test_sub_categories.created_for', 0)
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
                        ->where('test_sub_categories.created_for', 1)
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
     *  return subjects by categoryId by sub categoryId for admin
     */
    protected static function getCollegeSubjectsByCatIdBySubcatidForAdmin($catId, $subcatId){
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $testSubjects = [];
        $subjects = DB::table('test_subjects')
                        ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                        ->join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                        ->where('test_sub_categories.created_for', 0)
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
     *  return subjects by categoryId by sub categoryId for admin
     */
    protected static function getCollegeSubjectsByCatIdBySubcatIdByUser($catId, $subcatId){
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $loginUser = Auth::guard('web')->user();
        $testSubjects = [];
        $subjects = DB::table('test_subjects')
                        ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                        ->join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
                        ->where('test_sub_categories.created_for', 0)
                        ->where('test_sub_categories.test_category_id', $catId)
                        ->where('test_subjects.test_sub_category_id', $subcatId)
                        ->where('test_subjects.created_by', $loginUser->id)
                        ->select('test_subjects.id','test_subjects.*')
                        ->groupBy('test_subjects.id')
                        ->get();
        foreach($subjects as $subject){
            $testSubjects[] = $subject;
        }
        return $testSubjects;
    }

    // /**
    //  *  return subjects by categoryId by sub categoryId for admin
    //  */
    // protected static function getCollegeSubjectsByCatIdBySubcatIdByUserType($catId, $subcatId){
    //     $catId = InputSanitise::inputInt($catId);
    //     $subcatId = InputSanitise::inputInt($subcatId);
    //     $loginUser = Auth::guard('web')->user();
    //     $testSubjects = [];
    //     $subjects = DB::table('test_subjects')
    //                     ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
    //                     ->join('college_categories', 'college_categories.id', '=', 'test_sub_categories.test_category_id')
    //                     ->where('test_sub_categories.created_for', 0)
    //                     ->where('test_sub_categories.test_category_id', $catId)
    //                     ->where('test_subjects.test_sub_category_id', $subcatId)
    //                     ->where('test_subjects.created_by', $loginUser->id)
    //                     ->select('test_subjects.id','test_subjects.*')
    //                     ->groupBy('test_subjects.id')
    //                     ->get();
    //     foreach($subjects as $subject){
    //         $testSubjects[] = $subject;
    //     }
    //     return $testSubjects;
    // }

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
     *  get category of sub category
     */
    public function collegeCategory(){
        return $this->belongsTo(CollegeCategory::class, 'test_category_id');
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

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id','=','test_subjects.test_sub_category_id')
                ->where('test_subjects.test_category_id', $categoryId)
                ->where('test_subjects.test_sub_category_id', $subcategoryId)->where('test_subjects.name', $subjectName);

            if(!empty($subjectId)){
                $result->where('test_subjects.id', '!=', $subjectId);
            }
            $result->where('test_sub_categories.created_for', 0)->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('test_categories', 'test_categories.id','=','test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id','=','test_subjects.test_sub_category_id')
                ->where('test_subjects.test_category_id', $categoryId)->where('test_subjects.test_sub_category_id', $subcategoryId)->where('test_subjects.name', $subjectName);

            if(!empty($subjectId)){
                $result->where('test_subjects.id', '!=', $subjectId);
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
     *  return test subjects  by collegeId
     */
    protected static function getSubjectsByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $loginUser = Auth::user();
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('users','users.id','=','test_subjects.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        if(User::TNP == $loginUser->user_type){
            $result->where('test_subjects.created_by', $loginUser->id);
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subjects.*','college_categories.college_dept_id','college_categories.name as category','test_sub_categories.name as subcategory','users.name as user')
                ->groupBy('test_subjects.id')->paginate();
    }

    protected static function getSubjectsByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','test_subjects.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('test_subjects.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('test_subjects.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subjects.*','college_categories.college_dept_id','college_categories.name as category','test_sub_categories.name as subcategory','users.name as user')
                ->groupBy('test_subjects.id')->paginate();
    }

    protected static function getSubjectsByCollegeIdByAssignedDepts($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','test_subjects.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('test_subjects.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('test_subjects.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subjects.*','college_categories.college_dept_id','college_categories.name as category','test_sub_categories.name as subcategory')
                ->groupBy('test_subjects.id')->get();
    }

    protected static function getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($collegeId,$categoryId,$subcategoryId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','test_subjects.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                ->where('users.college_id', $collegeId)
                ->where('test_subjects.test_category_id', $categoryId)
                ->where('test_subjects.test_sub_category_id', $subcategoryId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('test_subjects.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('test_subjects.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subjects.*','college_categories.college_dept_id','college_categories.name as category','test_sub_categories.name as subcategory')
                ->groupBy('test_subjects.id')->get();
    }

    /**
     *  return test subjects
     */
    protected static function getSubjectsWithPagination(){
        return static::join('test_categories', 'test_categories.id', '=', 'test_subjects.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
                ->where('test_sub_categories.created_for', 1)->select('test_subjects.*','test_categories.name as category','test_sub_categories.name as subcategory')
                ->groupBy('test_subjects.id')->paginate();
    }

    protected static function deleteCollegeSubjectAndPapersByUserId($userId){
        $subjects =  static::where('created_by', $userId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $testSubject){
                if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                    foreach($testSubject->papers as $paper){
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
                $testSubject->delete();
            }
        }
        return;
    }
}
