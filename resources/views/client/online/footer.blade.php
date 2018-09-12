<footer>
  @php
    if('local' == \Config::get('app.env')){
      $homeUrl = 'https://localvchip.com/';
    } else {
      $homeUrl = 'https://vchipedu.com/';
    }
  @endphp
 <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Services</h3>
          <ul>
            <li class=""><a href="{{$homeUrl}}erp" target="_blank">Digital Edu & ERP</a></li>
            <li class=""><a href="{{$homeUrl}}offlineworkshops" target="_blank">Workshops</a></li>
            <li class=""><a href="{{$homeUrl}}motivationalspeech" target="_blank">Motivational Speech</a></li>
            <li class=""><a href="{{$homeUrl}}virtualplacementdrive" target="_blank">Virtual Placement Drive</a></li>

            <li class=""><a href="{{ url('/') }}">Digital-Edu </a></li>
            <li class=""><a href="{{ url('digitalmarketing') }}">Digital Marketing</a></li>
            <li class=""><a href="{{ url('webdevelopment') }}">Web & App </a></li>
            <li class=""><a href="{{ url('pricing') }}">Pricing</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2"> Digital Education</h3>
          <h3 class="hidden-1">Education</h3>
          <ul >
             <li><a href="{{$homeUrl}}courses" target="_blank">Online Courses</a></li>
             <!-- <li><a href="{{$homeUrl}}liveCourse">Live Course</a></li> -->
             <li class=""></li>
             <li><a href="{{$homeUrl}}online-tests" target="_blank">Online Test Series</a></li>
             <li class=""></li>
             <li><a href="{{$homeUrl}}workshops" target="_blank">Workshops</a></li>
             <li class=""></li>
             <li><a href="{{$homeUrl}}vkits" target="_blank">Hobby Projects</a></li>
             <li class=""></li>
             <li><a href="{{$homeUrl}}documents" target="_blank">Documents</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3 class="hidden-2">Placement & Other</h3>
          <h3 class="hidden-1">Placement</h3>
          <ul>
            <li><a href="{{$homeUrl}}placements" target="_blank">Placement</a></li>
            <li><a href="{{$homeUrl}}showTest/1" target="_blank">Placement Mock Test</a></li>
            <li><a href="{{$homeUrl}}discussion" target="_blank">Discussion Forum</a></li>
            <li><a href="{{$homeUrl}}blog" target="_blank">Blogs</a></li>
            <li><a href="{{$homeUrl}}ourpartner" target="_blank">Our Partners</a></li>
            <li><a href="{{$homeUrl}}career" target="_blank">Career</a></li>
            <li><a href="{{$homeUrl}}admin/login" target="_blank">Admin Dashboard</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
          <h3> Contact Us </h3>
          <address>
           <p>VCHIP TECHNOLOGY PVT LTD</p>
           <p>Address: 3rd Floor,Sr No 132/2A/3</p>
           <p>Shrinivas,Labhade Park,Near BSNL</p>
           <p>Office, WARJE, PUNE-411058, INDIA.</p>
           <p>Phone: 020-25235596</p>
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