<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\MotivationalSpeechDetail;

class MotivationalSpeechVideo extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'motivational_speech_detail_id', 'video_path'];

    /**
     *  add/update category
     */
    protected static function addOrUpdateMotivationalSpeechVideo( Request $request, $isUpdate=false){
        $name = InputSanitise::inputString($request->get('name'));
        $speechId   = InputSanitise::inputInt($request->get('motivational_speech_detail_id'));
        $videoId   = InputSanitise::inputInt($request->get('video_id'));
        $videoPath = $request->get('video_path');
        if( $isUpdate && isset($videoId)){
            $video = static::find($videoId);
            if(!is_object($video)){
            	return Redirect::to('admin/manageMotivationalSpeechvideos');
            }
        } else{
            $video = new static;
        }
        $video->name = $name;
        $video->motivational_speech_detail_id = $speechId;
        $video->video_path = $videoPath;
        $video->save();
        return $video;
    }

    public function motivationalspeech(){
    	return $this->belongsTo(MotivationalSpeechDetail::class, 'motivational_speech_detail_id');
    }
}
