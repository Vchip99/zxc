@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
<style type="text/css">
#vchip-header {
  background: #4d4d4d;
}
@media screen and (max-width: 992px) {
  #vchip-header.vchip-cover {
    height: inherit !important;
    padding: 48px 0 !important;
  }
}
@media screen and (max-width: 480px) {
  #vchip-header .text-left {
    text-align: center !important;
  }

}
@media screen and (max-width: 480px) {
  #vchip-header .btn {
    display: block;
    width: 100%;
  }
}
.header-text  {
  margin-top: 112px
  margin-bottom: 48px;
}
@media screen and (max-width: 768px) {
  .header-text {
    margin-top: 0;
    text-align: center;
  }
  .header-text  {
    margin-top: 0;
    text-align: center;
  }
}
.header-text h1 {
  margin-bottom: 0px;
  font-size: 80px;
  font-weight: 300;
  color: #fff;
  font-family: "Kaushan Script", cursive !important;
}
@media screen and (max-width: 768px) {
  .header-text h1 {
    font-size: 34px;
    line-height: 1.2;
    margin-bottom: 10px;
  }
}
#vchip-header
{
  background-size: cover;
  background-position: top center;
  background-repeat: no-repeat;
  position: relative;
}
.vchip-cover {
  height: 900px;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  position: relative;
  float: left;
  width: 100%;
}
.vchip-cover a {
  color: #01bafd;
  text-decoration:none;
}
.vchip-cover a:hover {
  color: white;
}
.vchip-cover .overlay {
  z-index: 1;
  position: absolute;
  bottom: 0;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.5);
}
.vchip-cover > .vchip-container {
  position: relative;
  z-index: 10;
}
@media screen and (max-width: 768px) {
  .vchip-cover {
    height: 600px;
  }
}
.vchip-cover p {
  color: rgba(255, 255, 255, 0.9);
  font-size: 20px !important;
  font-weight: 300;
}
.vchip-cover > .vchip-container {
  position: relative;
  z-index: 10;
}
.vchip-container {
  max-width: 1100px;
  position: relative;
  margin: 0 auto;
  padding-left: 15px;
  padding-right: 15px;
  margin-top: 300px;  }
  @media screen and (max-width: 768px) {
    .vchip-container {margin-top: 0px;}
  }
  .video-form{
    padding:30px 40px;
    background: rgba(0, 0, 0, 0.5);
    margin-left: 30px;
    margin-right: 30px;
  }
  .form-group{
    border-radius: 0px;
  }
  .reg-btn{
    line-height: 30px;
    width: 100%;
  }
