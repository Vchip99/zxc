<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubject;
use Intervention\Image\ImageManagerStatic as Image;

class ClientOnlineTestSubCategory extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id','client_id', 'image_path', 'price', 'monthly_price','created_by'];

    /**
     *  add/update sub category
     */
    protected static function addOrUpdateSubCategory($subdomainName,Request $request, $isUpdate=false){
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        $catId = InputSanitise::inputInt($request->get('category'));
        $name = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputString($request->get('price'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];
        if( $isUpdate && isset($subcatId)){
            $testSubcategory = static::find($subcatId);
            if(!is_object($testSubcategory)){
                return 'false';
            }
        } else{
            $testSubcategory = new static;
        }
        $testSubcategory->name = $name;
        $testSubcategory->category_id = $catId;
        $testSubcategory->client_id = $clientId;
        $testSubcategory->price = $price;
        $testSubcategory->monthly_price = '';
        $testSubcategory->created_by = $createdBy;

        $clientName = $subdomainName;

        if($request->exists('image_path')){
            $subCategoryImage = $request->file('image_path')->getClientOriginalName();
            $subCategoryImageFolder = "client_images"."/".$clientName."/"."testSubCategoryImages/";

            $subCategoryFolderPath = $subCategoryImageFolder.str_replace(' ', '_', $name);
            if(!is_dir($subCategoryFolderPath)){
                mkdir($subCategoryFolderPath, 0755, true);
            }
            $subCategoryImagePath = $subCategoryFolderPath ."/". $subCategoryImage;
            if(file_exists($subCategoryImagePath)){
                unlink($subCategoryImagePath);
            } elseif(!empty($testSubcategory->id) && file_exists($testSubcategory->image_path)){
                unlink($testSubcategory->image_path);
            }
            $request->file('image_path')->move($subCategoryFolderPath, $subCategoryImage);
            $testSubcategory->image_path = $subCategoryImagePath;
            if(in_array($request->file('image_path')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($testSubcategory->image_path);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
        }

        $testSubcategory->save();
        return $testSubcategory;
    }

    /**
     *  add/update payable sub category
     */
    protected static function addOrUpdatePayableSubCategory( Request $request, $isUpdate=false){
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        $name = InputSanitise::inputString($request->get('name'));
        $price = InputSanitise::inputString($request->get('price'));
        $monthlyPrice = InputSanitise::inputString($request->get('monthly_price'));
        if( $isUpdate && isset($subcatId)){
            $testSubcategory = static::find($subcatId);
            if(!is_object($testSubcategory)){
                return Redirect::to('admin/managePayableSubCategory');
            }
        } else{
            $testSubcategory = new static;
        }
        $testSubcategory->name = $name;
        $testSubcategory->category_id = 0;
        $testSubcategory->client_id = 0;
        $testSubcategory->price = $price;
        $testSubcategory->monthly_price = $monthlyPrice;

        if($request->exists('image_path')){
            $subCategoryImage = $request->file('image_path')->getClientOriginalName();
            $subCategoryImageFolder = "client_images/admin/testSubCategoryImages/";

            $subCategoryFolderPath = $subCategoryImageFolder.str_replace(' ', '_', $name);
            if(!is_dir($subCategoryFolderPath)){
                File::makeDirectory($subCategoryFolderPath, $mode = 0777, true, true);
            }
            $subCategoryImagePath = $subCategoryFolderPath ."/". $subCategoryImage;
            if(file_exists($subCategoryImagePath)){
                unlink($subCategoryImagePath);
            } elseif(!empty($testSubcategory->id) && file_exists($testSubcategory->image_path)){
                unlink($testSubcategory->image_path);
            }
            $request->file('image_path')->move($subCategoryFolderPath, $subCategoryImage);
            $testSubcategory->image_path = $subCategoryImagePath;
            // open image
            $img = Image::make($testSubcategory->image_path);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
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
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        if($clientId > 0 && $id > 0){
            return DB::connection('mysql2')->table('client_online_test_sub_categories')
                    ->where('category_id', $id)
                    ->where('client_id', $clientId)
                    ->get();
        }
        return;
    }

    protected static function getOnlineTestSubcategoriesByCategoryIdWithPapers($id, Request $request){
        $loginClient = Auth::guard('client')->user();
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else if(is_object($loginUser)){
            $clientId = $loginUser->client_id;
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
                ->groupBy('client_online_test_sub_categories.id')
                ->get();
        }
        return;
    }

    protected static function getPayableSubcategoriesByIdsWithPapers($ids){
        return DB::connection('mysql2')->table('client_online_test_sub_categories')
            ->join('client_online_test_subjects', function($join){
                $join->on('client_online_test_subjects.sub_category_id', '=', 'client_online_test_sub_categories.id');
            })
            ->join('client_online_test_subject_papers', function($join){
                $join->on('client_online_test_subject_papers.sub_category_id', '=', 'client_online_test_sub_categories.id');
                $join->on('client_online_test_subject_papers.subject_id', '=', 'client_online_test_subjects.id');
            })
            ->whereIn('client_online_test_sub_categories.id', $ids)
            ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
            ->select('client_online_test_sub_categories.*')
            ->groupBy('client_online_test_sub_categories.id')
            ->get();
    }

    protected static function getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($id, Request $request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
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
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
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

    protected static function showPayableSubCategories(){
        return static::where('client_id', 0)->where('category_id', 0)->select('client_online_test_sub_categories.*')->get();
    }

    protected static function showPayableSubcategoryById($subcategoryId){
        return static::where('client_id', 0)->where('category_id', 0)->where('id', $subcategoryId)->first();
    }

    protected static function showPayableSubcategoriesByIdesAssociatedWithQuestion($subcategoryIdes){
        return static::join('client_online_test_subjects', function($join){
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
                ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                ->where('client_online_test_sub_categories.client_id', 0)
                ->where('client_online_test_sub_categories.category_id', 0)
                ->whereIn('client_online_test_sub_categories.id', $subcategoryIdes)
                ->select('client_online_test_sub_categories.*')
                ->groupBy('client_online_test_sub_categories.id')->get();
    }

    protected static function showSubCategoriesAssociatedWithQuestion($request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
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

    protected static function showPaidSubCategoriesAssociatedWithQuestion($request){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
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
        return  $result->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))->where('client_online_test_sub_categories.price', '>',0)
                ->select('client_online_test_sub_categories.*')->groupBy('client_online_test_sub_categories.id')
                ->get();
    }

    protected static function showPayableSubCategoriesAssociatedWithQuestion(){
        return DB::connection('mysql2')->table('client_online_test_sub_categories')
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
                })->where('client_online_test_sub_categories.client_id', 0)
                ->where('client_online_test_sub_categories.category_id', 0)
                ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
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

    public function deletePayableSubCategoryImageFolder(){
        $subCategoryImageFolder = "client_images/admin/testSubCategoryImages/".str_replace(' ', '_', $this->name);
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

    protected static function isClientTestSubCategoryExist(Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $result = static::where('client_id', $clientId)->where('category_id', $categoryId)->where('name', '=',$subCategoryName);
        if(!empty($subCategoryId)){
            $result->where('id', '!=', $subCategoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function isPayableTestSubCategoryExist(Request $request){
        $subCategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $result = static::where('client_id', 0)->where('category_id', 0)->where('name', '=',$subCategoryName);
        if(!empty($subCategoryId)){
            $result->where('id', '!=', $subCategoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function assignClientTestSubCategoriesToClientByClientIdByTeacherId($clientId,$teacherId){
        $subcategories = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($subcategories) && false == $subcategories->isEmpty()){
            foreach($subcategories as $subcategory){
                $subcategory->created_by = 0;
                $subcategory->save();
            }
        }
    }

    protected static function getClientOnlineTestSubCategoriesByUpdatedDate($searchDate){
        return static::whereDate('updated_at','>=',$searchDate)->get();
    }
}