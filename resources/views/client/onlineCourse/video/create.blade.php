@extends('client.dashboard')
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
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($video->id))
    <form action="{{url('updateOnlineVideo')}}" method="POST" enctype="multipart/form-data">
    {{method_field('PUT')}}
    <input type="hidden" name="video_id" value="{{$video->id}}">
  @else
    <form action="{{url('createOnlineVideo')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Institute Course Name:</label>
      <div class="col-sm-3">
        <select class="form-control" name="institute_course" required title="Institute Course" onChange="selectCourse(this);">
            <option value="">Select Institute Course ...</option>
            @if(count($instituteCourses) > 0)
              @foreach($instituteCourses as $instituteCourse)
                @if( $video->client_institute_course_id == $instituteCourse->id)
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
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" required title="Course">
            <option value="">Select Course ...</option>
            @if(count($courses) > 0)
              @foreach($courses as $course)
                @if( isset($video->id) && $video->course_id == $course->id)
                  <option value="{{$course->id}}" selected="true">{{$course->name}}</option>
                @else
                  <option value="{{$course->id}}">{{$course->name}}</option>
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
          <input type="text" class="form-control" name="video" value="{{$video->name}}" required="true">
        @else
          <input type="text" class="form-control" name="video" value="" placeholder="Video Name" required="true">
        @endif
        @if($errors->has('video')) <p class="help-block">{{ $errors->first('video') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Description</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$video->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true" placeholder="Description" ></textarea>
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
          <input type="text" class="form-control" name="duration" value="" placeholder="duration" required="true">
        @endif
        @if($errors->has('duration')) <p class="help-block">{{ $errors->first('duration') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="video_path" value="{{($video->video_path)?$video->video_path:NULL}}" placeholder="Add video path with iframe" required="true">
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
  </div>
</form>

<script type="text/javascript">
  function selectCourse(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineCourseByInstituteCourseId')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('course');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Course ...';
            select.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  select.appendChild(opt);
              });
            }
          });
    }
  }
</script>

@stop