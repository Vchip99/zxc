@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/index.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/hover.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/animate.min.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">

    .header-color {
      background-color: #00c0ef !important;
    }
    .model-title{
      color: #000;
    }
    .model-input{
      color: #000;
      border-color: #000;
      background-color: #fff;
    }

    #img-carousel .carousel-inner .item img{
      width: 100%
    }
    #img-carousel .carousel-indicators{
      margin-bottom: -40px;
    }
    #img-carousel .carousel-indicators>li{
      border:2px solid  #e84a64;
    }
    @media screen and (max-width: 800px) {

    #img-carousel .carousel-indicators{
    margin-bottom: -30px;    }}

    }
    .news .news-item img {
      width: 100px;
      height: 100px;
    }
    .news .news-item {
      padding-left: 115px;
      position: relative;
      margin-bottom: 20px;
    }
    .news .thumb {
      position: absolute;
      left: 0;
      top: 0;
    }
    .news .thumb {
      width: 100px;
      height: 100px;
    }
    @media screen and (max-width:320px) {
    .news .thumb {
      margin-top: 70px;
    }
    .news p{
      margin: 0 0 0px;
      padding: 0px 0px;
      text-align: justify;
    }
    }
    .carousel-controls a .fa, .carousel-controls .btn-vertical-slider .fa  {
      -webkit-transition: all 0.4s ease-in-out;
      -moz-transition: all 0.4s ease-in-out;
      -ms-transition: all 0.4s ease-in-out;
      -o-transition: all 0.4s ease-in-out;
      background: #dddddd;
      color: #6091ba;
      display: inline-block;
      width: 20px;
      height: 20px;
      text-align: center;
      margin-right: 0;
      font-size: 15px;
      outline: 0;}
      .carousel-controls a:focus {outline:0 !important;}
      .carousel-controls a.next .fa {
      padding-top: 2px;
      padding-right: 1px;
    }
    .carousel-controls div {
      padding-top: 2px;

    }
    .carousel-controls .btn-vertical-slider:hover .fa{
      background: #6091ba; color: #fff;
    }
    .carousel-controls .btn-vertical-slider i:hover{
      color: #fff;
    }
    .carousel-controls a.prev .fa {
      padding-top: 2px;
      padding-right: 1px;
    }
    .carousel-controls a:hover .fa {
      background: #6091ba;
      color: #fff;
    }
    /**/
    #myCarousel{
      height: 300px;
    }
    #myCarousel .btn-vertical-slider{
    /*     margin-left:35px;
    */     cursor:pointer;
    }

    a {  cursor:pointer;}
    .carousel.vertical .carousel-inner .item {
      -webkit-transition: 0.6s ease-in-out top;
      -moz-transition: 0.6s ease-in-out top;
      -ms-transition: 0.6s ease-in-out top;
      -o-transition: 0.6s ease-in-out top;
      transition: 0.6s ease-in-out top;
    }
    .carousel .vertical .active {
      top: 0;
    }
    .carousel.vertical .next {
      top: 100%;
    }
    .carousel.vertical .prev {
      top: -100%;
    }
    .carousel.vertical .next.left,
    .carousel.vertical .prev.right {
      top: 0;
    }
    .carousel.vertical .active.left {
      top: -100%;
    }
    .carousel.vertical .active.right {
      top: 100%;
    }
    .carousel.vertical .item {
      left: 0;
    }
    .gate-news {
      position: relative;
    }
    .gate-news .carousel-controls {
      right: 10px;
      top: 10px;
    }
    .gate-news .fa {
      color: #6091ba;
      margin-right: 5px;
      font-size: 18px;
    }
    .date-label {
      background: #f5f5f5;
      display: inline-block;
      width: 60px;
      height: 50px;
      text-align: center;
      font-size: 13px;
    }
    .date-label .month {
      background: #6091ba;
      color: #fff;
      display: block;
      font-size: 13px;
      text-transform: uppercase;
    }
    .date-label .date-number {
      clear: left;
      display: block;
      padding-top: 5px;
      font-size: 15px;
      font-family: 'open sans', arial, sans-serif;
      font-weight: 500;
    }
    /*t*/
    #testimonials-carousel.carousel {
      padding-bottom: 60px;
    }
    #testimonials-carousel.carousel .carousel-inner .item {
    opacity: 0;
    -webkit-transition-property: opacity;
      -ms-transition-property: opacity;
          transition-property: opacity;
    }
    #testimonials-carousel.carousel .carousel-inner .active {
    opacity: 1;
    -webkit-transition-property: opacity;
      -ms-transition-property: opacity;
          transition-property: opacity;
    }
    #testimonials-carousel.carousel .carousel-indicators {
      bottom: 10px;
    }
    #testimonials-carousel.carousel .carousel-indicators > li {
      background-color: #e84a64;
      border: none;
    }
    #testimonials-carousel blockquote {
      text-align: center;
      border: none;
    }
    #testimonials-carousel .profile-circle img{
      width: 100px !important;
      height: 100px  !important;
      margin: 0 auto;
      border-radius: 50%;
      border: 2px solid #fff;
    }
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('client.front.header_menu')
  @include('client.front.login_form')
  @if(1 == $subdomain->about_show_hide)
