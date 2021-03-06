<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestQuestion;
use App\Models\RegisterClientOnlinePaper;
use App\Models\ClientOnlinePaperSection;

class ClientOnlineTestSubjectPaper extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'subject_id', 'date_to_active', 'time','client_id', 'date_to_inactive', 'show_calculator', 'show_solution', 'option_count', 'time_out_by', 'is_free', 'allowed_unauthorised_user','created_by','paper_pattern'];

    /**
     *  add/update paper
     */
    protected static function addOrUpdateOnlineTestSubjectPaper( Request $request, $isUpdate=false){
        $sessions = [];
        $addPaperSessions = [];
        $updatePaperSessions = [];
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $subcatId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('name'));
        $dateToActive = $request->get('date_to_active');
        $dateToInactive = $request->get('date_to_inactive');
        $time = strip_tags(trim($request->get('time')));
        $showCalculator = InputSanitise::inputInt($request->get('show_calculator'));
        $showSolution = InputSanitise::inputInt($request->get('show_solution'));
        $optionCount = InputSanitise::inputInt($request->get('option_count'));
        $isFree = InputSanitise::inputInt($request->get('is_free'));
        $unauthorisedUser = InputSanitise::inputInt($request->get('allowed_unauthorised_user'));
        $timeOutBy = $request->get('time_out_by');
        $paperPattern = $request->get('paper_pattern');
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];

        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
                return 'false';
            }
        } else{
            $paper = new static;
        }
        $loginUser = Auth::guard('client')->user();

        $paper->name = $paperName;
        $paper->category_id = $catId;
        $paper->sub_category_id = $subcatId;
        $paper->subject_id = $subjectId;
        $paper->date_to_active =  $dateToActive;
        $paper->client_id = $clientId;
        $paper->date_to_inactive = $dateToInactive;
        $paper->show_calculator = $showCalculator;
        $paper->show_solution = $showSolution;
        $paper->option_count = $optionCount;
        $paper->created_by = $createdBy;
        $subCat = ClientOnlineTestSubCategory::find($subcatId);
        if(is_object($subCat) && $subCat->price <=0){
            $paper->is_free = 1;
        }else {
            $paper->is_free = $isFree;
        }
        $paper->allowed_unauthorised_user = $unauthorisedUser;
        if(1 == $paperPattern){
            $paper->paper_pattern = $paperPattern;
            $paper->time_out_by = 1;
            $paper->time = ('' == $time)? '1200':$time;
        } else {
            $paper->paper_pattern = $paperPattern;
            $paper->time_out_by = $timeOutBy;
            if(1 == $timeOutBy){
                $paper->time = ('' == $time)? '1200':$time;
            } else {
                $paper->time = '';
            }
        }
        $paper->save();

        if( $isUpdate && isset($paperId)){
            $allSessions = $request->except('_token','_method', 'paper_id', 'category', 'subcategory','subject', 'name', 'date_to_active', 'date_to_inactive', 'time', 'show_calculator', 'show_solution', 'option_count', 'time_out_by', 'all_session_count', 'is_free', 'allowed_unauthorised_user');

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
                $allSessions = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId);
                if(is_object($allSessions) && false == $allSessions->isEmpty()){
                    // update or delete
                    foreach($allSessions as $paperSession){
                        if(false == in_array($paperSession->id, $addPaperSessions)){
                            if(isset($updatePaperSessions[$paperSession->id]) && !empty($updatePaperSessions[$paperSession->id]['session'])){
                                if(isset($updatePaperSessions[$paperSession->id])){
                                    $paperSession->name =  InputSanitise::cleanSpecial($updatePaperSessions[$paperSession->id]['session']);
                                    if(0 == $paperPattern){
                                        $paperSession->duration = $updatePaperSessions[$paperSession->id]['duration'];
                                    } else {
                                        $paperSession->duration = '';
                                    }
                                    $paperSession->category_id = $catId;
                                    $paperSession->sub_category_id =$subcatId;
                                    $paperSession->subject_id = $subjectId;
                                    $paperSession->paper_id = $paperId;
                                    $paperSession->client_id = $clientId;
                                    $paperSession->created_by = $createdBy;
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
                            $paperSession = new ClientOnlinePaperSection;
                            $paperSession->name = InputSanitise::cleanSpecial($updatePaperSession['session']);
                            if(0 == $paperPattern){
                                $paperSession->duration = $updatePaperSession['duration'];
                            } else {
                                $paperSession->duration = '';
                            }
                            $paperSession->category_id = $catId;
                            $paperSession->sub_category_id =$subcatId;
                            $paperSession->subject_id = $subjectId;
                            $paperSession->paper_id = $paperId;
                            $paperSession->client_id = $clientId;
                            $paperSession->created_by = $createdBy;
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
                    if(0 == $paperPattern){
                        $duration = $request->get('duration_'.$i);
                    } else {
                        $duration = '';
                    }
                    if(!empty($session)){
                        $sessions[] = [
                                    'name' => InputSanitise::cleanSpecial($session),
                                    'duration' => $duration,
                                    'category_id' => $catId,
                                    'sub_category_id' => $subcatId,
                                    'subject_id' => $subjectId,
                                    'paper_id' => $paper->id,
                                    'client_id' => $clientId,
                                    'created_by' => $createdBy,
                                ];
                    }
                }
                if(count($sessions) > 0){
                    DB::connection('mysql2')->table('client_online_paper_sections')->insert($sessions);
                }
            }
        }
        return $paper;
    }

    /**
     *  add/update paper
     */
    protected static function addOrUpdatePayableSubjectPaper(Request $request, $isUpdate=false){
        $sessions = [];
        $addPaperSessions = [];
        $updatePaperSessions = [];
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $subcatId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('name'));
        $dateToActive = $request->get('date_to_active');
        $dateToInactive = $request->get('date_to_inactive');
        $time = strip_tags(trim($request->get('time')));
        $showCalculator = InputSanitise::inputInt($request->get('show_calculator'));
        $showSolution = InputSanitise::inputInt($request->get('show_solution'));
        $optionCount = InputSanitise::inputInt($request->get('option_count'));
        $isFree = InputSanitise::inputInt($request->get('is_free'));
        $unauthorisedUser = InputSanitise::inputInt($request->get('allowed_unauthorised_user'));
        $timeOutBy = $request->get('time_out_by');

        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
                return Redirect::to('admin/managePayablePaper');
            }
        } else{
            $paper = new static;
        }

        $paper->name = $paperName;
        $paper->category_id = 0;
        $paper->sub_category_id = $subcatId;
        $paper->subject_id = $subjectId;
        $paper->date_to_active =  $dateToActive;
        $paper->time = $time;
        $paper->client_id = 0;
        $paper->date_to_inactive = $dateToInactive;
        $paper->show_calculator = $showCalculator;
        $paper->show_solution = $showSolution;
        $paper->option_count = $optionCount;
        $paper->time_out_by = $timeOutBy;
        $paper->created_by = 0;
        $subCat = ClientOnlineTestSubCategory::find($subcatId);
        if(is_object($subCat) && $subCat->price <=0){
            $paper->is_free = 1;
        }else {
            $paper->is_free = $isFree;
        }
        $paper->allowed_unauthorised_user = $unauthorisedUser;
        $paper->save();

        if( $isUpdate && isset($paperId)){
            $allSessions = $request->except('_token','_method', 'paper_id', 'category', 'subcategory','subject', 'name', 'date_to_active', 'date_to_inactive', 'time', 'show_calculator', 'show_solution', 'option_count', 'time_out_by', 'all_session_count', 'is_free', 'allowed_unauthorised_user');

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
                $allSessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId($paperId);
                if(is_object($allSessions) && false == $allSessions->isEmpty()){
                    // update or delete
                    foreach($allSessions as $paperSession){
                        if(false == in_array($paperSession->id, $addPaperSessions)){
                            if(isset($updatePaperSessions[$paperSession->id]) && !empty($updatePaperSessions[$paperSession->id]['session'])){
                                if(isset($updatePaperSessions[$paperSession->id])){
                                    $paperSession->name =  InputSanitise::cleanSpecial($updatePaperSessions[$paperSession->id]['session']);
                                    $paperSession->duration = $updatePaperSessions[$paperSession->id]['duration'];
                                    $paperSession->category_id = 0;
                                    $paperSession->sub_category_id =$subcatId;
                                    $paperSession->subject_id = $subjectId;
                                    $paperSession->paper_id = $paperId;
                                    $paperSession->client_id = 0;
                                    $paperSession->created_by = 0;
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
                            $paperSession = new ClientOnlinePaperSection;
                            $paperSession->name = InputSanitise::cleanSpecial($updatePaperSession['session']);
                            $paperSession->duration = $updatePaperSession['duration'];
                            $paperSession->category_id = 0;
                            $paperSession->sub_category_id =$subcatId;
                            $paperSession->subject_id = $subjectId;
                            $paperSession->paper_id = $paperId;
                            $paperSession->client_id = 0;
                            $paperSession->created_by = 0;
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
                                    'name' => InputSanitise::cleanSpecial($session),
                                    'duration' => $duration,
                                    'category_id' => 0,
                                    'sub_category_id' => $subcatId,
                                    'subject_id' => $subjectId,
                                    'paper_id' => $paper->id,
                                    'client_id' => 0,
                                    'created_by' => 0
                                ];
                    }
                }
                if(count($sessions) > 0){
                    DB::connection('mysql2')->table('client_online_paper_sections')->insert($sessions);
                }
            }
        }

        return $paper;
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(ClientOnlineTestCategory::class, 'category_id');
    }

    /**
     *  get category of sub category
     */
    public function subcategory(){
        return $this->belongsTo(ClientOnlineTestSubCategory::class, 'sub_category_id');
    }

    /**
     *  get category of sub category
     */
    public function subject(){
        return $this->belongsTo(ClientOnlineTestSubject::class, 'subject_id');
    }

    public function questions(){
        return $this->hasMany(ClientOnlineTestQuestion::class, 'paper_id');
    }

    public function payableQuestionCount(){
        // return $this->hasMany(ClientOnlineTestQuestion::class, 'paper_id');
        return ClientOnlineTestQuestion::where('client_id', 0)->where('category_id', 0)->where('paper_id', $this->id)->count();
    }

    protected static function getOnlinePapersBySubjectId($subjectId){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->where('subject_id', $subjectId)
                    ->where('client_id', $clientId)
                    ->get();
    }

    protected static function getPayablePapersBySubjectId($subjectId){
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->where('subject_id', $subjectId)
                    ->where('client_id', 0)
                    ->where('category_id', 0)
                    ->get();
    }

    protected static function showPaper($request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', 'clients.id', '=', 'client_online_test_subject_papers.client_id')->with('category')->with('subcategory')->with('subject');
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->select('client_online_test_subject_papers.*')->get();
    }

    protected static function showPayablePaper(){
        return static::where('client_id', 0)->where('category_id', 0)->select('client_online_test_subject_papers.*')->get();
    }

    protected static function getRegisteredPapersByCatIdBySubCatId($catId, $subcatId, $userId){
        $testSubjectPapers = [];
        $papers = [];
        $result = DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_categories', 'client_online_test_categories.id', '=', 'client_online_test_subject_papers.category_id' )
                    ->join('client_online_test_sub_categories', 'client_online_test_sub_categories.id', '=', 'client_online_test_subject_papers.sub_category_id' )
                    ->join('register_client_online_papers', 'register_client_online_papers.client_paper_id', '=', 'client_online_test_subject_papers.id' )
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'register_client_online_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                    });

        $papers = $result->where('client_online_test_subject_papers.category_id', $catId)
                    ->where('client_online_test_subject_papers.sub_category_id', $subcatId)
                    ->where('register_client_online_papers.client_user_id', $userId)
                    ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                    ->select('client_online_test_subject_papers.*')->groupBy('client_online_test_subject_papers.id')->get();

        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->subject_id][] = $paper;
            }
        }
        return $testSubjectPapers;
    }

    protected static function getPayableRegisteredPapersBySubCatId($subcatId, $userId){
        $testSubjectPapers = [];
        $papers = [];
        $result = DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_sub_categories', 'client_online_test_sub_categories.id', '=', 'client_online_test_subject_papers.sub_category_id' )
                    ->join('register_client_online_papers', 'register_client_online_papers.client_paper_id', '=', 'client_online_test_subject_papers.id' )
                    ->where('client_online_test_subject_papers.category_id', 0)
                    ->where('client_online_test_subject_papers.sub_category_id', $subcatId)
                    ->where('register_client_online_papers.client_user_id', $userId)
                    ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                    ->select('client_online_test_subject_papers.*')
                    ->groupBy('client_online_test_subject_papers.id')->get();

        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->subject_id][] = $paper;
            }
        }
        return $testSubjectPapers;
    }

    protected static function getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request){
        $testSubjectPapers = [];
        $papers = [];
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result =   DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_questions', function($join){
                        $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
                    })
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    });

        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        $papers = $result->where('client_online_test_subject_papers.category_id', $catId)
                    ->where('client_online_test_subject_papers.sub_category_id', $subcatId)
                    ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                    ->select('client_online_test_subject_papers.*')->groupBy('client_online_test_subject_papers.id')->get();

        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->subject_id][] = $paper;
            }
        }
        return $testSubjectPapers;
    }

    protected static function showPayablePapersBySubCategoryIdAssociatedWithQuestion($subcatId){
        $papers = DB::connection('mysql2')->table('client_online_test_subject_papers')
            ->join('client_online_test_questions', function($join){
                $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
            })
            ->where('client_online_test_subject_papers.client_id', 0)
            ->where('client_online_test_subject_papers.category_id', 0)
            ->where('client_online_test_subject_papers.sub_category_id', $subcatId)
            ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
            ->select('client_online_test_subject_papers.*')->groupBy('client_online_test_subject_papers.id')->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->subject_id][] = $paper;
            }
        }
        return $testSubjectPapers;
    }

    /**
     *  return paper by Id
     */
    protected static function getOnlineTestSubjectPaperById($id, $request){
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                ->join('clients', 'clients.id', '=', 'client_online_test_subject_papers.client_id')
                ->select('client_online_test_subject_papers.*')
                ->where('client_online_test_subject_papers.id', $id)
                ->where('clients.subdomain', InputSanitise::getCurrentClient($request))->first();
    }

    /**
     *  return payable paper by Id
     */
    protected static function getPayablePaperById($id){
        return static::where('client_online_test_subject_papers.id', $id)
                ->where('client_online_test_subject_papers.category_id', 0)
                ->select('client_online_test_subject_papers.*')
                ->first();
    }

    protected static function getRegisteredSubjectPapersByUserId($userId){
        $testSubjectPapers = [];
        $papers = [];
        $testSubjectIds = [];
        $testPaperIds = [];
        $results = [];
        $papers = DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('register_client_online_papers', 'register_client_online_papers.client_paper_id', '=', 'client_online_test_subject_papers.id')
                    ->join('clientusers', 'clientusers.id', '=', 'register_client_online_papers.client_user_id')
                    ->where('clientusers.id', $userId)
                    ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                    ->select('client_online_test_subject_papers.*')
                    ->get();

        if(false == $papers->isEmpty()){
            foreach($papers as $paper){
                $testSubjectPapers[$paper->subject_id][] = $paper;
                $testSubjectIds[] = $paper->subject_id;
                $testPaperIds[] = $paper->id;
            }
            $results['papers'] = $testSubjectPapers;
            $results['paperIds'] = $testPaperIds;
            $results['subjectIds'] = array_unique($testSubjectIds);
        }
        return $results;
    }

    protected static function getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId($categoryId, $subcategoryId, $subjectId){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_categories', 'client_online_test_categories.id', '=', 'client_online_test_subject_papers.category_id' )
                    ->join('client_online_test_sub_categories', 'client_online_test_sub_categories.id', '=', 'client_online_test_subject_papers.sub_category_id' )
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                    })
                    ->where('client_online_test_subject_papers.category_id', $categoryId)
                    ->where('client_online_test_subject_papers.sub_category_id', $subcategoryId)
                    ->where('client_online_test_subject_papers.subject_id', $subjectId)
                    ->where('clients.id', $clientId)
                    ->select('client_online_test_subject_papers.*')
                    ->get();
    }

    protected static function getPayablePapersBySubCategoryIdBySubjectId($subcategoryId, $subjectId){

        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_sub_categories', 'client_online_test_sub_categories.id', '=', 'client_online_test_subject_papers.sub_category_id' )
                    ->where('client_online_test_subject_papers.sub_category_id', $subcategoryId)
                    ->where('client_online_test_subject_papers.subject_id', $subjectId)
                    ->where('client_online_test_subject_papers.client_id', 0)
                    ->where('client_online_test_subject_papers.category_id', 0)
                    ->select('client_online_test_subject_papers.*')
                    ->get();
    }

    protected static function getPapersBySubCategoryId($subcategoryId){
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->where('client_online_test_subject_papers.sub_category_id', $subcategoryId)
                    ->select('client_online_test_subject_papers.*')
                    ->get();
    }

    public function deleteRegisteredPaper(){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $registeredPapers = RegisterClientOnlinePaper::where('client_paper_id', $this->id)->where('client_id', $clientId)->get();
        if(is_object($registeredPapers) && false == $registeredPapers->isEmpty()){
            foreach($registeredPapers as $registeredPaper){
                $registeredPaper->delete();
            }
        }
    }

    public function deletePayableRegisteredPaper(){
        $registeredPapers = RegisterClientOnlinePaper::where('client_paper_id', $this->id)->where('client_id', $this->client_id)->get();
        if(is_object($registeredPapers) && false == $registeredPapers->isEmpty()){
            foreach($registeredPapers as $registeredPaper){
                $registeredPaper->delete();
            }
        }
    }

    protected static function deleteClientOnlineTestSubjectPapersByClientId($clientId){
        $subjectPapers = static::where('client_id', $clientId)->get();
        if(is_object($subjectPapers) && false == $subjectPapers->isEmpty()){
            foreach($subjectPapers as $subjectPaper){
                $subjectPaper->delete();
            }
        }
    }

    protected static function getClientOnlineTestSubjectPapersByClient(){
        return static::where('client_online_test_subject_papers.client_id', Auth::guard('clientuser')->user()->client_id)
            ->select('client_online_test_subject_papers.*')->get();
    }

    protected static function isClientTestPaperExist(Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('paper'));
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $result = static::where('client_id', $clientId)->where('category_id', $categoryId)->where('sub_category_id', $subcategoryId)->where('subject_id', $subjectId)->where('name', $paperName);
        if(!empty($paperId)){
            $result->where('id', '!=', $paperId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    protected static function isPayablePaperExist(Request $request){
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('paper'));
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $result = static::where('client_id', 0)->where('category_id', 0)->where('sub_category_id', $subcategoryId)->where('subject_id', $subjectId)->where('name', $paperName);
        if(!empty($paperId)){
            $result->where('id', '!=', $paperId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    protected static function assignClientTestPapersToClientByClientIdByTeacherId($clientId,$teacherId){
        $papers = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $paper->created_by = 0;
                $paper->save();
            }
        }
    }
}