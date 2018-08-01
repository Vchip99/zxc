@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css?ver=1.0')}}"/>
  <section class="content-header">
    <h1> Attendance Calendar</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-address-book"></i> Batch </li>
      <li class="active"> Attendance Calendar</li>
    </ol>
  </section>
  <style type="text/css">
    #mycalendar .fc-day-header{
      background-color: white;
      color: black;
    }
    .fc-day-number{
      border-style: solid !important;
      border-top-width: 1px !important;
      border-right-width: 1px !important;
      border-left-width: 1px !important;
      border-color: white !important;
    }

    .fc-content-skeleton td[rowspan='2']{
      border-right-width: 1px !important;
      border-left-width: 1px !important;
      border-color: white !important;
    }
    .fc-day-number.fc-other-month {
       opacity: 1 !important;
    }
    .fc td, .fc th {
      vertical-align: inherit !important;
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
      </div>
    </form>
    <div id="mycalendar">
      {!! $calendar->calendar() !!}
    </div>
    <form method="GET" action="manageAttendance" id="showAttendanceForm">
      <input type="hidden" name="attendance_date" id="selected_date" value="">
      <input type="hidden" name="batch_id" id="selected_batch" value="">
    </form>
  </div>
  <input type="hidden" id="all_attendance_dates" value="{{$allAttendanceDates}}">
  <input type="hidden" id="attendance_stats" value="{{$attendanceStats}}">
  {!! $calendar->script() !!}
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
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
      $('td.fc-day-number').on('click',function(ele){
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
      $('td.fc-day-number').on('click',function(ele){
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
    $('td.fc-day-number').on('click',function(ele){
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
        $('td.fc-day-number[data-date=' + date + ']').css('background-color', 'green');
      });
    }
  }
  function showUnAttendanceDates(){
    $('td.fc-day').css('background-color', 'red');
    $('td.fc-day-number').css('background-color', 'red');
    $('td[rowspan=2]').css('background-color', 'red');
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
        $('.fc-bg td[data-date="' + dateStr + '"]').append('<b>&nbsp;Present - '+statsCountArr[0]+'<br>&nbsp;Absent - '+statsCountArr[1]+'</b>');
      });
    }
  }
</script>
@stop