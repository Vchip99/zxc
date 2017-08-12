@extends('layouts.master')
@section('header-title')
  <title>LMS - Start your own Online Education Institute |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/solution.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
@include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
  <div class="vchip-background-single">
    <div class="vchip-background-img">
      <figure>
        <img src="{{ asset('images/v-edu.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip education"/>
      </figure>
    </div>
    <div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="" class="v_container v_bg_grey">
  <div class="container ">
    <div class="row">
     <div class="col-md-9" >
      <h2 class="v_h2_title text-center">V-education</h2>
      <p>
        V-edu provide end to end IoT base both software and hardware solution to educational organization/instituted. We provides platform for building your own educational product like on-line courses, live courses, webinars, on-line test series, documents, research papers, hobby projects etc.

        V-edu help you to go beyond your conventional classroom teaching. You will get customers form all our the world by using V-edu platform. V-edu take all the responsibility of your content management so you have to worry about only your content like videos of on-line courses, questions for test series etc. We provide admin panel so admin can put his/her content. You have all the authority to set permissions to access your content, courses, tests series, documents. Also you can charge for accessing your contents. We know the value of contents so your contents save on our own cloud and they are safe and encrypted so no one can access it without permission.
      </p>
      <div class="v_container">
       <h4 class="v_h4_subtitle">Start your online university just by three steps</h4>
       <ul class="custom-list-style">
         <li> Sign in (as Admin) at V-edu </li>
         <li> Upload your contents </li>
         <li> Publish </li>
       </ul>
     </div>
   </div>
   <div class="col-md-3">
    <div class="vchip-right-sidebar ">
      <h3 class="v_h3_title text-center">Solutions</h3>
      <ul class="vchip_list">
        <li title="V-education"><a href="{{ url('vEducation') }}">V-education</a></li>
        <li title="V-connect"><a href="{{ url('vConnect') }}">V-connect</a></li>
        <li title="V-pendrive"><a href="{{ url('vPendrive') }}">V-pendrive</a></li>
        <li title="V-cloud"><a href="{{ url('vCloud') }}">V-cloud</a></li>
      </ul>
    </div>
    <div class="vchip-right-sidebar mrgn_30_top_btm">
      <h3 class="v_h3_title text-center">Products</h3>
      <div class="right-sidebar">
        <div class="media" title="Online courses">
          <div class=" media-left slideanim">
            <a href="{{ url('courses') }}"><img class="media-object" src="{{ asset('images/solution/online-course.png')}}" alt="online course"/></a>
          </div>
          <div class="media-body">
           <h4 class=""><a href="{{ url('courses') }}">Online courses</a></h4>
         </div>
       </div>
       <div class="media" title="Live courses">
        <div class="media-left slideanim">
          <a href="{{ url('livecourses') }}"><img class="media-object" src="{{ asset('images/solution/live-course.png')}}" alt="live course"/></a>
        </div>
        <div class="media-body">
         <h4 class=""><a href="{{ url('livecourses') }}">Live courses</a></h4>
       </div>
     </div>
     <div class="media" title="Test series">
      <div class="media-left slideanim">
        <a href="{{ url('online-tests') }}"><img class="media-object" src="{{ asset('images/solution/test-series.png')}}" alt="test series"/></a>
      </div>
      <div class="media-body">
       <h4 class=""><a href="{{ url('online-tests') }}">Test series</a></h4>
     </div>
   </div>
   <div class="media" title="V-kit">
    <div class="media-left slideanim">
      <a href="{{ url('vkits') }}"><img class="media-object" src="{{ asset('images/solution/v-kit.png')}}" alt="hobby project"/> </a>
    </div>
    <div class="media-body">
      <h4 class=""><a href="{{ url('vkits') }}">V-kit</a></h4>
    </div>
  </div>
  <div class="media" title="V-doc">
    <div class="media-left slideanim">
      <a href="{{ url('documents') }}"><img class="media-object" src="{{ asset('images/solution/v-doc.png')}}" alt="document"/></a>
    </div>
    <div class="media-body">
      <h4 class="media-heading"><a href="{{ url('documents') }}">V-doc</a></h4>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
