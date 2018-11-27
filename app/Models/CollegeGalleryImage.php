<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Auth,File,Redirect;
use App\Models\College;
use Intervention\Image\ImageManagerStatic as Image;

class CollegeGalleryImage extends Model
{
	 /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_gallery_type_id','images','created_by'];

    /**
     *  add/update
     */
    protected static function addOrUpdateCollegeGalleryImage( Request $request, $isUpdate=false){

        $galleryTypeId   = InputSanitise::inputInt($request->get('gallery_type'));
        $imagesStr = '';
        $galleryImage = new static;
        $galleryImage->images = $imagesStr;
        $galleryImage->college_gallery_type_id = $galleryTypeId;
        $galleryImage->college_id = Auth::user()->college_id;
        $galleryImage->created_by = Auth::user()->id;
        $galleryImage->save();

        $allowedImageTypes = ['image/png','image/jpeg','image/jpg'];
        if($request->exists('gallery_images')){
            foreach($request->file('gallery_images') as $file){
                if(in_array($file->getClientMimeType(), $allowedImageTypes)){
                    $imageName = $file->getClientOriginalName();
                    $collegeGalleryImagesFolder = 'collegeImages/'.Auth::user()->college_id.'/galleryImages/'. $galleryImage->id;
                    if(!is_dir($collegeGalleryImagesFolder)){
		                File::makeDirectory($collegeGalleryImagesFolder, $mode = 0777, true, true);
		            }
                    $file->move($collegeGalleryImagesFolder, $imageName);
                    // open image
                    $img = Image::make($collegeGalleryImagesFolder."/".$imageName);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                    if(empty($imagesStr)){
                    	$imagesStr = $collegeGalleryImagesFolder.'/'.$imageName;
                    } else {
                    	$imagesStr .= ','.$collegeGalleryImagesFolder.'/'.$imageName;
                    }
                }
            }
        }
        $galleryImage->images = $imagesStr;
        $galleryImage->save();
        return $galleryImage;
    }

    protected static function getGalleryImagesByCollegeIdByTypeId($collegeId,$typeId){
        return static::where('college_id', $collegeId)->where('college_gallery_type_id', $typeId)->get();
    }
}
