@extends('dashboard.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Manage Settings </h1>
  </section>
  <style type="text/css">
    @media screen and (max-width: 500px) {
      .v-container .container, .list-group .list-group-item, .col-xs-12, .col-xs-9,.col-md-offset-2, .col-xs-10{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }
  </style>
@stop
@section('dashboard_content')
	<div class="content-wrapper v-container tab-content" >
    <div id="profile" class="tab-pane active">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="panel panel-default">
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-8 "><b>Send sms about absent class</b></div>
                        <div class="col-xs-4 pull-left">
                            <input type="radio" name="absent_sms" value="1" @if(1 == $college->absent_sms) checked @endif onClick="changeSetting(this);"> Yes
                            <input type="radio" name="absent_sms" value="0" @if(0 == $college->absent_sms) checked @endif onClick="changeSetting(this);"> No
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-8 "><b>Send sms about offline exam marks</b></div>
                        <div class="col-xs-4 pull-left">
                            <input type="radio" name="offline_exam_sms" value="1" @if(1 == $college->offline_exam_sms) checked @endif onClick="changeSetting(this);"> Yes
                            <input type="radio" name="offline_exam_sms" value="0" @if(0 == $college->offline_exam_sms) checked @endif onClick="changeSetting(this);"> No
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-8 "><b>Send sms about extra class</b></div>
                        <div class="col-xs-4 pull-left">
                            <input type="radio" name="lecture_sms" value="1" @if(1 == $college->lecture_sms) checked @endif onClick="changeSetting(this);"> Yes
                            <input type="radio" name="lecture_sms" value="0" @if(0 == $college->lecture_sms) checked @endif onClick="changeSetting(this);"> No
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-8 "><b>Send sms about exam schedule</b></div>
                        <div class="col-xs-4 pull-left">
                            <input type="radio" name="exam_sms" value="1" @if(1 == $college->exam_sms) checked @endif onClick="changeSetting(this);"> Yes
                            <input type="radio" name="exam_sms" value="0" @if(0 == $college->exam_sms) checked @endif onClick="changeSetting(this);"> No
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about notice</b></div>
                        <div class="col-xs-7 pull-left">
                            @php
                              if(!empty($college->notice_sms)){
                                $noticeValues = explode(',',$college->notice_sms);
                              } else {
                                $noticeValues = [];
                              }
                            @endphp
                            <input type="checkbox" name="notice_sms" value="1" @if(in_array(1,$noticeValues)) checked @endif onClick="changeSetting(this);"> Students
                            <input type="checkbox" name="notice_sms" value="2" @if(in_array(2,$noticeValues)) checked @endif onClick="changeSetting(this);"> Lecturers & Hods
                            <input type="checkbox" name="notice_sms" value="3" @if(in_array(3,$noticeValues)) checked @endif onClick="changeSetting(this);"> Director & TPO
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about energnecy notice</b></div>
                        <div class="col-xs-7 pull-left">
                            @php
                              if(!empty($college->emergency_notice_sms)){
                                $emergencyNoticeValues = explode(',',$college->emergency_notice_sms);
                              } else {
                                $emergencyNoticeValues = [];
                              }
                            @endphp
                            <input type="checkbox" name="emergency_notice_sms" value="1" @if(in_array(1,$emergencyNoticeValues)) checked @endif onClick="changeSetting(this);"> Students
                            <input type="checkbox" name="emergency_notice_sms" value="2" @if(in_array(2,$emergencyNoticeValues)) checked @endif onClick="changeSetting(this);"> Lecturers & Hods
                            <input type="checkbox" name="emergency_notice_sms" value="3" @if(in_array(3,$emergencyNoticeValues)) checked @endif onClick="changeSetting(this);"> Director & TPO
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about holiday</b></div>
                        <div class="col-xs-7 pull-left">
                          @php
                            if(!empty($college->holiday_sms)){
                              $holidayValues = explode(',',$college->holiday_sms);
                            } else {
                              $holidayValues = [];
                            }
                          @endphp
                          <input type="checkbox" name="holiday_sms" value="1" @if(in_array(1,$holidayValues)) checked @endif onClick="changeSetting(this);"> Students
                          <input type="checkbox" name="holiday_sms" value="2" @if(in_array(2,$holidayValues)) checked @endif onClick="changeSetting(this);"> Lecturers & Hods
                          <input type="checkbox" name="holiday_sms" value="3" @if(in_array(3,$holidayValues)) checked @endif onClick="changeSetting(this);"> Director & TPO
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-8 "><b>Send sms about assignment</b></div>
                        <div class="col-xs-4 pull-left">
                            <input type="radio" name="assignment_sms" value="1" @if(1 == $college->assignment_sms) checked @endif onClick="changeSetting(this);"> Yes
                            <input type="radio" name="assignment_sms" value="0" @if(0 == $college->assignment_sms) checked @endif onClick="changeSetting(this);"> No
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
       </div>
    </div>
  </div>
<script type="text/javascript">
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    function changeSetting(ele){
      var column = $(ele).attr('name');
      var value = $(ele).attr('value');
      if(column && value){
        $.ajax({
            method:'POST',
            url: "{{url('changeCollegeSetting')}}",
            data:{_token:currentToken,column:column,value:value}
        }).done(function( status ) {
          if('false' == status){
            window.location.reload();
          }
        });
      }
    }
  </script>
@stop