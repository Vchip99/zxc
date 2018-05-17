<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubjectPaper;

class ClientOnlineTestSubject extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'client_id'];

    /**
     *  add/update subject
     */
    protected static function addOrUpdateSubject(Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('name'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        if( $isUpdate && isset($subjectId)){
            $testSubject = static::find($subjectId);
            if(!is_object($testSubject)){
                return Redirect::to('manageOnlineTestSubject');
            }
        } else{
            $testSubject = new static;
        }
        $testSubject->name = $subjectName;
        $testSubject->category_id = $categoryId;
        $testSubject->sub_category_id = $subcategoryId;
        $testSubject->client_id = Auth::guard('client')->user()->id;
        $testSubject->save();
        return $testSubject;
    }

    /**
     *  add/update payable subject
     */
    protected static function addOrUpdatePayableSubject(Request $request, $isUpdate=false){
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('name'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));

        if( $isUpdate && isset($subjectId)){
            $testSubject = static::find($subjectId);
            if(!is_object($testSubject)){
                return Redirect::to('admin/managePayableSubject');
            }
        } else{
            $testSubject = new static;
        }
        $testSubject->name = $subjectName;
        $testSubject->category_id = 0;
        $testSubject->sub_category_id = $subcategoryId;
        $testSubject->client_id = 0;
        $testSubject->save();
        return $testSubject;
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

    public function papers(){
        return $this->hasMany(ClientOnlineTestSubjectPaper::class, 'subject_id');
    }

    protected static function getOnlineSubjectsByCatIdBySubcatId($categoryId, $subcategoryId, $request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result =  DB::connection('mysql2')->table('client_online_test_subjects')
                    ->join('clients','clients.id', '=', 'client_online_test_subjects.client_id')
                    ->where('category_id', $categoryId)
                    ->where('sub_category_id', $subcategoryId);
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->select('client_online_test_subjects.*')->get();
    }

    protected static function getOnlineSubjectsByCatIdBySubcatIdWithQuestion($categoryId, $subcategoryId, $request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result =  DB::connection('mysql2')->table('client_online_test_subjects')
                    ->join('client_online_test_subject_papers', function($join){
                        $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
                    })
                    ->join('client_online_test_questions', function($join){
                        $join->on('client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id');
                        $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
                    })
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_subjects.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    });

        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->where('client_online_test_subjects.category_id', $categoryId)
                    ->where('client_online_test_subjects.sub_category_id', $subcategoryId)
                    ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                    ->select('client_online_test_subjects.*')->groupBy('client_online_test_subjects.id')->get();
    }

    protected static function showSubjects($request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients','clients.id', '=', 'client_online_test_subjects.client_id')->with('category')->with('subcategory');
            if(!empty($clientId)){
                $result->where('clients.id', $clientId);
            } else {
                $result->where('clients.subdomain', $client);
            }
        return  $result->select('client_online_test_subjects.*')->get();
    }

    protected static function showPayableSubjects(){
        return static::where('client_id', 0)->where('category_id', 0)->select('client_online_test_subjects.*')->get();
    }

    protected static function showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($subcategoryId){
        return  DB::connection('mysql2')->table('client_online_test_subjects')
            ->join('client_online_test_subject_papers', function($join){
                $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
            })
            ->join('client_online_test_questions', function($join){
                $join->on('client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id');
                $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
            })->where('client_online_test_subjects.client_id', 0)
            ->where('client_online_test_subjects.category_id', 0)
            ->where('client_online_test_subjects.sub_category_id', $subcategoryId)
            ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
            ->select('client_online_test_subjects.*')->groupBy('client_online_test_subjects.id')->get();
    }

    protected static function getSubjectsByIds($ids){
        return DB::connection('mysql2')->table('client_online_test_subjects')->whereIn('id', $ids)
                        ->select('client_online_test_subjects.*')
                        ->get();
    }

    protected static function deleteClientOnlineTestSubjectsByClientId($clientId){
        $subjects = static::where('client_id', $clientId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $subject->delete();
            }
        }
    }

    protected static function isClientTestSubjectExist(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $result = static::where('client_id', $clientId)->where('category_id', $categoryId)->where('sub_category_id', $subcategoryId)->where('name', $subjectName);
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

    protected static function isPayableSubjectExist(Request $request){
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $result = static::where('client_id', 0)->where('category_id', 0)->where('sub_category_id', $subcategoryId)->where('name', $subjectName);
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

    protected static function getPayableSubjectsBySubcatId($subcategoryId){
        return static::where('client_id', 0)->where('category_id', 0)->where('sub_category_id', $subcategoryId)->select('client_online_test_subjects.*')->get();
    }
}
