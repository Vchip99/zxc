<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\WorkshopDetail;

class WorkshopCategory extends Model
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
    protected static function addOrUpdateWorkshopCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return Redirect::to('admin/manageWorkshopCategory');
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
}