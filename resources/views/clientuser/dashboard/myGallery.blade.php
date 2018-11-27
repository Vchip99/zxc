@extends('clientuser.dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> My Gallery </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-picture-o"></i> Gallery</li>
      <li class="active">My Gallery </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
	<div class="container ">
    <div class="row">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </div>
      @endif
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
      <div class="row">
        @if(count($clientGalleryTypes) > 0)
          <a class="btn btn-primary" onClick="toggleImages('All')">All</a>
          @foreach($clientGalleryTypes as $clientGalleryType)
            <a class="btn btn-primary" onClick="toggleImages({{$clientGalleryType->id}})">{{$clientGalleryType->name}}</a>
          @endforeach
        @endif
      </div>
      <br>
      <div class="row">
        @if(count($galleryImages) > 0)
          @foreach($galleryImages as $type => $galleryImages)
            @foreach(explode(',',$galleryImages) as $image)
            <div class="col-md-4 col-sm-6 all_images image_{{$type}}">
              <div class="thumbnail" >
                <div class="vid">
                  <img src="{{ $image }}" style="width:100%;height:300px">
                </div>
              </div>
            </div>
            @endforeach
          @endforeach
        @else
          No Images
        @endif
      </div>
    </div>
  </div>
<script type="text/javascript">
  function toggleImages(type){
    if(type > 0){
      $('.all_images').each(function(idx,obj){
        $(obj).addClass('hide');
      });
      $('.image_'+type).each(function(idx,obj){
        $(obj).removeClass('hide');
      });
    } else {
      $('.all_images').each(function(idx,obj){
        $(obj).removeClass('hide');
      });
    }
  }
</script>
@stop