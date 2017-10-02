 <footer>
 <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Services</h3>
          <ul>
            <li class=""><a href="{{ url('erp') }}">Digital edu & ERP</a></li>
            <li class=""><a href="{{ url('educationalPlatform') }}">Education Platform</a></li>
            <li class=""><a href="{{ url('digitalMarketing') }}">Digital Marketing</a></li>
            <li class=""><a href="{{ url('pricing') }}">pricing</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2"> Digital Education</h3>
          <h3 class="hidden-1">Education</h3>
          <ul >
             <li><a href="{{ url('courses')}}">Online Courses</a></li>
             <li><a href="{{ url('liveCourse') }}">Live course</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('online-tests') }}">Online Test Series</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('workshops') }}">Workshop</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('vkits') }}">Hoby Project</a></li>
             <li class="divider"></li>
             <li><a href="{{ url('documents') }}">Documents</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2">Placement & Other</h3>
          <h3 class="hidden-1">Placement</h3>
          <ul>
            <li><a href="{{ url('placements')}}">Placement</a></li>
            <li><a href="{{ url('/showTest') }}/1">Placement mock test</a></li>
            <li><a href="{{url('discussion')}}">Discussion forum</a></li>
            <li><a href="{{url('blog')}}">Blog</a></li>
            <li><a href="{{url('ourpartner')}}">Our partners</a></li>
            <li><a href="{{url('career')}}">Career</a></li>
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
                 <button class=" btn-info btn-sm" type="submit">Subscribe!</button>
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
        <p class="pull-left" title="vchiptech.com"><a href="http://www.vchiptech.com/" class="site_link" target="_blank"> vchiptech.com </a></p>
      </div>
      <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 text-center social-contact" >
        <ul class="social-network social-circle ">
          <li><a href="#" class="icoFacebook" title="Facebook"><i class="fa fa-facebook"></i></a></li>
          <li><a href="#" class="icoTwitter" title="Twitter"><i class="fa fa-twitter"></i></a></li>
          <li><a href="#" class="icoGoogle" title="Google +"><i class="fa fa-google-plus"></i></a></li>
          <li><a href="#" class="icoLinkedin" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
        </ul>
      </div>
      <div class="col-lg-4  col-md-4 col-sm-6 col-xs-12 ">
        <p class="pull-right" title="vchipedu.com"><a href="https://vchipedu.com/" class="site_link" target="_blank"> vchipedu.com </a></p>
      </div>
    </div>
  </div>
</div>
</footer>
