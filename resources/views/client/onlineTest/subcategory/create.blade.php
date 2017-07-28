@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Sub Category </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test </li>
      <li class="active"> Manage Sub Category </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($testSubcategory->id))
    <form action="{{url('updateOnlineTestSubCategory')}}" method="POST" enctype="multipart/form-data">
      {{method_field('PUT')}}
      <input type="hidden" name="subcat_id" value="{{$testSubcategory->id}}">
  @else
      <form action="{{url('createOnlineTestSubCategory')}}" method="POST" enctype="multipart/form-data">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
    <label class="col-sm-2 col-form-label">Institute Course Name:</label>
    <div class="col-sm-3">
      <select class="form-control" name="institute_course" required title="Category" onChange="selectCategory(this);" >
          <option value="">Select Institute Course ...</option>
          @if(count($instituteCourses) > 0)
            @foreach($instituteCourses as $instituteCourse)
              @if( $testSubcategory->client_institute_course_id == $instituteCourse->id)
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
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      <select class="form-control" id="category" name="category" required title="Category">
          <option value="">Select Category ...</option>
          @if( isset($testSubcategory->id) && count($testCategories) > 0)
            @foreach($testCategories as $testCategory)
              @if( $testSubcategory->category_id == $testCategory->id)
                <option value="{{$testCategory->id}}" selected="true">{{$testCategory->name}}</option>
              @else
                <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
              @endif
            @endforeach
          @endif
        </select>
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      @if(isset($testSubcategory))
        <input type="text" class="form-control" name="name" value="{{$testSubcategory->name}}" required="true">
      @else
        <input type="text" class="form-control" name="name" value="" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
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
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  function selectCategory(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineTestCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('category');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Category ...';
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