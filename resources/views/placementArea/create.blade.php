@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Area </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Manage Placement Area </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementArea->id))
    <form action="{{url('admin/updatePlacementArea')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="area_id" value="{{$placementArea->id}}">
  @else
   <form action="{{url('admin/createPlacementArea')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('area')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="area">Placement Area Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="area" name="area" value="{{($placementArea)?$placementArea->name:null}}" required="true">
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
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