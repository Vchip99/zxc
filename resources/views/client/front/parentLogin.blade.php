<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="SHORTCUT ICON" href="img/logo/vedu.png"/>
 <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
 <meta name="csrf-token" content="{{ csrf_token() }}" />
 <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{ asset('css/bootstrap.min.css?ver=1.0')}}" rel="stylesheet">
  <link href="{{ asset('css/jquery-confirm.min.css?ver=1.0')}}" rel="stylesheet">
  <script src="{{ asset('js/jquery.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/bootstrap.min.js?ver=1.0')}}"></script>
  <script src="{{ asset('js/jquery-confirm.min.js?ver=1.0')}}"></script>
  <script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  </script>
  <style type="text/css">
  body{
  background-image: url('{{ url('images/header.jpg')}}');
  background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover
  }

.login-body{
  border-top:10px solid #01bafd;
  padding: 30px;
  background-color: rgba(0, 0, 0, 0.5);
}
.login-body h2{
 color: #fff;
 text-align: center;
 text-transform: uppercase;
}
.login-body  form{
  background-color: rgba(0, 0, 0, 0.5);
  padding: 30px;
}
.login-body form .form-control {
  background: transparent;
  color: #fff;
  font-size: 16px !important;
  width: 100%;
  border: 2px solid rgba(255, 255, 255, 0.2) !important;
  -webkit-transition: 0.5s;
  -o-transition: 0.5s;
  transition: 0.5s;
  border-radius: 0px!important
}
.login-body form .form-group input:focus{
  border: 2px solid #fff !important;
}
@media(max-width: 360px){
.login-body{
   padding: 30px 7px;
   }
  }
  @media (min-width: 619px) and (max-width: 768px){
.col-sm-offset-3 {
    margin-left: 20%;}
.col-sm-6{
  width: 70%;
  }
}
</style>


</head>
<body>
<section style="margin-top: 100px;">
   <div class="container ">
    <div class="row">
      <div class="col-sm-6 col-sm-offset-3 ">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
      @if (session('status'))
          <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ session('status') }}
          </div>
      @endif
      <div class="login-body ">
      <h2>Sign In</h2>
        <form method="POST" action="{{url('parentLogin')}}">
          {{csrf_field()}}
            <div class="form-group ">
              <input type="phone" class="form-control" name="mobile" id="signInPhone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" />
              <span class="help-block"></span>
            </div>
            <label class="hide" style="color: white;" id="signInOtpMessage">Otp sent successfully.</label>
            <div class="form-group hide" id="signInOtpDiv">
              <input name="login_otp" id="login_otp" type="text" class="form-control" placeholder="Enter OTP" >
              <span class="help-block"></span>
            </div>
            <button type="submit" id="loginBtn" name="submit" class="btn btn-info btn-block hide" title="Login" onClick="this.form.submit(); this.disabled=true;">Login</button>
            <button type="button" title="Send Otp" id="sendSignInOtpBtn" class="btn btn-info btn-block" onclick="event.preventDefault(); sendSignInOtp();" >Send OTP</button></br>
            </br>
        </form>
      </div>
      </div>
    </div>
   </div>
</section>
<script type="text/javascript">
  function sendSignInOtp(){
    var mobile = $('#signInPhone').val();
    if(mobile && 10 == mobile.length ){
      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserParentSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        if('success' == result['status']){
          var resultObj = JSON.parse(result['message']);
          if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
            $('#signInOtpDiv').removeClass('hide');
            $('#loginBtn').removeClass('hide');
            $('#sendSignInOtpBtn').addClass('hide');
            $('#signInPhone').prop('readonly', true);
            $('#login_otp').prop('required', true);
            $('#signInOtpMessage').removeClass('hide');
          } else {
            $.confirm({
              title: 'Alert',
              content: resultObj.ErrorMessage
            });
          }
        } else {
          $.confirm({
            title: 'Alert',
            content: result['message']
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