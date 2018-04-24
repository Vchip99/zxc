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
    <form action="{{url('updateOnlineVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="video_id" id="video_id" value="{{$video->id}}">
  @else
    <form action="{{url('createOnlineVideo')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="">Select Category</option>
            @if(count($categories) > 0)
              @foreach($categories as $category)
                @if( !empty($video->id) && $video->category_id == $category->id)
                  <option value="{{$category->id}}" selected="true">{{$category->name}}</option>
                @else
                  <option value="{{$category->id}}">{{$category->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
      <label class="col-sm-2 col-form-label">Sub Category Name:</label>
      <div class="col-sm-3">
        <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourse(this);" required title="Sub Category">
          <option value="">Select Sub Category</option>
          @if(!empty($video->id) && count($subCategories) > 0 )
            @foreach($subCategories as $subCategory)
              @if( $video->sub_category_id == $subCategory->id )
                <option value="{{ $subCategory->id }}" selected> {{ $subCategory->name }} </option>
              @else
                <option value="{{ $subCategory->id }}"> {{ $subCategory->name }} </option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label class="col-sm-2 col-form-label">Course Name</label>
      <div class="col-sm-3">
        <select id="course" class="form-control" name="course" required title="Course">
            <option value="">Select Course</option>
            @if(!empty($video->id) && count($courses) > 0)
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
      <label for="" class="col-sm-2 col-form-label">Video Name</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <input type="text" class="form-control" name="video" id="video" value="{{$video->name}}" required="true">
        @else
          <input type="text" class="form-control" name="video" id="video" value="" placeholder="Video Name" required="true">
        @endif
        @if($errors->has('video')) <p class="help-block">{{ $errors->first('video') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="" class="col-sm-2 col-form-label">Description</label>
      <div class="col-sm-3">
        @if(isset($video->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$video->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true" placeholder="Description" ></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
        <span class="hide" id="videoError" style="color: white;">Given name is already exist with selected course.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('duration')) has-error @endif">
      <label for="" class="col-sm-2 col-form-label">Duration</label>
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
      <label for="" class="col-sm-2 col-form-label">Video Path</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="video_path" value="{{($video->video_path)?$video->video_path:NULL}}" placeholder="Add video path with iframe" required="true">
      </div>
    </div>
    <div class="form-group row @if ($errors->has('is_free')) has-error @endif">
      <label for="" class="col-sm-2 col-form-label">Free Video:</label>
      <div class="col-sm-3">
          @if(isset($video->id))
          <label class="radio-inline"><input type="radio" name="is_free" value="1" @if(1 == $video->is_free) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="is_free" value="0" @if(0 == $video->is_free) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="is_free" value="1"> Yes</label>
            <label class="radio-inline"><input type="radio" name="is_free" value="0" checked> No</label>
          @endif
        @if($errors->has('is_free')) <p class="help-block">{{ $errors->first('is_free') }}</p> @endif
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

  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getOnlineSubCategories')}}",
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
              url: "{{url('getOnlineCourseByCatIdBySubCatIdForClient')}}",
              data: {category:category,sub_category_id:id}
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
        url: "{{url('isClientCourseVideoExist')}}",
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