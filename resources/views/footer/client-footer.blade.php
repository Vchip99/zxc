<footer id="contact">
  <div class="footer" >
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
          <h3> organizations</h3>
          <ul>
            <li class="" title="Main Site"><a href="{{ $subdomain->institute_url }}" target="_blank">Main Site</a></li>
            <li title="Home"><a href="/"> Home</a></li>
            @if(1 == $client->course_permission)
              <li title="Courses"><a href="{{ url('online-courses') }}" >Courses</a></li>
            @endif
            @if(1 == $client->test_permission)
              <li title="Test Series"><a href="{{ url('online-tests') }}" >Test Series</a></li>
            @endif
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