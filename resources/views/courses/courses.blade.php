@extends('layouts.master')
@section('header-title')
  <title>Free Online Courses by Industrial Expert |V-edu</title>
@stop
@section('header-css')
  @include('layouts.home-css')
  <link href="{{ asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{ asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
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
                    <div class="course-auther ">
                      <a href="{{ url('courseDetails')}}/{{$course->id}}"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="{{$course->author}}"> {{$course->author}}</i>
                      </a>
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
            <h3 class="v_h3_title ">Earn a Professional Certificate, Nano degree course...</p>
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
                    University at Home</li></ul>
                    <p class="">You can learn as you want at your home.</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 mrgn_40_top ">
                  <div class="feature-center">
                    <img src="{{ asset('images/courses/professional-certificate.png')}}"
                    width="100" height="100"
                    class="img-responsive center-block" alt="Professional Certificate"/>
                    <ul class="vchip_categories list-inline">
                      <li>Professional Certificate</li></ul>
                      <p class="">After successful completion of certificate course, you will get certificate that will enhance level of your resumes and accelerate your career.</p>
                    </div>
                  </div>
                  <div class="col-md-4 col-sm-12 mrgn_40_top ">
                    <div class="feature-center">
                      <img src="{{ asset('images/courses/nano-degree-course.png')}}" width="100"
                      height="100"
                      class="img-responsive center-block" alt="Nano degree course"/>
                      <ul class="vchip_categories list-inline">
                        <li>Nano degree course</li></ul>
                        <p class="">Graduate and master level courses that helps to get mastery in that field. </p>
                      </div>
                    </div>

                  </div>
    <!-- /.End Services Row -->
  </div><!-- /.End Container -->
</section>

@stop
@section('footer')
	@include('footer.footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
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

          var authorDiv = document.createElement('div');
          authorDiv.className = "course-auther";
          authorDiv.innerHTML = '<a href="'+ url +'"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="'+ obj.author +'">'+ obj.author +'</i></a>';
          secondDiv.appendChild(authorDiv);
          firstDiv.appendChild(secondDiv);
          divCourses.appendChild(firstDiv);
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
        data: {catId:catId, subcatId:subcatId}
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
        data: {catId:catId, subcatId:subcatId}
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
      var arrJson = {'difficulty' : arrDifficulty, 'certified' : arrCertified, 'fees' : arrFees, 'startingsoon' : startingsoon, 'latest' : latest, 'categoryId' : categoryId, 'subcategoryId' : subcategoryId };
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

  function registerCourse(ele){
    var userId = parseInt(document.getElementById('user_id').value);
    var courseId = parseInt($(ele).data('course_id'));
    if( true == isNaN(userId)){
      alert('please login first and then register course.');
    } else {
      $.ajax({
        method: "POST",
        url: "{{url('registerCourse')}}",
        data: {user_id:userId, course_id:courseId}
      })
      .done(function( msg ) {
        var id = 'register-'+courseId;
        var favEle = document.getElementById(id);
        favEle.readOnly = true;
        favEle.innerHTML = 'Registered Course';
        favEle.removeAttribute('onclick');
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