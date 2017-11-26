<footer>
 <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Services</h3>
          <ul>
            <li class=""><a href="{{ url('erp') }}">Digital Edu & ERP</a></li>
            <li class=""><a href="{{ url('educationalPlatform') }}">Education Platform</a></li>
            <li class=""><a href="{{ url('digitalMarketing') }}">Digital Marketing</a></li>
            <li class=""><a href="{{ url('pricing') }}">Pricing</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2"> Digital Education</h3>
          <h3 class="hidden-1">Education</h3>
          <ul >
             <li><a href="{{ url('courses')}}">Online Courses</a></li>
             <li><a href="{{ url('liveCourse') }}">Live Course</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('online-tests') }}">Online Test Series</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('workshops') }}">Workshops</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('vkits') }}">Hobby Projects</a></li>
             <li class="divider"></li>
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
            <li><a href="{{url('blog')}}">Blog</a></li>
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
</script>