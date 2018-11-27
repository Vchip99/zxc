<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeGalleryType;
use App\Models\CollegeGalleryImage;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeGalleryTypeController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
        });
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCollegeGalleryType = [
        'name' => 'required'
    ];

    /**
     *  show list
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $galleryTypes = CollegeGalleryType::where('college_id', $loginUser->college_id)->orderBy('id', 'desc')->paginate();
        return view('collegeModule.galleryType.list', compact('galleryTypes'));
    }

    /**
     *  show create
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$galleryType = new CollegeGalleryType;
		return view('collegeModule.galleryType.create', compact('galleryType'));
    }

    /**
     *  store
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeGalleryType);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $galleryType = CollegeGalleryType::addOrUpdateCollegeGalleryType($request);
            if(is_object($galleryType)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType')->with('message', 'Gallery Type created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType');
    }

    /**
     *  edit
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$galleryType = CollegeGalleryType::find($id);
    		if(is_object($galleryType)){
                return view('collegeModule.galleryType.create', compact('galleryType'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType');
    }

    /**
     *  update
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeGalleryType);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$galleryTypeId = InputSanitise::inputInt($request->get('gallery_type_id'));
    	if(isset($galleryTypeId)){
            DB::beginTransaction();
            try
            {
                $galleryType = CollegeGalleryType::addOrUpdateCollegeGalleryType($request, true);
                if(is_object($galleryType)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType')->with('message', 'Gallery Type updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType');
    }

    /**
     *  delete
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $galleryTypeId = InputSanitise::inputInt($request->get('gallery_type_id'));
        if(isset($galleryTypeId)){
    		$galleryType = CollegeGalleryType::find($galleryTypeId);
    		if(is_object($galleryType)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($galleryType->created_by == $loginUser->id || User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type || User::TNP == $loginUser->user_type){
                        $galleryImages = CollegeGalleryImage::getGalleryImagesByCollegeIdByTypeId($loginUser->college_id,$galleryType->id);
                        if(is_object($galleryImages) && false == $galleryImages->isEmpty()){
                            foreach($galleryImages as $galleryImage){
                                $collegeGalleryImagesFolder = 'collegeImages/'.$loginUser->college_id.'/galleryImages/'. $galleryImage->id;
                                InputSanitise::delFolder($collegeGalleryImagesFolder);
                                $galleryImage->delete();
                            }
                        }
            			$galleryType->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType')->with('message', 'Gallery Type deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryType');
    }

    protected function isCollegeGalleryTypeExist(Request $request){
        return CollegeGalleryType::isCollegeGalleryTypeExist($request);
    }
}