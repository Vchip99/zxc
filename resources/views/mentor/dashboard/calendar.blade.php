@extends('mentor.dashboard.dashboard')
@section('dashboard_header')
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
@section('module_title')
  <section class="content-header">
    <h1> Calendar  </h1>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
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
            <div class="modal-body">
              <div class="">
                <form action="{{url('mentor/createSchedule')}}" method="POST">
                  {{ csrf_field() }}
                  <fieldset>
                    <div class="form-group row">
                      <label>Type :</label><input type="radio" name="type" value="1" checked> Fixed Meeting <input type="radio" name="type" value="2"> Proposed Meeting
                    </div>
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
                      <label>Student Email:</label>(add email then verify it)<button class="pull-right btn-info" id="verifyEmail">Verify Email</button>
                      <input class="form-control" placeholder="Email" id="email" name="email" type="email" value="" required>
                      <input type="hidden" name="user_id" id="user" value="">
                    </div>
                    <div class="form-group row">
                      <label>Comment:</label>
                      <textarea name="comment" id="comment" placeholder="Comment here.." class="form-control" rows="7"></textarea>
                    </div>
                    <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                    <button class="btn btn-info" id="submitBtn" type="submit" disabled="true">Submit</button>
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
                      <th>Email</th>
                      <th>Mobile</th>
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
                        <td>{{$schedule['email']}}</td>
                        <td>{{$schedule['mobile']}}</td>
                        <td>{{$schedule['from']}}</td>
                        <td>{{$schedule['to']}}</td>
                        <td>{{$schedule['comment']}}</td>
                        <td>
                          <input type="radio" name="type_{{$id}}" data-id="{{$id}}" value="1" @if(1 == $schedule['type']) checked @endif onClick="changeType(this);"> Fixed Meeting <br>
                          <input type="radio" name="type_{{$id}}" data-id="{{$id}}" value="2" @if(2 == $schedule['type']) checked @endif onClick="changeType(this);"> Proposed Meeting <br>
                          <input type="radio" name="type_{{$id}}" data-id="{{$id}}" value="3" @if(3 == $schedule['type']) checked @endif onClick="changeType(this);"> Student Request <br>
                          <input type="radio" name="type_{{$id}}" data-id="{{$id}}" value="4" onClick="changeType(this);"> Cancle Request
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
  <script src="{{ asset('js/moment.min.js')}}"></script>
  <script src="{{ asset('js/fullcalendar.min.js')}}"></script>
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

  $('#verifyEmail').click( function(event){
    event.preventDefault();
    var emailStr = $('#email').val();
    if(!emailStr){
      alert('please enter email id.');
    } else {
      $.ajax({
          method: "POST",
          url: "{{url('mentor/getStudentByEmail')}}",
          data: {email:emailStr}
      })
      .done(function( msg ) {
        if(msg){
          $('#user').val(msg.id);
          $('#submitBtn').attr('disabled',false);
        } else {
          $('#submitBtn').attr('disabled',true);
          alert('Entered email id does not exist. please enter correct email id');
        }
      });
    }
  });

  function changeType(ele){
    var id = $(ele).data('id');
    var type = $(ele).attr('value')
    console.log(id);
    console.log(type);
    $.ajax({
        method: "POST",
        url: "{{url('mentor/changeMeetingTypeById')}}",
        data: {id:id,type:type}
    })
    .done(function( msg ) {
      window.location.reload();
    });

  }
</script>
@stop
