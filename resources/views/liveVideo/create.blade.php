@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Live Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Live Courses </li>
      <li class="active"> Manage Live Video </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($liveVideo->id))
    <form action="{{url('admin/updateLiveVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="live_video_id" id="live_video_id" value="{{$liveVideo->id}}">
  @else
    <form action="{{url('admin/createLiveVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" required title="Course">
            <option value="">Select Course ...</option>
            @if(count($liveCourses) > 0)
              @foreach($liveCourses as $liveCourse)
                @if( isset($liveVideo->id) && $liveVideo->live_course_id == $liveCourse->id)
                  <option value="{{$liveCourse->id}}" selected="true">{{$liveCourse->name}}</option>
                @else
                  <option value="{{$liveCourse->id}}">{{$liveCourse->name}}</option>
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
        @if(isset($liveVideo->id))
          <input type="text" class="form-control" name="video" id="video" value="{{$liveVideo->name}}" required="true">
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
        @if(isset($liveVideo->id))
          <textarea type="text" class="form-control" name="description" placeholder="Description" required="true">{{$liveVideo->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" placeholder="Description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('duration')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Duration</label>
      <div class="col-sm-3">
        @if(isset($liveVideo->id))
          <input type="text" class="form-control" name="duration" value="{{$liveVideo->duration}}" required="true">
        @else
          <input type="text" class="form-control" name="duration" value="" placeholder="duration" required="true">
        @endif
        @if($errors->has('duration')) <p class="help-block">{{ $errors->first('duration') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="video_path" value="{{($liveVideo->video_path)?$liveVideo->video_path:NULL}}" placeholder="Please add video path with iframe" required="true">
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Start Date:</label>
      <div class="col-sm-3">
        <input type="Date" name="start_date" @if(isset($liveVideo->id)) value="{{$liveVideo->start_date}}" @endif required="true">
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
          <button type="button" class="btn btn-primary" onclick="searchLiveCourseVideo();">Submit</button>
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  function searchLiveCourseVideo(){
    var video = document.getElementById('video').value;
    var course = document.getElementById('course').value;
    if(document.getElementById('live_video_id')){
      var videoId = document.getElementById('live_video_id').value;
    } else {
      var videoId = 0;
    }
    if(video && course){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isLiveCourseVideoExist')}}",
        data:{video:video,course:course,live_video_id:videoId}
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