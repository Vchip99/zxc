@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/service.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/themify-icons/themify-icons.css?ver=1.0')}}" rel="stylesheet"/>

@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
  @include('header.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single" >
      <div class="vchip-background-img">
        <figure>
          <img src="{{ asset('images/digital-marketing.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="contact us" />
        </figure>
      </div>
      <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
      </div>
    </div>
  </section>
  <section id="" class="v_container ">
     <h2 class="v_h2_title text-center"> Digital Education</h2>
     <hr class="section-dash-dark "/>
      <div class="container">
      <p>If you are looking to expand your network and in search of new customers then you are at right place. Digital marketing work on the principle of Attract, Engage and convert your targeted audiences into customers. </p>
      <p>Our expert team have good experience of Google Online Marketing and they know the major Challenges of Digital Marketing. They have good hands-on AdWords so you will get the most out of AdWords for your business growth. </p>
      </div>
  </section>
  <section id="feature" class="v_container ">
    <div class="container">
        <div class="row">
        <div class="col-md-6 animate-box" >
          <div class="feature-center" >
            <span class="icon">
              <i class="ti-image"></i>
            </span>
            <span class="counter">
              <h3 class="v_h3_title">Digital Advertising</h3>
              <p>Online advertising have importance to reach your brand to beyond their existing networks, to highly targeted audiences. Most of your targeted audiences are our user so advertisement on our platform is most helpful to reach to your targeted audience.</p>
            </span>
          </div>
        </div>
        <div class="col-md-6 animate-box" >
          <div class="feature-center" >
            <span class="icon">
              <i class="ti-email "></i>
            </span>
            <span class="counter">
              <h3 class="v_h3_title">Email Marketing</h3>
              <p>By email marketing is very prominent way to reach to your targeted audience.  We do email marketing with targeted area in which you have interest.</p>
            </span>
          </div>
        </div>
         <div class="col-md-6 animate-box" >
          <div class="feature-center" >
            <span class="icon">
              <i class="ti-search"></i>
            </span>
            <span class="counter">
              <h3 class="v_h3_title">Search Engine Optimization (SEO)</h3>
              <p>Search engine optimization (SEO) is best to remove milestones between your online visibility and your right audience. Our team work from small-small issues to real issues, so your website will get good google ranking.</p>
            </span>
          </div>
        </div>
         <div class="col-md-6 animate-box" >
          <div class="feature-center" >
            <span class="icon">
              <i class="ti-sharethis"></i>
            </span>
            <span class="counter">
              <h3 class="v_h3_title">Social Media Marketing</h3>
              <p>first step toward success is the understanding of your audience. Social medias are very helpful for gathering data about your audience and it give instance reach to your audience. Social media is one of the key factor to increase traffic on your website and so improve the search engine ranking. </p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="" class="v_container ">
    <h2 class="v_h2_title text-center"> Contact Us</h2>
    <hr class="section-dash-dark "/>
    <h3 class="v_h3_title text-center">For More Detail Contact Us</h3>
    <div class="container">
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
                @if('verify_email' == $error)
                  <li><a href="{{ url('verifyAccount')}}">Click here to resend verification email</a></li>
                @else
                  <li>{{ $error }}</li>
                @endif
              @endforeach
          </ul>
      </div>
    @endif
      <div class="row">
        <form class="form-horizontal" method="post" action="{{ url('sendContactUsMail')}}" enctype="multipart/form-data">
            <div class="v_contactus-area">
                <div class="well">
                    {{ csrf_field()}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required="required" />
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required="required" /></div>
                            </div>
                            <div class="form-group">
                                  <label for="subject">
                                      Mobile No.</label>
                                  <div class="input-group">
                                      <span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span>
                                      </span>
                                      <input type="phone" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required="true" /></div>
                              </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Message</label>
                                <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                                    placeholder="Message"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary pull-right" id="btnContactUs" title="Send Message">
                                Send Message</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
  </section>
@stop
@section('footer')
  @include('footer.footer')
@stop