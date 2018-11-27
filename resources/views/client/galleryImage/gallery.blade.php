@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
   @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    .btn-default{
      background-color: #3c8dbc;
      border-color: #367fa9;
      color: white;
    }
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('client.front.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/gallery.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
<!-- Start course section -->
<section id="sidemenuindex" class="v_container">
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
          <a class="btn btn-default" onClick="toggleImages('All')">All</a>
          @foreach($clientGalleryTypes as $clientGalleryType)
            <a class="btn btn-default" onClick="toggleImages({{$clientGalleryType->id}})">{{$clientGalleryType->name}}</a>
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
</section>
@stop
@section('footer')
	@include('footer.client-footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
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