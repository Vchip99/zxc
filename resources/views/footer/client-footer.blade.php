<footer id="contact">
  <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
          <h3> organizations</h3>
          <ul>
            @if(is_object($subdomain))
              <li class="" title="Main Site"><a href="{{ $subdomain->institute_url }}" target="_blank">Main Site</a></li>
            @endif
            <li title="Home"><a href="/"> Home</a></li>
            <li title="Courses"><a href="{{ url('online-courses') }}" >Courses</a></li>
            <li title="Test Series"><a href="{{ url('online-tests') }}" >Test Series</a></li>
            <li title="Admin Log in"><a href="{{ url('client/login') }}" >Admin Log in</a></li>
          </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
          <h3> Contact Us </h3>
          <h4>
            @if(is_object($subdomain))
              {!! $subdomain->contact_us !!}
            @endif
         </h4>

       </div>
     </div>
     <!--/.row-->
   </div>
 </div>
 <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
          <p class="pull-left " title="vchiptech.com"><a href="http://www.vchiptech.com/" class="site_link" target="_blank"> vchiptech.com </a></p>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 text-center social-contact" >
          <ul class="social-network social-circle ">
            @if(is_object($subdomain))
              <li><a href="{{ $subdomain->facebook_url }}" class="icoFacebook" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
              <li><a href="{{ $subdomain->twitter_url }}" class="icoTwitter" title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a></li>
              <li><a href="{{ $subdomain->google_url }}" class="icoGoogle" title="Google +" target="_blank"><i class="fa fa-google-plus"></i></a></li>
              <li><a href="{{ $subdomain->linkedin_url }}" class="icoLinkedin" title="Linkedin" target="_blank"><i class="fa fa-linkedin"></i></a></li>
            @endif
          </ul>
        </div>
        <div class="col-lg-4  col-md-4 col-sm-6 col-xs-12 ">
          @if(is_object($subdomain))
          <p class="pull-right" title="{{ $subdomain->institute_name }}"><a href="{{ $subdomain->institute_url }}" class="site_link" target="_blank">
              {{ $subdomain->institute_name }}
            </a></p>
          @endif
        </div>
      </div>
    </div>
  </div>
</footer>
<div id="loginUserModel" class="modal fade " role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"  style="border-bottom: none;">
        <button class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <div class="modal-data">
            <div class="form-group">
              <input id="email" name="email" type="email" class="form-control" placeholder="vchip@gmail.com" autocomplete="off" required>
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <input id="password" name="password" type="password" class="form-control" placeholder="password" data-type="password" autocomplete="off" required >
              <span class="help-block"></span>
            </div>
            <div id="loginErrorMsg" class="hide">Wrong username or password</div>
            <button type="submit" value="login" name="submit" class="btn btn-info btn-block" onClick="loginUser();">Login</button>
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
                  <a href="{{ url('/')}}" ">Sign Up</a>
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
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    if(email && password){
      $.ajax({
          method: "POST",
          url: "{{ url('clientUserLogin') }}",
          data: {email:email, password:password}
      })
      .done(function( msg ) {
        if('true' == msg){
          window.location.reload(true);
        } else {
          document.getElementById('loginErrorMsg').classList.remove('hide');
          if('Try after some time.' == msg){
            document.getElementById('loginErrorMsg').innerHTML = msg;
          } else {
            window.location.reload(true);
          }
        }
      });
    }
  }

  function changeType(ele){
    document.getElementById(ele).setAttribute('type', ele);
    document.getElementById('loginErrorMsg').classList.add('hide');
  }

  $(window).on('load', function(e){
    if (window.location.hash == '#_=_') {
      window.location.hash = ''; // for older browsers, leaves a # behind
      history.pushState('', document.title, window.location.pathname); // nice and clean
      e.preventDefault(); // no page reload
    }
  });
</script>