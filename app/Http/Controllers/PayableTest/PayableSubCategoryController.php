<?php

namespace App\Http\Controllers\PayableTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect, Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientScore;
use App\Models\ClientOnlinePaperSection;
use App\Models\ClientUserSolution;
use App\Models\PayableClientSubCategory;

class PayableSubCategoryController extends Controller
{
	 /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    protected $validateCreateSubcategory = [
        'name' => 'required',
        'price' => 'required',
        'monthly_price' => 'required',
        'image_path' => 'required',
    ];

    protected $validateUpdateSubcategory = [
        'name' => 'required',
        'price' => 'required',
        'monthly_price' => 'required',
    ];

    /**
     *  show all sub category
     */
    protected function show(Request $request){
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
        return view('payableTest.subcategory.list', compact('testSubCategories'));
    }

    /**
     *  show create UI for sub category
     */
    protected function create(Request $request){
        $testSubcategory = new ClientOnlineTestSubCategory;
        return view('payableTest.subcategory.create', compact('testSubcategory'));
    }

    /**
     *  store sub category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcategory = ClientOnlineTestSubCategory::addOrUpdatePayableSubCategory($request);
            if(is_object($subcategory)){
                DB::connection('mysql2')->commit();
                return Redirect::to('admin/managePayableSubCategory')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePayableSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $testSubcategory = ClientOnlineTestSubCategory::find($id);
            if(is_object($testSubcategory)){
                return view('payableTest.subcategory.create', compact('testSubcategory'));
            }
        }
        return Redirect::to('admin/managePayableSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $subcatId = InputSanitise::inputInt($request->get('subcat_id'));
        if(isset($subcatId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $subcategory = ClientOnlineTestSubCategory::addOrUpdatePayableSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('admin/managePayableSubCategory')->with('message', 'Sub Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/managePayableSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete(Request $request){
        $subcat_id = InputSanitise::inputInt($request->get('subcat_id'));
        if(isset($subcat_id)){
            $testSubcategory = ClientOnlineTestSubCategory::find($subcat_id);

            if(is_object($testSubcategory)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                        foreach($testSubcategory->subjects as $testSubject){
                            if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                                foreach($testSubject->papers as $paper){
                                    if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                        foreach($paper->questions as $question){
                                            ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
                                            $question->delete();
                                        }
                                    }
                                    ClientScore::deleteScoresByPaperId($paper->id);
                                    ClientOnlinePaperSection::deletePayablePaperSectionsByPaperId($paper->id);
                                    $paper->deletePayableRegisteredPaper();
                                    $paper->delete();
                                }
                            }
                            $testSubject->delete();
                        }
                    }
                    $testSubcategory->deletePayableSubCategoryImageFolder();
                    $payableClientSubCategories = PayableClientSubCategory::getPayableSubCategoriesBySubCategoryId($testSubcategory->id);
                    if(is_object($payableClientSubCategories) && false == $payableClientSubCategories->isEmpty()){
                        foreach($payableClientSubCategories as $payableClientSubCategory){
                            $payableClientSubCategory->end_date = date('Y-m-d');
                            $payableClientSubCategory->save();
                        }
                    }
                    $testSubcategory->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('admin/managePayableSubCategory')->with('message', 'Sub Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/managePayableSubCategory');
    }

    protected function isPayableTestSubCategoryExist(Request $request){
        return ClientOnlineTestSubCategory::isPayableTestSubCategoryExist($request);
    }
}