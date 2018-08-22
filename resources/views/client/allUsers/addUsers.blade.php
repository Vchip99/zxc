@extends('client.dashboard')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Add Users </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-group"></i> Users Info </li>
      <li class="active"> Add Users </li>
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
  &nbsp;
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
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Download Excel File:</label>
      <div class="col-sm-3">
        <a class="btn btn-primary cust-btn" href="{{asset('Download Add Users File.xlsx')}}" download data-toggle="tooltip" data-placement="bottom">Download Add Users File</a>
      </div>
      <div class="col-sm-3">
        <button id="submitButton" type="submit" class="btn btn-primary cust-btn1" data-toggle="modal" data-target="#modelAddUsers">Upload Users</button>
      </div>

      <div class="modal fade" id="modelAddUsers" >
        <div class="modal-dialog" role="document" >
          <div class="modal-content" >
            <div class="modal-header">
              <h5 class="modal-title model-title">Add Users</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="uploadClientUsersForm" action="{{url('uploadClientUsers')}}" method="POST" enctype="multipart/form-data">
              {{csrf_field()}}
              <div class="modal-body">
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Add Users:</label>
                  <div class="col-sm-3">
                    <input type="file" class="model-input" id="upload_users" name="users" required="true">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary cust-btn1" data-dismiss="modal" onclick="uploadUsers();">Add Users</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="form-group">
      <label class="col-sm-2 col-form-label" for="name"></label>
      <input type="radio" name="signup_type" id="signupRadioEmail" value="email" checked onClick="toggleSignUp(this.value);">Email-id/User-id
      <input type="radio" name="signup_type" value="mobile" onClick="toggleSignUp(this.value);">Mobile
    </div>
    <div id="signUpEmailDiv">
      <form action="{{url('addEmailUser')}}" method="POST" id="addEmailUser">
        {{ csrf_field() }}
        <div id="allUsers">
          <div class="form-group row" id="1">
            <label class="col-sm-2 col-form-label" >User Details <sup>*<sup></label>
            <div class="col-sm-2">
              <input type="text" class="form-control" id="name_1" name="name_1" value="" placeholder="User Name" required>
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control" name="phone_1" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}">
            </div>
            <div class="col-sm-2">
              <input type="text" class="form-control" name="email_1" value="" placeholder="Email Id/User Id" required>
            </div>
            <div class="col-sm-2">
              <input type="password" class="form-control" name="password_1" value="" placeholder="Password" required>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-3">
            <button id="addUser" class="btn btn-primary cust-btn2" onclick="event.preventDefault(); addNewUser();" >Add User</button>
            <button id="register" type="submit" class="btn btn-primary cust-btn2">Submit</button>
          </div>
        </div>
      </form>
    </div>
    <div class="hide" id="signUpPhoneDiv">
      <form action="{{url('addMobileUser')}}" method="POST">
        {{ csrf_field() }}
        <div class="form-group row">
          <label class="col-sm-2 col-form-label" for="name">User Name <sup>*<sup></label>
          <div class="col-sm-3">
            <input id="signUpNameInput" type="text" class="form-control" name="name" value="" placeholder="User Name" required>
          </div>
        </div>
        <div class="form-group row" >
          <label class="col-sm-2 col-form-label" for="name">Mobile No <sup>*<sup></label>
          <div class="col-sm-3">
            <input type="text" class="form-control " id="signUpPhone" name="phone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required>
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
  $('#addEmailUser').on('submit', function() {
    if(false == $('#addEmailUser').get(0).checkValidity()) {
      return false;
    }
    $('#register').prop('disabled', true);
  });
  function toggleSignUp(value){
    if('email' == value){
      $('#signUpEmailDiv').removeClass('hide');
      $('#signUpPhoneDiv').addClass('hide');
    } else {
      $('#signUpEmailDiv').addClass('hide');
      $('#signUpPhoneDiv').removeClass('hide');
    }
    emptySignUpForm();
  }
  function emptySignUpForm(){
    $('#signUpNameInput').val('');
    $('#signUpPhone').val('');
  }
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
        url: "{{url('sendClientUserSignUpOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#signUpOtpMessage').removeClass('hide');
          $('#user_otp').focus();
        } else {
          $.confirm({
            title: 'Alert',
            content: 'Something went wrong while sending otp.'
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

  function addNewUser(){
    var allUsers = document.getElementById('allUsers');
    var latestCount = ($('#allUsers input').length/4) + 1;
    var deletePath = "{{ asset('images/delete3.png')}}";

    var eleDiv = document.createElement('div');
    eleDiv.className = 'form-group row';
    eleDiv.setAttribute("id", latestCount);

    var eleLabel = document.createElement('label');
    eleLabel.className = 'col-sm-2 col-form-label';
    eleDiv.appendChild(eleLabel);

    var firstDiv = document.createElement('div');
    firstDiv.className = 'col-sm-2';
    firstDiv.innerHTML = '<input type="text" class="form-control" id="name_'+latestCount+'" name="name_'+latestCount+'" value="" placeholder="User Name" required>';
    eleDiv.appendChild(firstDiv);

    var phoneDiv = document.createElement('div');
    phoneDiv.className = 'col-sm-2';
    phoneDiv.innerHTML = '<input type="text" class="form-control" name="phone_'+latestCount+'" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}">';
    eleDiv.appendChild(phoneDiv);

    var secondDiv = document.createElement('div');
    secondDiv.className = 'col-sm-2';
    secondDiv.innerHTML = '<input type="text" class="form-control" name="email_'+latestCount+'" value="" placeholder="Email Id/User Id" required>';
    eleDiv.appendChild(secondDiv);

    var thirdDiv = document.createElement('div');
    thirdDiv.className = 'col-sm-2';
    thirdDiv.innerHTML = '<input type="password" class="form-control" name="password_'+latestCount+'" value="" placeholder="Password" required>';
    eleDiv.appendChild(thirdDiv);

    var eleImg = document.createElement('img');
    eleImg.className = 'img-vsm';
    eleImg.setAttribute("src", deletePath);
    eleImg.setAttribute("onclick", 'removeElement(\'allUsers\','+latestCount+')');
    eleDiv.appendChild(eleImg);
    allUsers.appendChild(eleDiv);
    $('#name_'+latestCount).focus();
  }
  function removeElement(parentDiv, childDiv){
    if (document.getElementById(childDiv)){
      var child = document.getElementById(childDiv);
      var parent = document.getElementById(parentDiv);
      parent.removeChild(child);
    }
  }
  function uploadUsers(){
    if(document.getElementById('upload_users').value.length > 0){
      document.getElementById('uploadClientUsersForm').submit();
    } else {
      alert('please add a users file.');
          return false;
    }
  }
</script>
@stop