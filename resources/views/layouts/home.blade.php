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
  @include('header.header_menu')
  @include('header.header_info')
<section class="v_bg_grey v_container" >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">ABOUT US</h2>
        <hr class="section-dash-dark"/>
      </div>
    </div>
    <ul class="nav nav-pills mrgn_30_top" >
      <li class="active"><a data-toggle="tab" href="#about1" title="About">About</a></li>
      <li><a data-toggle="tab" href="#vission" title="Vision">Vision</a></li>
      <li><a data-toggle="tab" href="#mission" title="Mission">Mission</a></li>
    </ul>

    <div class="tab-content" style="background-color: #01bafd; padding : 6px 15px;">
      <div id="about1" class="tab-pane fade in active">
        <h3 class="v_h3_title">About</h3>
        <p>Education is need of better society and in our country(India) most of people live in villages . So V-edu is working on Digital Education platform, so that we can provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always belives that a better society is a best place to live and educated society is better than best. So at initial stage will we provide our services, V-edu platform at basic pay and after 2020 we will open V-edu platform completely free of cost for society.</p>
      </div>
      <div id="vission" class="tab-pane fade">
        <h3 class="v_h3_title">Vission</h3>
        <p>To be at leading and respectable place in the knowledge led creativity movement. World see toward our country as in leading industry of Electronics and IT sector.</p>
      </div>
      <div id="mission" class="tab-pane fade">
        <h3 class="v_h3_title">Mission</h3>
        <p>Our dream is to identify our country as digital villages and we are working hard to make our dream into reality. In our country, most of population live in villages, so its the first step toward digital India. In digital villages, we will fulfill all the basic needs like education, health care and all detail about agriculture by digitally with quality. We think that educated society is best one and our dream is to make villages as a better place to live, so we are working on an online education platform namely V-edu. So quality education will reach to villages and remote areas along with the urban area. We will make the V-edu as open source, so that any one interested can start their career in the education field.</p>
      </div>
    </div>
  </div><br />
</section>
<section id="vchip_solution" class="v_container" >
 <div class="container ">
  <div class="row mrgn_60_btm">
    <div class="col-md-8 col-md-offset-2 text-center ">
      <h2 class="v_h2_title">OUR SOLUTIONS</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">Learn with fun...</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 mrgn_60_btm text-center slideanim">
      <div class="v_solution_containt">
        <div class="icon ">
          <span data-toggle="tooltip" title="V-Education"><i class="fa fa-graduation-cap"></i></span>
        </div>
        <h3 class="v_h3_title">V-Education</h3>
        <p class="more v_p_text mrgn_10_left_rgt">
          End to end IoT base both hardware and software solution to institutes, colleges and educational organizations.
        </p>
      </div>
    </div>
    <div class="col-md-3  mrgn_60_btm text-center  ">
      <div class="v_solution_containt">
        <div class="icon mrgn_30_top_btm">
          <span data-toggle="tooltip" title="V-Connect"><i class="fa fa-mobile"></i></span>
        </div>
        <h3 class="v_h3_title">V-Connect</h3>
        <p class="more v_p_text mrgn_10_left_rgt">
          V-connect connects the mobile phone, pad and laptop. Using V-connect lecturer  can monitor student activity, share screen with individual or in group, lock individual or grouped devices, send new activities or assignment and receive alerts messages from devices.
        </p>
      </div>
    </div>
    <div class="col-md-3 mrgn_60_btm text-center slideanim">
      <div class="v_solution_containt">
        <div class="icon">
          <span data-toggle="tooltip" title="V-Pendrive"><i class="fa fa-eraser"></i></span>
        </div>
        <h3 class="v_h3_title">V-Pendrive</h3>
        <p class="more v_p_text mrgn_10_left_rgt">
          Students can access our on-line courses, test series and other facilities remotely even without Internet connectivity.
        </p>
      </div>
    </div>
    <div class="col-md-3 mrgn_60_btm text-center  ">
      <div class="v_solution_containt ">
        <div class="icon">
          <span data-toggle="tooltip" title="V-Cloud"><i class="fa fa-cloud"></i></span>
        </div>
        <h3 class="v_h3_title">V-Cloud</h3>
        <p class="more v_p_text mrgn_10_left_rgt ">
          V-Cloud use for storing all the information about students. Here we store students assignment, their work, response and all about our class.
        </p>
      </div>
    </div>
  </div>
