@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Skills </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-files-o"></i> Users Info </li>
      <li class="active"> Manage Skills </li>
    </ol>
  </section>
@stop
@section('admin_content')
  &nbsp;
  <div class="container admin_div">
  @if(isset($skill->id))
    <form action="{{url('admin/updateSkill')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="skill_id" id="skill_id" value="{{$skill->id}}">
  @else
   <form action="{{url('admin/createSkill')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('name')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="name">Skill Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="skill" name="name" value="{{($skill)?$skill->name:null}}" required="true">
        @if($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
        <span class="hide" id="skillError" style="color: white;">Given name is already exist.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchSkill();">Submit</button>
      </div>
    </div>
    </form>
  </div>
<script type="text/javascript">
  function searchSkill(){
    var skill = document.getElementById('skill').value;
    if(document.getElementById('skill_id')){
      var skillId = document.getElementById('skill_id').value;
    } else {
      var skillId = 0;
    }
    if(skill){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isSkillExist')}}",
        data:{name:skill,skill_id:skillId}
      }).done(function( msg ) {
        if('true' == msg){
          document.getElementById('skillError').classList.remove('hide');
          document.getElementById('skillError').classList.add('has-error');
        } else {
          document.getElementById('skillError').classList.add('hide');
          document.getElementById('skillError').classList.remove('has-error');
          document.getElementById('submitForm').submit();
        }
      });
    } else {
      alert('please enter name.');
    }
  }
</script>
@stop