<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\DocumentsCategory;
use DB;

class RegisterDocuments extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'documents_docs_id'];

    protected static function registerReadDocuments(Request $request){
    	$userId = $request->get('user_id');
    	$documentId = $request->get('document_id');
    	if(isset($userId) && isset($documentId)){
    		$registeredReadDoc = static::firstOrNew(['user_id' => $userId, 'documents_docs_id' => $documentId]);
    		if(is_object($registeredReadDoc) && empty($registeredReadDoc->id)){
    			$registeredReadDoc->save();
    		}
    		return $registeredReadDoc;
    	}
    }

    protected static function getRegisteredCategoriesByUserId($userId){
        return DB::table('documents_categories')
                ->join('documents_docs', 'documents_docs.doc_category_id', '=', 'documents_categories.id')
                ->join('register_documents', 'register_documents.documents_docs_id', '=', 'documents_docs.id')
                ->where('register_documents.user_id', $userId)
                ->select('documents_categories.id', 'documents_categories.name')
                ->groupBy('documents_categories.id')
                ->get();
    }


    protected static function getRegisteredFavouriteCategoriesByUserId($userId){
        return DB::table('documents_categories')
                ->join('documents_docs', 'documents_docs.doc_category_id', '=', 'documents_categories.id')
                ->join('register_favourite_documents', 'register_favourite_documents.documents_docs_id', '=', 'documents_docs.id')
                ->where('register_favourite_documents.user_id', $userId)
                ->select('documents_categories.id', 'documents_categories.name')
                ->groupBy('documents_categories.id')
                ->get();
    }

    protected static function getRegisteredDocumentsByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function deleteRegisteredDocsByUserId($userId){
        $documents = static::where('user_id', $userId)->get();
        if(is_object($documents) && false == $documents->isEmpty()){
            foreach($documents as $document){
                $document->delete();
            }
        }
        return;
    }

}