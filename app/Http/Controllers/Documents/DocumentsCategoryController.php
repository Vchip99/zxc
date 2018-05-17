<?php

namespace App\Http\Controllers\Documents;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\DocumentsCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class DocumentsCategoryController extends Controller
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
    protected $validateDocumentsCategory = [
        'category' => 'required|string',
    ];

    /**
     *  show list of document category
     */
    protected function show(){
    	$documentsCategories = DocumentsCategory::paginate();
    	return view('documentsCategory.list', compact('documentsCategories'));
    }

    /**
     *  show create document category UI
     */
    protected function create(){
        $documentsCategory = new DocumentsCategory;
        return view('documentsCategory.create', compact('documentsCategory'));
    }

    /**
     *  store document category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateDocumentsCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:documents*');
        DB::beginTransaction();
        try
        {
            $documentsCategory = DocumentsCategory::addOrUpdateDocumentsCategory($request);
            if(is_object($documentsCategory)){
                DB::commit();
                return Redirect::to('admin/manageDocumentsCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageDocumentsCategory');
    }

    /**
     *  edit document category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $documentsCategory = DocumentsCategory::find($id);
            if(is_object($documentsCategory)){
                return view('documentsCategory.create', compact('documentsCategory'));
            }
        }
        return Redirect::to('admin/manageDocumentsCategory');
    }

    /**
     *  update document category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateDocumentsCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:documents*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $documentsCategory = DocumentsCategory::addOrUpdateDocumentsCategory($request, true);
                if(is_object($documentsCategory)){
                    DB::commit();
                    return Redirect::to('admin/manageDocumentsCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageDocumentsCategory');
    }

    /**
     *  delete document category
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:documents*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $documentsCategory = DocumentsCategory::find($categoryId);
            if(is_object($documentsCategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($documentsCategory->documents) && false == $documentsCategory->documents->isEmpty()){
                        foreach($documentsCategory->documents as $document){
                            $document->deleteFavouriteDocuments();
                            $document->deleteDocumentImageFolder();
                            $document->delete();
                        }
                    }
                    $documentsCategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageDocumentsCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageDocumentsCategory');
    }

    protected function isDocumentCategoryExist(Request $request){
        return DocumentsCategory::isDocumentCategoryExist($request);
    }
}