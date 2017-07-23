<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class RegisterFavouriteDocuments extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'documents_docs_id'];

    protected static function registerFavouriteReadDocuments(Request $request){
    	$userId = $request->get('user_id');
    	$documentId = $request->get('document_id');
    	if(isset($userId) && isset($documentId)){
    		$registeredReadDoc = static::firstOrNew(['user_id' => $userId, 'documents_docs_id' => $documentId]);
    		if(is_object($registeredReadDoc) && empty($registeredReadDoc->id)){
    			$registeredReadDoc->save();
    	        return 'true';
            } else {
                $registeredReadDoc->delete();
                return 'false';
            }
    	}
    }

    protected static function getFavouriteDocumentsByCategoryId($request){
    	$userId = $request->get('user_id');
    	$categoryId = $request->get('id');

    	return DB::table('documents_docs')
                ->join('register_favourite_documents', 'register_favourite_documents.documents_docs_id', '=', 'documents_docs.id')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->where('register_favourite_documents.user_id', $userId)
                ->where('documents_docs.doc_category_id', $categoryId)
                ->select('documents_docs.*', 'documents_categories.name As category_name')
                ->get();
    }

    protected static function getRegisteredFavouriteDocumentsByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function deleteRegisteredFavouriteDocumentsByUserId($userId){
        $documents = static::where('user_id', $userId)->get();
        if(is_object($documents) && false == $documents->isEmpty()){
            foreach($documents as $document){
                $document->delete();
            }
        }
        return;
    }

}
