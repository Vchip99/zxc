<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use App\Libraries\InputSanitise;

class DocumentsCategory extends Model
{
	public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  create/update document category
     */
    protected static function addOrUpdateDocumentsCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $documentsCategory = static::find($categoryId);
            if(!is_object($documentsCategory)){
            	return Redirect::to('admin/manageDocumentsCategory');
            }
        } else{
            $documentsCategory = new static;
        }
        $documentsCategory->name = $categoryName;
        $documentsCategory->save();
        return $documentsCategory;
    }

    /**
     *  return all document categories
     */
    protected function getDocumentsCategoriesAssociatedWithDocs(){
        return DB::table('documents_categories')
                    ->join('documents_docs','documents_docs.doc_category_id', '=', 'documents_categories.id')
                    ->select('documents_categories.*')->groupBy('documents_categories.id')
                    ->get();
    }

    public function documents(){
        return $this->hasMany(DocumentsDoc::class, 'doc_category_id');
    }

    protected static function isDocumentCategoryExist(Request $request){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', $categoryName);
        if(!empty($categoryId)){
            $result->where('id', '!=', $categoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
