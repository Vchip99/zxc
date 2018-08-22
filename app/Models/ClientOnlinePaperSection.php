<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;

class ClientOnlinePaperSection extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'duration', 'category_id', 'sub_category_id', 'subject_id', 'paper_id','client_id','created_by'];

    protected static function paperSectionsByPaperId($paperId, $clientId=NULL, $request=NULL){
        if($clientId > 0){
            return static::where('client_id', $clientId)->where('paper_id', $paperId)->get();
        } else{
            $client = InputSanitise::getCurrentClient($request);
            return static::join('clients', 'clients.id', '=', 'client_online_paper_sections.client_id')->where('clients.subdomain', $client)->where('paper_id', $paperId)->select('client_online_paper_sections.*')->get();
        }
    }

    protected static function payablePaperSectionsByPaperId($paperId){
        return static::where('client_id', 0)->where('category_id', 0)->where('paper_id', $paperId)->get();
    }

    protected static function deletePayablePaperSectionsByPaperId($paperId){
        $results = static::where('client_id', 0)->where('category_id', 0)->where('paper_id', $paperId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }

    protected static function deleteClientPaperSectionsByClientIdByPaperId($clientId,$paperId){
        $results = static::where('client_id', $clientId)->where('paper_id', $paperId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }

    protected static function deleteClientPaperSectionsByClientId($clientId){
        $results = static::where('client_id', $clientId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }

    protected static function assignClientTestPaperSectionsToClientByClientIdByTeacherId($clientId,$teacherId){
        $paperSections = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($paperSections) && false == $paperSections->isEmpty()){
            foreach($paperSections as $paperSection){
                $paperSection->created_by = 0;
                $paperSection->save();
            }
        }
    }
}
