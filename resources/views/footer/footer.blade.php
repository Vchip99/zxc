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
             <!-- <li><a href="{{ url('liveCourse') }}">Live Course</a></li> -->
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
            <li><a href="{{url('admin/login')}}">Admin Dashboard</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Contact Us </h3>
          <address>
           <p>VCHIP TECHNOLOGY PVT LTD</p>
           <p>Address: GITANJALI COLONY, NEAR RAJYOG SOCIETY, </p>
           <p> WARJE, PUNE-411058, INDIA.</p>
           <p>Email: info@vchiptech.com</p>
           <form action="{{url('subscribedUser')}}" method="POST">
            {{csrf_field()}}
              <div class="v_subscribe_form input-group">
                 <input class="btn btn-sm" name="email" id="email" type="email" placeholder="Email" required>
                 <button class=" btn-info btn-sm" type="submit">Subscribe</button>
              </div>
           </form>
         </address>
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
            <div class="form-group">
              <input id="useremail" name="email" type="text" class="form-control" placeholder="vchip@gmail.com" onfocus="changeType('email');" autocomplete="off" required>
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <input id="password" name="password" type="text" class="form-control" placeholder="password" data-type="password" onfocus="changeType('password');" autocomplete="off" required >
              <span class="help-block"></span>
            </div>
            <div id="loginErrorMsg" class="hide">Wrong username or password</div>
            <button type="submit" value="login" name="submit" class="btn btn-info btn-block" onClick="loginUser();">Login</button>
            <br />
            <div class="form-group">
              <a href="{{ url('/auth/facebook') }}" class="btn btn-facebook btn-info" style="width: 209px; background-color: #3B5998; border-color: #3B5998;"><i class="fa fa-facebook"></i> Login </a>
            </div>
            <div class="form-group">
              <a href="{{ url('/auth/google') }}" class="btn btn-google btn-info" style="width: 209px; background-color: #DD4B39; border-color: #DD4B39;"><i class="fa fa-google"></i> Login </a>
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
    var password = document.getElementById('password').value;
    if(email && password){
      $.ajax({
          method: "POST",
          url: "{{ url('userLogin') }}",
          data: {email:email, password:password}
      })
      .done(function( msg ) {
        if('true' == msg){
          window.location.reload(true);
        } else {
          document.getElementById('loginErrorMsg').classList.remove('hide');
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
    if (window.location.hash == '#tab_4') {
      $('#menu > li').removeClass('active');
      $('#menu > li > a').attr('aria-expanded', false);
      $('#menu > li:last').addClass('active');
      $('#menu > li:last > a').attr('aria-expanded', true);
      $('.tab-content > div#tab_1').removeClass('active in');
      $('.tab-content > div#tab_4').addClass('active in');
    }
  });
</script>