@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/index_new.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/hover.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/themify-icons/themify-icons.css?ver=1.0')}}" rel="stylesheet"/>
  <!-- <link href="{{asset('css/animate.min.css?ver=1.0')}}" rel="stylesheet"/> -->
  <style>
.divide-nav{
  background-color:#151B54;
  padding-top: 5px;
}
.divide-text{
    color:#fff;
    line-height: 20px;
    font-size:10px;
    padding: 15px 0px;
}
.affix {
  top: 0px;
  width:100%;
}
.filler{
  min-height: 2000px;
}


#vchip-header {
  background: #4d4d4d;
/*top:-90px !important;*/
}
@media screen and (max-width: 1200px) {
  .divide-nav{display: none;}
}
#marquee-text span {
    margin-right: 10%;
    }
#marquee-text{
   margin: 0px;
   position: absolute;
   color: #fff;
    }
@media screen and (max-width: 1200px) {
.top-nav{display: none;}
}
@media screen and (max-width: 800px) {
.top-nav{display: none;}
}

  /*tree*/
  .tree-tital{
    font-weight: 800;
    color: #448eda;
    font-size: 23px;
  }
  .tree-menu {
    margin: 13% 0 25%;
    float: left;
    font-weight: bolder;
    width: 100%;
}
@media screen and (max-width: 991px){
.tree-menu{
    margin: 2% 0;
    line-height: 1;
}
}
@media screen and (max-width: 991px){
.tree-menu ,.tree-tital{
    text-align: center;
}
}
.top-right {
  float: left;
}

.top-right ul >li {
  display: inline-block;
  margin-left: 15px;
  text-transform: uppercase;
  height: 5px;

}
.top-right .btn{
border:1px solid #fff;
  padding: 0px 5px;
  font-weight: bolder;
}
.top-right a {
  font-size: 16px;
  color: #fff;
}

