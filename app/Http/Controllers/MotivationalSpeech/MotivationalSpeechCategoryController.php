<?php

namespace App\Http\Controllers\MotivationalSpeech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\MotivationalSpeechCategory;
use App\Models\MotivationalSpeechVideo;
use App\Models\MotivationalSpeechDetail;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class MotivationalSpeechCategoryController extends Controller
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
    protected $validateMotivationalSpeechCategory = [
        'category' => 'required|string',
    ];


    protected function show(){
        $motivationalSpeechCategories = MotivationalSpeechCategory::paginate();
        return view('motivationalSpeechCategory.list', compact('motivationalSpeechCategories'));
    }

    protected function create(){
        $motivationalSpeechCategory = new MotivationalSpeechCategory;
        return view('motivationalSpeechCategory.create', compact('motivationalSpeechCategory'));
    }

    /**
     *  store  category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateMotivationalSpeechCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $category = MotivationalSpeechCategory::addOrUpdateMotivationalSpeechCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageMotivationalSpeechCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageMotivationalSpeechCategory');
    }

    /**
     *  edit  category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $motivationalSpeechCategory = MotivationalSpeechCategory::find($id);
            if(is_object($motivationalSpeechCategory)){
                return view('motivationalSpeechCategory.create', compact('motivationalSpeechCategory'));
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechCategory');
    }

    /**
     *  update  category
     */
    protected function update(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = MotivationalSpeechCategory::addOrUpdateMotivationalSpeechCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechCategory');
    }

    /**
     *  delete  category
     */
    protected function delete(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $motivationalSpeechCategory = MotivationalSpeechCategory::find($categoryId);
            if(is_object($motivationalSpeechCategory)){
                DB::beginTransaction();
                try
                {
                    $motivationalSpeechDetails = MotivationalSpeechDetail::where('motivational_speech_category_id', $motivationalSpeechCategory->id)->get();
                    if(is_object($motivationalSpeechDetails) && false == $motivationalSpeechDetails->isEmpty()){
                        foreach($motivationalSpeechDetails as $motivationalSpeechDetail){
                            $videos = MotivationalSpeechVideo::where('motivational_speech_detail_id', $motivationalSpeechDetail->id)->get();
                            if(is_object($videos) && false == $videos->isEmpty()){
                                foreach($videos as $video){
                                    $video->delete();
                                }
                            }
                            $motivationalSpeechDetailImageFolder = "motivationalSpeechDetailsImages/".str_replace(' ', '_', $motivationalSpeechDetail->name);
                            if(is_dir($motivationalSpeechDetailImageFolder)){
                                InputSanitise::delFolder($motivationalSpeechDetailImageFolder);
                            }
                            $motivationalSpeechDetail->delete();
                        }
                    }
                    $motivationalSpeechCategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechCategory')->with('message', 'Motivational Speech Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechCategory');
    }

    protected function isMotivationalSpeechCategoryExist(Request $request){
        return MotivationalSpeechCategory::isMotivationalSpeechCategoryExist($request);
    }
}
