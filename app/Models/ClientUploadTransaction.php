<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;
use Intervention\Image\ImageManagerStatic as Image;

class ClientUploadTransaction extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','clientuser_id','image','comment','client_id' ];

    /**
     *  add offline payment
     */
    protected static function addUploadTransaction(Request $request){

        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $comment  = InputSanitise::inputString($request->get('comment'));
        $dbImagePath = '';
        $user = Auth::guard('clientuser')->user();

    	$uploadTransaction = new static;
        $uploadTransaction->client_batch_id = $clientBatchId;
        $uploadTransaction->clientuser_id = $user->id;
        $uploadTransaction->comment = $comment;
        $uploadTransaction->client_id = $user->client_id;

        $client = Client::find($user->client_id);
        $userStoragePath = "clientUserStorage/".str_replace(' ', '_', $client->name)."/".$user->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath, 0755, true);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            if(!empty($user->photo) && file_exists($user->photo)){
                unlink($user->photo);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $dbImagePath = $userStoragePath."/".$userImage;
            // open image
            $img = Image::make($dbImagePath);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }
        $uploadTransaction->image = $dbImagePath;
        $uploadTransaction->save();
        return $uploadTransaction;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'clientuser_id');
    }

    protected static function deleteClientUploadTransactionByBatchIdsByClientId($batchId,$clientId){
        $results = static::where('client_id', $clientId)->where('client_batch_id', $batchId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                unlink("$result->image");
                $result->delete();
            }
        }
    }
}
