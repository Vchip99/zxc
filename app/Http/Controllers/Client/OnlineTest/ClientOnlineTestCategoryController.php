<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\PayableClientSubCategory;
use App\Models\ClientUserSolution;
use App\Models\ClientScore;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Models\ClientOnlinePaperSection;

class ClientOnlineTestCategoryController extends ClientBaseController
{
	 /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateCategory = [
        'category' => 'required|string',
    ];

    /**
     * show all category
     */
    protected function show(Request $request){
        $isPurchasedSubCategories = [];
    	$testCategories = ClientOnlineTestCategory::showCategories($request);
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId(Auth::guard('client')->user()->id);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $isPurchasedSubCategories[] = $payableSubCategory->category_id;
            }
        }
    	return view('client.onlineTest.category.list', compact('testCategories', 'isPurchasedSubCategories'));
    }

    /**
     * show UI for create category
     */
    protected function create(){
    	$testCategory = new ClientOnlineTestCategory;
    	return view('client.onlineTest.category.create', compact('testCategory'));
    }

    /**
     *  store category
     */
    protected function store($subdomain,Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $category = ClientOnlineTestCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOnlineTestCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * edit category
     */
    protected function edit($subdomain, $id){
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$testCategory = ClientOnlineTestCategory::find($catId);
    		if(is_object($testCategory)){
    			return view('client.onlineTest.category.create', compact('testCategory'));
    		}
    	}
		return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * update category
     */
    protected function update($subdomain,Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $category = ClientOnlineTestCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * delete category
     */
    protected function delete($subdomain,Request $request){
    	$catId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($catId)){
    		$category = ClientOnlineTestCategory::find($catId);
    		if(is_object($category)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                        foreach($category->subcategories as $testSubcategory){
                            if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                                foreach($testSubcategory->subjects as $testSubject){
                                    if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                                        foreach($testSubject->papers as $paper){
                                            if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                foreach($paper->questions as $question){
                                                    ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
                                                    if($question->category_id > 0){
                                                        $question->delete();
                                                    }
                                                }
                                            }
                                            ClientScore::deleteScoresByPaperId($paper->id);
                                            $paper->deleteRegisteredPaper();
                                            if($paper->category_id > 0){
                                                ClientOnlinePaperSection::deleteClientPaperSectionsByClientIdByPaperId($paper->client_id,$paper->id);
                                                $paper->delete();
                                            }
                                        }
                                    }
                                    if($testSubject->category_id > 0){
                                        $testSubject->delete();
                                    }
                                }
                            }
                            if($testSubcategory->category_id > 0){
                                $testSubcategory->deleteSubCategoryImageFolder($request);
                                $testSubcategory->delete();
                            }
                        }
                    }
                    $client = Auth::guard('client')->user();
                    $purchasedSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($client->id, $catId);
                    if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
                        foreach($purchasedSubCategories as $purchasedSubCategory){
                            $purchasedSubCategory->end_date = date('Y-m-d');
                            $purchasedSubCategory->save();
                        }
                    }
        			$category->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('manageOnlineTestCategory');
    }

    protected function isClientTestCategoryExist(Request $request){
        return ClientOnlineTestCategory::isClientTestCategoryExist($request);
    }
}