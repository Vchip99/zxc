@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> College Calendar </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-clock-o"></i> Time Table </li>
      <li class="active"> College Calendar </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($collegeCalendar->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCollegeCalender')}}" method="POST" id="submitForm" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    <input type="hidden" id="time_table_id" name="time_table_id" value="{{$collegeCalendar->id}}">
  @else
   <form action="{{url('college/'.Session::get('college_user_url').'/createCollegeCalender')}}" method="POST" id="submitForm" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="image_path">Image:</label>
      <div class="col-sm-3">
        @if(isset($collegeCalendar->id))
          <input type="file" class="form-control"  name="image_path" id="image_path" >
        @else
          <input type="file" class="form-control"  name="image_path" id="image_path" required>
        @endif
        @if(isset($collegeCalendar->image_path))
          <b><span>Existing Image: {!! basename($collegeCalendar->image_path) !!}</span></b>
        @endif
      </div>
    </div>
     <input type="hidden" name="type" value="3">
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop