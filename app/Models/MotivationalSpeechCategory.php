<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\MotivationalSpeechDetail;

class MotivationalSpeechCategory extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update category
     */
    protected static function addOrUpdateMotivationalSpeechCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return Redirect::to('admin/manageMotivationalSpeechCategory');
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->save();
        return $category;
    }

    protected static function isMotivationalSpeechCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', $category);
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
