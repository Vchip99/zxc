@extends('layouts.master')
@section('header-title')
<title>V-Connect - Better Class Connectivity |V-edu</title>
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
        <img src="{{ asset('images/v-connect.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip connect"/>
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
     <div class="col-md-9">
      <h2 class="v_h2_title text-center">V-connect</h2>
      <p>
        V-connect tool use to connect whole class at one place. V-connect tool support to any smart phone, ipad, tablets, laptop also we have our own device (V-pad) in which all the features of V-connects are already install.
        <br/>

        The vast feature of V-connect, makes it easy for teachers to send resources such as worksheets, tests paper, assignment and their result.
        <br/>
        You are able to assign different user roles depending upon the level of access each user requires.
      </p>
      <div class="v_container">
       <h4 class="v_h4_subtitle">Utilities For Students</h4>
       <ul class="custom-list-style">
         <li> Assignments, Notifications and Resources: Students can seen assignments, notification and the resources given by teachers also they can submit their assignments.</li>
         <li> Discussion forum: Any student can add query here and start new discussion. All the reply to your query will be save on V-cloud and also send updates to your e-mail. It also give you notification about others reply on your query. </li>
         <li> Students can take content snapshot during the lesson.  </li>
       </ul>
       <p class="bold text-center"><b>Any utilities for student's can control by lecturers</b></p>
     </div>

     <div>
       <h2 class="v_h2_title ">Key Features</h2>
       <figure class=" mrgn_30_top">
        <div class="row key-feature">
          <div class="thumbnail col-md-3 col-sm-4">
            <img src="{{ asset('images/solution/connect/Lecture-screen-shairing.jpg')}}"
            class="img-responsive" alt="Lecturee screen sharing" />
            <div class="caption text-center">
              <h3>Lecturee screen sharing</h3>
            </div>
          </div>
          <div class="thumbnail col-md-3 col-sm-4 ">
            <img src="{{ asset('images/solution/connect/forming-group.jpg')}}"
            class="img-responsive" alt="Formation of groups" />
            <div class="caption text-center">
             <h3>Formation of groups</h3>
           </div>
         </div>
         <div class="thumbnail col-md-3 col-sm-4">
          <img src="{{ asset('images/solution/connect/Student-Screen-Sharing.jpg')}}"
          class="img-responsive" alt="Students screen sharing" />
          <div class="caption text-center">
           <h3>Students screen sharing</h3>
         </div>
       </div>
       <div class="thumbnail col-md-3 col-sm-4">
        <img src="{{ asset('images/solution/connect/lock.png')}}"
        class="img-responsive" alt="lock" />
        <div class="caption text-center">
         <h3>Lock</h3>
       </div>
     </div>
     <div class="thumbnail col-md-3 col-sm-4">
      <img src="{{ asset('images/solution/connect/chat.png')}}"
      class="img-responsive" alt="chat" />
      <div class="caption text-center">
       <h3>Chat</h3>
     </div>
   </div>
   <div class="thumbnail col-md-3 col-sm-4">
    <img src="{{ asset('images/solution/connect/assigning-work.jpg')}}"
    class="img-responsive" alt="Assign work" />
    <div class="caption text-center">
     <h3>Assign work</h3>
   </div>
 </div>
</div>
</figure>

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
          <div class=" media-left ">
            <a href="{{ url('courses') }}"><img class="media-object" src="{{ asset('images/solution/online-course.png')}}" alt="online course"/></a>
          </div>
          <div class="media-body">
           <h4 class=""><a href="{{ url('courses') }}">Online courses</a></h4>
         </div>
       </div>
       <div class="media" title="Live courses">
        <div class="media-left ">
          <a href="{{ url('livecourses') }}"><img class="media-object" src="{{ asset('images/solution/live-course.png')}}" alt="live course"/></a>
        </div>
        <div class="media-body">
         <h4 class=""><a href="{{ url('livecourses') }}">Live courses</a></h4>
       </div>
     </div>
     <div class="media" title="Test series">
      <div class="media-left ">
        <a href="{{ url('online-tests') }}"><img class="media-object" src="{{ asset('images/solution/test-series.png')}}" alt="test series"/></a>
      </div>
      <div class="media-body">
       <h4 class=""><a href="{{ url('online-tests') }}">Test series</a></h4>
     </div>
   </div>
 <div class="media">
  <div class="media-left ">
    <a href="{{ url('vkits') }}"><img class="media-object" src="{{ asset('images/solution/v-kit.png')}}" alt="hobby project"/> </a>
  </div>
  <div class="media-body">
    <h4 class=""><a href="{{ url('vkits') }}">V-kit</a></h4>
  </div>
