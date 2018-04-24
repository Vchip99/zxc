@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |Vchip-edu</title>
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
      @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      <div class="col-sm-3 ">
        <h4 class="v_h4_subtitle"> Filter By</h4>
        <div class="dropdown mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category_id" name="category_id" onchange="showSubCategories(this);" title="Category">
            <option>Select Category</option>
            @if(count($testCategories) > 0)
              @foreach($testCategories as $testCategory)
                  <option value="{{$testCategory->id}}">{{$testCategory->name}}</option>
              @endforeach
            @endif
            @if(count($payableTestCategories) > 0)
              @foreach($payableTestCategories as $payableTestCategory)
                  <option value="{{$payableTestCategory->id}}">{{$payableTestCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="col-sm-9">
        <div class="row" id="testSubCategories">
          @if(count($testSubCategories) > 0)
            @foreach($testSubCategories as $testSubCategory)
              <div class="col-lg-6 col-md-6 col-sm-6 small-img">
                  <div class="vchip_product_itm text-left">
                    <a href="{{url('getTest')}}/{{ $testSubCategory->id }}" class="btn-link">
                      <figure title="{{$testSubCategory->name}}">
                        <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                      </figure>
                      <ul class="vchip_categories list-inline">
                        <li>{{$testSubCategory->name}}</li>
                      </ul>
                    </a>
                    <div class="categoery" style="padding-left: 18px;">
                      <span style="color: #e91e63;">Price: {{$testSubCategory->price}} Rs.</span>
                      @if(is_object(Auth::guard('clientuser')->user()))
                        @if( isset($purchasedSubCategories[$testSubCategory->category_id]) && true == in_array($testSubCategory->id, $purchasedSubCategories[$testSubCategory->category_id]))
                          <a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>
                        @else
                          @if($testSubCategory->price > 0)
                            <a href="{{ url('purchaseTestSubCategory')}}/{{$testSubCategory->id}}" class="btn btn-primary" title="Pay Now" style="min-width: 100px;">Pay Now</a>
                          @else
                            <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>
                          @endif
                        @endif
                      @else
                        @if($testSubCategory->price > 0)
                          <a class="btn btn-primary" title="Pay Now" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>
                        @else
                          <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>
                        @endif
                      @endif
                    </div>
                    <div class="vchip_product_content">
                      <p class="mrgn_20_top"><a href="{{url('getTest')}}/{{ $testSubCategory->id }}" class="btn-link">Start Test <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                      </p>
                    </div>
                  </div>
              </div>
            @endforeach
          @endif
          @if(count($clientPurchasedSubCat) > 0)
            @foreach($clientPurchasedSubCat as $purchasedSubCategory)
              <div class="col-lg-6 col-md-6 col-sm-6 small-img">
                  <div class="vchip_product_itm text-left">
                    <a href="{{url('getTest')}}/{{ $purchasedSubCategory['sub_category_id'] }}" class="btn-link">
                      <figure title="{{$purchasedSubCategory['sub_category']}}">
                        @if(!empty($purchasedSubCategory['client_image']))
                          <img src="{{ asset($purchasedSubCategory['client_image']) }}" alt="exam" class="img-responsive " />
                        @else
                          <img src="{{ asset($purchasedSubCategory['image_path']) }}" alt="exam" class="img-responsive " />
                        @endif
                      </figure>
                      <ul class="vchip_categories list-inline">
                        <li>{{$purchasedSubCategory['sub_category']}}</li>
                      </ul>
                    </a>
                    <div class="categoery" style="padding-left: 18px;">
                      <span style="color: #e91e63;">Price: {{ $purchasedSubCategory['client_user_price']}} Rs.</span>
                      @if(is_object(Auth::guard('clientuser')->user()))
                        @if( isset($purchasedSubCategories[$purchasedSubCategory['category_id']]) && true == in_array($purchasedSubCategory['sub_category_id'], $purchasedSubCategories[$purchasedSubCategory['category_id']]))
                          <a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>
                        @else
                          @if($purchasedSubCategory['client_user_price'] > 0)
                            <a href="{{ url('purchaseTestSubCategory')}}/{{$purchasedSubCategory['sub_category_id']}}" class="btn btn-primary" title="Pay Now" style="min-width: 100px;">Pay Now</a>
                          @else
                            <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>
                          @endif
                        @endif
                      @else
                        @if($purchasedSubCategory['client_user_price'] > 0)
                          <a class="btn btn-primary" title="Pay Now" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>
                        @else
                          <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>
                        @endif
                      @endif
                    </div>
                    <div class="vchip_product_content">
                      <p class="mrgn_20_top"><a href="{{url('getTest')}}/{{ $purchasedSubCategory['sub_category_id'] }}" class="btn-link">Start Test <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                      </p>
                    </div>
                  </div>
              </div>
            @endforeach
          @endif
          @if(0 > count($testSubCategories) && 0 > count($clientPurchasedSubCategories))
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
    function checkLogin(){
      $('#loginUserModel').modal();
      return false;
    }
      function showSubCategories(ele){
        var id = parseInt($(ele).val());
        var userId = parseInt(document.getElementById('user_id').value);
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
                $.each(msg['sub_categories'], function(idx, obj) {
                  var mainDiv = document.createElement('div');
                  mainDiv.className = 'col-lg-6 col-md-6 col-sm-6 small-img';

                  var productDiv = document.createElement('div');
                  productDiv.className = "vchip_product_itm text-left";

                  var ancDiv = document.createElement('a');
                  contentUrl = "{{url('getTest')}}/"+obj.id;
                  ancDiv.className = 'btn-link';
                  ancDiv.setAttribute('href', contentUrl);

                  var imageDiv = document.createElement('figure');
                  imageUrl = "{{ asset('')}}"+ obj.image_path;
                  imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" alt="test "/>';
                  ancDiv.appendChild(imageDiv);

                  var eleUl = document.createElement('ul');
                  eleUl.className="mrgn_5_top vchip_categories list-inline";
                  eleUl.innerHTML='<li>'+ obj.name +'</li>';
                  ancDiv.appendChild(eleUl);
                  productDiv.appendChild(ancDiv);

                  var priceDiv = document.createElement('div');
                  priceDiv.className = 'categoery';
                  priceDiv.setAttribute("style", "padding-left: 18px;");
                  priceDiv.innerHTML = '<span style="color: #e91e63;">Price: '+ obj.price +' Rs.</span>';
                  if(userId > 0){
                    if(msg['purchasedSubCategories'][obj.category_id] && msg['purchasedSubCategories'][obj.category_id][obj.id]){
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>';
                    } else {
                      if(obj.price > 0){
                        url = "{{ url('purchaseTestSubCategory')}}/"+obj.id;
                        priceDiv.innerHTML += '<a href="'+ url +'" class="btn btn-primary" title="Pay Now" style="min-width: 100px;">Pay Now</a>';
                      } else {
                        priceDiv.innerHTML += ' <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                      }
                    }
                  } else {
                    if(obj.price > 0){
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Pay Now" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>';
                    } else {
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                    }
                  }
                  productDiv.appendChild(priceDiv);

                  var contentDiv = document.createElement('div');
                  contentDiv.className ='vchip_product_content';
                  contentDiv.innerHTML = '<p class="mrgn_20_top"><a href="'+contentUrl+'" class="btn-link">Start Test <i class="fa fa-angle-right"aria-hidden="true"></i></a></p>';
                  productDiv.appendChild(contentDiv);
                  mainDiv.appendChild(productDiv);
                  subcatDiv.appendChild(mainDiv);
                });
            }
            if( msg['clientPurchasedSubCategories']){
                $.each(msg['clientPurchasedSubCategories'], function(idx, obj) {
                  var mainDiv = document.createElement('div');
                  mainDiv.className = 'col-lg-6 col-md-6 col-sm-6 small-img';

                  var productDiv = document.createElement('div');
                  productDiv.className = "vchip_product_itm text-left";

                  var ancDiv = document.createElement('a');
                  contentUrl = "{{url('getTest')}}/"+obj['sub_category_id'];
                  ancDiv.className = 'btn-link';
                  ancDiv.setAttribute('href', contentUrl);

                  var imageDiv = document.createElement('figure');
                  if(obj['client_image']){
                    imageUrl = "{{ asset('')}}"+ obj['client_image'];
                  } else {
                    imageUrl = "{{ asset('')}}"+ obj['image_path'];
                  }
                  imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" alt="test "/>';
                  ancDiv.appendChild(imageDiv);

                  var eleUl = document.createElement('ul');
                  eleUl.className="mrgn_5_top vchip_categories list-inline";
                  eleUl.innerHTML='<li>'+ obj['sub_category'] +'</li>';
                  ancDiv.appendChild(eleUl);
                  productDiv.appendChild(ancDiv);

                  var priceDiv = document.createElement('div');
                  priceDiv.className = 'categoery';
                  priceDiv.setAttribute("style", "padding-left: 18px;");
                  priceDiv.innerHTML = '<span style="color: #e91e63;">Price: '+ obj['client_user_price'] +' Rs.</span>';
                  if(userId > 0){
                    if(msg['purchasedSubCategories'][obj['category_id']] && msg['purchasedSubCategories'][obj['category_id']][obj['sub_category_id']]){
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>';
                    } else {
                      if(obj['client_user_price'] > 0){
                        url = "{{ url('purchaseTestSubCategory')}}/"+obj['sub_category_id'];
                        priceDiv.innerHTML += '<a href="'+ url +'" class="btn btn-primary" title="Pay Now" style="min-width: 100px;">Pay Now</a>';
                      } else {
                        priceDiv.innerHTML += ' <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                      }
                    }
                  } else {
                    if(obj['client_user_price'] > 0){
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Pay Now" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>';
                    } else {
                      priceDiv.innerHTML += ' <a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                    }
                  }
                  productDiv.appendChild(priceDiv);

                  var contentDiv = document.createElement('div');
                  contentDiv.className ='vchip_product_content';
                  contentDiv.innerHTML = '<p class="mrgn_20_top"><a href="'+contentUrl+'" class="btn-link">Start Test <i class="fa fa-angle-right"aria-hidden="true"></i></a></p>';
                  productDiv.appendChild(contentDiv);
                  mainDiv.appendChild(productDiv);
                  subcatDiv.appendChild(mainDiv);
                });
            }
            if(0 > msg['sub_categories'].length && 0 > msg['clientPurchasedSubCategories'].length){
              subcatDiv.innerHTML = 'No sub categories are available.';
            }
          });
        }
      }
    </script>
@stop