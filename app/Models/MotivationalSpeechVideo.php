<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\MotivationalSpeechDetail;
use App\Models\MotivationalSpeechCategory;

class MotivationalSpeechVideo extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'motivational_speech_detail_id', 'video_path', 'motivational_speech_category_id'];

    /**
     *  add/update category
     */
    protected static function addOrUpdateMotivationalSpeechVideo( Request $request, $isUpdate=false){
        $name = InputSanitise::inputString($request->get('name'));
        $speechId   = InputSanitise::inputInt($request->get('motivational_speech_detail_id'));
        $categoryId   = InputSanitise::inputInt($request->get('motivational_speech_category_id'));
        $videoId   = InputSanitise::inputInt($request->get('motivational_video_id'));
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
        $video->motivational_speech_category_id = $categoryId;
        $video->save();

        return $video;
    }

    public function motivationalspeech(){
    	return $this->belongsTo(MotivationalSpeechDetail::class, 'motivational_speech_detail_id');
    }

    public function motivationalspeaker(){
        return $this->belongsTo(MotivationalSpeechCategory::class, 'motivational_speech_category_id');
    }

    protected static function isMotivationalSpeechVideoExist(Request $request){
        $video = InputSanitise::inputString($request->get('video'));
        $speechId   = InputSanitise::inputInt($request->get('speech'));
        $categoryId   = InputSanitise::inputInt($request->get('category'));
        $videoId   = InputSanitise::inputInt($request->get('video_id'));
        $result = static::where('motivational_speech_category_id', $categoryId)->where('motivational_speech_detail_id', $speechId)->where('name', $video);
        if(!empty($videoId)){
            $result->where('id', '!=', $videoId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
