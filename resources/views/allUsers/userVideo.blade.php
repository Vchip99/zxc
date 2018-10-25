@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Video Url </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> User Video Url </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container admin_div">
        @if(Session::has('message'))
          <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
          <form action="{{url('admin/updateStudentVideo')}}" method="POST" enctype="multipart/form-data">
          {{ method_field('PUT') }}
          <input type="hidden" id="student_id" name="student" value="{{($selectedStudent)?$selectedStudent->id:null}}">
          {{ csrf_field() }}
          <div class="row">
            <label class="col-sm-2 col-form-label" >College:</label>
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="showDepartments();" required>
                <option value=""> Select College </option>
                @if(count($colleges) > 0)
                  @foreach($colleges as $college)
                    @if(is_object($selectedStudent) && $selectedStudent->college_id == $college->id)
                      <option value="{{$college->id}}" selected="true">{{$college->name}}</option>
                    @else
                      <option value="{{$college->id}}">{{$college->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Department:</label>
            <div class="col-md-3 mrgn_10_btm" id="dept">
              <select class="form-control" id="selected_dept" name="department" onChange="resetYear();" required>
                <option value=""> Select Departemnt </option>
                @if(is_object($selectedStudent) && count($collegeDepts) > 0)
                  @foreach($collegeDepts as $collegeDept)
                    @if($selectedStudent->college_dept_id == $collegeDept->id)
                      <option value="{{$collegeDept->id}}" selected="true"> {{$collegeDept->name}} </option>
                    @else
                      <option value="{{$collegeDept->id}}"> {{$collegeDept->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Year:</label>
            <div class="col-md-3 mrgn_10_btm" id="showYears">
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this.value);" required>
                <option value=""> Select Year </option>
                  <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                  <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                  <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                  <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Student:</label>
            <div class="col-md-3 mrgn_10_btm" id="student">
              <select class="form-control" id="selected_student" name="student" onChange="showResult();" required>
                <option value=""> Select User </option>
                 @if(is_object($selectedStudent) && count($students) > 0)
                  @foreach($students as $student)
                    @if($selectedStudent->year == $student->year)
                      @if($selectedStudent->id == $student->id)
                        <option value="{{$student->id}}" selected="true"> {{$student->name}} </option>
                      @else
                        <option value="{{$student->id}}"> {{$student->name}} </option>
                      @endif
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
          </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" for="category">Video Url:</label>
          <div class="col-sm-3  mrgn_10_btm">
            <input type="text" class="form-control" id="recorded_video" name="recorded_video" value="{{($selectedStudent)?$selectedStudent->recorded_video:null}}" required="true">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" >Resume:</label>
          <div class="col-sm-3  mrgn_10_btm">
            <input type="file" name="resume" id="resume" class="form-control">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" >Selected Resume:</label>
          <div class="col-sm-3  mrgn_10_btm">
            <span id="selectedResume"> {{($selectedStudent)?basename($selectedStudent->resume):null}}</span>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" >Skills:</label>
          <div class="col-sm-10  mrgn_10_btm">
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
<script type="text/javascript">
  function resetYear(){
    document.getElementById('selected_year').selectedIndex = '';
    document.getElementById('selected_student').selectedIndex = '';
    document.getElementById('student_id').value = 0;
    document.getElementById('recorded_video').value = '';
    unsetStudent();
    resetSkills();
  }

  function showResult(ele){
    var student = parseInt(document.getElementById('selected_student').value);
    $.ajax({
          method: "POST",
          url: "{{url('admin/getStudentById')}}",
          data: {student:student}
      })
      .done(function( msg ) {
        document.getElementById('recorded_video').value = msg.recorded_video;
        if(msg.resume){
          document.getElementById('selectedResume').innerHTML = msg.resume.split('/').reverse()[0];
        } else {
          document.getElementById('selectedResume').innerHTML = '';
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

  function resetSkills(){
    document.getElementById('selectedResume').innerHTML = '';
    $.each($('.userKills'),function(idx,skill){
      $(skill).prop('checked', false);
    });
  }

  function showStudents(){
    var college = document.getElementById('college').value;
    var user_type = 2;
    var selected_dept = document.getElementById('selected_dept').value;
    var selected_year = document.getElementById('selected_year').value;
    document.getElementById('selected_student').value = 0;
    document.getElementById('student_id').value = 0;
    document.getElementById('recorded_video').value = '';

    if(user_type > 0){
      $.ajax({
        method: "POST",
        url: "{{url('admin/searchUsers')}}",
        data:{college_id:college, user_type:user_type, department_id:selected_dept, selected_year:selected_year}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_student');
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
  }

  function showDepartments(){
    var college = document.getElementById('college').value;
    document.getElementById('dept').classList.remove('hide');
    document.getElementById('showYears').classList.remove('hide');

    document.getElementById('selected_dept').value = 0;
    document.getElementById('selected_year').value = 0;
    unsetStudent();
    document.getElementById('student_id').value = 0;
    document.getElementById('recorded_video').value = '';

    if(college > 0){
      $.ajax({
        method: "POST",
        url: "{{url('admin/getDepartments')}}",
        data:{college:college}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_dept');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Department';
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
      select = document.getElementById('selected_dept');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Department';
      select.appendChild(opt);
    }
    document.getElementById('selected_year').selectedIndex = '';
    resetSkills();
  }

  function unsetStudent(){
    select = document.getElementById('selected_student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop