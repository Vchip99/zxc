@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/box.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/workshop.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/clg_service.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  @media(max-width: 412px){
    #topic .workshop_detail label {
     width:120px;
    }
  }
  .block-with-text {
     text-overflow: ellipsis !important;
    overflow: hidden !important;
    white-space: nowrap !important;
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
          <img src="{{asset('images/corporate-bg.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2 class="animated bounceInLeft">Digital Education</h2>
        </div>
    </div>
  </section>
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
<section id="topic" class="v_container v_bg_grey">
  <div class="container">
    <div class="row">
      <div class="col-md-12 workshop_detail v_bg_white">
        <input id="tab1" type="radio" name="tabs" checked>
        <label for="tab1">About speaker</label>
        <input id="tab2" type="radio" name="tabs">
        <label for="tab2">Topic</label>
        <input id="tab3" type="radio" name="tabs">
        <label for="tab3">video</label>
        <section id="content1">
          <div class="col-md-12 workshop_tital">
              <h2 class="v_h3_title">About speaker</h2>
           </div>
            <div class="col-md-12 workshop_detail">
              <div class="col-sm-4 col-sm-push-8 detail_img ">
                <img src="{{ asset($motivationalSpeechDetail->about_image) }}" class="img-responsive" alt="motivational speech" />
              </div>
              <div class="col-sm-8 col-sm-pull-4">{{ $motivationalSpeechDetail->about }}</div>
            </div>
        </section>
        <section id="content2">
          <div class="col-md-12 workshop_tital">
              <h2 class="v_h3_title">Topic</h2>
           </div>
            <div class="col-md-12 workshop_detail">
               <ul class="ul_custom">{{ $motivationalSpeechDetail->topics }}
               </ul>
            </div>
        </section>
        <section id="content3">
            <div class="col-md-12 workshop_tital">
              <h2 class="v_h3_title">Videos</h2>
            </div>
            <div class="col-md-12 workshop_detail">
              @if(count($videos))
                @foreach($videos as $video)
                  <div class="col-md-4 col-sm-6 video-container">
                    <div class="vid">
                      {!! $video->video_path !!}
                    </div>
                  </div>
                @endforeach
              @endif
            </div>
        </section>


      </div>
    </div>
  </div>
</section>
<section id="" class="v_container ">
  <div class="container">
    <div class="row">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">detail about how the program will arrange</h2>
      </div>
      <div class="col-md-12 workshop_detail">
        {{ $motivationalSpeechDetail->program_details }}
      </div>
    </div>
  </div>
</section>
<!-- Organise workshop -->
<section id="" class="v_container ">
  <div class="container">
    <div class="row">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">Organise Motivational Speach in your College or Industry </h2>
      </div>
      <div class="col-md-12 workshop_detail">
   <div class="col-md-4">
        <div class="hover-img" data-toggle="modal" data-target="#collegeModal">
          <img src="{{asset('images/workshop/college.jpg')}}" class="img-responsive" alt="workshop" />
          <figcaption>
            <h3>COLLEGE WORKSHOP</h3>
            <p>To Organise College  Motivational Speach</p>
          </figcaption>
          <a><b>Click </b></a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="hover-img" data-toggle="modal" data-target="#corporateModal">
          <img src="{{asset('images/workshop/corporate.jpg')}}" class="img-responsive" alt="workshop" />
          <figcaption>
            <h3>Corporate Workshop</h3>
            <p>To Organise Industry  Motivational Speach</p>
          </figcaption>
          <a><b>Click </b></a>
        </div>
    </div>
      </div>
    </div>
  </div>

  <!-- clg -->
  <div class="modal fade" id="collegeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="cursor: pointer !important;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="{{ url('motivationalspeechquery') }}">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="myModalLabel">College</h4>
        </div>
        <div class="modal-body">
              <div class="form-group">
                  <label for="name"> Name</label>
                  <input type="text" class="form-control" name="name" placeholder="Enter name" required="required" />
              </div>
              <div class="form-group">
                  <label for="email"> Email Address</label>
                  <div class="input-group">
                      <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                      </span>
                      <input type="email" class="form-control" name="email" placeholder="Enter email" required="required" /></div>
              </div>
              <div class="form-group">
                  <label for="name"> Contact Number</label>
                  <input type="text" class="form-control" name="mobile" name="mobile" placeholder="Mobile Number" required="required" />
              </div>
              <div class="form-group">
                  <label for="name">Organization Name</label>
                  <input type="text" class="form-control" name="org_name" placeholder="Enter Organization Name"  />
              </div>
               <div class="form-group">
                  <label for="name">Query</label>
                  <textarea name="text_message" id="message" class="form-control" rows="9" cols="25" required="required"
                      placeholder="Message"></textarea>
              </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  <!-- corporate -->
  <div class="modal fade" id="corporateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="cursor: pointer !important;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="{{ url('motivationalspeechquery') }}">
        {{ csrf_field() }}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="myModalLabel">Industry</h4>
        </div>
        <div class="modal-body">
              <div class="form-group">
                  <label for="name"> Name</label>
                  <input type="text" class="form-control" name="name" placeholder="Enter name" required="required" />
              </div>
              <div class="form-group">
                  <label for="email"> Email Address</label>
                  <div class="input-group">
                      <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                      </span>
                      <input type="email" class="form-control" name="email" placeholder="Enter email" required="required" /></div>
              </div>
              <div class="form-group">
                  <label for="name"> Contact Number</label>
                  <input type="text" class="form-control" name="mobile" name="mobile" placeholder="Mobile Number" required="required" />
              </div>
              <div class="form-group">
                  <label for="name">Organization Name</label>
                  <input type="text" class="form-control" name="org_name" placeholder="Enter Organization Name"  />
              </div>
               <div class="form-group">
                  <label for="name">Query</label>
                  <textarea name="text_message" id="message" class="form-control" rows="9" cols="25" required="required"
                      placeholder="Message"></textarea>
              </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" >Submit</button>
        </div>
          </form>
      </div>
    </div>
  </div>
</section>
<!-- end -->

<section id="" class="v_container v_bg_grey">
  <div class="container">
    <div class="row v_bg_white ">
     <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">Other speakers</h2>
      </div>
      <div class="col-md-12 workshop_detail">
        <div class="MultiCarousel " data-items="1,3,3,4" data-slide="1" id="MultiCarousel"  data-interval="1000">
          <div class="MultiCarousel-inner">
            @if(count($motivationalSpeechDetails) > 0)
              @foreach($motivationalSpeechDetails as $motivationalSpeech)
                <div class="item">
                    <div class="more-iteam " >
                      <p>
                       <img src="{{ asset($motivationalSpeech->about_image) }}" class="img-responsive" alt="speaker Image" />
                      </p>
                      <div class="caption">
                        <div class="blur"></div>
                        <div class="caption-text">
                          <h3 class="ellipsed" title="{{ $motivationalSpeech->name }}">{{ $motivationalSpeech->name }} </h3>
                          <hr class="hr" />
                          <p >{!!  substr($motivationalSpeech->about, 0, 40) !!} ...</p>
                        </div>
                      </div>
                      <a href="{{ url('motivationalSpeechDetails')}}/{{$motivationalSpeech->id }}"> Read More</a>
                    </div>
                </div>
              @endforeach
            @endif
          </div>
          <button class="btn btn-primary leftLst"><</button>
          <button class="btn btn-primary rightLst">></button>
        </div>
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
  <script type="text/javascript">
  $(document).ready(function () {
    var itemsMainDiv = ('.MultiCarousel');
    var itemsDiv = ('.MultiCarousel-inner');
    var itemWidth = "";

    $('.leftLst, .rightLst').click(function () {
        var condition = $(this).hasClass("leftLst");
        if (condition)
            click(0, this);
        else
            click(1, this)
    });

    ResCarouselSize();




    $(window).resize(function () {
        ResCarouselSize();
    });

    //this function define the size of the items
    function ResCarouselSize() {
        var incno = 0;
        var dataItems = ("data-items");
        var itemClass = ('.item');
        var id = 0;
        var btnParentSb = '';
        var itemsSplit = '';
        var sampwidth = $(itemsMainDiv).width();
        var bodyWidth = $('body').width();
        $(itemsDiv).each(function () {
            id = id + 1;
            var itemNumbers = $(this).find(itemClass).length;
            btnParentSb = $(this).parent().attr(dataItems);
            itemsSplit = btnParentSb.split(',');
            $(this).parent().attr("id", "MultiCarousel" + id);


            if (bodyWidth >= 1200) {
                incno = itemsSplit[3];
                itemWidth = sampwidth / incno;
            }
            else if (bodyWidth >= 992) {
                incno = itemsSplit[2];
                itemWidth = sampwidth / incno;
            }
            else if (bodyWidth >= 768) {
                incno = itemsSplit[1];
                itemWidth = sampwidth / incno;
            }
            else {
                incno = itemsSplit[0];
                itemWidth = sampwidth / incno;
            }
            $(this).css({ 'transform': 'translateX(0px)', 'width': itemWidth * itemNumbers });
            $(this).find(itemClass).each(function () {
                $(this).outerWidth(itemWidth);
            });

            $(".leftLst").addClass("over");
            $(".rightLst").removeClass("over");

        });
    }


    //this function used to move the items
    function ResCarousel(e, el, s) {
        var leftBtn = ('.leftLst');
        var rightBtn = ('.rightLst');
        var translateXval = '';
        var divStyle = $(el + ' ' + itemsDiv).css('transform');
        var values = divStyle.match(/-?[\d\.]+/g);
        var xds = Math.abs(values[4]);
        if (e == 0) {
            translateXval = parseInt(xds) - parseInt(itemWidth * s);
            $(el + ' ' + rightBtn).removeClass("over");

            if (translateXval <= itemWidth / 2) {
                translateXval = 0;
                $(el + ' ' + leftBtn).addClass("over");
            }
        }
        else if (e == 1) {
            var itemsCondition = $(el).find(itemsDiv).width() - $(el).width();
            translateXval = parseInt(xds) + parseInt(itemWidth * s);
            $(el + ' ' + leftBtn).removeClass("over");

            if (translateXval >= itemsCondition - itemWidth / 2) {
                translateXval = itemsCondition;
                $(el + ' ' + rightBtn).addClass("over");
            }
        }
        $(el + ' ' + itemsDiv).css('transform', 'translateX(' + -translateXval + 'px)');
    }

    //It is used to get some elements from btn
    function click(ell, ee) {
        var Parent = "#" + $(ee).parent().attr("id");
        var slide = $(Parent).attr("data-slide");
        ResCarousel(ell, Parent, slide);
    }

});
</script>
@stop