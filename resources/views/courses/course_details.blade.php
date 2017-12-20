@extends('layouts.master')
@section('header-title')
  <title>Online Courses details by  Industrial Expert |Vchip-edu</title>
@stop
@section('header-css')
	@include('layouts.home-css')
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
          <img src="{{asset('images/header.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip course details" />
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
              @if('true' == $isCourseRegistered)
                <a class="btn btn-default voted-btn" id="favourite" data-favourite="true" onClick="registerCourse(this);" data-course_id="{{$courseId}}" title="Favourite" style="color: rgb(233, 30, 99);"> <i class="fa fa-star " aria-hidden="true"></i> </a>
              @else
                <a class="btn btn-default voted-btn" id="favourite" data-favourite="false" onClick="registerCourse(this);" data-course_id="{{$courseId}}" title="Un Favourite"> <i class="fa fa-star " aria-hidden="true"></i> </a>
              @endif
          </div>
      </div>

    <div class="tab-content">
      <div id="videoLectures" class="tab-pane fade in active">
        <div class="col-sm-12 mrgn_20_top_btm ">
          @foreach($videos as $index => $video)
          <div class="row mrgn_30_top border_box padding_10">
            <div class="col-md-3" title="{{$video->name}}">
              <a href="{{ url('episode')}}/{{$video->id}}">
              <h1 class="video_id">{{ $index + 1}}</h1>
              </a>
            </div>
            <div class="col-md-9 menu">
              <span class="divider">&#9679;</span>
              <span class="running-time">Run Time- {{ gmdate('H:i:s', $video->duration)}}</span>
              <h4 class="v_h4_subtitle" title="{{$video->name}}">
                <a href="{{ url('episode')}}/{{$video->id}}">{{$video->name}}</a>
              </h4>
              <p class="more data-lg">{{$video->description}}</p>
              <p class="more data-sm">{{$video->description}}</p>
              <span class="v_download" title="Download">
                <a class="btn btn-primary is-bold" role="button" data-toggle="collapse" href="#collapseExample_{{$video->id}}" aria-expanded="false" aria-controls="collapseExample">
                Download</a>
              </span>
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
  <script type="text/javascript">

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