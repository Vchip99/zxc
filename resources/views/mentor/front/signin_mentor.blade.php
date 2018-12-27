 <!DOCTYPE html>
 <html lang="en">
 <head>
  <title>Register</title>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet">
  <script src="{{asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  <link href="{{asset('css/index.css')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_main.css')}}" rel="stylesheet"/>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
  <style type="text/css">
    .fullscreen_bg {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-size: cover;
      background-position: 50% 50%;
      background-image: url('{{ url('images/header.jpg')}}');
      background-repeat:repeat;
    }

    .form-wrap {
    border-top: 10px solid #01bafd;
    position: relative;
    width: 100%;
  }
 .form-wrap h3 {
    font-family: "Kaushan Script", cursive;
  }
   .form-wrap .tab-content {
    z-index: 10;
    position: relative;
    clear: both;
    background: rgba(0, 0, 0, 0.5);
    padding: 30px;}
  .form-wrap .tab-content h3 {
    color: #fff;  }
  .form-wrap .tab-content label {
    color: rgba(255, 255, 255, 0.8);  }
  .form-wrap .tab-content .tab-content-inner {
    display: none;  }
  .form-wrap .tab-content .tab-content-inner.active {
    display: block;  }
  .form-wrap .tab-content .form-control {
    color: #fff !important;
    border: 2px solid rgba(255, 255, 255, 0.2);
  }
  option{
    color: #000;
    background-color:  transparent !important;
  }
  div.multiple{
    color: #fff;
  }
  .v_container{
    margin-top:50px ;
    margin-bottom:50px ;
  }

  </style>

</head>
<body>
  <div id="fullscreen_bg" class="fullscreen_bg"></div>
  <div class="container v_container">
    <div class="overlay"></div>
    <div class="vchip-container " >
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <div class="form-wrap">
            <div class="tab">
              <div class="tab-content">
                <div class="tab-content-inner active" data-content="signup">
                  <ul class=" nav-tabs v_login_reg text-center">
                    <li class="active"><a data-toggle="tab" href="#home">Mentor Sign In</a></li>
                  </ul>
                  <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">
                      <div class="form-group" style="color: white;">
                        <input type="radio" name="signin_type" id="signinRadioEmail" value="email" checked onClick="toggleSignIn(this.value);">Email-id
                        <input type="radio" name="signin_type" value="mobile" onClick="toggleSignIn(this.value);">Mobile
                      </div>
                      <form id="loginForm" method="post" action="{{ url('mentor/login') }}">
                          {!! csrf_field() !!}
                        <div class="form-group">
                          <input id="email" name="email" type="text" class="form-control signInEmail" placeholder="Email-id" onfocus="this.type='email'" autocomplete="off" required>
                          <span class="help-block"></span>
                        </div>
                        <div class="form-group">
                          <input id="password" name="password" type="text" class="form-control signInEmail" placeholder="password" data-type="password" onfocus="this.type='password'" autocomplete="off" required >
                          <span class="help-block"></span>
                        </div>
                        <div class="form-group hide signInMobile">
                          <input type="phone" class="form-control" name="mobile" id="signInPhone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" />
                          <span class="help-block"></span>
                        </div>
                        <label class="hide" style="color: white;" id="signInOtpMessage">Otp sent successfully.</label>
                        <div class="form-group hide" id="signInOtpDiv">
                          <input name="login_otp" id="login_otp" type="text" class="form-control" placeholder="Enter OTP" >
                          <span class="help-block"></span>
                        </div>
                        <div id="loginErrorMsg" class="alert alert-error hide">Wrong username or password</div>
                        <button type="submit" value="login" name="submit" id="loginBtn" class="btn btn-info btn-block signInEmail" data-toggle="tooltip" title="Login">Login</button>
                        <button title="Send Otp" id="sendSignInOtpBtn" class="btn btn-info btn-block hide signInMobile" onclick="event.preventDefault(); sendSignInOtp();" >Send OTP</button>
                      </form>
                      <br/>
                      <a href="{{ url('/')}}" class="btn btn-default btn-block"> Home</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function toggleSignIn(value){
    if('email' == value){
      $('.signInEmail').removeClass('hide');
      $('.signInMobile').addClass('hide');
      $('#sendSignInOtpBtn').addClass('hide');
      $('#password').prop('required', true);
      $('#email').prop('required', true);
      $('#password').val('');
      $('#email').val('');
      $('#signInPhone').val('');
      $('#loginBtn').prop('type','submit');
    } else {
      $('.signInEmail').addClass('hide');
      $('.signInMobile').removeClass('hide');
      $('#sendSignInOtpBtn').removeClass('hide');
      $('#password').val('');
      $('#email').val('');
      $('#password').prop('required', false);
      $('#email').prop('required', false);
      $('#signInPhone').val('');
      $('#loginBtn').prop('type','button');
    }
  }

  function sendSignInOtp(){
    var mobile = $('#signInPhone').val();
    if(mobile && 10 == mobile.length ){
    $('#signInPhone').prop('readonly', true);
      $.ajax({
        method: "POST",
        url: "{{url('sendMentorSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#signInOtpMessage').removeClass('hide');
          $('#signInOtpDiv').removeClass('hide');
          $('#loginBtn').removeClass('hide');
          $('#sendSignInOtpBtn').addClass('hide');
          $('#loginBtn').prop('type','submit');
        } else {
          $('#sendSignInOtpBtn').removeClass('hide');
          $('#signInPhone').prop('readonly', false);
          $('#signInOtpMessage').addClass('hide');
          $('#signInOtpDiv').addClass('hide');
          $('#loginBtn').addClass('hide');
          $('#loginBtn').prop('type','button');
          $.confirm({
            title: 'Alert',
            content: 'Something wrong in otp result.'
          });
        }
      });
    } else if(!mobile) {
      $.confirm({
        title: 'Alert',
        content: 'Enter mobile no.'
      });
    } else if(mobile.length < 10){
      $.confirm({
        title: 'Alert',
        content: 'Enter 10 digit mobile no.'
      });
    }
  }
  </script>
</body>
</html>