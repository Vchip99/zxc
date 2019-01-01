@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />

<style>
  .fa {
    font-size: medium !important;
  }
  .rating-container .filled-stars{
    color: #e7711b;
    border-color: #e7711b;
  }
  .rating-xs {
      font-size: 0em;
  }
  .user-block img {
    width: 40px;
    height: 40px;
    float: left;
    border: 2px solid #d2d6de;
    padding: 1px;
  }
  .img-circle {
    border-radius: 50%;
  }
  .user-block .username, .user-block .description{
      display: block;
      margin-left: 50px;
  }
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
                  <div style="padding-left: 10px;"><a href="{{asset($userData->resume)}}" download><button type="button" class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a></div>
                  @endif
                  <div class="row">
                    <a data-toggle="modal" data-target="#review-model-{{$userData->id}}" style="cursor: pointer;">
                      <span style= "position:relative; top:7px;">
                        @if(isset($reviewData[$userData->id])) {{$reviewData[$userData->id]['avg']}} @else 0 @endif
                      </span>
                      <div style="display: inline-block;">
                        <input id="rating_input{{$userData->id}}" name="input-{{$userData->id}}" class="rating rating-loading" value="@if(isset($reviewData[$userData->id])) {{$reviewData[$userData->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                      </div>
                      <span style= "position:relative; top:7px;">
                        @if(isset($reviewData[$userData->id]))
                          {{count($reviewData[$userData->id]['rating'])}} <i class="fa fa-group"></i>
                        @else
                          0 <i class="fa fa-group"></i>
                        @endif
                      </span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div id="review-model-{{$userData->id}}" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    &nbsp;&nbsp;&nbsp;
                    <button class="close" data-dismiss="modal">×</button>
                    <div class="form-group row ">
                      <span style= "position:relative; top:7px;">
                        @if(isset($reviewData[$userData->id])) {{$reviewData[$userData->id]['avg']}} @else 0 @endif
                      </span>
                      <div  style="display: inline-block;">
                        <input name="input-{{$userData->id}}" class="rating rating-loading" value="@if(isset($reviewData[$userData->id])) {{$reviewData[$userData->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                      </div>
                      <span style= "position:relative; top:7px;">
                        @if(isset($reviewData[$userData->id]))
                          {{count($reviewData[$userData->id]['rating'])}} <i class="fa fa-group"></i>
                        @else
                          0 <i class="fa fa-group"></i>
                        @endif
                      </span>
                      @if(is_object(Auth::user()))
                        <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$userData->id}}">
                        @if(isset($reviewData[$userData->id]) && isset($reviewData[$userData->id]['rating'][Auth::user()->id]))
                          Edit Rating
                        @else
                          Give Rating
                        @endif
                        </button>
                      @else
                        <button class="pull-right" onClick="giveRating({{$userData->user_id}})">Give Rating</button>
                      @endif
                    </div>
                  </div>
                  <div class="modal-body row">
                    <div class="form-group row" style="overflow: auto;">
                      @if(isset($reviewData[$userData->id]))
                        @foreach($reviewData[$userData->id]['rating'] as $userId => $review)
                          <div class="user-block cmt-left-margin">
                            @if(is_file($userNames[$userId]['photo']) || (!empty($userNames[$userId]['photo']) && false == preg_match('/userStorage/',$userNames[$userId]['photo'])))
                              <img src="{{ asset($userNames[$userId]['photo'])}} " class="img-circle" alt="User Image">
                            @else
                              <img src="{{ url('images/user1.png')}}" class="img-circle" alt="User Image">
                            @endif
                            <span class="username">{{ $userNames[$userId]['name'] }} </span>
                            <span class="description">Shared publicly - {{$review['updated_at']}}</span>
                          </div>
                          <br>
                          <input id="rating_input-{{$userData->id}}-{{$userId}}" name="input-{{$userData->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                          {{$review['review']}}
                          <hr>
                        @endforeach
                      @else
                        Please give ratings
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="rating-model-{{$userData->id}}" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button class="close" data-dismiss="modal">×</button>
                    Rate and Review
                  </div>
                  <div class="modal-body row">
                    <form action="{{ url('giveRating')}}" method="POST">
                      <div class="form-group row ">
                        {{ csrf_field() }}
                        @if(isset($reviewData[$userData->id]) && is_object(Auth::user()) && isset($reviewData[$userData->id]['rating'][Auth::user()->id]))
                          <input id="rating_input-{{$userData->id}}" name="input-{{$userData->id}}" class="rating rating-loading" value="{{$reviewData[$userData->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                        @else
                          <input id="rating_input-{{$userData->id}}" name="input-{{$userData->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                        @endif
                        Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$userData->id])  && is_object(Auth::user()) && isset($reviewData[$userData->id]['rating'][Auth::user()->id])) {{trim($reviewData[$userData->id]['rating'][Auth::user()->id]['review'])}} @endif">
                        <br>
                        <input type="hidden" name="module_id" value="{{$userData->id}}">
                        <input type="hidden" name="module_type" value="5">
                        <input type="hidden" name="rating_id" value="@if(isset($reviewData[$userData->id]) && is_object(Auth::user()) && isset($reviewData[$userData->id]['rating'][Auth::user()->id])) {{$reviewData[$userData->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                        <button type="submit" class="pull-right">Submit</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
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
  <script src="{{ asset('js/star-rating.js') }}"></script>
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
    var userId = parseInt(document.getElementById('user_id').value);

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
        var h4Ele = document.createElement('h4');
        h4Ele.innerHTML = '<strong>'+data['name']+'</strong>';
        fourthDiv.appendChild(h4Ele);

        var pExp = document.createElement('p');
        pExp.innerHTML = '<strong>Experience:</strong>'+data['experience'];
        fourthDiv.appendChild(pExp);

        var pCmp = document.createElement('p');
        pCmp.innerHTML = '<strong>Company Name:</strong>'+data['company'];
        fourthDiv.appendChild(pCmp);

        var pEdu = document.createElement('p');
        pEdu.innerHTML = '<strong>Education:</strong>'+data['education'];
        fourthDiv.appendChild(pEdu);

        var pSkill = document.createElement('p');
        pSkill.innerHTML = '<strong>Skills:</strong>'+data['skill'];
        fourthDiv.appendChild(pSkill);

        var pBottom = document.createElement('p');
        pBottom.innerHTML = '';
        if(data['twitter']){
          pBottom.innerHTML += '<a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="'+data['twitter']+'"><i class="fa fa-twitter"></i></a>';
        }
        if(data['google']){
          pBottom.innerHTML += ' <a class="btn btn-danger btn-sm" rel="publisher" target="_blank" href="'+data['google']+'"><i class="fa fa-google-plus"></i></a>';
        }
        if(data['facebook']){
          pBottom.innerHTML += ' <a class="btn btn-primary btn-sm" rel="publisher" target="_blank" href="'+data['facebook']+'"><i class="fa fa-facebook"></i></a>';
        }
        fourthDiv.appendChild(pBottom);

        if(data['is_file_resume']){
          var resumeDiv = document.createElement('div');
          resumeDiv.setAttribute('style','padding-left: 30px;');
          resumeDiv.innerHTML = '<a href="'+ data['resume'] +'" download><button type="button"  class="btn btn-success ">Resume <i class="fa fa-download"></i></button></a>';
          fourthDiv.appendChild(resumeDiv);
        }

        var rowDiv = document.createElement('div');
        var ancTag = document.createElement('a');
        ancTag.setAttribute('data-toggle','modal');
        ancTag.setAttribute('data-target','#review-model-'+id);
        ancTag.setAttribute('style',"cursor: pointer;");

        var avgDiv = document.createElement('span');
        avgDiv.setAttribute('style','position:relative; top:7px;');
        if(data['ratingData'] && data['ratingData']['avg']){
          avgDiv.innerHTML = data['ratingData']['avg'];
        } else {
          avgDiv.innerHTML = 0;
        }
        ancTag.appendChild(avgDiv);

        var starDiv = document.createElement('div');
        starDiv.setAttribute('style','display: inline-block;');

        var ratingInput = document.createElement('input');
        ratingInput.setAttribute('id','rating_input'+id);
        ratingInput.setAttribute('name','input-'+id);
        ratingInput.setAttribute('class','rating rating-loading');
        if(data['ratingData'] && data['ratingData']['avg']){
          ratingInput.setAttribute('value',data['ratingData']['avg']);
        } else {
          ratingInput.setAttribute('value',0);
        }
        ratingInput.setAttribute('data-min',0);
        ratingInput.setAttribute('data-max',5);
        ratingInput.setAttribute('data-step','0.1');
        ratingInput.setAttribute('data-size','xs');
        ratingInput.setAttribute('data-show-clear','false');
        ratingInput.setAttribute('data-show-caption','false');
        ratingInput.setAttribute('readonly','true');

        starDiv.appendChild(ratingInput);
        ancTag.appendChild(starDiv);

        var grpDiv = document.createElement('span');
        grpDiv.setAttribute('style','position:relative; top:7px;');
        if(data['ratingData'] && data['ratingData']['rating']){
          grpDiv.innerHTML = Object.keys(data['ratingData']['rating']).length+' <i class="fa fa-group"></i>';
        } else {
          grpDiv.innerHTML = '0 <i class="fa fa-group"></i>';
        }
        ancTag.appendChild(grpDiv);
        rowDiv.appendChild(ancTag);

        fourthDiv.appendChild(rowDiv);
        secondDiv.appendChild(fourthDiv);
        firstDiv.appendChild(secondDiv);
        divUsers.appendChild(firstDiv);
        var brEle = document.createElement('br');
        divUsers.appendChild(brEle);
      });
      $.each(msg, function(id, data) {
        var reviewModel = document.createElement('div');
        reviewModel.setAttribute('id','review-model-'+id);
        reviewModel.setAttribute('class','modal fade');
        reviewModel.setAttribute('role','dialog');

        reviewModelInnerHTML = '';
        if(data['ratingData'] && data['ratingData']['rating']){
          reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">'+data['ratingData']['avg']+'</span><div  style="display: inline-block;"><input name="input-'+id+'" class="rating rating-loading" value="'+data['ratingData']['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> '+Object.keys(data['ratingData']['rating']).length+' <i class="fa fa-group"></i></span>';
        } else {
          reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">0</span><div  style="display: inline-block;"><input name="input-'+id+'" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> 0 <i class="fa fa-group"></i></span>';
        }
        if(userId > 0){
          reviewModelInnerHTML += '<button class="pull-right" data-toggle="modal" data-target="#rating-model-'+id+'">';
          if(data['ratingData'] && data['ratingData']['rating'] && data['ratingData']['rating'][userId]){
            reviewModelInnerHTML += 'Edit Rating';
          } else {
            reviewModelInnerHTML += 'Give Rating';
          }
          reviewModelInnerHTML += '</button>';
        } else {
          reviewModelInnerHTML += '<button class="pull-right" onClick="giveRating('+userId+')">Give Rating</button>';
        }
        reviewModelInnerHTML += '</div></div>';

        reviewModelInnerHTML += '<div class="modal-body row">';
        if(data['ratingData'] && data['ratingData']['rating']){
          $.each(data['ratingData']['rating'], function(userId, reviewData) {
            if('system' == reviewData.image_exist){
              var userImagePath = "{{ asset('') }}"+reviewData.user_photo;
              var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
            } else if('other' == reviewData.image_exist){
              var userImagePath = reviewData.user_photo;
              var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
            } else {
              var userImagePath = "{{ asset('images/user1.png') }}";
              var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
            }
            reviewModelInnerHTML += '<div class="user-block cmt-left-margin">'+userImage+'<span class="username">'+reviewData.user_name+'</span><span class="description">Shared publicly - '+reviewData.updated_at+'</span></div><br/>';

            reviewModelInnerHTML += '<input id="rating_input-'+id+'-'+userId+'" name="input-'+id+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
          });
        } else {
          reviewModelInnerHTML += 'Please give ratings';
        }
        reviewModelInnerHTML += '</div></div></div></div></div>';
        reviewModel.innerHTML = reviewModelInnerHTML;
        divUsers.appendChild(reviewModel);

        var ratingModel = document.createElement('div');
        ratingModel.setAttribute('id','rating-model-'+id);
        ratingModel.setAttribute('class','modal fade');
        ratingModel.setAttribute('role','dialog');
        var ratingUrl = "{{ url('giveRating')}}";
        var csrfField = '{{ csrf_field() }}';
        ratingModelInnerHTML = '';
        if(data['ratingData'] && data['ratingData']['rating']){
          ratingModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField+'<input id="rating_input-'+id+'" name="input-'+id+'" class="rating rating-loading" value="'+Object.keys(data['ratingData']['rating']).length+'" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
        } else {
          ratingModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField+'<input id="rating_input-'+id+'" name="input-'+id+'" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
        }
        if(data['ratingData'] && data['ratingData']['rating'] && data['ratingData']['rating'][userId]){
          ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="'+data['ratingData']['rating'][userId]['review']+'">';
          ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+id+'"><input type="hidden" name="module_type" value="5"><input type="hidden" name="rating_id" value="'+data['ratingData']['rating'][userId]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
        } else {
          ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="">';
          ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+id+'"><input type="hidden" name="module_type" value="5"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
        }

        ratingModel.innerHTML = ratingModelInnerHTML;
        divUsers.appendChild(ratingModel);
      });
      var inputRating = $('input.rating');
      if(inputRating.length) {
        inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
      }
    } else {
      divUsers.innerHTML = 'No Result!';
    }
  }

  function giveRating(dataUser){
    var userId = parseInt(document.getElementById('user_id').value);
    if(isNaN(userId)) {
      $('#loginUserModel').modal();
      return false;
    }
  }
  </script>
@stop