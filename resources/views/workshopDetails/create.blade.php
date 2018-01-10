@extends('admin.master')
  &nbsp;
  @section('module_title')
  <section class="content-header">
    <h1> Manage Workshop Details </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-link"></i> Workshop </li>
      <li class="active"> Manage Workshop Details </li>
    </ol>
  </section>
@stop
@section('admin_content')
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <div class="container admin_div">
  @if(isset($workshopDetail->id))
    <form action="{{url('admin/updateWorkshopDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="workshop_id" name="workshop_id" value="{{$workshopDetail->id}}">
  @else
    <form action="{{url('admin/createWorkshopDetails')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
        <select id="category" class="form-control" name="category" required title="Category">
            <option value="0">Select Category</option>
            @if(count($workshopCategories) > 0)
              @foreach($workshopCategories as $workshopCategory)
                @if( isset($workshopDetail->id) && $workshopDetail->workshop_category_id == $workshopCategory->id)
                  <option value="{{$workshopCategory->id}}" selected="true">{{$workshopCategory->name}}</option>
                @else
                  <option value="{{$workshopCategory->id}}">{{$workshopCategory->name}}</option>
                @endif
              @endforeach
            @endif
        </select>
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
      </div>
    </div>
     <div class="form-group row @if ($errors->has('course')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Workshop Name:</label>
      <div class="col-sm-3">
        @if(isset($workshopDetail->id))
          <input type="text" class="form-control" name="workshop" id="workshop" value="{{$workshopDetail->name}}" required="true">
        @else
          <input type="text" class="form-control" name="workshop" id="workshop" value="" placeholder="Workshop Name" required="true">
        @endif
        @if($errors->has('workshop')) <p class="help-block">{{ $errors->first('workshop') }}</p> @endif
        <span class="hide" id="workshopError" style="color: white;">Given workshop name is already exist with selected category.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row @if ($errors->has('workshop_image')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="workshop_image">Workshop Image:</label>
      <div class="col-sm-3">
        <input type="file" class="form-control"  name="workshop_image" id="workshop_image" >
        @if($errors->has('workshop_image')) <p class="has-error">{{ $errors->first('workshop_image') }}</p> @endif
        @if(isset($workshopDetail->workshop_image))
          <b><span>Existing Image: {!! basename($workshopDetail->workshop_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Name:</label>
      <div class="col-sm-3">
        @if(isset($workshopDetail->id))
          <input type="text" class="form-control" name="author" value="{{$workshopDetail->author}}" required="true">
        @else
          <input type="text" class="form-control" name="author" value="" placeholder="Author Name" required="true">
        @endif
        @if($errors->has('author')) <p class="help-block">{{ $errors->first('author') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('author_introduction')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Author Introduction:</label>
      <div class="col-sm-3">
        @if(isset($workshopDetail->id))
          <textarea type="text" class="form-control" name="author_introduction" required="true">{{$workshopDetail->author_introduction}}</textarea>
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
        @if(isset($workshopDetail->author_image))
          <b><span>Existing Image: {!! basename($workshopDetail->author_image) !!}</span></b>
        @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('description')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Workshop Description:</label>
      <div class="col-sm-3">
        @if(isset($workshopDetail->id))
          <textarea type="text" class="form-control" name="description" required="true">{{$workshopDetail->description}}</textarea>
        @else
          <textarea type="text" class="form-control" name="description" required="true"></textarea>
        @endif
        @if($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('certified')) has-error @endif">
      <label for="course" class="col-sm-2 col-form-label">Certified:</label>
      <div class="col-sm-3">
          @if(isset($workshopDetail->id))
          <label class="radio-inline"><input type="radio" name="certified" value="1" @if(1 == $workshopDetail->certified) checked="true" @endif> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" @if(0 == $workshopDetail->certified) checked="true" @endif> No</label>
          @else
            <label class="radio-inline"><input type="radio" name="certified" value="1"> Yes</label>
          <label class="radio-inline"><input type="radio" name="certified" value="0" checked> No</label>
          @endif
        @if($errors->has('certified')) <p class="help-block">{{ $errors->first('certified') }}</p> @endif
      </div>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">Start Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="start_date" id="start_date" @if(isset($workshopDetail->id)) value="{{$workshopDetail->start_date}}" @endif required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#start_date').datetimepicker({format: 'YYYY-MM-DD'});
          });
      </script>
    </div>
    <div class="form-group row">
      <label for="course" class="col-sm-2 col-form-label">End Date:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="end_date" id="end_date" @if(isset($workshopDetail->id)) value="{{$workshopDetail->end_date}}" @endif required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#end_date').datetimepicker({format: 'YYYY-MM-DD'});
          });
      </script>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          <button type="button" class="btn btn-primary" onclick="searchWorkshop();">Submit</button>
        </div>
      </div>
  </div>
</form>
<script type="text/javascript">
  function searchWorkshop(){
    var category = document.getElementById('category').value;
    var workshop = document.getElementById('workshop').value;
    if(document.getElementById('workshop_id')){
      var workshopId = document.getElementById('workshop_id').value;
    } else {
      var workshopId = 0;
    }
    if(category && workshop){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isOnlineWorkshopExist')}}",
        data:{category:category,workshop:workshop,workshop_id:workshopId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('workshopError').classList.remove('hide');
          document.getElementById('workshopError').classList.add('has-error');
        } else {
          document.getElementById('workshopError').classList.add('hide');
          document.getElementById('workshopError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop