@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Video </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users Info</li>
      <li class="active">Video </li>
    </ol>
  </section>

@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="container">
      <div class="container admin_div">
          <form action="{{url('college/'.Session::get('college_user_url').'/updateStudentVideo')}}" method="POST" enctype="multipart/form-data">
          {{ method_field('PUT') }}
          {{ csrf_field() }}
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >Department:</label>
            <div class="col-sm-3">
             <select class="form-control" id="dept" onChange="resetYear(this);" required>
                <option value=""> Select Department </option>
                @if(count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if(is_object($selectedStudent) && $selectedStudent->college_dept_id == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected>{{$collegeDept->name}}</option>
                    @else
                      <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >Year:</label>
            <div class="col-sm-3">
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);" required>
                <option value=""> Select Year </option>
                <option value="1" @if(is_object($selectedStudent) && 1 == $selectedStudent->year) selected @endif>First Year</option>
                <option value="2" @if(is_object($selectedStudent) && 2 == $selectedStudent->year) selected @endif>Second Year</option>
                <option value="3" @if(is_object($selectedStudent) && 3 == $selectedStudent->year) selected @endif>Third Year</option>
                <option value="4" @if(is_object($selectedStudent) && 4 == $selectedStudent->year) selected @endif>Fourth Year</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >User:</label>
            <div class="col-sm-3">
              <select class="form-control" id="student"  name="student" onChange="showResult(this);" required>
                <option value="">Select User </option>
                @if(count($students) > 0)
                  @foreach($students as $student)
                    @if(is_object($selectedStudent) && $selectedStudent->id == $student->id)
                      <option value="{{$student->id}}" selected>{{$student->name}} </option>
                    @else
                      <option value="{{$student->id}}">{{$student->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >User Video Url(User You tube Url):</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="recorded_video" name="recorded_video" value="{{($selectedStudent)?$selectedStudent->recorded_video:null}}" required="true" placeholder="Enter video url">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >Resume:</label>
            <div class="col-sm-3">
              <input type="file" name="resume" id="resume" class="form-control">
            </div>
          </div>
          @if(is_object($selectedStudent))
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >Selected Resume:</label>
            <div class="col-sm-3">
              <span id="selectedResume"> {{basename($selectedStudent->resume)}}</span>
            </div>
          </div>
          @endif
          <div class="form-group row">
            <label class="col-sm-2 col-form-label" >Skills:</label>
            <div class="col-sm-10">
              @if(count($skills) > 0)
                @foreach($skills as $skill)
                  @if(in_array($skill->id,$selectedStudentSkills))
                    <input type="checkbox" class="userKills" name="skills[]" value="{{$skill->id}}" id="skill-{{$skill->id}}" checked> {{$skill->name}}
                  @else
                    <input type="checkbox" class="userKills" name="skills[]" value="{{$skill->id}}" id="skill-{{$skill->id}}" > {{$skill->name}}
                  @endif
                @endforeach
              @endif
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-2" title="Submit">
              <button type="submit" class="btn btn-primary" style="width: 100px;">Submit</button>
            </div>
          </div>
          </form>
      </div>
    </div>
  </div>
<script type="text/javascript">

  function resetYear(){
    document.getElementById('selected_year').value = '';
    resetUser();
    document.getElementById('recorded_video').value = '';
  }

  function showStudents(){
    var user_type = 2;
    var year = parseInt(document.getElementById('selected_year').value);
    var department = parseInt(document.getElementById("dept").value);
    $.ajax({
          method: "POST",
          url: "{{url('showStudentsByDepartmentByYear')}}",
          data: {year:year,department:department,user_type:user_type}
      })
      .done(function( msg ) {
        select = document.getElementById('student');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select User';
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

  function showResult(ele){
    var student = parseInt(document.getElementById('student').value);
    $.ajax({
        method: "POST",
        url: "{{url('getStudentById')}}",
        data: {student:student}
    })
    .done(function( msg ) {
      document.getElementById('recorded_video').value = msg.recorded_video;
      if(msg.resume){
        document.getElementById('selectedResume').innerHTML = msg.resume.split('/').reverse()[0];
      }
      if(msg.skills){
        var skills = msg.skills.split(',');
        $.each(skills,function(idx,skill){
          if($('#skill-'+skill)){
            $('#skill-'+skill).prop('checked', true);
          }
        });
      } else {
        $.each($('.userKills'),function(idx,skill){
          $(skill).prop('checked', false);
        });
      }
    });
  }

  function resetUser(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop