<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth, DB;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\Question;
use App\Models\User;

class Score extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'category_id', 'subcat_id', 'subject_id', 'paper_id','right_answered', 'wrong_answered', 'unanswered', 'test_score', 'verification_code'];

    /**
     *  add score
     */
    protected static function addScore($userId, $result){
        $userId     = Auth::user()->id;

    	$score = new static();
    	$score->user_id = $userId;
    	$score->category_id = $result['category_id'];
    	$score->subcat_id = $result['subcat_id'];
        $score->subject_id = $result['subject_id'];
        $score->paper_id = $result['paper_id'];
    	$score->right_answered = $result['right_answered'];
    	$score->wrong_answered = $result['wrong_answered'];
    	$score->unanswered	   = $result['unanswered'];
        $score->test_score     = $result['marks'];
        $score->verification_code = $result['verification_code'];
    	$score->save();

    	return $score;
    }

    protected static function getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
        $paperIds = [];
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $scores = static::where('category_id', $catId)
                    ->where('subcat_id', $subcatId)
                    ->whereIn('paper_id', $testSubjectPaperIds)
                    ->where('user_id', $loginUser->id)->get();
            if(is_object($scores) && false == $scores->isEmpty()){
                foreach($scores as $score){
                    $paperIds[] = $score->paper_id;
                }
            }
        }
        return $paperIds;
    }

    protected static function getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$paperId,$subjectId,$userId){
        if(is_object(Auth::user())){
        return static::where('category_id', $categoryId)
                ->where('subcat_id', $subcatId)
                ->where('paper_id', $paperId)
                ->where('subject_id', $subjectId)
                ->where('user_id', $userId)
                ->first();
        }
        return;
    }

    protected static function getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$testScore,$userCollegeId){
        $result = DB::table('scores')->join('users', 'users.id', '=', 'scores.user_id')
                ->where('scores.category_id', $categoryId)
                ->where('scores.subcat_id', $subcatId)
                ->where('scores.paper_id', $paperId)
                ->where('scores.subject_id', $subjectId)
                ->where('scores.test_score', '>', DB::raw($testScore));
        if('all' != $userCollegeId && $userCollegeId > 0){
            $result->where('users.college_id', $userCollegeId);
        }
        return $result->count();
    }

    protected static function getTestUserScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId){
        $paperIds = [];
        if(count($testSubjectIds) > 0 && count($testSubjectPaperIds) > 0 && !empty($userId)) {
            $scores = static::whereIn('paper_id', $testSubjectPaperIds)
                    ->whereIn('subject_id', $testSubjectIds)
                    ->where('user_id', $userId)
                    ->get();

            if(is_object($scores) && false == $scores->isEmpty()){
                foreach($scores as $score){
                    $paperIds[] = $score->paper_id;
                }
            }
        }
        return $paperIds;
    }

    protected static function getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId, $userCollegeId){
        $result = static::join('users', 'users.id', '=', 'scores.user_id')
                ->where('scores.category_id', $categoryId)
                ->where('scores.subcat_id', $subcategoryId)
                ->where('scores.paper_id', $paperId)
                ->where('scores.subject_id', $subjectId);
        if('all' != $userCollegeId && $userCollegeId > 0){
            $result->where('users.college_id', $userCollegeId);
        }
        return $result->count();
    }

    public function subject(){
        return $this->belongsTo(TestSubject::class, 'subject_id');
    }

    public function paper(){
        return $this->belongsTo(TestSubjectPaper::class, 'paper_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rank($userCollegeId){
        $rank =$this->getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id,$this->test_score, $userCollegeId);
        $totalRank =$this->getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id,$userCollegeId);
        return ($rank + 1).'/'.$totalRank;
    }
    public function totalMarks(){
        $totalMarks = 0;
        $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id);
        if( is_object($questions) && false == $questions->isEmpty()){
            foreach($questions as $question){
                $totalMarks += $question->positive_marks;
            }
        }

        $percentage = round(($this->test_score/$totalMarks)*100,2);

        return ['totalMarks' => $totalMarks, 'percentage' => $percentage];
    }

    protected static function getScoreByCollegeIdByDeptIdByFilters($collegeId,$collegeDeptId,Request $request){
        $result = static::join('users', 'users.id', '=', 'scores.user_id')
                        ->join('test_subjects', 'test_subjects.id' , '=', 'scores.subject_id' )
                        ->join('test_subject_papers', 'test_subject_papers.id' , '=', 'scores.paper_id' );
        if($request->student > 0){
            $result->where('scores.user_id', $request->student);
        }
        if($collegeId > 0){
            $result->where('users.college_id', $collegeId);
        }
        if($request->department > 0){
            $result->where('users.college_dept_id', $collegeDeptId);
        }
        if($request->category > 0){
            $result->where('scores.category_id', $request->category);
        }
        if($request->subcategory > 0){
            $result->where('scores.subcat_id', $request->subcategory);
        }
        return $result->where('users.year', $request->year)
                ->select('scores.*', 'test_subjects.name as subject', 'test_subject_papers.name as paper')
                ->orderBy('test_score', 'desc')->get();
    }

    protected static function getUserTestResultsByCatBySubCat(Request $request){
        $result = static::join('users', 'users.id', '=', 'scores.user_id')
                        ->join('test_subjects', 'test_subjects.id' , '=', 'scores.subject_id' )
                        ->join('test_subject_papers', 'test_subject_papers.id' , '=', 'scores.paper_id' );
        if($request->user > 0){
            $result->where('scores.user_id', $request->user);
        }
        if($request->category > 0){
            $result->where('scores.category_id', $request->category);
        }
        if($request->subcategory > 0){
            $result->where('scores.subcat_id', $request->subcategory);
        }
        return $result->select('scores.*', 'test_subjects.name as subject', 'test_subject_papers.name as paper')
                ->orderBy('test_score', 'desc')->get();
    }

    protected static function deleteUserScoresByUserId($userId){
        $scores = static::where('user_id', $userId)->get();
        if(is_object($scores) && false == $scores->isEmpty()){
            foreach($scores as $score){
                $score->delete();
            }
        }
        return;
    }

    protected static function deleteUserScoresByPaperId($paperId){
        $scores = static::where('paper_id', $paperId)->get();
        if(is_object($scores) && false == $scores->isEmpty()){
            foreach($scores as $score){
                $score->delete();
            }
        }
        return;
    }

    protected static function getAllUsersResults(Request $request){
        $collegeId = $request->get('college');
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');

        $result = static::join('users', 'users.id', '=', 'scores.user_id');

        if($collegeId > 0){
            $result->where('users.college_id', $collegeId);
        } else if('other' == $collegeId){
            $result->where('users.college_id', $collegeId);
        }
        if($categoryId > 0 ) {
            $result->where('scores.category_id', $categoryId);
        }
        if($subcategoryId > 0 ) {
            $result->where('scores.subcat_id', $subcategoryId);
        }
        if( $subjectId > 0){
            $result->where('scores.subject_id', $subjectId);
        }
        if($paperId > 0){
            $result->where('scores.paper_id', $paperId);
        }
        return $result->select('scores.*')->orderBy('test_score', 'desc')->get();
    }

    protected static function getAllCompanyTestResults(){
        return static::join('test_categories', 'test_categories.id','=', 'scores.category_id')
            ->where('test_categories.category_for',0)->get();
    }
}