<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentsDoc;
use App\Models\DocumentsCategory;
use App\Models\RegisterDocuments;
use App\Models\RegisterFavouriteDocuments;
use App\Models\ReadNotification;
use App\Models\Notification;
use Auth,Hash,DB;

class DocumentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('auth');
    }

    /**
     *  show list of all document
     */
    protected function show($id=NULL){
        $registeredDocIds = [];
        $favouriteDocIds = [];
        $documents = DocumentsDoc::getDocumentsAssociatedWithCategory();
        $documentsCategories = DocumentsCategory::getDocumentsCategoriesAssociatedWithDocs();
        $registeredDocuments = $this->getRegisteredDocumentIds();
        $favouriteDocIds = $this->getFavouritedDocumentIds();
        if(is_object(Auth::user()) && $id > 0){
            $currentUser = Auth::user()->id;
            DB::beginTransaction();
            try
            {
                $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINDOCUMENT,$id,$currentUser);

                if(is_object($readNotification)){
                    DB::commit();
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return view('documents.documents', compact('documents', 'documentsCategories', 'registeredDocIds', 'favouriteDocIds', 'id'));
    }

    /**
     *  return documents by categoryId
     */
    protected function getDocumentsByCategoryId(Request $request){
        $categoryId = $request->get('id');
        $userId = $request->get('user_id');
        if(isset($categoryId) && empty($userId)){
            $result['documents'] = DocumentsDoc::getDocumentsByCategoryId($categoryId);
            $result['registeredDocuments'] = [];
            $result['favouriteDocIds'] = [];
        } else {
            $result['documents'] = DocumentsDoc::getRegisteredDocumentsByCategoryId($categoryId, $userId);
            $result['registeredDocuments'] = $this->getRegisteredDocumentIds();
            $result['favouriteDocIds'] = $this->getFavouritedDocumentIds();
        }
        return $result;
    }

    /**
     *  return documents by filter criteria
     */
    protected function getDocumentsBySearchArray(Request $request){
        $userId = $request->get('userId');
        if(empty($userId)){
            $result['documents'] = DocumentsDoc::getDocumentsBySearchArray($request);
            $result['registeredDocuments'] = [];
            $result['favouriteDocIds'] = [];
        } else {
            $result['documents'] = DocumentsDoc::getDocumentsBySearchArray($request);
            $result['registeredDocuments'] = $this->getRegisteredDocumentIds();
            $result['favouriteDocIds'] = $this->getFavouritedDocumentIds();
        }
        return $result;
    }

    protected function registerDocuments(Request $request){
        return RegisterDocuments::registerReadDocuments($request);
    }

    protected function registerFavouriteDocuments(Request $request){
        return RegisterFavouriteDocuments::registerFavouriteReadDocuments($request);
    }

    protected function getFavouriteDocumentsByCategoryId(Request $request){
        return RegisterFavouriteDocuments::getFavouriteDocumentsByCategoryId($request);
    }

    protected function getRegisteredDocumentIds(){
        $registeredDocumentIds = [];
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredDocuments = RegisterDocuments::getRegisteredDocumentsByUserId($userId);
            if(false == $registeredDocuments->isEmpty()){
                foreach($registeredDocuments as $registeredDocument){
                    $registeredDocumentIds[] = $registeredDocument->documents_docs_id;
                }
            }
        }
        return $registeredDocumentIds;
    }

    protected function getFavouritedDocumentIds(){
        $favouriteDocIds = [];
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $favouriteDocuments = RegisterFavouriteDocuments::getRegisteredFavouriteDocumentsByUserId($userId);
            if(false == $favouriteDocuments->isEmpty()){
                foreach($favouriteDocuments as $favouriteDocument){
                    $favouriteDocIds[] = $favouriteDocument->documents_docs_id;
                }
            }
        }
        return $favouriteDocIds;
    }
}