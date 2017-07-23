@extends('layouts.master')
@section('header-title')
  <title>V-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/index.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/hover.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/animate.min.css?ver=1.0')}}" rel="stylesheet"/>

@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('subdomain.header_menu')
  @include('subdomain.login_form')
<section class="v_bg_grey v_container" >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">{!! $subdomain->home_about_value !!}</h2>
        <hr class="section-dash-dark"/>
      </div>
    </div>
    <ul class="nav nav-pills mrgn_30_top" >
      <li class="active"><a data-toggle="tab" href="#about1">About</a></li>
      <li><a data-toggle="tab" href="#vission">Vission</a></li>
      <li><a data-toggle="tab" href="#mission">Mission</a></li>
    </ul>

    <div class="tab-content" style="background-color: #01bafd; padding : 6px 15px;">
      <div id="about1" class="tab-pane fade in active">
        <h3 class="v_h3_title">About</h3>
        <p>{!! $subdomain->home_about_content !!}</p>
      </div>
      <div id="vission" class="tab-pane fade">
        <h3 class="v_h3_title">Vission</h3>
        <p>{!! $subdomain->home_vission_content !!}</p>
      </div>
      <div id="mission" class="tab-pane fade">
        <h3 class="v_h3_title">Mission</h3>
        <p>{!! $subdomain->home_mission_content !!}</p>
      </div>
    </div>
  </div><br />
</section>
<section id="courses">
  <div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title editable" id="editable_online_name" name="editable_online_name">{{ $subdomain->home_online_name }}</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title editable" id="editable_online_desc" name="editable_online_desc">{{ $subdomain->home_online_content }}</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
      <div class="vchip_product_itm text-left">
        <figure>
          <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
        </figure>
        <ul class="vchip_categories list-inline">
          <li>Online Courses</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide online courses... </p>
          <p class="mrgn_20_top"><a href="" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
      <div class="vchip_product_itm">
        <figure>
          <img src="{{asset('images/index/live-course.jpg')}}" alt="live course" class="img-responsive">
        </figure>
        <ul class="vchip_categories list-inline">
          <li>Live courses</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide Livecourses... </p>
          <p class="mrgn_20_top"><a href="" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
      </div>
    </div>
    <!-- <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
      <div class="vchip_product_itm">
        <figure>
          <img src="{{asset('images/index/webinar.jpg')}}" alt="webinar" class="img-responsive">
        </figure>
        <ul class="vchip_categories list-inline">
          <li>Webinar</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide Webine... </p>
          <p class="mrgn_20_top"><a href="webinar.html" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
      </div>
    </div> -->
  </div>
</div>
</section>
</section>
<section class="v_bg_grey" id="test">
  <div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title editable" id="editable_test_series" name="editable_test_series">{{ $subdomain->home_test_value }}</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title editable" id="editable_test_desc" name="editable_test_desc">{{ $subdomain->home_test_content }}</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
      <div class="vchip_product_itm text-left">
        <figure>
          <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
        </figure>
        <ul class="vchip_categories list-inline">
          <li>Test1</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide online test series... </p>
          <p class="mrgn_20_top"><a href="" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6">
      <div class="vchip_product_itm">
        <figure>
          <img src="{{asset('images/index/live-course.jpg')}}" alt="live course" class="img-responsive">
        </figure>
        <ul class="vchip_categories list-inline">
          <li>Test2</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide online test series... </p>
          <p class="mrgn_20_top"><a href="" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
</section>

<section class="v_container">
  <div class="container" >
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">OUR CUSTOMERS</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title">Happy customers...Successful adventure.</h3>
      </div>
      <div class="row our_customer">
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
          <div class="hovereffect">
            <img class="" src="{{ asset('images/logo/ssgmce-logo.jpg')}}" alt="SSGMCE" />
            <div class="overlay">
             <h2>SSGMCE</h2>
             <a class="info" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">link here</a>
           </div>
         </div>
       </div>
       <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim mrgn_10_top">
        <div class="hovereffect">
          <img class="" src="{{ asset('images/logo/gate-the-Direction.png')}}" alt="GATE THE Direction" style="padding: 20px 0px;">
          <div class="overlay">
           <h2>GATE THE Direction</h2>
           <a class="info" href="http://gatethedirection.com/" target="_blank">link here</a>
         </div>
       </div>
     </div>
     <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim mrgn_10_top">
      <div class="hovereffect">
        <img class="" src="{{ asset('images/logo/kaizen.jpg')}}" alt="Kaizen Technology"/>
        <div class="overlay">
         <h2>Kaizen Technology</h2>
         <a class="info" href="http://kaizenn.org/" target="_blank">link here</a>
       </div>
     </div>
   </div>
   <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim mrgn_10_top">
    <div class="hovereffect">
      <img class="" src="{{ asset('images/logo/lasthour-logo.jpg')}}" alt="Last Hours Technology"/>
      <div class="overlay">
       <h2>Last Hours Technology</h2>
       <a class="info" href="http://lasthourstech.com/" target="_blank">link here</a>
     </div>
   </div>
 </div>

