<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineTestSubCategory;

class ClientOnlineTestCategory extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'created_by'];

    /**
     *  add/update test category
     */
    protected static function addOrUpdateCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];

        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return 'false';
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->client_id = $clientId;
        $category->created_by = $createdBy;
        $category->save();
        return $category;
    }

    protected static function showCategories($request){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                });
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_test_categories.*')
                ->get();
    }

    protected static function getOnlineTestCategoriesAssociatedWithQuestion($request){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = DB::connection('mysql2')->table('client_online_test_categories')
                    ->join('client_online_test_sub_categories', function($join){
                        $join->on('client_online_test_sub_categories.category_id', '=', 'client_online_test_categories.id');
                    })
                    ->join('client_online_test_subjects', function($join){
                        $join->on('client_online_test_subjects.category_id', '=', 'client_online_test_categories.id');
                        $join->on('client_online_test_subjects.sub_category_id', '=', 'client_online_test_sub_categories.id');
                    })
                    ->join('client_online_test_subject_papers', function($join){
                        $join->on('client_online_test_subject_papers.category_id', '=', 'client_online_test_categories.id');
                        $join->on('client_online_test_subject_papers.sub_category_id', '=', 'client_online_test_sub_categories.id');
                        $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
                    })
                    ->join('client_online_test_questions', function($join){
                        $join->on('client_online_test_questions.category_id', '=', 'client_online_test_categories.id');
                        $join->on('client_online_test_questions.subcat_id', '=', 'client_online_test_sub_categories.id');
                        $join->on('client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id');
                        $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
                    })
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subjects.client_id');
                        $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                        $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    });
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return $result->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
            ->select('client_online_test_categories.id', 'client_online_test_categories.name')
            ->groupBy('client_online_test_categories.id')->get();
    }

    protected static function getOnlineTestCategoriesAssociatedWithPayableSubCategory($request,$categoryIds){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = DB::connection('mysql2')->table('client_online_test_categories')
                    ->join('payable_client_sub_categories', function($join){
                        $join->on('payable_client_sub_categories.category_id', '=', 'client_online_test_categories.id');
                    })
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_test_categories.client_id');
                        $join->on('clients.id', '=', 'payable_client_sub_categories.client_id');
                    });
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        if(count($categoryIds) > 0){
            $result->whereNotIn('client_online_test_categories.id', $categoryIds);
        }
        $result->where('payable_client_sub_categories.start_date', '<=',date('Y-m-d H:i:s'));
        $result->where('payable_client_sub_categories.end_date', '>=',date('Y-m-d H:i:s'));
        return $result->select('client_online_test_categories.id', 'client_online_test_categories.name')
            ->groupBy('client_online_test_categories.id')->get();
    }

    public function subcategories(){
        return $this->hasMany(ClientOnlineTestSubCategory::class, 'category_id');
    }

    /**
     * return test categopries registered subject papers
     */
    protected static function getTestCategoriesByRegisteredSubjectPapersByUserId($userId){
        $userId = InputSanitise::inputInt($userId);
        $result =  DB::connection('mysql2')->table('client_online_test_categories')
                ->join('client_online_test_subject_papers', 'client_online_test_subject_papers.category_id', 'client_online_test_categories.id')
                ->join('register_client_online_papers', 'register_client_online_papers.client_paper_id', 'client_online_test_subject_papers.id')
                ->join('clientusers', 'clientusers.id', '=', 'register_client_online_papers.client_user_id')
                ->where('register_client_online_papers.client_user_id', $userId)
                ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'));
        return $result->select('client_online_test_categories.id', 'client_online_test_categories.name')->groupBy('client_online_test_categories.id')->get();
    }

    protected static function getCategoriesByInstituteCourseId($id){
        return static::where('client_institute_course_id', $id)->get();
    }

    protected static function deleteClientOnlineTestCategoriesByClientId($clientId){
        $categories = static::where('client_id', $clientId)->get();
        if(is_object($categories) && false == $categories->isEmpty()){
            foreach($categories as $category){
                $category->delete();
            }
        }
    }

    protected static function isClientTestCategoryExist(Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('client_id', $clientId)->where('name', '=',$categoryName);
        if(!empty($categoryId)){
            $result->where('id', '!=', $categoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function assignClientTestCategoriesToClientByClientIdByTeacherId($clientId,$teacherId){
        $categories = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($categories) && false == $categories->isEmpty()){
            foreach($categories as $category){
                $category->created_by = 0;
                $category->save();
            }
        }
    }
}
