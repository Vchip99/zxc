@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
<style>
    .memberinfotop{
      margin-top: 100px;
    }
    .memberinfo{
      margin:10px;
    }
    .image{
      height:150px;
      width:150px;
    }
    .topcontent{
      padding-top:20px;
    }
    .content{
      /*padding-top: 20px;*/
    }

    .button{
      float:right;

    }
    .button1{
      float:left;
    }
    @media only screen and (max-width: 418px){
      body{
        font-size: 13px;
      }
    }
    @media only screen and (max-width: 386px){
      body{
        font-size: 12px;
      }
    }
    @media only screen and (max-width: 375px){
      body{
        font-size: 11px;
      }
    }

    @media (max-width: 1190px) {
      .navbar-header {
          float: none;
      }
      .navbar-left,.navbar-right {
          float: none !important;
      }
      .navbar-toggle {
          display: block;
      }
      .navbar-collapse {
          border-top: 1px solid transparent;
          box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
          min-height: 410px;
      }
      .navbar-fixed-top {
          top: 0;
          border-width: 0 0 1px;
      }
      .navbar-collapse.collapse {
          display: none!important;
      }
      .navbar-nav {
          float: none!important;
          margin-top: 7.5px;
      }
      .navbar-nav>li {
          float: none;
      }
      .navbar-nav>li>a {
          padding-top: 10px;
          padding-bottom: 10px;
      }
      .collapse.in{
          display:block !important;
      }
    }
    .iframe-container iframe{
      width: 100% !important;
    }
    .vid {position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden; }
    .vid iframe, .vid object,.vid embed {position: absolute; top: 0; left: 0; width: 100%; height: 100%;}
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
          <img src="{{asset('images/zero-to-hero-01.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Mock Interview" />
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
          <h4 class="v_h4_subtitle"> Sort By</h4>
          <div class="mrgn_20_top_btm" >
            <select class="form-control" id="skill" name="skill" required title="Skill"  onChange="selectUsers();">
              <option value="">Select Skill</option>
              @if(count($userSkills) > 0)
                @foreach($userSkills as $skill)
                  <option value="{{$skill->id}}">{{$skill->name}}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>
        <div class="col-sm-9 col-sm-push-3" id="allUsers">
          @if(count($userDatas) > 0)
            @foreach($userDatas as $userData)
            <div style="border:1px solid black;">
              <div class="row memberinfo" >
                <div class="col-md-5 ">
                  <div class="vid">
                    {!! $userData->youtube !!}
                  </div>
                </div>
                <div class="col-md-7">
                  @php
                    $expArr = explode(',',$userData->experiance);
                    $skillArr = explode(',',$userData->skill_ids);
                  @endphp
                  <h4><strong>{{ $testUsers[$userData->user_id]->name }}</strong></h4>
                  <p><strong>Experience:</strong>{{$expArr[0]}} yr {{$expArr[1]}} month</p>
                  <p><strong>Company Name:</strong>{{$userData->company}}</p>
                  <p><strong>Education:</strong>{{$userData->education}}</p>
                  <p><strong>Skills:</strong>
                    @if(count($skillArr) > 0)
                      @foreach($skillArr as $skill)
                        #{{$userSkills[$skill]->name}}
                      @endforeach
                    @endif
                  </p>
                  <p class="bottom">
                    @if(!empty($userData->twitter))
                      <a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="{{$userData->twitter}}">
                          <i class="fa fa-twitter"></i>
                      </a>
                    @endif
                    @if(!empty($userData->google))
                      <a class="btn btn-danger btn-sm" rel="publisher" target="_blank" href="{{$userData->google}}">
                          <i class="fa fa-google-plus"></i>
                      </a>
                    @endif
                    @if(!empty($userData->facebook))
                      <a class="btn btn-primary btn-sm" rel="publisher" target="_blank" href="{{$userData->facebook}}">
                          <i class="fa fa-facebook"></i>
                      </a>
                    @endif
                  </p>
                  @if(!empty($userData->resume) && is_file($userData->resume))
                  <div style="padding-left: 10px;"><a href="{{asset($userData->resume)}}" download><button type="button"  class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a></div>
                  @endif
                </div>
              </div>
            </div>
            <br>
            @endforeach
          @else
            No Data
          @endif
        </div>
        <div class="col-sm-3 col-sm-pull-9">
          <div class="hidden-div1">
            <h4 class="v_h4_subtitle"> Sort By</h4>
            <div class="mrgn_20_top_btm" >
              <select class="form-control" id="skill1" name="skill" required title="Skill"  onChange="selectUsersNew();">
                <option value="">Select Skill</option>
                @if(count($userSkills) > 0)
                  @foreach($userSkills as $skill)
                    <option value="{{$skill->id}}">{{$skill->name}}</option>
                  @endforeach
                @endif
              </select>
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
                    <a class="img-course-box" href="http://www.ssgmce.org" target="_blank">
                      <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Shri Sant Gajanan Maharaj College of Engineering"  class="img-responsive" />
                    </a>
                    <div class="course-box-content">
                      <h4 class="course-box-title" title="Shri Sant Gajanan Maharaj College of Engineering" data-toggle="tooltip" data-placement="bottom">
                        <a href="http://www.ssgmce.org/" target="_blank">Shri Sant Gajanan Maharaj College of Engineering</a>
                      </h4>
                      <p class="more"> Shri Sant Gajanan Maharaj College of Engineering</p>
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

  function selectUsers(){
    var skillId = parseInt(document.getElementById('skill').value);
    if( 0 < skillId ){
      $.ajax({
          method: "POST",
          url: "{{url('getSelectedStudentBySkillId')}}",
          data: {skill_id:skillId}
      })
      .done(function( msg ) {
        renderData(msg);
      });
    }
  }

  function selectUsersNew(){
    var skillId = parseInt(document.getElementById('skill1').value);
    if( 0 < skillId ){
      $.ajax({
          method: "POST",
          url: "{{url('getSelectedStudentBySkillId')}}",
          data: {skill_id:skillId}
      })
      .done(function( msg ) {
        renderData(msg);
      });
    }
  }

  function renderData(msg){
    divUsers = document.getElementById('allUsers');
    divUsers.innerHTML = '';
    if(Object.keys(msg).length) {
      $.each(msg, function(id, data) {
          var firstDiv = document.createElement('div');
          firstDiv.setAttribute('style', 'border:1px solid black;');

          var secondDiv = document.createElement('div');
          secondDiv.className = 'row memberinfo';

          var thirdDiv = document.createElement('div');
          thirdDiv.className = 'col-md-5';
          if(data['youtube']){
            thirdDiv.innerHTML = '<div class="vid">'+data['youtube']+'</div>';
          } else {
            thirdDiv.innerHTML = '<div class="vid"></div>';
          }
          secondDiv.appendChild(thirdDiv);

          var fourthDiv = document.createElement('div');
          fourthDiv.className = 'col-md-7 topcontent';
          fourthDivInnerHtml = '';
          fourthDivInnerHtml += '<h4><strong>'+data['name']+'</strong></h4><p><strong>Experience:</strong>'+data['experience']+'</p><p><strong>Company Name:</strong>'+data['company']+'</p><p><strong>Education:</strong>'+data['education']+'</p><p><strong>Skills:</strong>'+data['skill']+'</p>';
          fourthDivInnerHtml += '<p class="bottom">';
          if(data['twitter']){
            fourthDivInnerHtml += '<a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="'+data['twitter']+'"><i class="fa fa-twitter"></i></a>';
          }
          if(data['google']){
            fourthDivInnerHtml += ' <a class="btn btn-danger btn-sm" rel="publisher" target="_blank" href="'+data['google']+'"><i class="fa fa-google-plus"></i></a>';
          }
          if(data['facebook']){
            fourthDivInnerHtml += ' <a class="btn btn-primary btn-sm" rel="publisher" target="_blank" href="'+data['facebook']+'"><i class="fa fa-facebook"></i></a>';
          }
          fourthDivInnerHtml += '</p>';
          if(data['is_file_resume']){
            fourthDivInnerHtml += '<div style="padding-left: 30px;"><a href="'+ data['resume'] +'" download><button type="button"  class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a></div>';
          }
          fourthDiv.innerHTML = fourthDivInnerHtml;
          secondDiv.appendChild(fourthDiv);
          firstDiv.appendChild(secondDiv);
          divUsers.appendChild(firstDiv);
          var brEle = document.createElement('br');
          divUsers.appendChild(brEle);
      });
    } else {
      divUsers.innerHTML = 'No Result!';
    }
  }
  </script>
@stop