@extends((1 == $clientUser->user_type)?'clientuser.dashboard.dashboard':'clientuser.dashboard.teacher_dashboard')
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
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Calendar  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-user"></i> Calendar </li>
      <li class="active"> My Calendar </li>
    </ol>
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
    <ul>
      <li>Color code as per below priority:
        <ol>
          <li>Yellow Color - Emergency Notice</li>
          <li>Red Color - Exam Schedule</li>
          <li>Green Color - Holiday</li>
          <li>Blue Color - Notice</li>
          <li>Pink Color - Classes</li>
        </ol>
      </li>
    </ul>
    <div id="calendar">
    </div>
    <input type="hidden" id="daycolours" value="{{$dayColours}}">
    @if(count($calendarData) > 0)
      @foreach($calendarData as $calenderDate => $data)
        <div class="modal todays_data" id="modal_{{$calenderDate}}" role="dialog" style="display: none;">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Schedule of {{$calenderDate}}</h4>
                @if(isset($data['emergency_notices']) && count($data['emergency_notices']) > 0)
                <div><label>Emergency Message:</label><br>
                  @if(1 == count($data['emergency_notices']))
                    {{$data['emergency_notices'][0]['batch']}} @ {{$data['emergency_notices'][0]['title']}}
                  @else
                    @foreach($data['emergency_notices'] as $index => $notice)
                      {{$notice['batch']}} @ {{$notice['title']}} <br>
                    @endforeach
                  @endif
                </div>
                <hr>
                @endif
                @if(isset($data['classes']) && count($data['classes']) > 0)
                <label>Class:</label>
                <table>
                  <thead>
                    <tr>
                      <th>Batch</th>
                      <th>Subject</th>
                      <th>Topic</th>
                      <th>From</th>
                      <th>To</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data['classes'] as $class)
                      <tr>
                        <td>{{$class['batch']}}</td>
                        <td>{{$class['subject']}}</td>
                        <td>{{$class['topic']}}</td>
                        <td>{{$class['from']}}</td>
                        <td>{{$class['to']}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                <hr>
                @endif
                @if(isset($data['exams']) && count($data['exams']) > 0)
                <label>Exam:</label>
                <table>
                  <thead>
                    <tr>
                      <th>Batch</th>
                      <th>Name</th>
                      <th>Subject</th>
                      <th>Topic</th>
                      <th>From</th>
                      <th>To</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data['exams'] as $exam)
                      <tr>
                        <td>{{$exam['batch']}}</td>
                        <td>{{$exam['title']}}</td>
                        <td>{{$exam['subject']}}</td>
                        <td>{{$exam['topic']}}</td>
                        <td>{{$exam['from']}}</td>
                        <td>{{$exam['to']}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
                <hr>
                @endif
                @if(isset($data['holiday']) && count($data['holiday']) > 0)
                <div><label>Holiday:</label><br>
                  @if(1 == count($data['holiday']))
                    {{$data['holiday'][0]['batch']}} @ {{$data['holiday'][0]['title']}}
                  @else
                    @foreach($data['holiday'] as $index => $holiday)
                      {{$holiday['batch']}} @ {{$holiday['title']}} <br>
                    @endforeach
                  @endif
                </div>
                <hr>
                @endif
                @if(isset($data['notices']) && count($data['notices']) > 0)
                <div><label>Notice:</label><br>
                  @if(1 == count($data['notices']))
                    {{$data['notices'][0]['batch']}} @ {{$data['notices'][0]['title']}}
                  @else
                    @foreach($data['notices'] as $index => $notice)
                      {{$notice['batch']}} @ {{$notice['title']}} <br>
                    @endforeach
                  @endif
                </div>
                @endif
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
        $('td.fc-day[data-date=' + dateStr + ']').css('background-color', colorStr);
      });
    }
  }

  function dayClick(){
    $('td.fc-day').on('click',function(ele){
      var selectedDate = $(this).data('date');
      $('#modal_'+selectedDate).modal();
    });
  }
</script>
@stop
