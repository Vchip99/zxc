@extends('dashboard.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Attendance </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Academic </li>
      <li class="active"> Manage Attendance </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container ">
    @if(count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
   <form action="{{url('college/'.Session::get('college_user_url').'/markCollegeAttendance')}}" method="POST">
    {{ csrf_field() }}
      <div class="row" >
          <div class="form-group">
              <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <input type="text"  class="form-control" name="attendance_date" id="attendance_date" value="{{$attendanceDate}}" >
                </div>
              </div>
              <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <select class="form-control" id="department" name="department" title="department" onChange="selectYear(this);">>
                    <option value="">Select Departments</option>
                    @if(count($departments) > 0)
                      @foreach($departments as $department)
                        @if($selectedDepartment == $department->id)
                          <option value="{{$department->id}}" selected>{{$department->name}}</option>
                        @else
                          <option value="{{$department->id}}">{{$department->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <select class="form-control" id="college_year" name="year" required title="year" onChange="selectSubject(this);">
                    <option value="">Select Year</option>
                    <option value="1" @if(1 == $selectedCollegeYear) selected @endif>First </option>
                    <option value="2" @if(2 == $selectedCollegeYear) selected @endif>Second </option>
                    <option value="3" @if(3 == $selectedCollegeYear) selected @endif>Third </option>
                    <option value="4" @if(4 == $selectedCollegeYear) selected @endif>Fourth </option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div style="margin-bottom: 10px">
                  <select class="form-control" id="subject" name="subject" required title="subject" onChange="selectStudent(this);">
                    <option value="">Select Subject</option>
                    @if(count($subjects) > 0)
                      @foreach($subjects as $subject)
                        @if($selectedSubject == $subject->id)
                          <option value="{{$subject->id}}" selected>{{$subject->name}}</option>
                        @else
                          <option value="{{$subject->id}}">{{$subject->name}}</option>
                        @endif
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>
          </div>
      </div>
      <div class="row" >
        <div class="form-group">
          <div class="col-md-12">
              <div style="margin-bottom: 10px">
                <input type="radio" name="mark_attendance" value="1" checked> Mark Attendance As Present &nbsp;
                <input type="radio" name="mark_attendance" value="0"> Mark Attendance As Absent
              </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12" id="all-result">
          <div class="panel panel-info">
            <div class="panel-heading text-center">
              <span class="">Students</span>
              <span class="pull-right">Toggle All - <input type="checkbox" onClick="toggleAll(this);"></span>
            </div>
            <div class="panel-body">
              <table  class="" id="">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="college_attendance_users" class="">
                  @if(count($students) > 0)
                    @foreach($students as $index => $student)
                      <tr style="overflow: auto;">
                        <td>{{ $index + 1}}</td>
                        <td> {{$student->name}}</td>
                        <td> {{$student->email}}</td>
                        <td>
                          @if(in_array($student->id,$presentStudents))
                            <input type="checkbox" name="students[]" id="student_{{$student->id}}" value="{{$student->id}}" checked="checked">
                          @else
                            <input type="checkbox" name="students[]" id="student_{{$student->id}}" value="{{$student->id}}">
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          <input type="hidden" name="all_users" id="all_users" value="{{$allStudents}}">
          <div class="form-group">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary" style="float: right;width: 90px !important;">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
<script type="text/javascript">
  $(function () {
      var currentDate = "{{ date('Y-m-d')}}";
      $('#attendance_date').datetimepicker({
        defaultDate: currentDate,
        format: 'YYYY-MM-DD'
      }).on('dp.change', function (e) {
        document.getElementById('college_attendance_users').innerHTML = '';
        document.getElementById('department').selectedIndex = '';
        document.getElementById('college_year').selectedIndex = '';
        document.getElementById('subject').selectedIndex = '';
    });
  });

  function toggleAll(ele){
    if(true == $(ele).prop('checked')){
      $('input[id^=student_]').prop('checked', 'checked');
    } else {
      $('input[id^=student_]').prop('checked', '');
    }
  }

  function selectStudent(ele){
    var subjectId = parseInt($(ele).val());
    var date = document.getElementById('attendance_date').value;
    var departmentId = document.getElementById('department').value;
    var collegeYear = document.getElementById('college_year').value;
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    if(departmentId && collegeYear && subjectId){
      $.ajax({
          method:'POST',
          url: "{{url('getCollegeStudentAttendanceByDepartmentIdByYearBySubject')}}",
          data:{_token:currentToken,subject_id:subjectId,department_id:departmentId,college_year:collegeYear ,attendance_date:date}
      }).done(function( result ) {
          var users = document.getElementById('college_attendance_users');
          users.innerHTML = '';
          var allUsers = document.getElementById('all_users');
          allUsers.value = '';
          if(result['collegeUsers'].length){
            $.each(result['collegeUsers'], function(idx, obj) {
              if(result['collegeAttendance'].indexOf(String(obj.id)) > -1){
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" style="overflow: auto;"><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td>'+obj.email+'</td><td><input type="checkbox" name="students[]" id="student_'+obj.id+'" value="'+obj.id+'" checked="checked"></td></tr>';
              } else {
                users.innerHTML +='<tr class="student" id="div_student_'+obj.id+'" style="overflow: auto;"><td>'+(idx + 1)+'</td><td>'+obj.name+'</td><td>'+obj.email+'</td><td><input type="checkbox" name="students[]" id="student_'+obj.id+'" value="'+obj.id+'"></td></tr>';
              }
              if(0 == idx){
                allUsers.value = obj.id;
              } else {
                allUsers.value += ','+ obj.id;
              }
            });
          } else {
            users.innerHTML = '<tr class="student"><td colspan="4">No Result!</td></tr>';
          }
      });
    }
  }

  function selectSubject(ele){
    var year = $(ele).val();
    var department = document.getElementById('department').value;
    if(year && department){
      $.ajax({
        method: "POST",
        url: "{{url('getCollegeSubjectsByDepartmentIdByYear')}}",
        data: {department:department,year:year}
      })
      .done(function( msg ) {
        select = document.getElementById('subject');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Subject';
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
      select = document.getElementById('subject');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Subject';
      select.appendChild(opt);
    }
  }

  function selectYear(){
    document.getElementById('college_year').value = '';

    selectEle = document.getElementById('subject');
    selectEle.innerHTML = '';
    var opt = document.createElement('option');
    opt.value = '';
    opt.innerHTML = 'Select Subject';
    selectEle.appendChild(opt);
  }
</script>
@stop