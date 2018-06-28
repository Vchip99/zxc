<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class AdvertisementPage extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'url', 'price', 'parent_page'];

    /**
     *  add/update advertisement page
     */
    protected static function addOrUpdateAdvertisementPage( Request $request, $isUpdate=false){
        $name = InputSanitise::inputString($request->get('name'));
        $price   = InputSanitise::inputString($request->get('price'));
        $url   = $request->get('url');
        $pageId   = InputSanitise::inputInt($request->get('page_id'));
        $pageType   = InputSanitise::inputInt($request->get('page_type'));
        if(1 == $pageType){
            $parentPage = 0;
        } else {
            $parentPage = InputSanitise::inputInt($request->get('parent_page'));
        }
        if( $isUpdate && isset($pageId)){
            $advertisementPage = static::find($pageId);
            if(!is_object($advertisementPage)){
            	return 'false';
            }
        } else{
            $advertisementPage = new static;
        }
        $advertisementPage->name = $name;
        $advertisementPage->url = $url;
        $advertisementPage->price = $price;
        $advertisementPage->parent_page = $parentPage;
        $advertisementPage->save();
        return $advertisementPage;
    }
}
