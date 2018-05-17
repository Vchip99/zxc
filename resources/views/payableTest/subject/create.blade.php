@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Subject </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Payable Test </li>
      <li class="active"> Manage Subject </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($subject->id))
    <form action="{{url('admin/updatePayableSubject')}}" method="POST" id="submitForm">
    {{method_field('PUT')}}
    <input type="hidden" name="subject_id" id="subject_id" value="{{$subject->id}}">
  @else
    <form action="{{url('admin/createPayableSubject')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
  <div class="form-group row @if ($errors->has('subcategory')) has-error @endif">
    <label class="col-sm-2 col-form-label">Sub Category Name:</label>
    <div class="col-sm-3">
      <select id="subcategory" class="form-control" name="subcategory" required title="Sub Category">
        <option value="">Select Sub Category ...</option>
        @if(count($testSubCategories) > 0 )
          @foreach($testSubCategories as $testSubCategory)
            @if($subject->sub_category_id == $testSubCategory->id)
              <option value="{{$testSubCategory->id}}" selected>{{$testSubCategory->name}}</option>
            @else
              <option value="{{$testSubCategory->id}}">{{$testSubCategory->name}}</option>
            @endif
          @endforeach
        @endif
      </select>
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
      <span class="hide" id="subjectError" style="color: white;">Given name is already exist with selected sub category.Please enter another name.</span>
    </div>
  </div>
  <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchSubject();">Submit</button>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  function searchSubject(){
    var subcategory = document.getElementById('subcategory').value;
    var subject = document.getElementById('subject').value;
    if(document.getElementById('subject_id')){
      var subjectId = document.getElementById('subject_id').value;
    } else {
      var subjectId = 0;
    }
    if(subcategory && subject){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isPayableSubjectExist')}}",
        data:{subcategory:subcategory,subject:subject,subject_id:subjectId}
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
    } else if(!subcategory){
      alert('please select subcategory.');
    } else if(!subject){
      alert('please enter subject name.');
    }
  }
</script>
@stop