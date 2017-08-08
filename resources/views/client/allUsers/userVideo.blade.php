@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> User Video Url </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> User Dashboard </li>
      <li class="active"> User Video Url </li>
    </ol>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
@stop
@section('dashboard_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="col-md-3 mrgn_10_btm">
              <select class="form-control" id="course" name="course" onChange="showStudents(this.value);">
                <option value="0"> Select Courses </option>
                @if(count($instituteCourses) > 0)
                  @foreach($instituteCourses as $instituteCourse)
                    @if($courseId == $instituteCourse->id)
                      <option value="{{$instituteCourse->id}}" selected="true">{{$instituteCourse->name}}</option>
                    @else
                      <option value="{{$instituteCourse->id}}">{{$instituteCourse->name}}</option>
                    @endif
                  @endforeach
                @endif
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
        <div class="row">
          <div class="container admin_div">
              <form action="{{url('updateUserVideo')}}" method="POST">
              {{ method_field('PUT') }}
              <input type="hidden" id="student_id" name="student_id" value="{{($selectedStudent)?$selectedStudent->id:null}}">
              <input type="hidden" id="course_id" name="course_id" value="{{$courseId}}">

              {{ csrf_field() }}
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="category">Video Url:</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" id="recorded_video" name="recorded_video" value="{{($selectedStudent)?$selectedStudent->recorded_video:null}}" required="true">
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
    </div>
  </div>
<script type="text/javascript">

  function showResult(ele){
    var student = parseInt(document.getElementById('selected_student').value);
    var courseId = parseInt(document.getElementById('course').value);
    $.ajax({
          method: "POST",
          url: "{{url('getStudentById')}}",
          data: {student_id:student,course_id:courseId}
      })
      .done(function( msg ) {
        if(msg){
          document.getElementById('student_id').value = msg.id;
          document.getElementById('recorded_video').value = msg.recorded_video;
        } else {
          document.getElementById('student_id').value = 0;
          document.getElementById('recorded_video').value = '';
        }
    });
  }
  function showStudents(){
    var courseId = document.getElementById('course').value;
    document.getElementById('selected_student').value = 0;
    if(courseId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('searchUsers')}}",
        data:{course_id:courseId}
      })
      .done(function( msg ) {
        select = document.getElementById('selected_student');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select User';
        select.appendChild(opt);
        if( 0 < msg['users'].length){
          $.each(msg['users'], function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }

</script>
@stop