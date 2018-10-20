@extends('dashboard.dashboard')
@section('module_title')
  <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css?ver=1.0')}}"/>
  <section class="content-header">
    <h1> Manage Attenadance  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-calendar"></i> Academic </li>
      <li class="active"> Manage Attenadance </li>
    </ol>
  </section>
  <style type="text/css">
    #calendar .fc-day-header{
      background-color: white;
      color: black;
    }
    .fc td {
      vertical-align: bottom !important;
    }

    #calendar .fc-toolbar .fc-center h2{
      font-size: 15px;
    }
    #calendar .fc-view-container{
      border: 1px solid;
    }
    #calendar .fc-content-skeleton{
      border-top: 1px solid white !important;
    }
    #calendar .fc-content-skeleton .fc-day-top {
      border-right: 1px solid white !important;
    }
    #calendar .fc-day-top {
      opacity: 1 !important;
    }
  </style>
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
     <form method="GET" action="{{url('college/'.Session::get('college_user_url').'/manageCollegeAttendance')}}" id="attendanceCalendarForm">
      <div class="form-group row">
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="year" name="year" title="year">
            <option value="">Select Year</option>
            @for($i=2018;$i<=2050;$i++)
              @if($selectedYear == $i)
                <option value="{{$i}}" selected>{{$i}}</option>
              @else
                <option value="{{$i}}">{{$i}}</option>
              @endif
            @endfor
          </select>
        </div>
        <div class="col-md-3 mrgn_10_btm">
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
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="college_year" name="college_year" required title="College Year" onChange="selectSubject(this);">
            <option value="">Select Year</option>
            <option value="1" @if(1 == $selectedCollegeYear) selected @endif>First </option>
            <option value="2" @if(2 == $selectedCollegeYear) selected @endif>Second </option>
            <option value="3" @if(3 == $selectedCollegeYear) selected @endif>Third </option>
            <option value="4" @if(4 == $selectedCollegeYear) selected @endif>Fourth </option>
          </select>
        </div>
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="subject" name="subject" required title="subject" onChange="getAttendanceCalendar(this);">
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
        <div class="col-md-3 mrgn_10_btm"><b>P-Present, A-Absent</b></div>
      </div>
    </form>
    <div id="calendar">
    </div>
    <form method="GET" action="{{url('college/'.Session::get('college_user_url').'/manageAttendance')}}" id="showAttendanceForm">
      <input type="hidden" name="attendance_date" id="selected_date" value="">
      <input type="hidden" name="department_id" id="selected_department" value="">
      <input type="hidden" name="college_year" id="selected_college_year" value="">
      <input type="hidden" name="subject_id" id="selected_subject" value="">
    </form>
  </div>
  <input type="hidden" id="all_attendance_dates" value="{{$allAttendanceDates}}">
  <input type="hidden" id="attendance_stats" value="{{$attendanceStats}}">
  <input type="hidden" id="default_date" value="{{$defaultDate}}">
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
  var defaultDate = document.getElementById('default_date').value;
  $('#calendar').fullCalendar({
    header: {
      left: '',
      center: 'prev title next',
      right: ''
    },
    defaultDate:defaultDate,
  });
    showUnAttendanceDates();
    showAttendanceDates();
    showAttendanceStats();
    $('button.fc-prev-button').on('click',function(){
      showUnAttendanceDates();
      showAttendanceDates();
      showAttendanceStats();
      $('td.fc-day').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedDepartment = document.getElementById('department').value;
        var selectedCollegeYear = document.getElementById('college_year').value;
        var selectedSubject = document.getElementById('subject').value;
        if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
          confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
        }
      });
      $('td.fc-day-top').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedDepartment = document.getElementById('department').value;
        var selectedCollegeYear = document.getElementById('college_year').value;
        var selectedSubject = document.getElementById('subject').value;
        if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
          confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
        }
      });
    });
    $('button.fc-next-button').on('click',function(){
      showUnAttendanceDates();
      showAttendanceDates();
      showAttendanceStats();
      $('td.fc-day').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedDepartment = document.getElementById('department').value;
        var selectedCollegeYear = document.getElementById('college_year').value;
        var selectedSubject = document.getElementById('subject').value;
        if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
          confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
        }
      });
      $('td.fc-day-top').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedDepartment = document.getElementById('department').value;
        var selectedCollegeYear = document.getElementById('college_year').value;
        var selectedSubject = document.getElementById('subject').value;
        if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
          confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
        }
      });
    });

    $('td.fc-day').on('click',function(ele){
      var selectedDate = $(this).data('date');
      var selectedDepartment = document.getElementById('department').value;
      var selectedCollegeYear = document.getElementById('college_year').value;
      var selectedSubject = document.getElementById('subject').value;
      if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
        confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
      }
    });
    $('td.fc-day-top').on('click',function(ele){
      var selectedDate = $(this).data('date');
      var selectedDepartment = document.getElementById('department').value;
      var selectedCollegeYear = document.getElementById('college_year').value;
      var selectedSubject = document.getElementById('subject').value;
      if(selectedDate && selectedDepartment && selectedCollegeYear && selectedSubject){
        confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject);
      }
    });
});

  function showAttendanceDates(){
    allAttendanceDates = document.getElementById('all_attendance_dates').value;
    if(allAttendanceDates){
      $.each(allAttendanceDates.split(','), function(idx, date){
        $('td.fc-day[data-date=' + date + ']').css('background-color', 'green');
        $('td.fc-day-top[data-date=' + date + ']').css('background-color', 'green');
      });
    }
  }
  function showUnAttendanceDates(){
    $('td.fc-day').css('background-color', 'red');
    $('td.fc-day-top').css('background-color', 'red');
  }

  function confirmation(selectedDate,selectedDepartment,selectedCollegeYear,selectedSubject){
    document.getElementById('selected_date').value = selectedDate;
    document.getElementById('selected_department').value = selectedDepartment;
    document.getElementById('selected_college_year').value = selectedCollegeYear;
    document.getElementById('selected_subject').value = selectedSubject;
    document.getElementById('showAttendanceForm').submit();
  }
  function getAttendanceCalendar(ele){
    var batchId = parseInt($(ele).val());
    var year = document.getElementById('year').value;
    if(batchId && year){
      document.getElementById('attendanceCalendarForm').submit();
    }
  }
  function showAttendanceStats(){
    attendanceStats = document.getElementById('attendance_stats').value;
    if(attendanceStats){
      $.each(attendanceStats.split(','), function(idx, stats){
        var statsArr = stats.split(':');
        var dateStr = statsArr[0];
        var statsCountArr = statsArr[1].split('-');
        $('.fc-bg td[data-date="' + dateStr + '"]').append('<b>&nbsp;P-'+statsCountArr[0]+'<br>&nbsp;A-'+statsCountArr[1]+'</b>');
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