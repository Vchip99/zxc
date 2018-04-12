<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Auth;
use DB, Session;

class ClientTeam extends Model
{
	public $timestamps = false;
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_details','client_id', 'image'];

    protected static function updateTeam($request){
        $ArrVal = array_keys($request->all());
        $loginClient = Auth::guard('client')->user();
        $clientTeams = static::where('client_id', $loginClient->id)->get();
        $subdomainArr = explode('.', $loginClient->subdomain);
        $clientName = $subdomainArr[0];

        if(count($clientTeams)>0){
            foreach($clientTeams as $member){
                $memberArr = [];
                $memberInd = 'member_degignation_'.$member->id;
                $imageInd  = 'image_'.$member->id;
                if(in_array($memberInd, $ArrVal) && !empty($request->get($memberInd))){

                    $memberArr['member_details'] = $request->get($memberInd);
                }

                if(in_array($imageInd, $ArrVal) && !empty($request->file($imageInd))){
                    $memberImage = $request->file($imageInd)->getClientOriginalName();
                    $teamImageFolder = "client_images/".$clientName."/"."teamImages/";

                    if(!is_dir($teamImageFolder)){
                        mkdir($teamImageFolder, 0755, true);
                    }
                    $memberImagePath = $teamImageFolder ."/". $memberImage;
                    if(file_exists($memberImagePath)){
                        unlink($memberImagePath);
                    } elseif(!empty($member->id) && file_exists($member->image)){
                        unlink($member->image);
                    }
                    $request->file($imageInd)->move($teamImageFolder, $memberImage);
                    $memberArr['image'] = $memberImagePath;
                    // open image
                    $img = Image::make($memberImagePath);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
                if(count($memberArr) > 0){
                    $member->update($memberArr);
                }
            }

		}
		return 'true';
    }

    protected static function getClientTeam($subdomain){
    	return static::join('clients', 'clients.id', '=', 'client_teams.client_id')
    					->where('clients.subdomain', $subdomain)->select('client_teams.*')->get();
    }

    protected static function addTeam($client){
        $teamMember = ['Mr. Vishal Agarwal<br />CEO<br />M-Tech: IIT Kharagpur','Mr. Vishal Agarwal<br />CEO<br />M-Tech: IIT Kharagpur','Mr. Vishal Agarwal<br />CEO<br />M-Tech: IIT Kharagpur'];
        $teamMemberImages = [ 0 => '/images/testimonial/testimonial-1.jpg', 1 => '/images/testimonial/testimonial-2.jpg', 2 => '/images/testimonial/testimonial-3.jpg'];
        foreach($teamMember as $index => $member){
            $teamObj = new static;
            $teamObj->member_details = $member;
            $teamObj->client_id = $client->id;
            $teamObj->image = $teamMemberImages[$index];
            $teamObj->save();
        }
        return 'true';
    }
}