@extends('layouts.master')
@section('header-title')
  <title>Virtual Placement |V-edu</title>
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
          <img src="{{asset('images/placement-drive-bg.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Virtual Placement Drive" />
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
<section id="" class="v_container ">
   <div class="container">
     <div class="row">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h2_title">Virtual Placement Drive by Vchip</h2>
      </div>
      <div class="col-md-12 workshop_detail">
        <div class="col-sm-4 col-sm-push-8 detail_img ">
          <img src="{{ asset($virtualplacementdrive->about_image) }}" class="img-responsive" alt="about_image" />
        </div>
        <div class="col-sm-8 col-sm-pull-4">
          <ul class="ul_custom">
            {!! $virtualplacementdrive->about !!}
          </ul>
        </div>
      </div>
     </div>
   </div>
</section>
<section id="topic" class="v_container v_bg_grey">
   <div class="container">
     <div class="row">
        <div class="col-md-12 workshop_detail v_bg_white" />
          <div class="panel with-nav-tabs panel-default" style="border-radius: 0px;">
            <div class="panel-heading" style="margin-left: 0px; ">
                    <ul class="nav nav-tabs" style="margin-left: 0px; ">
                        <li class="active"><a href="#onlineTest" data-toggle="tab">Online Test</a></li>
                        <li><a href="#gd" data-toggle="tab">GD</a></li>
                        <li><a href="#pi" data-toggle="tab">TI/PI</a></li>
                        <li><a href="#hr" data-toggle="tab">HR</a></li>
                        <li><a href="#suggest" data-toggle="tab">Suggestions</a></li>
                    </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="onlineTest">
                        <div class="col-md-12 workshop_tital">
                          <h2 class="v_h2_title">Online Test</h2>
                        </div>
                        <div class="col-md-12 workshop_detail">
                          <ul class="ul_custom">
                            {!! $virtualplacementdrive->online_test !!}
                          </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="gd">
                        <div class="col-md-12 workshop_tital">
                          <h2 class="v_h2_title">Group Discussion</h2>
                        </div>
                        <div class="col-md-12 workshop_detail">
                          <ul class="ul_custom">
                            {!! $virtualplacementdrive->gd !!}
                          </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pi">
                        <div class="col-md-12 workshop_tital">
                          <h2 class="v_h2_title">TI/PI</h2>
                        </div>
                        <div class="col-md-12 workshop_detail">
                          <ul class="ul_custom">
                            {!! $virtualplacementdrive->pi !!}
                          </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="hr">
                        <div class="col-md-12 workshop_tital">
                          <h2 class="v_h2_title">HR</h2>
                        </div>
                        <div class="col-md-12 workshop_detail">
                          <ul class="ul_custom">
                            {!! $virtualplacementdrive->hr !!}
                          </ul>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="suggest">
                        <div class="col-md-12 workshop_tital">
                          <h2 class="v_h2_title">SUGGESTIONS</h2>
                        </div>
                        <div class="col-md-12 workshop_detail">
                          <ul class="ul_custom">
                            {!! $virtualplacementdrive->suggestions !!}
                          </ul>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<section id="benifit" class="v_container ">
   <div class="container">
     <div class="row">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h2_title">how the program will arrange</h2>
      </div>
      <div class="col-md-12 workshop_detail">
        <div class="col-sm-4 detail_img">
          <img src="{{ asset($virtualplacementdrive->program_arrangement_image) }}" class="img-responsive" alt="workshop" />
        </div>
        <div class="col-sm-8">
          <ul class="ul_custom">
            {!! $virtualplacementdrive->program_arrangement !!}
          </ul>
        </div>
      </div>
     </div>
   </div>
</section>
<section id="benifit" class="v_container v_bg_grey">
   <div class="container">
     <div class="row">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h2_title">Features and Advantages</h2>
      </div>
      <div class="col-md-12 workshop_detail">
          <ul class="ul_custom">
            {!! $virtualplacementdrive->advantages !!}
          </ul>
      </div>
     </div>
   </div>
</section>
<section id="" class="v_container gal-container">
  <div class="container">
    <div class="row v_bg_white ">
     <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">Gallery by Vchip</h2>
      </div>
      <div class="col-md-12 workshop_detail">
        <div class="MultiCarousel " data-items="1,3,3,4" data-slide="1" id="MultiCarousel"  data-interval="1000">
          <div class="MultiCarousel-inner">
            <div class="item">
                <div class="more-iteam " >
                  <p>
                   <a href="#" data-toggle="modal" data-target="#1">
                    <img src="{{ asset('images/placement/gallery/img-1.jpg')}}" class="thumbnail img-responsive" alt="placement" />
                  </a>
                  </p>
                </div>
            </div>
            <div class="item">
                <div class="more-iteam " >
                  <p>
                   <img src="{{ asset('images/placement/gallery/img-2.jpg')}}" class="thumbnail img-responsive" alt="placement" />
                  </p>
                </div>
            </div>
            <div class="item">
                <div class="more-iteam " >
                  <p>
                   <img src="{{ asset('images/placement/gallery/img-4.png')}}" class="thumbnail img-responsive" alt="placement" />
                  </p>
                </div>
            </div>
            <div class="item">
                <div class="more-iteam " >
                  <p>
                   <img src="{{ asset('images/placement/gallery/img-5.jpg')}}" class="thumbnail img-responsive" alt="placement" />
                  </p>
                </div>
            </div>
            <div class="item">
                <div class="more-iteam " >
                  <p>
                   <img src="{{ asset('images/placement/gallery/img-3.jpg')}}" class="thumbnail img-responsive" alt="placement" />
                  </p>
                </div>
            </div>
          </div>
          <button class="btn btn-primary leftLst"><</button>
          <button class="btn btn-primary rightLst">></button>
        </div>
      </div>
    </div>
  </div>
