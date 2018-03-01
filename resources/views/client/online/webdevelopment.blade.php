@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/service.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/themify-icons/themify-icons.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  ul.user-list {
  list-style: none;
  padding: 0;
}
.user-list i {
    font-size: 30px;
    float: left;
    color: #e91e63;
    position: relative;/*
    opacity: 0.6;*/
    -webkit-transition: all .8s ease;
    transition: all .8s ease;
    padding-right: 5px;
}
ul.user-list h3{ font-size: 20px;
color: #747474; }
.prizing{
  background: #eee;
  background-size: cover;
  background-attachment: fixed;
  padding: 80px 0;
  text-align: center;
  position: relative;
  color: #000;
}
.prizing .block {
  position: relative;
  z-index: 99;
  color: #000;
}
.prizing .block h2 {
  margin-bottom: 15px;
  color: #000;
}
.prizing .block p {
  font-size: 15px;
  font-weight: 300;
  font-family: 'Roboto', sans-serif;
  margin-top: 20px;
}
.prizing .block .btn-contact {
  background:  #02bdd5;
  border: none;
  color: #fff;
  padding: 16px 35px;
  margin-top: 20px;
  font-size: 12px;
  letter-spacing: 2px;
  text-transform: uppercase;
  border-radius: 0;
}
.prizing .block .btn-contact i {
  margin-right: 10px;
}
/*slider*/
.carousel-shadow{
padding:20px;
box-shadow: 0 13px 40px 0 rgba(62,68,93,.3), 0 2px 4px 0 rgba(62,68,93,.06);
  transform: translateY(-5px);}
  /**/
  .content-box {
    position: relative;
    box-shadow: 1px 4px 10px -6px #222;
    margin-bottom: 20px;
    padding: 1px 0px;
    margin-top:30px;
}
.content-box:hover {
    box-shadow: 1px 4px 10px -6px;
}
.content-box:hover ,.content-box:active  ,.content-box:focus {
    text-decoration: none;
}
.content-box a h4 {
    color: #222;
    font-weight: 600;
    font-family: serif;
    font-size: 17px;
}
.content-box-icon {
    width: 50px;
    height: 50px;
    background: #ce2a1b;
    display: block;
    text-align: center;
    line-height: 50px;
    font-size: 25px;
    border-radius: 100%;
    margin: auto;
    color: #fff;
}
.bg-vaiolet{
    background: #673ab7;
}
.bg-blue{
    background:rgb(38, 172, 226);
}
.bg-light-red{
    background: #e91740;
}
.bg-light-blue{
    background: #009688;
}
/*modal*/
   #formModal  .formModal{
      border-top:10px solid #01bafd;
    }
    #formModal .close{color: #fff;}
    #formModal .modal, .modal-content{border-radius:  0px;
      background-color: rgba(0, 0, 0, 0.5);}
     #formModal  form{
        background-color: rgba(0, 0, 0, 0.5);
            padding: 30px;
      }
     #formModal form .form-control {
    background: transparent;
    color: #fff;
    font-size: 16px !important;
    width: 100%;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    -webkit-transition: 0.5s;
    -o-transition: 0.5s;
    transition: 0.5s;
    border-radius: 0px!important
}
#formModal form .form-group input:focus{border: 2px solid #fff !important;
}
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('client.online.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single" >
      <div class="vchip-background-img">
        <figure>
          <img src="{{ asset('images/web&app.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="contact us" />
        </figure>
      </div>
      <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
      </div>
    </div>
  </section>
  <section id="feature" class="v_container ">
    @if(Session::has('message'))
      <div class="alert alert-success" id="message">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          {{ Session::get('message') }}
      </div>
    @endif
    @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
   <h2 class="v_h2_title text-center"> Custom web & App Development</h2>
   <hr class="section-dash-dark mrgn_60_btm"/>
    <div class="container">
    <p>We develop custom website and app (Android and iOS) from scratch for educational institute/organizations. Our expert at first do study about your business and suggest you appropriate design. Once you finalize the design and submit the initial required information, our team will design and develop website within 15 days. Also, at the same time our team will design, develop and store your app at Google play store and app store. We have specialty in designing and developing website and app from scratch in education sector.   </p>

    </div>
</section>
<section id="" class="v_container v_bg_grey" >
  <div class="container">

    <div class="row">
      <div class="col-md-6 vcenter">
           <h3 class="v_h3_title "> How to Use it:</h3>
           <ul class="user-list">
             <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Design Responsive website</h3></li>
             <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Develop Dynamic website</h3></li>
             <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Android app development</h3></li>
             <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>iOS app development</h3></li>
          </ul>
      </div>
      <br/>
        <div class="col-md-6 carousel-shadow">
          <div id="carousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
              <li data-target="#carousel" data-slide-to="0" class="active"></li>
              <li data-target="#carousel" data-slide-to="1"></li>
              <li data-target="#carousel" data-slide-to="2"></li>
              <li data-target="#carousel" data-slide-to="3"></li>
              <li data-target="#carousel" data-slide-to="4"></li>
              <li data-target="#carousel" data-slide-to="5"></li>
              <li data-target="#carousel" data-slide-to="6"></li>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
              <div class="item active">
                <a href="http://vchiptech.com/" target="_blank"><img src="{{ asset('images/web&app/vchiptech.jpg')}}" alt="Vchip Technology"/></a>
              </div>
              <div class="item ">
                <a href="http://vceinstitute.com/" target="_blank"><img src="{{ asset('images/web&app/vce.jpg')}}" alt="VCE"/></a>
              </div>
              <div class="item ">
                <a href="http://itienggtech.com/" target="_blank"><img src="{{ asset('images/web&app/apex.jpg')}}" alt="APEX"/></a>
              </div>
              <div class="item ">
                <a href="http://edutimetest.com" target="_blank"><img src="{{ asset('images/web&app/edutime.jpg')}}" alt="Edutimes"/></a>
              </div>
              <div class="item ">
                <a href="http://vivekanandgurukul.com" target="_blank"><img src="{{ asset('images/web&app/gurukul.jpg')}}" alt="Swami vivekananda gurukul"/></a>
              </div>
              <div class="item">
                <a href="http://gatethedirection.com/" target="_blank"><img src="{{ asset('images/web&app/gate.jpg')}}" alt="Gate The Direction"/></a>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</section>
<section class="v_container ">
  <h2 class="v_h2_title text-center">We provide</h2>
  <hr class="section-dash-dark mrgn_60_btm"/>
  <div class="container">
      <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-vaiolet">
                            <i class="ti-cloud-up"></i>
                        </span>
                        <h4>Hosting for one year</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-light-blue">
                            <i class="ti-world"></i>
                        </span>
                        <h4>Domain name</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-light-red">
                            <i class="ti-desktop"></i>
                        </span>
                        <h4>Design and Develop websites</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-blue">
                            <i class="ti-mobile"></i>
                        </span>
                        <h4>Design and Develop app</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-vaiolet">
                            <i class="ti-pencil-alt"></i>
                        </span>
                        <h4>Editing in any page</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-light-blue">
                            <i class="ti-layers-alt"></i>
                        </span>
                        <h4>Add/remove the page</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-light-red">
                            <i class="ti-settings"></i>
                        </span>
                        <h4>Solving any technical issues</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-blue">
                            <i class="ti-search"></i>
                        </span>
                        <h4>SEO</h4>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="content-box text-center">
                        <span class="content-box-icon bg-vaiolet">
                            <i class="ti-calendar"></i>
                        </span>
                        <h4>Digital Marketing (one Month)</h4>
                </div>
            </div>
      </div>
  </div>
</section>
<section class="prizing v_container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <h2 class="title wow fadeInDown" data-wow-delay=".3s" data-wow-duration="500ms">Price </h2>
                    <p class="wow fadeInDown" data-wow-delay=".5s" data-wow-duration="500ms">Rs. 2999</p>
                    <a href="{{ url('getWebdevelopment')}}" data-toggle="modal" class="btn btn-default btn-contact wow fadeInDown" data-wow-delay=".7s" data-wow-duration="500ms">Buy Now</a>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('footer')
  @include('client.online.footer')
@stop