</div>
</div>
</div>
</section>

<section id="counter1" class="blog-wrapper" style="background: #eee" >
 <div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2 text-center ">
      <h2 class="v_h2_title">EVENTS</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">New ideas...Successful adventure.</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-4 animate-box slideanim" >
      <div class="feature-center" style="text-align: center">
        <span class="icon">
          <i class="ti-download"></i>
        </span>
        <span class="counter">
         <h2>ELECTROfreaks</h2>
         <img src="{{asset('images/index/electro.jpg')}}" alt="technical event" style="width: 100px;">
         <h6 class="blog-date">Technical Event </h6>
         <p>Technical event conducted by Vchip design in Amravati (India) in the month of February of every years.</p>
       </span>
     </div>
   </div>
   <div class="col-md-4 col-sm-4 animate-box" >
    <div class="feature-center" style="text-align: center">
      <span class="icon">
        <i class="ti-face-smile"></i>
      </span>
      <span class="counter">
        <h2>V-Tech</h2>
        <img src="{{asset('images/index/v-tech.JPG')}}" alt="technical event" style="width: 100px;"  />
        <h6 class="blog-date">Technical Event</h6>
        <p>Technical event conducted by Vchip design in Pune (India) in the month of August of every years.</p>
      </span>
    </div>
  </div>
  <div class="col-md-4 col-sm-4 animate-box slideanim" >
    <div class="feature-center" style="text-align: center">
      <span class="icon">
        <i class="ti-face-smile"></i>
      </span>
      <span class="counter">
        <h2>V-Debu</h2>
        <img src="{{asset('images/index/award.jpg')}}" alt="paper Presentation" style="width: 100px;"  />
        <h6 class="blog-date">Paper Presentation</h6>
        <p>Its the competition for debut in new technology. Winner get internship, funding and work space from Vchip design.</p>
      </span>
    </div>
  </div>
</div>
</div>
</section>
<section id="team" class="team blog-wrapper" >
 <div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2 text-center ">
      <h2 class="v_h2_title">OUR TEAM</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">Motive of bridging between educational organization  and Industry.</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/team-member-1.jpg')}}" alt="member-1">
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vishesh Agrawal</strong></p>
        <p>CEO</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail ">
        <div class="arrow-top"></div>
        <p><strong>Mr. Ajay Jangra</strong></p>
        <p>Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/team-member-2.jpg')}}" alt="member-2">
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/team-member-3.jpg')}}" alt="member-3">
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vishal Kumar</strong></p>
        <p>Adjoint Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Vishal Parvani</strong></p>
        <p>Adjoint Professor</p>
        <p>M-Tech: Bits Pilani </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/team-member-4.jpg')}}" alt="member-5">
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/team-member-5.jpg')}}" alt="member-6">
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vartul Sharma</strong></p>
        <p>Adjoint Professor</p>
        <p>M-Tech: COAP Pune </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Aditya Jagtap</strong></p>
        <p>Adjoint Professor</p>
        <p>M-Tech: VIT Vellore </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/team-member-6.jpg')}}" alt="member-5">
      </div>
    </div>
  </div>
</div>
</section>
@stop
@section('footer')
  @include('footer.client-footer')
  <script type="text/javascript">
  $(document).ready(function() {
    var showChar = 100;
    var ellipsestext = "...";
    var moretext = "<br /> Read More";
    var lesstext = "<br /> less";
    $('.more').each(function() {
      var content1 = $(this).html();

      if(content1.length > showChar) {

        var c = content1.substr(0, showChar);
        var h = content1.substr(showChar, content1.length - showChar);

        var html = c + '<span class="moreelipses">'+ellipsestext+'</span><span class="morecontent"><span>' + h + '</span><a href="" class="morelink" style="color:#01bafd;">'+moretext+'</a></span>';

        $(this).html(html);
      }

    });

    $(".morelink").click(function(){
      if($(this).hasClass("less")) {
        $(this).removeClass("less");
        $(this).html(moretext);
      } else {
        $(this).addClass("less");
        $(this).html(lesstext);
      }
    $(this).parent().prev().toggle();    //toggle the containt of ellipsestext ie. ...
    $(this).prev().toggle();            //toggle the containt of h
    return false;
  });
  });
</script>

<script>
$("#teacher-radiobtn").click(function(){
$(".show_hide").show();
});

$("#student-radiobtn").click(function(){
$(".show_hide").hide();
});
</script>
@stop