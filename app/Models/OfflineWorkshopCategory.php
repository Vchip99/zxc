<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;

class OfflineWorkshopCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateWorkshopCategory( Request $request, $isUpdate=false){
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
        $category->save();
        return $category;
    }

    public function workshops(){
        return $this->hasMany(WorkshopDetail::class, 'workshop_category_id');
    }

    protected static function getWorkshopCategory(){
        return static::join('offline_workshop_details', 'offline_workshop_details.offline_workshop_category_id', '=', 'offline_workshop_categories.id')->select('offline_workshop_categories.*')->groupBy('offline_workshop_categories.id')->get();
    }

    protected static function isOfflineWorkshopCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', '=',$category);
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
