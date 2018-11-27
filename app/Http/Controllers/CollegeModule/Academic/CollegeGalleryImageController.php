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

class CollegeGalleryImageController extends Controller
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
    protected $validateCollegeGalleryImage = [
        'gallery_type' => 'required',
        'gallery_images' => 'required'
    ];

    /**
     *  show list
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $galleryTypes = [];
        $loginUser = Auth::user();
        $galleryImages = CollegeGalleryImage::where('college_id', $loginUser->college_id)->orderBy('id', 'desc')->paginate();
        $collegeGalleryTypes = CollegeGalleryType::where('college_id', $loginUser->college_id)->get();
        if(is_object($collegeGalleryTypes) && false == $collegeGalleryTypes->isEmpty()){
            foreach($collegeGalleryTypes as $collegeGalleryType){
                $galleryTypes[$collegeGalleryType->id] = $collegeGalleryType->name;
            }
        }
        return view('collegeModule.galleryImage.list', compact('galleryImages','galleryTypes'));
    }

    /**
     *  show create
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $galleryImage = new CollegeGalleryImage;
		$collegeGalleryTypes = CollegeGalleryType::where('college_id', $loginUser->college_id)->get();
		return view('collegeModule.galleryImage.create', compact('collegeGalleryTypes','galleryImage'));
    }

    /**
     *  store
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeGalleryImage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $galleryImage = CollegeGalleryImage::addOrUpdateCollegeGalleryImage($request);
            if(is_object($galleryImage)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryImage')->with('message', 'Gallery Image created successfully!');
            }
        }
        catch(\Exception $e)
        {   dd($e);
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryImage');
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
    		$galleryImage = CollegeGalleryImage::find($id);
    		if(is_object($galleryImage)){
                $loginUser = Auth::user();
                $collegeGalleryTypes = CollegeGalleryType::where('college_id', $loginUser->college_id)->get();
                return view('collegeModule.galleryImage.create', compact('collegeGalleryTypes','galleryImage'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryImage');
    }

    /**
     *  delete
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $galleryImageId = InputSanitise::inputInt($request->get('gallery_image_id'));
        if(isset($galleryImageId)){
    		$galleryImage = CollegeGalleryImage::find($galleryImageId);
    		if(is_object($galleryImage)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($galleryImage->created_by == $loginUser->id || User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type || User::TNP == $loginUser->user_type){
                        $collegeGalleryImagesFolder = 'collegeImages/'.Auth::user()->college_id.'/galleryImages/'. $galleryImage->id;
                        InputSanitise::delFolder($collegeGalleryImagesFolder);
            			$galleryImage->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryImage')->with('message', 'Gallery Image deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeGalleryImage');
    }

    protected function gallery($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeGalleryTypes = [];
        $galleryImages = [];
        $loginUser = Auth::user();
        $collegeGalleryImages = CollegeGalleryImage::where('college_id', $loginUser->college_id)->get();
        if(is_object($collegeGalleryImages) && false == $collegeGalleryImages->isEmpty()){
            foreach($collegeGalleryImages as $galleryImage){
                $galleryImages[$galleryImage->college_gallery_type_id] = $galleryImage->images;
            }
            if(count($galleryImages) > 0){
                $galleryTypeIds = array_keys($galleryImages);
                $collegeGalleryTypes = CollegeGalleryType::find(array_unique($galleryTypeIds));
            }
        }
        return view('collegeModule.galleryImage.gallery', compact('galleryImages','collegeGalleryTypes'));
    }
}