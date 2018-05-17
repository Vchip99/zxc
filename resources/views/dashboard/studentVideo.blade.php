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
    <div class="">
      <div class="top mrgn_40_btm">
        <div class="container">
          <div class="row">
            <div class="">
              <input type="hidden" id="login_User_Type" name="login_User_Type" value="{{Auth::user()->user_type}}">
              @if(5 == Auth::user()->user_type || 6 == Auth::user()->user_type)
                <div class="col-md-3 mrgn_10_btm">
                  <select class="form-control" id="dept" onChange="resetYear(this);">
                    <option value="0"> Select Department </option>
                    @if(count($collegeDepts) > 0)
                      @foreach($collegeDepts as $collegeDept)
                        @if(is_object($selectedStudent) && $selectedStudent->college_dept_id == $collegeDept->id)
                          <option value="{{$collegeDept->id}}" selected="true">{{$collegeDept->name}}</option>
                        @else
                          <option value="{{$collegeDept->id}}">{{$collegeDept->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              @endif
              @if((is_object($selectedStudent) && 2 == $selectedStudent->user_type) || 4 == Auth::user()->user_type || 3 == Auth::user()->user_type )
                <div class="col-md-3 mrgn_10_btm" id="div_year">
              @else
                <div class="col-md-3 mrgn_10_btm hide" id="div_year">
              @endif
                <select class="form-control" id="selected_year" name="year" onChange="showStudents(this);">
                  <option value="0"> Select Year </option>
                  <option value="1" @if(is_object($selectedStudent) &&'1' == $selectedStudent->year) selected="true" @endif >First Year</option>
                  <option value="2" @if(is_object($selectedStudent) &&'2' == $selectedStudent->year) selected="true" @endif >Second Year</option>
                  <option value="3" @if(is_object($selectedStudent) &&'3' == $selectedStudent->year) selected="true" @endif >Third Year</option>
                  <option value="4" @if(is_object($selectedStudent) &&'4' == $selectedStudent->year) selected="true" @endif >Fourth Year</option>
                </select>
              </div>
              <div class="col-md-3 ">
                <select class="form-control" id="student" onChange="showResult(this);">
                  <option value="0">Select User </option>
                  @if(is_object($selectedStudent) && count($students) > 0)
                    @foreach($students as $student)
                      @if(is_object($selectedStudent) && $selectedStudent->year == $student->year)
                        @if($selectedStudent->id == $student->id)
                          <option value="{{$student->id}}" selected="true">{{$student->name}}</option>
                        @else
                          <option value="{{$student->id}}">{{$student->name}}</option>
                        @endif
                      @endif
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="container admin_div">
              <form action="{{url('updateStudentVideo')}}" method="POST">
              {{ method_field('PUT') }}
              <input type="hidden" id="student_id" name="student" value="{{($selectedStudent)?$selectedStudent->id:null}}">

              {{ csrf_field() }}
              <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="category">Video Url:</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" id="recorded_video" name="recorded_video" value="{{($selectedStudent)?$selectedStudent->recorded_video:null}}" required="true" placeholder="Enter video url">
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

  function resetYear(){

    document.getElementById('div_year').classList.remove('hide');
    document.getElementById('selected_year').value = 0;
    resetUser();
    document.getElementById('student_id').value = 0;
    document.getElementById('recorded_video').value = '';
  }

  function showStudents(){
    var user_type = 2;
    if(document.getElementById('selected_year')){
      var year = parseInt(document.getElementById('selected_year').value);
    } else {
      var year = 0;
    }
    if(document.getElementById("dept")){
        var department = parseInt(document.getElementById("dept").value);
    } else {
        var department = 0;
    }

    document.getElementById('student_id').value = 0;
    document.getElementById('recorded_video').value = '';

    $.ajax({
          method: "POST",
          url: "{{url('showStudentsByDepartmentByYear')}}",
          data: {year:year,department:department,user_type:user_type}
      })
      .done(function( msg ) {
        select = document.getElementById('student');
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

  function showResult(ele){
    var student = parseInt(document.getElementById('student').value);
    $.ajax({
        method: "POST",
        url: "{{url('getStudentById')}}",
        data: {student:student}
    })
    .done(function( msg ) {
      document.getElementById('student_id').value = msg.id;
      document.getElementById('recorded_video').value = msg.recorded_video;
    });
  }

  function resetUser(){
    select = document.getElementById('student');
    select.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '0';
    opt.innerHTML = 'Select User';
    select.appendChild(opt);
  }
</script>
@stop