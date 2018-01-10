@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Course Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Course Video </li>
    </ol>
  </section>
@stop
  <div class="container admin_div">
  @if(isset($video->id))
    <form action="{{url('admin/updateCourseVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="video_id" id="video_id" value="{{$video->id}}">
  @else
    <form action="{{url('admin/createCourseVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" required title="Course">
            <option value="">Select Course ...</option>
            @if(count($courseCourses) > 0)
              @foreach($courseCourses as $courseCourse)
                @if( isset($video->id) && $video->course_id == $courseCourse->id)
                  <option value="{{$courseCourse->id}}" selected="true">{{$courseCourse->name}}</option>
                @else
                  <option value="{{$courseCourse->id}}">{{$courseCourse->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('video')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Video Name</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <input type="text" class="form-control" name="video" id="video" value="{{$video->name}}" required="true">
        @else
          <input type="text" class="form-control" name="video" id="video" value="" placeholder="Video Name" required="true">
        @endif
        @if($errors->has('video')) <p class="help-block">{{ $errors->first('video') }}</p> @endif
        <span class="hide" id="videoError" style="color: white;">Given name is already exist with selected course.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Description</label>
      <div class="col-sm-3">
        @if(isset($video->id))
           <textarea type="text" class="form-control" name="description" required="true">{{$video->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('duration')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Duration</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <input type="text" class="form-control" name="duration" value="{{$video->duration}}" required="true">
        @else
          <input type="text" class="form-control" name="duration" value="" placeholder="Duration" required="true">
        @endif
        @if($errors->has('duration')) <p class="help-block">{{ $errors->first('duration') }}</p> @endif
      </div>
    </div>


    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="video_path" value="{{($video->video_path)?$video->video_path:NULL}}" placeholder="Add Video Path with iframe" required="true">
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchCourseVideo();">Submit</button>
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  function searchCourseVideo(){
    var video = document.getElementById('video').value;
    var course = document.getElementById('course').value;
    if(document.getElementById('video_id')){
      var videoId = document.getElementById('video_id').value;
    } else {
      var videoId = 0;
    }
    if(video && course){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isCourseVideoExist')}}",
        data:{video:video,course:course,video_id:videoId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('videoError').classList.remove('hide');
          document.getElementById('videoError').classList.add('has-error');
        } else {
          document.getElementById('videoError').classList.add('hide');
          document.getElementById('videoError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!video){
      alert('please select video.');
    } else if(!course){
      alert('please enter name.');
    }
  }
</script>

@stop