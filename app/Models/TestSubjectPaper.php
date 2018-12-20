<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\Question;
use App\Models\PaperSection;
use App\Models\User;
use App\Libraries\InputSanitise;
use DB,Auth;

class TestSubjectPaper extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'test_category_id', 'test_sub_category_id', 'test_subject_id', 'price','date_to_active','time', 'date_to_inactive', 'show_calculator', 'show_solution', 'option_count', 'time_out_by', 'verification_code_count', 'verification_code','paper_pattern'];

    /**
     *  add/update paper
     */
    protected static function addOrUpdateTestSubjectPaper( Request $request, $isUpdate=false){
        $sessions = [];
        $addPaperSessions = [];
        $updatePaperSessions = [];
        $verificationCodeString = '';
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
        $isVerificationCode = InputSanitise::inputInt($request->get('is_verification_code'));
        $verificationCodeCount = InputSanitise::inputInt($request->get('verification_code_count'));
        $addVerificationCodeCount = InputSanitise::inputInt($request->get('add_verification_code_count'));
        $timeOutBy = $request->get('time_out_by');
        $paperPattern = $request->get('paper_pattern');

        if($isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
                return 'false';
            }
            if(1 == $isVerificationCode){
                if($addVerificationCodeCount > 0){
                    for($i=1; $i <= $addVerificationCodeCount; $i++) {
                        $code = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
                        if(empty($paper->verification_code)){
                            $paper->verification_code = $code;
                        } else {
                            $paper->verification_code = trim($paper->verification_code.','.$code);
                        }
                    }
                    $paper->verification_code_count = $paper->verification_code_count + $addVerificationCodeCount;
                    $paper->verification_code = $paper->verification_code;
                }
            } else {
                $paper->verification_code_count = '';
                $paper->verification_code = '';
            }
        } else {
            $paper = new static;

            if($verificationCodeCount > 0){
                for($i=1; $i <= $verificationCodeCount; $i++) {
                    $code = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
                    if(1 == $i){
                        $verificationCodeString = $code;
                    } else {
                        $verificationCodeString .= ','.$code;
                    }
                }
            }
            $paper->verification_code_count = $verificationCodeCount;
            $paper->verification_code = $verificationCodeString;
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
        $paper->paper_pattern = $paperPattern;
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
                            if(isset($updatePaperSessions[$paperSession->id]) && !empty($updatePaperSessions[$paperSession->id]['session'])){
                                if(isset($updatePaperSessions[$paperSession->id])){
                                    $paperSession->name = str_replace(" ", "_", $updatePaperSessions[$paperSession->id]['session']);
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
                }
                // add new
                foreach($updatePaperSessions as $index => $updatePaperSession){
                    if(true == in_array($index, $addPaperSessions)){
                        if(!empty($updatePaperSession['session'])){
                            $paperSession = new PaperSection;
                            $paperSession->name = str_replace(" ", "_", $updatePaperSession['session']);
                            $paperSession->duration = $updatePaperSession['duration'];
                            $paperSession->test_category_id = $catId;
                            $paperSession->test_sub_category_id =$subcatId;
                            $paperSession->test_subject_id = $subjectId;
                            $paperSession->test_subject_paper_id = $paperId;
                            $paperSession->save();
                        }
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
                                    'name' => str_replace(" ", "_", $session),
                                    'duration' => $duration,
                                    'test_category_id' => $catId,
                                    'test_sub_category_id' => $subcatId,
                                    'test_subject_id' => $subjectId,
                                    'test_subject_paper_id' => $paper->id,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
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
                    ->where('test_sub_categories.created_for', 1)
                    ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                    ->select('test_subject_papers.id','test_subject_papers.*')
                    ->groupBy('test_subject_papers.id')
                    ->get();

        foreach($papers as $paper){
            $testSubjectPapers[$paper->test_subject_id][] = $paper;
        }
        return $testSubjectPapers;
    }

     /**
     *  return papers by categoryId by sub categoryId
     */
    protected static function getCollegeSubjectPapersByCatIdBySubCatId($catId, $subcatId){
        $testSubjectPapers = [];
        $papers = [];
        $catId = InputSanitise::inputInt($catId);
        $subcatId = InputSanitise::inputInt($subcatId);
        $papers = DB::table('test_subject_papers')
                    ->join('questions', 'questions.paper_id', '=', 'test_subject_papers.id')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->where('test_subject_papers.test_category_id', $catId)
                    ->where('test_subject_papers.test_sub_category_id', $subcatId)
                    ->where('test_sub_categories.created_for', 0)
                    ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
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
                    ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('questions', 'questions.paper_id', '=', 'test_subject_papers.id')
                    // ->join('register_papers', 'register_papers.test_subject_paper_id', '=', 'test_subject_papers.id')
                    // ->join('users', 'users.id', '=', 'register_papers.user_id')
                    ->where('test_sub_categories.created_for', 1)
                    ->where('test_categories.category_for', 1)
                    ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                    // ->where('register_papers.user_id', $userId)
                    ->select('test_subject_papers.*')
                    ->groupBy('test_subject_papers.id')
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

    protected static function getSubjectPapersByCollegeIdByCollegeDeptId($collegeId,$collegeDeptId=NULL){
        $testSubjectPapers = [];
        $papers = [];
        $testSubjectIds = [];
        $testPaperIds = [];
        $results = [];
        $papers = DB::table('test_subject_papers')
                    ->join('questions', 'questions.paper_id', '=', 'test_subject_papers.id')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->where('test_sub_categories.created_for', 0)
                    ->where('college_categories.college_id', $collegeId)
                    ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
                    ->select('test_subject_papers.*')->groupBy('test_subject_papers.id')
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

    // protected static function getSubjectPapers(){
    //     $testSubjectPapers = [];
    //     $papers = [];
    //     $testSubjectIds = [];
    //     $testPaperIds = [];
    //     $results = [];
    //     $papers = DB::table('test_subject_papers')
    //                 ->join('questions', 'questions.paper_id', '=', 'test_subject_papers.id')
    //                 ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
    //                 ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
    //                 ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
    //                 ->where('test_sub_categories.created_for', 1)
    //                 ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
    //                 ->select('test_subject_papers.*')->groupBy('test_subject_papers.id')
    //                 ->get();
    //     if(false == $papers->isEmpty()){
    //         foreach($papers as $paper){
    //             $testSubjectPapers[$paper->test_subject_id][] = $paper;
    //             $testSubjectIds[] = $paper->test_subject_id;
    //             $testPaperIds[] = $paper->id;
    //         }
    //         $results['papers'] = $testSubjectPapers;
    //         $results['paperIds'] = $testPaperIds;
    //         $results['subjectIds'] = array_unique($testSubjectIds);
    //     }
    //     return $results;
    // }

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
                    ->where('test_sub_categories.created_for', 1)
                    ->where('test_subject_papers.test_category_id', $catId)
                    ->where('test_subject_papers.test_sub_category_id', $subcatId)
                    ->where('test_subject_papers.test_subject_id', $subjectId)
                    ->select('test_subject_papers.id','test_subject_papers.*')
                    ->groupBy('test_subject_papers.id')
                    ->get();
    }

    protected static function getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($catId, $subcatId, $subjectId){
        return DB::table('test_subject_papers')
                    ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                    ->join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                    ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                    ->where('test_sub_categories.created_for', 0)
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

    // /**
    //  *  get category of sub category
    //  */
    // public function collegeCategory(){
    //     return $this->belongsTo(CollegeCategory::class, 'test_category_id');
    // }

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

    /**
     *  get user
     */
    public function getUser(){
        $user = User::find($this->user_id);
        if(is_object($user)){
            return $user->name;
        }
        return;
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

    protected static function isTestPaperExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('paper'));
        $paperId = InputSanitise::inputInt($request->get('paper_id'));

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id','=','test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id','=','test_subject_papers.test_subject_id')
                ->where('test_subject_papers.test_category_id', $categoryId)->where('test_subject_papers.test_sub_category_id', $subcategoryId)->where('test_subject_papers.test_subject_id', $subjectId)->where('test_subject_papers.name', $paperName);

            if(!empty($paperId)){
                $result->where('test_subject_papers.id', '!=', $paperId);
            }
            $result->where('test_sub_categories.created_for', 0)->where('college_categories.college_id',$loginUser->college_id);
        } else {
            $result = static::join('test_categories', 'test_categories.id','=','test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id','=','test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id','=','test_subject_papers.test_subject_id')
                ->where('test_subject_papers.test_category_id', $categoryId)->where('test_subject_papers.test_sub_category_id', $subcategoryId)->where('test_subject_papers.test_subject_id', $subjectId)->where('test_subject_papers.name', $paperName)->where('test_sub_categories.created_for', 1);

            if(!empty($paperId)){
                $result->where('test_subject_papers.id', '!=', $paperId);
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

    protected static function getFirstCompanyTestPaperAssociatedWithQuestion(){
        return DB::table('test_subject_papers')
            ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
            ->join('questions', function($join){
                $join->on('questions.category_id', '=', 'test_categories.id');
                $join->on('questions.subcat_id', '=', 'test_sub_categories.id');
                $join->on('questions.subject_id', '=', 'test_subjects.id');
                $join->on('questions.paper_id', '=', 'test_subject_papers.id');
            })
            ->where('test_categories.category_for', 0)
            ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
            ->select('test_subject_papers.id','test_subject_papers.name')
            ->groupBy('test_subject_papers.id')->first();
    }
    protected static function getAllCompanyTestPaperAssociatedWithQuestion(){
        return DB::table('test_subject_papers')
            ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
            ->join('questions', function($join){
                $join->on('questions.category_id', '=', 'test_categories.id');
                $join->on('questions.subcat_id', '=', 'test_sub_categories.id');
                $join->on('questions.subject_id', '=', 'test_subjects.id');
                $join->on('questions.paper_id', '=', 'test_subject_papers.id');
            })
            ->where('test_categories.category_for', 0)
            ->select('test_subject_papers.id','test_subject_papers.name','test_subject_papers.date_to_active','test_subject_papers.date_to_inactive')
            ->groupBy('test_subject_papers.id')->get();
    }

    protected static function getTestPaperAssociatedWithQuestionById($id){
        return DB::table('test_subject_papers')
            ->join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
            ->join('questions', function($join){
                $join->on('questions.category_id', '=', 'test_categories.id');
                $join->on('questions.subcat_id', '=', 'test_sub_categories.id');
                $join->on('questions.subject_id', '=', 'test_subjects.id');
                $join->on('questions.paper_id', '=', 'test_subject_papers.id');
            })
            ->where('test_categories.category_for', 0)
            ->where('test_subject_papers.date_to_inactive', '>=', date('Y-m-d H:i:s'))
            ->where('test_subject_papers.id', $id)
            ->select('test_subject_papers.id','test_subject_papers.*')
            ->groupBy('test_subject_papers.id')->first();
    }

    /**
     *  return test papers  by collegeId by dept
     */
    protected static function getPapersByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $loginUser = Auth::user();
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->join('users','users.id','=','test_subjects.created_by')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        if(User::TNP == $loginUser->user_type){
            $result->where('test_subjects.created_by', $loginUser->id);
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subject_papers.*','college_categories.college_dept_id','college_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','test_subjects.created_by','users.name as user')
                ->groupBy('test_subject_papers.id')->paginate();
    }

    protected static function getPapersByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->join('users','users.id','=','test_subjects.created_by')
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
        return $result->where('test_sub_categories.created_for', 0)->select('test_subject_papers.*','college_categories.college_dept_id','college_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','test_subjects.created_by','users.name as user')
                ->groupBy('test_subject_papers.id')->paginate();
    }

    /**
     *  return test papers  by collegeId by dept
     */
    protected static function getPapersByUserIdByCollegeIdByDeptId($userId,$collegeId,$deptId=NULL){
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->where('college_categories.college_id', $collegeId)
                ->where('test_subjects.created_by', $userId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);
        }
        return $result->where('test_sub_categories.created_for', 0)->select('test_subject_papers.id','test_subject_papers.name','college_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject')
                ->groupBy('test_subject_papers.id')->get();
    }

    /**
     *  return test papers
     */
    protected static function getPapersWithPagination(){
        $result = static::join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->join('admins','admins.id','=','test_sub_categories.created_by')
                ->where('test_sub_categories.created_for', 1);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('test_sub_categories.created_by', Auth::guard('admin')->user()->id);
        }
        return $result->select('test_subject_papers.*','test_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','test_sub_categories.created_by as subcategory_by','admins.name as admin')
                ->groupBy('test_subject_papers.id')->paginate();
    }

    /**
     *  return purchased test papers
     */
    protected static function getPurchasedPapers($adminId = NULL){
        $result = static::join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->join('register_papers','register_papers.test_subject_paper_id','=','test_subject_papers.id')
                ->where('test_sub_categories.created_for', 1)
                ->where('register_papers.price','>', 0);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('test_sub_categories.created_by', Auth::guard('admin')->user()->id);
        } else {
            if($adminId > 0){
                $result->where('test_sub_categories.created_by', $adminId);
            }
        }
        return $result->select('register_papers.id','test_subject_papers.name','test_subject_papers.price','test_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','register_papers.user_id','register_papers.updated_at','test_sub_categories.created_by')
                ->groupBy('register_papers.id')->get();
    }

    /**
     *  return purchased test paper by id
     */
    protected static function getPurchasedPaperById($paperId){
        $result = static::join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
                ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
                ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
                ->join('register_papers','register_papers.test_subject_paper_id','=','test_subject_papers.id')
                ->where('test_sub_categories.created_for', 1)
                ->where('register_papers.price','>', 0)
                ->whereNotNull('register_papers.payment_id')
                ->whereNotNull('register_papers.payment_request_id')
                ->where('register_papers.id',$paperId);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('test_sub_categories.created_by', Auth::guard('admin')->user()->id);
        }
        return $result->select('register_papers.id','test_subject_papers.name','test_subject_papers.price','register_papers.user_id','register_papers.updated_at')
                ->first();
    }

    protected static function getSubAdminPapersWithPagination(){
        return static::join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
            ->join('admins','admins.id','=', 'test_sub_categories.created_by')
            ->where('test_sub_categories.created_for', 1)
            ->where('test_sub_categories.created_by','!=', 1)
            ->select('test_subject_papers.*','test_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','test_sub_categories.created_by as subcategory_by','admins.name as admin')
            ->groupBy('test_subject_papers.id')->paginate();
    }

    protected static function getSubAdminPapers($adminId){
        return static::join('test_categories', 'test_categories.id', '=', 'test_subject_papers.test_category_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'test_subject_papers.test_sub_category_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'test_subject_papers.test_subject_id')
            ->join('admins','admins.id','=', 'test_sub_categories.created_by')
            ->where('test_sub_categories.created_for', 1)
            ->where('test_sub_categories.created_by', $adminId)
            ->select('test_subject_papers.*','test_categories.name as category', 'test_sub_categories.name as subcategory','test_subjects.name as subject','test_sub_categories.created_by as subcategory_by','admins.name as admin')
            ->groupBy('test_subject_papers.id')->get();
    }
}
