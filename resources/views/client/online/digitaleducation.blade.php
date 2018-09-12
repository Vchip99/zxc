@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/themify-icons/themify-icons.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    #feature {
  padding: 80px 0;
}
#feature .media {
  margin: 0px 0 70px 0;
}
#feature .media .media-left {
  padding-right: 25px;
}
#feature h3 {
  color: #000;
  font-size: 18px;
  text-transform: uppercase;
  text-align: center;
  margin-bottom: 20px;
  margin: 0px 0px 15px;
  font-weight: 400;
}
#feature p {
  line-height: 25px;
  font-size: 14px;
  color: #777777;
}
#feature .icon {
  text-decoration: none;
  color: #fff;
  background-color: #02bdd5;
  height: 100px;
  text-align: center;
  width: 100px;
  font-size: 50px;
  line-height: 100px;
  overflow: hidden;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  -ms-border-radius: 50%;
  -o-border-radius: 50%;
  text-shadow: #00a4ba 1px 1px, #00a4ba 2px 2px, #00a4ba 3px 3px, #00a4ba 4px 4px, #00a4ba 5px 5px, #00a4ba 6px 6px, #00a4ba 7px 7px, #00a4ba 8px 8px, #00a4ba 9px 9px, #00a4ba 10px 10px, #00a4ba 11px 11px, #00a4ba 12px 12px, #00a4ba 13px 13px, #00a4ba 14px 14px, #00a4ba 15px 15px, #00a4ba 16px 16px, #00a4ba 17px 17px, #00a4ba 18px 18px, #00a4ba 19px 19px, #00a4ba 20px 20px, #00a4ba 21px 21px, #00a4ba 22px 22px, #00a4ba 23px 23px, #00a4ba 24px 24px, #00a4ba 25px 25px, #00a4ba 26px 26px, #00a4ba 27px 27px, #00a4ba 28px 28px, #00a4ba 29px 29px, #00a4ba 30px 30px, #00a4ba 31px 31px, #00a4ba 32px 32px, #00a4ba 33px 33px, #00a4ba 34px 34px, #00a4ba 35px 35px, #00a4ba 36px 36px, #00a4ba 37px 37px, #00a4ba 38px 38px, #00a4ba 39px 39px, #00a4ba 40px 40px, #00a4ba 41px 41px, #00a4ba 42px 42px, #00a4ba 43px 43px, #00a4ba 44px 44px, #00a4ba 45px 45px, #00a4ba 46px 46px, #00a4ba 47px 47px, #00a4ba 48px 48px, #00a4ba 49px 49px, #00a4ba 50px 50px, #00a4ba 51px 51px, #00a4ba 52px 52px, #00a4ba 53px 53px, #00a4ba 54px 54px, #00a4ba 55px 55px, #00a4ba 56px 56px, #00a4ba 57px 57px, #00a4ba 58px 58px, #00a4ba 59px 59px, #00a4ba 60px 60px, #00a4ba 61px 61px, #00a4ba 62px 62px, #00a4ba 63px 63px, #00a4ba 64px 64px, #00a4ba 65px 65px, #00a4ba 66px 66px, #00a4ba 67px 67px, #00a4ba 68px 68px, #00a4ba 69px 69px, #00a4ba 70px 70px, #00a4ba 71px 71px, #00a4ba 72px 72px, #00a4ba 73px 73px, #00a4ba 74px 74px, #00a4ba 75px 75px, #00a4ba 76px 76px, #00a4ba 77px 77px, #00a4ba 78px 78px, #00a4ba 79px 79px, #00a4ba 80px 80px, #00a4ba 81px 81px, #00a4ba 82px 82px, #00a4ba 83px 83px, #00a4ba 84px 84px, #00a4ba 85px 85px, #00a4ba 86px 86px, #00a4ba 87px 87px, #00a4ba 88px 88px, #00a4ba 89px 89px, #00a4ba 90px 90px, #00a4ba 91px 91px, #00a4ba 92px 92px, #00a4ba 93px 93px, #00a4ba 94px 94px, #00a4ba 95px 95px, #00a4ba 96px 96px, #00a4ba 97px 97px, #00a4ba 98px 98px, #00a4ba 99px 99px, #00a4ba 100px 100px;
}
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
.vchip-background-content {
  width: 100% !important;
  bottom: 30px !important;
  position: static !important;
  background: rgba(0, 0, 0, 0.5) !important;
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
          <img src="{{ asset('images/digital-education.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Digital Education" />
        </figure>
      </div>
      <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Start your own online education with in 15 mins</h2>
      </div>
    </div>
  </section>
<section id="feature" class="v_container v_bg_grey">
   <h2 class="v_h2_title text-center"> Digital Education</h2>
   <hr class="section-dash-dark mrgn_60_btm"/>
    <div class="container">
      <div class="row">
          <div class=" col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInUp animated" data-wow-duration="500ms" data-wow-delay="300ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-layout-grid2 "></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Responsive web-page </h4>
                      <p>Build your responsive website on our sub-domain within 5 minute</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="600ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="fa fa-laptop" aria-hidden="true"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Online test series</h4>
                      <p>Generate your own online tests/test-series. It supports both MCQs and Numerical type questions. You can generate any numbers of test series</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="900ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="fa fa-book"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Online Courses</h4>
                      <p>Generate your own Online course. If students have any difficulty then they can comment to corresponding video. You can generate any numbers of courses</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1200ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-bell"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Notification</h4>
                      <p>Notify the students, whenever someone replies to their query/comments.</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1500ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-comments-smiley"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Admin message</h4>
                      <p>Message trigger to all the users if Admin add any new test Series or course</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1800ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-pencil-alt"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Assignment</h4>
                      <p>Lecturer can give assignment to students and they can submit it back. Also you can give them correction and marking</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1800ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-money"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Payment Gateway</h4>
                      <p>Payment will directly transfer to your account</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1800ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-layout-grid3"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Dashboard for students</h4>
                      <p>All detail about students, their result and performance detail about online test-series, all the online courses that added in favorite and if course is certified then certificate of course like that all the things are available in dashboard</p>
                  </div>
              </div>
          </div>
          <div class="col-md-4 col-lg-4 col-xs-12">
              <div class="media wow fadeInDown animated" data-wow-duration="500ms" data-wow-delay="1800ms">
                  <div class="media-left">
                      <div class="icon">
                          <i class="ti-user"></i>
                      </div>
                  </div>
                  <div class="media-body">
                      <h4 class="media-heading">Admin panel (ERP)</h4>
                      <p>Admin can access all the detail and performance of his/her students just by click. </p><p>Also Admin can manage all detail about the students. Give permission to access the courses etc. </p>
                  </div>
              </div>
          </div>
      </div>
    </div>
</section>
<section id="" class="v_container " >
  <div class="container">

    <div class="row">
      <div class="col-md-6 vcenter">
      <h3 class="v_h3_title "> How to Use it:</h3>
           <ul class="user-list">
              <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3> Start online class with in 15 minutes.</h3>    </li>
            <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Sign-up, its free to use up-to 20 login. </h3>    </li>
            <li class=""><span class="" ><i class="ti-check-box"></i> </span>  <h3>Just go through video.</h3>    </li>
          </ul>

    </div>
      <div class="col-md-6 ">
        <div class="embed-responsive embed-responsive-16by9">
           <iframe width="560" height="315" src="https://www.youtube.com/embed/nYQairlPfbA" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    </div>
  </div>
</section>
<section class="prizing">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <h2 class="title wow fadeInDown" data-wow-delay=".3s" data-wow-duration="500ms">Price </h2>
                    <p class="wow fadeInDown" data-wow-delay=".5s" data-wow-duration="500ms">All the above mention facilities are at free of cost up-to 20 login.<br/>@ Rs. 2999 /year for unlimited logins.</p>
                    <a href="{{ url('pricing') }}" class="btn btn-default btn-contact wow fadeInDown" data-wow-delay=".7s" data-wow-duration="500ms">Buy Now</a>
                </div>
            </div>

        </div>
    </div>
</section>
@stop
@section('footer')
  @include('client.online.footer')
@stop