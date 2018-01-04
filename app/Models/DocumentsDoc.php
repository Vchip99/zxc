<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\DocumentsCategory;
use DB;
use Intervention\Image\ImageManagerStatic as Image;

class DocumentsDoc extends Model
{
	// public $table = 'documents_docs';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'author', 'introduction', 'doc_category_id','is_paid', 'price', 'difficulty_level','type_of_document', 'date_of_update', 'doc_image', 'doc_pdf'];

    /**
     *  create/update document
     */
    protected static function addOrUpdateDocument( Request $request, $isUpdate=false){
        $documentName = InputSanitise::inputString($request->get('name'));
        $documentAuthor = InputSanitise::inputString($request->get('author'));
        $documentIntroduction = InputSanitise::inputString($request->get('introduction'));
        $documentCategoryId = InputSanitise::inputInt($request->get('doc_category_id'));
        $documentIsPaid = InputSanitise::inputInt($request->get('is_paid'));
        $documentPrice = strip_tags(trim($request->get('price')));
        $documentDifficultyLevel = InputSanitise::inputInt($request->get('difficulty_level'));
        $documentTypeOfDocument = InputSanitise::inputInt($request->get('type_of_document'));

        $documentDateOfUpdate = strip_tags(trim($request->get('date_of_update')));
        $documentId = InputSanitise::inputInt($request->get('document_id'));
        if( $isUpdate && isset($documentId)){
            $documentsDoc = DocumentsDoc::find($documentId);
            if(!is_object($documentsDoc)){
                return Redirect::to('admin/manageDocumentsDoc');
            }
        } else{
            $documentsDoc = new DocumentsDoc;
        }

        $documentFolderPath = public_path()."/documentStorage/".str_replace(' ', '_', $documentName);
        if(!is_dir($documentFolderPath)){
        	mkdir($documentFolderPath);
        }
        if($request->exists('doc_image')){
	        $documentFrontImage = $request->file('doc_image')->getClientOriginalName();
	        $documentFrontImagePath = $documentFolderPath."/".$documentFrontImage;
	        if(file_exists($documentFrontImagePath)){
	        	unlink($documentFrontImagePath);
	        } elseif(!empty($documentsDoc->id) && file_exists($documentsDoc->doc_image_path)){
                unlink($documentsDoc->doc_image_path);
            }
	        $request->file('doc_image')->move($documentFolderPath, $documentFrontImage);
            $dbFrontImagePath = "documentStorage/".str_replace(' ', '_', $documentName)."/".$documentFrontImage;
	    }

        if($request->exists('doc_pdf')){
	     	$projectPdf = $request->file('doc_pdf')->getClientOriginalName();
	        $projectPdfPath = $documentFolderPath."/".$projectPdf;
	        if(file_exists($projectPdfPath)){
	        	unlink($projectPdfPath);
	        }
	        $request->file('doc_pdf')->move($documentFolderPath, $projectPdf);
            $dbPdfPath = "documentStorage/".str_replace(' ', '_', $documentName)."/".$projectPdf;
	    }


        $documentsDoc->name = $documentName;
        $documentsDoc->author = $documentAuthor;
        $documentsDoc->introduction = $documentIntroduction;
        $documentsDoc->doc_category_id = $documentCategoryId;
        $documentsDoc->is_paid = $documentIsPaid;
        $documentsDoc->difficulty_level = $documentDifficultyLevel;
        $documentsDoc->type_of_document = $documentTypeOfDocument;
        $documentsDoc->date_of_update = $documentDateOfUpdate;
        $documentsDoc->price = $documentPrice;

        if(isset($dbFrontImagePath)){
            $documentsDoc->doc_image_path = $dbFrontImagePath;
             // open image
            $img = Image::make($documentsDoc->doc_image_path);
            // enable interlacing
            $img->interlace();
            // save image interlaced
            $img->save();
        }
        if(isset($dbPdfPath)){
            $documentsDoc->doc_pdf_path = $dbPdfPath;
        }

        $documentsDoc->save();
        return $documentsDoc;

    }

    /**
     *  return documents by categoryId
     */
    protected static function getDocumentsByCategoryId($id){
        $id = InputSanitise::inputInt($id);
        return DB::table('documents_docs')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->select('documents_docs.*', 'documents_categories.name as category_name')
                ->where('doc_category_id', $id)->get();
    }

    /**
     *  return documents assocaited with category
     */
    protected static function getDocumentsAssociatedWithCategory(){
        return DB::table('documents_docs')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->select('documents_docs.*', 'documents_categories.name as category_name')
                ->paginate(12);
    }

    /**
     *  return documents by filter array
     */
    protected static function getDocumentsBySearchArray(Request $request){
        $searchFilter = json_decode($request->get('arr'),true);
        $difficulty = $searchFilter['difficulty'];
        $typeOfDoc = $searchFilter['typeOfDoc'];
        $categoryId = InputSanitise::inputInt($searchFilter['categoryId']);
        $fees = $searchFilter['fees'];

        $results = DB::table('documents_docs')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->select('documents_docs.*', 'documents_categories.name as category_name');

        if(count($difficulty) > 0){
            $results->whereIn('difficulty_level', $difficulty);
        }
        if(count($typeOfDoc) > 0){
            $results->whereIn('type_of_document', $typeOfDoc);
        }
        if(count($fees) > 0 && isset($fees[0])){
            if(1 == $fees[0]){
                $results->where('price', '>', 0);
            } else {
                $results->where('price', '=', 0);
            }
        }
        if(!empty($categoryId)){
            $results->where('doc_category_id', $categoryId);
        }
        return $results->get();
    }

    protected static function allRegisterDocuments($userId){
        return DB::table('documents_docs')
                ->join('register_documents', 'register_documents.documents_docs_id', '=', 'documents_docs.id')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->where('register_documents.user_id', $userId)
                ->select('documents_docs.*', 'documents_categories.name As category_name')
                ->get();
    }

    protected static function allFavouriteRegisterDocuments($userId){
        return DB::table('documents_docs')
                ->join('register_favourite_documents', 'register_favourite_documents.documents_docs_id', '=', 'documents_docs.id')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->where('register_favourite_documents.user_id', $userId)
                ->select('documents_docs.*', 'documents_categories.name As category_name')
                ->get();
    }

    protected static function getRegisteredDocumentsByCategoryId($categoryId, $userId){
        return DB::table('documents_docs')
                ->join('register_documents', 'register_documents.documents_docs_id', '=', 'documents_docs.id')
                ->join('documents_categories', 'documents_categories.id', '=', 'documents_docs.doc_category_id')
                ->where('register_documents.user_id', $userId)
                ->where('documents_docs.doc_category_id', $categoryId)
                ->select('documents_docs.*', 'documents_categories.name As category_name')
                ->get();
    }


     /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(DocumentsCategory::class, 'doc_category_id');
    }

    public function deleteFavouriteDocuments(){
        $favouriteDocuments = RegisterFavouriteDocuments::where('documents_docs_id', $this->id)->get();
        if(is_object($favouriteDocuments) && false == $favouriteDocuments->isEmpty()){
            foreach($favouriteDocuments as $favouriteDocument){
                $favouriteDocument->delete();
            }
        }
    }

    public function deleteDocumentImageFolder(){
        $documentImageFolder = "documentStorage/".str_replace(' ', '_', $this->name);
        if(is_dir($documentImageFolder)){
            InputSanitise::delFolder($documentImageFolder);
        }
    }

}
