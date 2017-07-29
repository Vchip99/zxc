<?php

namespace App\Http\Controllers\Documents;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\DocumentsDoc;
use App\Models\DocumentsCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class DocumentsDocController extends Controller
{
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageDocument')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateDocumentsDoc = [
        'name' => 'required|string',
        'author' => 'required|string',
        'introduction' => 'required|string',
        'doc_category_id' => 'required',
        'is_paid' => 'required|integer',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'type_of_document' => 'required|integer',
        'date_of_update' => 'required|date',
        'doc_image' => 'required|image',
        'doc_pdf' => 'required',
    ];

    protected $validateUpdateDocumentsDoc = [
        'name' => 'required|string',
        'author' => 'required|string',
        'introduction' => 'required|string',
        'doc_category_id' => 'required',
        'is_paid' => 'required|integer',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'type_of_document' => 'required|integer',
        'date_of_update' => 'required|date',
    ];

    /**
     *  show list of documents
     */
    protected function show(){
    	$documentsDocs = DocumentsDoc::paginate();
    	return view('documentsDoc.list', compact('documentsDocs'));
    }

    /**
     *  show acreate document UI
     */
    protected function create(){
        $documentsDoc = new DocumentsDoc;
        $documentsCategories = DocumentsCategory::all();
        return view('documentsDoc.create', compact('documentsDoc', 'documentsCategories'));
    }

    /**
     *  store document
     */
    protected function store( Request $request){
        $v = Validator::make($request->all(), $this->validateDocumentsDoc);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $documentsDoc = DocumentsDoc::addOrUpdateDocument($request);
            if(is_object($documentsDoc)){
                DB::commit();
                return Redirect::to('admin/manageDocumentsDoc')->with('message', 'Document created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageDocumentsDoc');
    }

    /**
     *  edit document
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $documentsDoc = DocumentsDoc::find($id);
            if(is_object($documentsDoc)){
                $documentsCategories = DocumentsCategory::all();
                return view('documentsDoc.create', compact('documentsDoc', 'documentsCategories'));
            }
        }
        return Redirect::to('admin/manageDocumentsDoc');
    }

    /**
     *  update document
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateDocumentsDoc);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $documentId = InputSanitise::inputInt($request->get('document_id'));
        if(isset($documentId)){
            DB::beginTransaction();
            try
            {
                $documentsDoc = DocumentsDoc::addOrUpdateDocument($request, true);
                if(is_object($documentsDoc)){
                    DB::commit();
                    return Redirect::to('admin/manageDocumentsDoc')->with('message', 'Document updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageDocumentsDoc');
    }

    /**
     *  delete document
     */
    protected function delete( Request $request){
        $documentId = InputSanitise::inputInt($request->get('document_id'));
        if(isset($documentId)){
            $documentsDoc = DocumentsDoc::find($documentId);
            if(is_object($documentsDoc)){
                DB::beginTransaction();
                try
                {
                    $documentsDoc->deleteFavouriteDocuments();
                    $documentsDoc->deleteDocumentImageFolder();
                    $documentsDoc->delete();
                    DB::commit();
                    return Redirect::to('admin/manageDocumentsDoc')->with('message', 'Document deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageDocumentsDoc');
    }
}