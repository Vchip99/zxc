<?php

namespace App\Http\Controllers\Discussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\DiscussionCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class DiscussionCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageDiscussion')){
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
    protected $validateCreateCategory = [
        'category' => 'required|string',
    ];

    /**
     * show all category
     */
    protected function show(){
    	$discussionCategories = DiscussionCategory::paginate();
    	return view('discussionCategory.list', compact('discussionCategories'));
    }

    /**
     *  show create Discussion category UI
     */
    protected function create(){
    	$discussionCategory = new DiscussionCategory;
    	return view('discussionCategory.create', compact('discussionCategory'));
    }

        /**
     *  store Discussion
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:discussions*');
        DB::beginTransaction();
        try
        {
            $discussionCategory = DiscussionCategory::addOrUpdateDiscussionCategory($request);
            if(is_object($discussionCategory)){
                DB::commit();
                return Redirect::to('admin/manageDiscussionCategory')->with('message', 'Discussion category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageDiscussionCategory');
    }

    /**
     *  edit Discussion category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$discussionCategory = DiscussionCategory::find($id);
    		if(is_object($discussionCategory)){
    			return view('discussionCategory.create', compact('discussionCategory'));
    		}
    	}
    	return Redirect::to('admin/manageDiscussionCategory');
    }

    /**
     *  update Discussion category
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:discussions*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
         		$discussionCategory = DiscussionCategory::addOrUpdateDiscussionCategory($request, true);
    	        if(is_object($discussionCategory)){
                    DB::commit();
    	            return Redirect::to('admin/manageDiscussionCategory')->with('message', 'Discussion category updated successfully!');
    	        }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageDiscussionCategory');
    }

    /**
     *  delete Discussion
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:discussions*');
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$discussionCategory = DiscussionCategory::find($categoryId);
    		if(is_object($discussionCategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($discussionCategory->discussionPosts) && false == $discussionCategory->discussionPosts->isEmpty()){
                        foreach($discussionCategory->discussionPosts as $discussionPost){
                            $discussionPost->deleteCommantsAndSubComments();
                            $discussionPost->delete();
                        }
                    }
        			$discussionCategory->delete();
                    DB::commit();
        			return Redirect::to('admin/manageDiscussionCategory')->with('message', 'Discussion category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageDiscussionCategory');
    }

    protected function isDiscussionCategoryExist(Request $request){
        return DiscussionCategory::isDiscussionCategoryExist($request);
    }
}