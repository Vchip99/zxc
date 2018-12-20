@extends('layouts.master')
@section('header-title')
  <title>Online Courses details by  Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
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
  </style>
  <style >
.video_id{font-weight: 900px;
  font-size: 90px;
  text-align: center;}
  /*read-more*/
  .morecontent span {
      display: none;
  }
  .morelink {
      display: block;
  }
  @media  (min-width: 617px) {
   .data-sm { display: none; }
  }
  @media  (max-width: 616px) {
    .data-lg { display: none; }
  }
  /*download*/
  .download-item{width: 100px;}
  .btn-group-lg .btn{border-radius: 0px;}
  @media  (min-width: 350px) {
    }
  @media  (max-width: 349px) {
      .top-btn-align, .bottom-btn-align{text-align: center;}
    }
  .vote-btn.selected{
    color:#e91e63 !important;
  }
@media(max-width: 505px){
.col-sm-12, .col-md-9{
  padding-left: 0px;
  padding-right: 0px;
}
}
.download_iteam {
border: 1px solid #ddd;
width: 100px;
border-radius: 20px;
padding: 10px;
margin-top: 10px;
}
.download_iteam .fa {
font-size: 20px;
margin: 0px 5px;
}
  .pay-now{
    text-align: right;
    margin-bottom: 10px;
  }
  @media(max-width: 768px){
    .pay-now{text-align: center;}
  }
  .pay-now span{
    color: #e91e63;
    font-weight: bold;
  }
  </style>
@stop
@section('header-js')
  @include('layouts.home-js')
@stop
@section('content')
	@include('header.header_menu')
  <section id="vchip-background" class="">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/header.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Course Details" />
        </figure>
            <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
      </div>
    </div>
  </section>
