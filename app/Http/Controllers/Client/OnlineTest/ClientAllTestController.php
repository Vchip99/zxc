<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;

class ClientAllTestController extends ClientBaseController
{
	 /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateCategory = [
        'category' => 'required|string',
    ];
    protected $validateCreateSubcategory = [
        'category' => 'required',
        'name' => 'required',
        'image_path' => 'required',
    ];
    protected $validateCreateSubject = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'name' => 'required|string',
    ];

    protected $validatePaper = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'name' => 'required|string',
        'date_to_active' => 'required',
        'date_to_inactive' => 'required',
    ];

    /**
     * create all
     */
    protected function showAll($subdomainName,Request $request){
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
    	$testCategories = ClientOnlineTestCategory::showCategories($request);
    	return view('client.onlineTest.test_all', compact('testCategories', 'subdomainName','loginUser'));
    }

    /**
     *  store category
     */
    protected function storeCategory($subdomainName,Request $request){
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
                return Redirect::to('manageAllTest')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageAllTest');
    }

    /**
     *  store sub category
     */
    protected function storeSubCategory($subdomainName,Request $request){
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
                return Redirect::to('manageAllTest')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAllTest');
    }

    /**
     *  store subject
     */
    protected function storeSubject($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubject);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $testSubject = ClientOnlineTestSubject::addOrUpdateSubject($request);
            if(is_object($testSubject)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAllTest')->with('message', 'Subject created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAllTest');
    }

    /**
     *  store paper
     */
    protected function storePaper($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validatePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $paper = ClientOnlineTestSubjectPaper::addOrUpdateOnlineTestSubjectPaper($request);
            if(is_object($paper)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAllTest')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }

        return Redirect::to('manageAllTest');
    }
}