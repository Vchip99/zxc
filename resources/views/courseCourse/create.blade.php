@extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Course </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Course </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <div class="container admin_div">
  @if(isset($course->id))
    <form action="{{url('admin/updateCourseCourse')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="course_id" name="course_id" value="{{$course->id}}">
  @else
    <form action="{{url('admin/createCourseCourse')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="0">Select Category ...</option>
            @if(count($courseCategories) > 0)
              @foreach($courseCategories as $courseCategory)
                @if( isset($course->id) && $course->course_category_id == $courseCategory->id)
                  <option value="{{$courseCategory->id}}" selected="true">{{$courseCategory->name}}</option>
                @else
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
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
        @if(isset($course->id) && count($courseSubCategories) > 0)
          <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
            <option value="0">Select Sub Category ...</option>
            @foreach($courseSubCategories as $courseSubCategory)
              @if($course->course_sub_category_id == $courseSubCategory->id)
                <option value="{{$courseSubCategory->id}}" selected="true">{{$courseSubCategory->name}}</option>
              @else
                <option value="{{$courseSubCategory->id}}">{{$courseSubCategory->name}}</option>
              @endif
            @endforeach
          </select>
        @else
          <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
            <option value="0">Select Sub Category ...</option>
          </select>
        @endif
        @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Course Name:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <input type="text" class="form-control" name="course" id="course" value="{{$course->name}}" required="true">
        @else
          <input type="text" class="form-control" name="course" id="course" value="" placeholder="Course Name" required="true">
        @endif
        @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
        <span class="hide" id="courseError" style="color: white;">Given name is already exist with selected category and subcategory.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Name:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <input type="text" class="form-control" name="author" value="{{$course->author}}" required="true">
        @else
          <input type="text" class="form-control" name="author" value="" placeholder="Author Name" required="true">
        @endif
        @if($errors->has('author')) <p class="help-block">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author_introduction')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Introduction:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <textarea type="text" class="form-control" name="author_introduction" required="true">{{$course->author_introduction}}</textarea>
        @else
          <textarea type="text" class="form-control" name="author_introduction" required="true"></textarea>
        @endif
        @if($errors->has('author_introduction')) <p class="help-block">{{ $errors->first('author_introduction') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="image_path">Author Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="author_image" id="author_image" >
        @if($errors->has('author_image')) <p class="has-error">{{ $errors->first('author_image') }}</p> @endif
        @if(isset($course->author_image))
          <b><span>Existing Image: {!! basename($course->author_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Course Description:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$course->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('price')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Price:</label>
      <div class="col-sm-3">
        @if(isset($course->id))
          <input type="text" class="form-control" name="price" value="{{$course->price}}" required="true">
        @else
          <input type="text" class="form-control" name="price" value="" placeholder="Price" required="true">
        @endif
        @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('difficulty_level')) has-error @endif">
      <label class="col-sm-2 col-form-label">Difficulty Level:</label>
      <div class="col-sm-3">
        <select id="difficulty_level" class="form-control" name="difficulty_level" required title="Difficulty Level">
            <option value="">Select Difficulty Level ...</option>
            <option value="1" @if('1' == $course->difficulty_level ) selected @endif>Beginner</option>
            <option value="2" @if('2' == $course->difficulty_level ) selected @endif>Intermediate</option>
            <option value="3" @if('3' == $course->difficulty_level ) selected @endif>Advance</option>
        </select>
        @if($errors->has('difficulty_level')) <p class="help-block">{{ $errors->first('difficulty_level') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('certified')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Certified:</label>
      <div class="col-sm-3">
          @if(isset($course->id))
          <label class="radio-inline"><input type="radio" name="certified" value="1" @if(1 == $course->certified) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" @if(0 == $course->certified) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="certified" value="1"> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" checked> No</label>
          @endif
        @if($errors->has('certified')) <p class="help-block">{{ $errors->first('certified') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('image_path')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="image_path">Course Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="image_path" id="image_path" >
        @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
        @if(isset($course->image_path))
          <b><span>Existing Image: {!! basename($course->image_path) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Release Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="release_date" id="release_date" @if(isset($course->id)) value="{{$course->release_date}}" @endif required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#release_date').datetimepicker({format: 'YYYY-MM-DD hh:mm'});
          });
      </script>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchCourse();">Submit</button>
        </div>
      </div>
  </div>
</form>

<script type="text/javascript">

  function getCourseSubCategories(id){
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('admin/getCourseSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '0';
            opt.innerHTML = 'Select Sub Category';
            select.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  if(id == obj.id){
                    opt.selected = true;
                  }
                  select.appendChild(opt);
              });
            }
          });
    }
  }

  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    getCourseSubCategories(id);
  }
  // $(document).ready(function(){
  //   var course = document.getElementById('course_id');
  //   if( course && 0 < parseInt(course.value)){
  //       categoryId = parseInt(document.getElementById('category').value);
  //       getCourseSubCategories(categoryId);
  //   }
  // });

  function searchCourse(){
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    var course = document.getElementById('course').value;
    if(document.getElementById('course_id')){
      var courseId = document.getElementById('course_id').value;
    } else {
      var courseId = 0;
    }
    if(category && subcategory && course){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isCourseCourseExist')}}",
        data:{category:category,subcategory:subcategory,course:course,course_id:courseId}
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
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!course){
      alert('please enter name.');
    }
  }
</script>
@stop