<section class="v_bg_grey v_container" >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">{!! $subdomain->home_about_value !!}</h2>
        <hr class="section-dash-dark"/>
      </div>
    </div>
    <ul class="nav nav-pills mrgn_30_top" >
      <li class="active" title="About"><a data-toggle="tab" href="#about1">About</a></li>
      <li title="Vission"><a data-toggle="tab" href="#vission">Vission</a></li>
      <li title="Mission"><a data-toggle="tab" href="#mission">Mission</a></li>
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
  @endif
   @if(1 == $subdomain->course_show_hide)
<section id="courses">
  <div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title editable" id="editable_online_name" name="editable_online_name">{{ $subdomain->home_course_name }}</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title editable" id="editable_online_desc" name="editable_online_desc">{{ $subdomain->home_course_content }}</h3>
    </div>
  </div>
  <div class="row">
    @if(is_object($defaultCourse))
      <div class="col-lg-4 col-md-4 col-sm-6">
          <div class="vchip_product_itm text-left">
            <figure>
              <img src="{{asset($defaultCourse->image_path)}}" alt="onlne course" class="img-responsive"  title="{{ $defaultCourse->name }}">
            </figure>
            <ul class="vchip_categories list-inline">
              <li>{{ $defaultCourse->name }}</li>
            </ul>
            <div class="vchip_product_content">
              <p>We provide online courses... </p>
              <p class="mrgn_20_top"><a href="{{ url('courseDetails')}}/{{ $defaultCourse->id }}" class="btn-link">Learn More <i
                class="fa fa-angle-right"
                aria-hidden="true"></i></a>
              </p>
            </div>
          </div>
        </div>
    @endif
    @if(count($onlineCourses) > 0)
      @if(Auth::guard('clientuser')->user())
        @foreach($onlineCourses as $course)
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="vchip_product_itm text-left">
              <figure>
                <img src="{{asset($course->image_path)}}" alt="onlne course" class="img-responsive" title="{{ $course->name }}">
              </figure>
              <ul class="vchip_categories list-inline">
                <li>{{ $course->name }}</li>
              </ul>
              <div class="vchip_product_content">
                <p>We provide online courses... </p>
                <p class="mrgn_20_top" title="Learn More"><a href="{{ url('courseDetails')}}/{{ $course->id }}" class="btn-link">Learn More <i
                  class="fa fa-angle-right"
                  aria-hidden="true"></i></a>
                </p>
              </div>
            </div>
          </div>
        @endforeach
      @else
        @foreach($onlineCourses as $course)
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="vchip_product_itm text-left">
              <figure>
                <img src="{{asset($course->image_path)}}" alt="onlne course" class="img-responsive" title="{{ $course->name }}">
              </figure>
              <ul class="vchip_categories list-inline">
                <li>{{ $course->name }}</li>
              </ul>
              <div class="vchip_product_content">
                <p>We provide online courses... </p>
                <p class="mrgn_20_top" title="Learn More"><a href="{{ url('courseDetails')}}/{{ $course->id }}" class="btn-link">Learn More <i
                  class="fa fa-angle-right"
                  aria-hidden="true"></i></a>
                </p>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    @endif
  </div>
</div>
</section>
  @endif
  @if(1 == $subdomain->test_show_hide)
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
    @if(is_object($defaultTest))
      <div class="col-lg-4 col-md-4 col-sm-6">
          <div class="vchip_product_itm text-left">
            <figure>
              <img src="{{asset($defaultTest->image_path)}}" alt="onlne course" class="img-responsive" title="{{ $defaultTest->name }}">
            </figure>
            <ul class="vchip_categories list-inline">
              <li>{{ $defaultTest->name }}</li>
            </ul>
            <div class="vchip_product_content">
              <p>We provide online test series... </p>
              <p class="mrgn_20_top" title="Learn More"><a href="{{ url('courseDetails')}}/{{ $defaultTest->id }}" class="btn-link">Learn More <i
                class="fa fa-angle-right"
                aria-hidden="true"></i></a>
              </p>
            </div>
          </div>
        </div>
    @endif
    @if(count($onlineTestSubcategories)>0)
      @if(Auth::guard('clientuser')->user())
        @foreach($onlineTestSubcategories as $subcategory)
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="vchip_product_itm text-left">
              <figure>
                <img src="{{asset($subcategory->image_path)}}" alt="onlne course" class="img-responsive" title="{{ $subcategory->name }}">
              </figure>
              <ul class="vchip_categories list-inline">
                <li>{{ $subcategory->name }}</li>
              </ul>
              <div class="vchip_product_content">
                <p>We provide online test series... </p>
                <p class="mrgn_20_top" title="Learn More"><a href="{{url('getTest')}}/{{$subcategory->id}}" class="btn-link">Learn More <i
                  class="fa fa-angle-right"
                  aria-hidden="true"></i></a>
                </p>
              </div>
            </div>
          </div>
        @endforeach
      @else
        @foreach($onlineTestSubcategories as $subcategory)
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="vchip_product_itm text-left">
              <figure>
                <img src="{{asset($subcategory->image_path)}}" alt="onlne course" class="img-responsive" title="{{ $subcategory->name }}">
              </figure>
              <ul class="vchip_categories list-inline">
                <li>{{ $subcategory->name }}</li>
              </ul>
              <div class="vchip_product_content">
                <p>We provide online test series... </p>
                <p class="mrgn_20_top" title="Learn More"><a href="{{url('getTest')}}/{{$subcategory->id}}" class="btn-link">Learn More <i
                  class="fa fa-angle-right"
                  aria-hidden="true"></i></a>
                </p>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    @endif
  </div>
  </div>