</div>
<div class="media">
  <div class="media-left ">
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
      <h2 class="v_h2_title ">FEATURES</h2>
      <hr class="section-dash-dark"/>
      <article class="row ">
        <div class="col-md-2 col-sm-2 hidden-xs ">
          <figure class="thumb   ">
            <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}"/>
          </figure>
        </div>
        <div class="col-md-10 col-sm-10 col-xs-12">
          <div class="line-top"></div>
          <div class="panel panel-default border-top">
            <div class="panel-body">
             <h4><strong>LECTURER SCREEN SHARING</strong></h4><br/>
             <p>Lecturer’s screen can be share to all the students or group of students or any individual student or no one. It’s up to lecturer.</p>
           </div>
         </div>
       </div>
     </article>
     <article class="row  mrgn_30_top">
      <div class="col-md-2 col-sm-2 hidden-xs">
        <figure class="thumb ">
          <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}" />

        </figure>
      </div>
      <div class="col-md-10 col-sm-10 col-xs-12">
        <div class="line-top"></div>
        <div class="panel panel-default border-top">
          <div class="panel-body">
           <h4><strong>Formation of groups</strong></h4><br/>
           <p>In the same class suppose students are working on different-different projects in group then lecturer has to give different instruction to different group. So lecturer can form the groups of students and control the access of particular data to particular group.</p>
         </div>
       </div>
     </div>
   </article>
   <article class="row  mrgn_30_top">
    <div class="col-md-2 col-sm-2 hidden-xs">
      <figure class="thumb ">
        <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}" />
      </figure>
    </div>
    <div class="col-md-10 col-sm-10 col-xs-12">
      <div class="line-top"></div>
      <div class="panel panel-default border-top">
        <div class="panel-body">
         <h4><strong>Students screen sharing</strong></h4><br/>
         <p>Lecturer can access the screen of any student so lecturer come to know, what exactly his students are doing and also lecturer can share screen of any student to other students for showing his/her work to other student's.</p>
       </div>
     </div>
   </div>
 </article>
 <article class="row  mrgn_30_top">
  <div class="col-md-2 col-sm-2 hidden-xs">
    <figure class="thumb ">
      <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}" />
    </figure>
  </div>
  <div class="col-md-10 col-sm-10 col-xs-12">
    <div class="line-top"></div>
    <div class="panel panel-default border-top">
      <div class="panel-body">
       <h4><strong>Lock</strong></h4><br/>
       <p>Lecturer can ban any student from accessing any data, assignment, resources. Lecturer can also from sharing lecturer screen.</p>
     </div>
   </div>
 </div>
</article>
<article class="row  mrgn_30_top">
  <div class="col-md-2 col-sm-2 hidden-xs">
    <figure class="thumb ">
      <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}" />
    </figure>
  </div>
  <div class="col-md-10 col-sm-10 col-xs-12">
    <div class="line-top"></div>
    <div class="panel panel-default border-top">
      <div class="panel-body">
       <h4><strong>Chat</strong></h4><br/>
       <p>Students can chat with each other and with lecturer.</p>
     </div>
   </div>
 </div>
</article>
<article class="row   mrgn_30_top">
  <div class="col-md-2 col-sm-2 hidden-xs">
    <figure class="thumb">
      <img class="img-responsive" src="{{ asset('images/solution/feature/feature-1.jpg')}}" />
    </figure>
  </div>
  <div class="col-md-10 col-sm-10 col-xs-12">
    <div class="line-top"></div>
    <div class="panel panel-default border-top">
      <div class="panel-body">
       <h4><strong>Assign work</strong></h4><br/>
       <p>Lecturer can assigned any education related work like assignment and students can submit their work. Also it help to keep all the record of students at one place.</p>
     </div>
   </div>
 </div>
</article>
</div>
</div>
</section>
@stop
@section('footer')
@include('footer.footer')
@stop