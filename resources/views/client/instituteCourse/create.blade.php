@extends('client.dashboard')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Create Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Institute Courses </li>
      <li class="active"> Create Courses </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($course->id))
    <form action="{{url('updateClientInstituteCourse')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="course_id" value="{{$course->id}}">
  @else
    <form action="{{url('createClientInstituteCourse')}}" method="POST">
  @endif

    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="course">Course Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="course" name="course" value="{{($course->id)?$course->name:NULL}}" required="true">
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
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