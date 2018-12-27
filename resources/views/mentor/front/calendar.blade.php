@extends('mentor.front.master')
@section('title')
  <title>MENTOR - HOME</title>
@stop
@section('header-css')
  <script src="{{asset('js/moment-with-locales.min.js?ver=1.0')}}" type="text/javascript"></script>
  <script src="{{asset('js/bootstrap-datetimepicker.min.js?ver=1.0')}}" type="text/javascript"></script>
  <link href="{{asset('css/bootstrap-datetimepicker.min.css?ver=1.0')}}" rel="stylesheet"/>
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

  .todays_data td, th {
    padding: 6px;
    border: 1px solid #ccc;
    text-align: left;
  }
  .todays_data th {
    background: #333;
    color: white;
    font-weight: bold;
  }
  .todays_data table {
    width: 100%;
    border-collapse: collapse;
  }
  .btn-yellow{  background: yellow;}
  .btn-red{  background: red;}
  .btn-green{  background: green;}
  .btn-blue{  background: blue;}
  .btn-pink{  background: #e6004e;}

  .fc .fc-row .fc-content-skeleton td,.fc-unthemed td{
    border-color: black;
  }
  span.fc-day-number{
    color: black;
  }
  .fc-day-top.fc-other-month {
      opacity: 1;
  }
  @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px) {
    thead tr,td {
      position: inherit !important;
    }
    tr,td{
      display: inline-flex;
      width: 100% !important;
    }
    .fc-row, .fc-day-header{
      width: 100% !important;
    }
    .fc-scroller{
      width: 100%;
    }
  }
  .btn-primary {
    width: 100%;
  }
  </style>
@stop
@section('content')
    @include('mentor.front.header_menu')
    <div class="container" style="margin-top: 90px;">
      @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div class="pull-right">
      <a href="#add-schedule" data-toggle="modal" class="col-sm-3 btn btn-primary" style="width: 100px;">Add Schedule</a>
      <div id="add-schedule" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" data-dismiss="modal">×</button>
              <h2  class="modal-title">Add Schedule</h2>
            </div>
            <div class="modal-body" style="padding: 30px;">
              <div class="">
                <form action="{{url('createUserSchedule')}}" method="POST">
                  {{ csrf_field() }}
                  <fieldset>
                    <div class="form-group row">
                      <label>Date:</label>
                      <input class="form-control" placeholder="Date" name="date" id="date" type="text" value="" required>
                      <script type="text/javascript">
                          var currentDate = "{{ date('Y-m-d')}}";
                          $(function () {
                              $('#date').datetimepicker({defaultDate: currentDate,format: 'YYYY-MM-DD'});
                          });
                      </script>
                    </div>
                    <div class="form-group row">
                      <label>From Time:</label>
                      <input class="form-control" placeholder="From Time" name="from_time" id="from_time" type="text" value="" required>
                      <script type="text/javascript">
                          var currentTime = "{{ date('H:i')}}";
                          $(function () {
                              $('#from_time').datetimepicker({format: 'LT'});
                              $('#from_time').val(currentTime);
                          });
                      </script>
                    </div>
                    <div class="form-group row">
                      <label>To Time:</label>
                      <input class="form-control" placeholder="To Time" name="to_time" id="to_time" type="text" value="" required>
                      <script type="text/javascript">
                          var currentTime = "{{ date('H:i')}}";
                          $(function () {
                            $('#to_time').datetimepicker({format: 'LT'});
                            $('#to_time').val(currentTime);
                          });
                      </script>
                    </div>
                    <div class="form-group row">
                      <label>Area:</label>
                      @if(count($areaNames) > 0)
                        <select class="form-control" id="area" name="area" required placeholder="Area" onChange="selectSkill(this);">
                          <option value=""> Select Area </option>
                          @foreach($areaNames as $areaId => $areaName)
                            <option value="{{$areaId}}"> {{$areaName}} </option>
                          @endforeach
                        </select>
                      @endif
                    </div>
                    <div class="form-group row">
                      <label>Skill:</label>
                      <select class="form-control" id="skill" name="skill" required placeholder="Skill" onChange="selectMentors(this);">
                        <option value=""> Select Skill </option>
                      </select>
                    </div>
                    <div class="form-group row">
                      <label>Mentor:</label>
                      <select class="form-control" id="mentor" name="mentor" required placeholder="Mentor">
                        <option value=""> Select Mentor </option>
                      </select>
                    </div>
                    <div class="form-group row">
                      <label>Comment:</label>
                      <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control" rows="7"></textarea>
                    </div>
                    <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                    <button class="btn btn-info" id="submitBtn" type="submit">Submit</button>
                  </fieldset>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ul>
      <li>Color code as per below priority:<button type="button" class="btn btn-green"></button> - Fixed Meeting, <button type="button" class="btn btn-yellow"></button> - Proposed Meeting, <button type="button" class="btn btn-pink"></button> - Student Request
      </li>
    </ul>
    <div id="calendar">
    </div>
    <input type="hidden" id="daycolours" value="{{$dayColours}}">
    @if(count($calendarData) > 0)
      @foreach($calendarData as $calenderDate => $data)
        <div class="modal todays_data" id="modal_{{$calenderDate}}" role="dialog" style="display: none;">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" style="overflow: auto;">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Schedule of {{$calenderDate}}</h4>
                <table>
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>From</th>
                      <th>To</th>
                      <th>Comment</th>
                      <th>Meeting Type</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data as $id => $schedule)
                      <tr style="overflow: auto;">
                        <td>{{$schedule['title']}}</td>
                        <td>{{$schedule['from']}}</td>
                        <td>{{$schedule['to']}}</td>
                        <td>{{$schedule['comment']}}</td>
                        <td>
                          @if(1 == $schedule['type'])
                            Fixed Meeting
                          @elseif(2 == $schedule['type'])
                            Proposed Meeting
                          @else
                            Student Request
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                <hr>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endif
    </div>

