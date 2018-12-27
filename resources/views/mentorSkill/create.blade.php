@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Mentor Skill </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Mentor </li>
      <li class="active"> Mentor Skill </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="container admin_div">
  @if(isset($skill->id))
    <form action="{{url('admin/updateMentorSkill')}}" method="POST" id="submitForm">
    {{ method_field('PUT') }}
    <input type="hidden" name="skill_id" id="skill_id" value="{{$skill->id}}">
  @else
    <form action="{{url('admin/createMentorSkill')}}" method="POST" id="submitForm">
  @endif
    {{ csrf_field() }}
    <div class="form-group row  @if ($errors->has('area')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="area">Area:</label>
      <div class="col-sm-3">
        @if(isset($skill->id))
          @if(count($areaNames) > 0)
            @foreach($areaNames as $areaId => $areaName)
              @if($skill->mentor_area_id == $areaId)
                <input type="text" class="form-control" name="area_text" value="{{$areaNames[$skill->mentor_area_id]}}" readonly>
                <input type="hidden" class="form-control" id="area" name="area" value="{{$skill->mentor_area_id}}" required="true">
              @endif
            @endforeach
          @endif
        @else
          @if(count($areaNames) > 0)
            <select class="form-control" id="area" name="area" required>
              <option value=""> Select Area </option>
            @foreach($areaNames as $areaId => $areaName)
              <option value="{{$areaId}}"> {{$areaName}} </option>
            @endforeach
            </select>
          @endif
        @endif
        @if($errors->has('area')) <p class="help-block">{{ $errors->first('area') }}</p> @endif
      </div>
    </div>
    <div class="form-group row  @if ($errors->has('skill')) has-error @endif">
      <label class="col-sm-2 col-form-label" for="skill">Skill Name:</label>
      <div class="col-sm-3">
        <input type="text" class="form-control" id="skill" name="skill" value="{{($skill->id)?$skill->name:NULL}}" required="true">
        @if($errors->has('skill')) <p class="help-block">{{ $errors->first('skill') }}</p> @endif
        <span class="hide" id="skillError" style="color: white;">Given name is already exist with selected area.Please enter another name.</span>
      </div>
    </div>
    <div class="form-group row">
      <div class="offset-sm-2 col-sm-3" title="Submit">
        <button type="button" class="btn btn-primary" onclick="searchskill();">Submit</button>
      </div>
    </div>
  </form>
</div>
<script type="text/javascript">
  function searchskill(){
    var area = document.getElementById('area').value;
    var skill = document.getElementById('skill').value;
    if(document.getElementById('skill_id')){
      var skillId = document.getElementById('skill_id').value;
    } else {
      var skillId = 0;
    }
    if(skill){
      $.ajax({
        method:'POST',
        url: "{{url('admin/isMentorSkillExist')}}",
        data:{area_id:area,skill:skill,skill_id:skillId}
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