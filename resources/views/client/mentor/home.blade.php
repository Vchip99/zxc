@extends('client.mentor.master')
@section('title')
  <title>MENTOR - HOME</title>
@stop
@section('header-css')
  <style type="text/css">
      /* testimonial */
       /*.section {
        background: url('{{ url("images/testimonial.png") }}') no-repeat 50%;
        background-size: cover;
        background-attachment: fixed;
        padding: 120px 0;
        color: #fff;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
        position: relative;
      }
       .section:before{
        content: '';
        z-index: 9;
        background: rgba(255, 255, 255, 0.78);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
      }*/
       .block {
        position: relative;
        z-index: 999;
      }
      .section .v_h3_title{color: #000;}
      #quote-carousel
        {
          padding: 0 10px 30px 10px;
          margin-top: 30px;
          color: #000;
          z-index: 999;
        }
        #quote-carousel p, h6, #testimonial h2 {
          color: #000;
        }
       #quote-carousel h6{
        font-weight: bolder;
        color: #00BFFF  ;
        margin-left: 10px;
        font-size: 16px;
      }
        /* Control buttons  */
        #quote-carousel .carousel-control
        {
          background: none;
          color: #000;
          text-shadow: none;
          margin-top: 30px;
          height: auto;
        }
        /* Previous button  */
        #quote-carousel .carousel-control.left
        {
          display: none;
        }
        /* Next button  */
        #quote-carousel .carousel-control.right
        {
          display: none;
        }
        /* Changes the position of the indicators */
        #quote-carousel .carousel-indicators
        {
          right: 50%;
          top: auto;
          bottom: 0px;
          margin-right: -19px;
        }
        /* Changes the color of the indicators */
        #quote-carousel .carousel-indicators li
        {
          background: #01bafd;
          border-radius: 0px;
        }
        #quote-carousel .carousel-indicators .active
        {
          background: #31708f;
          border: none;
        }
        #quote-carousel img
        {
          width: 300px;
          height: 150px
          z-index: 999;

        }
        .item blockquote {
        border-left: none;
        margin: 0;
        }
        .item blockquote img {
        margin-bottom: 10px;
        }
        .item blockquote p{
        background-color: #fff;
        padding: 10px;
        color: #747474 !important;
        }
        .item blockquote p:before {
        content: "\f10d";
        font-family: 'Fontawesome';
        float: left;
        margin-right: 10px;
        }
        .img-circle{  border:2px solid #01bafd;
        }
        @media (min-width: 768px) {
          #quote-carousel
          {
          margin-bottom: 0;
          padding: 0 40px 30px 40px;
          }
        }
        @media (max-width: 768px) {
          #quote-carousel .carousel-indicators {
          bottom: -20px !important;
          }
          #quote-carousel .carousel-indicators li {
          display: inline-block;
          margin: 0px 5px;
          }
          #quote-carousel .carousel-indicators li.active {
          margin: 0px 5px;

          }
        }
        @media screen and (max-width: 1200px) {
          .person-detail{
          border-bottom: 1px solid pink; }
        }
          .person-detail{
            padding:10px;
            background: #3a9cc8;
            position: relative;
            transition: all .7s ease 0s;
            -webkit-transition: all .7s ease 0s;
            -moz-transition: all .7s ease 0s;
            -o-transition: all .7s ease 0s;
            -ms-transition: all .7s ease 0s;
          }

          /**/
        .v-icon{
              width:150px;
            margin: 0 auto;
            height: 150px;
            border-radius: 50%;
            background-color: #fff;
            box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);
        }
        .v-icon i {
            position: relative;
            top: 50%;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            font-size: 60px;
            color:#e91e63;
        }

        /*event*/
          .feature-center {
            padding: 20px;
            float: left;
            width: auto;
            position: relative;
            bottom: 0;
            margin-bottom: 40px;
            -webkit-transition: 0.3s;
            -o-transition: 0.3s;
            transition: 0.3s;
            text-align: center;
          }
          .feature-center .desc {
            padding-left: 180px;
          }
          .feature-center .icon {
            margin-bottom: 20px;
            display: block;
          }
          .feature-center .icon i {
            font-size: 40px;
            color: #e91e63;
          }
          .feature-center .icon2 {
            float: left;
          }
          .feature-center .icon2 i {
            font-size: 100px;
          }
          .feature-center p:last-child {
            margin-bottom: 0;
          }
          .feature-center p, .feature-center h3 {
            margin-bottom: 30px;
          }
          .feature-center h3 {
            font-size: 22px;
            color: #5d5d5d;
          }
          .feature-center:hover, .feature-center:focus {
            background: #01bafd;
            bottom: 10px;
            -webkit-box-shadow: 0px 18px 71px -10px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 0px 18px 71px -10px rgba(0, 0, 0, 0.75);
            box-shadow: 0px 18px 71px -10px rgba(0, 0, 0, 0.75);
          }
          .feature-center:hover p:last-child, .feature-center:focus p:last-child {
            margin-bottom: 0;
          }
          .feature-center:hover .icon i, .feature-center:focus .icon i {
            color: rgba(255, 255, 255, 0.7);
          }
          .feature-center:hover p, .feature-center:hover h3, .feature-center:focus p, .feature-center:focus h3 {
            color: #fff !important;
          }
          .feature-center:hover a, .feature-center:focus a {
            color: rgba(255, 255, 255, 0.7);
          }
          .feature-center:hover a:hover, .feature-center:focus a:hover {
            color: #fff;
          }
          @media screen and (max-width: 768px) {
            .feature-center:hover, .feature-center:focus {
              bottom: 0;
            }
          }
          .feature-center:hover .counter {
            color: #fff;
          }
  </style>
