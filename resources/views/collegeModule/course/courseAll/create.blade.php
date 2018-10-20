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
    <h1> Manage All </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage All </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <div class="container">
  @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
  </div>
  <!-- <div class="container admin_div">
    <form action="{{url('college/'.Session::get('college_user_url').'/createAllCourseCategory')}}" method="POST" id="submitCategoryForm">
      {{ csrf_field() }}
      <div class="form-group row  @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="category" name="category" value="" required="true">
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
          <span class="hide" id="categoryError" style="color: white;">Given name is already exist.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchCategory();">Submit</button>
        </div>
      </div>
    </form>
  </div>
  <br> -->
  <div class="container admin_div">
    <form action="{{url('college/'.Session::get('college_user_url').'/createAllCourseSubCategory')}}" method="POST" id="submitSubCategoryForm">
      {{ csrf_field() }}
      <div class="form-group row @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label">Category Name:</label>
        <div class="col-sm-3">
          <select class="form-control" name="category" id="select_category" title="Category" required="true">
              <option value="">Select Category</option>
              @if(count($courseCategories) > 0)
                @foreach($courseCategories as $courseCategory)
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                @endforeach
              @endif
            </select>
            @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
        <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="subcategory" id="subcategory" value="" required="true">
          @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
          <span class="hide" id="subcategoryError" style="color: white;">Given name is already exist with selected category.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row">
          <div class="offset-sm-2 col-sm-3" title="Submit">
            <button type="button" class="btn btn-primary" onclick="searchSubCategory();">Submit</button>
          </div>
      </div>
    </form>
  </div>
  <br>
  <div class="container admin_div">
    <form action="{{url('college/'.Session::get('college_user_url').'/createAllCourseCourse')}}" method="POST" enctype="multipart/form-data" id="submitCourseForm">
      {{ csrf_field() }}
      <div class="form-group row @if ($errors->has('category')) has-error @endif">
        <label class="col-sm-2 col-form-label">Category Name:</label>
        <div class="col-sm-3">
          <select id="course_category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
              <option value="">Select Category</option>
              @if(count($courseCategories) > 0)
                @foreach($courseCategories as $courseCategory)
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                @endforeach
              @endif
          </select>
          @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
        <label class="col-sm-2 col-form-label">Sub Category Name:</label>
        <div class="col-sm-3">
          <select id="course_subcategory" class="form-control" name="subcategory" required title="Sub Category">
            <option value="">Select Sub Category</option>
          </select>
          @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('course')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Course Name:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" name="course" id="course" value="" placeholder="Course Name" required="true">
          @if($errors->has('course')) <p class="help-block">{{ $errors->first('course') }}</p> @endif
          <span class="hide" id="courseError" style="color: white;">Given name is already exist with selected category and subcategory.Please enter another name.</span>
        </div>
      </div>
      <div class="form-group row @if ($errors->has('author')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Author Name:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control" name="author" value="" placeholder="Author Name" required="true">
          @if($errors->has('author')) <p class="help-block">{{ $errors->first('author') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('author_introduction')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Author Introduction:</label>
        <div class="col-sm-3">
            <textarea type="text" class="form-control" name="author_introduction" required="true"></textarea>
          @if($errors->has('author_introduction')) <p class="help-block">{{ $errors->first('author_introduction') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('author_image')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="image_path">Author Image:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control"  name="author_image" id="author_image" >
          @if($errors->has('author_image')) <p class="has-error">{{ $errors->first('author_image') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('description')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Course Description:</label>
        <div class="col-sm-3">
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
          @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('price')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Price:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" name="price" value="" placeholder="Price" required="true">
          @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('difficulty_level')) has-error @endif">
        <label class="col-sm-2 col-form-label">Difficulty Level:</label>
        <div class="col-sm-3">
          <select id="difficulty_level" class="form-control" name="difficulty_level" required title="Difficulty Level">
              <option value="">Select Difficulty Level</option>
              <option value="1">Beginner</option>
              <option value="2">Intermediate</option>
              <option value="3">Advance</option>
          </select>
          @if($errors->has('difficulty_level')) <p class="help-block">{{ $errors->first('difficulty_level') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('certified')) has-error @endif">
        <label for="course" class="col-sm-2 col-form-label">Certified:</label>
        <div class="col-sm-3">
            <label class="radio-inline"><input type="radio" name="certified" value="1"> Yes</label>
            <label class="radio-inline"><input type="radio" name="certified" value="0" checked> No</label>
          @if($errors->has('certified')) <p class="help-block">{{ $errors->first('certified') }}</p> @endif
        </div>
      </div>
      <div class="form-group row @if ($errors->has('image_path')) has-error @endif">
        <label class="col-sm-2 col-form-label" for="image_path">Course Image:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control" name="image_path" id="image_path" >
          @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
        </div>
      </div>
      <div class="form-group row">
        <label for="course" class="col-sm-2 col-form-label">Release Date:</label>
        <div class="col-sm-3">
          <input type="text"  class="form-control" name="release_date" id="release_date" required="true">
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
    </form>
  </div>
<script type="text/javascript">
  function searchCategory(){
    var category = document.getElementById('category').value;
    if(document.getElementById('category_id')){
      var categoryId = document.getElementById('category_id').value;
    } else {
      var categoryId = 0;
    }
    if(category){
      $.ajax({
        method:'POST',
        url: "{{url('isCourseCategoryExist')}}",
        data:{category:category,category_id:categoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('categoryError').classList.remove('hide');
          document.getElementById('categoryError').classList.add('has-error');
        } else {
          document.getElementById('categoryError').classList.add('hide');
          document.getElementById('categoryError').classList.remove('has-error');
          document.getElementById('submitCategoryForm').submit();
        }
      });
    } else {
      alert('please enter category name.');
    }
  }

  function searchSubCategory(){
    var category = document.getElementById('select_category').value;
    var subcategory = document.getElementById('subcategory').value;
    if(document.getElementById('subCategory_id')){
      var subcategoryId = document.getElementById('subCategory_id').value;
    } else {
      var subcategoryId = 0;
    }
    if(category && subcategory){
      $.ajax({
        method:'POST',
        url: "{{url('isCourseSubCategoryExist')}}",
        data:{category:category,subcategory:subcategory,subcategory_id:subcategoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('subcategoryError').classList.remove('hide');
          document.getElementById('subcategoryError').classList.add('has-error');
        } else {
          document.getElementById('subcategoryError').classList.add('hide');
          document.getElementById('subcategoryError').classList.remove('has-error');
          document.getElementById('submitSubCategoryForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please enter sub category name.');
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
        select = document.getElementById('course_subcategory');
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

  function searchCourse(){
    var category = document.getElementById('course_category').value;
    var subcategory = document.getElementById('course_subcategory').value;
    var course = document.getElementById('course').value;
    if(document.getElementById('course_id')){
      var courseId = document.getElementById('course_id').value;
    } else {
      var courseId = 0;
    }
    if(category && subcategory && course){
      $.ajax({
        method:'POST',
        url: "{{url('isCourseCourseExist')}}",
        data:{category:category,subcategory:subcategory,course:course,course_id:courseId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('courseError').classList.remove('hide');
          document.getElementById('courseError').classList.add('has-error');
        } else {
          document.getElementById('courseError').classList.add('hide');
          document.getElementById('courseError').classList.remove('has-error');
          document.getElementById('submitCourseForm').submit();
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