</section>
<section id="" class="v_container ">
  <div class="container text-center">
    <div>
     <h2 class="v_h2_title ">OUR PRODUCTS</h2>
     <hr class="section-dash-dark"/>
     <ul class="nav nav-pills" >
      <li class="active"><a data-toggle="tab" href="#online_courses">Online Classes</a></li>
      <li><a data-toggle="tab" href="#live_courses">Students</a></li>
     <!--  <li><a data-toggle="tab" href="#test_series1">Test series</a></li>
      <li><a data-toggle="tab" href="#v_kit">V-kit</a></li>
      <li><a data-toggle="tab" href="#documents">Documents</a></li> -->
    </ul>
    <div class="tab-content" style="background-color: #428bca; padding : 6px 15px;">
      <div id="online_courses" class="tab-pane fade in active">
        <h3>Online Classes</h3>
        <p>Start your online classes  with in a minutes just follow following video.</p>
        <p style="" class="embed-responsive embed-responsive-16by9 v_video">
          <!-- <iframe  src="https://www.youtube.com/embed/QSIPNhOiMoE" frameborder="0" allowfullscreen></iframe> -->
          <iframe src="https://www.youtube.com/embed/tAZDiJxIRZk" frameborder="0" allowfullscreen></iframe></p>
        </div>
        <div id="live_courses" class="tab-pane fade">
          <h3>Students</h3>
          <p>Just follow following video to know how to use our platform.</p>
          <p style="" class="embed-responsive embed-responsive-16by9 v_video">
            <!-- <iframe  src="https://www.youtube.com/embed/QSIPNhOiMoE" frameborder="0" allowfullscreen></iframe> -->
            <iframe src="https://www.youtube.com/embed/nYQairlPfbA" frameborder="0" allowfullscreen></iframe>
            </p>
          </div>
          <!-- <div id="test_series1" class="tab-pane fade">
            <h3>Test series</h3>
            <p>Start your online test series with in a minutes just follow following video.</p>
            <p style="" class="embed-responsive embed-responsive-16by9 v_video">
              <iframe  src="https://www.youtube.com/embed/QSIPNhOiMoE" frameborder="0" allowfullscreen></iframe></p>
            </div>
            <div id="v_kit" class="tab-pane fade">
              <h3>Hobby projects</h3>
              <p>Put your hobby projects on cloud with in a minutes just follow following video.</p>
              <p style="" class="embed-responsive embed-responsive-16by9 v_video">
                <iframe  src="https://www.youtube.com/embed/QSIPNhOiMoE" frameborder="0" allowfullscreen></iframe></p>
              </div>
              <div id="documents" class="tab-pane fade">
                <h3>Documents</h3>
                <p>Put your research paper on cloud with in a minutes just follow following video.</p>
                <p style="" class="embed-responsive embed-responsive-16by9 v_video">
                  <iframe  src="https://www.youtube.com/embed/QSIPNhOiMoE" frameborder="0" allowfullscreen></iframe></p>
                </div>
              </div>
            </div>
          </div> -->
        </section>
          <section id="" class="v_container v_bg_grey">
          <div class="container text-center">
            <div>
             <h2 class="v_h2_title">USE OUR COURSES, TEST SERIES AND MORE</h2>
             <hr class="section-dash-dark"/>
             <p>Even you can use the online test series, online courses, research document which are develop by V-edu. We are develop our own custom contents like online test series for GATE, CAT, IIT-JEE, PSUS, SSC exams, Aptitude etc. Our all the contents are develop by industrial expert in that particular field. </p>
           </div>
         </div>
       </section>
          @stop
          @section('footer')
          @include('footer.footer')
          @stop