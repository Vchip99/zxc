<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestQuestion;
use App\Models\RegisterClientOnlinePaper;
use App\Models\ClientInstituteCourse;

class ClientOnlineTestSubjectPaper extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'subject_id', 'price', 'date_to_active', 'time','client_id', 'client_institute_course_id'];

    /**
     *  add/update paper
     */
    protected static function addOrUpdateOnlineTestSubjectPaper( Request $request, $isUpdate=false){

        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $subcatId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperName = InputSanitise::inputString($request->get('name'));+
        $price = InputSanitise::inputInt($request->get('price'));
        $dateToActive = $request->get('date_to_active');
        $time = strip_tags(trim($request->get('time')));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));

        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
                return Redirect::to('admin/managePaper');
            }
        } else{
            $paper = new static;
        }

        $paper->name = $paperName;
        $paper->category_id = $catId;
        $paper->sub_category_id = $subcatId;
        $paper->subject_id = $subjectId;
        $paper->price = $price;
        $paper->date_to_active = $dateToActive;
        $paper->time = $time;
        $paper->client_id = Auth::guard('client')->user()->id;
        $paper->client_institute_course_id = $instituteCourseId;
        $paper->save();

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

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    protected static function getOnlinePapersBySubjectId($subjectId){
        return DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->where('subject_id', $subjectId)
                    ->where('client_id', Auth::guard('client')->user()->id)
                    ->get();
    }

    protected static function showPaper($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', 'clients.id', '=', 'client_online_test_subject_papers.client_id')->with('category')->with('subcategory')->with('subject');
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_test_subject_papers.*')->get();
    }

    protected static function getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request){
        $testSubjectPapers = [];
        $papers = [];
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result = DB::connection('mysql2')->table('client_online_test_subject_papers')
                    ->join('client_online_test_categories', 'client_online_test_categories.id', '=', 'client_online_test_subject_papers.category_id' )
                    ->join('client_online_test_sub_categories', 'client_online_test_sub_categories.id', '=', 'client_online_test_subject_papers.sub_category_id' )
                    ->join('client_online_test_questions', 'client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id' )
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                    });
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        $papers = $result->where('client_online_test_subject_papers.category_id', $catId)
                    ->where('client_online_test_subject_papers.sub_category_id', $subcatId)
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
                    ->where('clients.id', Auth::guard('client')->user()->id)
                    ->select('client_online_test_subject_papers.*')
                    ->get();
    }

    public function deleteRegisteredPaper(){
        $registeredPapers = RegisterClientOnlinePaper::where('client_paper_id', $this->id)->where('client_id', Auth::guard('client')->user()->id)->get();
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

    protected static function getClientOnlineTestSubjectPapersByAssignedClientUserInstituteCourse(){
        return static::join('client_user_institute_courses', 'client_user_institute_courses.client_institute_course_id', '=', 'client_online_test_subject_papers.client_institute_course_id')
            ->where('client_online_test_subject_papers.client_id', Auth::guard('clientuser')->user()->client_id)
            ->where('client_user_institute_courses.client_user_id', Auth::guard('clientuser')->user()->id)
            ->where('client_user_institute_courses.test_permission', 1)->select('client_online_test_subject_papers.*')->get();
    }
}