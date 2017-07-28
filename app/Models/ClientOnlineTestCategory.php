<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientInstituteCourse;

class ClientOnlineTestCategory extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'client_institute_course_id'];

     /**
     *  add/update test category
     */
    protected static function addOrUpdateCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return Redirect::to('admin/manageCategory');
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->client_id = Auth::guard('client')->user()->id;
        $category->client_institute_course_id = $instituteCourseId;
        $category->save();
        return $category;
    }

    protected static function showCategories($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                })
                ->join('client_institute_courses', 'client_institute_courses.id', '=', 'client_online_test_categories.client_institute_course_id');
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_test_categories.*')
                ->get();
    }

    protected static function getOnlineTestCategoriesAssociatedWithQuestion($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = DB::connection('mysql2')->table('client_online_test_categories')
                    ->join('client_online_test_subjects', 'client_online_test_subjects.category_id', '=', 'client_online_test_categories.id')
                    ->join('client_online_test_subject_papers', 'client_online_test_subject_papers.category_id', '=', 'client_online_test_categories.id')
                    ->join('client_online_test_questions', 'client_online_test_questions.category_id', '=', 'client_online_test_categories.id')
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subjects.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    });
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->select('client_online_test_categories.id', 'client_online_test_categories.name')
            ->groupBy('client_online_test_categories.id')->get();
    }

    public function subcategories(){
        return $this->hasMany(ClientOnlineTestSubCategory::class, 'category_id');
    }

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    /**
     * return test categopries registered subject papers
     */
    protected static function getTestCategoriesByRegisteredSubjectPapersByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        return DB::connection('mysql2')->table('client_online_test_categories')
                ->join('client_online_test_subject_papers', 'client_online_test_subject_papers.category_id', 'client_online_test_categories.id')
                ->join('register_client_online_papers', 'register_client_online_papers.client_paper_id', 'client_online_test_subject_papers.id')
                ->join('clientusers', 'clientusers.id', '=', 'register_client_online_papers.client_user_id')
                ->where('register_client_online_papers.client_user_id', $userId)
                ->select('client_online_test_categories.id', 'client_online_test_categories.name')->groupBy('client_online_test_categories.id')->get();
    }

    protected static function getCategoriesByInstituteCourseId($id){
        return static::where('client_institute_course_id', $id)->get();
    }
}
