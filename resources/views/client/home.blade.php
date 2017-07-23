@extends('client.dashboard')
@section('dashboard_header')
  <link href="{{ asset('css/nav_footer.css?ver=1.0') }}" rel="stylesheet"/>
  <link href="{{ asset('css/index.css') }}" rel="stylesheet"/>
  <link href="{{ asset('css/hover.css?ver=1.0') }}" rel="stylesheet"/>
  <script src="{{ asset('templateEditor/ckeditor/ckeditor.js')}}"></script>
  <style type="text/css">
    .editable:hover
    {
      border-color: red;
      border-style: dotted;
    }
    .header-color {
      background-color: #00c0ef !important;
    }
    .model-title{
      color: #000;
    }
    .model-input{
      color: #000;
      border-color: #e5e5e5;
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
@section('dashboard_content')
  <nav class="navbar navbar-default  shrink" role="navigation" style="margin: 0 0px; ">
    <div class="container">
      <div class="pull-left">
        <a id="nav-logo" class="navbar-brand pull-left" href=""><i class="fa fa-university"></i></a>
      </div>
      <div class="navbar-header pull-right">
        <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <div class="pull-right dropdown " >
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i><b class="caret"></b>
          </a>
          <ul class="dropdown-menu" role="menu">
            <div class="navbar-content">
              <li title="Dashbord"><a href=""><i class="fa fa-tachometer" aria-hidden="true"></i>
                Dashbord</a></li>
                <!-- <li><a href=""><i class="fa fa-user" aria-hidden="true"></i>
                  My Profile</a></li> -->
                  <li title="Logout"><a href=""><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></li>
                </div>
              </ul>
            </div>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
             <li class="" title="Main Site"><a href="">Main Site</a></li>
             <li class="" title="HOME"><a href="">HOME</a></li>
            @if(1 == $client->course_permission)
              <li id="courses5" class="" title="Course"><a href="">Course</a></li>
            @endif
            @if(1 == $client->test_permission)
              <li id="test5" class="" title="Test Series"><a href="">Test Series</a></li>
            @endif
            </ul>
          </div>
       </div>
  </nav>
  <form id="clientHomePage" method="post" action="{{ url('updateClientHome')}}" enctype="multipart/form-data">
    {{ method_field('PUT') }}
    {{ csrf_field() }}

@if(!empty($subdomain->background_image))
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="{{ $subdomain->background_image }}" data-stellar-background-ratio="0.5">
@else
<header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" style="background-image: url('{{ url('images/header.jpg')}}');background-attachment: fixed;
  background-position: center;
  background-size:cover;
  -webkit-background-size:cover;
  -moz-background-size:cover;
  -o-background-size:cover;" data-stellar-background-ratio="0.5">

@endif
  <div class="overlay"></div>
  <input type="hidden" id="subdomain" name="subdomain" value="{{ $subdomain->subdomain}}" />
  <div class="vchip-container ">
    <a  class="btn btn-primary" data-toggle="modal" data-target="#header" style="float: right; margin-top: 50px;" title="Edit Header">Edit Header</a>
    <div class="row">
      <div class="col-md-12 col-md-offset-0 text-left">
        <div class="row mrgn_200_top">
          <div class="col-md-7 mt-text animate-box" data-animate-effect="fadeInUp">
            <div class="editable">
              <h1 id="home_content" name="home_content" class="cursive-font" contenteditable="true">{!! $subdomain->home_content_value !!}</h1>
              <input type="hidden" id="home_content_value" name="home_content_value" value="{{ $subdomain->home_content_value}}" />
              <script type="text/javascript">
                CKEDITOR.disableAutoInline = true;
                contentEditor = CKEDITOR.inline('home_content');
                contentEditor.on('blur', function(event) {
                  document.getElementById('home_content_value').value = this.getData();
                });
              </script>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</header>
<div class="modal fade" id="header" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title model-title">Header Contents</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Header Bachground Image:</label>
          <div>
            <input type="file" class="model-input" id="background_image" data-id="" name="background_image" value="">
          </div>
        </div>
        <!-- <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Header Slogan:</label>
          <div>
            <input type="text" class="model-input" id="header_content" name="header_content" value="" style="width:300px"/>
          </div>
        </div> -->
        <input type="hidden" name="model_header" id="model_header" value="">
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" title="Save">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" title="Close">Close</button>
      </div>
    </div>
  </div>
</div>

  @if(1 == $subdomain->about_show_hide)
<section class="v_bg_grey v_container" >
  <div class="container">
    <div class="row" id="about_header">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <div class="editable" id="about">
          <h2 class="v_h2_title" id="home_about" name="home_about" contenteditable="true">{{ $subdomain->home_about_value}}</h2>
          <input type="hidden" id="home_about_value" name="home_about_value" value="{{ $subdomain->home_about_value}}" />
        </div>
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          aboutEditor = CKEDITOR.inline('home_about');
          aboutEditor.on('blur', function(event) {
            document.getElementById('home_about_value').value = this.getData();
          });
        </script>
        <hr class="section-dash-dark"/>
      </div>
    </div>
    <ul class="nav nav-pills mrgn_30_top" >
      <li class="active"><a data-toggle="tab" href="#about1" title="About">About</a></li>
      <li><a data-toggle="tab" href="#vission" title="Vission">Vission</a></li>
      <li><a data-toggle="tab" href="#mission" title="Mission">Mission</a></li>
    </ul>
    <div class="tab-content editable" style="background-color: #01bafd; padding : 6px 15px;">
      <div id="about1" class="tab-pane fade in active">
        <h3 class="v_h3_title">About</h3>
        <p id="editable_about" name="editable_about" contenteditable="true">{{ $subdomain->home_about_content }}</p>
        <input type="hidden" id="home_about_content" name="home_about_content" value="{{ $subdomain->home_about_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          aboutEditorContent = CKEDITOR.inline('editable_about');
          aboutEditorContent.on('blur', function(event) {
            document.getElementById('home_about_content').value = this.getData();
          });
        </script>
      </div>
      <div id="vission" class="tab-pane fade">
        <h3 class="v_h3_title">Vission</h3>
        <p id="editable_vission" class="editable_vission" contenteditable="true">{{ $subdomain->home_vission_content }}</p>
        <input type="hidden" id="home_vission_content" name="home_vission_content" value="{{ $subdomain->home_vission_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          vissionEditorContent = CKEDITOR.inline('editable_vission');
          vissionEditorContent.on('blur', function(event) {
            document.getElementById('home_vission_content').value = this.getData();
          });
        </script>
      </div>
      <div id="mission" class="tab-pane fade">
        <h3 class="v_h3_title">Mission</h3>
        <p id="editable_mission" class="editable_mission" contenteditable="true">{{ $subdomain->home_mission_content }}</p>
        <input type="hidden" id="home_mission_content" name="home_mission_content" value="{{ $subdomain->home_mission_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          missionEditorContent = CKEDITOR.inline('editable_mission');
          missionEditorContent.on('blur', function(event) {
            document.getElementById('home_mission_content').value = this.getData();
          });
        </script>
      </div>
    </div>
  </div><br />
</section>
  @endif
  @if(1 == $subdomain->course_show_hide)
<section class="v_bg_white" id="courses1">
  <div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title editable" id="editable_online_name" name="editable_online_name" contenteditable="true">{{ $subdomain->home_course_name }}</h2>
      <input type="hidden" id="home_course_name" name="home_course_name" value="{{ $subdomain->home_course_name }}" />
       <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          onlineEditorValue = CKEDITOR.inline('editable_online_name');
          onlineEditorValue.on('blur', function(event) {
            document.getElementById('home_course_name').value = this.getData();
          });
        </script>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title editable" id="editable_online_desc" name="editable_online_desc" contenteditable="true">{{ $subdomain->home_course_content }}</h3>
      <input type="hidden" id="home_course_content" name="home_course_content" value="{{ $subdomain->home_course_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          onlineEditorContent = CKEDITOR.inline('editable_online_desc');
          onlineEditorContent.on('blur', function(event) {
            document.getElementById('home_course_content').value = this.getData();
          });
        </script>
    </div>
  </div>
  <div class="row">
    @if(is_object($defaultCourse))
      <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
          <div class="vchip_product_itm text-left">
            <figure title="{{ $defaultCourse->name }}">
              <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
            </figure>
            <ul class="vchip_categories list-inline">
              <li>{{ $defaultCourse->name }}</li>
            </ul>
            <div class="vchip_product_content">
              <p>We provide online courses... </p>
              <p class="mrgn_20_top" title="Learn More"><a href="{{ url('courseDetails')}}/{{ $defaultCourse->id }}" class="btn-link">Learn More <i
                class="fa fa-angle-right"
                aria-hidden="true"></i></a>
              </p>
            </div>
          </div>
        </div>
    @endif
    @if(count($onlineCourses) > 0)
      @foreach($onlineCourses as $courses)
        <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
          <div class="vchip_product_itm text-left">
            <figure title="{{ $courses->name }}">
              <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
            </figure>
            <ul class="vchip_categories list-inline">
              <li>{{ $courses->name }}</li>
            </ul>
            <div class="vchip_product_content">
              <p>We provide online courses... </p>
              <p class="mrgn_20_top" title="Learn More"><a href="{{ url('courseDetails')}}/{{ $courses->id }}" class="btn-link">Learn More <i
                class="fa fa-angle-right"
                aria-hidden="true"></i></a>
              </p>
            </div>
          </div>
        </div>
      @endforeach
    @endif
</div>
</section>
  @endif
  @if(1 == $subdomain->test_show_hide)
<section class="v_bg_grey" id="test1">
  <div class="container v_container">
   <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title editable" id="editable_test_series" name="editable_test_series" contenteditable="true">{{ $subdomain->home_test_value }}</h2>
      <input type="hidden" id="home_test_value" name="home_test_value" value="{{ $subdomain->home_test_value }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          testEditorValue = CKEDITOR.inline('editable_test_series');
          testEditorValue.on('blur', function(event) {
            document.getElementById('home_test_value').value = this.getData();
          });
        </script>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title editable" id="editable_test_desc" name="editable_test_desc" contenteditable="true">{{ $subdomain->home_test_content }}</h3>
      <input type="hidden" id="home_test_content" name="home_test_content" value="{{ $subdomain->home_test_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          testEditorContent = CKEDITOR.inline('editable_test_desc');
          testEditorContent.on('blur', function(event) {
            document.getElementById('home_test_content').value = this.getData();
          });
        </script>
    </div>
  </div>
  <div class="row">
     @if(is_object($defaultTest))
      <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
          <div class="vchip_product_itm text-left">
            <figure title="{{ $defaultTest->name }}">
              <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
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
      @foreach($onlineTestSubcategories as $subcategory)
        <div class="col-lg-4 col-md-4 col-sm-6 slideanim">
          <div class="vchip_product_itm text-left">
            <figure title="{{ $subcategory->name }}">
              <img src="{{asset('images/index/online-course.jpg')}}" alt="onlne course" class="img-responsive">
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
  </div>
</div>
</section>
  @endif
  @if(1 == $subdomain->customer_show_hide)
<section class="v_container v_bg_white" id="customer1">
  <div class="container" >
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title  editable" id="editable_customer_value" name="editable_customer_value" contenteditable="true">{{ $subdomain->home_customer_value }}</h2>
        <input type="hidden" id="home_customer_value" name="home_customer_value" value="{{ $subdomain->home_customer_value }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          testEditorContent = CKEDITOR.inline('editable_customer_value');
          testEditorContent.on('blur', function(event) {
            document.getElementById('home_customer_value').value = this.getData();
          });
        </script>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title editable" id="editable_customer_content" name="editable_customer_content" contenteditable="true">{{ $subdomain->home_customer_content }}</h3>
        <input type="hidden" id="home_customer_content" name="home_customer_content" value="{{ $subdomain->home_customer_content }}" />
        <script type="text/javascript">
          CKEDITOR.disableAutoInline = true;
          testEditorContent = CKEDITOR.inline('editable_customer_content');
          testEditorContent.on('blur', function(event) {
            document.getElementById('home_customer_content').value = this.getData();
          });
        </script>
      </div>
      <div class="row our_customer">
        @if(count($clientCustomers) > 0)
          @foreach($clientCustomers as $customer)
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
              <div  data-toggle="modal" data-target="#customer_{{ $customer->id }}" title="{{ $customer->name }}">
                <div class="hovereffect">
                  <img class="" id="client-customer-img_{{ $customer->id }}"  src="{{asset($customer->image)}}" alt="SSGMCE" >
                  <div class="overlay">
                    <h2 id="client_customer_name_{{ $customer->id }}">{{ $customer->name }}</h2>
                    <a id="client_customer_url_{{ $customer->id }}" class="info" href="{{ $customer->url }}" target="_blank">link here</a>
                  </div>
                </div>
              </div>
              <!-- <a data-toggle="modal" data-target="#customer_{{ $customer->id }}" >Edit </a> -->
              <div class="modal fade" id="customer_{{ $customer->id }}" >
                <div class="modal-dialog" role="document" >
                  <div class="modal-content" >
                    <div class="modal-header">
                      <h5 class="modal-title model-title">Customer Details</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="recipient-name" class="form-control-label model-title">Image:</label>
                        <div>
                          <input type="file" class="model-input" id="model-customer-image_{{ $customer->id }}" data-id="{{ $customer->id }}" name="customer_image_{{ $customer->id }}" value="">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="recipient-name" class="form-control-label model-title">Customer Name:</label>
                        <div>
                          <input type="text" class="model-input" id="customer_name_{{ $customer->id }}" name="customer_name_{{ $customer->id }}" value="{{ $customer->name }}" style="width:300px"/>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="recipient-name" class="form-control-label model-title">Link Url:</label>
                        <div>
                          <input type="text" class="model-input" id="customer_link_url_{{ $customer->id }}" name="customer_link_url_{{ $customer->id }}" value="{{ $customer->url }}" style="width:300px">
                        </div>
                      </div>
                      <input type="hidden" name="model_customer" id="model_customer" value="{{ $customer->id }}">
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-primary" data-dismiss="modal">Save</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
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
      <!-- Carousel indicators -->
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
      <!-- Carousel items -->
      <div class="carousel-inner">
        @if(count($testimonials) > 0)
          @foreach($testimonials as $index => $testimonial)
            @if( 0 == $index)
              <div class="active item">
            @else
              <div class="item">
            @endif
              <div class="profile-circle text-center" >
                <p class="editable" id="image_testimonial_{{$testimonial->id}}" contenteditable="true">{!! $testimonial->image !!}</p>
                  <input type="hidden" id="testimonial_image_{{$testimonial->id}}" name="testimonial_image_{{$testimonial->id}}" value="">
                  <script type="text/javascript">
                    CKEDITOR.disableAutoInline = true;
                    vissionEditorContent = CKEDITOR.inline('image_testimonial_{{$testimonial->id}}');
                    vissionEditorContent.on('blur', function(event) {
                      document.getElementById('testimonial_image_{{$testimonial->id}}').value = this.getData();
                    });
                  </script>
              </div>
                <p class="editable" id="editable_testimonial_{{$testimonial->id}}" class="editable_testimonial_first" contenteditable="true">{{ $testimonial->testimonial }}</p>
                <input type="hidden" id="testimonial_{{$testimonial->id}}" name="testimonial_{{$testimonial->id}}" value="{{ $testimonial->testimonial }}" />
                <script type="text/javascript">
                  CKEDITOR.disableAutoInline = true;
                  vissionEditorContent = CKEDITOR.inline('editable_testimonial_{{$testimonial->id}}');
                  vissionEditorContent.on('blur', function(event) {
                    document.getElementById('testimonial_{{$testimonial->id}}').value = this.getData();
                  });
                </script>

                <p class="editable pull-right" id="editable_author_{{$testimonial->id}}" contenteditable="true">{{ $testimonial->author }}</p>
                <input type="hidden" id="author_{{$testimonial->id}}" name="author_{{$testimonial->id}}" value="{{ $testimonial->author }}" />
                <script type="text/javascript">
                  CKEDITOR.disableAutoInline = true;
                  vissionEditorContent = CKEDITOR.inline('editable_author_{{$testimonial->id}}');
                  vissionEditorContent.on('blur', function(event) {
                    document.getElementById('author_{{$testimonial->id}}').value = this.getData();
                  });
                </script>
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
          <div class="border team1" data-toggle="modal" data-target="#teamName_{{$member->id}}" id="{{$member->id}}">
            <img id="client-team-img_{{$member->id}}" alt="" src="{{ asset($member->image)}}" style="height:300px; width:300px">
            <div class="person-detail v_bg_grey">
                <h4 class="" id="client-degignation-h4_{{$member->id}}">{!! $member->member_details !!}</h4>
                <input type="hidden" name="member_degignation_{{$member->id}}" id="client-degignation_{{$member->id}}" value="" />
            </div>
          </div>
        </div>
        <div class="modal fade" id="teamName_{{$member->id}}" >
          <div class="modal-dialog" role="document" >
            <div class="modal-content" >
              <div class="modal-header">
                <h5 class="modal-title model-title">Team Member Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="recipient-name" class="form-control-label model-title">Image:</label>
                  <div>
                    <input type="file" class="model-input" id="model-team-image_{{$member->id}}" data-id="{{$member->id}}" name="image_{{$member->id}}" value="">
                  </div>
                </div>
                <div class="form-group">
                  <label for="recipient-name" class="form-control-label model-title">Designation:</label>
                  <div>
                    <textarea type="text" class="model-input" id="model-team-designation_{{$member->id}}" name="designation" value="" style="width:300px">{!! $member->member_details !!}</textarea>
                  </div>
                </div>
                <input type="hidden" name="model_team" id="model_team" value="{{$member->id}}">
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</section>
@endif
<footer id="footer">
  <div class="footer">
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
         <h4 class="editable" id="editable_contact" contenteditable="true">{!! $subdomain->contact_us !!}</h4>
          <input type="hidden" id="contact_us" name="contact_us" value="{!! $subdomain->contact_us !!}" />
          <script type="text/javascript">
            CKEDITOR.disableAutoInline = true;
            vissionEditorContent = CKEDITOR.inline('editable_contact');
            vissionEditorContent.on('blur', function(event) {
              document.getElementById('contact_us').value = this.getData();
            });
          </script>
       </div>
     </div>
   </div>
 </div>

 <div id="footer" class="footer-bottom">
  <div class="container">
    <div class="row">
      <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 vchip-design">
        <p class="pull-left" title="vchiptech.com"><a href="http://www.vchiptech.com/" class="site_link" target="_blank">Design by: vchiptech.com </a></p>
      </div>
      <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 social-network1">
        <ul class="social-network social-circle pull-left">
          <li><a data-href="{{ $subdomain->facebook_url }}" class="icoFacebook" title="Facebook" data-toggle="modal" data-target="#facebookUrl"><i class="fa fa-facebook"></i></a></li>
          <li><a data-href="{{ $subdomain->twitter_url }}" class="icoTwitter" title="Twitter" data-toggle="modal" data-target="#twitterUrl"><i class="fa fa-twitter"></i></a></li>
          <li><a data-href="{{ $subdomain->google_url }}" class="icoGoogle" title="Google +" data-toggle="modal" data-target="#googleUrl"><i class="fa fa-google-plus"></i></a></li>
          <li><a data-href="{{ $subdomain->linkedin_url }}" class="icoLinkedin" title="Linkedin" data-toggle="modal" data-target="#linkedinUrl"><i class="fa fa-linkedin"></i></a></li>
        </ul>
        <input type="hidden" id="facebook_url" name="facebook_url" value="">
        <input type="hidden" id="twitter_url" name="twitter_url" value="">
        <input type="hidden" id="google_url" name="google_url" value="">
        <input type="hidden" id="linkedin_url" name="linkedin_url" value="">
      </div>
      <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6 institute1">
        <p class="pull-right" title="{{ $subdomain->institute_name }}"><a data-href="{{ $subdomain->institute_url }}" id="site_link" class="site_link"  data-toggle="modal" data-target="#instituteName" >{{ $subdomain->institute_name }}</a></p>
        <input type="hidden" id="institute_name" name="institute_name" value="">
        <input type="hidden" id="institute_url" name="institute_url" value="">
      </div>
    </div>
  </div>
</div>
</footer>
<input type="hidden" name="about_section" id="about_section" value="{{ $subdomain->about_show_hide }}">
<input type="hidden" name="course_section" id="course_section" value="{{ $subdomain->course_show_hide }}">
<input type="hidden" name="test_section" id="test_section" value="{{ $subdomain->test_show_hide }}">
<input type="hidden" name="customer_section" id="customer_section" value="{{ $subdomain->customer_show_hide }}"">
<input type="hidden" name="testimonial_section" id="testimonial_section" value="{{$subdomain->testimonial_show_hide}}">
<input type="hidden" name="team_section" id="team_section" value="{{$subdomain->team_show_hide}}">
<button class="btn btn-primary"  style="float: right; padding-right: 50px;" title="Update">Update</button>
</form>
<!-- institute name -->
<div class="modal fade" id="instituteName" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title model-title">Institute Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Name:</label>
          <div>
            <input type="text" class="model-input" id="client-institute-name" name="institute" value="" style="width:300px">
          </div>
        </div>
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Url:</label>
          <div>
            <input type="text" class="model-input" id="client-institute-url" name="url" value="" style="width:300px">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- facebook -->
<div class="modal fade" id="facebookUrl" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Facebook Url:</label></br>
          <input type="text" class="model-input" id="client-facebook-url" name="url" value="">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- twitter -->
<div class="modal fade" id="twitterUrl" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Twitter Url:</label></br>
          <input type="text" class="model-input" id="client-twitter-url" name="url" value="">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- google + -->
<div class="modal fade" id="googleUrl" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Google Plus Url:</label></br>
          <input type="text" class="model-input" id="client-google-url" name="url" value="">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- linkedin -->
<div class="modal fade" id="linkedinUrl" >
  <div class="modal-dialog" role="document" >
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="recipient-name" class="form-control-label model-title">Linkedin Url:</label></br>
          <input type="text" class="model-input" id="client-linkedin-url" name="url" value="" />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('.remove').click(function(){
    var sectionId = $(this).prop('id')
    $.confirm({
        title: 'Confirmation',
        content: 'You want to reomve this section from home page?',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    if('courses' == sectionId){
                      $('#course_section').val(0);
                    }
                    if('test' == sectionId){
                      $('#test_section').val(0);
                    }
                    if('about' == sectionId){
                      $('#about_section').val(0);
                    }
                    if('customer' == sectionId){
                      $('#customer_section').val(0);
                    }
                    if('testimonial' == sectionId){
                      $('#testimonial_section').val(0);
                    }
                    if('team' == sectionId){
                      $('#team_section').val(0);
                    }
                    document.getElementById('clientHomePage').submit();
                  }
              },
              Cancle: function () {
              }
          }
        });
  });

  $('.add').click(function(){
    var sectionId = $(this).prop('id');
    if('courses' == sectionId){
      $('#course_section').val(1);
    }
    if('test' == sectionId){
      $('#test_section').val(1);
    }
    if('about' == sectionId){
      $('#about_section').val(1);
    }
    if('customer' == sectionId){
      $('#customer_section').val(1);
    }
    if('testimonial' == sectionId){
      $('#testimonial_section').val(1);
    }
    if('team' == sectionId){
      $('#team_section').val(1);
    }
    document.getElementById('clientHomePage').submit();
  });

  $('#instituteName').on('hide.bs.modal', function () {
    var name = $("#client-institute-name").val();
    var url = $("#client-institute-url").val();
    if( name && url ){
      var institute = document.getElementById('site_link');
      institute.setAttribute('data-href',url);
      institute.innerHTML = name;
      document.getElementById('institute_name').value = name;
      document.getElementById('institute_url').value = url;
    }
  })

  $('#facebookUrl').on('hide.bs.modal', function () {
    var url = $("#client-facebook-url").val();
    if( url ){
      var facebook = document.getElementById('facebookUrl');
      facebook.setAttribute('data-href',url);
      document.getElementById('facebook_url').value = url;
    }
  })

   $('#twitterUrl').on('hide.bs.modal', function () {
    var url = $("#client-twitter-url").val();
    if( url ){
      var twitter = document.getElementById('twitterUrl');
      twitter.setAttribute('data-href',url);
      document.getElementById('twitter_url').value = url;
    }
  })

  $('#googleUrl').on('hide.bs.modal', function () {
    var url = $("#client-google-url").val();
    if( url ){
      var google = document.getElementById('googleUrl');
      google.setAttribute('data-href',url);
      document.getElementById('google_url').value = url;
    }
  })

  $('#linkedinUrl').on('hide.bs.modal', function () {
    var url = $("#client-linkedin-url").val();
    if( url ){
      var linkedin = document.getElementById('linkedinUrl');
      linkedin.setAttribute('data-href',url);
      document.getElementById('linkedin_url').value = url;
    }
  })

  $('div[id^=teamName_]').on('hide.bs.modal', function (event) {
    var modal = $(this);
    var memberId = modal.find('#model_team').val();
    var designation = $("#model-team-designation_"+memberId).val();
    if( designation ){
      var designationEle = document.getElementById('client-degignation-h4_'+memberId);
      designationEle.innerHTML = designation;
      document.getElementById('client-degignation_'+memberId).value = designation;
    }
  })

  $('input[id^=model-team-image_]').change( function(event) {
    var imageId = $(event.target).data('id');
    $("img#client-team-img_"+imageId).fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));
  });

  $('div[id^=customer_]').on('hide.bs.modal', function (event) {
    var modal = $(this);
    var customerId = modal.find('#model_customer').val();
    var customerName = $("#customer_name_"+customerId).val();
    if( customerName ){
      var customerNameEle = document.getElementById('client_customer_name_'+customerId);
      customerNameEle.innerHTML = customerName;
    }
    var customerLink = $("#customer_link_url_"+customerId).val();
    if( customerLink ){
      var customerLinkEle = document.getElementById('client_customer_url_'+customerId);
        customerLinkEle.href = customerLink;
    }
  })

  $('input[id^=model-customer-image_]').change( function(event) {
    var customerId = $(event.target).data('id');
    $("img#client-customer-img_"+customerId).fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));
  });

  // $('div#header').on('hide.bs.modal', function (event) {
  //   var header_slogan = $("#header_content").val();
  //   if( header_slogan ){
  //     var homeContentEle = document.getElementById('home_content');
  //     homeContentEle.innerHTML = header_slogan;
  //     document.getElementById('home_content_value').value = header_slogan;
  //   }
  // })

  $('#background_image').change( function(event) {
    $("header#vchip-header").fadeIn("fast").attr('style',"background-image: url('"+URL.createObjectURL(event.target.files[0])+"')");
  });
</script>
@stop

