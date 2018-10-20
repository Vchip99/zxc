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
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Online Test </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('college/'.Session::get('college_user_url').'/updateSubject')}}" method="POST" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="subject_id" id="subject_id" value="{{$subject->id}}">
  @else
    <form action="{{url('college/'.Session::get('college_user_url').'/createSubject')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('category')) has-error @endif">
    <label class="col-sm-2 col-form-label">Category Name:</label>
    <div class="col-sm-3">
      @if(isset($subject->id))
        @if(count($testCategories) > 0)
          @foreach($testCategories as $testCategory)
            @if($subject->test_category_id == $testCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$testCategory->name}}" readonly>
              <input type="hidden" name="category" id="category" value="{{$testCategory->id}}">
            @endif
          @endforeach
        @endif
      @else
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="">Select Category</option>
            @if(count($testCategories) > 0)
              @foreach($testCategories as $testCategory)
                <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
              @endforeach
            @endif
        </select>
      @endif
        @if($errors->has('category')) <p class="help-block">{{ $errors->first('category') }}</p> @endif
    </div>
  </div>
  <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      @if(isset($subject->id))
        @if(count($testSubCategories) > 0 && isset($subject->id))
          @foreach($testSubCategories as $testSubCategory)
            @if($subject->test_sub_category_id == $testSubCategory->id)
              <input type="text" class="form-control" name="subcategory_text" value="{{$testSubCategory->name}}" readonly>
              <input type="hidden" name="subcategory" id="subcategory" value="{{$testSubCategory->id}}">
            @endif
          @endforeach
        @endif
      @else
        <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
          <option value="">Select Sub Category</option>
        </select>
      @endif
      @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
    </div>
  </div>
   <div class="form-group row @if ($errors->has('name')) has-error @endif">
    <label for="name" class="col-sm-2 col-form-label">Subject Name:</label>
    <div class="col-sm-3">
      @if(isset($subject->id))
        <input type="text" class="form-control" name="name" id="subject" value="{{$subject->name}}" required="true">
      @else
        <input type="text" class="form-control" name="name" id="subject" value="" placeholder="Subject Name" required="true">
      @endif
      @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
      <span class="hide" id="subjectError" style="color: white;">Given name is already exist with selected category and sub category.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        @if(isset($subject->id) && $subject->created_by == Auth::User()->id)
          <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
        @elseif(empty($subject->id))
          <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
        @endif
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  function selectSubcategory(ele){
    id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('getCollegeSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
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

  function searchSubject(){
    var category = document.getElementById('category').value;
    var subcategory = document.getElementById('subcategory').value;
    var subject = document.getElementById('subject').value;
    if(document.getElementById('subject_id')){
      var subjectId = document.getElementById('subject_id').value;
    } else {
      var subjectId = 0;
    }
    if(category && subcategory && subject){
      $.ajax({
        method:'POST',
        url: "{{url('isTestSubjectExist')}}",
        data:{category:category,subcategory:subcategory,subject:subject,subject_id:subjectId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('subjectError').classList.remove('hide');
          document.getElementById('subjectError').classList.add('has-error');
        } else {
          document.getElementById('subjectError').classList.add('hide');
          document.getElementById('subjectError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else if(!category){
      alert('please select category.');
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!subject){
      alert('please enter subject name.');
    }
  }
</script>
@stop