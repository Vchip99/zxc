@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |V-edu</title>
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
        <img src="{{asset('images/gate.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip Gate Exam" />
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
      <div class="col-sm-3 ">
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
      <div class="col-sm-9">
        <div class="row" id="testSubCategories">
          @if(count($testSubCategories) > 0)
            @foreach($testSubCategories as $testSubCategory)
              <div class="col-lg-6 col-md-6 col-sm-6 slideanim small-img">
                  <div class="vchip_product_itm text-left">
                    <figure title="{{$testSubCategory->name}}">
                      <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                    </figure>
                    <ul class="vchip_categories list-inline">
                      <li>{{$testSubCategory->name}}</li>
                    </ul>
                    <div class="vchip_product_content">
                      <p class="mrgn_20_top"><a href="{{url('getTest')}}/{{ $testSubCategory->id }}" class="btn-link" title="Start Test">Start Test <i
                        class="fa fa-angle-right"
                        aria-hidden="true"></i></a>
                      </p>
                    </div>
                  </div>
                </div>
              @endforeach
          @else
            No tests are available.
          @endif
          </div>
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
                contentUrl = "{{url('getTest')}}/"+obj.id;
                contentDiv.innerHTML = '<p class=""><a href="'+ contentUrl +'" class="btn-link">Start Test <iclass="fa fa-angle-right"aria-hidden="true"></i></a></p>';
                productDiv.appendChild(contentDiv);
                mainDiv.appendChild(productDiv);
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