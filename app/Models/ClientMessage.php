<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;
use Intervention\Image\ImageManagerStatic as Image;

class ClientMessage extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','photo','message','client_id'];

    /**
     *  add/update client message
     */
    protected static function addOrUpdateClientMessage( Request $request, $isUpdate=false){
        $messageString = InputSanitise::inputString($request->get('message'));
        $batchId   = InputSanitise::inputInt($request->get('batch'));
        $messageId   = InputSanitise::inputInt($request->get('message_id'));
        $loginUser = Auth::guard('client')->user();

        if( $isUpdate && isset($messageId)){
            $message = static::find($messageId);
            if(!is_object($message)){
            	return 'false';
            }
        } else{
            $message = new static;
        }
        $message->message = $messageString;
        $message->client_id = $loginUser->id;
        $message->client_batch_id = $batchId;
        $message->save();

     	$subdomainArr = explode('.', $loginUser->subdomain);
        $clientName = $subdomainArr[0];

        if($request->exists('photo') && !empty($request->file('photo'))){
            $messageImage = $request->file('photo')->getClientOriginalName();
            $messageImageFolder = "client_images"."/".$clientName."/"."messageImages";
            $messageImageFolderPath = $messageImageFolder.'/'.$message->id;

            if(!is_dir($messageImageFolderPath)){
                File::makeDirectory($messageImageFolderPath, $mode = 0777, true, true);
            }
            $messageImagePath = $messageImageFolderPath ."/". $messageImage;
            if(file_exists($messageImagePath)){
                unlink($messageImagePath);
            } elseif(!empty($message->id) && file_exists($message->photo)){
                unlink($message->photo);
            }
            $request->file('photo')->move($messageImageFolderPath, $messageImage);
            $message->photo = $messageImagePath;
            // open image
            $img = Image::make($message->photo);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
            $message->save();
        }
        return $message;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function getMessagesByBatchIdsByClientId($batchIds,$clientId){
    	return static::where('client_id', $clientId)->whereIn('client_batch_id', $batchIds)->orWhere('client_batch_id', 0)->orderBy('updated_at','desc')->get();
    }

    protected static function deleteMessagesByBatchIdsByClientId($batchId,$clientId){
        $results = static::where('client_id', $clientId)->where('client_batch_id', $batchId)->get();
        if(is_object($results) && false == $results->isEMpty()){
            foreach($results as $result){
                $dir = dirname($result->photo);
                InputSanitise::delFolder($dir);
                $result->delete();
            }
        }
    }
}
