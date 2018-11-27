<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientGalleryType;
use App\Models\ClientGalleryImage;

class ClientGalleryImageController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateClientGalleryImage = [
        'gallery_type' => 'required',
        'gallery_images' => 'required'
    ];

    protected function show($subdomainName){
        $galleryTypes = [];
        $galleryImages = ClientGalleryImage::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate();
        $clientGalleryTypes = ClientGalleryType::where('client_id', Auth::guard('client')->user()->id)->get();
        if(is_object($clientGalleryTypes) && false == $clientGalleryTypes->isEmpty()){
            foreach($clientGalleryTypes as $clientGalleryType){
                $galleryTypes[$clientGalleryType->id] = $clientGalleryType->name;
            }
        }
        return view('client.galleryImage.list', compact('galleryImages','subdomainName','galleryTypes'));
    }

    /**
     *  create
     */
    protected function create($subdomainName){
        $galleryImage = new ClientGalleryImage;
        $galleryTypes = ClientGalleryType::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.galleryImage.create', compact('galleryImage', 'subdomainName','galleryTypes'));
    }

    /**
     *  store
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientGalleryImage);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $galleryImage = ClientGalleryImage::addOrUpdateClientGalleryImage($request);
            if(is_object($galleryImage)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageGalleryImages')->with('message', 'Gallery Images created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create gallery image.');
        }
        return Redirect::to('manageGalleryImages');
    }

    /**
     *  edit
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $loginUser = Auth::guard('client')->user();
            $galleryImage = ClientGalleryImage::where('id',$id)->where('client_id',$loginUser->id)->first();
            if(is_object($galleryImage)){
                $galleryTypes = ClientGalleryType::where('client_id', $loginUser->id)->get();
                return view('client.galleryImage.create', compact('galleryImage', 'subdomainName','galleryTypes'));
            }
        }
        return Redirect::to('manageGalleryImages');
    }

    /**
     *  delete
     */
    protected function delete(Request $request){
        $galleryImageId = InputSanitise::inputInt($request->get('gallery_image_id'));
        $galleryImage = ClientGalleryImage::find($galleryImageId);
        if(is_object($galleryImage)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientGalleryImagesFolder = 'client_images/'.Auth::guard('client')->user()->name.'/galleryImages/'. $galleryImage->id;
                InputSanitise::delFolder($clientGalleryImagesFolder);
                $galleryImage->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageGalleryImages')->with('message', 'Gallery Images deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete gallery image.');
            }
        }
        return Redirect::to('manageGalleryImages');
    }
}