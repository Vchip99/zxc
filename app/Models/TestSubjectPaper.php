<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\Question;
use App\Models\PaperSection;
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
    protected $fillable = ['name', 'test_category_id', 'test_sub_category_id', 'test_subject_id', 'price','date_to_active','time', 'date_to_inactive', 'show_calculator', 'show_solution', 'option_count', 'time_out_by'];

    /**
     *  add/update paper
     */
    protected static function addOrUpdateTestSubjectPaper( Request $request, $isUpdate=false){
        $sessions = [];
        $addPaperSessions = [];
        $updatePaperSessions = [];
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $subcatId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputInt($request->get('price'));
        $dateToActive = $request->get('date_to_active');
        $dateToInactive = $request->get('date_to_inactive');
        $time = strip_tags(trim($request->get('time')));
        $showCalculator = InputSanitise::inputInt($request->get('show_calculator'));
        $showSolution = InputSanitise::inputInt($request->get('show_solution'));
        $optionCount = InputSanitise::inputInt($request->get('option_count'));
        $timeOutBy = $request->get('time_out_by');

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
        $paper->date_to_inactive = $dateToInactive;
        $paper->show_calculator = $showCalculator;
        $paper->show_solution = $showSolution;
        $paper->option_count = $optionCount;
        $paper->time_out_by = $timeOutBy;
        $paper->time = $time;
        $paper->save();

        if( $isUpdate && isset($paperId)){
            $allSessions = $request->except('_token','_method', 'paper_id', 'category', 'subcategory','subject', 'name', 'price', 'date_to_active', 'date_to_inactive', 'time', 'show_calculator', 'show_solution', 'option_count', 'time_out_by', 'all_session_count');
            if(count($allSessions) > 0){
                foreach($allSessions as $index => $paperSession){
                    $explodes = explode('_', $index);
                    if('new' == $explodes[0]){
                        $addPaperSessions[] = $explodes[1];
                    } else {
                        $updatePaperSessions[$explodes[1]][$explodes[0]] = $paperSession;
                    }
                }
            }

            if(count($updatePaperSessions) > 0){
                $allSessions = PaperSection::where('test_subject_paper_id', $paperId)->get();
                if(is_object($allSessions) && false == $allSessions->isEmpty()){
                    // update or delete
                    foreach($allSessions as $paperSession){
                        if(false == in_array($paperSession->id, $addPaperSessions)){
                            if(isset($updatePaperSessions[$paperSession->id])){
                                $paperSession->name = $updatePaperSessions[$paperSession->id]['session'];
                                $paperSession->duration = $updatePaperSessions[$paperSession->id]['duration'];
                                $paperSession->test_category_id = $catId;
                                $paperSession->test_sub_category_id =$subcatId;
                                $paperSession->test_subject_id = $subjectId;
                                $paperSession->test_subject_paper_id = $paperId;
                                $paperSession->save();
                            } else {
                                $paperSession->delete();
                            }
                        }
                    }
                }
                // add new
                foreach($updatePaperSessions as $index => $updatePaperSession){
                    if(true == in_array($index, $addPaperSessions)){
                        $paperSession = new PaperSection;
                        $paperSession->name = $updatePaperSession['session'];
                        $paperSession->duration = $updatePaperSession['duration'];
                        $paperSession->test_category_id = $catId;
                        $paperSession->test_sub_category_id =$subcatId;
                        $paperSession->test_subject_id = $subjectId;
                        $paperSession->test_subject_paper_id = $paperId;
                        $paperSession->save();
                    }
                }
            }
        } else {
            $allSessionCount = InputSanitise::inputInt($request->get('all_session_count'));
            if($allSessionCount > 0){
                for($i=1; $i<=$allSessionCount; $i++){
                    $session = $request->get('session_'.$i);
                    $duration = $request->get('duration_'.$i);
                    if(!empty($session)){
                        $sessions[] = [
                                    'name' => $session,
                                    'duration' => $duration,
                                    'test_category_id' => $catId,
                                    'test_sub_category_id' => $subcatId,
                                    'test_subject_id' => $subjectId,
                                    'test_subject_paper_id' => $paper->id
                                ];
                    }
                }
                if(count($sessions) > 0){
                    DB::table('paper_sections')->insert($sessions);
                }
            }
        }
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
                    ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d'))
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

        $allSessions = PaperSection::where('test_subject_paper_id', $this->id)->get();
        if(is_object($allSessions) && false == $allSessions->isEmpty()){
            foreach($allSessions as $session){
                $session->delete();
            }
        }
    }
}
