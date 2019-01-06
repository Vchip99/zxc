@extends('mentor.front.master')
@section('title')
  <title>Vchip Mentor</title>
@stop
@section('header-css')
  <link href="{{asset('css/nav_footer.css?ver=1.0')}}" rel="stylesheet"/>
  <style>
    .memberinfotop{
      margin-top: 100px;
    }
    .memberinfo{
      margin:10px !important;
    }
    .image{
      height:220px;
      width:200px;
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
  <div class="container">
    <div class="row">
      <div class="col-md-10 memberinfotop col-md-offset-1">
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
        <div style="border:1px solid black;">
          <div class="row memberinfo" >

            <div class="col-md-4">
              <div>
                @if(!empty($mentor->photo))
                  <img src="{{asset($mentor->photo)}}" alt="member" class="image img-circle">
                @else
                  <img src="{{asset('images/user1.png')}}" alt="member" class="image img-circle">
                @endif
              </div>
            </div>
            <div class="col-md-8 topcontent">
              <p><strong>Name:</strong> {{$mentor->name}}</p>
              <p><strong>Designation:</strong> {{$mentor->designation}}</p>
              <p><strong>Education:</strong> {{$mentor->education}}</p>
              <p><strong>Key Skills:</strong>
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
              @if($mentor->fees > 0)
                <div><strong>Fees: {{$mentor->fees}} /Hour </strong></div>
              @else
                <div><strong>Fees: 0 /Hour </strong></div>
              @endif
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
                      <form action="{{ url('giveRating')}}" method="POST">
                        <div class="form-group row ">
                          {{ csrf_field() }}
                          @if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id]))
                            <input id="rating_input-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="{{$reviewData[$mentor->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                          @else
                            <input id="rating_input-{{$mentor->id}}" name="input-{{$mentor->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                          @endif
                          Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$mentor->id])  && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{trim($reviewData[$mentor->id]['rating'][Auth::user()->id]['review'])}} @endif">
                          <br>
                          <input type="hidden" name="module_id" value="{{$mentor->id}}">
                          <input type="hidden" name="module_type" value="1">
                          <input type="hidden" name="rating_id" value="@if(isset($reviewData[$mentor->id]) && is_object(Auth::user()) && isset($reviewData[$mentor->id]['rating'][Auth::user()->id])) {{$reviewData[$mentor->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                          <button type="submit" class="pull-right">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              @if(is_object(Auth::user()))
                <a class="btn btn-primary" href="#message_{{$mentor->id}}" data-toggle="modal">Message</a>
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
              @else
                <a class="btn btn-primary" onClick="checkLogin();">Message</a>
              @endif
            </div>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>About:</strong></h4>
            <p>{!! $mentor->about !!}</p>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>Experiance:</strong></h4>
            <p>{!! $mentor->experiance !!}</p>
          </div>
        </div><br>
        <div style="border:1px solid black;">
          <div class="row memberinfo">
            <h4><strong>Achievements:</strong></h4>
            <p>{!! $mentor->achievement !!}</p>
          </div>
        </div><br>
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
                        <a data-toggle="modal" data-target="#review-model-{{$mentor->id}}" style="cursor: pointer;">
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
      </div>
    </div>
  </div>
@stop
@section('footer')
  @include('mentor.front.footer')
@stop