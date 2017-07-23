<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\Question;
use App\Libraries\InputSanitise;
use DB;

class TestSubjectPaper extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'test_sub_category_id', 'test_subject_id', 'price','date_to_active','time'];

    /**
     *  add/update paper
     */
    protected static function addOrUpdateTestSubjectPaper( Request $request, $isUpdate=false){

        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $subcatId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputInt($request->get('price'));
        $dateToActive = $request->get('date_to_active');
        $time = strip_tags(trim($request->get('time')));

        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
                return Redirect::to('admin/managePaper');
            }
        } else{
            $paper = new static;
        }

        $paper->name = $paperName;
        $paper->test_category_id = $catId;
        $paper->test_sub_category_id = $subcatId;
        $paper->test_subject_id = $subjectId;
        $paper->price = $price;
        $paper->date_to_active = $dateToActive;
        $paper->time = $time;
        $paper->save();

        return $paper;
    }

    /**
     *  return papers by subjectId
     */
    protected static function getSubjectPapersBySubcatId($subcatId){
    	$testSubjectPapers = [];
    	$papers = [];
        $subcatId = InputSanitise::inputInt($subcatId);
    	$papers = DB::table('test_subject_papers')
    				->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
    				->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
    				->where('test_subjects.test_sub_category_id', $subcatId)
    				->select('test_subject_papers.*')
    				->get();
    	foreach($papers as $paper){
            $testSubjectPapers[$paper->test_subject_id][] = $paper;
        }
    	return $testSubjectPapers;
    }

    /**
     *  return all papers
     */
    protected static function getAllSubjectPapers(){
    	$testSubjectPapers = [];
    	$papers = [];
    	$papers = DB::table('test_subject_papers')
    				->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
    				->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subjects.test_sub_category_id')
    				->select('test_subject_papers.*')
    				->get();
    	foreach($papers as $paper){
            $testSubjectPapers[$paper->test_subject_id][] = $paper;
        }
    	return $testSubjectPapers;
    }

    /**
     *  return papers by categoryId by sub categoryId
     */
    protected static function getSubjectPapersByCatIdBySubCatId($catId, $subcatId){
        $testSubjectPapers = [];
        $papers = [];
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $papers = DB::table('test_subject_papers')
                    ->join('questions', 'questions.paper_id', '=', 'test_subject_papers.id')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->where('test_subject_papers.test_category_id', $catId)
                    ->where('test_subject_papers.test_sub_category_id', $subcatId)
                    ->select('test_subject_papers.id','test_subject_papers.*')
                    ->groupBy('test_subject_papers.id')
                    ->get();

        foreach($papers as $paper){
            $testSubjectPapers[$paper->test_subject_id][] = $paper;
        }
        return $testSubjectPapers;
    }

    /**
     *  return papers by subjectId
     */
    protected function getSubjectPapersBySubjectId($subjectId){
        $subjectId = InputSanitise::inputInt($subjectId);
        return DB::table('test_subject_papers')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->where('test_subjects.id', $subjectId)
                    ->select('test_subject_papers.*')
                    ->get();

    }

    protected static function getRegisteredSubjectPapersByUserId($userId){
        $testSubjectPapers = [];
        $papers = [];
        $testSubjectIds = [];
        $testPaperIds = [];
        $results = [];
        $papers = DB::table('test_subject_papers')
                    ->join('register_papers', 'register_papers.test_subject_paper_id', '=', 'test_subject_papers.id')
                    ->join('users', 'users.id', '=', 'register_papers.user_id')
                    ->where('users.id', $userId)
                    ->select('test_subject_papers.*')
                    ->get();
        if(false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->test_subject_id][] = $paper;
                $testSubjectIds[] = $paper->test_subject_id;
                $testPaperIds[] = $paper->id;
            }
            $results['papers'] = $testSubjectPapers;
            $results['paperIds'] = $testPaperIds;
            $results['subjectIds'] = array_unique($testSubjectIds);
        }
        return $results;
    }

    protected static function getRegisteredSubjectPapersByCatIdBySubCatIdByUserId($catId, $subcatId, $userId){
        $testSubjectPapers = [];
        $papers = DB::table('test_subject_papers')
                    ->join('register_papers', 'register_papers.test_subject_paper_id', '=', 'test_subject_papers.id')
                    ->where('register_papers.user_id', $userId)
                    ->where('test_subject_papers.test_category_id', $catId)
                    ->where('test_subject_papers.test_sub_category_id', $subcatId)
                    ->select('test_subject_papers.*')
                    ->get();
        if(false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->test_subject_id][] = $paper;
            }
        }
        return $testSubjectPapers;
    }


    protected static function getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($catId, $subcatId, $subjectId){
        return DB::table('test_subject_papers')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->where('test_subject_papers.test_category_id', $catId)
                    ->where('test_subject_papers.test_sub_category_id', $subcatId)
                    ->where('test_subject_papers.test_subject_id', $subjectId)
                    ->select('test_subject_papers.id','test_subject_papers.*')
                    ->groupBy('test_subject_papers.id')
                    ->get();
    }

    /**
     *  get category of paper
     */
    public function category(){
        return $this->belongsTo(TestCategory::class, 'test_category_id');
    }

    /**
     *  get category of paper
     */
    public function subcategory(){
        return $this->belongsTo(TestSubCategory::class, 'test_sub_category_id');
    }

    /**
     *  get category of paper
     */
    public function subject(){
        return $this->belongsTo(TestSubject::class, 'test_subject_id');
    }

    public function questions(){
        return $this->hasMany(Question::class, 'paper_id');
    }

    public function deleteRegisteredPaper(){
        $registerPapers = RegisterPaper::where('test_subject_paper_id', $this->id)->get();
        if(is_object($registerPapers) && false == $registerPapers->isEmpty()){
            foreach($registerPapers as $paper){
                $paper->delete();
            }
        }
    }
}
