<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientOnlinePaperSection extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'duration', 'category_id', 'sub_category_id', 'subject_id', 'paper_id','client_id'];

    protected static function paperSectionsByPaperId($paperId, $clientId=NULL, $request=NULL){
        if($clientId > 0){
            return static::where('client_id', $clientId)->where('paper_id', $paperId)->get();
        } else{
            $client = InputSanitise::getCurrentClient($request);
            return static::join('clients', 'clients.id', '=', 'client_online_paper_sections.client_id')->where('clients.subdomain', $client)->where('paper_id', $paperId)->select('client_online_paper_sections.*')->get();
        }
    }
}
