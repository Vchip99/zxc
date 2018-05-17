<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth,DB,Session,File;
use App\Models\Client;
use Intervention\Image\ImageManagerStatic as Image;

class PayableClientSubCategory extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'category_id', 'sub_category_id', 'admin_price', 'client_user_price','payment_request_id', 'payment_id','client_image', 'start_date', 'end_date', 'sub_category'];


    protected static function addPayableClientSubCategory($data){
    	$subCategory = new static;
    	$subCategory->client_id = Auth::guard('client')->user()->id;
		$subCategory->category_id = $data['category_id'];
		$subCategory->sub_category_id = $data['sub_category_id'];
		$subCategory->admin_price = $data['admin_price'];
		$subCategory->client_user_price = $data['client_user_price'];
		$subCategory->payment_request_id  = $data['payment_request_id'];
		$subCategory->payment_id = $data['payment_id'];
		$subCategory->client_image = '';
		$subCategory->start_date = $data['start_date'];
		$subCategory->end_date = $data['end_date'];
        $subCategory->sub_category = $data['sub_category'];
		$subCategory->save();
    }

    protected static function updatePayableSubCategory(Request $request){
        $payableSubcategoryId = $request->get('payable_subcategory_id');
        $subcategoryId = $request->get('subcategory_id');
        $subcategoryName = $request->get('subcategory_name');
        $subcatPrice = $request->get('subcat_price');
        // subcat_image
        $subCategory = static::where('id', $payableSubcategoryId)->where('sub_category_id', $subcategoryId)->where('client_id', Auth::guard('client')->user()->id)->first();
        if(!is_object($subCategory)){
            return 'false';
        }
        $subCategory->client_user_price = $subcatPrice;
        if($request->exists('subcat_image')){
            $subCategoryImage = $request->file('subcat_image')->getClientOriginalName();
            $subCategoryImageFolder = "client_images/admin/testSubCategoryImages/";

            $subCategoryFolderPath = $subCategoryImageFolder.str_replace(' ', '_', $subcategoryName);
            if(!is_dir($subCategoryFolderPath)){
                File::makeDirectory($subCategoryFolderPath, $mode = 0777, true, true);
            }
            $subCategoryImagePath = $subCategoryFolderPath ."/". $subCategoryImage;
            if(file_exists($subCategoryImagePath)){
                unlink($subCategoryImagePath);
            } elseif(!empty($subCategory->id) && file_exists($subCategory->client_image)){
                unlink($subCategory->client_image);
            }
            $request->file('subcat_image')->move($subCategoryFolderPath, $subCategoryImage);
            $subCategory->client_image = $subCategoryImagePath;
            // open image
            $img = Image::make($subCategory->client_image);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }
        $subCategory->save();
        return $subCategory;
    }

    protected static function getPayableSubCategoryByClientIdByCategoryId($clientId, $categoryId){
        return static::where('client_id', $clientId)
            ->where('category_id', $categoryId)
            ->where('end_date', '>=',date('Y-m-d'))
            ->get();
    }

    protected static function getPayableSubCategoryByClientIdBySubCategoryId($clientId, $subcategoryId){
        return static::where('client_id', $clientId)
            ->where('sub_category_id', $subcategoryId)
            ->where('end_date', '>=',date('Y-m-d'))
            ->first();
    }

    protected static function getPayableSubCategoryByClientId($clientId){
        return static::where('client_id', $clientId)->where('end_date', '>=',date('Y-m-d'))->get();
    }

    protected static function getPayableSubCategoriesBySubCategoryId($subcategoryId){
        return static::where('sub_category_id', $subcategoryId)->where('end_date', '>=',date('Y-m-d'))->get();
    }

    protected static function getDeActivePayableSubCategory(){
        return static::where('end_date',date('Y-m-d', strtotime('-1 day')))->get();
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function clientName(){
        $client = Client::find($this->client_id);
        if(is_object($client)){
            return $client->name;
        } else {
            return 'deleted';
        }
    }
}