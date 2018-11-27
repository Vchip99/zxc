@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Gallery </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-picture-o"></i> Gallery </li>
      <li class="active"> Gallery </li>
    </ol>
  </section>
  <style type="text/css">
    .btn-default{
      background-color: #3c8dbc;
      border-color: #367fa9;
      color: white;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="row">
      @if(count($collegeGalleryTypes) > 0)
        <a class="btn btn-default " onClick="toggleImages('All')">All</a>
        @foreach($collegeGalleryTypes as $galleryType)
          <a class="btn btn-default " onClick="toggleImages({{$galleryType->id}})">{{$galleryType->name}}</a>
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
                <img src="{{ url($image) }}" style="width:100%;height:300px">
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