@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css?ver=1.0')}}"/>
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
@section('module_title')
  <section class="content-header">
    <h1> My Attendance  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Attendance </li>
      <li class="active"> My Attendance </li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  &nbsp;
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  <form method="GET" action="getAttendance" id="attendanceForm">
    <!-- {{ csrf_field()}} -->
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
       <select class="form-control" id="batch" name="batch" title="batch" onChange="getAttendance(this);">
          <option value="">Select Batch</option>
          @if(count($batches) > 0)
            @foreach($batches as $batch)
              @if($userFirstBatchId == $batch->id || $selectedBatch == $batch->id)
                <option value="{{$batch->id}}" selected>{{$batch->name}}</option>
              @else
                <option value="{{$batch->id}}">{{$batch->name}}</option>
              @endif
            @endforeach
          @endif
       </select>
      </div>
      <div class="col-md-3 mrgn_10_btm"><b>P-Present, A-Absent, T-Total</b></div>
    </div>
  </form>
  <div id="calendar">
  </div>
  </div>
  <input type="hidden" id="present_dates" value="{{$allPresentDates}}">
  <input type="hidden" id="absent_dates" value="{{$allAbsentDates}}">
  <input type="hidden" id="attendance_stats" value="{{$attendanceStats}}">
  <input type="hidden" id="default_date" value="{{$defaultDate}}">
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
<script type="text/javascript">

  function getAttendance(ele){
    var batchId = parseInt($(ele).val());
    var year = document.getElementById('year').value;
    if(batchId && year){
      document.getElementById('attendanceForm').submit();
    }
  }
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
    showPresentDates();
    showAbsentDates();
    showAttendanceStats();
    $('button.fc-prev-button').on('click',function(){
      showPresentDates();
      showAbsentDates();
      showAttendanceStats();
    });
    $('button.fc-next-button').on('click',function(){
      showPresentDates();
      showAbsentDates();
      showAttendanceStats();
    });
  });

  function showPresentDates(){
    presentDates = document.getElementById('present_dates').value;
    if(presentDates){
      $.each(presentDates.split(','), function(idx, date){
        $('td.fc-day[data-date=' + date + ']').css('background-color', 'green');
      });
    }
  }
  function showAbsentDates(){
    absentDates = document.getElementById('absent_dates').value;
    if(absentDates){
      $.each(absentDates.split(','), function(idx, date){
        $('td.fc-day[data-date=' + date + ']').css('background-color', 'red');
      });
    }
  }
  function showAttendanceStats(){
    attendanceStats = document.getElementById('attendance_stats').value;
    if(attendanceStats){
      $.each(attendanceStats.split(','), function(idx, stats){
        var statsArr = stats.split(':');
        var dateStr = statsArr[0];
        var statsCountArr = statsArr[1].split('-');
        $('.fc-bg td[data-date="' + dateStr + '"]').append('<b>&nbsp;P-'+statsCountArr[0]+'<br>&nbsp;A-'+statsCountArr[1]+'<br>&nbsp;T-'+statsCountArr[2]+'</b>');
      });
    }
  }
</script>
@stop
