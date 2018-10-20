@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 150px;
    }
  </style>
@stop
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
    <form action="{{url('college/'.Session::get('college_user_url').'/updateCourseVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="video_id" id="video_id" value="{{$video->id}}">
  @else
    <form action="{{url('college/'.Session::get('college_user_url').'/createCourseVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        @if(!empty($video->id))
          @if(count($courseCategories) > 0)
            @foreach($courseCategories as $category)
              @if($video->course_category_id == $category->id)
                <input type="text" class="form-control" name="category_text" value="{{$category->name}}" readonly>
                <input type="hidden" class="form-control" id="category" name="category" value="{{$category->id}}">
              @endif
            @endforeach
          @endif
        @else
          <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="">Select Category</option>
            @if(count($courseCategories) > 0)
              @foreach($courseCategories as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
              @endforeach
            @endif
          </select>
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Category Name:</label>
      <div class="col-sm-3">
        @if(!empty($video->id))
          @foreach($courseSubCategories as $subCategory)
            @if($video->course_sub_category_id == $subCategory->id)
              <input type="text" name="subcategory_text" class="form-control" value="{{ $subCategory->name }}" readonly>
              <input type="hidden" id="subcategory" name="subcategory" class="form-control" value="{{ $subCategory->id }}">
            @endif
          @endforeach
        @else
          <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourse(this);" required title="Sub Category">
            <option value="">Select Sub Category</option>
            @if(!empty($video->id) && count($courseSubCategories) > 0 )
              @foreach($courseSubCategories as $subCategory)
                <option value="{{ $subCategory->id }}"> {{ $subCategory->name }} </option>
              @endforeach
            @endif
          </select>
          @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name</label>
      <div class="col-sm-3">
        @if(!empty($video->id))
          @foreach($courseCourses as $courseCourse)
            @if($video->course_id == $courseCourse->id)
              <input type="text" name="course_text" class="form-control" value="{{$courseCourse->name}}" readonly>
              <input type="hidden" id="course" name="course" class="form-control" value="{{$courseCourse->id}}">
            @endif
          @endforeach
        @else
          <select id="course" class="form-control" name="course" required title="Course">
            <option value="">Select Course</option>
            @if(!empty($video->id) && count($courseCourses) > 0)
              @foreach($courseCourses as $courseCourse)
                <option value="{{$courseCourse->id}}">{{$courseCourse->name}}</option>
              @endforeach
            @endif
          </select>
          @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
        @endif
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
    <div class="form-group row @if ($errors->has('video_source')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Video Source:</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <label class="radio-inline"><input type="radio" name="video_source" value="youtube" onclick="showPath(this);"
            @if(true == preg_match('/iframe/',$video->video_path))
              checked=true
            @endif
          > You Tube/iframe</label>
          <label class="radio-inline"><input type="radio" name="video_source" value="system" onclick="showPath(this);"
            @if(true == preg_match('/courseVideos/',$video->video_path))
              checked=true
            @endif
          > System </label>
        @else
          <label class="radio-inline"><input type="radio" name="video_source" value="youtube" checked onclick="showPath(this);"> You Tube/iframe</label>
          <label class="radio-inline"><input type="radio" name="video_source" value="system" onclick="showPath(this);"> System </label>
        @endif
        @if($errors->has('video_source')) <p class="help-block">{{ $errors->first('video_source') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('video_path')) has-error @endif"">
      <label for="course" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          @if(false == preg_match('/courseVideos/',$video->video_path))
            <input id="youtubePath" type="text" class="form-control"  name="video_path" value="{{$video->video_path}}" placeholder="Add Video Path with iframe" required="true">
            <input id="systemPath" type="file" class="form-control hide"  name="video_path" value="" required="true">
          @else
            <input id="youtubePath" type="text" class="form-control hide"  name="video_path" value="" placeholder="Add Video Path with iframe" required="true">
            <input id="systemPath" type="file" class="form-control"  name="video_path" value="" required="true">
            <b><span class="" id="existingFileName">Existing File: {!! basename($video->video_path) !!}</span></b>
          @endif
        @else
          <input id="youtubePath" type="text" class="form-control"  name="video_path" value="{{($video->video_path)?$video->video_path:NULL}}" placeholder="Add Video Path with iframe" required="true">
          <input id="systemPath" type="file" class="form-control hide"  name="video_path" required="true">
        @endif
        @if($errors->has('video_path')) <p class="help-block">{{ $errors->first('video_path') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          @if(isset($video->id) && $video->collegeCourse->created_by == Auth::User()->id)
            <button id="submitBtn" type="button" class="btn btn-primary" onclick="searchCourseVideo();">Submit</button>
          @elseif(empty($video->id))
            <button id="submitBtn" type="button" class="btn btn-primary" onclick="searchCourseVideo();">Submit</button>
          @endif
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  $('input[type="file"]').change(function(event) {
      var totalBytes = this.files[0].size;
      // check file is > 500mb
      if(totalBytes > 524288000){
        $('#submitBtn').attr('disabled', true);
        alert('please upload file less than 500mb');
      } else {
        $('#submitBtn').attr('disabled', false);
      }
    }
  );
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
        url: "{{url('isCourseVideoExist')}}",
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
      alert('please select course name.');
    }
  }
  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getCollegeCourseSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category';
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
  function selectCourse(ele){
    var id = parseInt($(ele).val());
    var category = document.getElementById('category').value;
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getCourseByCatIdBySubCatIdByUser')}}",
          data: {category:category,subcategory:id}
      })
      .done(function( msg ) {
        select = document.getElementById('course');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Course';
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
  function showPath(ele){
    if('system' == $(ele).val()){
      $('#systemPath').removeClass('hide');
      $('#existingFileName').removeClass('hide');
      $('#youtubePath').addClass('hide');
    } else {
      $('#youtubePath').removeClass('hide');
      $('#systemPath').addClass('hide');
      $('#existingFileName').addClass('hide');
    }
  }
</script>

@stop