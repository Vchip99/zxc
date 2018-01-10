@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Blog Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-newspaper-o"></i> Blog Category </li>
      <li class="active"> Manage Blog Category </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($blogCategory->id))
    <form action="{{url('admin/updateBlogCategory')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="category_id" id="category_id" value="{{$blogCategory->id}}">
  @else
   <form action="{{url('admin/createBlogCategory')}}" method="POST" id="submitForm">
    @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="category">Category Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="category" name="category" value="{{($blogCategory)?$blogCategory->name:null}}" required="true">
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
        <span class="hide" id="categoryError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchCategory();">Submit</button>
      </div>
    </div>
  </div>
</form>
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
        url: "{{url('admin/isBlogCategoryExist')}}",
        data:{category:category,category_id:categoryId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('categoryError').classList.remove('hide');
          document.getElementById('categoryError').classList.add('has-error');
        } else {
          document.getElementById('categoryError').classList.add('hide');
          document.getElementById('categoryError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop