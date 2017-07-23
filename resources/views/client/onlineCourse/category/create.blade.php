@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($category->id))
    <form action="{{url('updateOnlineCategory')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="category_id" value="{{$category->id}}">
  @else
    <form action="{{url('createOnlineCategory')}}" method="POST">
  @endif

    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="category" name="category" value="{{($category->id)?$category->name:NULL}}" required="true">
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        <button type="submit" class="btn btn-primary" title="Submit">Submit</button>
      </div>
    </div>
  </form>
</div>
@stop