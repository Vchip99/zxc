@extends('admin.master')
@section('module_title')
  <link rel="stylesheet" href="{{ asset('css/fullcalendar.min.css?ver=1.0')}}"/>
  <section class="content-header">
    <h1> Clients Activity </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-bookmark-o"></i> Clients Info </li>
      <li class="active"> Clients Activity </li>
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
  </style>
@stop
@section('admin_content')
  <div class="container">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    <div id="calendar">
    </div>
    <input type="hidden" id="loginDates" value="{{$loginDates}}">
    @if(count($calendarData) > 0)
      @foreach($calendarData as $calenderDate => $clientDatas)
        @if(count($clientDatas) > 0)
          <div class="modal todays_data" id="modal_{{$calenderDate}}" role="dialog" style="display: none;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header" style="overflow-x: auto;">
                  <button type="button" class="close" data-dismiss="modal">×</button>
                  <h4 class="modal-title">Login Clients on {{$calenderDate}}</h4>
                </div>
                <div class="modal-body">
                  <ol>
                    @foreach($clientDatas as $clientId => $data)
                      <li><a href="#client_data_{{$clientId}}_{{$calenderDate}}" data-toggle="modal">{{ $loginClientNames[$clientId] }}</a></li>
                    @endforeach
                  </ol>
                </div>
              </div>
            </div>
          </div>
          @foreach($clientDatas as $clientId => $data)
          <div class="modal todays_data" id="client_data_{{$clientId}}_{{$calenderDate}}" role="dialog" style="display: none;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header" style="overflow-x: auto;">
                  <button type="button" class="close" data-dismiss="modal">×</button>
                  <h4 class="modal-title">Login Activity of {{ $loginClientNames[$clientId] }} on {{$calenderDate}}</h4>
                </div>
                <div class="modal-body">
                  @if(isset($data['log_in_out_time']) && count($data['log_in_out_time']) > 0)
                    <label>Login - Logout:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Session Id</th>
                          <th>In Time</th>
                          <th>Out Time</th>
                          <th>Total Time</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['log_in_out_time'] as $index => $activities)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>{{$activities['session_id']}}</td>
                            <td>{{$activities['login_time']}}</td>
                            <td>{{$activities['logout_time']}}</td>
                            <td>{{$activities['total_time']}}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                  @if(isset($data['batches']) && count($data['batches']) > 0)
                    <label>Batches:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Sub Category/ Sms</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['batches'] as $index => $batchArr)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>{{$batchArr['batch']}}</td>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                  @if(isset($data['courses']) && count($data['courses']) > 0)
                    <label>Courses:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Course</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['courses'] as $index => $courseArr)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>{{$courseArr['course']}}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                  @if(isset($data['subcategories']) && count($data['subcategories']) > 0)
                    <label>Test Subcategories:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Subcategory</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['subcategories'] as $index => $subcategoryArr)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>{{$subcategoryArr['subcategory']}}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                  @if(isset($data['assignments']) && count($data['assignments']) > 0)
                    <label>Assignments:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Assignment</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['assignments'] as $index => $assignmentArr)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>
                              @if(!empty($assignmentArr['assignment']))
                                {!! mb_strimwidth($assignmentArr['assignment'], 0, 400, "...") !!}
                              @else
                                It a Document
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                  @if(isset($data['payables']) && count($data['payables']) > 0)
                    <label>Purchased Sub category or Sms:</label>
                    <table>
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Sub Category/ Sms</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data['payables'] as $index => $payableArr)
                          <tr style="overflow: auto;">
                            <td>{{$index + 1}}</td>
                            <td>{{$payableArr['payable']}}</td>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                    <hr>
                  @endif
                </div>
              </div>
            </div>
          </div>
          @endforeach
        @endif
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
    daycolours = document.getElementById('loginDates').value;
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
    $('td.fc-day-top').on('click',function(ele){
      var selectedDate = $(this).data('date');
      $('#modal_'+selectedDate).modal();
    });
  }
</script>
@stop