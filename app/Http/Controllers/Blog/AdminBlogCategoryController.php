<?php

namespace App\Http\Controllers\Blog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\BlogCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class AdminBlogCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageBlog')){
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
    	$blogCategories = BlogCategory::paginate();
    	return view('blogCategory.list', compact('blogCategories'));
    }

    /**
     *  show create blog category UI
     */
    protected function create(){
    	$blogCategory = new BlogCategory;
    	return view('blogCategory.create', compact('blogCategory'));
    }

        /**
     *  store blog
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:blogs*');
        DB::beginTransaction();
        try
        {
            $blogCategory = BlogCategory::addOrUpdateBlogCategory($request);
            if(is_object($blogCategory)){
                DB::commit();
                return Redirect::to('admin/manageBlogCategory')->with('message', 'Blog category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageBlogCategory');
    }

    /**
     *  edit blog category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$blogCategory = BlogCategory::find($id);
    		if(is_object($blogCategory)){
    			return view('blogCategory.create', compact('blogCategory'));
    		}
    	}
    	return Redirect::to('admin/manageBlogCategory');
    }

    /**
     *  update blog category
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:blogs*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
         		$blogCategory = BlogCategory::addOrUpdateBlogCategory($request, true);
    	        if(is_object($blogCategory)){
                    DB::commit();
    	            return Redirect::to('admin/manageBlogCategory')->with('message', 'Blog category updated successfully!');
    	        }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageBlogCategory');
    }

    /**
     *  delete blog
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:blogs*');
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$blogCategory = BlogCategory::find($categoryId);
    		if(is_object($blogCategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($blogCategory->blogs) && false == $blogCategory->blogs->isEmpty()){
                        foreach($blogCategory->blogs as $blog){
                            $blog->deleteCommantsAndSubComments();
                            $blog->deleteBlogTags();
                            $blog->delete();
                        }
                    }
        			$blogCategory->delete();
                    DB::commit();
        			return Redirect::to('admin/manageBlogCategory')->with('message', 'Blog category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageBlogCategory');
    }

    protected function isBlogCategoryExist(Request $request){
        return BlogCategory::isBlogCategoryExist($request);
    }
}