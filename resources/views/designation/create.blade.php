@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Designation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Designation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($designation->id))
    <form action="{{url('admin/updateDesignation')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="designation_id" value="{{$designation->id}}">
  @else
   <form action="{{url('admin/createDesignation')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('designation')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="designation">Designation Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="designation" name="designation" value="{{($designation)?$designation->name:null}}" required="true">
        @if($errors->has('designation')) <p class="help-block">{{ $errors->first('designation') }}</p> @endif
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