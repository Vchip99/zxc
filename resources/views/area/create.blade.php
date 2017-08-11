@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Area </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Area </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($area->id))
    <form action="{{url('admin/updateArea')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="area_id" value="{{$area->id}}">
  @else
   <form action="{{url('admin/createArea')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('designation')) has-error @endif">
      <label class="col-sm-2 col-form-label">Designation Name:</label>
      <div class="col-sm-3">
        <select class="form-control" name="designation" required title="Designation">
            <option value="">Select Designation</option>
            @if(count($designations) > 0)
              @foreach($designations as $designation)
                @if( $area->designation_id == $designation->id)
                  <option value="{{$designation->id}}" selected="true">{{$designation->name}}</option>
                @else
                  <option value="{{$designation->id}}">{{$designation->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          @if($errors->has('designation')) <p class="help-block">{{ $errors->first('designation') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('area')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="area">Area Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="area" name="area" value="{{($area)?$area->name:null}}" required="true">
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