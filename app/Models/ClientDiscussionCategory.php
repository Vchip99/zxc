<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;


class ClientDiscussionCategory extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id'];

    /**
     *  add/update Discussion category
     */
    protected static function addOrUpdateDiscussionCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));

        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return 'false';
            }
        } else{
            $category = new static;
        }

        $category->name = $categoryName;
        $category->client_id = Auth::guard('client')->user()->id;
        $category->save();
        return $category;
    }

    protected static function getCategoriesByClient(){
        if(Auth::guard('client')->user()){
            $clientId = Auth::guard('client')->user()->id;
            $userId = 0;
        } else {
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $userId = Auth::guard('clientuser')->user()->id;
        }
        return static::where('client_id', $clientId)->get();
    }

    protected static function isClientDiscussionCategoryExist(Request $request){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('client_id', Auth::guard('client')->user()->id)->where('name', '=',$categoryName);
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
}
