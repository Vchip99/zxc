@extends('dashboard.dashboard')
@section('dashboard_header')
  <style type="text/css">
    .btn-primary{
      width: 150px;
    }
    .btn{
      border-radius: 2px !important;
    }
  </style>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Time Table </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Time Table</li>
      <li class="active">My Time Table</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
    <div class="row">
      <button class="btn btn-primary" data-id="collegeTT" onClick="toggleTT(this)">College Time Table</button> &nbsp;
      <button class="btn btn-default" data-id="examTT" onClick="toggleTT(this)">Exam Time Table</button>&nbsp;
      <button class="btn btn-default" data-id="calender" onClick="toggleTT(this)">College Calendar</button>&nbsp;
    </div>
    <br>
    <div id="collegeTT" class="row" style="max-height: 600px; width: 100%; overflow-x: auto;">
      <img src="{{asset($collegeTimeTable->image_path)}}">
    </div>
    <br>
    <div id="examTT" class="row hide" style="max-height: 600px; width: 100%; overflow-x: auto;">
      <img src="{{asset($examTimeTable->image_path)}}">
    </div>
    <br>
    @if(preg_match('/(\.jpg|\.png|\.jpeg)$/', $collegeCalendar->image_path))
      <div id="calender" class="row hide" style="max-height: 600px; width: 100%; overflow-x: auto;">
        <img src="{{asset($collegeCalendar->image_path)}}"  width="100%" height="100%">
      </div>
    @else
      <div id="calender" class="row hide">
         <object data="{{asset($collegeCalendar->image_path)}}" type="application/pdf" width="100%" height="1000">
          <a href="{{asset($collegeCalendar->image_path)}}"></a>
         </object>
      </div>
    @endif
  </div>
<script type="text/javascript">
  function toggleTT(ele){
    $('.btn').removeClass('btn-primary');
    $('.btn').addClass('btn-default');
    if('collegeTT' == $(ele).data('id')){
      $('#collegeTT').removeClass('hide');
      $('#examTT').addClass('hide');
      $('#calender').addClass('hide');
    } else if('examTT' == $(ele).data('id')){
      $('#examTT').removeClass('hide');
      $('#collegeTT').addClass('hide');
      $('#calender').addClass('hide');
    } else if('calender' == $(ele).data('id')){
      $('#calender').removeClass('hide');
      $('#collegeTT').addClass('hide');
      $('#examTT').addClass('hide');
    }
    $(ele).removeClass('btn-default');
    $(ele).addClass('btn-primary');
  }
</script>
@stop