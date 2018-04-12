@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu - Digital Education, Online Courses & eLearning |Vchip Technology</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{asset('css/index_new.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/hover.css?ver=1.0')}}" rel="stylesheet"/>
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
      height: 784px !important;
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
    .footer{
      border-top: 0px !important;
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
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')

@stop
@section('content')
  @include('header.header_menu')
  @include('header.header_info')
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
          <p class="tree-tital"><a href="{{ url('erp')}}">COLLEGE/STUDENTS</a></p>
            <div class="tree-menu">Bridging a gap between industries & Students</div>
            <div class="tree-menu">Placement, Internship, Sponsor projects</div>
            <div class="tree-menu">Workshops on emerging Technology </div>
            <div class="tree-menu">Digital Education & ERP Management </div>
            <div class="tree-menu">Start-ups</div>
      </div>
      @php
        if('local' == \Config::get('app.env')){
          $onlineUrl = 'https://online.localvchip.com/';
        } else {
          $onlineUrl = 'https://online.vchipedu.com/';
        }
      @endphp
      <div class="col-md-2 " style="padding: 4% 0;">
           <p class="tree-tital" ><a href="{{$onlineUrl}}">COACHING INSTITUTE</a></p>
            <div class="tree-menu">Digital Education Platform</div>
            <div class="tree-menu">ERP Management</div>
            <div class="tree-menu">Web and Mobile App Development</div>
            <div class="tree-menu">Digital Marketing</div>
            <div class="tree-menu">SEO</div>
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