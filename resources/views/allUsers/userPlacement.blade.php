@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Test Result </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users Info </li>
      <li class="active"> User Test Result </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="college" name="college" onChange="showDepartments();">
                <option value="0"> Select College </option>
                @if(is_object($selectedStudent) && 'other' == $selectedStudent->college_id)
                  <option value="other" selected="true">Other</option>
                @else
                  <option value="other">Other</option>
                @endif
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
            @if(is_object($selectedStudent) && ('other' == $selectedStudent->college_id || 5 == $selectedStudent->user_type || 6 == $selectedStudent->user_type))
              <div class="col-md-3 mrgn_10_btm hide" id="dept">
            @else
              <div class="col-md-3 mrgn_10_btm" id="dept">
            @endif
              <select class="form-control" id="selected_dept" name="departemnt" onChange="resetYear();">
                <option value="0"> Select Departemnt </option>
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
            @if(is_object($selectedStudent) && ('other' == $selectedStudent->college_id || 5 == $selectedStudent->user_type || 6 == $selectedStudent->user_type))
              <div class="col-md-3 mrgn_10_btm hide" id="showYears">
            @else
              <div class="col-md-3 mrgn_10_btm" id="showYears">
            @endif
              <select class="form-control" id="selected_year" name="year" onChange="showStudents(this.value);">
                <option value="0"> Select Year </option>
                  <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                  <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                  <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                  <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
              </select>
            </div>
            <div class="col-md-3 mrgn_10_btm" id="student">
              <select class="form-control" id="selected_student" name="student" onChange="showResult();">
                <option value="0"> Select User </option>
                 @if(is_object($selectedStudent) && count($students) > 0)
                  @foreach($students as $student)
                    @if($selectedStudent->id == $student->id)
                      <option value="{{$student->id}}" selected="true"> {{$student->name}} </option>
                    @else
                      <option value="{{$student->id}}"> {{$student->name}} </option>
                    @endif
                  @endforeach
                @endif
              </select>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row text-center">
          <div class="mrgn_20_btm" id="video">
            <button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_video"><i class="fa fa-book" >
              @if(is_object($selectedStudent) && $selectedStudent->recorded_video)
                Recorded Video of Student
              @else
                Video of User is not uploaded
              @endif
            </i></button>
            <div id="student_video" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h2  class="modal-title">Recorded Video</h2>
                  </div>
                  <div class="modal-body">
                    <div class="iframe-container">
                      @if(is_object($selectedStudent) && $selectedStudent->recorded_video)
                        {!! $selectedStudent->recorded_video !!}
                      @else
                        Video of User is not uploaded
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mrgn_20_btm" id="resume">
            <button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_resume"><i class="fa fa-book" >
            @if(is_object($selectedStudent) && $selectedStudent->resume)
              Resume of User
            @else
              Resume of User is not uploaded
            @endif
            </i></button>
            <div id="student_resume" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    <h2  class="modal-title">Resume</h2>
                  </div>
                  <div class="modal-body">
                    <div class="iframe-container">
                      @if(is_object($selectedStudent) && $selectedStudent->resume)
                        <iframe src="{{asset($selectedStudent->resume)}}" frameborder="0"></iframe>
                      @else
                        Resume of User is not uploaded
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  function resetYear(){
    document.getElementById('selected_year').value = 0;
    unsetStudent();
    document.getElementById('video').innerHTML = '';
    document.getElementById('resume').innerHTML = '';
  }

function showResult(ele){
    var student = parseInt(document.getElementById('selected_student').value);
    $.ajax({
        method: "POST",
        url: "{{url('admin/getStudentById')}}",
        data: {student:student}
    })
    .done(function( msg ) {
      var divVideo = document.getElementById('video');
      divVideo.innerHTML = '';
      var divResume = document.getElementById('resume');
      divResume.innerHTML = '';
      if(msg){
        var videoInnerHTML = '<button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_video"><i class="fa fa-book" >';
        if(msg.recorded_video){
          videoInnerHTML += 'Recorded Video of User';
        } else {
          videoInnerHTML += 'Video of User is not uploaded';
        }
        videoInnerHTML += '</i></button><div id="student_video" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">Recorded Video</h2></div><div class="modal-body"><div class="iframe-container">';
        if(msg.recorded_video){
          videoInnerHTML += msg.recorded_video;
        } else {
          videoInnerHTML += 'Video of User is not uploaded';
        }
        videoInnerHTML += '</div></div></div></div></div>';
        divVideo.innerHTML = videoInnerHTML;

        var resumeInnerHTML = '<button type="button" class="btn btn-lg btn-primary btn-circle" title="Read" data-toggle="modal" data-placement="bottom"   href="#student_resume"><i class="fa fa-book" >';
        if(msg.resume){
          resumeInnerHTML += 'Resume of User';
        } else {
          resumeInnerHTML += 'Resume of User is not uploaded';
        }
        resumeInnerHTML += '</i></button><div id="student_resume" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">Resume</h2></div><div class="modal-body"><div class="iframe-container">';
        if(msg.resume){
          var url = "{{url('')}}/"+msg.resume;
          resumeInnerHTML += '<iframe src="'+url+'" frameborder="0"></iframe>';
        } else {
          resumeInnerHTML += 'Resume of Student is not uploaded';
        }
        resumeInnerHTML += '</div></div></div></div></div>';
        divResume.innerHTML = resumeInnerHTML;
      }

    });
  }
  function showStudents(){
    var college = document.getElementById('college').value;
    var user_type = 2;
    var selected_dept = document.getElementById('selected_dept').value;
    var selected_year = document.getElementById('selected_year').value;
    document.getElementById('selected_student').value = 0;
    document.getElementById('video').innerHTML = '';
    document.getElementById('resume').innerHTML = '';

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
        opt.value = '0';
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
    document.getElementById('video').innerHTML = '';
    document.getElementById('resume').innerHTML = '';

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
        opt.value = '0';
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
      document.getElementById('dept').classList.add('hide');
      document.getElementById('showYears').classList.add('hide');
      showStudents();
    }
  }

  function unsetStudent(){
    select = document.getElementById('selected_student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop