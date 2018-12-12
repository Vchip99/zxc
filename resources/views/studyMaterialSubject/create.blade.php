@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-file-pdf-o"></i> Study Material </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('admin/updateStudyMaterialSubject')}}" method="POST" enctype="multipart/form-data" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" id="subject_id" name="subject_id" value="{{$subject->id}}">
  @else
    <form action="{{url('admin/createStudyMaterialSubject')}}" method="POST" enctype="multipart/form-data" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row @if ($errors->has('category')) has-error @endif">
      <label class="col-sm-2 col-form-label">Category Name:</label>
      <div class="col-sm-3">
      @if(isset($subject->id))
        @if(count($courseCategories) > 0)
          @foreach($courseCategories as $courseCategory)
            @if( isset($subject->id) && $subject->course_category_id == $courseCategory->id)
              <input type="text" class="form-control" name="category_text" value="{{$courseCategory->name}}" readonly>
              <input type="hidden" name="category" id="category" value="{{$courseCategory->id}}">
            @endif
          @endforeach
        @endif
      @else
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="0">Select Category</option>
            @if(count($courseCategories) > 0)
              @foreach($courseCategories as $courseCategory)
                <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
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
        @if(isset($subject->id) && count($courseSubCategories) > 0)
            @foreach($courseSubCategories as $courseSubCategory)
              @if($subject->course_sub_category_id == $courseSubCategory->id)
                <input type="text" class="form-control" name="subcategory_text" value="{{$courseSubCategory->name}}" readonly>
                <input type="hidden" name="subcategory" id="subcategory" value="{{$courseSubCategory->id}}">
              @endif
            @endforeach
        @else
          <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
            <option value="0">Select Sub Category</option>
          </select>
        @endif
        @if($errors->has('subcategory')) <p class="help-block">{{ $errors->first('subcategory') }}</p> @endif
      </div>
    </div>
    <div class="form-group row @if ($errors->has('subject')) has-error @endif">
      <label for="subject" class="col-sm-2 col-form-label">Subject Name:</label>
      <div class="col-sm-3">
        @if(isset($subject->id))
          <input type="text" class="form-control" name="subject" id="subject" value="{{$subject->name}}" required="true">
        @else
          <input type="text" class="form-control" name="subject" id="subject" value="" placeholder="Subject Name" required="true">
        @endif
        @if($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
        <span class="hide" id="subjectError" style="color: white;">Given name is already exist with selected category and subcategory.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-3" title="Submit">
          @if(!empty($subject->id) && $subject->admin_id == Auth::guard('admin')->user()->id)
            <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
          @elseif(empty($subject->id))
            <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
          @else
            <a href="{{ url('admin/manageStudyMaterialSubject') }}" class="btn btn-primary">Back</a>
          @endif
        </div>
      </div>
  </form>
  </div>

<script type="text/javascript">

  function getCourseSubCategories(id){
    if( 0 < id ){
      $.ajax({
              method: "POST",
              url: "{{url('admin/getCourseSubCategories')}}",
              data: {id:id}
          })
          .done(function( msg ) {
            select = document.getElementById('subcategory');
            select.innerHTML = '';
            var opt = document.createElement('option');
            opt.value = '0';
            opt.innerHTML = 'Select Sub Category';
            select.appendChild(opt);
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                  var opt = document.createElement('option');
                  opt.value = obj.id;
                  opt.innerHTML = obj.name;
                  if(id == obj.id){
                    opt.selected = true;
                  }
                  select.appendChild(opt);
              });
            }
          });
    }
  }

  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    getCourseSubCategories(id);
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
        url: "{{url('admin/isStudyMaterialSubjectExist')}}",
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
    } else if(!course){
      alert('please enter name.');
    }
  }
</script>
@stop