@extends('client.dashboard')
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
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($subcategory->id))
    <form action="{{url('updateOnlineSubCategory')}}" method="POST">
      {{method_field('PUT')}}
      <input type="hidden" name="subCategory_id" value="{{$subcategory->id}}">
  @else
      <form action="{{url('createOnlineSubCategory')}}" method="POST">
  @endif

    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      <select class="form-control" id="category" name="category" required title="Category">
        <option value="">Select Category</option>
          @if(count($categories) > 0)
            @foreach($categories as $category)
              @if($subcategory->category_id == $category->id)
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
    <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      @if(isset($subcategory))
        <input type="text" class="form-control" name="subcategory" value="{{$subcategory->name}}" required="true">
      @else
        <input type="text" class="form-control" name="subcategory" value="" required="true">
      @endif
      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3">
        <button type="submit" class="btn btn-primary" title="Submit">Submit</button>
      </div>
    </div>
  </div>
</form>
@stop