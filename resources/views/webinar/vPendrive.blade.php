@extends('layouts.master')
@section('header-title')
  <title>Learn – offline at remote area |V-edu</title>
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
				<img src="{{ asset('images/v-pd.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip pen drive"/>
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
      <h2 class="v_h2_title text-center">V-pendrive</h2>
      <p>
        V-pendrive is very useful learning platform when you are in remote area and you don’t have Internet access. So we call it as any-time, any-where educational platform.
      </p>
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
    </div>
  </div>
</section>
<section class="v_container ">
<div class="container">
  <div class="row">
   <div class="col-md-5">
       <h2 class="v_h2_title mrgn_50_top_btm text-center">Features of V-pendrive:</h2>
       <ul class="list3">
         <li >V-pen drive has complete video lecture of corresponding course.
          </li>
          <li>No need of Internet connectivity.
          </li>
          <li>Validity of pendive is life time.
          </li>
          <li>Topic-wise, chapter-wise explanations, videos, worksheets, quizzes, and more.
          </li>
       </ul>
     </div>
   <div class="col-md-7 text-center">
   <a href="#">
      <div id="" class="pd_img img-container">
        <img class="img-responsive pd-img img-hover" src="{{ asset('images/solution/pen/pen-drive.png')}}" alt="pendrive"  />
      </div>
    </a>
   </div>
  </div>
  <div class="row mrgn_30_top">
    <div class="col-md-7">
      <a href="#">
        <div id="" class="pd_img img-container">
          <img class="img-responsive img-hover" src="{{ asset('images/solution/pen/pen-drive-1.png')}}" alt="pendrive"  />
        </div>
      </a>
    </div>
    <div class="col-md-5">
        <h2 class="v_h2_title text-center">Working of V-pendrive:</h2>
        <ul class="list3">
          <li >The pen drive has the entire content and can be viewed on the TV, laptop, tablets, projectors or your smart phone.
          </li>
          <li>Pen drive is not locked to one device, so you can take it around with you wherever you go and access is on any above mention device.
          </li>
          <li>This pen drive directly gets connected to the usb slot of the your device and you can view the educational content on your device monitor. ie. its just a simple pen drive which can be used on TV, laptop, tablets, projectors or your smart phone.
          </li>
          <li> By connecting V-pendrive to  projectors your students will enjoy learning from V-edu experts.
          </li>
        </ul>
      </div>
  </div>
</div>
</section>
<section id="" class="v_container v_bg_grey">
    <div class="container text-center">
      <div>
       <h2 class="v_h2_title">Advantages V-pendrive</h2>
       <hr class="section-dash-dark"/>
       <p>Like all other online platforms or digital learning platforms, you have to have internet access to access the course. But what you will do when you are on the move, or in some remote area, or you don't want you to waste time on the internet? Having all these concerns in mind, We are come up with V-pendrive concept.</p>
     </div>
   </div>
 </section>
@stop
@section('footer')
@include('footer.footer')
@stop