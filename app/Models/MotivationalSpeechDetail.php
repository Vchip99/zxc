<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\MotivationalSpeechDetail;
use App\Models\MotivationalSpeechCategory;

class MotivationalSpeechDetail extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'motivational_speech_category_id','about', 'about_image', 'topics', 'program_details'];


    /**
     *  create/update
     */
    protected static function addOrUpdateMotivationalSpeechDetails(Request $request, $isUpdate = false){
        $components = [];
        $addComponents = [];
        $updateComponents = [];

    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $name = InputSanitise::inputString($request->get('name'));
        $motivationalSpeechId = InputSanitise::inputString($request->get('motivational_speech_id'));
        $about = $request->get('about');
        $topics = $request->get('topics');
        $programDetails = $request->get('program_details');

    	if( $isUpdate && !empty($motivationalSpeechId)){
    		$motivationalSpeechDetails = static::find($motivationalSpeechId);
    		if(!is_object($motivationalSpeechDetails)){
    			return Redirect::to('admin/manageMotivationalSpeechDetails');
    		}
    	} else {
    		$motivationalSpeechDetails = new static;
    	}
    	$motivationalSpeechDetails->name = $name;
    	$motivationalSpeechDetails->motivational_speech_category_id = $categoryId;
    	$motivationalSpeechDetails->about = $about;
        $motivationalSpeechDetails->topics = $topics;
        $motivationalSpeechDetails->program_details = $programDetails;
        if($request->exists('about_image')){
            $aboutImage = $request->file('about_image')->getClientOriginalName();
            $motivationalSpeechImageFolder = "motivationalSpeechDetailsImages/";
            $motivationalSpeechFolderPath = $motivationalSpeechImageFolder.str_replace(' ', '_', $name);
            if(!is_dir($motivationalSpeechFolderPath)){
                mkdir($motivationalSpeechFolderPath, 0777, true);
            }
            $aboutImagePath = $motivationalSpeechFolderPath ."/". $aboutImage;
            if(file_exists($aboutImagePath)){
                unlink($aboutImagePath);
            } elseif(!empty($motivationalSpeechDetails->id) && file_exists($motivationalSpeechDetails->about_image)){
                unlink($motivationalSpeechDetails->about_image);
            }
            $request->file('about_image')->move($motivationalSpeechFolderPath, $aboutImage);
            $motivationalSpeechDetails->about_image = $aboutImagePath;
        }

    	$motivationalSpeechDetails->save();
    	return $motivationalSpeechDetails;
    }

    public function category(){
    	return $this->belongsTo(MotivationalSpeechCategory::class, 'motivational_speech_category_id');
    }

    protected static function getMotivationalSpeechesByCategory(Request $request){
        $data = [];
        $results = static::where('motivational_speech_category_id', $request->id)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $data[] = [
                            'id' => $result->id,
                            'name' => $result->name,
                            'about' => $result->about,
                            'about_image' => $result->about_image,
                        ];
            }
        }
        return $data;
    }
}
