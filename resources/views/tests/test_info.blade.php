@extends('layouts.master')
@section('header-title')
  <title>Online Test Series for GATE, CAT, Aptitude |Vchip-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
   <link href="{{ asset('css/box.css')}}" rel="stylesheet"/>
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
    <div class="row">
      <div class="col-sm-3 hidden-div ">
        <h4 class="v_h4_subtitle"> Filter By</h4>
        <div class="dropdown mrgn_20_top_btm" id="cat">
          <select class="form-control" id="category_id" name="category_id" title="Category" onchange="showSubCategories(this);">
            <option>Select Category</option>
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
                  <div class="vchip_product_itm text-left">
                    <a href="{{url('getTest')}}/{{ $testSubCategory->id }}" class="btn-link" title="Start Test">
                      <figure title="{{$testSubCategory->name}}">
                          <img src="{{ asset($testSubCategory->image_path) }}" alt="exam" class="img-responsive " />
                      </figure>
                      <ul class="vchip_categories list-inline">
                        <li>{{$testSubCategory->name}}</li>
                      </ul>
                    </a>
                    <div class="categoery" style="padding-left: 18px;">
                      <span style="color: #e91e63;">Price: {{$testSubCategory->price}} Rs.</span>
                      @if(is_object(Auth::user()))
                        @if(true == in_array($testSubCategory->id, $purchasedSubCategories))
                          <a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>
                        @else
                          @if($testSubCategory->price > 0)
                            <a data-id="{{$testSubCategory->id}}" class="btn btn-primary" title="Pay Now" style="min-width: 100px;" onClick="purchaseSubCategory(this);">Pay Now</a>
                            <form id="purchaseSubCategory_{{$testSubCategory->id}}" method="POST" action="{{ url('purchaseTestSubCategory') }}">
                              {{ csrf_field() }}
                              <input type="hidden" name="category_id" value="{{$testSubCategory->test_category_id}}">
                              <input type="hidden" name="subcategory_id" value="{{$testSubCategory->id}}">
                            </form>
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
                    <div class="row text-center">
                      <a data-toggle="modal" data-target="#review-model-{{$testSubCategory->id}}" style="cursor: pointer;">
                        <div style="display: inline-block;">
                          @if(isset($reviewData[$testSubCategory->id])) {{$reviewData[$testSubCategory->id]['avg']}} @else 0 @endif
                        </div>
                        <div style="display: inline-block;">
                          <input name="input-{{$testSubCategory->id}}" class="rating rating-loading" value="@if(isset($reviewData[$testSubCategory->id])) {{$reviewData[$testSubCategory->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                        </div>
                        <div style="display: inline-block;">
                            @if(isset($reviewData[$testSubCategory->id]))
                              {{count($reviewData[$testSubCategory->id]['rating'])}} <i class="fa fa-group"></i>
                            @else
                              0 <i class="fa fa-group"></i>
                            @endif
                        </div>
                      </a>
                    </div>
                  </div>
              </div>
              <div id="review-model-{{$testSubCategory->id}}" class="modal fade" role="dialog">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      &nbsp;&nbsp;&nbsp;
                      <button class="close" data-dismiss="modal">×</button>
                      <div class="form-group row ">
                        <div  style="display: inline-block;">
                          @if(isset($reviewData[$testSubCategory->id])) {{$reviewData[$testSubCategory->id]['avg']}} @else 0 @endif
                        </div>
                        <div  style="display: inline-block;">
                          <input name="input-{{$testSubCategory->id}}" class="rating rating-loading" value="@if(isset($reviewData[$testSubCategory->id])) {{$reviewData[$testSubCategory->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                        </div>
                        <div  style="display: inline-block;">
                          @if(isset($reviewData[$testSubCategory->id]))
                            {{count($reviewData[$testSubCategory->id]['rating'])}} <i class="fa fa-group"></i>
                          @else
                            0 <i class="fa fa-group"></i>
                          @endif
                        </div>
                        @if(is_object(Auth::user()))
                          <button class="pull-right" data-toggle="modal" data-target="#rating-model-{{$testSubCategory->id}}">
                          @if(isset($reviewData[$testSubCategory->id]) && isset($reviewData[$testSubCategory->id]['rating'][Auth::user()->id]))
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
                        @if(isset($reviewData[$testSubCategory->id]))
                          @foreach($reviewData[$testSubCategory->id]['rating'] as $userId => $review)
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
                            <input id="rating_input-{{$testSubCategory->id}}-{{$userId}}" name="input-{{$testSubCategory->id}}" class="rating rating-loading" value="{{$review['rating']}}" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
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
              <div id="rating-model-{{$testSubCategory->id}}" class="modal fade" role="dialog">
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
                          @if(isset($reviewData[$testSubCategory->id]) && is_object(Auth::user()) && isset($reviewData[$testSubCategory->id]['rating'][Auth::user()->id]))
                            <input id="rating_input-{{$testSubCategory->id}}" name="input-{{$testSubCategory->id}}" class="rating rating-loading" value="{{$reviewData[$testSubCategory->id]['rating'][Auth::user()->id]['rating']}}" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                          @else
                            <input id="rating_input-{{$testSubCategory->id}}" name="input-{{$testSubCategory->id}}" class="rating rating-loading" value="0" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">
                          @endif
                          Review:<input type="text" name="review-text" class="form-control" value="@if(isset($reviewData[$testSubCategory->id])  && is_object(Auth::user()) && isset($reviewData[$testSubCategory->id]['rating'][Auth::user()->id])) {{trim($reviewData[$testSubCategory->id]['rating'][Auth::user()->id]['review'])}} @endif">
                          <br>
                          <input type="hidden" name="module_id" value="{{$testSubCategory->id}}">
                          <input type="hidden" name="module_type" value="2">
                          <input type="hidden" name="rating_id" value="@if(isset($reviewData[$testSubCategory->id]) && is_object(Auth::user()) && isset($reviewData[$testSubCategory->id]['rating'][Auth::user()->id])) {{$reviewData[$testSubCategory->id]['rating'][Auth::user()->id]['review_id']}} @endif">
                          <button type="submit" class="pull-right">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
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
              <option>Select Category</option>
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
    <script src="{{ asset('js/star-rating.js') }}"></script>
    <script type="text/javascript">
      function showSubCategories(ele){
        id = parseInt($(ele).val());
        if( 0 < id ){
          $.ajax({
            method: "POST",
            url: "{{url('getSubCategories')}}",
            data: {id:id,rating:true}
          })
          .done(function( msg ) {
            var userId = parseInt(document.getElementById('user_id').value);
            var subcatDiv = document.getElementById('testSubCategories');
            subcatDiv.innerHTML = '';
            if( 0 < msg['subcategories'].length){
              $.each(msg['subcategories'], function(idx, obj) {
                var mainDiv = document.createElement('div');
                mainDiv.className = 'col-lg-6 col-md-6 col-sm-6 small-img';

                var productDiv = document.createElement('div');
                productDiv.className = "vchip_product_itm text-left";

                var ancDiv = document.createElement('a');
                contentUrl = "{{url('getTest')}}/"+obj.id;
                ancDiv.className = 'btn-link';
                ancDiv.setAttribute('href', contentUrl);

                var figureDiv = document.createElement('figure');
                figureDiv.setAttribute('title', obj.name);
                var imageDiv = document.createElement('div');
                imageUrl = "{{asset('')}}"+ obj.image_path;
                imageDiv.innerHTML = '<img src="'+ imageUrl +'"class="img-responsive" width="800"height="400" alt="test "/>';
                figureDiv.appendChild(imageDiv);
                ancDiv.appendChild(figureDiv);

                var eleUl = document.createElement('ul');
                eleUl.className="mrgn_5_top vchip_categories list-inline";
                eleUl.innerHTML='<li>'+ obj.name +'</li>';
                ancDiv.appendChild(eleUl);
                productDiv.appendChild(ancDiv);

                var priceDiv = document.createElement('a');
                priceDiv.className = 'categoery';
                priceDiv.setAttribute('style', 'padding-left: 18px;');
                priceDiv.innerHTML = '';
                priceDiv.innerHTML += '<span style="color: #e91e63;">Price: '+ obj.price+' Rs.</span>';
                if(userId > 0){
                  if(msg['purchasedSubCategories'][obj.id]){
                    priceDiv.innerHTML += '<a class="btn btn-primary" title="Paid" style="min-width: 100px;">Paid</a>';
                  } else {
                    if(obj.price > 0){
                      var purchaseUrl = "{{url('purchaseTestSubCategory')}}";
                      var csrfField = '{{ csrf_field() }}';
                      priceDiv.innerHTML += '<a data-id="'+obj.id+'" onClick="purchaseSubCategory(this);" class="btn btn-primary" title="Pay Now" style="min-width: 100px;">Pay Now</a><form id="purchaseSubCategory_'+obj.id+'" method="POST" action="'+purchaseUrl+'">'+csrfField+'<input type="hidden" name="category_id" value="'+obj.test_category_id+'"><input type="hidden" name="subcategory_id" value="'+obj.id+'"></form>';
                    } else {
                      priceDiv.innerHTML += '<a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                    }
                  }
                } else {
                  if(obj.price > 0){
                    priceDiv.innerHTML += '<a class="btn btn-primary" title="Pay Now" style="min-width: 100px;"  onClick="checkLogin();">Pay Now</a>';
                  } else {
                    priceDiv.innerHTML += '<a class="btn btn-primary" title="Free" style="min-width: 100px;">Free</a>';
                  }
                }
                productDiv.appendChild(priceDiv);

                var ratingDiv = document.createElement('div');
                ratingDiv.className = "row text-center";
                if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
                  ratingDiv.innerHTML = '<a data-toggle="modal" data-target="#review-model-'+obj.id+'" style="cursor: pointer;"><div style="display: inline-block;">'+msg['ratingData'][obj.id]['avg']+'</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;">'+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></div></a>';
                } else {
                  ratingDiv.innerHTML = '<a data-toggle="modal" data-target="#review-model-'+obj.id+'" style="cursor: pointer;"><div style="display: inline-block;">0</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;"> 0 <i class="fa fa-group"></i></div></a>';
                }
                productDiv.appendChild(ratingDiv);
                mainDiv.appendChild(productDiv);
                subcatDiv.appendChild(mainDiv);

                var reviewModel = document.createElement('div');
                reviewModel.setAttribute('id','review-model-'+obj.id);
                reviewModel.setAttribute('class','modal fade');
                reviewModel.setAttribute('role','dialog');

                reviewModelInnerHTML = '';
                if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
                  reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><div  style="display: inline-block;">'+msg['ratingData'][obj.id]['avg']+'</div><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div  style="display: inline-block;"> '+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></div>';
                } else {
                  reviewModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">&nbsp;&nbsp;&nbsp;<button class="close" data-dismiss="modal">×</button><div class="form-group row "><div  style="display: inline-block;">0</div><div  style="display: inline-block;"><input name="input-'+obj.id+'" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div  style="display: inline-block;"> 0 <i class="fa fa-group"></i></div>';
                }
                if(userId > 0){
                  reviewModelInnerHTML += '<button class="pull-right" data-toggle="modal" data-target="#rating-model-'+obj.id+'">';
                  if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
                    reviewModelInnerHTML += 'Edit Rating';
                  } else {
                    reviewModelInnerHTML += 'Give Rating';
                  }
                  reviewModelInnerHTML += '</button>';
                } else {
                  reviewModelInnerHTML += '<button class="pull-right" onClick="checkLogin()">Give Rating</button>';
                }
                reviewModelInnerHTML += '</div></div>';

                reviewModelInnerHTML += '<div class="modal-body row">';
                if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating']){
                  $.each(msg['ratingData'][obj.id]['rating'], function(userId, reviewData) {
                    if('system' == msg['userNames'][userId]['image_exist']){
                      var userImagePath = "{{ asset('') }}"+msg['userNames'][userId]['photo'];
                      var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                    } else if('other' == obj.image_exist){
                      var userImagePath = msg['userNames'][userId]['photo'];
                      var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                    } else {
                      var userImagePath = "{{ asset('images/user1.png') }}";
                      var userImage = '<img class="img-circle" src="'+userImagePath+'" alt="User Image" />';
                    }
                    reviewModelInnerHTML += '<div class="user-block cmt-left-margin">'+userImage+'<span class="username">'+msg['userNames'][userId]['name']+'</span><span class="description">Shared publicly - '+reviewData.updated_at+'</span></div><br/>';
                    reviewModelInnerHTML += '<input id="rating_input-'+obj.id+'-'+userId+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
                  });
                } else {
                  reviewModelInnerHTML += 'Please give ratings';
                }
                reviewModelInnerHTML += '</div></div></div></div></div>';
                reviewModel.innerHTML = reviewModelInnerHTML;
                subcatDiv.appendChild(reviewModel);

                var ratingModel = document.createElement('div');
                ratingModel.setAttribute('id','rating-model-'+obj.id);
                ratingModel.setAttribute('class','modal fade');
                ratingModel.setAttribute('role','dialog');
                var ratingUrl = "{{ url('giveRating')}}";
                var csrfField = '{{ csrf_field() }}';
                ratingModelInnerHTML = '';
                ratingModelInnerHTML += '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button>Rate and Review</div><div class="modal-body row"><form action="'+ratingUrl+'" method="POST"><div class="form-group row ">'+csrfField;
                if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
                  ratingModelInnerHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['rating'][userId]['rating']+'" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
                } else {
                  ratingModelInnerHTML += '<input id="rating_input-'+obj.id+'" name="input-'+obj.id+'" class="rating rating-loading" value="" data-min="1" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false">Review:';
                }
                if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['rating'][userId]){
                  ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="'+msg['ratingData'][obj.id]['rating'][userId]['review']+'">';
                  ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="2"><input type="hidden" name="rating_id" value="'+msg['ratingData'][obj.id]['rating'][userId]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
                } else {
                  ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="">';
                  ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="2"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
                }

                ratingModel.innerHTML = ratingModelInnerHTML;
                subcatDiv.appendChild(ratingModel);
              });
              var inputRating = $('input.rating');
              if(inputRating.length) {
                inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
              }
            }
          });
        }
      }
      function purchaseSubCategory(ele){
        $.confirm({
          title: 'Confirmation',
          content: 'Do you want to purchase this sub category?',
          type: 'red',
          typeAnimated: true,
          buttons: {
            Ok: {
              text: 'Ok',
              btnClass: 'btn-red',
              action: function(){
                var subcategoryId = parseInt($(ele).data('id'));
                document.getElementById('purchaseSubCategory_'+subcategoryId).submit();
              }
            },
            Cancle: function () {
            }
          }
        });
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