<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Auth;

class ClientGalleryType extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_id','name'];

    /**
     *  add/update
     */
    protected static function addOrUpdateClientGalleryType( Request $request, $isUpdate=false){
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
        $galleryType->client_id = Auth::guard('client')->user()->id;
        $galleryType->save();
        return $galleryType;
    }
}
