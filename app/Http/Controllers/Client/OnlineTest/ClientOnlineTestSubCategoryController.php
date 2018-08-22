<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientUserSolution;
use App\Models\ClientScore;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Models\ClientOnlinePaperSection;

class ClientOnlineTestSubCategoryController extends ClientBaseController
{
	 /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateSubcategory = [
        'category' => 'required',
        'name' => 'required',
        'image_path' => 'required',
    ];

    protected $validateUpdateSubcategory = [
        'category' => 'required',
        'name' => 'required',
    ];

    /**
     *  show all sub category
     */
    protected function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategories($request);
        return view('client.onlineTest.subcategory.list', compact('testSubCategories','subdomainName','loginUser'));
    }

    /**
     *  show create UI for sub category
     */
    protected function create($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $testCategories = ClientOnlineTestCategory::where('client_id', $clientId)->get();
        $testSubcategory = new ClientOnlineTestSubCategory;
        return view('client.onlineTest.subcategory.create', compact('testCategories', 'testSubcategory', 'subdomainName','loginUser'));
    }

    /**
     *  store sub category
     */
    protected function store($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcategory = ClientOnlineTestSubCategory::addOrUpdateSubCategory($subdomainName,$request);
            if(is_object($subcategory)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOnlineTestSubCategory')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineTestSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($subdomainName, $id,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $testSubcategory = ClientOnlineTestSubCategory::find($id);
            if(is_object($testSubcategory)){
                $testCategories = ClientOnlineTestCategory::where('client_id', $testSubcategory->client_id)->get();
                return view('client.onlineTest.subcategory.create', compact('testCategories', 'testSubcategory', 'subdomainName','loginUser'));
            }
        }
        return Redirect::to('manageOnlineTestSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update($subdomainName, Request $request){
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
                $subcategory = ClientOnlineTestSubCategory::addOrUpdateSubCategory($subdomainName,$request, true);
                if(is_object($subcategory)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestSubCategory')->with('message', 'Sub Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOnlineTestSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete($subdomainName, Request $request){
        $subcat_id = InputSanitise::inputInt($request->get('subcat_id'));
        if(isset($subcat_id)){
            $testSubcategory = ClientOnlineTestSubCategory::find($subcat_id);
            if(is_object($testSubcategory)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = InputSanitise::getLoginUserByGuardForClient();
                    if($testSubcategory->created_by > 0 && $loginUser->id != $testSubcategory->created_by){
                        return Redirect::to('manageOnlineTestSubCategory');
                    }
                    if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                        return Redirect::to('manageOnlineTestSubCategory');
                    }
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
                                    ClientOnlinePaperSection::deleteClientPaperSectionsByClientIdByPaperId($paper->client_id,$paper->id);
                                    $paper->deleteRegisteredPaper();
                                    $paper->delete();
                                }
                            }
                            $testSubject->delete();
                        }
                    }
                    $testSubcategory->deleteSubCategoryImageFolder($request);
                    $testSubcategory->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestSubCategory')->with('message', 'Sub Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('manageOnlineTestSubCategory');
    }

    protected function getOnlineTestCategories(Request $request){
        return ClientOnlineTestCategory::getCategoriesByInstituteCourseId($request->get('id'));
    }

    protected function isClientTestSubCategoryExist(Request $request){
        return ClientOnlineTestSubCategory::isClientTestSubCategoryExist($request);
    }
}