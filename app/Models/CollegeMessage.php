<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;
use Intervention\Image\ImageManagerStatic as Image;

class CollegeMessage extends Model
{
	  /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_dept_ids','years','photo','message','created_by','start_date','end_date'];

    /**
     *  add/update message
     */
    protected static function addOrUpdateCollegeMessage(Request $request, $isUpdate=false){
        $messageString = $request->get('message');
        $departments   = implode(',', $request->get('departments'));
        if(count($request->get('years')) > 0){
        	$years   = implode(',', $request->get('years'));
        } else {
        	$years   = '1,2,3,4';
        }
        $messageId   = InputSanitise::inputInt($request->get('message_id'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if( $isUpdate && isset($messageId)){
            $message = static::find($messageId);
            if(!is_object($message)){
            	return 'false';
            }
        } else{
            $message = new static;
        }
        $message->message = $messageString;
        $message->college_id = Auth::user()->college_id;
        $message->college_dept_ids = $departments;
        $message->years = $years;
        $message->created_by = Auth::user()->id;
        $message->start_date = $startDate;
        $message->end_date = $endDate;
        $message->save();

        if($request->exists('photo') && !empty($request->file('photo'))){
            $messageImage = $request->file('photo')->getClientOriginalName();
            $collegeGalleryImagesFolder = 'collegeImages/'.Auth::user()->college_id.'/messageImages/'. $message->id;

            if(!is_dir($collegeGalleryImagesFolder)){
                File::makeDirectory($collegeGalleryImagesFolder, $mode = 0777, true, true);
            }
            $messageImagePath = $collegeGalleryImagesFolder ."/". $messageImage;
            if(file_exists($messageImagePath)){
                unlink($messageImagePath);
            } elseif(!empty($message->id) && file_exists($message->photo)){
                unlink($message->photo);
            }
            $request->file('photo')->move($collegeGalleryImagesFolder, $messageImage);
            $message->photo = $messageImagePath;

            if(in_array($request->file('photo')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($message->photo);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
            $message->save();
        }
        return $message;
    }

    protected static function getMessagesByCollegeIdByDeptByYear($collegeId,$department,$year){
        return static::where('college_id', $collegeId)->whereRaw("find_in_set($department , college_dept_ids)")->whereRaw("find_in_set($year , years)")->where('start_date','')->where('end_date','')->orderBy('updated_at','desc')->get();
    }

    protected static function getEventsByCollegeIdByDept($collegeId,$department){
        return static::where('college_id', $collegeId)->whereRaw("find_in_set($department , college_dept_ids)")->where('start_date','!=','')->where('end_date','!=','')->orderBy('start_date','asc')->get();
    }
}
