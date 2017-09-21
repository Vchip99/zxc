@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Subject  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Assignment </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('updateAssignmentSubject')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="subject_id" value="{{$subject->id}}">
  @else
   <form action="{{url('createAssignmentSubject')}}" method="POST">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Institute Course Name:</label>
      <div class="col-sm-3">
        <select class="form-control" name="institute_course" required title="Institute Course">
            <option value="">Select Institute Course</option>
            @if(count($instituteCourses) > 0)
              @foreach($instituteCourses as $instituteCourse)
                @if( $subject->client_institute_course_id == $instituteCourse->id)
                  <option value="{{$instituteCourse->id}}" selected="true">{{$instituteCourse->name}}</option>
                @else
                  <option value="{{$instituteCourse->id}}">{{$instituteCourse->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
          @if($errors->has('institute_course')) <p class="help-block">{{ $errors->first('institute_course') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('subject')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="subject">Subject Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="subject" name="subject" value="{{($subject)?$subject->name:null}}" required="true">
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="submit" class="btn btn-primary" style="width: 90px !important;">Submit</button>
      </div>
    </div>
    </form>
  </div>
@stop