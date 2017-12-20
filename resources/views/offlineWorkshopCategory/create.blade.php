@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-space-shuttle"></i> Offline Workshop </li>
      <li class="active"> Manage Category </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;

<div class="container admin_div">
  @if(isset($workshopCategory->id))
    <form action="{{url('admin/updateOfflineWorkshopCategory')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="category_id" value="{{$workshopCategory->id}}">
  @else
    <form action="{{url('admin/createOfflineWorkshopCategory')}}" method="POST">
  @endif
      {{ csrf_field() }}
      <div class="form-group row  @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="category" name="category" value="{{($workshopCategory->id)?$workshopCategory->name:NULL}}" required="true">
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
</div>
@stop