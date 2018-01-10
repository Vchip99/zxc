@extends('admin.master')
@section('admin_content')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Sub Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses </li>
      <li class="active"> Manage Sub Category </li>
    </ol>
  </section>
@stop
  &nbsp;
  <div class="container admin_div">
  @if(isset($courseSubcategory->id))
    <form action="{{url('admin/updateCourseSubCategory')}}" method="POST" id="submitForm">
      {{method_field('PUT')}}
      <input type="hidden" name="subCategory_id" id="subCategory_id" value="{{$courseSubcategory->id}}">
  @else
      <form action="{{url('admin/createCourseSubCategory')}}" method="POST" id="submitForm">
  @endif

    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      <select class="form-control" name="category" id="category" title="Category" required="true">
          <option value="">Select Category ...</option>
          @if(count($courseCategories) > 0)
            @foreach($courseCategories as $courseCategory)
              @if( $courseSubcategory->course_category_id == $courseCategory->id)
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
    <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      @if(isset($courseSubcategory))
        <input type="text" class="form-control" name="subcategory" id="subcategory" value="{{$courseSubcategory->name}}" required="true">
      @else
        <input type="text" class="form-control" name="subcategory" id="subcategory" value="" required="true">
      @endif
      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      <span class="hide" id="subcategoryError" style="color: white;">Given name is already exist with selected category.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchSubCategory();">Submit</button>
      </div>
    </div>
  </div>
</form>
<script type="text/javascript">
  function searchSubCategory(){
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    if(document.getElementById('subCategory_id')){
      var subcategoryId = document.getElementById('subCategory_id').value;
    } else {
      var subcategoryId = 0;
    }
    if(category && subcategory){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isCourseSubCategoryExist')}}",
        data:{category:category,subcategory:subcategory,subcategory_id:subcategoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('subcategoryError').classList.remove('hide');
          document.getElementById('subcategoryError').classList.add('has-error');
        } else {
          document.getElementById('subcategoryError').classList.add('hide');
          document.getElementById('subcategoryError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please enter name.');
    }
  }
</script>
@stop