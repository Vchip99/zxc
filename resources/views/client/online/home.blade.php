@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/index_new.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/hover.css?ver=1.0')}}" rel="stylesheet"/>
   <style>
    .vchip-container {
      max-width: 100% !important;
    }
    .form-control {
     background-color: #fff !important;
     color: black;
    }
    @media(max-width: 725px)
    {
      #vchip-header h2.animated{
        font-size: 20px !important;
      }
    }
    @media(min-width: 726px) and (max-width: 1035px)
    {
      #vchip-header h2.animated{
        font-size: 30px !important;
      }
    }

    @media(min-width: 1036px)
    {
      #vchip-header h2.animated{
        font-size: 45px !important;
      }
    }
    @media(max-width: 991px)
    {
      #bg_view{
        display: none;
      }
      #vchip-header .about-videos i {
        color: #fd012c !important;
        border: 3px solid #fd012c !important;
      }

      .vchip-cover p {
         color: black !important;
      }
      .thumbnail{
        background-color: #0099cc !important;
        border: 0px !important;
      }
    }
    @media(min-width: 992px)
    {
      #sm_view{
        display: none;
      }
    }
#vchip-header {
  background: #0099cc !important;
/*top:-90px !important;*/
  height: 785px !important;
}

/*video*/
#watch-video i {
    position: relative;
    display: table-cell;
    vertical-align: middle;
    margin: 0;
    padding: 0;
    right: -9px;
    -webkit-transition: 0.3s;
    -o-transition: 0.3s;
    transition: 0.3s;
}
/*about videos*/
#vchip-header .about-videos{
margin-top: 20px;
cursor: pointer;}
#vchip-header .about-videos i{
font-size: 15px;
color: #01bafd;
padding: 6px;
background-color: rgba(255,255,255,.35);
border-radius: 50%;
border:3px solid #01bafd;
}
@media(max-width: 768px)
{
  #vchip-header .about-videos i{
font-size: 15px;
padding: 3px;
border:2px solid #01bafd;
}
}
@media(max-width: 990px){
  #clg{
  margin-left: -10px;
}
}
.about-video-tital{
font-size: 15px;
  color: white;
  padding-bottom:
  font-style:italic;
}
@media(max-width: 1088px){
  #vchip-header h1 {
    font-size: 60px;
}
}
@media(max-width: 544px){
  #vchip-header h1 {
    font-size: 30px;
}
#vchip-header .about-videos i{
font-size: 20px;
}
}
@media(max-width: 990px){
  #vchip-header .mt-text {
    margin-top: 0;
    text-align: center;
}
}
@media(min-width: 578px) and (max-width: 764px){
.col-xs-12 {
    width: 70%;
    margin-left: 20%;
}
}
@media(max-width: 578px){
.col-xs-12 {
    width: 80%;
    margin-left: 10%;
}
}
@media(max-width: 492px){
.col-xs-12 {
    width: 100%;
    margin-left: 0%;
}
}
</style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('client.online.header_menu')
  <header id="vchip-header" class="vchip-cover vchip-cover-md" role="banner" data-stellar-background-ratio="0.5">
  <!-- <div class="overlay"></div> -->
  <div class="vchip-container ">
    <div class="row">
      <div class="col-md-12 col-md-offset-0 text-left">
        <div class="row mrgn_70_top">
        @if(Session::has('message'))
          <div class="alert alert-success" id="message">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ session('status') }}
            </div>
        @endif
          <div class="col-md-12 ">
            <h2 class="animated bounceInRightt" align="center" style="color: white;background: #074371;">Start your own online education with in 15 mins</h2>
          </div>
          <div class="row"></div>
            <div class="col-md-7 ">
              <h2 class="cursive-font" style="color: black;"><!-- Start your own online education with in 15 mins --></h2>
              <div class="row info">
                <div class="" >
                  <div class="thumbnail">
                    <div class="vid"  id="bg_view">
                      <iframe width="560" height="315" src="https://www.youtube.com/embed/nYQairlPfbA?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="about-videos" id="sm_view">
                      <p data-toggle="modal" data-target="#instituteModal" id="cotching-inst" align="center">  <i class="fa fa-play-circle-o" aria-hidden="true" ></i>
                        <span class="about-video-tital"><em>COACHING INSTITUTE</em></span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-5  animated bounceInRight" data-animate-effect="fadeInRight" style="margin-top: 32px; ">
              <form class="form-horizontal" method="post" action="{{ url('sendContactUsMail')}}" enctype="multipart/form-data">
                <div class="v_contactus-area">
                    <div class="well">
                        {{ csrf_field()}}
                        <div class="row">
                            <h2 class="v_h2_title text-center" style="color: #e91e63; margin-bottom: 2px !important;"> Contact us</h2>
                            <hr class="section-dash-dark"/>
                            <div class="">
                                <div class="form-group row">
                                    <!-- <label for="name" class="col-sm-2">Name:</label> -->
                                    <div class="col-sm-12">
                                      <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required="required" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <!-- <label for="phone" class="col-sm-2">Phone:</label> -->
                                    <div class="col-sm-12">
                                        <input type="phone" class="form-control" id="phone" name="phone" placeholder="Mobile number(10 digit)" pattern="[0-9]{10}" required="true" /></div>
                                </div>
                                <div class="form-group row">
                                    <!-- <label for="email" class="col-sm-2">Email:</label> -->
                                    <div class="col-sm-12">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required="required" /></div>
                                </div>
                                <div class="form-group row">
                                    <!-- <label for="name"  class="col-sm-2">Message:</label> -->
                                    <div class="col-sm-12">
                                      <textarea name="message" id="message" class="form-control" rows="6" cols="25" required="required" placeholder="Message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary pull-right" id="btnContactUs" title="Send Message">Send Message</button>
                            </div>
                        </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- Modal collage-->
  <div class="modal fade" id="collegeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 0px;">
        <div class="modal-header" style=" padding: 5px 10px; font-weight: bolder; text-align: center;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">College</h4>
        </div>
        <div class="modal-body" style="padding: 0px; ">
          <div class="vid"  id="collegeVideo">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/nYQairlPfbA?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
 <!-- Modal private institute-->
  <div class="modal fade" id="instituteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 0px;">
        <div class="modal-header" style=" padding: 5px 10px; font-weight: bolder; text-align: center;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">Private Institute</h4>
        </div>
        <div class="modal-body" style="padding: 0px; ">
          <div class="vid" id="instituteVideo">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/tAZDiJxIRZk?enablejsapi=1" frameborder="0" allowfullscreen></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop