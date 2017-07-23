@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Blog Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Blog Category </li>
      <li class="active"> Manage Blog Category </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($blogCategory->id))
    <form action="{{url('admin/updateBlogCategory')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="category_id" value="{{$blogCategory->id}}">
  @else
   <form action="{{url('admin/createBlogCategory')}}" method="POST">
    @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="category" name="category" value="{{($blogCategory)?$blogCategory->name:null}}" required="true">
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop