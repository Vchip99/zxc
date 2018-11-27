<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeGalleryType extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','name','created_by'];

    /**
     *  add/update
     */
    protected static function addOrUpdateCollegeGalleryType( Request $request, $isUpdate=false){
        $typeName = InputSanitise::inputString($request->get('name'));
        $galleryTypeId   = InputSanitise::inputInt($request->get('gallery_type_id'));
        if( $isUpdate && isset($galleryTypeId)){
            $galleryType = static::find($galleryTypeId);
            if(!is_object($galleryType)){
            	return 'false';
            }
        } else{
            $galleryType = new static;
        }
        $galleryType->name = $typeName;
        $galleryType->college_id = Auth::user()->college_id;
        $galleryType->created_by = Auth::user()->id;
        $galleryType->save();
        return $galleryType;
    }

    protected static function isCollegeGalleryTypeExist(Request $request){
        $typeName = $request->get('name');
        $galleryTypeId   = $request->get('gallery_type_id');
        $loginUser = Auth::guard('web')->user();
        $result = static::where('name', '=',$typeName)->where('college_id', $loginUser->college_id);
        if($galleryTypeId > 0){
            $result->where('id', '!=', $galleryTypeId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
