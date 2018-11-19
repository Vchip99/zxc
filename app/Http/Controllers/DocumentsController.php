<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentsDoc;
use App\Models\DocumentsCategory;
use App\Models\RegisterDocuments;
use App\Models\RegisterFavouriteDocuments;
use App\Models\ReadNotification;
use App\Models\Notification;
use Auth,Hash,DB,Cache;
use App\Models\Add;

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
    protected function show(Request $request, $id=NULL){
        $registeredDocIds = [];
        $favouriteDocIds = [];
        if(empty($request->getQueryString())){
            $page = 'page=1';
        } else {
            $page = $request->getQueryString();
        }
        $documents = Cache::remember('vchip:documents:documents-'.$page,60, function() {
            return DocumentsDoc::getDocumentsAssociatedWithCategory();
        });

        $documentsCategories = Cache::remember('vchip:documents:documentsCategories',60, function() {
            return DocumentsCategory::getDocumentsCategoriesAssociatedWithDocs();
        });
        // $registeredDocIds = $this->getRegisteredDocumentIds();
        $favouriteDocIds = $this->getFavouritedDocumentIds();
        $loginUser = Auth::user();
        if(is_object($loginUser) && $id > 0){
            $currentUser = $loginUser->id;
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
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('documents.documents', compact('documents', 'documentsCategories', 'favouriteDocIds', 'id', 'ads'));
    }

    /**
     *  return documents by categoryId
     */
    protected function getDocumentsByCategoryId(Request $request){
        $categoryId = $request->get('id');
        $userId = $request->get('user_id');
        if(isset($categoryId) && empty($userId)){
            $result['documents'] = Cache::remember('vchip:documents:documents:cat-'.$categoryId,60, function() use ($categoryId){
                return DocumentsDoc::getDocumentsByCategoryId($categoryId);
            });
            $result['favouriteDocIds'] = [];
        } else {
            $result['documents'] = Cache::remember('vchip:documents:documents:cat-'.$categoryId,60, function() use ($categoryId){
                return DocumentsDoc::getDocumentsByCategoryId($categoryId);
            });
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
            $result['favouriteDocIds'] = [];
        } else {
            $result['documents'] = DocumentsDoc::getDocumentsBySearchArray($request);
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

    protected function getFavouriteDocumentsByUserId(Request $request){
        $result['documents'] = RegisterFavouriteDocuments::getFavouriteDocumentsByUserId($request->user_id);
        $result['favouriteDocIds'] = $this->getFavouritedDocumentIds();
        return $result;
    }

    protected function getRegisteredDocumentIds(){
        $registeredDocumentIds = [];
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $userId = $loginUser->id;
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
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $userId = $loginUser->id;
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