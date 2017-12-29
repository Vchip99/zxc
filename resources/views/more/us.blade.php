@extends('layouts.master')
@section('header-title')
  <title>About Us | Vchip-edu</title>
@stop
@section('header-css')
    @include('layouts.home-css')
    <style type="text/css">
    /*customer*/
.tile
{
  width:100%;
  height:200px;
  margin:10px;
  background-color:#fff;
  display:inline-block;
  background-size:cover;
  position:relative;
  cursor:pointer;
  transition: all 0.4s ease-out;
  box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.44);
  overflow:hidden;
  color:white;
  font-family:'Roboto';

}
.tile img
{
  height:100%;
  width:100%;
  position:absolute;
  top:0;
  left:0;
  z-index:0;
  transition: all 0.4s ease-out;
}
.tile .text
{
   z-index:99;
  position:absolute;
  /*padding:30px;*/
  height:calc(100% - 60px);
}
.tile h1
{
  font-weight:300;
  margin:0;
  text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
  color:#e91e63;
  background-color: rgba(255,255,255,.35);
  padding:0px;
  font-size: 15px;
  top: 0px !important;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);
}
.tile h2
{
  font-weight:100;
  margin:20px 0 0 0;
  font-style:italic;
   transform: translateX(200px);
}
.tile p
{
  font-weight:300;
  margin:20px 0 0 0;
  line-height: 25px;
/*   opacity:0; */
  transform: translateX(-200px);
  transition-delay: 0.2s;
}
.animate-text
{
  padding: 50px 0px 50px 45px;
  opacity:0;
  transition: all 0.6s ease-in-out;
}
.animate-text>a
{
font-weight: bolder;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);

}
.tile:hover
{
/*   background-color:#99aeff; */
box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.64);
  transform:scale(1.05);
}
.tile:hover img
{
  opacity: 0.2;
}
.tile:hover .animate-text
{
  transform:translateX(0);
  opacity:1;
}
@media (min-width: 500px) and (max-width: 764px){
.tile {
     width: 60%;
     margin-left: 20%;
  }
}
@media screen and (max-width: 388px){
   .tile {
     width: 100%;
  }
}
/*team section*/

  /*team */
  .team{
    padding-bottom: 60px;
    width: 100%;
  }
  .team .border{
    border: 2px solid #000;
    background: #fff;
  }
  .team img{
    width: 100% !important;
    height: auto;
    height: 200px !important;
  }
  .single-member{
    margin: 0;
    padding: 0;
  }
  @media (min-width: 535px) and (max-width: 768px) {
    .single-member{
      width: 60%;
      margin: 0px auto;
      height: auto;
    }
  }
/*NEW*/
@media screen and (max-width: 534px){
.single-member {
    width: 90%;
    margin: 0px auto;
  }
}/*
@media screen and (max-width: 388px){
   .single-member {
     width: 100%;
  }
}*/

  }
    @media screen and (max-width: 592px) {

    .team {
    width: 100% !important;
  }
   .team img{
    height: 240px !important;
  }
  }
  @media screen and (max-width: 400px) {

     .team img{
    height: 200px !important;
  }
  }
  .person-detail{
    text-align: center;
    padding:10px;
    background: #3a9cc8;
    position: relative;
    transition: all .7s ease 0s;
    -webkit-transition: all .7s ease 0s;
    -moz-transition: all .7s ease 0s;
    -o-transition: all .7s ease 0s;
    -ms-transition: all .7s ease 0s;
  }

  .arrow-top{
    position: absolute;
    width: 20px;
    height: 20px;
    background: #3ba0cc;
    transform:rotate(45deg);
    -webkit-transform:rotate(45deg);
    -moz-transform:rotate(45deg);
    -o-transform:rotate(45deg);
    -ms-transform:rotate(45deg);
    bottom: -10px;
    left: 46%;
    transition: all .7s ease 0s;
    -webkit-transition: all .7s ease 0s;
    -moz-transition: all .7s ease 0s;
    -o-transition: all .7s ease 0s;
    -ms-transition: all .7s ease 0s;
  }
  .arrow-bottom{
    position: absolute;
    width: 20px;
    height: 20px;
    background: #3ba0cc;
    transform:rotate(45deg);
    -webkit-transform:rotate(45deg);
    -moz-transform:rotate(45deg);
    -o-transform:rotate(45deg);
    -ms-transform:rotate(45deg);
    top: -9px;
    left: 46%;
    transition: all .7s ease 0s;
    -webkit-transition: all .7s ease 0s;
    -moz-transition: all .7s ease 0s;
    -o-transition: all .7s ease 0s;
    -ms-transition: all .7s ease 0s;
  }
  .person-detail h3{
    font-size: 20px;
    color: #fff;
  }
  .person-detail p{
    font-size: 13px;
    color: #fff;
    font-family: 'Open Sans', sans-serif;
  }
  .single-member:hover .person-detail{
    background: #2a2a2a;
  }
  .single-member:hover .arrow-top{
    background: #2a2a2a;
  }
  .single-member:hover .arrow-bottom{
    background: #2a2a2a;
  }
   /* testimonial */
 .section {
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
}
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
  </style>
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
                <img src="{{ asset('images/about-us.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="about us" />
            </figure>
        </div>
        <div class="vchip-background-content">
        </div>
    </div>
