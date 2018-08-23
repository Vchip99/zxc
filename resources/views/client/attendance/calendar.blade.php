@extends((!empty($loginUser->subdomain))?'client.dashboard':'clientuser.dashboard.teacher_dashboard')
@section('module_title')
  <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css?ver=1.0')}}"/>
  <section class="content-header">
    <h1> Attendance Calendar</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Attendance Calendar</li>
    </ol>
  </section>
  <style type="text/css">
    #calendar .fc-day-header{
      background-color: white;
      color: black;
    }
    .fc td, .fc th {
      vertical-align: bottom !important;
    }
    .fc-toolbar .fc-center h2{
      font-size: 15px;
    }
    .fc-view-container{
      border: 1px solid;
    }
  </style>
@stop
@section('dashboard_content')
  &nbsp;
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
     <form method="GET" action="manageAttendanceCalendar" id="attendanceCalendarForm">
      <div class="form-group row">
        <div class="col-md-3 mrgn_10_btm">
          <select class="form-control" id="year" name="year" title="year">
            <option value="">Select Year</option>
            @for($i=2018;$i<=2050;$i++)
              @if($currnetYear == $i || $selectedYear == $i)
                <option value="{{$i}}" selected>{{$i}}</option>
              @else
                <option value="{{$i}}">{{$i}}</option>
              @endif
            @endfor
          </select>
        </div>
        <div class="col-md-3 mrgn_10_btm">
         <select class="form-control" id="batch" name="batch" title="batch" onChange="getAttendanceCalendar(this);">
            <option value="">Select Batch</option>
            @if(count($batches) > 0)
              @foreach($batches as $batch)
                @if($selectedBatch == $batch->id)
                  <option value="{{$batch->id}}" selected>{{$batch->name}}</option>
                @else
                  <option value="{{$batch->id}}">{{$batch->name}}</option>
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
    <form method="GET" action="manageAttendance" id="showAttendanceForm">
      <input type="hidden" name="attendance_date" id="selected_date" value="">
      <input type="hidden" name="batch_id" id="selected_batch" value="">
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
        var selectedBatch = document.getElementById('batch').value;
        if(selectedDate && selectedBatch){
          confirmation(selectedDate,selectedBatch);
        }
      });
      $('td.fc-day-top').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedBatch = document.getElementById('batch').value;
        if(selectedDate && selectedBatch){
          confirmation(selectedDate,selectedBatch);
        }
      });
    });
    $('button.fc-next-button').on('click',function(){
      showUnAttendanceDates();
      showAttendanceDates();
      showAttendanceStats();
      $('td.fc-day').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedBatch = document.getElementById('batch').value;
        if(selectedDate && selectedBatch){
          confirmation(selectedDate,selectedBatch);
        }
      });
      $('td.fc-day-top').on('click',function(ele){
        var selectedDate = $(this).data('date');
        var selectedBatch = document.getElementById('batch').value;
        if(selectedDate && selectedBatch){
          confirmation(selectedDate,selectedBatch);
        }
      });
    });

    $('td.fc-day').on('click',function(ele){
      var selectedDate = $(this).data('date');
      var selectedBatch = document.getElementById('batch').value;
      if(selectedDate && selectedBatch){
        confirmation(selectedDate,selectedBatch);
      }
    });
    $('td.fc-day-top').on('click',function(ele){
      var selectedDate = $(this).data('date');
      var selectedBatch = document.getElementById('batch').value;
      if(selectedDate && selectedBatch){
        confirmation(selectedDate,selectedBatch);
      }
    });
});

  function showAttendanceDates(){
    allAttendanceDates = document.getElementById('all_attendance_dates').value;
    if(allAttendanceDates){
      $.each(allAttendanceDates.split(','), function(idx, date){
        $('td.fc-day[data-date=' + date + ']').css('background-color', 'green');
      });
    }
  }
  function showUnAttendanceDates(){
    $('td.fc-day').css('background-color', 'red');
  }

  function confirmation(selectedDate,selectedBatch){
    document.getElementById('selected_date').value = selectedDate;
    document.getElementById('selected_batch').value = selectedBatch;
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
</script>
@stop