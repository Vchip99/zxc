<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;

class PlacementFaq extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'question', 'answer'];

    protected function addOrUpdatePlacementFaq(Request $request, $isUpdate=false){
    	$faqs = [];
    	$questionCount = $request->get('question_count');
    	$area = InputSanitise::inputInt($request->get('area'));
    	$company = InputSanitise::inputInt($request->get('company'));
    	$faqId = InputSanitise::inputInt($request->get('faq_id'));
    	if(!empty($faqId)){
    		$faq = static::find($faqId);
    		if(is_object($faq)){
    			$faq->question = $request->get('question');
    			$faq->answer   = $request->get('answer');
    			$faq->placement_area_id   = $area;
    			$faq->placement_company_id   = $company;
    			$faq->save();
    			return $faq;
    		}
    		return 'false';
    	} else {
	    	if($questionCount > 0){
	    		for($i=1; $i<=$questionCount; $i++){
	    			$question = $request->get('question_'.$i);
	    			$answer = $request->get('answer_'.$i);
	    			if(!empty($question) && !empty($answer)){
		    			$faqs[] = [
				    				'placement_area_id' => $area,
				    				'placement_company_id' => $company,
				    				'question' => trim($question),
				    				'answer' => trim($answer)
				    			];
	    			}
	    		}
	    		if(count($faqs) > 0){
	    			DB::table('placement_faqs')->insert($faqs);
	    			return 'true';
	    		}
	    	}
	    	return 'false';
    	}
    }

    public function area(){
    	return $this->belongsTo(PlacementArea::class, 'placement_area_id');
    }

    public function company(){
    	return $this->belongsTo(PlacementCompany::class, 'placement_company_id');
    }

}
