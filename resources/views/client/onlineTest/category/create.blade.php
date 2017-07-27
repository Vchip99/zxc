@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test </li>
      <li class="active"> Manage Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($testCategory->id))
    <form action="{{url('updateOnlineTestCategory')}}" method="POST">
    {{ method_field('PUT') }}
    <input type="hidden" name="category_id" value="{{$testCategory->id}}">
  @else
   <form action="{{url('createOnlineTestCategory')}}" method="POST">
    @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Institute Course Name:</label>
      <div class="col-sm-3">
        <select class="form-control" name="institute_course" required title="Category">
            <option value="">Select Institute Course ...</option>
            @if(count($instituteCourses) > 0)
              @foreach($instituteCourses as $instituteCourse)
                @if( $testCategory->client_institute_course_id == $instituteCourse->id)
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
    <div class="form-group row  @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="category" name="category" value="{{($testCategory)?$testCategory->name:null}}" required>
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