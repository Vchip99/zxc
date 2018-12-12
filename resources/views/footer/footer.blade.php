<div class="container-box rotated">
  @if(is_object(Auth::user()))
    @if('ceo@vchiptech.com' != Auth::user()->email)
      <button type="button" class="btn btn-info btn-lg turned-button" id="{{$chatAdminId}}" data-user_name="Admin Chat" onclick="showChat(this);">Get In Touch</button>
    @else
      <button type="button" class="btn btn-info btn-lg turned-button">Get In Touch</button>
    @endif
  @else
    <button type="button" class="btn btn-info btn-lg turned-button" data-toggle="modal" data-target="#loginUserModel">Get In Touch</button>
  @endif
</div>
<footer>
  @php
    if('local' == \Config::get('app.env')){
      $homeUrl = 'https://localvchip.com/';
      $onlineUrl = 'https://online.localvchip.com/';
    } else {
      $homeUrl = 'https://vchipedu.com/';
      $onlineUrl = 'https://online.vchipedu.com/';
    }
  @endphp
 <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Services</h3>
          <ul>
            <li class=""><a href="{{ url('erp') }}">Digital Edu & ERP</a></li>
            <li class=""><a href="{{ url('offlineworkshops') }}">Workshops</a></li>
            <li class=""><a href="{{ url('motivationalspeech') }}">Motivational Speech</a></li>
            <li class=""><a href="{{ url('virtualplacementdrive') }}">Virtual Placement Drive</a></li>
            <li class=""><a href="{{$onlineUrl}}" target="_blank">Digital-Edu Platform</a></li>
            <li class=""><a href="{{$onlineUrl}}digitalmarketing" target="_blank">Digital Marketing</a></li>
            <li class=""><a href="{{$onlineUrl}}webdevelopment" target="_blank">Web & App Development</a></li>
            <li class=""><a href="{{$onlineUrl}}pricing" target="_blank">Pricing</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2"> Digital Education</h3>
          <h3 class="hidden-1">Education</h3>
          <ul >
             <li><a href="{{ url('courses')}}">Online Courses</a></li>
             <li class=""></li>
             <li><a href="{{ url('online-tests') }}">Online Test Series</a></li>
             <li class=""></li>
             <li><a href="{{ url('workshops') }}">Workshops</a></li>
             <li class=""></li>
             <li><a href="{{ url('vkits') }}">Hobby Projects</a></li>
             <li class=""></li>
             <li><a href="{{ url('documents') }}">Documents</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2">Placement & Other</h3>
          <h3 class="hidden-1">Placement</h3>
          <ul>
            <li><a href="{{ url('placements')}}">Placement</a></li>
            <li><a href="{{ url('/showTest') }}/1">Placement Mock Test</a></li>
            <li><a href="{{url('discussion')}}">Discussion Forum</a></li>
            <li><a href="{{url('blog')}}">Blogs</a></li>
            <li><a href="{{url('ourpartner')}}">Our Partners</a></li>
            <li><a href="{{url('career')}}">Career</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> About Vchip </h3>
          <ul>
            <li><a href="{{url('us')}}" title="US">US</a></li>
            <li><a href="{{url('ourpartner')}}" title="Our partners">Our partners</a></li>
            <li><a href="{{url('career')}}" title="Career">Career</a></li>
            <li><a href="{{url('contactus')}}" title="Contact us">Contact us</a></li>
            <li><a href="{{url('terms-and-conditions')}}" title="Terms and Conditions">Terms and Conditions</a></li>
            <li><a href="{{url('privacy-policy')}}" title="Privacy Policy">Privacy Policy</a></li>
            <li><a href="{{url('faq')}}" title="FAQ">FAQ</a></li>
          </ul>
        </div>
      </div>
   </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
          <p class="pull-left" title="vchiptech.com"><a href="https://vchiptech.com/" class="site_link" target="_blank"> vchiptech.com </a></p>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 text-center social-contact" >
          <ul class="social-network social-circle ">
            <li><a href="https://www.facebook.com/vchip99/" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#" class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://plus.google.com/u/0/115493121296973872760" class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="https://www.linkedin.com/company/13213434/" class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
          </ul>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-12 ">
          <p class="pull-right" title="vchipedu.com"><a href="https://vchipedu.com/" class="site_link" target="_blank"> vchipedu.com </a></p>
        </div>
      </div>
    </div>
  </div>
  @include('footer.livechat')
</footer>
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
<script type="text/javascript">
  function loginUser(){
    var email = document.getElementById('useremail').value;
    var password = document.getElementById('userpassword').value;
    var signInModelPhone = document.getElementById('signInModelPhone').value;
    var loginOtp = document.getElementById('login_model_otp').value;
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

  $(window).on('load', function(e){
    if (window.location.hash == '#_=_') {
      window.location.hash = ''; // for older browsers, leaves a # behind
      history.pushState('', document.title, window.location.pathname); // nice and clean
      e.preventDefault(); // no page reload
    }
    if (window.location.hash == '#tab_4') {
      $('#menu > li').removeClass('active');
      $('#menu > li > a').attr('aria-expanded', false);
      $('#menu > li:last').addClass('active');
      $('#menu > li:last > a').attr('aria-expanded', true);
      $('.tab-content > div#tab_1').removeClass('active in');
      $('.tab-content > div#tab_4').addClass('active in');
    }
  });

  function toggleModelSignIn(value){
    if('email' == value){
      $('.signInModelEmail').removeClass('hide');
      $('.signInModelMobile').addClass('hide');
      $('#sendSignInModelOtpBtn').addClass('hide');
      $('#userpassword').prop('required', true);
      $('#useremail').prop('required', true);
      $('#userpassword').val('');
      $('#useremail').val('');
      $('#signInModelPhone').val('');
    } else {
      $('.signInModelEmail').addClass('hide');
      $('.signInModelMobile').removeClass('hide');
      $('#sendSignInModelOtpBtn').removeClass('hide');
      $('#userpassword').val('');
      $('#useremail').val('');
      $('#userpassword').prop('required', false);
      $('#useremail').prop('required', false);
      $('#signInModelPhone').val('');
    }
  }

  function sendSignInModelOtp(){
    var mobile = $('#signInModelPhone').val();
    if(mobile && 10 == mobile.length ){
    $('#signInModelPhone').prop('readonly', true);
      $.ajax({
        method: "POST",
        url: "{{url('sendVchipUserSignInOtp')}}",
        data: {mobile:mobile}
      })
      .done(function( result ) {
        var resultObj = JSON.parse(result);
        if('000' == resultObj.ErrorCode && 'Success' == resultObj.ErrorMessage){
          $('#signInModelOtpMessage').removeClass('hide');
          $('#signInModelOtpDiv').removeClass('hide');
          $('#loginModelBtn').removeClass('hide');
          $('#sendSignInModelOtpBtn').addClass('hide');
        } else {
          $('#sendSignInModelOtpBtn').removeClass('hide');
          $('#signInModelPhone').prop('readonly', false);
          $('#signInModelOtpMessage').addClass('hide');
          $('#signInModelOtpDiv').addClass('hide');
          $('#loginModelBtn').addClass('hide');
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

  function checkLogin(){
    $('#loginUserModel').modal();
    return false;
  }
</script>