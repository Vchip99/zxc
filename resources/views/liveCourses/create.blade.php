@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Live Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-pie-chart"></i> Live Courses </li>
      <li class="active"> Manage Live Courses </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container admin_div">
  @if(isset($liveCourse->id))
    <form action="{{url('admin/updateLiveCourse')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="live_course_id" name="live_course_id" value="{{$liveCourse->id}}">
  @else
    <form action="{{url('admin/createLiveCourse')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        <select id="category" class="form-control" name="category" required title="Category">
            <option value="">Select Category ...</option>
            <option value="1" @if('1' == $liveCourse->category_id ) selected @endif>Technology</option>
            <option value="2" @if('2' == $liveCourse->category_id ) selected @endif>Science</option>
        </select>
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        @if(isset($liveCourse->id))
          <input type="text" class="form-control" name="course" id="course" value="{{$liveCourse->name}}" required="true">
        @else
          <input type="text" class="form-control" name="course" id="course" value="" placeholder="Course Name" required="true">
        @endif
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
        <span class="hide" id="courseError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Name:</label>
      <div class="col-sm-3">
        @if(isset($liveCourse->id))
          <input type="text" class="form-control" name="author" value="{{$liveCourse->author}}" required="true">
        @else
          <input type="text" class="form-control" name="author" value="" placeholder="Author Name" required="true">
        @endif
        @if($errors->has('author')) <p class="help-block">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author_introduction')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Introduction:</label>
      <div class="col-sm-3">
        @if(isset($liveCourse->id))
          <textarea type="text" class="form-control" name="author_introduction" required="true">{{$liveCourse->author_introduction}}</textarea>
        @else
          <textarea type="text" class="form-control" name="author_introduction" required="true"></textarea>
        @endif
        @if($errors->has('author_introduction')) <p class="help-block">{{ $errors->first('author_introduction') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="author_image">Author Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="author_image" id="author_image" >
        @if($errors->has('author_image')) <p class="has-error">{{ $errors->first('author_image') }}</p> @endif
        @if(isset($liveCourse->author_image))
          <b><span>Existing Image: {!! basename($liveCourse->author_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('image_path')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="image_path">Course Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="image_path" id="image_path" >
        @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
        @if(isset($liveCourse->image_path))
          <b><span>Existing Image: {!! basename($liveCourse->image_path) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Live Course Description:</label>
      <div class="col-sm-3">
        @if(isset($liveCourse->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$liveCourse->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>

    <div class="form-group row @if ($errors->has('difficulty_level')) has-error @endif">
      <label class="col-sm-2 col-form-label">Difficulty Level:</label>
      <div class="col-sm-3">
        <select id="difficulty_level" class="form-control" name="difficulty_level" required title="Difficulty Level">
            <option value="">Select Difficulty Level ...</option>
            <option value="1" @if('1' == $liveCourse->difficulty_level ) selected @endif>Beginner</option>
            <option value="2" @if('2' == $liveCourse->difficulty_level ) selected @endif>Intermediate</option>
            <option value="3" @if('3' == $liveCourse->difficulty_level ) selected @endif>Advance</option>
        </select>
        @if($errors->has('difficulty_level')) <p class="help-block">{{ $errors->first('difficulty_level') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('certified')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Certified:</label>
      <div class="col-sm-3">
          @if(isset($liveCourse->id))
          <label class="radio-inline"><input type="radio" name="certified" value="1" @if(1 == $liveCourse->certified) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" @if(0 == $liveCourse->certified) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="certified" value="1"> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" checked> No</label>
          @endif
        @if($errors->has('certified')) <p class="help-block">{{ $errors->first('certified') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('on_demand')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">On Demand:</label>
      <div class="col-sm-3">
          @if(isset($liveCourse->id))
          <label class="radio-inline"><input type="radio" name="on_demand" value="1" @if(1 == $liveCourse->on_demand) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="on_demand" value="0" @if(0 == $liveCourse->on_demand) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="on_demand" value="1"> Yes</label>
            <label class="radio-inline"><input type="radio" name="on_demand" value="0" checked> No</label>
          @endif
        @if($errors->has('on_demand')) <p class="help-block">{{ $errors->first('on_demand') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('price')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Price:</label>
      <div class="col-sm-3">
        @if(isset($liveCourse->id))
          <input type="text" class="form-control" name="price" value="{{$liveCourse->price}}" required="true">
        @else
          <input type="text" class="form-control" name="price" value="" placeholder="Price" required="true">
        @endif
        @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Start Date:</label>
      <!-- <div class="col-sm-3">
        <input type="Date" name="start_date" @if(isset($liveCourse->id)) value="{{$liveCourse->start_date}}" @endif required="true">
      </div> -->
      <div class='col-sm-3'>
          <input type='text' class="form-control" name="start_date" id="start_date" @if(isset($liveCourse->id)) value="{{$liveCourse->start_date}}" @endif required="true" />
      </div>
      <script type="text/javascript">
          $(function () {
              $('#start_date').datetimepicker({format: 'YYYY-MM-DD hh:mm'});
          });
      </script>
      </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">End Date:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control"  name="end_date" id="end_date" @if(isset($liveCourse->id)) value="{{$liveCourse->end_date}}" @endif required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#end_date').datetimepicker({format: 'YYYY-MM-DD hh:mm'});
          });
      </script>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
          <button type="button" class="btn btn-primary" onclick="searchCourse();">Submit</button>
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  function searchCourse(){
    var category = document.getElementById('category').value;
    var live_course = document.getElementById('course').value;
    if(document.getElementById('live_course_id')){
      var courseId = document.getElementById('live_course_id').value;
    } else {
      var courseId = 0;
    }
    if(live_course){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isLiveCourseExist')}}",
        data:{category:category,live_course:live_course,live_course_id:courseId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('courseError').classList.remove('hide');
          document.getElementById('courseError').classList.add('has-error');
        } else {
          document.getElementById('courseError').classList.add('hide');
          document.getElementById('courseError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop