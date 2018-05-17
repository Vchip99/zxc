<?php

namespace App\Http\Controllers\AdvertisementPage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\AdvertisementPage;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class AdvertisementPageController extends Controller
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
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
    protected $validateCreateAdvertisementPage = [
        'name' => 'required',
        'price' => 'required'
    ];

	protected function show(){
		$advertisementPages = AdvertisementPage::paginate();
    	return view('advertisementPage.list', compact('advertisementPages'));
	}

	/**
     * show UI for create page
     */
    protected function create(){
    	$advertisementPage = new AdvertisementPage;
        $mainPages = AdvertisementPage::where('parent_page', 0)->get();
    	return view('advertisementPage.create', compact('advertisementPage', 'mainPages'));
    }

    /**
     *  store advertisementPage
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateAdvertisementPage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $advertisementPage = AdvertisementPage::addOrUpdateAdvertisementPage($request);
            if(is_object($advertisementPage)){
                DB::commit();
                return Redirect::to('admin/manageAdvertisementPages')->with('message', 'Advertisement Page created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageAdvertisementPages');
    }

       /**
     * edit advertisementPage
     */
    protected function edit($id){
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$advertisementPage = AdvertisementPage::find($catId);
    		if(is_object($advertisementPage)){
                $mainPages = AdvertisementPage::where('parent_page', 0)->get();
    			return view('advertisementPage.create', compact('advertisementPage', 'mainPages'));
    		}
    	}
		return Redirect::to('admin/manageAdvertisementPages');
    }

        /**
     * update advertisementPage
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateAdvertisementPage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $pageId = InputSanitise::inputInt($request->get('page_id'));
        if(isset($pageId)){
            DB::beginTransaction();
            try
            {
                $advertisementPage = AdvertisementPage::addOrUpdateAdvertisementPage($request, true);
                if(is_object($advertisementPage)){
                    DB::commit();
                    return Redirect::to('admin/manageAdvertisementPages')->with('message', 'Advertisement Page updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageAdvertisementPages');
    }

      /**
     * delete advertisementPage
     */
    protected function delete(Request $request){
    	$pageId = InputSanitise::inputInt($request->get('page_id'));
    	if(isset($pageId)){
    		$advertisementPage = AdvertisementPage::find($pageId);
    		if(is_object($advertisementPage)){
                DB::beginTransaction();
                try
                {
        			$advertisementPage->delete();
                    DB::commit();
                    return Redirect::to('admin/manageAdvertisementPages')->with('message', 'Advertisement Page deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageAdvertisementPages');
    }
}
