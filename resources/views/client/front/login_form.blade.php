@if(!empty($subdomain->background_image))
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="{{ $subdomain->background_image }}" data-stellar-background-ratio="0.5">
@else
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="background-image: url('{{ url('images/header.jpg')}}');background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover;" data-stellar-background-ratio="0.5">

@endif
  <div class="overlay"></div>
  <div class="vchip-container ">
    <div class="row">
      <div class="col-md-12 col-md-offset-0 text-left">
        <div class="row mrgn_200_top">
        @if(Session::has('message'))
          <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
        @if(session('status'))
            <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session('status') }}
            </div>
        @endif
          <div class="col-md-7 mt-text animate-box" data-animate-effect="fadeInUp">
            <h1 class="cursive-font animated bounceInLeft">{!! $subdomain->home_content_value !!}</h1>
          </div>
          @if(!Auth::guard('clientuser')->user())
          <div class="col-md-5  animated " data-animate-effect="fadeInRight">
            <div class="form-wrap">
              <div class="tab">
                <div class="tab-content">
                  <div class="tab-content-inner active" data-content="signup">
                    @if(count($errors) > 0)
                      <div class="alert alert-danger">
                        <ul>
                          @foreach ($errors->all() as $error)
                            @if('verify_email' == $error)
                              <li><a href="{{ url('verifyAccount')}}">Click here to resend verification email</a></li>
                            @else
                              <li>{{ $error }}</li>
                            @endif
                          @endforeach
                        </ul>
                      </div>
                    @endif
                    <ul class=" nav-tabs v_login_reg text-center">
                      <li class="active"><a data-toggle="tab" href="#home" title="Sign In">Sign In</a></li>
                      <li><a data-toggle="tab" href="#menu1" title="Sign Up">Sign Up</a></li>
                    </ul>
                    <div class="tab-content">
                      <div id="home" class="tab-pane fade in active">
                        <div class="form-group" style="color: white;">
                          <input type="radio" name="signin_type" id="signinRadioEmail" value="email" checked onClick="toggleSignIn(this.value);">Email-id/User-id
                          <input type="radio" name="signin_type" value="mobile" onClick="toggleSignIn(this.value);">Mobile
                        </div>
                        <form id="loginForm" method="post" action="{{ url('login') }}">
                          {!! csrf_field() !!}
                          <div class="form-group signInEmail">
                            <input name="email" type="text" id="signInEmail" class="form-control" placeholder="vchip@gmail.com" autocomplete="off">
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group signInEmail">
                            <input name="password" type="password" id="signInPassword" class="form-control" placeholder="password" data-type="password" autocomplete="off" >
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group hide signInMobile">
                            <input type="phone" class="form-control" name="mobile" id="signInPhone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" />
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group hide" id="signInOtpDiv">
                            <input name="login_otp" id="login_otp" type="text" class="form-control" placeholder="Enter OTP" >
                            <span class="help-block"></span>
                          </div>
                          <button type="submit" id="loginBtn" name="submit" class="btn btn-info btn-block signInEmail" title="Login">Login</button>
                          <button title="Send Otp" id="sendSignInOtpBtn" class="btn btn-info btn-block hide signInMobile" onclick="event.preventDefault(); sendSignInOtp();" >Send OTP</button></br>
                          </br>
                        </form>
                        <div>
                        <div class="form-group">
                            <a href="{{ url('/auth/facebook') }}" class="btn btn-info btn-block" style="color: white; background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login</a>
                        </div>
                        <div class="form-group">
                            <a href="{{ url('/auth/google') }}" class="btn btn-info btn-block" style="color: white;background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login</a>
                        </div>
                        <a href="{{ url('forgotPassword')}}" title="Forgot Password">Forgot Password?</a></div>
                      </div>
                      <div id="menu1" class="tab-pane fade">
                        <form id="registerUser" method="post" action="{{ url('register')}}">
                          {{ csrf_field() }}
                          <div class="form-group" style="color: white;">
                            <input type="radio" name="signup_type" id="signupRadioEmail" value="email" checked onClick="toggleSignUp(this.value);">Email-id/User-id
                            <input type="radio" name="signup_type" value="mobile" onClick="toggleSignUp(this.value);">Mobile
                          </div>
                          <div class="form-group">
                            <input id="signUpNameInput" type="text" class="form-control" name="name" value="" placeholder="User Name" autocomplete="off" required/>
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input type="phone" class="form-control" name="phone" id="signUpPhone" value="" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" />
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="signUpEmailInput" name="email" type="text" class="form-control signUpEmail" autocomplete="off" placeholder="Email-id/User-id" >
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="signUpPassword" name="password" type="password" class="form-control signUpEmail" data-type="password" autocomplete="off" placeholder="password" >
                            <span class="help-block"></span>
                          </div>
                          <div class="form-group">
                            <input id="signUpConfirmPassword" name="confirm_password" type="password" class="form-control signUpEmail" data-type="password" autocomplete="off" placeholder="confirm password" >
                            <span class="help-block"></span>
                          </div>
                          <button title="Send Otp" id="sendSignUpOtpBtn" class="btn btn-info btn-block hide signUpMobile" onclick="event.preventDefault(); sendSignUpOtp();" >Send OTP</button></br>
                          <div class="form-group hide" id="signUpOtpDiv">
                            <input name="user_otp" type="text" class="form-control" placeholder="Enter OTP" >
                            <span class="help-block"></span>
                          </div>
                          <button title="Register"  id="register" class="btn btn-info btn-block signUpEmail" onclick="event.preventDefault(); confirmSubmit();" >Register</button></br>
                        </form>
                        <div>
                          <a title="alredy member" data-toggle="tab" href="#home">Already member?</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
   $(function() {
    $('#signinRadioEmail').click();
    $('#signupRadioEmail').click();
    $('.multiselect').multiselect();
   });
  function confirmSubmit(){
    document.getElementById('register').setAttribute("disabled",true);
    document.getElementById('registerUser').submit();
  }
  function toggleSignUp(value){
    if('email' == value){
      $('.signUpEmail').prop('required', true);
      $('.signUpEmail').removeClass('hide');
      $('.signUpMobile').prop('required', false);
      $('.signUpMobile').addClass('hide');
      $('#signUpPhone').prop('required', false);
    } else {
      $('.signUpEmail').prop('required', false);
      $('.signUpEmail').addClass('hide');
      $('.signUpMobile').prop('required', true);
      $('.signUpMobile').removeClass('hide');
      $('#signUpPhone').prop('required', true);
    }
    emptySignUpForm();
  }
  function toggleSignIn(value){
    if('email' == value){
      $('.signInEmail').removeClass('hide');
      $('.signInMobile').addClass('hide');
      $('#sendSignInOtpBtn').addClass('hide');
      $('#signInPassword').prop('required', true);
      $('#signInEmail').prop('required', true);
      $('#signInPassword').val('');
      $('#signInEmail').val('');
      $('#signInPhone').val('');
    } else {
      $('.signInEmail').addClass('hide');
      $('.signInMobile').removeClass('hide');
      $('#sendSignInOtpBtn').removeClass('hide');
      $('#signInPassword').val('');
      $('#signInEmail').val('');
      $('#signInPassword').prop('required', false);
      $('#signInEmail').prop('required', false);
      $('#signInPhone').val('');
    }
  }
  function sendSignInOtp(){
    var mobile = $('#signInPhone').val();
    if(mobile){
      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        if('success' == result['status']){
          $('#signInOtpDiv').removeClass('hide');
          $('#loginBtn').removeClass('hide');
          $('#sendSignInOtpBtn').addClass('hide');
          $('#signInPhone').prop('readonly', true);
          $('#signInPassword').val('');
          $('#signInEmail').val('');
          $('#login_otp').prop('required', true);
        } else {
          alert(result['message']);
        }
      });
    } else {
      alert('enter mobile no.');
    }
  }
  function sendSignUpOtp(){
    var mobile = $('#signUpPhone').val();
    if(mobile){
    $('#signUpOtpDiv').removeClass('hide');
    $('#register').removeClass('hide');
    $('#sendSignUpOtpBtn').addClass('hide');
    $('#signUpPhone').prop('readonly', true);
    $('#name').prop('readonly', true);

      $.ajax({
        method: "POST",
        url: "{{url('sendClientUserSignUpOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( msg ) {
        // console.log(msg);
      });
    } else {
      alert('enter mobile no.');
    }
  }
  function emptySignUpForm(){
    $('#signUpNameInput').val('');
    $('#signUpPhone').val('');
    $('#signUpEmailInput').val('');
    $('#signUpPassword').val('');
    $('#signUpConfirmPassword').val('');
  }
</script>
<style type="text/css">
#vchip-header .form-wrap .tab-content label {
    color: rgba(25, 23, 23, 0.8);
}
</style>
</header>