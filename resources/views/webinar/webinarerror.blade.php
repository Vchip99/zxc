@extends('layouts.master')
@section('header-css')
  @include('layouts.home-css')
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
        <img src="{{ asset('images/error-page.png')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip webinar error" />
      </figure>
    </div>
    <div class="vchip-background-content">
    </div>
  </div>
</section>
<section id="" class="v_container ">
  <div class="container text-center">
    <div class="mrgn_100_top_btm">
        <h2 class="v_h2_title">Oops! Webinar Not Available</h2>
          <hr class="section-dash-dark"/>
          <h3 class="v_h3_title ">The page you are looking for is not available or has been removed or changed.</h3>
          <h1 class="" style="font-size: 50px;">404</h1>
    </div>
  </div>
</section>

@stop
@section('footer')
@include('footer.footer')
@stop