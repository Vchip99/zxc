 <footer>
  <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> organizations</h3>
          <ul>
            <li title="V-Education"><a href="{{ url('vEducation')}}" > V-Education</a></li>
            <li title="V-Connect"><a href="{{ url('vConnect')}}" >V-Connect</a></li>
            <li title="V-Pendrive"><a href="{{ url('vPendrive')}}" >V-Pendrive</a></li>
            <li title="V-Cloud"><a href="{{ url('vCloud')}}" >V-Cloud</a></li>
            <li title="Admin Dashbord"><a href="{{ url('admin/home') }}" >Admin Dashbord</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> STUDENT</h3>
          <ul>
            <li title="Course"> <a href="{{ url('courses')}}"> Course</a> </li>
            <li title="Live Course"> <a href="{{ url('liveCourse') }}" >Live Course</a> </li>
            <!-- <li> <a href="{{ url('webinar') }}" > Webinar</a> </li> -->
            <li title="V-Doc"> <a href="{{ url('documents') }}" > V-Doc </a> </li>
            <li title="Projects"> <a href="{{ url('vkits') }}" >Projects</a> </li>
            <li title="Student Dashbord"><a href="{{ url('dashboard') }}" >Student Dashbord</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Subscribe </h3>
          <ul>
            <li>Get latest update, news</li>
            <form class="v_subscribe_form" action="{{url('subscribedUser')}}" method="POST">
              {{csrf_field()}}
              <input type="email" name="email" placeholder="Enter your email" required="true"><br/><br/>
              <button class="btn-primary" type="submit">Subscribe!</button>
            </form>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Contact Us </h3>
          <address>
           <p>VCHIP TECHNOLOGY PVT LTD</p>
           <p>Address: GITANJALI COLONY, NEAR RAJYOG SOCIETY, </p>
           <p> WARJE, PUNE-411058, INDIA.</p>
           <p>Email: info@vchiptech.com</p>
         </address>
       </div>
     </div>
     <!--/.row-->
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
