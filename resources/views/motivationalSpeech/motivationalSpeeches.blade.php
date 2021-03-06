@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/box.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/clg_service.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
  @media(max-width: 412px){
    #topic .workshop_detail label {
     width:120px;
    }
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
          <img src="{{asset('images/course.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Motivational Speech" />
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
          <select id="category" class="form-control" name="category" data-toggle="tooltip" title="Category" onChange="selectWorkshop(this);" required>
            <option value="0">Select Speacker</option>
            @if(count($motivationalSpeechCategories) > 0)
              @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
                <option value="{{$motivationalSpeechCategory->id}}">{{$motivationalSpeechCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>

      </div>
      <div class="col-sm-9 col-sm-push-3 ">
        <div class="row info" id="workshop">
          @if(count($motivationalSpeechDetails) > 0)
            @foreach($motivationalSpeechDetails as $motivationalSpeech)
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 ">
                <div class="cuadro_intro_hover " style="background-color:#cccccc;">
                  <p style="text-align:center;">
                    <img src="{{ asset($motivationalSpeech->about_image) }}" class="img-responsive" alt="workshop" />
                  </p>
                  <div class="caption">
                    <div class="blur"></div>
                    <div class="caption-text">
                      <h3 class="ellipsed" title="{{ $motivationalSpeech->name }}"><a href="{{ url('motivationalSpeechDetails')}}/{{$motivationalSpeech->id }}">{{ $motivationalSpeech->name }}</a></h3>
                      <p class="block-with-text">{!!  substr($motivationalSpeech->about, 0, 60) !!} ...</p>
                    </div>
                  </div>
                </div>
                <p class="link"><a href="{{ url('motivationalSpeechDetails')}}/{{$motivationalSpeech->id }}"> Read More</a></p>
              </div>
            @endforeach
          @else
            No motivational speeches are available.
          @endif
        </div>
        <div   id="pagination">
          {{ $motivationalSpeechDetails->links() }}
        </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
          <h4 class="v_h4_subtitle"> Sort By</h4>
          <div class="mrgn_20_top_btm" >
            <select id="category" class="form-control" name="category" data-toggle="tooltip" title="Category" onChange="selectWorkshop(this);" required>
              <option value="0">Select Speacker</option>
              @if(count($motivationalSpeechCategories) > 0)
                @foreach($motivationalSpeechCategories as $motivationalSpeechCategory)
                  <option value="{{$motivationalSpeechCategory->id}}">{{$motivationalSpeechCategory->name}}</option>
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
  function selectWorkshop(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getMotivationalSpeechesByCategory')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        divWorkshop = document.getElementById('workshop');
        divWorkshop.innerHTML = '';
        document.getElementById('pagination').innerHTML = '';
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
            var firstDiv = document.createElement('div');
            firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
            var secondDiv = document.createElement('div');
            secondDiv.className = "cuadro_intro_hover";
            secondDiv.setAttribute("style","background-color:#cccccc;");
            secondDiv.innerHTML = '';
            var imgUrl = "{{ asset('') }}" + obj['about_image'];
            secondDiv.innerHTML +='<p style="text-align:center;"><img src="'+imgUrl+'" alt="workshop" class="img-responsive"/></p>';
            var url = "{{ url('motivationalSpeechDetails')}}/"+  obj['id'];
            var about = obj['about'];
            secondDiv.innerHTML +='<div class="caption"><div class="blur"></div><div class="caption-text"><h3 class="ellipsed" title="'+ obj['name'] +'"><a href="'+ url +'">'+ obj['name'] +'</a></h3><p class="block-with-text">'+ about.substr(0, 60) +'...</p></div></div>'
            firstDiv.appendChild(secondDiv);
            var pEle = document.createElement('p');
            pEle.className = "link";
            pEle.innerHTML ='<a href="'+ url +'"> Read More</a>';
            firstDiv.appendChild(pEle);
            divWorkshop.appendChild(firstDiv);
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