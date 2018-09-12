@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
   <link href="{{ asset('css/box.css')}}" rel="stylesheet"/>
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
        <img src="{{asset('images/gate.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Exam" />
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
      <div class="col-sm-3 hidden-div ">
        <h4 class="v_h4_subtitle"> Filter By</h4>
        <div class="dropdown mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category_id" name="category_id" title="Category" onchange="showSubCategories(this);">
            <option>Select Category ...</option>
            @if(count($testCategories) > 0)
              @foreach($testCategories as $testCategory)
                @if($catId == $testCategory->id)
                  <option value="{{$testCategory->id}}" selected>{{$testCategory->name}}</option>
                @else
                  <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                @endif
              @endforeach
            @endif
          </select>
        </div>

      </div>
      <div class="col-sm-9 col-sm-push-3">
        <div class="row" id="testSubCategories">
          @if(count($testSubCategories) > 0)
            @foreach($testSubCategories as $testSubCategory)
              <div class="col-lg-6 col-md-6 col-sm-6 small-img">
                <a href="{{url('getTest')}}/{{ $testSubCategory->id }}" class="btn-link" title="Start Test">
                  <div class="vchip_product_itm text-left">
                    <figure title="{{$testSubCategory->name}}">
                        <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                    </figure>
                    <ul class="vchip_categories list-inline">
                      <li>{{$testSubCategory->name}}</li>
                    </ul>
                    <div class="vchip_product_content">
                      <p class="mrgn_20_top">Start Test <i class="fa fa-angle-right" aria-hidden="true"></i>
                      </p>
                    </div>
                  </div>
                </a>
              </div>
              @endforeach
          @else
            No tests are available.
          @endif
        </div>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
          <h4 class="v_h4_subtitle"> Filter By</h4>
          <div class="dropdown mrgn_20_top_btm" id="cat">
            <select class="form-control" id="category_id" name="category_id" title="Category" onchange="showSubCategories(this);">
              <option>Select Category ...</option>
              @if(count($testCategories) > 0)
                @foreach($testCategories as $testCategory)
                  @if($catId == $testCategory->id)
                    <option value="{{$testCategory->id}}" selected>{{$testCategory->name}}</option>
                  @else
                    <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                  @endif
                @endforeach
              @endif
            </select>
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
    <script type="text/javascript">
      function showSubCategories(ele){
        id = parseInt($(ele).val());
        if( 0 < id ){
          $.ajax({
            method: "POST",
            url: "{{url('getSubCategories')}}",
            data: {id:id}
          })
          .done(function( msg ) {
            var subcatDiv = document.getElementById('testSubCategories');
            subcatDiv.innerHTML = '';
            if( 0 < msg.length){
              $.each(msg, function(idx, obj) {
                var mainDiv = document.createElement('div');
                mainDiv.className = 'col-lg-6 col-md-6 col-sm-6 small-img';

                var ancDiv = document.createElement('a');
                contentUrl = "{{url('getTest')}}/"+obj.id;
                ancDiv.className = 'btn-link';
                ancDiv.setAttribute('href', contentUrl);

                var productDiv = document.createElement('div');
                productDiv.className = "vchip_product_itm text-left";

                var figureDiv = document.createElement('figure');
                figureDiv.setAttribute('title', obj.name);
                var imageDiv = document.createElement('div');
                imageUrl = "{{asset('')}}"+ obj.image_path;
                imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" width="800"height="400" alt="test "/>';
                figureDiv.appendChild(imageDiv);
                productDiv.appendChild(figureDiv);

                var eleUl = document.createElement('ul');
                eleUl.className="mrgn_5_top vchip_categories list-inline";
                eleUl.innerHTML='<li>'+ obj.name +'</li>';
                productDiv.appendChild(eleUl);

                var contentDiv = document.createElement('div');
                contentDiv.className ='vchip_product_content';
                // contentUrl = "{{url('getTest')}}/"+obj.id;
                contentDiv.innerHTML = '<p class="mrgn_20_top">Start Test <i class="fa fa-angle-right"aria-hidden="true"></i></p>';
                productDiv.appendChild(contentDiv);
                ancDiv.appendChild(productDiv);
                mainDiv.appendChild(ancDiv);
                subcatDiv.appendChild(mainDiv);
                });
            }
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