@stop
@section('content')
    @include('client.mentor.header_menu')
    <section style="height: 300px; width: 100%; background: yellow;">
      <div class="container" style="text-align: center;">
        <div style="font-size: 30px; padding-top: 100px;"><b>MENTOR</b></div>
        <p >Connect with experts to become a experts.  </p><br>
        <a class="btn btn-primary" href="{{ url('mentor/login')}}">Mentor Login</a>
        <a class="btn btn-primary" href="{{ url('mentorSignup')}}">Mentor Sign-up</a>
      </div>
    </section>
    <section class="container">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      @if(Session::has('message'))
        <div class="alert alert-success" id="message">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        </div>
      @endif
      <h1 style="text-align: center;">About</h1>
      <p style="text-align: justify;">As an entrepreneur, it's exciting to go it alone and create something on your own. However, the reality is that, while you have a great idea, you may not know exactly what you should be doing with your business at which times to develop it into a sustainable business. I've had several mentors over the years and learned a large amount of valuable lessons from each and every one of them. From not making certain business decisions to fostering certain partnerships, a mentor can help guide you through your entrepreneurial journey.</p>
    </section><hr>
    <section class="container">
      <div class="row">
        <div class="col-sm-6">
          <h2>How mentoring platform work</h2>
          <ul style="text-align: justify;">
            <li>Sign-up and Sign-in (It's Free)</li>
            <li>Go to Mentor page and select the area and skill in which you need a mentor</li>
            <li>Select interested mentors and go to their profile</li>
            <li>Massage your query to Mentors.</li>
            <li>Mentor will respond accordingly. Also he will provide detail about available time, how to pay money etc.</li>
            <li>Last but not least, please review to mentor accordingly to your experience with their positive and negative (if any) points, so it will be helpful for other mentees. </li>
          </ul>
        </div>
        <div class="col-sm-6">
          <iframe width="100%" height="315" src="https://www.youtube.com/embed/nYQairlPfbA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </section><hr>
    <section class="container">
      <h1 style="text-align: center;">Popular Mentors</h1>
      <div class="scrolling-wrapper">
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
        <div class="card">
          <a href="{{ url('mentorinfo')}}">
            <div class="panel">
              <div>
                <img src="{{url('images/mark.jpg')}}" alt="member" class="" />
              </div>
              <p><b>Name: Mark Zukarbarg</b></p>
              <p><strong>Reviews: </strong></p>
            </div>
          </a>
        </div>
      </div><br>
      <div style="text-align: center;">
        <a href="{{ url('mentors') }}" class="btn btn-primary">More Mentors</a>
      </div>
    </section><hr>
<!--     <section>
      <h1 style="text-align: center;">Our Testimonial</h1>
    </section><hr> -->
    <section class=" section" >
      <div class="container block" >
        <div class="row">
          <div class="col-md-8 col-md-offset-2 text-center ">
            <h2 class="v_h2_title">TESTIMONIALS</h2>
            <hr class="section-dash-dark"/>
            <h3 class="v_h3_title ">Happy customers...Successful adventure.</h3>
          </div>
        </div>
        <div class="row">
              <div class="col-md-12">
                <div class="carousel slide" data-ride="carousel" id="quote-carousel">
                  <!-- Bottom Carousel Indicators -->
                  <ol class="carousel-indicators">
                    <li data-target="#quote-carousel" data-slide-to="0" class="active"></li>
                    <li data-target="#quote-carousel" data-slide-to="1"></li>
                    <li data-target="#quote-carousel" data-slide-to="2"></li>
                  </ol>

                  <!-- Carousel Slides / Quotes -->
                  <div class="carousel-inner">

                    <!-- Quote 1 -->
                    <div class="item active">
                      <blockquote>
                        <div class="row">
                          <div class="col-sm-3 text-center">
                            <img class="img-circle" src="{{ asset('images/testimonial/testimonial-1.jpg') }}" style="width: 100px;height:100px;" alt="Testimonial" />
                          </div>
                          <div class="col-sm-9">
                            <p>Thanks to Vchip-edu, i was able to start my online classes on Vchip-edu platform within a day and now growth  of my institute is nearly double.</p>
                            <h6>Ajay Joshi</h6>
                          </div>
                        </div>
                      </blockquote>
                    </div>
                    <!-- Quote 2 -->
                    <div class="item">
                      <blockquote>
                        <div class="row">
                          <div class="col-sm-3 text-center">
                            <img class="img-circle" src="{{ asset('images/testimonial/testimonial-2.jpg') }}" style="width: 100px;height:100px;">
                          </div>
                          <div class="col-sm-9">
                            <p>Vchip-edu platform help to my student at the time of preparation of placement</p>
                            <h6>Vishal Langote</h6>
                          </div>
                        </div>
                      </blockquote>
                    </div>
                    <!-- Quote 3 -->
                    <div class="item">
                      <blockquote>
                        <div class="row">
                          <div class="col-sm-3 text-center">
                            <img class="img-circle" src="{{ asset('images/testimonial/testimonial-3.jpg') }}" style="width: 100px;height:100px;">
                          </div>
                          <div class="col-sm-9">
                            <p>One of the great digital education platform. It helped me for preparation of placement.</p>
                            <h6>Rashmi Raut</h6>
                          </div>
                        </div>
                      </blockquote>
                    </div>
                  </div>

                  <!-- Carousel Buttons Next/Prev -->
                  <a data-slide="prev" href="#quote-carousel" class="left carousel-control"><i class="fa fa-chevron-left"></i></a>
                  <a data-slide="next" href="#quote-carousel" class="right carousel-control"><i class="fa fa-chevron-right"></i></a>
                </div>
              </div>
        </div>
      </div>
    </section><hr>
@stop
@section('footer')
    @include('client.mentor.footer')
@stop
