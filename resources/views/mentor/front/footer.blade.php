@php
  if('local' == \Config::get('app.env')){
    $tremsUrl = 'http://localvchip.com/terms-and-conditions';
    $privacyUrl = 'http://localvchip.com/privacy-policy';
  } else {
    $tremsUrl = 'https://vchipedu.com/terms-and-conditions';
    $privacyUrl = 'https://vchipedu.com/privacy-policy';
  }
@endphp
<footer class="footer" style="background: #0c1a1e;">
	<div class="container" style="padding-top: 20px;">
		<div class="row">
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">ABOUT</h2>
				<p><a href="/">Home</a></p>
				<p><a href="{{ url('mentors') }}">Mentors</a></p>
				<p><a href="{{ url('faq') }}">FAQ</a></p>
			</div>
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">MENTOR</h2>
				<p><a href="{{ url('mentors') }}">All Mentors</a></p>
				<p><a href="{{ url('mentor/login')}}">Mentor Login</a></p>
				<p><a href="{{ url('mentor/signup')}}">Mentor Sign-up</a></p>
			</div>
			<div class="col-sm-4">
				<h2 style="text-decoration: underline;">TERMS</h2>
				<p><a href="{{$tremsUrl}}" target="_blank">Terms and Conditions</a></p>
				<p><a href="{{$privacyUrl}}" target="_blank"> Private Policy</a></p>
			</div>
		</div>
	</div>
  <hr>
  <div style="text-align: center;">
      <a href="https://vchiedu.com">©2018 Vchip Technology, All Rights Reserved. Design by: Vchip Technology</a>
  </div>
  <div id="loginUserModel" class="modal fade " role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header"  style="border-bottom: none;">
          <button class="close" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body">
          <div class="modal-data">
              <div class="form-group" style="color: white;">
                <input type="radio" name="signin_model_type" value="email" checked onClick="toggleModelSignIn(this.value);">Email-id
                <input type="radio" name="signin_model_type" value="mobile" onClick="toggleModelSignIn(this.value);">Mobile
              </div>
              <div class="form-group">
                <input id="useremail" name="email" type="email" class="form-control signInModelEmail" placeholder="vchip@gmail.com" autocomplete="off" required>
                <span class="help-block"></span>
              </div>
              <div class="form-group">
                <input id="userpassword" name="password" type="password" class="form-control signInModelEmail" placeholder="password" data-type="password" autocomplete="off" required >
                <span class="help-block"></span>
              </div>
              <div class="form-group hide signInModelMobile">
                <input type="phone" class="form-control" name="mobile" id="signInModelPhone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" />
                <span class="help-block"></span>
              </div>
              <label class="hide" style="color: white;" id="signInModelOtpMessage">Otp sent successfully.</label>
              <div class="form-group hide" id="signInModelOtpDiv">
                <input name="login_otp" id="login_model_otp" type="text" class="form-control" placeholder="Enter OTP" >
                <span class="help-block"></span>
              </div>
              <div id="loginErrorMsg" class="hide">Wrong username or password</div>
              <div id="otpErrorMsg" class="hide" style="color: white;">Wrong otp entered</div>
              <div>
                <label>
                  <input type="radio" name="terms_condition" checked><a href="{{ url('terms-and-conditions')}}">Accepted Terms and Condition</a>
                </label>
              </div>
              <button type="button" value="login" id="loginModelBtn" name="submit" class="btn btn-info btn-block signInModelEmail" onClick="loginUser();">Login</button>
              <button title="Send Otp" id="sendSignInModelOtpBtn" class="btn btn-info btn-block hide signInModelMobile" onclick="event.preventDefault(); sendSignInModelOtp();" >Send OTP</button>
              <br />
              <div class="form-group">
                <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook btn-info btn-block" style="background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login </a>
              </div>
              <div class="form-group">
                <a href="{{ url('/auth/google') }}" class="btn btn-google btn-info btn-block" style="background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login </a>
              </div>
              <div class="form-group">
                <div class="col-md-12 control">
                    <div style="margin-top: 10px; margin-bottom: 20px;  color:#fff;" >
                        Need an account?
                    <a href="{{ url('signup')}}" ">Sign Up</a>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<style type="text/css">
	#loginUserModel .modal, #loginUserModel .modal-content {
	    border-radius: 0px;
	    background-color: rgba(0, 0, 0, 0.5);
	}
	#loginUserModel .modal-dialog {
	    border-top: 10px solid #01bafd;
	}
</style>
<script type="text/javascript">
  	$(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 10000); // <-- time in milliseconds
    });
    function loginUser(){
	    var email = document.getElementById('useremail').value;
	    var password = document.getElementById('userpassword').value;
	    var signInModelPhone = document.getElementById('signInModelPhone').value;
	    var loginOtp = document.getElementById('login_model_otp').value;
	    document.getElementById('loginModelBtn').disabled = true;
	    if((email && password)||(signInModelPhone && loginOtp)){
		      $.ajax({
		          method: "POST",
		          url: "{{ url('userLogin') }}",
		          data: {email:email, password:password,login_otp:loginOtp,mobile:signInModelPhone}
		      })
		      .done(function( msg ) {
		        if('true' == msg){
		          window.location.reload(true);
		        } else {
		          if(loginOtp){
		            document.getElementById('otpErrorMsg').classList.remove('hide');
		          } else {
		            document.getElementById('loginErrorMsg').classList.remove('hide');
		          }
		          document.getElementById('loginModelBtn').disabled = false;
		          $('#userpassword').val('');
		          $('#useremail').val('');
		          $('#signInModelPhone').val('');
		          $('#login_model_otp').val('');
		          $('#signInModelOtpMessage').addClass('hide');
		          $('#signInModelOtpDiv').addClass('hide');
		        }
		      });
	    }
	}
	function checkLogin(){
	    $('#loginUserModel').modal();
	    return false;
  	}
</script>
</footer>