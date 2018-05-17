@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Designation </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-asterisk"></i> Zero To Hero </li>
      <li class="active"> Manage Designation </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($designation->id))
    <form action="{{url('admin/updateDesignation')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="designation_id" id="designation_id" value="{{$designation->id}}">
  @else
   <form action="{{url('admin/createDesignation')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('designation')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="designation">Designation Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="designation" name="designation" value="{{($designation)?$designation->name:null}}" required="true">
        @if($errors->has('designation')) <p class="help-block">{{ $errors->first('designation') }}</p> @endif
        <span class="hide" id="designationError" style="color: white;">Given designation is already exist.Please enter another designation.</span>
      </div>
    </div>

    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchDesignation();">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchDesignation(){
    var designation = document.getElementById('designation').value;
    if(document.getElementById('designation_id')){
      var designationId = document.getElementById('designation_id').value;
    } else {
      var designationId = 0;
    }
    if(designation){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isDesignationExist')}}",
        data:{designation:designation,designation_id:designationId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('designationError').classList.remove('hide');
          document.getElementById('designationError').classList.add('has-error');
        } else {
          document.getElementById('designationError').classList.add('hide');
          document.getElementById('designationError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop