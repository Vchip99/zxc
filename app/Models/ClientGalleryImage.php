<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ClientGalleryType;
use DB,Auth,File;
use Intervention\Image\ImageManagerStatic as Image;

class ClientGalleryImage extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_id','client_gallery_type_id','images'];

    /**
     *  add/update
     */
    protected static function addOrUpdateClientGalleryImage( Request $request, $isUpdate=false){
        $galleryTypeId   = InputSanitise::inputInt($request->get('gallery_type'));
        $imagesStr = '';
        $galleryImage = new static;
        $galleryImage->images = $imagesStr;
        $galleryImage->client_gallery_type_id = $galleryTypeId;
        $galleryImage->client_id = Auth::guard('client')->user()->id;
        $galleryImage->save();

        $allowedImageTypes = ['image/png','image/jpeg','image/jpg'];
        if($request->exists('gallery_images')){
            foreach($request->file('gallery_images') as $file){
                if(in_array($file->getClientMimeType(), $allowedImageTypes)){
                    $imageName = $file->getClientOriginalName();
                    $clientGalleryImagesFolder = 'client_images/'.Auth::guard('client')->user()->name.'/galleryImages/'. $galleryImage->id;
                    if(!is_dir($clientGalleryImagesFolder)){
		                File::makeDirectory($clientGalleryImagesFolder, $mode = 0777, true, true);
		            }
                    $file->move($clientGalleryImagesFolder, $imageName);
                    // open image
                    $img = Image::make($clientGalleryImagesFolder."/".$imageName);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                    if(empty($imagesStr)){
                    	$imagesStr = $clientGalleryImagesFolder.'/'.$imageName;
                    } else {
                    	$imagesStr .= ','.$clientGalleryImagesFolder.'/'.$imageName;
                    }
                }
            }
        }
        $galleryImage->images = $imagesStr;
        $galleryImage->save();
        return $galleryImage;
    }

    protected static function getGalleryImagesByClientIdByTypeId($clientId,$typeId){
        return static::where('client_id', $clientId)->where('client_gallery_type_id', $typeId)->get();
    }

    protected static function deleteClientGalleryImagesByClientId($clientId){
        $images = static::where('client_id', $clientId)->get();
        if(is_object($images) && false == $images->isEmpty()){
            foreach($images as $image){
                $image->delete();
            }
        }
        return;
    }
}