<!-- Start course section -->
<section class="v_container v_bg_grey">
  <div class="container">
    @if(count($videos) > 0)
      <div class="col-md-12">
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
        </div>
        <div class="pay-now">
          <a data-toggle="modal" data-target="#review-model-{{$course->id}}" style="cursor: pointer;">
            <div style="display: inline-block;">
              @if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif
            </div>
            <div style="display: inline-block;">
              <input id="rating_input{{$course->id}}" name="input-{{$course->id}}" class="rating rating-loading" value="@if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
            </div>
            <div style="display: inline-block;">
                @if(isset($reviewData[$course->id]))
                  {{count($reviewData[$course->id]['rating'])}} <i class="fa fa-group"></i>
                @else
                  0 <i class="fa fa-group"></i>
                @endif
            </div>
          </a>
          <span> &nbsp;&nbsp;Price: {{ $course->price }} Rs.</span>
          @if($course->price > 0)
            @if('true' == $isCourseRegistered)
              <a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" title="Paid">Paid</a>
            @else
              @if(is_object(Auth::user()))
                <a data-course_id="{{$course->id}}" class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="purchaseCourse(this);">Pay Now</a>
                <form id="purchaseCourse_{{$course->id}}" method="POST" action="{{ url('purchaseCourse')}}">
                  {{ csrf_field() }}
                  <input type="hidden" name="course_id" value="{{$course->id}}">
                  <input type="hidden" name="course_category_id" value="{{$course->course_category_id}}">
                  <input type="hidden" name="course_sub_category_id" value="{{$course->course_sub_category_id}}">
                </form>
              @else
                <a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" title="Pay Now" onClick="checkLogin();">Pay Now</a>
              @endif
            @endif
          @else
            <a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" title="Free">Free</a>
          @endif
        </div>
        <div class=" btn-group btn-group-justified btn-group-lg " role="group" aria-label="...">
            <div class="btn-group" role="group" title="Back">
                <a class=" btn btn-default btn-symboll" href="{{ url('courses')}}" title="Back">
                <span class=" fa fa-arrow-circle-left" aria-hidden="true"></span>
                </a>
            </div>
            <div class="btn-group" role="group" title="Videos">
                <button type="button" id="stars" class="btn-tab btn btn-primary"  href="#videoLectures" data-toggle="tab" >
                    <div class="">Videos</div>
                </button>
            </div>
            <div class="btn-group" role="group" title="About Course">
                <button type="button" id="favorites" class="btn-tab btn btn-default" href="#briefCourse" data-toggle="tab" >
                    <div class="">About</div>
                </button>
            </div>
            <div class="btn-group" role="group" title="Favourite">
              @if(0 == $course->price)
                @if(is_object(Auth::user()))
                  @if('true' == $isCourseRegistered)
                    <a class="btn btn-default voted-btn" id="favourite" data-favourite="true" onClick="registerCourse(this);" data-course_id="{{$courseId}}" title="Favourite" style="color: rgb(233, 30, 99);"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                  @else
                    <a class="btn btn-default voted-btn" id="favourite" data-favourite="false" onClick="registerCourse(this);" data-course_id="{{$courseId}}" title="Un Favourite"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                  @endif
                @else
                    <a class="btn btn-default voted-btn" id="favourite" data-favourite="false" onClick="checkLogin();" title="Un Favourite"> <i class="fa fa-star " aria-hidden="true"></i> </a>
                @endif
              @endif
            </div>
        </div>
        <div class="tab-content">
          <div id="videoLectures" class="tab-pane fade in active">
            <div class="col-sm-12 mrgn_20_top_btm ">
              @foreach($videos as $index => $video)
              <div class="row mrgn_30_top border_box padding_10">
                <div class="col-md-3" title="{{$video->name}}">
                  @if('true' == $isCourseRegistered || 1 == $video->is_free || $course->price <= 0 || is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email)
                    <a href="{{ url('episode')}}/{{$video->id}}">
                  @else
                    <a>
                  @endif
                    <h1 class="video_id">{{ $index + 1}}</h1>
                  </a>
                </div>
                <div class="col-md-9 menu">
                  <span class="divider">&#9679;</span>
                  <span class="running-time">Run Time- {{ gmdate('H:i:s', $video->duration)}}</span>
                  <h4 class="v_h4_subtitle" title="{{$video->name}}">
                    @if('true' == $isCourseRegistered || 1 == $video->is_free || $course->price <= 0 || is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email)
                      <a href="{{ url('episode')}}/{{$video->id}}">
                    @else
                      <a>
                    @endif
                    {{$video->name}}</a>
                  </h4>
                  <p class="more data-lg">{{$video->description}}</p>
                  <p class="more data-sm">{{$video->description}}</p>
                  <div class="collapse download_iteam" id="collapseExample_{{$video->id}}">
                    <div class="">
                      <a download title="pdf">
                        <i class="fa fa-file-pdf-o " aria-hidden="true"></i>
                      </a>
                      <a download title="video">
                        <i class="fa fa-video-camera " aria-hidden="true"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div id="briefCourse" class="tab-pane fade ">
           <section class="v_container ">
             <div class="container">
               <div class="row">
                 <h2 class="v_h2_title">Introduction</h2>
                 <hr class="border_bottom"/>
                 <p class="mrgn_20_top_btm">
                  {!! $course->description !!}
                </p>
                <h2 class="v_h2_title mrgn_20_top_btm">Meet The Auther</h2>
                <hr class="border_bottom"/>
                <div class="row">
                  <div class="col-md-4">
                    @if(!empty($course->author_image))
                      <img class="author-img img-responsive" src="{{ asset($course->author_image)}}" alt="Auther">
                    @else
                      <img class="author-img img-responsive" src="{{ asset('images/default_author_image.png')}}" alt="Auther">
                    @endif
                  </div>
                  <div class="col-md-8 meet-the-author-description">
                    <h3 class="meet-the-author-author-name">
                      <a>{{ $course->author }}</a>
                    </h3>
                    <div class="course-staff-info views-fieldset" data-module="views_fieldsets">
                      <div class="course-staff-info views-fieldset" data-module="views_fieldsets">
                        <p class="staff-title">{{ $course->author_introduction }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section><span>&nbsp;</span>
          </div>
        </div>
      </div>
      <div id="review-model-{{$course->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              &nbsp;&nbsp;&nbsp;
              <button class="close" data-dismiss="modal">×</button>
              <div class="form-group row ">
                <div  style="display: inline-block;">
                  @if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif
                </div>
                <div  style="display: inline-block;">
                  <input name="input-{{$course->id}}" class="rating rating-loading" value="@if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                </div>
                <div  style="display: inline-block;">
                  @if(isset($reviewData[$course->id]))
                    {{count($reviewData[$course->id]['rating'])}} <i class="fa fa-group"></i>
                  @else
                    0 <i class="fa fa-group"></i>
                  @endif
                </div>
                @if(is_object(Auth::user()))
                  <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$course->id}}">
                  @if(isset($reviewData[$course->id]) && isset($reviewData[$course->id]['rating'][Auth::user()->id]))
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
                @if(isset($reviewData[$course->id]))
                  @foreach($reviewData[$course->id]['rating'] as $userId => $review)
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
                    <input id="rating_input-{{$course->id}}-{{$userId}}" name="input-{{$course->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
      <div id="rating-model-{{$course->id}}" class="modal fade" role="dialog">
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
                  @if(isset($reviewData[$course->id]) && is_object(Auth::user()) && isset($reviewData[$course->id]['rating'][Auth::user()->id]))
                    <input id="rating_input-{{$course->id}}" name="input-{{$course->id}}" class="rating rating-loading" value="{{$reviewData[$course->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                  @else
                    <input id="rating_input-{{$course->id}}" name="input-{{$course->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                  @endif
                  Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$course->id])  && is_object(Auth::user()) && isset($reviewData[$course->id]['rating'][Auth::user()->id])) {{trim($reviewData[$course->id]['rating'][Auth::user()->id]['review'])}} @endif">
                  <br>
                  <input type="hidden" name="module_id" value="{{$course->id}}">
                  <input type="hidden" name="module_type" value="1">
                  <input type="hidden" name="rating_id" value="@if(isset($reviewData[$course->id]) && is_object(Auth::user()) && isset($reviewData[$course->id]['rating'][Auth::user()->id])) {{$reviewData[$course->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                  <button type="submit" class="pull-right">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    @else
      <ul class="nav nav-tabs nav-tabsVcourse">
        <li><a href="{{ url('courses')}}">Back</a></li>
      </ul>
      No Contents.
    @endif
  </div>
