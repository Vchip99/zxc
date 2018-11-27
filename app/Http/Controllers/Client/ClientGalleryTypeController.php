<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\ClientGalleryType;
use App\Models\ClientGalleryImage;

class ClientGalleryTypeController extends ClientBaseController
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
    protected $validateClientGalleryType = [
        'name' => 'required'
    ];

    protected function show($subdomainName){
        $galleryTypes = ClientGalleryType::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate();
        return view('client.galleryType.list', compact('galleryTypes','subdomainName'));
    }

    /**
     *  create
     */
    protected function create($subdomainName){
        $galleryType = new ClientGalleryType;
        return view('client.galleryType.create', compact('galleryType', 'subdomainName'));
    }

    /**
     *  store
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientGalleryType);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $galleryType = ClientGalleryType::addOrUpdateClientGalleryType($request);
            if(is_object($galleryType)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageGalleryTypes')->with('message', 'Gallery Type created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create gallery type.');
        }
        return Redirect::to('manageGalleryTypes');
    }

    /**
     *  edit
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $galleryType = ClientGalleryType::find($id);
            if(is_object($galleryType)){
                return view('client.galleryType.create', compact('galleryType', 'subdomainName'));
            }
        }
        return Redirect::to('manageGalleryTypes');
    }

    /**
     *  update
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientGalleryType);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $galleryTypeId = InputSanitise::inputInt($request->get('gallery_type_id'));
        if(isset($galleryTypeId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $galleryType = ClientGalleryType::addOrUpdateClientGalleryType($request, true);
                if(is_object($galleryType)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageGalleryTypes')->with('message', 'Gallery Type updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update gallery type.');
            }
        }
        return Redirect::to('manageGalleryTypes');
    }

    /**
     *  delete
     */
    protected function delete(Request $request){
        $galleryTypeId = InputSanitise::inputInt($request->get('gallery_type_id'));
        $galleryType = ClientGalleryType::find($galleryTypeId);
        if(is_object($galleryType)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $galleryImages = ClientGalleryImage::getGalleryImagesByClientIdByTypeId(Auth::guard('client')->user()->id,$galleryType->id);
                if(is_object($galleryImages) && false == $galleryImages->isEmpty()){
                    foreach($galleryImages as $galleryImage){
                        $clientGalleryImagesFolder = 'client_images/'.Auth::guard('client')->user()->name.'/galleryImages/'. $galleryImage->id;
                        InputSanitise::delFolder($clientGalleryImagesFolder);
                        $galleryImage->delete();
                    }
                }
                $galleryType->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageGalleryTypes')->with('message', 'Gallery Type deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete gallery type.');
            }
        }
        return Redirect::to('manageGalleryTypes');
    }
}