@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
@stop
@section('module_title')
  <section class="content-header">
    <h1> Add Parent  </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-graduation-cap"></i> Add Parent </li>
      <li class="active"> Add Parent </li>
    </ol>
  </section>
  <style type="text/css">
    sup {
      color: red;
    }
    .cust-btn{
      width: 200px !important;
    }
    .cust-btn1{
      width: 110px !important;
    }
    .cust-btn2{
      width: 90px !important;
    }
  </style>
@stop
@section('dashboard_content')
  <div class="container admin_div">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    @if(count($errors) > 0)
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif
    <div>
      <form action="{{url('addParent')}}" method="POST">
        {{ csrf_field() }}
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" for="name">Parent Name <sup>*<sup></label>
          <div class="col-sm-3">
            <input id="signUpNameInput" type="text" class="form-control" name="name" value="{{$loginUser->parent_name}}" placeholder="Parent Name" required>
          </div>
        </div>
        <div class="form-group row" >
          <label class="col-sm-2 col-form-label" for="name">Mobile No <sup>*<sup></label>
          <div class="col-sm-3">
            <input type="text" class="form-control " id="signUpPhone" name="phone" value="{{$loginUser->parent_phone}}" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required>
          </div>
        </div>
        <div class="form-group row hide" id="signUpOtpDiv">
          <label class="col-sm-2 col-form-label">Enter OTP <sup>*<sup></label>
          <div class="col-sm-3">
            <input id="user_otp" name="user_otp" type="text" class="form-control" placeholder="Enter OTP" required>
          </div>
          <div class="col-sm-3">
            <label class="hide" style="color: white;" id="signUpOtpMessage">Otp sent successfully.</label>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-3">
            <button title="Send Otp" id="sendSignUpOtpBtn" class="btn btn-primary signUpMobile cust-btn2" onclick="event.preventDefault(); sendSignUpOtp();">Send OTP</button>
            <button id="registerMobile" type="submit" class="btn btn-primary hide cust-btn2">Submit</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<script type="text/javascript">
  function sendSignUpOtp(){
    var mobile = $('#signUpPhone').val();
    var name = $('#signUpNameInput').val();
    if(10 == mobile.length && name.length){
      $('#signUpOtpDiv').removeClass('hide');
      $('#registerMobile').removeClass('hide');
      $('#sendSignUpOtpBtn').addClass('hide');
      $('#signUpPhone').prop('readonly', true);
      $('#signUpNameInput').prop('readonly', true);
      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserParentAddOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        var resultObj = JSON.parse(JSON.stringify(result));
        if('success' == resultObj.status){
          var jsonMessage = JSON.parse(resultObj.message);
          if('000' == jsonMessage.ErrorCode && 'Success' == jsonMessage.ErrorMessage){
            $('#signUpOtpMessage').removeClass('hide');
            $('#user_otp').focus();
          } else {
            $.confirm({
              title: 'Alert',
              content: 'Something wrong in otp result.'
            });
          }
        } else {
          $('#sendSignUpOtpBtn').removeClass('hide');
          $('#registerMobile').addClass('hide');
          $('#signUpOtpDiv').addClass('hide');
          $('#signUpPhone').prop('readonly', false);
          $('#signUpNameInput').prop('readonly', false);
          $.confirm({
            title: 'Alert',
            content: resultObj.message
          });
        }
      });
    } else if(10 > mobile.length){
      $.confirm({
        title: 'Alert',
        content: 'enter 10 digit mobile no.'
      });
    } else if(!name.length){
      $.confirm({
        title: 'Alert',
        content: 'enter name.'
      });
    } else {
      $.confirm({
        title: 'Alert',
        content: 'enter mobile no.'
      });
    }
  }

</script>
@stop
