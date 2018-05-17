<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB, Session;

class ClientTestimonial extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['testimonial', 'author', 'client_id', 'image'];

    protected static function updateTestimonials(Request $request){
    	$ArrVal = array_keys($request->all());
		$testimonials = static::where('client_id', Auth::guard('client')->user()->id)->get();
		if(count($testimonials)>0){
			foreach($testimonials as $testimonial){
                $testimonialArr = [];
				$testimonialInd = 'testimonial_'.$testimonial->id;
				$authorInd = 'author_'.$testimonial->id;
                $imageInd  = 'testimonial_image_'.$testimonial->id;
				if(in_array($testimonialInd, $ArrVal) && !empty($request->get($testimonialInd))){
					$testimonialArr['testimonial'] = $request->get($testimonialInd);
				}
                if(in_array($authorInd, $ArrVal) && !empty($request->get($authorInd))){
                    $testimonialArr['author'] =  $request->get($authorInd);
                }
                if(in_array($imageInd, $ArrVal) && !empty($request->get($imageInd))){
                    $testimonialArr['image'] =  $request->get($imageInd);
                }
                if( count($testimonialArr) > 0){
                    $testimonial->update( $testimonialArr );
                }
			}
		}
        return 'true';
    }

    protected static function getClientTestimonials($subdomain){
    	return static::join('clients', 'clients.id', '=', 'client_testimonials.client_id')
    					->where('clients.subdomain', $subdomain)->select('client_testimonials.*')->get();
    }

    protected static function addTestimonials($client){
        $testimonialArr = ['GATE THE Direction is one of the pioneers in GATE coaching for M.tech admission. It is one of the institute who setup its own notes, test series along with excellence class room teaching. It is very grateful for our regional students as they have chance to get a guidance of IITan. Thanks to Vishesh sir for their dedication and devotion toward his teaching. GATE exam is a not only way for higher education but most of PSUS are also select on the basis of GATE score.','GATE THE Direction is though me the exact meaning of the electronics engineering. The great thing about this institute is affection of Vishesh sir toward his students. He is always there for his students. Day by day important and competition in GATE exam is increasing. It’s just like mandatory exam also lots of PSUS are shortlist students for interview on the basis of GATE score. Particularly it is not only GATE oriented teaching but at first knowledge oriented teaching. There are lots of motivation section conducted, which is help to make your enthusiasm. Also GATE THE Direction provide proper material and necessary tips and tricks. After all your regular study is must. Best luck to GATE aspires.','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.'];
        foreach($testimonialArr as $testimonial){
            $testimonialObj= new static;
            $testimonialObj->testimonial = $testimonial;
            $testimonialObj->author = 'Sanket pusatkar (M.tech IIT Powai)';
            $testimonialObj->image = '<img alt="" src="/templateEditor/kcfinder/upload/images/testimonial-2.jpg" style="height:300px; width:300px" />';
            $testimonialObj->client_id = $client->id;
            $testimonialObj->save();
        }
        return 'true';
    }
}
