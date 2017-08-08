<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientInstituteCourse;

class ClientOnlineTestSubject extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'client_id', 'client_institute_course_id'];

     /**
     *  add/update subject
     */
    protected static function addOrUpdateSubject( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectName = InputSanitise::inputString($request->get('name'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));

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
        $testSubject->client_institute_course_id = $instituteCourseId;
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

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    protected static function getOnlineSubjectsByCatIdBySubcatId($categoryId, $subcategoryId, $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
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
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result =  DB::connection('mysql2')->table('client_online_test_subjects')
                    ->join('client_online_test_questions', 'client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id')
                    ->join('clients', 'clients.id', '=', 'client_online_test_subjects.client_id')
                    ->where('client_online_test_subjects.category_id', $categoryId)
                    ->where('client_online_test_subjects.sub_category_id', $subcategoryId);
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->select('client_online_test_subjects.id','client_online_test_subjects.*')->groupBy('client_online_test_subjects.id')->get();
    }

    protected static function showSubjects($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
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
}
