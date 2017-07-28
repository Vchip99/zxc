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
  <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
    <label class="col-sm-2 col-form-label">Institute Course Name:</label>
    <div class="col-sm-3">
      <select class="form-control" name="institute_course" required title="Category" onChange="selectCategory(this);" >
          <option value="">Select Institute Course ...</option>
          @if(count($instituteCourses) > 0)
            @foreach($instituteCourses as $instituteCourse)
              @if( $subcategory->client_institute_course_id == $instituteCourse->id)
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
          @if( isset($subcategory->id) && count($categories) > 0)
            @foreach($categories as $category)
              @if( $subcategory->category_id == $category->id)
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
<script type="text/javascript">
  function selectCategory(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineCategories')}}",
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