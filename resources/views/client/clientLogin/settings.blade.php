@extends('client.dashboard')
@section('module_title')
  <section class="content-header">
    <h1> Settings </h1>
  </section>
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    @media screen and (max-width: 320px) {
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
        padding-left: 0px !important;
        padding-right: 0px !important;
      }
    }

    @media screen and (min-width: 350px) and (max-width: 415px){
      .content,.col-sm-12,.v-container, .container, .col-md-7, .list-group .list-group-item,.col-xs-7{
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
          <div class="col-md-offset-2">
            <div class="panel panel-default">
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Allow Login To Non-Verified Email-Id</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->allow_non_verified_email)
                            <input type="checkbox" id="allow_non_verified_email" checked onClick="toggleNonVerifiedEmail();">
                          @else
                            <input type="checkbox" id="allow_non_verified_email" onClick="toggleNonVerifiedEmail();">
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Student login using </b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->login_using)
                            <input type="radio" name="login_using" value="1" checked onClick="changeSetting(this);"> Facebook
                          @else
                            <input type="radio" name="login_using" value="1" onClick="changeSetting(this);"> Facebook
                          @endif
                          @if(2 == $loginUser->login_using)
                            <input type="radio" name="login_using" value="2" checked onClick="changeSetting(this);"> Google
                          @else
                            <input type="radio" name="login_using" value="2" onClick="changeSetting(this);"> Google
                          @endif
                          @if(3 == $loginUser->login_using)
                            <input type="radio" name="login_using" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="login_using" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->login_using)
                            <input type="radio" name="login_using" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="login_using" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about absent class to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->absent_sms)
                            <input type="radio" name="absent_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="absent_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->absent_sms)
                            <input type="radio" name="absent_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="absent_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->absent_sms)
                            <input type="radio" name="absent_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="absent_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->absent_sms)
                            <input type="radio" name="absent_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="absent_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about exam schedule to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->exam_sms)
                            <input type="radio" name="exam_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="exam_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->exam_sms)
                            <input type="radio" name="exam_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="exam_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->exam_sms)
                            <input type="radio" name="exam_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="exam_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->exam_sms)
                            <input type="radio" name="exam_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="exam_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about offline exam marks submission to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->offline_exam_sms)
                            <input type="radio" name="offline_exam_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="offline_exam_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->offline_exam_sms)
                            <input type="radio" name="offline_exam_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="offline_exam_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->offline_exam_sms)
                            <input type="radio" name="offline_exam_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="offline_exam_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->offline_exam_sms)
                            <input type="radio" name="offline_exam_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="offline_exam_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about assignment to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->assignment_sms)
                            <input type="radio" name="assignment_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="assignment_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->assignment_sms)
                            <input type="radio" name="assignment_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="assignment_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->assignment_sms)
                            <input type="radio" name="assignment_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="assignment_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->assignment_sms)
                            <input type="radio" name="assignment_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="assignment_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about notice to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->notice_sms)
                            <input type="radio" name="notice_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="notice_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->notice_sms)
                            <input type="radio" name="notice_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="notice_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->notice_sms)
                            <input type="radio" name="notice_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="notice_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->notice_sms)
                            <input type="radio" name="notice_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="notice_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about energnecy notice to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->emergency_notice_sms)
                            <input type="radio" name="emergency_notice_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="emergency_notice_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->emergency_notice_sms)
                            <input type="radio" name="emergency_notice_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="emergency_notice_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->emergency_notice_sms)
                            <input type="radio" name="emergency_notice_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="emergency_notice_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->emergency_notice_sms)
                            <input type="radio" name="emergency_notice_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="emergency_notice_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about holiday to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->holiday_sms)
                            <input type="radio" name="holiday_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="holiday_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->holiday_sms)
                            <input type="radio" name="holiday_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="holiday_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->holiday_sms)
                            <input type="radio" name="holiday_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="holiday_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->holiday_sms)
                            <input type="radio" name="holiday_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="holiday_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about Individual Message to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->individual_sms)
                            <input type="radio" name="individual_sms" value="1" checked onClick="changeSetting(this);"> Student
                          @else
                            <input type="radio" name="individual_sms" value="1" onClick="changeSetting(this);"> Student
                          @endif
                          @if(2 == $loginUser->individual_sms)
                            <input type="radio" name="individual_sms" value="2" checked onClick="changeSetting(this);"> Parent
                          @else
                            <input type="radio" name="individual_sms" value="2" onClick="changeSetting(this);"> Parent
                          @endif
                          @if(3 == $loginUser->individual_sms)
                            <input type="radio" name="individual_sms" value="3" checked onClick="changeSetting(this);"> Both
                          @else
                            <input type="radio" name="individual_sms" value="3" onClick="changeSetting(this);"> Both
                          @endif
                          @if(4 == $loginUser->individual_sms)
                            <input type="radio" name="individual_sms" value="4" checked onClick="changeSetting(this);"> None
                          @else
                            <input type="radio" name="individual_sms" value="4" onClick="changeSetting(this);"> None
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row toggle" id="dropdown-detail-1" data-toggle="detail-1">
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-xs-5 "><b>Send sms about lecture to</b></div>
                        <div class="col-xs-7 pull-left">
                          @if(1 == $loginUser->lecture_sms)
                            <input type="radio" name="lecture_sms" value="1" checked onClick="changeSetting(this);"> Teacher
                          @else
                            <input type="radio" name="lecture_sms" value="1" onClick="changeSetting(this);"> Teacher
                          @endif
                          @if(0 == $loginUser->lecture_sms)
                            <input type="radio" name="lecture_sms" value="0" checked onClick="changeSetting(this);"> No
                          @else
                            <input type="radio" name="lecture_sms" value="0" onClick="changeSetting(this);"> No
                          @endif
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
  @include('footer.clientLiveChat')
  <script type="text/javascript">
    var currentToken = $('meta[name="csrf-token"]').attr('content');
    function toggleNonVerifiedEmail(){
      $.ajax({
          method:'POST',
          url: "{{url('toggleNonVerifiedEmailStatus')}}",
          data:{_token:currentToken}
      }).done(function( status ) {
        var currentStatus = $('#allow_non_verified_email').prop('checked');
        if(true == currentStatus && 1 == status){
          $('#allow_non_verified_email').prop('checked', true);
        } else {
          $('#allow_non_verified_email').prop('checked', false);
        }
      });
    }

    function changeSetting(ele){
      var column = $(ele).attr('name');
      var value = $(ele).attr('value');
      if(column && value){
        $.ajax({
            method:'POST',
            url: "{{url('changeClientSetting')}}",
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