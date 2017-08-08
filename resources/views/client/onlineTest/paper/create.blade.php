@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Paper </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Test </li>
      <li class="active"> Manage Paper </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
    <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
    <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  &nbsp;
  <div class="container admin_div">
  @if(isset($paper->id))
    <form action="{{url('updateOnlineTestSubjectPaper')}}" method="POST">
    {{method_field('PUT')}}
    <input type="hidden" name="paper_id" value="{{$paper->id}}">
  @else
    <form action="{{url('createOnlineTestSubjectPaper')}}" method="POST">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('institute_course')) has-error @endif">
    <label class="col-sm-2 col-form-label">Institute Course Name:</label>
    <div class="col-sm-3">
      <select class="form-control" name="institute_course" required title="Category" onChange="selectCategory(this);" >
          <option value="">Select Institute Course ...</option>
          @if(count($instituteCourses) > 0)
            @foreach($instituteCourses as $instituteCourse)
              @if( $paper->client_institute_course_id == $instituteCourse->id)
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
      <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
          <option value="">Select Category ...</option>
          @if(count($testCategories) > 0 && isset($paper->id)))
            @foreach($testCategories as $testCategory)
              @if( isset($paper->id) && $paper->category_id == $testCategory->id)
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
  <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      <select id="subcategory" class="form-control" name="subcategory" onChange="selectSubject(this);" required title="Sub Category">
        <option value="">Select Sub Category ...</option>
        @if(count($testSubCategories) > 0 && isset($paper->id))
          @foreach($testSubCategories as $testSubCategory)
            @if($paper->sub_category_id == $testSubCategory->id)
                <option value="{{$testSubCategory->id}}" selected="true">{{$testSubCategory->name}}</option>
              @else
                <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('subject')) has-error @endif">
    <label class="col-sm-2 col-form-label">Subject Name:</label>
    <div class="col-sm-3">
      <select id="subject" class="form-control" name="subject" required title="Subject">
        <option value="">Select Subject ...</option>
          @if(count($testSubjects) > 0 && isset($paper->id))
          @foreach($testSubjects as $testSubject)
            @if($paper->subject_id == $testSubject->id)
                <option value="{{$testSubject->id}}" selected="true">{{$testSubject->name}}</option>
              @else
                <option value="{{$testSubject->id}}">{{$testSubject->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
      @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Paper Name:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" name="name" value="{{$paper->name}}" required="true">
      @else
        <input type="text" class="form-control" name="name" value="" placeholder="paper name" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('price')) has-error @endif">
      <label for="price" class="col-sm-2 col-form-label">Price:</label>
      <div class="col-sm-3">
        @if(isset($paper->id))
          <input type="text" class="form-control" name="price" value="{{$paper->price}}" required="true">
        @else
          <input type="text" class="form-control" name="price" value="" placeholder="price" required="true">
        @endif
        @if($errors->has('price')) <p class="help-block">{{ $errors->first('price') }}</p> @endif
      </div>
    </div>
  <div class="form-group row">
      <label for="date_to_active" class="col-sm-2 col-form-label">Date To Active:</label>
      <div class="col-sm-3">
        <input type="text"  class="form-control" name="date_to_active" id="date_to_active" @if(isset($paper->id)) value="{{$paper->date_to_active}}" @endif required="true" placeholder="date" required="true">
      </div>
      <script type="text/javascript">
          $(function () {
              $('#date_to_active').datetimepicker({format: 'YYYY-MM-DD'});
          });
      </script>
    </div>
  <div class="form-group row @if ($errors->has('time')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Total Time:</label>
    <div class="col-sm-3">
      @if(isset($paper->id))
        <input type="text" class="form-control" name="time" value="{{$paper->time}}" required="true">
      @else
        <input type="text" class="form-control" name="time" value="" required="true">
      @endif
      @if($errors->has('time')) <p class="help-block">{{ $errors->first('time') }}</p> @endif
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
    } else {
      select = document.getElementById('category');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Category ...';
      select.appendChild(opt);
    }
  }

  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineTestSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Sub Category ...';
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
    } else {
      select = document.getElementById('subcategory');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Sub Category ...';
      select.appendChild(opt);
    }
  }

  function selectSubject(ele){
    subcatId = parseInt($(ele).val());
    catId = parseInt(document.getElementById('category').value);
    if( 0 < catId && 0 < subcatId ){
      $.ajax({
              method: "POST",
              url: "{{url('getOnlineSubjectsByCatIdBySubcatId')}}",
              data: {catId:catId, subcatId:subcatId}
          })
          .done(function( msg ) {
            selectSub = document.getElementById('subject');
            selectSub.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '';
            opt.innerHTML = 'Select Subject ...';
            selectSub.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  selectSub.appendChild(opt);
              });
            }
          });
    } else {
      selectSub = document.getElementById('subject');
      selectSub.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Subject ...';
      selectSub.appendChild(opt);
    }
  }
</script>
@stop