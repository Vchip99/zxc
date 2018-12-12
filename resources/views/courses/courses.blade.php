@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
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
          <img src="{{asset('images/course.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Courses" />
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
      <div class="col-sm-3 hidden-div">
        <h4 class="v_h4_subtitle"> Sort By</h4>
        <div class="mrgn_20_top_btm" >
          <select id="category" class="form-control" name="category" data-toggle="tooltip" title="Category" onChange="selectSubcategory(this);" required>
            <option value="0">Select Category</option>
            @if(count($courseCategories) > 0)
              @foreach($courseCategories as $courseCategory)
                <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
              @endforeach
            @endif
          </select>
        </div>
        <div class="dropdown mrgn_20_top_btm" id="subcat">
          <select id="subcategory" class="form-control" name="subcategory" data-toggle="tooltip" title="Sub Category" onChange="selectCourses(this);" required>
              <option value="">Select Sub Category</option>
            </select>
        </div>
        <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
        <div class="panel"></div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Difficulty"> Difficulty</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="difficulty" onclick="searchCourse();"> Beginner</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="2" data-filter="difficulty" onclick="searchCourse();">Intermediate</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="3" data-filter="difficulty" onclick="searchCourse();"> Advanced</label>
          </div>
        </div>
        <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others"> Others</p>
        <div class="panel">
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="certified" onclick="searchCourse();">Certified</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="startingsoon" onclick="searchCourse();">Starting soon</label>
          </div>
          <div class="checkbox">
            <label><input class="search" type="checkbox" value="1" data-filter="latest" onclick="searchCourse();">Latest</label>
          </div>
        </div>
      </div>
      <div class="col-sm-9 col-sm-push-3 data ">
        <div class="row info" id="addCourses">
          @if(count($courses) > 0)
            @foreach($courses as $course)
              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                <div class="course-box">
                  <a class="img-course-box" href="{{ url('courseDetails')}}/{{$course->id}}">
                    @if(!empty($course->image_path))
                      <img class="img-responsive " src="{{ asset($course->image_path) }}" alt="course">
                    @else
                      <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="course">
                    @endif
                  </a>
                  <div class="topleft">@if( 1 == $course->certified )Certified @else Non Certified @endif</div>
                  <div class="topright">{{($course->price > 0)? 'Paid' : 'Free' }}</div>
                  <div class="course-box-content" >
                    <h4 class="course-box-title " title="{{$course->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a href="{{ url('courseDetails')}}/{{$course->id}}">{{$course->name}}</a></p></h4>
                    <div class="categoery">
                      <a  href="{{ url('courseDetails')}}/{{$course->id}}" data-toggle="tooltip" title="{{$course->category}}"> {{$course->category}}</a>
                    </div>
                    <br/>
                    <p class="block-with-text">
                      {{$course->description}}
                      <a type="button" class="show " data-show={{$course->id}}>Read More</a>
                    </p>
                    <div class="corse-detail" id="corse-detail-{{$course->id}}">
                      <div class="corse-detail-heder">
                        <span class="card-title"><b>{{$course->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="{{$course->id}}"><span aria-hidden="true">×</span></button>
                      </div><br/>
                        <p>{{$course->description}}</p>
                        <div class="text-center corse-detail-footer" >
                          <a href="{{ url('courseDetails')}}/{{$course->id}}" class="btn btn-primary btn-default" > Start Course</a>
                        </div>
                    </div>
                  </div>
                  <div class="row text-center">
                    <div style="display: inline-block;">
                      @if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif
                    </div>
                    <div style="display: inline-block;">
                      <input id="rating_input{{$course->id}}" name="input-{{$course->id}}" class="rating rating-loading" value="@if(isset($reviewData[$course->id])) {{$reviewData[$course->id]['avg']}} @else 0 @endif" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>
                    </div>
                    <div style="display: inline-block;">
                      <a data-toggle="modal" data-target="#review-model-{{$course->id}}">
                        @if(isset($reviewData[$course->id]))
                          {{count($reviewData[$course->id]['rating'])}} <i class="fa fa-group"></i>
                        @else
                          0 <i class="fa fa-group"></i>
                        @endif
                      </a>
                    </div>
                  </div>
                  <div class="course-auther text-center">
                    @if(is_object(Auth::user()))
                      @if(in_array($course->id, $userPurchasedCourses))
                          <a class="btn btn-sm btn-primary pay-width"> @if($course->price > 0) Paid @else Free @endif</a>
                      @elseif($course->price > 0)
                        <a data-course_id="{{$course->id}}" class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="purchaseCourse(this);">Pay Price: {{$course->price}} Rs.</a>
                        <form id="purchaseCourse_{{$course->id}}" method="POST" action="{{ url('purchaseCourse')}}">
                          {{ csrf_field() }}
                          <input type="hidden" name="course_id" value="{{$course->id}}">
                          <input type="hidden" name="course_category_id" value="{{$course->course_category_id}}">
                          <input type="hidden" name="course_sub_category_id" value="{{$course->course_sub_category_id}}">
                        </form>
                      @else
                        <a class="btn btn-sm btn-primary pay-width" >Free</a>
                      @endif
                    @else
                      @if($course->price > 0)
                        <a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="checkLogin();">Pay Price: {{$course->price}} Rs.</a>
                      @else
                        <a class="btn btn-sm btn-primary pay-width">Free</a>
                      @endif
                    @endif
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
                            {{$userNames[$userId]}}:
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
            @endforeach
            @else
              No courses are available.
            @endif
        </div>
        <div style="" id="pagination">
          {{ $courses->links() }}
        </div>
        <br/>
      </div>
      <div class="col-sm-3 col-sm-pull-9">
        <div class="hidden-div1">
           <h4 class="v_h4_subtitle"> Sort By</h4>
          <div class="mrgn_20_top_btm" >

            <select id="categoryNew" class="form-control" name="category" data-toggle="tooltip" title="Category" onChange="selectSubcategoryNew(this);" required>
              <option value="0">Select Category</option>
              @if(count($courseCategories) > 0)
                @foreach($courseCategories as $courseCategory)
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="dropdown mrgn_20_top_btm" id="subcat">
            <select id="subcategoryNew" class="form-control" name="subcategory" data-toggle="tooltip" title="Sub Category" onChange="selectCoursesNew(this);" required>
                <option value="">Select Sub Category</option>
              </select>
          </div>
          <h4 class="v_h4_subtitle mrgn_20_top_btm"> Filter By</h4>
          <div class="panel"></div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Difficulty"> Difficulty</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="difficulty" onclick="searchCourse();"> Beginner</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="2" data-filter="difficulty" onclick="searchCourse();">Intermediate</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="3" data-filter="difficulty" onclick="searchCourse();"> Advanced</label>
            </div>
          </div>
          <p class="v_p_sm v_plus_minus_symbol mrgn_20_top_btm" data-toggle="tooltip" title="Others"> Others</p>
          <div class="panel">
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="certified" onclick="searchCourse();">Certified</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="startingsoon" onclick="searchCourse();">Starting soon</label>
            </div>
            <div class="checkbox">
              <label><input class="search" type="checkbox" value="1" data-filter="latest" onclick="searchCourse();">Latest</label>
            </div>
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
                    <img src="{{ asset('images/logo/ssgmce-logo.jpg') }}" alt="Mauli College of Engineering Shegaon"  class="img-responsive" />
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
<section id="education" class="v_container" >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top"">
        <h2 class="v_h2_title">Education at Vchip-edu</h2>
        <hr class="section-dash-dark"/>
        <h3 class="v_h3_title ">Earn a Professional Certificate, Nano degree course...</h3>
      </div>
    </div>
    <div class="row text-center mrgn_30_top ">
      <div class="col-md-4 col-sm-12 mrgn_40_top ">
        <div class="feature-center">
          <img src="{{ asset('images/courses/univercity-at-home.png')}}" width="100"
          height="100"
          class="img-responsive center-block" alt="University at Home"/>
          <ul class="vchip_categories list-inline">
            <li>
              University at Home
            </li>
          </ul>
            <p class="">You can learn as you want at your home.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-12 mrgn_40_top ">
        <div class="feature-center">
          <img src="{{ asset('images/courses/professional-certificate.png')}}"
          width="100" height="100"
          class="img-responsive center-block" alt="Professional Certificate"/>
          <ul class="vchip_categories list-inline">
            <li>Professional Certificate</li>
          </ul>
          <p class="">After successful completion of certificate course, you will get certificate that will enhance level of your resumes and accelerate your career.</p>
        </div>
      </div>
      <div class="col-md-4 col-sm-12 mrgn_40_top ">
        <div class="feature-center">
          <img src="{{ asset('images/courses/nano-degree-course.png')}}" width="100"
          height="100"
          class="img-responsive center-block" alt="Nano degree course"/>
          <ul class="vchip_categories list-inline">
            <li>Nano degree course</li>
          </ul>
          <p class="">Graduate and master level courses that helps to get mastery in that field. </p>
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
  <script src="{{ asset('js/star-rating.js') }}"></script>
  <script type="text/javascript">
  function getCourseSubCategories(id){
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getCourseSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }

  function selectSubcategory(ele){
    var id = parseInt($(ele).val());
    getCourseSubCategories(id);
  }

  function selectSubcategoryNew(ele){
    var id = parseInt($(ele).val());
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getCourseSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategoryNew');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category';
        select.appendChild(opt);
        if( 0 < msg.length){
          $.each(msg, function(idx, obj) {
              var opt = document.createElement('option');
              opt.value = obj.id;
              opt.innerHTML = obj.name;
              select.appendChild(opt);
          });
        }
      });
    }
  }
  function renderCourse(msg){
    var userId = parseInt(document.getElementById('user_id').value);
    divCourses = document.getElementById('addCourses');
    divCourses.innerHTML = '';
    document.getElementById('pagination').innerHTML = '';
    if(undefined !== msg['courses'] && 0 < msg['courses'].length) {
      $.each(msg['courses'], function(idx, obj) {
          var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-6";
          var secondDiv = document.createElement('div');
          secondDiv.className = "course-box";
          var url = "{{ url('courseDetails')}}/"+ obj.id;
          var anc = document.createElement('a');
          anc.className = 'img-course-box';
          anc.href = url;
          var img = document.createElement('img');
          img.className = "img-responsive";
          if(obj.image_path){
            img.src = "{{ asset('') }}" + obj.image_path;
          } else {
            img.src = "{{ asset('images/default_course_image.jpg') }}";
          }
          anc.appendChild(img);
          secondDiv.appendChild(anc);
          var topleftEle = document.createElement('div');
          topleftEle.className = "topleft";
          if(1 == obj.certified ){ certifiedVal = 'Certified';} else {certifiedVal='Non Certified'}
          topleftEle.innerHTML = certifiedVal;
          secondDiv.appendChild(topleftEle);
          var toprightEle = document.createElement('div');
          toprightEle.className = "topright";
          if( obj.price > 0 ){ price = 'Paid';} else { price='Free';}
          toprightEle.innerHTML = price;
          secondDiv.appendChild(toprightEle);

          var thirdDiv = document.createElement('div');
          thirdDiv.className = "course-box-content";

          var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a href="'+ url +'">'+ obj.name +'</a></p></h4>';
           courseContent += '<div class="categoery"><a  href="'+ url +'" title="'+obj.category+'">'+ obj.category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

          courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.description +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" > Start Course</a></div></div>';

          thirdDiv.innerHTML = courseContent;
          secondDiv.appendChild(thirdDiv);

          var ratingDiv = document.createElement('div');
          ratingDiv.className = "row text-center";
          if(msg['ratingData'][obj.id] && msg['ratingData'][obj.id]['avg']){
            ratingDiv.innerHTML = '<div style="display: inline-block;">'+msg['ratingData'][obj.id]['avg']+'</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="'+msg['ratingData'][obj.id]['avg']+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;"><a data-toggle="modal" data-target="#review-model-'+obj.id+'">'+Object.keys(msg['ratingData'][obj.id]['rating']).length+' <i class="fa fa-group"></i></a></div>';
          } else {
            ratingDiv.innerHTML = '<div style="display: inline-block;">0</div><div style="display: inline-block;"><input id="rating_input'+obj.id+'" name="input-" class="rating rating-loading" value="0" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly></div><div style="display: inline-block;"><a data-toggle="modal" data-target="#review-model-'+obj.id+'">0 <i class="fa fa-group"></i></a></div>';
          }
          secondDiv.appendChild(ratingDiv);

          var authorDiv = document.createElement('div');
          authorDiv.className = "course-auther text-center";
          if(false == isNaN(userId)){
            if(msg['userPurchasedCourses'] && msg['userPurchasedCourses'].length > 0 && true == msg['userPurchasedCourses'].indexOf(obj.id) > -1){
              authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Paid </a>';
            } else if( obj.price > 0 ){
              var purchaseCourseUrl = "{{ url('purchaseCourse')}}";
              var csrfField = '{{ csrf_field() }}';
              authorDiv.innerHTML = '<a data-course_id="'+obj.id+'" class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="purchaseCourse(this);">Pay Price: '+obj.price+' Rs.</a>';
              authorDiv.innerHTML +='<form id="purchaseCourse_'+obj.id+'" method="POST" action="'+purchaseCourseUrl+'">'+csrfField+'<input type="hidden" name="course_id" value="'+obj.id+'"><input type="hidden" name="course_category_id" value="'+obj.course_category_id+'"><input type="hidden" name="course_sub_category_id" value="'+obj.course_sub_category_id+'"></form>';
            } else {
              authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Free </a>';
            }
          } else {
            if( obj.price > 0 ){
              authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="checkLogin();">Pay Price: '+obj.price+' Rs.</a>';
            } else {
              authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Free </a>';
            }
          }
          secondDiv.appendChild(authorDiv);
          firstDiv.appendChild(secondDiv);
          divCourses.appendChild(firstDiv);

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
              reviewModelInnerHTML += msg['userNames'][userId] +':';
              reviewModelInnerHTML += '<input id="rating_input-'+obj.id+'-'+userId+'" name="input-'+obj.id+'" class="rating rating-loading" value="'+reviewData.rating+'" data-min="0" data-max="5" data-step="0.1" data-size="xs" data-show-clear="false" data-show-caption="false" readonly>'+reviewData.review+'<hr>';
            });
          } else {
            reviewModelInnerHTML += 'Please give ratings';
          }
          reviewModelInnerHTML += '</div></div></div></div></div>';
          reviewModel.innerHTML = reviewModelInnerHTML;
          divCourses.appendChild(reviewModel);

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
            ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="1"><input type="hidden" name="rating_id" value="'+msg['ratingData'][obj.id]['rating'][userId]['review_id']+'"><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
          } else {
            ratingModelInnerHTML += '<input type="text" name="review-text" class="form-control" value="">';
            ratingModelInnerHTML += '<br><input type="hidden" name="module_id" value="'+obj.id+'"><input type="hidden" name="module_type" value="1"><input type="hidden" name="rating_id" value=""><button type="submit" class="pull-right">Submit</button></div></form></div></div></div>';
          }

          ratingModel.innerHTML = ratingModelInnerHTML;
          divCourses.appendChild(ratingModel);

          var inputRating = $('input.rating');
          if(inputRating.length) {
            inputRating.removeClass('rating-loading').addClass('rating-loading').rating();
          }
      });
       $(function(){
          $('.show').on('click',function(){
            id = $(this).data('show')        ;
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
          $('.close').on('click',function(){
              id = $(this).data('close')
              $('[id ^=corse-detail-'+id).slideToggle('slow');
          });
      });
    } else {
      divCourses.innerHTML = 'No Result Found.';
    }
  }

  function selectCourses(ele){
    var subcatId = parseInt($(ele).val());
    var catId = parseInt(document.getElementById('category').value);
    if(catId > 0 && subcatId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getCourseByCatIdBySubCatId')}}",
        data: {catId:catId, subcatId:subcatId,rating:true}
      })
      .done(function( msg ) {
        renderCourse(msg);
        var searches = document.getElementsByClassName('search');
        $.each(searches, function(ind, obj){
          $(obj).attr('checked', false);
        });
      });
    }
  }

  function selectCoursesNew(ele){
    var subcatId = parseInt($(ele).val());
    var catId = parseInt(document.getElementById('categoryNew').value);
    if(catId > 0 && subcatId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getCourseByCatIdBySubCatId')}}",
        data: {catId:catId, subcatId:subcatId,rating:true}
      })
      .done(function( msg ) {
        renderCourse(msg);
        var searches = document.getElementsByClassName('search');
        $.each(searches, function(ind, obj){
          $(obj).attr('checked', false);
        });
      });
    }
  }

  function searchCourse(){
    var searches = document.getElementsByClassName('search');
    var arrDifficulty = [];
    var arrCertified = [];
    var arrFees = [];
    var arr = [];
    var startingsoon = 0;
    var latest = 0;
    $.each(searches, function(ind, obj){
      if(true == $(obj).is(':checked')){
        var filter = $(obj).data('filter');
        var filterVal = $(obj).val();
        if(false == (arr.indexOf(filter) > -1)){
          if('difficulty' == filter) {
            arrDifficulty.push(filterVal);
            arr.push(filterVal);
          }
          if('certified' == filter) {
            arrCertified.push(filterVal);
            arr.push(filterVal);
          }
          if('fees' == filter) {
            arrFees.push(filterVal);
            arr.push(filterVal);
          }
          if('startingsoon' == filter) {
            startingsoon = filterVal;
            arr.push(filterVal);
          }
          if('latest' == filter) {
            latest = filterVal;
            arr.push(filterVal);
          }
        }
      }
    });
    if(arr instanceof Array ){
      categoryId = document.getElementById('category').value;
      subcategoryId = document.getElementById('subcategory').value;
      var arrJson = {'difficulty' : arrDifficulty, 'certified' : arrCertified, 'fees' : arrFees, 'startingsoon' : startingsoon, 'latest' : latest, 'categoryId' : categoryId, 'subcategoryId' : subcategoryId,rating:true };
      $.ajax({
        method: "POST",
        url: "{{url('getOnlineCourseBySearchArray')}}",
        data: {arr:JSON.stringify(arrJson)}
      })
      .done(function( msg ) {
        renderCourse(msg);
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
  </script>
@stop