</section>
@stop
@section('footer')
	@include('footer.footer')
  <script src="{{ asset('js/star-rating.js') }}"></script>
  <script type="text/javascript">
  function purchaseCourse(ele){
    $.confirm({
      title: 'Confirmation',
      content: 'Do you want to purchase this course?',
      type: 'red',
      typeAnimated: true,
      buttons: {
        Ok: {
          text: 'Ok',
          btnClass: 'btn-red',
          action: function(){
            var courseId = parseInt($(ele).data('course_id'));
            document.getElementById('purchaseCourse_'+courseId).submit();
          }
        },
        Cancle: function () {
        }
      }
    });
  }

  function registerCourse(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var courseId = parseInt($(ele).data('course_id'));
    if( true == isNaN(userId)){
      $('#loginUserModel').modal();
    } else {
      $.ajax({
        method: "POST",
        url: "{{url('registerCourse')}}",
        data: {user_id:userId, course_id:courseId}
      })
      .done(function( msg ) {
        if('true' == msg){
          $(ele).css({'color':'#e91e63'})
        } else {
          $(ele).css({'color':'#000'})
        }
      });
    }
  }

  </script>
  <script>
  var showChar = 60;
  var ellipsestext = "...";
  var moretext = "Read more";
  var lesstext = "less";
  $('.more').each(function() {
    var content = $(this).html();

    if(content.length > showChar) {

      var c = content.substr(0, showChar);
      var h = content.substr(showChar-1, content.length - showChar);

      var html = c + '<span class="moreellipses" style="color:#01bafd; margin-left:5px;">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink" style="color:#01bafd";>' + moretext + '</a></span>';

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
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
  });

  $(document).ready(function() {
    $(".btn-tab.btn").click(function () {
      $(".btn-tab.btn").removeClass("btn-primary").addClass("btn-default");
      $(this).removeClass("btn-default").addClass("btn-primary");
    });
  });

</script>
@stop