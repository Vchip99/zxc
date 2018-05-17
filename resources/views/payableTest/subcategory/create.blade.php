@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Sub Category </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($testSubcategory->id))
    <form action="{{url('admin/updatePayableSubCategory')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="subcat_id" id="subCategory_id" value="{{$testSubcategory->id}}">
  @else
    <form action="{{url('admin/createPayableSubCategory')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      @if(isset($testSubcategory))
        <input type="text" class="form-control" name="name" id="subcategory" value="{{$testSubcategory->name}}" required="true">
      @else
        <input type="text" class="form-control" name="name" id="subcategory" value="" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      <span class="hide" id="subcategoryError" style="color: white;">Given name is already exist.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row @if ($errors->has('price')) has-error @endif">
    <label class="col-sm-2 col-form-label" for="price">Yearly Price:</label>
    <div class="col-sm-3">
      @if(isset($testSubcategory))
        <input type="text" class="form-control"  name="price" id="price" value="{{$testSubcategory->price}}" required>
      @else
        <input type="text" class="form-control"  name="price" id="price" required>
      @endif
      @if($errors->has('price')) <p class="has-error">{{ $errors->first('price') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('monthly_price')) has-error @endif">
    <label class="col-sm-2 col-form-label" for="monthly_price">Monthly Price:</label>
    <div class="col-sm-3">
      @if(isset($testSubcategory))
        <input type="text" class="form-control"  name="monthly_price" id="monthly_price" value="{{$testSubcategory->monthly_price}}" required>
      @else
        <input type="text" class="form-control"  name="monthly_price" id="monthly_price" required>
      @endif
      @if($errors->has('monthly_price')) <p class="has-error">{{ $errors->first('monthly_price') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('image_path')) has-error @endif">
    <label class="col-sm-2 col-form-label" for="image_path">Sub Category Image:</label>
    <div class="col-sm-3">
      <input type="file" class="form-control"  name="image_path" id="image_path" >
      @if($errors->has('image_path')) <p class="has-error">{{ $errors->first('image_path') }}</p> @endif
      @if(isset($testSubcategory->image_path))
        <b><span>Existing Image: {!! basename($testSubcategory->image_path) !!}</span></b>
      @endif
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
    var subcategory = document.getElementById('subcategory').value;
    if(document.getElementById('subCategory_id')){
      var subcategoryId = document.getElementById('subCategory_id').value;
    } else {
      var subcategoryId = 0;
    }
    if(subcategory){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isPayableTestSubCategoryExist')}}",
        data:{subcategory:subcategory,subcategory_id:subcategoryId}
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
    } else if(!subcategory){
      alert('please enter name.');
    }
  }
</script>
@stop