@stop
@section('footer')
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
  @include('mentor.front.footer')
  <script type="text/javascript">
  $(function() {
    // page is now ready, initialize the calendar...
    $('#calendar').fullCalendar({
      header: {
        left: '',
        center: 'prev title next',
        right: ''
      }
    })
    showDayColour();
    $('button.fc-prev-button').on('click',function(){
      showDayColour();
      dayClick();
    });
    $('button.fc-next-button').on('click',function(){
      showDayColour();
      dayClick();
    });
    dayClick();
  });

  function showDayColour(){
    daycolours = document.getElementById('daycolours').value;
    if(daycolours){
      $.each(daycolours.split(','), function(idx, daycolour){
        var dayArr = daycolour.split(':');
        var dateStr = dayArr[0];
        var colorStr = dayArr[1];
        $('td.fc-day-top[data-date=' + dateStr + ']').css('background-color', colorStr);
        $('td.fc-day[data-date=' + dateStr + ']').css('background-color', colorStr);
      });
    }
  }

  function dayClick(){
    $('td.fc-day').on('click',function(ele){
      var selectedDate = $(this).data('date');
      $('#modal_'+selectedDate).modal();
    });
    $('td.fc-day-top').on('click',function(ele){
      var selectedDate = $(this).data('date');
      $('#modal_'+selectedDate).modal();
    });
  }


  function selectSkill(ele){
    var areaId = $(ele).val();
    if(areaId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getMentorSkillsByAreaId')}}",
        data: {area:areaId}
      })
      .done(function( msg ) {
        select = document.getElementById('skill');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Skill';
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

  function selectMentors(ele){
    var skillId = $(ele).val();
    $.ajax({
      method: "POST",
      url: "{{url('getMentorsBySkillId')}}",
      data: {skill:skillId}
    })
    .done(function( msg ) {
      select = document.getElementById('mentor');
      select.innerHTML = '';
      var opt = document.createElement('option');
      opt.value = '';
      opt.innerHTML = 'Select Mentor';
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
</script>
@stop