select {
  background: transparent;
  border: medium none;
  color: #000;
  padding: 5px;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
  width: 70%; /* set width as per you needed */
}
select option:hover,
select option:focus,
select option:active,
select option:checked
{
    background: linear-gradient(#01bafd,#01bafd);
    background-color:#01bafd !important; /* for IE */
}
option:not(:checked) {
  background-color: #fff;
}
@media screen and (max-width: 768px) {

}
.vid {position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden; }
.vid iframe, .vid object,.vid embed {position: absolute; top: 0; left: 0; width: 100%; height: 100%;}
.video-mobile-headline{display: none; margin:0px;}
@media only screen and (max-device-width: 800px), only screen and (device-width: 1024px) and (device-height: 600px), only screen and (width: 1280px) and (orientation: landscape), only screen and (device-width: 800px), only screen and (max-width: 767px) {
  .video-container{ margin-bottom: 25px;}
  .flex-video { padding-top: 0;}
}

.img-course-box {
  width: 100%;
  height:150px;
  border-top-left-radius:2px;
  border-top-right-radius:2px;
  display:block;
  overflow: hidden;
  border-bottom: 2px dotted #ddd;
}
.img-course-box img{
  width: 100%;
  height: 100%;
  transition: all .25s ease;
}
/*.course-box {
  display: block;
  margin-bottom: 20px;
  line-height: 1.42857143;
  background-color: #fff;
  border-radius: 2px;
  position: relative;
  box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
  transition: box-shadow .25s;
  border-top: 18px solid #007ba7;

}
.course-box:hover {
  box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
}
.course-box-content{padding: 15px;}

.course-box-title {
  text-align: center !important;
  width: 100% !important;
  font-weight: bolder !important;
}*/
.course-box-content p{color: grey;}
.add-view {
    border-top: 1px solid #D4D4D4;
    text-align:center;
    background-color: #eee;
  }
  .add-view a {
    text-decoration: none !important;
    padding:10px;
    text-transform: uppercase;
    cursor: pointer;
  }
@media (min-width: 995px) and (max-width: 1600px) {
  .hidden-div {
    display: none !important;
  }
}
 .hidden-div {
 right: 0%;
    width: 100%;
  }
@media (max-width: 995px) {
  .hidden-div1 {
    display: none !important;
  }
}
@media (min-width: 768px) and (max-width: 995px) {

  .col-sm-pull-9 {
    right: 0%;
    width: 100%;
  }
    .col-sm-push-3 {
    left:  0%;
    width: 100%;
  }
   .add-1, .add-2{width:50%;
    float: left;
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;}
}
@media (min-width: 568px) and (max-width: 766px) {
  .add-1, .add-2{width:50%;
    float: left;
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;}
}
.block-with-text {
    display: inline-block;
    width: 180px;
    white-space: nowrap;
    overflow: hidden !important;
    text-overflow: ellipsis;
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
          <img src="{{asset('images/zero-to-hero-01.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
<!-- Start course section -->
<section id="sidemenuindex" class="v_container">
  <div class="container ">
    <div class="row">
      <div class="col-sm-3 hidden-div">
        <h4 class="v_h4_subtitle"> Sorted By</h4>
        <div class="mrgn_20_top_btm" >
          <select class="form-control" id="designation" name="designation" required title="Designation"  onChange="selectArea();">
            <option value="">Select Designation</option>
            @if(count($designations) > 0)
              @foreach($designations as $designation)
                <option value="{{$designation->id}}">{{$designation->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="dropdown mrgn_20_top_btm">
            <select class="form-control" id="area" name="area" required title="Area" onChange="selectHeros()">
              <option value="">Select Area</option>
            </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others"> Others</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="latest" onclick="searchCourse();">Letest</label>
          </div>
        </div>
      </div>
      <div class="col-sm-9 col-sm-push-3 data">
        <div class="row info" id="addHeros">
          @if(count($heros) > 0)
            @foreach($heros as $hero)
              <div class="col-md-4 col-sm-6  " title="{{$hero->name}}">
              <div class="thumbnail" >
                <div class="vid">
                  {!! $hero->url !!}
                </div>
              @if($id == $hero->id)
                <b  style="align-content: center;" class="block-with-text">  {{$hero->name}} <span style="color: red;">[new]</span></b>
              @else
               <b  style="align-content: center;" class="block-with-text">  {{$hero->name}} </b>
              @endif
              </div>
              </div>
            @endforeach
          @else
            No heros are available.
          @endif
        </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
          <h4 class="v_h4_subtitle"> Sorted By</h4>
          <div class="mrgn_20_top_btm" >
            <select class="form-control" id="designation1" name="designation" required title="Designation"  onChange="selectAreaNew();">
              <option value="">Select Designation</option>
              @if(count($designations) > 0)
                @foreach($designations as $designation)
                  <option value="{{$designation->id}}">{{$designation->name}}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="dropdown mrgn_20_top_btm">
              <select class="form-control" id="area1" name="area" required title="Area" onChange="selectHerosNew()">
                <option value="">Select Area</option>
              </select>
          </div>
          <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
          <div class="panel"></div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others"> Others</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="latest" onclick="searchCourse();">Letest</label>
            </div>
          </div>
        </div>
        <div class="advertisement-area" style="padding-right: 5px;">
          <span class="pull-right create-add"><a href="{{ url('createAd') }}"> Create Ad</a></span>
        </div>
        <br/>
        @if(count($ads) > 0)
          @foreach($ads as $ad)
            <div class="add-1">
              <div class="course-box">
                <a class="img-course-box" href="{{ $ad->website_url }}" target="_blank">
                  <img src="{{asset($ad->logo)}}" alt="{{ $ad->company }}"  class="img-responsive" />
                </a>
                <div class="course-box-content">
                  <h4 class="course-box-title" title="{{ $ad->company }}" data-toggle="tooltip" data-placement="bottom">
                    <a href="{{ $ad->website_url }}" target="_blank">{{ $ad->company }}</a>
                  </h4>
                  <p class="more"> {{ $ad->tag_line }}</p>
                </div>
              </div>
            </div>
          @endforeach
        @endif
        @if(count($ads) < 3)
          @for($i = count($ads)+1; $i <=3; $i++)
            @if(1 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="SSGMCE"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="SSGMCE" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ssgmce.org/Default.aspx?ReturnUrl=%2f" target="_blank">SSGMCE</a>
                    </h4>
                    <p class="more"> SSGMCE</p>
                  </div>
                </div>
              </div>
            @elseif(2 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://ghrcema.raisoni.net/" target="_blank">
                    <img src="{{ asset('images/logo/ghrcema_logo.png') }}" alt="G H RISONI"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="G H RISONI" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://ghrcema.raisoni.net/" target="_blank">G H RISONI</a>
                    </h4>
                    <p class="more"> G H RISONI</p>
                  </div>
                </div>
              </div>
            @elseif(3 == $i)
              <div class="add-1">
                <div class="course-box">
                  <a class="img-course-box" href="http://hvpmcoet.in/" target="_blank">
                    <img src="{{ asset('images/logo/hvpm.jpg') }}" alt="HVPM"  class="img-responsive" />
                  </a>
                  <div class="course-box-content">
                    <h4 class="course-box-title" title="HVPM" data-toggle="tooltip" data-placement="bottom">
                      <a href="http://hvpmcoet.in/" target="_blank">HVPM College of Engineer And Technology</a>
                    </h4>
                    <p class="more"> HVPM College of Engineer And Technology</p>
                  </div>
                </div>
              </div>
            @endif
          @endfor
        @endif
      </div>
    </div>
  </div>
</section>

@stop
@section('footer')
	@include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script type="text/javascript">
  function selectArea(){
    var designationId = parseInt(document.getElementById('designation').value);
    if( 0 < designationId ){
      $.ajax({
          method: "POST",
          url: "{{url('getAreasByDesignation')}}",
          data: {designation_id:designationId}
      })
      .done(function( msg ) {
        select = document.getElementById('area');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select Area';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }

  function selectAreaNew(){
    var designationId = parseInt(document.getElementById('designation1').value);
    if( 0 < designationId ){
      $.ajax({
          method: "POST",
          url: "{{url('getAreasByDesignation')}}",
          data: {designation_id:designationId}
      })
      .done(function( msg ) {
        select = document.getElementById('area1');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
        opt.innerHTML = 'Select Area';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }

  function selectHeros(){
    var designationId = parseInt(document.getElementById('designation').value);
    var areaId = parseInt(document.getElementById('area').value);
    if( 0 < designationId ){
      $.ajax({
          method: "POST",
          url: "{{url('getHeroByDesignationByArea')}}",
          data: {designation_id:designationId, area_id:areaId}
      })
      .done(function( msg ) {
        divHeros = document.getElementById('addHeros');
        divHeros.innerHTML = '';
        if(undefined !== msg && 0 < msg.length) {
          $.each(msg, function(idx, obj) {
              var firstDiv = document.createElement('div');
              firstDiv.className = 'col-md-4 col-sm-6 video-container';

              var secondDiv = document.createElement('div');
              secondDiv.className = 'vid';
              secondDiv.innerHTML = obj.url;
              firstDiv.appendChild(secondDiv);

              var eleB = document.createElement('b');
              eleB.setAttribute('style', 'align-content: center;');
              eleB.innerHTML = obj.name;
              firstDiv.appendChild(eleB);
              divHeros.appendChild(firstDiv);
          });
        }
      });
    }
  }

  function selectHerosNew(){
    var designationId = parseInt(document.getElementById('designation1').value);
    var areaId = parseInt(document.getElementById('area1').value);
    if( 0 < designationId ){
      $.ajax({
          method: "POST",
          url: "{{url('getHeroByDesignationByArea')}}",
          data: {designation_id:designationId, area_id:areaId}
      })
      .done(function( msg ) {
        divHeros = document.getElementById('addHeros');
        divHeros.innerHTML = '';
        if(undefined !== msg && 0 < msg.length) {
          $.each(msg, function(idx, obj) {
              var firstDiv = document.createElement('div');
              firstDiv.className = 'col-md-4 col-sm-6 video-container';

              var secondDiv = document.createElement('div');
              secondDiv.className = 'vid';
              secondDiv.innerHTML = obj.url;
              firstDiv.appendChild(secondDiv);

              var eleB = document.createElement('b');
              eleB.setAttribute('style', 'align-content: center;');
              eleB.innerHTML = obj.name;
              firstDiv.appendChild(eleB);
              divHeros.appendChild(firstDiv);
          });
        }
      });
    }
  }

 function searchCourse(){
    var searches = document.getElementsByClassName('search');
    var arrDifficulty = [];
    var arrCertified = [];
    var arrFees = [];
    var arr = [];
    var startingsoon = 0;
    var latest = 0;
    $.each(searches, function(ind, obj){
      if(true == $(obj).is(':checked')){
        var filter = $(obj).data('filter');
        var filterVal = $(obj).val();
        if(false == (arr.indexOf(filter) > -1)){
          if('latest' == filter) {
            latest = filterVal;
            arr.push(filterVal);
          }
        }
      }
    });
    if(arr instanceof Array ){
      var designationId = parseInt(document.getElementById('designation').value);
      var areaId = parseInt(document.getElementById('area').value);
      var arrJson = {'latest' : latest, 'designationId' : designationId, 'areaId' : areaId };
      $.ajax({
        method: "POST",
        url: "{{url('getHerosBySearchArray')}}",
        data: {arr:JSON.stringify(arrJson)}
      })
      .done(function( msg ) {
        divHeros = document.getElementById('addHeros');
        divHeros.innerHTML = '';
        if(undefined !== msg && 0 < msg.length) {
          $.each(msg, function(idx, obj) {
              var firstDiv = document.createElement('div');
              firstDiv.className = 'col-md-4 col-sm-6 video-container';

              var secondDiv = document.createElement('div');
              secondDiv.className = 'vid';
              secondDiv.innerHTML = obj.url;
              firstDiv.appendChild(secondDiv);

              var eleB = document.createElement('b');
              eleB.setAttribute('style', 'align-content: center;');
              eleB.innerHTML = obj.name;
              firstDiv.appendChild(eleB);
              divHeros.appendChild(firstDiv);
          });
        }
      });
    }
  }

  $(".toggle").slideUp();
  $(".trigger").click(function(){
    $(this).next(".toggle").slideToggle("slow");
  });

  var acc = document.getElementsByClassName("accordion");
  var i;
  for (i = 0; i < acc.length; i++) {
    acc[i].onclick = function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight){
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    }
  }
  </script>
@stop