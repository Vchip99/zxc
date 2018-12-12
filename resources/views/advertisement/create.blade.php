@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Advertisement </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-television"></i> Advertisement Page </li>
      <li class="active"> Manage Advertisement </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($advertisement->id))
    <form action="{{url('admin/updateAdvertisement')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="advertisement_id" id="advertisement_id" value="{{$advertisement->id}}">
  @else
    <form action="{{url('admin/createAdvertisement')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="image"> Image:</label>
      <div class="col-sm-3">
        @if(isset($advertisement->id))
          <input type="file" class="form-control"  name="image" id="image">
        @else
          <input type="file" class="form-control"  name="image" id="image" required>
        @endif
      </div>
        <b>Image size: width(600px) and height(100px)</b>
        @if($errors->has('image')) <p class="has-error">{{ $errors->first('image') }}</p> @endif
        @if(isset($advertisement->image))
          <b><span>Existing Image: {!! basename($advertisement->image) !!}</span></b>
        @endif

    </div>
    <div class="form-group row @if ($errors->has('url')) has-error @endif">
      <label for="url" class="col-sm-2 col-form-label">Url:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" name="url" id="url" value="{{$advertisement->url}}">
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
      @if(!empty($advertisement->id) && $advertisement->admin_id == Auth::guard('admin')->user()->id)
        <button type="submit" class="btn btn-primary">Submit</button>
      @elseif(empty($advertisement->id))
        <button type="submit" class="btn btn-primary">Submit</button>
      @else
        <a href="{{ url('admin/manageAdvertisements') }}" class="btn btn-primary">Back</a>
      @endif
      </div>
    </div>
  </div>
</form>
@stop