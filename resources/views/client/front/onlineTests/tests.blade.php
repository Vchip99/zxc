@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
   @include('layouts.home-css')
   <link href="{{ asset('css/box.css')}}" rel="stylesheet"/>
  <style type="text/css">
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
  @include('client.front.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/gate.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
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
          <select class="form-control" id="category_id" name="category_id" onchange="showSubCategories(this);" title="Category">
            <option>Select Category ...</option>
            @if(count($testCategories) > 0)
              @if(Auth::guard('clientuser')->user())
                @foreach($testCategories as $testCategory)
                  @if(in_array($testCategory->client_institute_course_id, $userCategoryPermissionIds))
                    <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                  @endif
                @endforeach
              @else
                @foreach($testCategories as $testCategory)
                    <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
                @endforeach
              @endif
            @endif
          </select>
        </div>
      </div>
      <div class="col-sm-9">
        <div class="row" id="testSubCategories">
          @if(count($testSubCategories) > 0)
            @if(Auth::guard('clientuser')->user())
              @foreach($testSubCategories as $testSubCategory)
                @if(in_array($testSubCategory->client_institute_course_id, $userSubCategoryPermissionIds))
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
                @endif
              @endforeach
            @else
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
            @endif
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
	@include('footer.client-footer')
  <script type="text/javascript">
      function showSubCategories(ele){
        var id = parseInt($(ele).val());
        var userId = parseInt(document.getElementById('user_id'));
        if( 0 < id ){
          $.ajax({
            method: "POST",
            url: "{{url('getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion')}}",
            data: {id:id}
          })
          .done(function( msg ) {
            var subcatDiv = document.getElementById('testSubCategories');
            subcatDiv.innerHTML = '';
            if( 0 < msg['sub_categories'].length){
              if(userId > 0){
                $.each(msg['sub_categories'], function(idx, obj) {
                  if(msg['sub_category_permission'].length > 0 && true == msg['sub_category_permission'].indexOf(obj.id) > -1){
                    var mainDiv = document.createElement('div');
                    mainDiv.className = 'col-sm-6';

                    var productDiv = document.createElement('div');
                    productDiv.className = "vchip_product_item";
                    var imageDiv = document.createElement('div');
                    imageUrl = "{{ asset('')}}"+ obj.image_path;
                    imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" width="800"height="400" alt="test "/>';
                    productDiv.appendChild(imageDiv);

                    var eleUl = document.createElement('ul');
                    eleUl.className="mrgn_5_top vchip_categories list-inline";
                    eleUl.innerHTML='<li>'+ obj.name +'</li>';
                    productDiv.appendChild(eleUl);

                    var contentDiv = document.createElement('div');
                    contentDiv.className ='vchip_product_content';
                    contentUrl = "{{url('getTest')}}/"+obj.id;
                    contentDiv.innerHTML = '<p class=""><a href="'+ contentUrl +'" class="btn-link">Start Test <i class="fa fa-angle-right"aria-hidden="true"></i></a></p>';
                    productDiv.appendChild(contentDiv);
                    mainDiv.appendChild(productDiv);
                    subcatDiv.appendChild(mainDiv);
                  }
                });
              } else {
                $.each(msg['sub_categories'], function(idx, obj) {
                  var mainDiv = document.createElement('div');
                  mainDiv.className = 'col-sm-6';

                  var productDiv = document.createElement('div');
                  productDiv.className = "vchip_product_item";
                  var imageDiv = document.createElement('div');
                  imageUrl = "{{ asset('')}}"+ obj.image_path;
                  imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" width="800"height="400" alt="test "/>';
                  productDiv.appendChild(imageDiv);

                  var eleUl = document.createElement('ul');
                  eleUl.className="mrgn_5_top vchip_categories list-inline";
                  eleUl.innerHTML='<li>'+ obj.name +'</li>';
                  productDiv.appendChild(eleUl);

                  var contentDiv = document.createElement('div');
                  contentDiv.className ='vchip_product_content';
                  contentUrl = "{{url('getTest')}}/"+obj.id;
                  contentDiv.innerHTML = '<p class=""><a href="'+ contentUrl +'" class="btn-link">Start Test <i class="fa fa-angle-right"aria-hidden="true"></i></a></p>';
                  productDiv.appendChild(contentDiv);
                  mainDiv.appendChild(productDiv);
                  subcatDiv.appendChild(mainDiv);
                });
              }
            } else {
              subcatDiv.innerHTML = 'No sub categories are available.';
            }
          });
        }
      }
    </script>
@stop