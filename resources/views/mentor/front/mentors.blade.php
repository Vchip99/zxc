@extends('mentor.front.master')
@section('title')
  <title>Mentor</title>
@stop
@section('header-css')
  <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
  <style>
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
    /* For Horizontal Scrolling */
    .scrolling-wrapper {
      overflow-x: scroll;
      overflow-y: hidden;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }
    .scrolling-wrapper .card {
      display: inline-block;
    }
    /* end For Horizontal Scrolling */
    .user-prof {
      /*border-radius: 50%;*/
      padding: 10px;
      width: 250px;
      height: 250px;
    }
  </style>
@stop
@section('content')
    @include('mentor.front.header_menu')

    <section class="container" style="margin-top: 100px;">
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
      <h1 style="text-align: center;">Connect with Expert to Become Expert</h1><br>
      <p style="text-align:justify;">Experience has its own importance. Connect with expert and learn from their experiences. We believe that learning from others experiences is the best way to learn with in short period and become expert. Sometime we stuck at some point and you might required days or months or years to clear the concepts, at that time it best to take advice from experts, because they already went through  similar condition.</p>
    </section><br>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-9" style="overflow: auto;">
            <div style="display: inline-block;">
              <select class="form-control" id="areaList" style="min-width: 170px;" onChange="selectSkills(this);">
                <option value="">Select Area</option>
                <option value="All">All</option>
                @if(count($mentorAreas) > 0)
                  @foreach($mentorAreas as $mentorArea)
                    <option value="{{$mentorArea->id}}">{{$mentorArea->name}}</option>
                  @endforeach
                @endif
              </select>
            </div>&nbsp;&nbsp;
            <div style="display: inline-block;">
              <select class="form-control" id="skillList" style="min-width: 170px;" onChange="getMentors(this);">
                <option value="">Select Skill</option>
                <option value="All">All</option>
                @if(count($skillNames) > 0)
                  @foreach($skillNames as $skillId => $skillName)
                    <option value="{{$skillId}}">{{$skillName}}</option>
                  @endforeach
                @endif
              </select>
            </div><br><br>
            <div id="allMentors">
            @if(count($mentors) > 0)
              @foreach($mentors as $mentor)
                <div style="border:1px solid black;">
                  <div class="row memberinfo" >
                    <div class="col-md-4">
                      <div>
                        @if(!empty($mentor->photo))
                          <img src="{{asset($mentor->photo)}}" alt="member" class="image img-circle">
                        @else
                          <img src="{{asset('images/user1.png')}}" alt="member" class="image img-circle">
                        @endif
                      </div><br>
                      @if($mentor->fees > 0)
                        <div><strong>Fees: {{$mentor->fees}} /Hour </strong></div>
                      @else
                        <div><strong>Fees: 0 /Hour </strong></div>
                      @endif
                      <div class="row">
                        <a data-toggle="modal" data-target="#review-model-{{$mentor->id}}" style="cursor: pointer;">
                          <span style= "position:relative; top:7px;">
                            @if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif
                          </span>
                          <div style="display: inline-block;">
                            <input id="rating_input{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="@if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                          </div>
                          <span style= "position:relative; top:7px;">
                              @if(isset($reviewData[$mentor->id]))
                                {{count($reviewData[$mentor->id]['rating'])}} <i class="fa fa-group"></i>
                              @else
                                0 <i class="fa fa-group"></i>
                              @endif
                          </span>
                        </a>
                      </div>
                      <div id="review-model-{{$mentor->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              &nbsp;&nbsp;&nbsp;
                              <button class="close" data-dismiss="modal">×</button>
                              <div class="form-group row ">
                                <span style= "position:relative; top:7px;">
                                  @if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif
                                </span>
                                <div  style="display: inline-block;">
                                  <input name="input-{{$mentor->id}}" class="rating rating-loading" value="@if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                                </div>
                                <span style= "position:relative; top:7px;">
                                  @if(isset($reviewData[$mentor->id]))
                                    {{count($reviewData[$mentor->id]['rating'])}} <i class="fa fa-group"></i>
                                  @else
                                    0 <i class="fa fa-group"></i>
                                  @endif
                                </span>
                                @if(is_object(Auth::user()))
                                  <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$mentor->id}}">
                                  @if(isset($reviewData[$mentor->id]) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id]))
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
                                @if(isset($reviewData[$mentor->id]))
                                  @foreach($reviewData[$mentor->id]['rating'] as $userId => $review)
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
                                    <input id="rating_input-{{$mentor->id}}-{{$userId}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
                      <div id="rating-model-{{$mentor->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button class="close" data-dismiss="modal">×</button>
                              Rate and Review
                            </div>
                            <div class="modal-body row">
                              <form action="{{ url('giveMentorRating')}}" method="POST">
                                <div class="form-group row ">
                                  {{ csrf_field() }}
                                  @if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id]))
                                    <input id="rating_input-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="{{$reviewData[$mentor->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                                  @else
                                    <input id="rating_input-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                                  @endif
                                  Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$mentor->id])  && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{trim($reviewData[$mentor->id]['rating'][Auth::user()->id]['review'])}} @endif">
                                  <br>
                                  <input type="hidden" name="mentor_id" value="{{$mentor->id}}">
                                  <input type="hidden" name="rating_id" value="@if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{$reviewData[$mentor->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                                  <button type="submit" class="pull-right">Submit</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-8 topcontent">
                      <p><strong>Name:</strong> {{$mentor->name}}</p>
                      <p><strong>Designation:</strong> {{$mentor->designation}}</p>
                      <p><strong>Education:</strong> {{$mentor->education}}</p>
                      <p><strong>Key Areas:</strong>
                        @php
                          $skillStr = '';
                          $skills = explode(',', $mentor->skills);
                          if(count($skills) > 0){
                            foreach($skills as $index => $skill){
                              if(isset($skillNames[$skill])){
                                $skillStr .= '#'.$skillNames[$skill].' ';
                              }
                            }
                          }
                        @endphp
                        {{$skillStr}}
                      </p>
                      <p>
                        @if(!empty($mentor->linked_in))
                          <a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="{{$mentor->linked_in}}">
                              <i class="fa fa-linkedin"></i>
                          </a>
                        @endif
                        @if(!empty($mentor->twitter))
                          <a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="{{$mentor->twitter}}">
                              <i class="fa fa-twitter"></i>
                          </a>
                        @endif
                        @if(!empty($mentor->facebook))
                          <a class="btn btn-primary btn-sm" rel="publisher" target="_blank" href="{{$mentor->facebook}}">
                              <i class="fa fa-facebook"></i>
                          </a>
                        @endif
                        @if(!empty($mentor->youtube))
                          <a class="btn btn-danger btn-sm" rel="publisher" target="_blank" href="{{$mentor->youtybe}}">
                              <i class="fa fa-youtube"></i>
                          </a>
                        @endif
                      </p>
                      <a class="btn btn-primary" href="{{url('mentorinfo')}}/{{$mentor->id}}">View Profile</a>
                      @if(is_object(Auth::user()))
                        <a class="btn btn-primary" href="#message_{{$mentor->id}}" data-toggle="modal">Message</a>
                      @else
                        <a class="btn btn-primary" onClick="checkLogin();">Message</a>
                      @endif
                    </div>
                  </div>
                </div><br>
                @if(is_object(Auth::user()))
                <div id="message_{{$mentor->id}}" class="modal fade" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button class="close" data-dismiss="modal">×</button>
                        <h2  class="modal-title">Message to mentor</h2>
                      </div>
                      <div class="modal-body">
                        <div class="">
                          <form action="{{url('privatechatByUser')}}" method="POST">
                            {{ csrf_field() }}
                            <fieldset>
                              <div class="form-group row">
                                <label>Mentor :</label>{{$mentor->name}}
                              </div>
                              <div class="form-group row">
                                <label>Message:</label>
                                <textarea name="message" placeholder="Message here.." class="form-control" rows="7" required></textarea>
                              </div>
                              <input type="hidden" name="receiver_id" value="{{$mentor->id}}">
                              <input type="hidden" name="sender_id" value="{{Auth::user()->id}}">
                              <button data-dismiss="modal" class="btn btn-info" type="button">Cancel</button>
                              <button class="btn btn-info" type="submit">Submit</button>
                            </fieldset>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
              @endforeach
            @endif
            </div>
          </div>
          <div class="col-sm-3">
            <div class="advertisement-area">
              <a class="pull-right create-add" href="{{$addUrl}}" target="_blank">Create Ad</a>
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
                        <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Mauli College of Engineering Shegaon"  class="img-responsive" />
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
  <section class="container">
    <h4><strong>Simillar Mentors:</strong></h4>
      <div class="scrolling-wrapper">
        @if(count($mentors) > 0)
          @foreach($mentors as $mentor)
            <div class="card">
              <a href="{{ url('mentorinfo')}}/{{$mentor->id}}">
                <div class="panel">
                  <div>
                    @if(!empty($mentor->photo))
                      <img src="{{asset($mentor->photo)}}" alt="member" class="user-prof" />
                    @else
                      <img src="{{asset('images/user1.png')}}" alt="member" class="user-prof" />
                    @endif
                  </div>
                  <p><b>Name: {{$mentor->name}}</b></p>
                  <p>
                    <div class="row">
                      <a data-toggle="modal" data-target="#review-model_{{$mentor->id}}" style="cursor: pointer;">
                        <span style= "position:relative; top:7px;">
                          @if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif
                        </span>
                        <div style="display: inline-block;">
                          <input id="ratingInput{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="@if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                        </div>
                        <span style= "position:relative; top:7px;">
                            @if(isset($reviewData[$mentor->id]))
                              {{count($reviewData[$mentor->id]['rating'])}} <i class="fa fa-group"></i>
                            @else
                              0 <i class="fa fa-group"></i>
                            @endif
                        </span>
                      </a>
                    </div>
                  </p>
                  <div id="review-model_{{$mentor->id}}" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            &nbsp;&nbsp;&nbsp;
                            <button class="close" data-dismiss="modal">×</button>
                            <div class="form-group row ">
                              <span style= "position:relative; top:7px;">
                                @if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif
                              </span>
                              <div  style="display: inline-block;">
                                <input name="rinput-{{$mentor->id}}" class="rating rating-loading" value="@if(isset($reviewData[$mentor->id])) {{$reviewData[$mentor->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                              </div>
                              <span style= "position:relative; top:7px;">
                                @if(isset($reviewData[$mentor->id]))
                                  {{count($reviewData[$mentor->id]['rating'])}} <i class="fa fa-group"></i>
                                @else
                                  0 <i class="fa fa-group"></i>
                                @endif
                              </span>
                              @if(is_object(Auth::user()))
                                <button class="pull-right" data-toggle="modal" data-target="#rating-model_{{$mentor->id}}">
                                @if(isset($reviewData[$mentor->id]) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id]))
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
                              @if(isset($reviewData[$mentor->id]))
                                @foreach($reviewData[$mentor->id]['rating'] as $userId => $review)
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
                                  <input id="ratingInput-{{$mentor->id}}-{{$userId}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
                  <div id="rating-model_{{$mentor->id}}" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button class="close" data-dismiss="modal">×</button>
                          Rate and Review
                        </div>
                        <div class="modal-body row">
                          <form action="{{ url('giveMentorRating')}}" method="POST">
                            <div class="form-group row ">
                              {{ csrf_field() }}
                              @if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id]))
                                <input id="ratingInput-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="{{$reviewData[$mentor->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                              @else
                                <input id="ratingInput-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                              @endif
                              Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$mentor->id])  && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{trim($reviewData[$mentor->id]['rating'][Auth::user()->id]['review'])}} @endif">
                              <br>
                              <input type="hidden" name="mentor_id" value="{{$mentor->id}}">
                              <input type="hidden" name="rating_id" value="@if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{$reviewData[$mentor->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                              <button type="submit" class="pull-right">Submit</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        @endif
      </div>
  </section>
