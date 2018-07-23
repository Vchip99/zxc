@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  @media only screen and (max-width: 760px), (max-device-width: 1024px) and (min-device-width: 768px){
  td {
      padding-left: 50% !important;
  }
}
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Attendance  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-tasks"></i> Attendance </li>
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

  <div class="form-group row">
    <div class="col-md-3 mrgn_10_btm">
      <select class="form-control" id="year" name="year" title="year">
        <option value="">Select Year</option>
        @for($i=2018;$i<=2050;$i++)
          @if($currnetYear == $i)
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
            <option value="{{$batch->id}}">{{$batch->name}}</option>
          @endforeach
        @endif
     </select>
    </div>
  </div>
  <div class="form-group row">
    <table class="" id="clientUserAssignment">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Month</th>
          <th>Present Dates</th>
          <th>Absent Days/No. of Days</th>
        </tr>
      </thead>
      <tbody id="studentAttendance">
        @if(count($months) > 0)
          @foreach($months as $index => $month)
            <tr class="student" id="div_student_{{$month}}" >
              <td> {{ $index}} </td>
              <td>{{$month}}</td>
              <td>{{(isset($attendanceCount[$index]) && isset($attendanceCount[$index]['present_date']))?implode(',',$attendanceCount[$index]['present_date']):0}}</td>
              <td>{{(isset($attendanceCount[$index]) && isset($attendanceCount[$index]['absent_date']))?count($attendanceCount[$index]['absent_date']):0}}/{{(isset($attendanceCount[$index]) && $attendanceCount[$index]['attendance_date'])?count($attendanceCount[$index]['attendance_date']):0}}</td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>
  </div>
<script type="text/javascript">
  function getAttendance(ele){
    batchId = parseInt($(ele).val());
    var year = document.getElementById('year').value;
    if( 0 < batchId ){
      $.ajax({
        method: "POST",
        url: "{{url('getAttendance')}}",
        data: {batch_id:batchId,year:year}
      })
      .done(function( msgs ) {
        body = document.getElementById('studentAssignment');
        body.innerHTML = '';
        console.log(msgs['months']);
        if(Object.keys(msgs['months']).length > 0){
           $.each(msgs['months'], function(idx, msg) {
            var eleTr = document.createElement('tr');

            var eleIndex = document.createElement('td');
            eleIndex.innerHTML = idx;
            eleTr.appendChild(eleIndex);

            var eleMonth = document.createElement('td');
            eleMonth.innerHTML = msg;
            eleTr.appendChild(eleMonth);

            var elePresent = document.createElement('td');
            if(msgs['attendanceCount'][idx] && msgs['attendanceCount'][idx]['present_date']){
              elePresent.innerHTML = msgs['attendanceCount'][idx]['present_date'];
            } else {
              elePresent.innerHTML = 0;
            }
            eleTr.appendChild(elePresent);

            var eleAbsent = document.createElement('td');
            if(msgs['attendanceCount'][idx]){
              if(msgs['attendanceCount'][idx]['absent_date'] && msgs['attendanceCount'][idx]['attendance_date']){
                eleAbsent.innerHTML = msgs['attendanceCount'][idx]['absent_date'].length+'/'+msgs['attendanceCount'][idx]['attendance_date'].length;
              } else if(msgs['attendanceCount'][idx]['absent_date']){
                eleAbsent.innerHTML = msgs['attendanceCount'][idx]['absent_date'].length+'/'+0;
              } else if(msgs['attendanceCount'][idx]['attendance_date']){
                eleAbsent.innerHTML = 0+'/'+msgs['attendanceCount'][idx]['attendance_date'].length;
              } else {
                eleAbsent.innerHTML = 0+'/'+0;
              }
            } else {
              eleAbsent.innerHTML = 0+'/'+0;
            }
            eleTr.appendChild(eleAbsent);

            body.appendChild(eleTr);
          });
        }
      });
    }
  }
</script>
@stop