.top-right a:hover {
  color: #01b1d7;
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
/*customer*/
.tile
{
  width:100%;
  height:200px;
  margin:10px;
  background-color:#99aeff;
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
  @include('header.header_menu')
  @include('header.header_info')
<section class="v_bg_grey v_container " >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title flyLeft">ABOUT US</h2>
        <hr class="section-dash-dark"/>
      </div>
    </div>
    <ul class="nav nav-pills mrgn_30_top" >
      <li class="active"><a data-toggle="tab" href="#about1">About</a></li>
      <li><a data-toggle="tab" href="#vission">Vission</a></li>
      <li><a data-toggle="tab" href="#mission">Mission</a></li>
    </ul>
    <div class="tab-content" style="background-color: #01bafd; padding : 6px 15px;">
      <div id="about1" class="tab-pane fade in active">
        <h3 class="v_h3_title ">About</h3>
          <p class="more"> Vchip-edu is the part of Vchip Technology. Vchip Technology is IoT base young start-up company having head office in Pune. Vchip Technology is working in Education, Health and Agriculture sectores.
          <br/>
          We at <b>Vchip-edu</b> believes that better education is right of everyone. In our country (India), most of people live in villages. So Vchip-edu is working on Digital Education platform, so that we can provide a quality education equally in villages and remote areas along with urban area. In other word everyone can learn with fun from anywhere in the world at any time.
          <br/><br/>
          Currently, we are focusing on bridging a gap between industries and educational organizations along with digital villages. In that we are working on direct interaction of students with industries and our experts who belong to different industries. Also, interaction of students with Founder and CEO’s of successful start-ups. Also, our platform have all the require things which are needed for placement like online test (design according to pattern of the major companies), online course for aptitude preparation, discussion forum etc.
          <br/><br/>
          We are providing access of Vchip-edu platform to colleges at totally free of cost, so more and more students can get benefit of it. Student access to our platform is also totally free of cost. Also, any private/coaching institutes can start their classes digitally with in 15 minute on our platform. For  private/coaching institutes also our platform is free to use upto 20 logins.
          <br/>
              <b>" We always believe that better society is a best place to live and educated society is better than best. "</b>
          </p>
      </div>
      <div id="vission" class="tab-pane fade">
        <h3 class="v_h3_title">Vission</h3>
          <p>To be at leading and respectable place in the knowledge led creativity movement. World see toward our country as leading industry of Electronics and IT sector.</p>
      </div>
      <div id="mission" class="tab-pane fade">
        <h3 class="v_h3_title">Mission</h3>
        <p>We are working on digital education platform with the prior motive of bridging a gap between industry and educational organizations along with digital village. So that, we are collaborating with educational organization, institutes, colleges along with well establish industries and start-ups. </p>
      </div>
    </div>
  </div>
  <br />
</section>
<section id="vchip_solution" class="v_container" >
  <div class="container ">
    <div class="row mrgn_60_btm">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">OUR SERVICES</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title">Learn with fun...</h3>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-push-2" style="padding: 1% 0;">
        <figure >
          <img src="{{asset('images/solution/tree-digram.png')}}" class="img-responsive" alt="Tree Digram" style="width: 100%;"/>
        </figure>
      </div>
      <div class="col-md-2 col-md-pull-8 " style="padding: 4% 0;">
          <p class="tree-tital">COLLEGE/STUDENTS</p>
            <div class="tree-menu"><a href="#">Bridging a gap between industries & Students</a></div>
            <div class="tree-menu"><a href="#">Placement, Internship, Sponsor projects</a></div>
            <div class="tree-menu"><a href="#">Workshops on emerging Technology </a></div>
            <div class="tree-menu"><a href="#">Digital Education & ERP Management </a></div>
            <div class="tree-menu"><a href="#">Start-ups</a></div>


    </div>
      <div class="col-md-2 " style="padding: 4% 0;">
           <p class="tree-tital" >COACHING INSTITUTE</p>
            <div class="tree-menu"><a href="#">Digital Education Platform</a></div>
            <div class="tree-menu"><a href="#">ERP Management</a></div>
            <div class="tree-menu"><a href="#">Web and Mobile App Development</a></div>
            <div class="tree-menu"><a href="#">Digital Marketing</a></div>
            <div class="tree-menu"><a href="#">SEO</a></div>
      </div>
    </div>
  </div>
</section>
<section class="v_bg_grey" id="feature">
  <div class="container v_container">
    <div class="row mrgn_60_btm">
     <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top">
      <h2 class="v_h2_title">Digital Education</h2>
      <hr class="section-dash-dark"/>
      <h3 class="v_h3_title">Digital village is our dream...</h3>
     </div>
    </div>
    <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-list-alt" aria-hidden="true"></i>
      </div>
      <div class="text-center">
      <h3 class="v_h3_title">ONLINE COURSES</h3>
      <p>We provide online courses...</p>
      <p class="mrgn_20_top">
        <a href="{{ url('courses')}}" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-laptop" aria-hidden="true"></i>
      </div>
      <div class="text-center">
      <h3 class="v_h3_title">Online Test Series</h3>
      <p>We provide online Test Series...</p>
      <p class="mrgn_20_top">
        <a href="{{ url('online-tests') }}" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-wrench" aria-hidden="true"></i>
      </div>
      <div class="text-center">
      <h3 class="v_h3_title">Workshop</h3>
      <p>We provide online Workshop...</p>
      <p class="mrgn_20_top">
        <a href="workshop.html" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-joomla" aria-hidden="true"></i>

      </div>
      <div class="text-center">
      <h3 class="v_h3_title">Hobby Project</h3>
      <p>We provide hobby projects...</p>
      <p class="mrgn_20_top">
        <a href="{{ url('vkits') }}" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-folder-open" aria-hidden="true"></i>
      </div>
      <div class="text-center">
      <h3 class="v_h3_title">Vchip-Doc</h3>
      <p>We provide research paper...</p>
      <p class="mrgn_20_top">
        <a href="{{ url('documents') }}" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 slideanim ">
      <div class="v-icon text-center mb-1 left-reveal">
         <i class="fa fa-file-video-o" aria-hidden="true"></i>
      </div>
      <div class="text-center">
      <h3 class="v_h3_title">Live courses</h3>
      <p>We provide Live courses...</p>
      <p class="mrgn_20_top">
        <a href="{{ url('liveCourse')}}" class="btn-link">Learn More
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </p>
      </div>
    </div>
    </div>
  </div>
</section>
<section id="event" class="v_container " >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center ">
        <h2 class="v_h2_title">EVENTS</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title">New ideas...Successful adventure.</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 col-sm-4 animate-box slideanim" >
        <div class="feature-center" >
          <span class="icon">
            <i class="ti-ruler-alt-2 icon-sm"></i>
          </span>
          <span class="counter">
            <h3 class="v_h3_title">ELECTROfreaks</h3>
            <p>Technical event conducted by Vchip Technology in Amravati (India) in the month of February of every years.</p>
          </span>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 animate-box" >
        <div class="feature-center" >
          <span class="icon">
            <i class="ti-panel icon-sm"></i>
          </span>
          <span class="counter">
           <h3 class="v_h3_title">Vchip-Tech</h3>
            <p>Technical event conducted by Vchip Technology in Pune (India) in the month of August of every years.</p>
          </span>
        </div>
      </div>
      <div class="col-md-4 col-sm-4 animate-box slideanim" >
        <div class="feature-center">
          <span class="icon">
           <i class="fa fa-trophy" aria-hidden="true"></i>
          </span>
          <span class="counter">
           <h3 class="v_h3_title">Vchip-Debu</h3>
            <p>Its the competition for debut in new technology. Winner get internship, funding and work space from Vchip Technology.</p>
          </span>
        </div>
      </div>
    </div>
  </div>
</section>
@stop
@section('footer')
  @include('footer.footer')
<script>
  $(document).ready(function() {
    var showChar = 500;
    var ellipsestext = "...";
    var moretext = "<br /> Read More";
    var lesstext = "<br /> less";
    $('.more').each(function() {
      var content1 = $(this).html();

      if(content1.length > showChar) {

        var c = content1.substr(0, showChar);
        var h = content1.substr(showChar, content1.length - showChar);

        var html = c + '<span class="moreelipses">'+ellipsestext+'</span><span class="morecontent"><span>' + h + '</span><a href="" class="morelink" style="color:#e91e63; font-weight:bolder;">'+moretext+'</a></span>';

        $(this).html(html);
      }

    });

    $(".morelink").click(function(){
      if($(this).hasClass("less")) {
        $(this).removeClass("less");
        $(this).html(moretext);
      } else {
        $(this).addClass("less");
        $(this).html(lesstext);
      }
    $(this).parent().prev().toggle();    //toggle the containt of ellipsestext ie. ...
    $(this).prev().toggle();            //toggle the containt of h
    return false;
  });
  });


</script>

@stop