</section>
<div tabindex="-1" class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
    <button class="close" type="button" data-dismiss="modal">×</button>
    <!-- <h3 class="modal-title">Heading</h3> -->
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
   </div>
  </div>
</div>

<!-- testimonial -->
<section id="" class="v_container v_bg_grey">
  <div class="container">
    <div class="row v_bg_white">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">Our Client's Testimonial</h2>
      </div>
        <div class="col-md-12 workshop_detail">
        <div id="testimonial4" class="carousel slide testimonial4_indicators testimonial4_control_button thumb_scroll_x swipe_x" data-ride="carousel" data-pause="hover" data-interval="5000" data-duration="2000">

          <ol class="carousel-indicators">
            <li data-target="#testimonial4" data-slide-to="0" class="active"></li>
            <li data-target="#testimonial4" data-slide-to="1"></li>
            <li data-target="#testimonial4" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner" role="listbox">
            <div class="item active">
              <div class="testimonial4_slide">
                <img src="{{ asset('images/user/amol_sir.jpg')}}" class="img-circle img-responsive" />
                <p>Vchip has conducted virtual placement drive at our college HVPM COET. We founded it very helpful to our students. It’s improve the performance of our students at the time of actual placement drive. Thanks to Vchip for a such great initiative. </p>
                <h4>Amol Karmarkar (TPO of HVPM COET) </h4>
              </div>
            </div>
            <div class="item">
              <div class="testimonial4_slide">
                <img src="{{ asset('images/user/vinay.jpg')}}" class="img-circle img-responsive" />
                <p>Team from Vchip was very supportive. At the last they given suggestions, which helped me to overcome the weakness of mine. Also, the session about current trends and demands of industries helped me to finalize the technology for my career.</p>
                <h4>Vinay Motghare (Student of G. H. Raisoni)</h4>
              </div>
            </div>
            <div class="item">
              <div class="testimonial4_slide">
                <img src="{{ asset('images/user/user.png')}}" class="img-circle img-responsive" />
                <p>Great work by Vchip. it helped our students to perform well at the time of actual placement drive.</p>
                <h4>Pournima Kawalkar (Lecturer at MGM COET)</h4>
              </div>
            </div>
          </div>
          <a class="left carousel-control" href="#testimonial4" role="button" data-slide="prev">
            <span class="fa fa-chevron-left"></span>
          </a>
          <a class="right carousel-control" href="#testimonial4" role="button" data-slide="next">
            <span class="fa fa-chevron-right"></span>
          </a>
        </div>
        </div>
    </div>
  </div>
</section>
<!-- contact -->
<section id="" class="v_container ">
  <div class="container">
    <div class="row v_bg_white">
      <div class="col-md-12 workshop_tital">
        <h2 class="v_h3_title">Contact Us For Virtual Placement</h2>
      </div>
        <div class="col-md-12 workshop_detail">
          <form method="POST" action="{{ url('virtualplacementquery') }}">
          {{ csrf_field() }}
            <div class="row">
              <div class="col-md-6">
                  <div class="form-group">
                      <label for="name">
                          Name</label>
                      <input type="text" class="form-control" name="name" placeholder="Enter Name" required="required" />
                  </div>
                  <div class="form-group">
                      <label for="email">
                          Email Address</label>
                      <div class="input-group">
                          <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                          </span>
                          <input type="email" class="form-control" name="email" placeholder="Enter Email" required="required" /></div>
                  </div>
                  <div class="form-group">
                      <label for="name">
                          Contact Number</label>
                      <input type="text" class="form-control" name="mobile" name="mobile" placeholder="Mobile Number" required="required" />

                  </div>
                  <div class="form-group">
                      <label for="name">
                          Organization Name</label>
                      <input type="text" class="form-control" name="org_name" placeholder="Enter Organization Name"  />
                  </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                      <label for="subject"> How you got to know about Vchipedu</label>
                      <select id="subject" name="subject" class="form-control" required="required">
                          <option value="Search Engine">Search Engine</option>
                          <option value="Referred By Friend">Referred By Friend</option>
                          <option value="Workshop/Training">Workshop/Training</option>
                          <option value="SMS/ Mail">SMS/ Mail</option>
                          <option value="Facebook/ YouTube">Facebook/ YouTube</option>
                          <option value="Blogs/ Forums">Blogs/ Forums</option>
                          <option value="Posters/ Pamphlets / Ads">Posters/ Pamphlets / Ads</option>
                          <option value="Newspaper">Newspaper</option>
                          <option value="Other">Other</option>
                      </select>
                </div>
                <div class="form-group">
                    <label for="name">
                        Query</label>
                    <textarea name="text_message" name="id" class="form-control" rows="9" cols="25" required="required"
                        placeholder="Message"></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary pull-right" id="btnContactUs"> Send Message</button>
              </div>
            </div>
          </form>
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
<script type="text/javascript">
  $(document).ready(function() {
    $('.thumbnail').click(function(){
          $('.modal-body').empty();
        var title = $(this).parent('a').attr("title");
        $('.modal-title').html(title);
        $($(this).parents('div').html()).appendTo('.modal-body');
        $('#myModal').modal({show:true});
    });
  });
</script>
@stop