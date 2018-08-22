<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;

class ClientOnlineSubCategoryController extends ClientBaseController
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
    protected $validateSubcategory = [
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

    /**
     *  show list of course sub category
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
    	$subCategories = ClientOnlineSubCategory::showSubCategories($request);
    	return view('client.onlineCourse.subcategory.list', compact('subCategories', 'subdomainName','loginUser'));
    }

    /**
     *  show create category UI
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
    	$categories = ClientOnlineCategory::where('client_id', $clientId)->get();
    	$subcategory = new ClientOnlineSubCategory;
    	return view('client.onlineCourse.subcategory.create', compact('subcategory','categories', 'subdomainName','loginUser'));
    }

    /**
     *  store sub category
     */
    protected function store($subdomain,Request $request){
        $v = Validator::make($request->all(), $this->validateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcategory = ClientOnlineSubCategory::addOrUpdateClientOnlineSubCategory($request);
        	if(is_object($subcategory)){
                DB::connection('mysql2')->commit();
        		return Redirect::to('manageOnlineSubCategory')->with('message', 'Sub category created successfully!');
        	}
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    	return Redirect::to('manageOnlineSubCategory');

    }

    /**
     *  edit sub category
     */
    protected function edit($subdomainName, $id, Request $request){
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
    		$subcategory = ClientOnlineSubCategory::find($id);
    		if(is_object($subcategory)){
                $categories = ClientOnlineCategory::showCategories($request);
	    		return view('client.onlineCourse.subcategory.create', compact('subcategory', 'categories', 'subdomainName','loginUser'));
    		}
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update($subdomainName,Request $request){
    	$v = Validator::make($request->all(), $this->validateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
        		$subcategory = ClientOnlineSubCategory::addOrUpdateClientOnlineSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineSubCategory')->with('message', ' sub category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete($subdomainName,Request $request){
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
    		$courseSubcategory = ClientOnlineSubCategory::find($subCategoryId);
    		if(is_object($courseSubcategory)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = InputSanitise::getLoginUserByGuardForClient();
                    if($courseSubcategory->created_by > 0 && $loginUser->id != $courseSubcategory->created_by){
                        return Redirect::to('manageOnlineSubCategory');
                    }
                    if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                        return Redirect::to('manageOnlineSubCategory');
                    }
                    if(true == is_object($courseSubcategory->courses) && false == $courseSubcategory->courses->isEmpty()){
                        $clientName = $subdomainName;
                        foreach($courseSubcategory->courses as $course){
                            if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                                foreach($course->videos as $video){
                                    $video->deleteCommantsAndSubComments();
                                    if(true == preg_match('/clientCourseVideos/',$video->video_path)){
                                        $courseVideoFolder = "clientCourseVideos/".$clientName."/".$video->course_id."/".$video->id;
                                        if(is_dir($courseVideoFolder)){
                                            InputSanitise::delFolder($courseVideoFolder);
                                        }
                                    }
                                    $video->delete();
                                }
                            }
                            $course->deleteRegisteredOnlineCourses();
                            $course->deleteCourseImageFolder($request);
                            $courseVideoFolder = "clientCourseVideos/".$clientName."/".$course->id;
                            if(is_dir($courseVideoFolder)){
                                InputSanitise::delFolder($courseVideoFolder);
                            }
                            $course->delete();
                        }
                    }
        			$courseSubcategory->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageOnlineSubCategory')->with('message', ' sub category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    protected function getOnlineCategories(Request $request){
        return ClientOnlineCategory::getCategoriesByInstituteCourseId($request->get('id'));
    }

    protected function isClientCourseSubCategoryExist(Request $request){
        return ClientOnlineSubCategory::isClientCourseSubCategoryExist($request);
    }
}
