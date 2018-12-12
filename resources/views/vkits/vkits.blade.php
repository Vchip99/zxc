@extends('layouts.master')
@section('header-title')
  <title>Hobby Projects in Electronics, IoT, VLSI and Vchip-kit |Vchip-edu </title>
@stop
@section('header-css')
  @include('layouts.home-css')
    <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
    <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/star-rating.css') }}" />
  <style type="text/css">
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
    .vchip_product_item{
      background:#FFF;
      padding: 20px;
      -webkit-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      -moz-box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      box-shadow:0 0 5px 0 rgba(130,130,130,0.4);
      margin-bottom:40px;
      text-align:left
    }
    .vchip_product_item:hover{

      box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);

    }
    .vchip_product_content{padding:10px 20px}
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
        <img src="{{asset('images/v-kit.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Vkit" />
      </figure>
    </div>
    <div class="vchip-background-content">
        <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="sidemenuindex"  class="v_container">
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
      <div class="col-sm-3  hidden-div">
        <h4 class="v_h4_subtitle "> Sort By</h4>
        <div class="mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category" name="category" title="Category" onchange="showProjects(this);">
            <option value="">Select Category</option>
            @if(count($vkitCategories) > 0)
              @foreach($vkitCategories as $index => $vkitCategory)
                <option value="{{$vkitCategory->id}}">{{$vkitCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Gateway"> Gateway</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="gateway" onclick="searchVkitProjects();">Android</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="gateway" onclick="searchVkitProjects();">Raspberry-pi</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="gateway" onclick="searchVkitProjects();">Intel galileo</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Microcontroller"> Microcontroller</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="microcontroller" onclick="searchVkitProjects();">AVR</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="microcontroller" onclick="searchVkitProjects();">Atmega328</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="microcontroller" onclick="searchVkitProjects();">8051/8052</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Others"> Others</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="upcoming" onclick="searchVkitProjects();">Upcoming</label>
          </div>
        </div>

      </div>
      <div class="col-sm-9 col-sm-push-3">
        <div class="row info" id="vkitprojects">
          @if(count($projects) > 0)
            @foreach($projects as $project)
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                <div class="course-box">
                  <a class="img-course-box" href="{{ url('vkitproject')}}/{{$project->id}}" title="{{$project->name}}">
                    @if(!empty($project->front_image_path))
                      <img class="img-responsive " src="{{ asset($project->front_image_path) }}" alt="vckits">
                    @else
                      <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="vckits">
                    @endif
                  </a>
                  <div class="course-box-content" >
                     <h4 class="course-box-title " title="{{$project->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a href="{{ url('vkitproject')}}/{{$project->id}}">{{$project->name}}</a></p></h4>
                     <br/>
                    <p class="block-with-text">
                      {{$project->introduction}}
                      <a type="button" class="show " data-show="{{$project->id}}">Read More</a>
                    </p>
                    <div class="corse-detail" id="corse-detail-{{$project->id}}">
                        <div class="corse-detail-heder">
                          <span class="card-title"><b>{{$project->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"  data-close="{{$project->id}}"><span aria-hidden="true">×</span></button>
                        </div><br/>
                          <p>{{$project->introduction}}</p>
                          <div class="text-center corse-detail-footer" >
                            <a href="{{ url('vkitproject')}}/{{$project->id}}" class="btn btn-primary btn-default" > Start Project</a>
                          </div>
                      </div>
                    </div>
                    <div class="course-auther text-center">
                      <div style="display: inline-block;">
                        @if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif
                      </div>
                      <div style="display: inline-block;">
                        <input id="rating_input{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="@if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                      </div>
                      <div style="display: inline-block;">
                        <a data-toggle="modal" data-target="#review-model-{{$project->id}}">
                          @if(isset($reviewData[$project->id]))
                            {{count($reviewData[$project->id]['rating'])}} <i class="fa fa-group"></i>
                          @else
                            0 <i class="fa fa-group"></i>
                          @endif
                        </a>
                      </div>
                    </div>
                    <div id="review-model-{{$project->id}}" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            &nbsp;&nbsp;&nbsp;
                            <button class="close" data-dismiss="modal">×</button>
                            <div class="form-group row ">
                              <div  style="display: inline-block;">
                                @if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif
                              </div>
                              <div  style="display: inline-block;">
                                <input name="input-{{$project->id}}" class="rating rating-loading" value="@if(isset($reviewData[$project->id])) {{$reviewData[$project->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                              </div>
                              <div  style="display: inline-block;">
                                @if(isset($reviewData[$project->id]))
                                  {{count($reviewData[$project->id]['rating'])}} <i class="fa fa-group"></i>
                                @else
                                  0 <i class="fa fa-group"></i>
                                @endif
                              </div>
                              @if(is_object(Auth::user()))
                                <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$project->id}}">
                                @if(isset($reviewData[$project->id]) && isset($reviewData[$project->id]['rating'][Auth::user()->id]))
                                  Edit Rating
                                @else
                                  Give Rating
                                @endif
                                </button>
                              @else
                                <button class="pull-right" onClick="checkLogin();">Give Rating</button>
                              @endif
                            </div>
                          </div>
                          <div class="modal-body row">
                            <div class="form-group row" style="overflow: auto;">
                              @if(isset($reviewData[$project->id]))
                                @foreach($reviewData[$project->id]['rating'] as $userId => $review)
                                  {{$userNames[$userId]}}:
                                  <input id="rating_input-{{$project->id}}-{{$userId}}" name="input-{{$project->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
                    <div id="rating-model-{{$project->id}}" class="modal fade" role="dialog">
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
                                @if(isset($reviewData[$project->id]) && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id]))
                                  <input id="rating_input-{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="{{$reviewData[$project->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                                @else
                                  <input id="rating_input-{{$project->id}}" name="input-{{$project->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                                @endif
                                Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$project->id])  && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id])) {{trim($reviewData[$project->id]['rating'][Auth::user()->id]['review'])}} @endif">
                                <br>
                                <input type="hidden" name="module_id" value="{{$project->id}}">
                                <input type="hidden" name="module_type" value="3">
                                <input type="hidden" name="rating_id" value="@if(isset($reviewData[$project->id]) && is_object(Auth::user()) && isset($reviewData[$project->id]['rating'][Auth::user()->id])) {{$reviewData[$project->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                                <button type="submit" class="pull-right">Submit</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
            @else
              No projects are available.
            @endif
          </div>
            <div  id="pagination">
              {{ $projects->links() }}
            </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
             <h4 class="v_h4_subtitle "> Sort By</h4>
              <div class="mrgn_20_top_btm" id="cat">
                <select class="form-control" id="category" name="category" title="Category" onchange="showProjects(this);">
                  <option value="">Select Category </option>
                  @if(count($vkitCategories) > 0)
                    @foreach($vkitCategories as $index => $vkitCategory)
                      <option value="{{$vkitCategory->id}}">{{$vkitCategory->name}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
              <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
              <div class="panel"></div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" title="Gateway"> Gateway</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="gateway" onclick="searchVkitProjects();">Android</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="2" data-filter="gateway" onclick="searchVkitProjects();">Raspberry-pi</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="3" data-filter="gateway" onclick="searchVkitProjects();">Intel galileo</label>
                </div>
              </div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Microcontroller"> Microcontroller</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="microcontroller" onclick="searchVkitProjects();">AVR</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="2" data-filter="microcontroller" onclick="searchVkitProjects();">Atmega328</label>
                </div>
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="3" data-filter="microcontroller" onclick="searchVkitProjects();">8051/8052</label>
                </div>
              </div>
              <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm"  title="Others"> Others</p>
              <div class="panel">
                <div class="checkbox">
                  <label><input class="search" type="checkbox" value="1" data-filter="upcoming" onclick="searchVkitProjects();">Upcoming</label>
                </div>
              </div>
        </div>
        <div class="advertisement-area">
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

  function renderVkitProjects(msg){
    var userId = parseInt(document.getElementById('user_id').value);
    projects = document.getElementById('vkitprojects');
    projects.innerHTML = '';
    document.getElementById('pagination').innerHTML = '';
    if( undefined !== msg['projects'].length && 0 < msg['projects'].length){
      $.each(msg['projects'], function(idx, obj) {
        var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
          var secondDiv = document.createElement('div');
          secondDiv.className = "course-box";
          var url = "{{ url('vkitproject')}}/"+ obj.id;
          var anc = document.createElement('a');
          anc.className = 'img-course-box';
          anc.href = url;
          anc.setAttribute('title', obj.name);
          var img = document.createElement('img');
          img.className = "img-responsive";
          if(obj.front_image_path){
            img.src = "{{ asset('') }}" + obj.front_image_path;
          } else {
            img.src = "{{ asset('images/default_course_image.jpg') }}";
          }
          anc.appendChild(img);
          secondDiv.appendChild(anc);

          var thirdDiv = document.createElement('div');
          thirdDiv.className = "course-box-content";

          var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a href="'+ url +'">'+ obj.name +'</a></p></h4>';
           courseContent += '<br/><p class="block-with-text">'+ obj.introduction+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

          courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.introduction +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" > Start Project</a></div></div>';

          thirdDiv.innerHTML = courseContent;
          secondDiv.appendChild(thirdDiv);

          var ratingDiv = document.createElement('div');
          ratingDiv.className = "course-auther text-center";
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
            ratingDiv.innerHTML = '<div style="display: inline-block;">'+msg['ratingData'][obj.id]['avg']+'</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;"><a data-toggle="modal" data-target="#review-model-'+obj.id+'">'+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></a></div>';
          } else {
            ratingDiv.innerHTML = '<div style="display: inline-block;">0</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;"><a data-toggle="modal" data-target="#review-model-'+obj.id+'">0 <i class="fa fa-group"></i></a></div>';
          }
          secondDiv.appendChild(ratingDiv);
          firstDiv.appendChild(secondDiv);

          var reviewModel = document.createElement('div');
          reviewModel.setAttribute('id','review-model-'+obj.id);
          reviewModel.setAttribute('class','modal fade');
          reviewModel.setAttribute('role','dialog');

          reviewModelInnerHTML = '';
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
            reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><div  style="display: inline-block;">'+msg['ratingData'][obj.id]['avg']+'</div><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div  style="display: inline-block;"> '+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></div>';
          } else {
            reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><div  style="display: inline-block;">0</div><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div  style="display: inline-block;"> 0 <i class="fa fa-group"></i></div>';
          }
          if(userId > 0){
            reviewModelInnerHTML += '<button class="pull-right" data-toggle="modal" data-target="#rating-model-'+obj.id+'">';
            if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
              reviewModelInnerHTML += 'Edit Rating';
            } else {
              reviewModelInnerHTML += 'Give Rating';
            }
            reviewModelInnerHTML += '</button>';
          } else {
            reviewModelInnerHTML += '<button class="pull-right" onClick="checkLogin()">Give Rating</button>';
          }
          reviewModelInnerHTML += '</div></div>';

          reviewModelInnerHTML += '<div class="modal-body row">';
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating']){
            $.each(msg['ratingData'][obj.id]['rating'], function(userId, reviewData) {
              reviewModelInnerHTML += msg['userNames'][userId] +':';
              reviewModelInnerHTML += '<input id="rating_input-'+obj.id+'-'+userId+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
            });
          } else {
            reviewModelInnerHTML += 'Please give ratings';
          }
          reviewModelInnerHTML += '</div></div></div></div></div>';
          reviewModel.innerHTML = reviewModelInnerHTML;
          firstDiv.appendChild(reviewModel);

          var ratingModel = document.createElement('div');
          ratingModel.setAttribute('id','rating-model-'+obj.id);
          ratingModel.setAttribute('class','modal fade');
          ratingModel.setAttribute('role','dialog');
          var ratingUrl = "{{ url('giveRating')}}";
          var csrfField = '{{ csrf_field() }}';
          ratingModelInnerHTML = '';
          ratingModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField;
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
            ratingModelInnerHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['rating'][userId]['rating']+'" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
          } else {
            ratingModelInnerHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
          }
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
            ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="'+msg['ratingData'][obj.id]['rating'][userId]['review']+'">';
            ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="3"><input type="hidden" name="rating_id" value="'+msg['ratingData'][obj.id]['rating'][userId]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
          } else {
            ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="">';
            ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="3"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
          }

          ratingModel.innerHTML = ratingModelInnerHTML;
          firstDiv.appendChild(ratingModel);
          projects.appendChild(firstDiv);
          var inputRating = $('input.rating');
          if(inputRating.length) {
            inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
          }
      });
       $(function(){
          $('.show').on('click',function(){
            id = $(this).data('show')        ;
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
          $('.close').on('click',function(){
            id = $(this).data('close')
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
      });
    } else {
      projects.innerHTML = 'No Result Found.';
    }
  }

    function searchVkitProjects(){
      var searches = document.getElementsByClassName('search');
      var arrGateway = [];
      var arrMicrocontroller = [];
      var arr = [];
      var upcoming = 0;
      $.each(searches, function(ind, obj){
        if(true == $(obj).is(':checked')){
          var filter = $(obj).data('filter');
          var filterVal = $(obj).val();
          if(false == (arrGateway.indexOf(filter) > -1)){
            if('gateway' == filter) {
              arrGateway.push(filterVal);
              arr.push(filterVal);
            }
            if('microcontroller' == filter) {
              arrMicrocontroller.push(filterVal);
              arr.push(filterVal);
            }
            if('upcoming' == filter) {
              upcoming = 1;
              arr.push(filterVal);
            }
          }
        }
      });
      if(arr instanceof Array ){
        categoryId = document.getElementById('category').value;
        var arrJson = {'gateway' : arrGateway, 'microcontroller' : arrMicrocontroller, 'upcoming' : upcoming, 'categoryId' : categoryId};
        $.ajax({
          method: "POST",
          url: "{{url('getVkitProjectsBySearchArray')}}",
          data: {arr:JSON.stringify(arrJson)}
        })
        .done(function( msg ) {;
          renderVkitProjects(msg)
        });
      }
    }

    function showProjects(ele){
      id = parseInt($(ele).val());
      if( 0 < id ){
        $.ajax({
          method: "POST",
          url: "{{url('getVkitProjectsByCategoryId')}}",
          data: {id:id, rating:true}
        })
        .done(function( msg ) {
          renderVkitProjects(msg)
          var searches = document.getElementsByClassName('search');
          $.each(searches, function(ind, obj){
            $(obj).attr('checked', false);
          });
        });
      }
    }
  </script>

  <script >
    $(".toggle").slideUp();
    $(".trigger").click(function(){
      $(this).next(".toggle").slideToggle("slow");
    });
  </script>
  <script type="text/javascript">
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