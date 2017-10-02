@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Company </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Placement </li>
      <li class="active"> Manage Placement Company </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($placementCompany->id))
    <form action="{{url('admin/updatePlacementCompany')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="company_id" value="{{$placementCompany->id}}">
  @else
      <form action="{{url('admin/createPlacementCompany')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('area')) has-error @endif">
    <label class="col-sm-2 col-form-label">Placement Area:</label>
    <div class="col-sm-3">
      <select class="form-control" name="area" required title="Placement Area">
          <option value="">Select Placement Area</option>
          @if(count($placementAreas) > 0)
            @foreach($placementAreas as $placementArea)
              @if( $placementCompany->placement_area_id == $placementArea->id)
                <option value="{{$placementArea->id}}" selected="true">{{$placementArea->name}}</option>
              @else
                <option value="{{$placementArea->id}}">{{$placementArea->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('company')) has-error @endif">
    <label for="company" class="col-sm-2 col-form-label">Placement Company Name:</label>
    <div class="col-sm-3">
      @if(isset($placementCompany))
        <input type="text" class="form-control" name="company" value="{{$placementCompany->name}}" required="true">
      @else
        <input type="text" class="form-control" name="company" value="" required="true">
      @endif
      @if($errors->has('company')) <p class="help-block">{{ $errors->first('company') }}</p> @endif
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