@stop
@section('footer')
  @include('mentor.front.footer')
  <script type="text/javascript">
    function selectSkills(ele){
      var areaId = $(ele).val();
      if(areaId > 0){
        $.ajax({
          method: "POST",
          url: "{{url('getMentorSkillsByAreaId')}}",
          data: {area:areaId}
        })
        .done(function( msg ) {
          select = document.getElementById('skillList');
          select.innerHTML = '';
          var opt = document.createElement('option');
          opt.value = '';
          opt.innerHTML = 'Select Skill';
          select.appendChild(opt);
          var optAll = document.createElement('option');
          optAll.value = 'All';
          optAll.innerHTML = 'All';
          select.appendChild(optAll);
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

    function getMentors(ele){
      var skillId = $(ele).val();
      var areaId = $('#areaList').val();
      $.ajax({
        method: "POST",
        url: "{{url('getMentorsByAreaIdBySkillId')}}",
        data: {area:areaId,skill:skillId}
      })
      .done(function( msg ) {
        allMentors = document.getElementById('allMentors');
        allMentors.innerHTML = '';
        if(msg['mentors'].length > 0){
          mentorsHTML = '';
          if(document.getElementById('user_id')){
            var currentUser = document.getElementById('user_id').value;
          } else {
            var currentUser = 0;
          }
          $.each(msg['mentors'], function(idx,obj){
            mentorsHTML += '<div style="border:1px solid black;"><div class="row memberinfo" ><div class="col-md-4"><div>';
            var assetStr = "{{asset('')}}/"+obj.photo;
            var memberInfo = "{{url('mentorinfo')}}/"+obj.id;
              if(obj.photo){
                mentorsHTML += '<img src="'+assetStr+'" alt="member" class="image img-circle">';
              }else{
                mentorsHTML += '<img src="images/user1.png" alt="member" class="image img-circle">';
              }
              if(obj.fees > 0){
                mentorsHTML += '</div><br><div><strong>Fees: '+ obj.fees +'/Hour </strong></div>';
              } else {
                mentorsHTML += '</div><br><div><strong>Fees: 0 /Hour </strong></div>';
              }

            if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
              mentorsHTML += '<div class="row"><a data-toggle="modal" data-target="#review-model-'+obj.id+'" style="cursor: pointer;"><span style= "position:relative; top:7px;">'+msg['ratingData'][obj.id]['avg']+'</span><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;">'+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></div></a></span></div>';
            } else {
              mentorsHTML += '<div class="row"><a data-toggle="modal" data-target="#review-model-'+obj.id+'" style="cursor: pointer;"><span style= "position:relative; top:7px;">0</span><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;">0 <i class="fa fa-group"></i></div></a></span></div>';
            }

              mentorsHTML += '<div class="col-md-8 topcontent"><p><strong>Name:</strong>'+ obj.name +'</p><p><strong>Designation:</strong> '+ obj.designation+'</p><p><strong>Education:</strong> '+ obj.education+'</p><p><strong>Key Areas:</strong>';
              var skillStr = '';
              var mentorSkills = obj.skills.split(',');
              $.each(mentorSkills, function(idx, skillId){
                if(msg['skillNames'][skillId]){
                  skillStr += '#'+msg['skillNames'][skillId]+' ';
                }
              });
              mentorsHTML += skillStr+'</p><p>';
              if(obj.linked_in){
                mentorsHTML +='<a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="'+obj.linked_in+'"><i class="fa fa-linkedin"></i></a> ';
              }
              if(obj.twitter){
                mentorsHTML +=' <a class="btn btn-primary btn-twitter btn-sm" target="_blank" href="'+obj.twitter+'"><i class="fa fa-twitter"></i></a>';
              }
              if(obj.facebook){
                mentorsHTML +=' <a class="btn btn-primary btn-sm" rel="publisher" target="_blank" href="'+obj.facebook+'"><i class="fa fa-facebook"></i></a>';
              }
              if(obj.youtube){
                mentorsHTML +=' <a class="btn btn-danger btn-sm" rel="publisher" target="_blank" href="'+obj.youtube+'"><i class="fa fa-youtube"></i></a>';
              }
              mentorsHTML +='</p> <a class="btn btn-primary" href="'+memberInfo+'">View Profile</a>';
              if(currentUser > 0){
                mentorsHTML += ' <a class="btn btn-primary" href="#message_'+obj.id+'" data-toggle="modal"> Message</a></div></div></div><br>';

                var urlStr = "{{url('privatechatByUser')}}";
                var csrfStr = '{{ csrf_field() }}';
                mentorsHTML += '<div id="message_'+obj.id+'" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h2  class="modal-title">Message to mentor</h2></div><div class="modal-body"><div class=""><form action="'+urlStr+'" method="POST">'+csrfStr+'<fieldset><div class="form-group row"><label>Mentor :</label>'+obj.name+'</div><div class="form-group row"><label>Message:</label><textarea name="message" placeholder="Message here.." class="form-control" rows="7" required></textarea></div><input type="hidden" name="receiver_id" value="'+obj.id+'"><input type="hidden" name="sender_id" value="'+currentUser+'"><button data-dismiss="modal" class="btn btn-info" type="button"> Cancel</button> <button class="btn btn-info" type="submit"> Submit</button></fieldset></form></div></div></div></div></div>';
              } else {
                mentorsHTML += ' <a class="btn btn-primary" onClick="checkLogin();"> Message</a></div></div></div><br>';
              }

              mentorsHTML += '<div id="review-model-'+obj.id+'" class="modal fade" role="dialog">';
              if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
                mentorsHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">'+msg['ratingData'][obj.id]['avg']+'</span><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> '+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></span>';
              } else {
                mentorsHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><span style= "position:relative; top:7px;">0</span><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><span style= "position:relative; top:7px;"> 0 <i class="fa fa-group"></i></span>';
              }
              if(currentUser > 0){
                mentorsHTML += '<button class="pull-right" data-toggle="modal" data-target="#rating-model-'+obj.id+'">';
                if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][currentUser]){
                  mentorsHTML += 'Edit Rating';
                } else {
                  mentorsHTML += 'Give Rating';
                }
                mentorsHTML += '</button>';
              } else {
                mentorsHTML += '<button class="pull-right" onClick="checkLogin()">Give Rating</button>';
              }
              mentorsHTML += '</div></div>';

              mentorsHTML += '<div class="modal-body row">';
              if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating']){
                $.each(msg['ratingData'][obj.id]['rating'], function(currentUser, reviewData) {
                  if('system' == msg['userNames'][currentUser]['image_exist']){
                    var userImagePath = "{{ asset('') }}"+msg['userNames'][currentUser]['photo'];
                    var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                  } else if('other' == obj.image_exist){
                    var userImagePath = msg['userNames'][currentUser]['photo'];
                    var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                  } else {
                    var userImagePath = "{{ asset('images/user1.png') }}";
                    var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                  }
                  mentorsHTML += '<div class="user-block cmt-left-margin">'+userImage+'<span class="username">'+msg['userNames'][currentUser]['name']+'</span><span class="description">Shared publicly - '+reviewData.updated_at+'</span></div><br/>';
                  mentorsHTML += '<input id="rating_input-'+obj.id+'-'+currentUser+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
                });
              } else {
                mentorsHTML += 'Please give ratings';
              }
              mentorsHTML += '</div></div></div></div></div></div>';

              var ratingUrl = "{{ url('giveMentorRating')}}";
              var csrfField = '{{ csrf_field() }}';
              mentorsHTML += '<div id="rating-model-'+obj.id+'" class="modal fade" role="dialog">';
              mentorsHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField;
              if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][currentUser]){
                mentorsHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['rating'][currentUser]['rating']+'" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
              } else {
                mentorsHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
              }
              if(msg['ratingData'] && msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][currentUser]){
                mentorsHTML += '<input type="text" name="review-text" class="form-control" value="'+msg['ratingData'][obj.id]['rating'][currentUser]['review']+'">';
                mentorsHTML += '<br><input type="hidden" name="mentor_id" value="'+obj.id+'"><input type="hidden" name="rating_id" value="'+msg['ratingData'][obj.id]['rating'][currentUser]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
              } else {
                mentorsHTML += '<input type="text" name="review-text" class="form-control" value="">';
                mentorsHTML += '<br><input type="hidden" name="mentor_id" value="'+obj.id+'"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
              }
              mentorsHTML += '</div>';
          })
          allMentors.innerHTML = mentorsHTML;
          var inputRating = $('input.rating');
          if(inputRating.length) {
            inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
          }
        }
      });
    }
  </script>
@stop