</div>
</section>
<section class="v_bg_grey">
<div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
        <h2 class="v_h2_title">OUR PRODUCT</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title">Digital village is our dream...</h3>
      </div>
  </div>
  <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
        <div class="vchip_product_itm text-left">
            <figure data-toggle="tooltip" title="Online Courses">
              <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
            </figure>
            <ul class="vchip_categories list-inline">
            <li>Online Courses</li>
          </ul>
          <div class="vchip_product_content">
            <p>We provide online courses... </p>
            <p class="mrgn_20_top"><a href="{{ url('courses')}}" class="btn-link">Learn More <i
              class="fa fa-angle-right"
              aria-hidden="true"></i></a>
            </p>
          </div>
        </div>
       </div>
       <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="vchip_product_itm">
            <figure data-toggle="tooltip" title="Live Courses">
              <img src="{{asset('images/index/live-course.jpg')}}" alt="live course" class="img-responsive">
            </figure>
            <ul class="vchip_categories list-inline">
          <li>Live Courses</li>
        </ul>
        <div class="vchip_product_content">
          <p>We provide Livecourses... </p>
          <p class="mrgn_20_top"><a href="{{ url('liveCourse')}}" class="btn-link">Learn More <i
            class="fa fa-angle-right"
            aria-hidden="true"></i></a>
          </p>
        </div>
        </div>
       </div>
       <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
        <div class="vchip_product_itm">
            <figure data-toggle="tooltip" title="Webinar">
              <img src="{{asset('images/index/webinar.jpg')}}" alt="webinar" class="img-responsive">
            </figure>
             <ul class="vchip_categories list-inline">
        <li>Webinar</li>
      </ul>
      <div class="vchip_product_content">
        <p>We provide Webine... </p>
        <p class="mrgn_20_top"><a href="{{ url('webinar')}}" class="btn-link">Learn More <i
          class="fa fa-angle-right"
          aria-hidden="true"></i></a>
        </p>
      </div>
        </div>
       </div>
       <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="vchip_product_itm">
            <figure data-toggle="tooltip" title="Online Test Series">
              <img src="{{asset('images/index/online-test-series.jpg')}}" alt="online test series" class="img-responsive">
            </figure>
          <ul class="vchip_categories list-inline">
        <li>Online Test Series</li>
      </ul>
      <div class="vchip_product_content">
        <p>We provide Online Test Series... </p>
        <p class="mrgn_20_top"><a href="{{ url('online-tests') }}" class="btn-link">Learn More <i
          class="fa fa-angle-right"
          aria-hidden="true"></i></a>
        </p>
      </div>
        </div>
       </div>
        <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
        <div class="vchip_product_itm">
            <figure data-toggle="tooltip" title="V-kit">
              <img src="{{asset('images/index/v-kit.jpg')}}" alt="v-kit" class="img-responsive">
            </figure>
          <ul class="vchip_categories list-inline">
      <li>V-kit</li>
    </ul>
     <div class="vchip_product_content">
      <p>We provide hobby projects </p>
      <p class="mrgn_20_top"><a href="{{ url('vkits') }}" class="btn-link">Learn More <i
        class="fa fa-angle-right"
        aria-hidden="true"></i></a>
      </p>
    </div>
        </div>
       </div>
       <div class="col-lg-4 col-md-4 col-sm-6">
        <div class="vchip_product_itm">
            <figure data-toggle="tooltip" title="V-Doc">
              <img src="{{asset('images/index/v-doc.jpg')}}" alt="documents" class="img-responsive">
            </figure>
         <ul class="vchip_categories list-inline">
      <li>V-Doc</li>
    </ul>

    <div class="vchip_product_content">
      <p>We provide research paper... </p>
      <p class="mrgn_20_top"><a href="{{ url('documents') }}" class="btn-link">Learn More <i
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
      <img class="" src="{{ asset('images/logo/ghrcema_logo.png')}}" alt="G H Risoni"/>
      <div class="overlay">
       <h2>G H Risoni</h2>
       <a class="info" href="http://ghrcema.raisoni.net/" target="_blank">link here</a>
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
      <h3 class="v_h3_title">Start-up to stand-up...</h3>
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
        <img src="{{asset('images/index/v-tech.jpg')}}" alt="technical event" style="width: 100px;"  />
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
      <h3 class="v_h3_title">The place where we deffer...</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/vishesh.jpg')}}" alt="member-1">
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
        <p>Adjunct Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/ajay.jpg')}}" alt="member-2">
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/vishal_kumar.jpg')}}" alt="member-3">
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vishal Kumar</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Vishal Parvani</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: Bits Pilani </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/vishal_parvani.jpg')}}" alt="member-5">
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{asset('images/team/vartul.jpg')}}" alt="member-6">
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vartul Sharma</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: COEP Pune </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Aditya Jagtap</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: VIT Vellore </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{asset('images/team/aditya.jpg')}}" alt="member-5">
      </div>
    </div>
  </div>
</div>
</section>
@stop
@section('footer')
  @include('footer.footer')
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

$(document).ready(function(){
        setTimeout(function() {
          $('.alert-success').fadeOut('fast');
        }, 2000); // <-- time in milliseconds
    });
</script>
@stop