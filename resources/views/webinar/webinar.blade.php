@extends('layouts.master')
@section('header-title')
  <title>Free Webinar by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <style type="text/css">
#webinar h1{font-weight: bold; color: #01bafd;}
@media(max-width:500px){#webinar h1{font-size: 25px;}
}
</style>
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
        <img src="{{ asset('images/webinar.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip webinar" />
      </figure>
    </div>
    <div class="vchip-background-content">
     <h2><b>We Will Help You To Learn </b></h2>
    </div>
  </div>
</section>
<!--
<section id="" class="v_container">
  <div class="container text-center">
    <div>
     <h2 class="v_h2_title">Webinars</h2>
     <hr class="section-dash-dark"/>
     <p>
      SpringPeople Webinars are LIVE Online Knowledge Sessions. These virtual 60 - 90 minutes meetings on emerging technologies or industry trends offers extensive opportunities to engage with our certified subject matter experts/trainers. Every Knowledge Session is recorded and can be viewed and shared by everyone who registers, time and again.
    </p>

  </div>
</div>
</section>
<section id="" class="v_container">
  <div class="container text-center">
   <div class="row">
    <div class="col-lg-12 col-md-12">
     <div class="row v_bg_grey border_box mrgn_20_top">
       <div class="col-md-3 webinar_tital text-center mrgn_20_top_btm">
        <a class="webinar-img" href=""><img class="img1" src="{{ asset('images/webinar/webinar.jpg')}}" alt="webinar img"/></a>
      </div>
      <div class="col-md-6 webinar_body more mrgn_20_top">
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque saepe iusto nulla molestias quis esse incidunt veritatis exercitationem dolore officiis. Nam quos iusto officiis ullam itaque voluptatum fuga assumenda culpa!
     </div>
     <div class="col-md-3 webinar_footer mrgn_70_top_btm">
       <a class="btn btn-block btn-success" href="{{ asset('webinarerror')}}" target="_blank"><i class="fa fa-cart-plus"></i> Register Now</a>
     </div>
   </div>
    <div class="row v_bg_grey border_box mrgn_20_top">
       <div class="col-md-3 webinar_tital text-center mrgn_20_top_btm">
        <a class="webinar-img" href=""><img class="img1" src="{{ asset('images/webinar/webinar.jpg')}}" alt="webinar img"/></a>
      </div>
      <div class="col-md-6 webinar_body more mrgn_20_top">
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque saepe iusto nulla molestias quis esse incidunt veritatis exercitationem dolore officiis. Nam quos iusto officiis ullam itaque voluptatum fuga assumenda culpa!
     </div>
     <div class="col-md-3 webinar_footer mrgn_70_top_btm">
       <a class="btn btn-block btn-success" href="{{ asset('webinarerror')}}" target="_blank"><i class="fa fa-cart-plus"></i> Register Now</a>
     </div>
   </div>
    <div class="row v_bg_grey border_box mrgn_20_top">
       <div class="col-md-3 webinar_tital text-center mrgn_20_top_btm">
        <a class="webinar-img" href=""><img class="img1" src="{{ asset('images/webinar/webinar.jpg')}}" alt="webinar img"/></a>
      </div>
      <div class="col-md-6 webinar_body more mrgn_20_top">
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque saepe iusto nulla molestias quis esse incidunt veritatis exercitationem dolore officiis. Nam quos iusto officiis ullam itaque voluptatum fuga assumenda culpa!
     </div>
     <div class="col-md-3 webinar_footer mrgn_70_top_btm">
       <a class="btn btn-block btn-success" href="{{ asset('webinarerror')}}" target="_blank"><i class="fa fa-cart-plus"></i> Register Now</a>
     </div>
   </div>
    <div class="row v_bg_grey border_box mrgn_20_top">
       <div class="col-md-3 webinar_tital text-center mrgn_20_top_btm">
        <a class="webinar-img" href=""><img class="img1" src="{{ asset('images/webinar/webinar.jpg')}}" alt="webinar img"/></a>
      </div>
      <div class="col-md-6 webinar_body more mrgn_20_top">
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque saepe iusto nulla molestias quis esse incidunt veritatis exercitationem dolore officiis. Nam quos iusto officiis ullam itaque voluptatum fuga assumenda culpa!
     </div>
     <div class="col-md-3 webinar_footer mrgn_70_top_btm">
       <a class="btn btn-block btn-success" href="{{ asset('webinarerror')}}" target="_blank"><i class="fa fa-cart-plus"></i> Register Now</a>
     </div>
   </div>
    <div class="row v_bg_grey border_box mrgn_20_top">
       <div class="col-md-3 webinar_tital text-center mrgn_20_top_btm">
        <a class="webinar-img" href=""><img class="img1" src="{{ asset('images/webinar/webinar.jpg')}}" alt="webinar img"/></a>
      </div>
      <div class="col-md-6 webinar_body more mrgn_20_top">
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque saepe iusto nulla molestias quis esse incidunt veritatis exercitationem dolore officiis. Nam quos iusto officiis ullam itaque voluptatum fuga assumenda culpa!
     </div>
     <div class="col-md-3 webinar_footer mrgn_70_top_btm">
       <a class="btn btn-block btn-success" href="{{ asset('webinarerror')}}" target="_blank"><i class="fa fa-cart-plus"></i> Register Now</a>
     </div>
   </div>
</div>
</div>
</div>
</section> -->
<section id="" class="v_container v_bg_grey">
  <div class="container text-center">
    <div>
     <h2 class="v_h2_title">Webinars</h2>
     <hr class="section-dash-dark"/>
     <p style="text-align: justify;">
      Vedu Webinars are LIVE Online Knowledge Sessions. These virtual 60 - 90 minutes meetings on emerging technologies or industry trends offers extensive opportunities to engage with our certified subject matter experts/trainers. Every Knowledge Session is recorded and can be viewed and shared by everyone who registers, time and again.
     </p>
    </div>
  </div>
</section>
<section id="webinar" class="v_container v_bg_grey">
  <div class="container text-center">
    <div class="row">
       <h1>"There is no Webinar Currently"</h1>
    </div>
  </div>
</section>
@stop
@section('footer')
@include('footer.footer')
@stop