</section>
  @endif
  @if(1 == $subdomain->customer_show_hide)
<section class="v_container v_bg_white" id="customer1">
  <div class="container" >
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title  editable" id="editable-customer-slogan" name="editable-customer-slogan">{{ $subdomain->home_customer_value }}</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title editable" id="editable-customer-desc" name="editable-customer-desc">{{ $subdomain->home_customer_content }}</h3>
      </div>
      <div class="row our_customer">
        @if(count($clientCustomers) > 0)
          @foreach($clientCustomers as $customer)
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
              <div class="hovereffect" data-toggle="{{ $customer->name }}" title="{{ $customer->name }}">
                <img class="" id="client-customer-img_1"  src="{{asset($customer->image)}}" alt="SSGMCE" >
                <div class="overlay">
                  <h2 id="client_customer_name_1">{{ $customer->name }}</h2>
                  <a id="client_customer_url_1" class="info" href="{{ $customer->url }}" target="_blank">link here</a>
               </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</section>
  @endif
@if(1 == $subdomain->testimonial_show_hide)
<section class="v-section v_bg_grey" id="testimonial1">
<div class="container">
<div class="row">
<h2 class="h-bold text-center">Testimonials </h2>
<div class="divider-short"></div>
  <div class="col-md-8 col-md-offset-2 mrgn-top-20">
    <div class="carousel slide" id="testimonials-carousel" data-ride="carousel" data-interval="3000">
      <ol class="carousel-indicators">
        @if(count($testimonials) > 0)
          @foreach($testimonials as $index => $testimonial)
            @if( 0 == $index)
              <li data-target="#testimonials-carousel" data-slide-to="{{$index}}" class="active"></li>
            @else
              <li data-target="#testimonials-carousel" data-slide-to="{{$index}}" ></li>
            @endif
          @endforeach
        @endif
      </ol>
      <div class="carousel-inner">
        @if(count($testimonials) > 0)
          @foreach($testimonials as $index => $testimonial)
            @if( 0 == $index)
              <div class="active item">
            @else
              <div class="item">
            @endif
              <div class="profile-circle text-center" >
                <p class="editable" id="image_testimonial_{{$testimonial->id}}">{!! $testimonial->image !!}</p>
              </div>
                <p class="editable" id="editable_testimonial_{{$testimonial->id}}" class="editable_testimonial_first">{{ $testimonial->testimonial }}</p>
                <p class="editable pull-right" id="editable_author_{{$testimonial->id}}">{{ $testimonial->author }}</p>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
</div>
</section>
@endif
  @if(1 == $subdomain->team_show_hide)
<section id="team1" class="team v_container v_bg_white" >
 <div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2 text-center ">
      <h2 class="v_h2_title">OUR TEAM</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">Motive of bridging between educational organization  and Industry.</h3>
    </div>
  </div>
    @if(count($clientTeam) > 0)
      @foreach($clientTeam as $index => $member)
        <div class="col-md-4">
          <div class="border">
          <p class="editable" id="editable_image_{{$member->id}}">
            <img id="client-team-img_{{$member->id}}" alt="" src="{!! $member->image !!}" style="height:300px; width:300px">
          </p>
          <div class="person-detail v_bg_grey">
            <h4 class="editable" id="editable_team_{{$member->id}}">{!! $member->member_details !!}</h4>
          </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</section>
@endif

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
  $(document).ready(function(){
      setTimeout(function() {
        $('.alert-success').fadeOut('fast');
      }, 2000); // <-- time in milliseconds
  });
  window.onload = function(){
    if (null != window.opener && false ==  window.closed) {
      window.opener.location.reload(true);
      window.close();
    }
  }
</script>
@stop