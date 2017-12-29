<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubject;


class ClientOnlineTestSubCategory extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id','client_id', 'image_path', 'price'];

    /**
     *  add/update sub category
     */
    protected static function addOrUpdateSubCategory( Request $request, $isUpdate=false){
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $name = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputString($request->get('price'));

        if( $isUpdate && isset($subcatId)){
            $testSubcategory = static::find($subcatId);
            if(!is_object($testSubcategory)){
                return Redirect::to('manageOnlineTestSubCategory');
            }
        } else{
            $testSubcategory = new static;
        }
        $testSubcategory->name = $name;
        $testSubcategory->category_id = $catId;
        $testSubcategory->client_id = Auth::guard('client')->user()->id;
        $testSubcategory->price = $price;

        $subdomainArr = explode('.', Auth::guard('client')->user()->subdomain);
        $clientName = $subdomainArr[0];

        if($request->exists('image_path')){
            $subCategoryImage = $request->file('image_path')->getClientOriginalName();
            $subCategoryImageFolder = "client_images"."/".$clientName."/"."testSubCategoryImages/";

            $subCategoryFolderPath = $subCategoryImageFolder.str_replace(' ', '_', $name);
            if(!is_dir($subCategoryFolderPath)){
                mkdir($subCategoryFolderPath, 0755, true);
            }
            $courseImagePath = $subCategoryFolderPath ."/". $subCategoryImage;
            if(file_exists($courseImagePath)){
                unlink($courseImagePath);
            } elseif(!empty($testSubcategory->id) && file_exists($testSubcategory->image_path)){
                unlink($testSubcategory->image_path);
            }
            $request->file('image_path')->move($subCategoryFolderPath, $subCategoryImage);
            $testSubcategory->image_path = $courseImagePath;
        }

        $testSubcategory->save();
        return $testSubcategory;
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(ClientOnlineTestCategory::class, 'category_id');
    }

    public function subjects(){
        return $this->hasMany(ClientOnlineTestSubject::class, 'sub_category_id');
    }

    protected static function getOnlineTestSubcategoriesByCategoryId($id, Request $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else if(is_object(Auth::guard('clientuser')->user())){
            $clientId = Auth::guard('clientuser')->user()->client_id;
        }
        if($clientId > 0 && $id > 0){
            return DB::connection('mysql2')->table('client_online_test_sub_categories')
                    ->where('category_id', $id)
                    ->where('client_id', $clientId)
                    ->get();
        }
        return;
    }

    protected static function getOnlineTestSubcategoriesByCategoryIdWithPapers($id, Request $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else if(is_object(Auth::guard('clientuser')->user())){
            $clientId = Auth::guard('clientuser')->user()->client_id;
        }
        if($clientId > 0 && $id > 0){
            return DB::connection('mysql2')->table('client_online_test_sub_categories')
                ->join('client_online_test_subjects', function($join){
                    $join->on('client_online_test_subjects.sub_category_id', '=', 'client_online_test_sub_categories.id');
                })
                ->join('client_online_test_subject_papers', function($join){
                    $join->on('client_online_test_subject_papers.sub_category_id', '=', 'client_online_test_sub_categories.id');
                    $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
                })
                ->where('client_online_test_sub_categories.category_id', $id)
                ->where('client_online_test_sub_categories.client_id', $clientId)
                ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                ->select('client_online_test_sub_categories.*')
                ->get();
        }
        return;
    }

    protected static function getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($id, Request $request){

        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result = DB::connection('mysql2')->table('client_online_test_sub_categories')
                ->join('client_online_test_subjects', function($join){
                    $join->on('client_online_test_subjects.sub_category_id', '=', 'client_online_test_sub_categories.id');
                })
                ->join('client_online_test_subject_papers', function($join){
                    $join->on('client_online_test_subject_papers.sub_category_id', '=', 'client_online_test_sub_categories.id');
                    $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
                })
                ->join('client_online_test_questions', function($join){
                    $join->on('client_online_test_questions.subcat_id', '=', 'client_online_test_sub_categories.id');
                    $join->on('client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id');
                    $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
                })
                ->join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                    $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                    $join->on('clients.id', '=', 'client_online_test_subjects.client_id');
                });

        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }

        return  $result->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))->where('client_online_test_sub_categories.category_id', $id)->select('client_online_test_sub_categories.*')
                ->groupBy('client_online_test_sub_categories.id')->get();
    }


    protected static function showSubCategories($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result = static::join('clients','clients.id', '=', 'client_online_test_sub_categories.client_id')->with('category');
                if(!empty($clientId)){
                    $result->where('clients.id', $clientId);
                } else {
                    $result->where('clients.subdomain', $client);
                }
            return  $result->select('client_online_test_sub_categories.*')
                ->get();
    }

    protected static function showSubCategoriesAssociatedWithQuestion($request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = DB::connection('mysql2')->table('client_online_test_sub_categories')
                ->join('client_online_test_subjects', function($join){
                    $join->on('client_online_test_subjects.sub_category_id', '=', 'client_online_test_sub_categories.id');
                })
                ->join('client_online_test_subject_papers', function($join){
                    $join->on('client_online_test_subject_papers.sub_category_id', '=', 'client_online_test_sub_categories.id');
                    $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
                })
                ->join('client_online_test_questions', function($join){
                    $join->on('client_online_test_questions.subcat_id', '=', 'client_online_test_sub_categories.id');
                    $join->on('client_online_test_questions.subject_id', '=', 'client_online_test_subjects.id');
                    $join->on('client_online_test_questions.paper_id', '=', 'client_online_test_subject_papers.id');
                })
                ->join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                    $join->on('clients.id', '=', 'client_online_test_subject_papers.client_id');
                    $join->on('clients.id', '=', 'client_online_test_subjects.client_id');
                });

        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                ->select('client_online_test_sub_categories.*')->groupBy('client_online_test_sub_categories.id')
                ->get();
    }

    protected static function getCurrentSubCategoriesAssociatedWithQuestion($subdomain){
        return DB::connection('mysql2')->table('client_online_test_sub_categories')
                ->join('client_online_test_questions', 'client_online_test_questions.subcat_id', '=', 'client_online_test_sub_categories.id')
                ->join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_test_questions.client_id');
                    $join->on('clients.id', '=', 'client_online_test_sub_categories.client_id');
                })
                ->where('clients.subdomain', $subdomain)->select('client_online_test_sub_categories.*')->groupBy('client_online_test_sub_categories.category_id')->orderBy('id', 'desc')->take(2)->get();
    }

    public function deleteSubCategoryImageFolder($request){
        $subdomain = explode('.',$request->getHost());
        $subCategoryImageFolder = "client_images/".$subdomain[0]."/"."testSubCategoryImages/".str_replace(' ', '_', $this->name);
        if(is_dir($subCategoryImageFolder)){
            InputSanitise::delFolder($subCategoryImageFolder);
        }
    }

    protected static function deleteClientOnlineTestSubCategoriesByClientId($clientId){
        $subcategories = static::where('client_id', $clientId)->get();
        if(is_object($subcategories) && false == $subcategories->isEmpty()){
            foreach($subcategories as $subcategory){
                $subcategory->delete();
            }
        }
    }

}