</section>
<section id="" class="v_container v_bg_grey">
   <h2 class="v_h2_title text-center"> About us</h2>
   <hr class="section-dash-dark"/>
    <div class="container">

          <div class="v_contactus-area">
             <p class="more"> Vchip-edu is the part of Vchip Technology. Vchip Technology is IoT base young start-up company having head office in Pune. Vchip Technology is working in Education, Health and Agriculture sectores.
          <br/>
          Vchip-edu believes that better education is right of everyone. In our country (India), most of people live in villages. So Vchip-edu is working on Digital Education platform, so that we can provide a quality education equally in villages and remote areas along with urban area. In other word everyone can learn with fun from anywhere in the world at any time.
          <br/><br/>
          Currently, we are focusing on bridging a gap between industries and educational organizations along with digital villages. In that we are working on direct interaction of students with industries and our experts who belong to different industries. Also, interaction of students with Founder and CEO’s of successful start-ups. Also, our platform have all the require things which are needed for placement like online test (design according to pattern of the major companies), online course for aptitude preparation, discussion forum etc.
          <br/><br/>
          We are providing access of Vchip-edu platform to colleges at totally free of cost, so more and more students can get benefit of it. Student access to our platform is also totally free of cost. Also, any private/coaching institutes can start their classes digitally with in 15 minute on our platform. For  private/coaching institutes also our platform is free to use upto 20 logins.
          <br/>
           <p class="text-center">   <b >" We always believe that better society is a best place to live and educated society is better than best. "</b></p>
          </p>
      </div>
    </div>
</section>
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
                        <p>Thanks to Vchip-edu, i have able to start my online classes on Vchip-edu platform within a day and now growth  of my institute is nearly double.</p>
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
                        <p>One of the great digital education platform. It is help me for preparation of placement.</p>
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
</section>
<section class="v_container ">
    <div class="container" >
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center ">
              <h2 class="v_h2_title">OUR CUSTOMERS</h2>
              <hr class="section-dash-dark"/>
              <h3 class="v_h3_title mrgn_30_btm">Happy customers...Successful adventure.</h3>
            </div>
            <div class="row our_customer">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
                  <div class="tile">
                        <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"/>
                        <div class="text">
                          <h1 title="SSGMCE">SSGMCE</h1>
                          <p class="animate-text">
                            <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank" class="btn-link">Learn More
                               <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                          </p>
                        </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
                  <div class="tile">
                        <img src="{{ asset('images/logo/pote.jpg') }}" alt="P.R. Pote College of Engineering & Management"/>
                        <div class="text">
                          <h1 title="P.R. Pote College of Engineering & Management">P.R. Pote College of Engineering & Management</h1>
                          <p class="animate-text">
                            <a href="http://www.prpcem.org/" target="_blank" class="btn-link">Learn More
                              <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                          </p>
                        </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
                  <div class="tile">
                        <img src="{{ asset('images/logo/hvpm.jpg') }}" alt="HVPM College of Engineer And Technology" />
                        <div class="text">
                          <h1 title="HVPM College of Engineer And Technology">HVPM College of Engineer And Technology</h1>
                          <p class="animate-text">
                            <a href="http://hvpmcoet.in/" target="_blank" class="btn-link">Learn More
                              <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                          </p>
                        </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 slideanim">
                  <div class="tile">
                        <img src="{{ asset('images/logo/ghrcema_logo.png') }}" alt="G H RISONI" />
                        <div class="text">
                          <h1 title="G H RISONI">G H RISONI</h1>
                          <p class="animate-text">
                            <a href="http://ghrcema.raisoni.net/" target="_blank" class="btn-link">Learn More
                              <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </a>
                          </p>
                        </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="team" class="team v_container v_bg_grey" >
 <div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2 text-center ">
      <h2 class="v_h2_title">OUR TEAM</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">Motive of bridging between educational organization  and Industry.</h3>
    </div>
  </div>
  <div class="row mrgn_20_top">
    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{ asset('images/team/vishesh.jpg') }}" alt="member-1" />
      </div>
      <div class="person-detail  slideanim">
        <div class="arrow-bottom "></div>
        <p><strong>Mr. Vishesh Agrawal</strong></p>
        <p>CEO</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
    </div>


    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail ">
        <div class="arrow-top"></div>
        <p><strong>Mr. Ajay Jangra</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{ asset('images/team/ajay.jpg') }}" alt="member-2" />
      </div>
    </div>

    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{ asset('images/team/vishal_kumar.jpg') }}" alt="member-3" />
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vishal Kumar</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: IIT Kharagpur </p>
      </div>
    </div>

    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Vishal Parvani</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: Bits Pilani </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{ asset('images/team/vishal_parvani.jpg') }}" alt="member-4" />
      </div>
    </div>

    <div class="col-md-2 single-member col-sm-4">
      <div class="person">
        <img class="img-responsive" src="{{ asset('images/team/vartul.jpg') }}" alt="member-5" />
      </div>
      <div class="person-detail slideanim">
        <div class="arrow-bottom"></div>
        <p><strong>Mr. Vartul Sharma</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: COEP Pune </p>
      </div>
    </div>
    <div class="col-md-2 single-member col-sm-4">
      <div class="person-detail">
        <div class="arrow-top"></div>
        <p><strong>Mr. Aditya Jagtap</strong></p>
        <p>Adjunct Professor</p>
        <p>M-Tech: VIT Vellore </p>
      </div>
      <div class="person slideanim">
        <img class="img-responsive" src="{{ asset('images/team/aditya.jpg') }}" alt="member-6" />
      </div>
    </div>
  </div>
 </div>
</section>
@stop
@section('footer')
    @include('footer.footer')
@stop