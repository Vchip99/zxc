@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Gallery Types </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-picture-o"></i> Gallery </li>
      <li class="active"> Manage Gallery Types </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
   <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeGalleryImage')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="name">Type Name:</label>
      <div class="col-sm-3">
        @if(isset($galleryImage->id))
          @if(count($collegeGalleryTypes))
            @foreach($collegeGalleryTypes as $galleryType)
              @if($galleryImage->college_gallery_type_id == $galleryType->id)
                <input type="text" class="form-control" name="gallery_type_text" value="{{$galleryType->name}}" readonly>
                <input type="hidden" name="gallery_type" value="{{$galleryType->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select name="gallery_type" class="form-control" required>
            <option value=""> Select Gallery Type</option>
            @if(count($collegeGalleryTypes))
              @foreach($collegeGalleryTypes as $galleryType)
                <option value="{{$galleryType->id}}">{{$galleryType->name}}</option>
              @endforeach
            @endif
          </select>
          @if($errors->has('gallery_type')) <p class="help-block">{{ $errors->first('gallery_type') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="gallery_images">Images:</label>
      <div class="col-sm-3">
        @if(isset($galleryImage->id))
          @foreach(explode(',',$galleryImage->images) as $index => $image)
            @if(0 == $index)
              {{ basename($image) }}
            @else
              ,{{ basename($image) }}
            @endif
          @endforeach
        @else
          <input type="file" class="form-control" name="gallery_images[]" required multiple>
          @if($errors->has('gallery_images')) <p class="help-block">{{ $errors->first('gallery_images') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
      @if(empty($galleryImage->id))
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      @else
        <a class="btn btn-primary" href="{{url('college/'.Session::get('college_user_url').'/manageCollegeGalleryImage')}}" style="width: 90px !important;">Back</a>
      @endif
      </div>
    </div>
    </form>
  </div>
@stop