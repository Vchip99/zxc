@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Gallery Types </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Gallery </li>
      <li class="active"> Manage Gallery Types </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($galleryType->id))
    <form action="{{url('updateClientGalleryType')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="gallery_type_id" value="{{$galleryType->id}}">
  @else
   <form action="{{url('createClientGalleryType')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="name">Type Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" name="name" placeholder="name" value="{{$galleryType->name}}">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop