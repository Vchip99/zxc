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
  @include('client.front.header_menu')
  <section id="vchip-background" class="mrgn_60_btm">
    <div class="vchip-background-single">
      <div class="vchip-background-img">
        <figure>
          <img src="{{asset('images/course.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
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
      <div class="col-sm-3 ">
        <h4 class="v_h4_subtitle"> Sorted By</h4>
        <div class="mrgn_20_top_btm" >
          <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" required title="Category">
            <option value="0">Select Category ...</option>
            @if(count($courseCategories) > 0)
              @if(Auth::guard('clientuser')->user())
                @foreach($courseCategories as $courseCategory)
                  @if(in_array($courseCategory->id, $userCourseCategoryIds))
                    <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                  @endif
                @endforeach
              @else
                @foreach($courseCategories as $courseCategory)
                  <option value="{{$courseCategory->id}}">{{$courseCategory->name}}</option>
                @endforeach
              @endif
            @endif
          </select>
        </div>
        <div class="dropdown mrgn_20_top_btm" id="subcat">
          <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourses(this);" required title="Sub Category">
              <option value="">Select Sub Category ...</option>
            </select>
        </div>
      </div>
    <div class="col-sm-9 ">
        <div class="row info" id="addCourses">
          @if(count($courses) > 0)
            @if(Auth::guard('clientuser')->user())
              @foreach($courses as $course)
                @if(in_array($course->client_institute_course_id, $userCoursePermissionIds))
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 slideanim">
                  <div class="course-box">
                    <a class="img-course-box" href="{{ url('courseDetails')}}/{{$course->id}}" title="{{$course->name}}">
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
                       <div class="categoery" title="{{$course->category}}">
                         <a  href="{{ url('courseDetails')}}/{{$course->id}}"> {{$course->category}}</a>
                       </div>
                       <br/>
                      <p class="block-with-text">
                        {{$course->description}}
                        <a type="button" class="show " data-show="{{$course->id}}">Read More</a>
                      </p>
                      <div class="corse-detail" id="corse-detail-{{$course->id}}" >
                          <div class="corse-detail-heder">
                            <span class="card-title"><b>{{$course->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="{{$course->id}}"><span aria-hidden="true">×</span></button>
                          </div><br/>
                            <p>{{$course->description}}</p>
                            <div class="text-center corse-detail-footer" >
                              <a href="{{ url('courseDetails')}}/{{$course->id}}" class="btn btn-primary btn-default" > Start Course</a>
                            </div>
                        </div>
                      </div>
                      <div class="course-auther">
                        <a href="{{ url('courseDetails')}}/{{$course->id}}"><i class="fa fa-long-arrow-right block-with-text" aria-hidden="true" title="{{$course->author}}"> {{$course->author}}</i>
                        </a>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            @else
              @foreach($courses as $course)
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6 slideanim">
                  <div class="course-box">
                    <a class="img-course-box" href="{{ url('courseDetails')}}/{{$course->id}}" title="{{$course->name}}">
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
                       <div class="categoery" title="{{$course->category}}">
                         <a  href="{{ url('courseDetails')}}/{{$course->id}}"> {{$course->category}}</a>
                       </div>
                       <br/>
                      <p class="block-with-text">
                        {{$course->description}}
                        <a type="button" class="show " data-show="{{$course->id}}">Read More</a>
                      </p>
                      <div class="corse-detail" id="corse-detail-{{$course->id}}" >
                          <div class="corse-detail-heder">
                            <span class="card-title"><b>{{$course->name}}</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="{{$course->id}}"><span aria-hidden="true">×</span></button>
                          </div><br/>
                            <p>{{$course->description}}</p>
                            <div class="text-center corse-detail-footer" >
                              <a href="{{ url('courseDetails')}}/{{$course->id}}" class="btn btn-primary btn-default" > Start Course</a>
                            </div>
                        </div>
                      </div>
                      <div class="course-auther">
                        <a href="{{ url('courseDetails')}}/{{$course->id}}"><i class="fa fa-long-arrow-right" aria-hidden="true"> {{$course->author}}</i>
                        </a>
                      </div>
                    </div>
                  </div>
              @endforeach
            @endif
          @else
            No courses are available.
          @endif
          </div>
        </div>
      </div>
  </div>
</section>
<section id="education" class="v_container" >
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center mrgn-60-top"">
        <h2 class="v_h2_title">Education at V-edu</h2>
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
              <li>University at Home</li>
            </ul>
            <p class="">You can learn a you want at your home.</p>
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
            <p class="">After successfully completion of certificate course, you will get certificate that enhance level of your resumes and accelerate your career.</p>
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
            <p class="">A graduate and master level courses use to get mastery in that field. </p>
          </div>
        </div>
      </div>
    </div>
</section>

@stop
@section('footer')
	@include('footer.client-footer')
  <script type="text/javascript" src="{{ asset('js/togleForFilterBy.js')}}"></script>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
  <script type="text/javascript">
  function getCourseSubCategories(id){
    if( 0 < id ){
      $.ajax({
          method: "POST",
          url: "{{url('getOnlineSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.innerHTML = 'Select Sub Category ...';
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

  function renderCourse(msg){
    divCourses = document.getElementById('addCourses');
    divCourses.innerHTML = '';
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
           courseContent += '<div class="categoery"><a  href="'+ url +'">'+ obj.category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

          courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.description +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" > Start Course</a></div></div>';

          thirdDiv.innerHTML = courseContent;
          secondDiv.appendChild(thirdDiv);

          var authorDiv = document.createElement('div');
          authorDiv.className = "course-auther";
          authorDiv.innerHTML = '<a href="'+ url +'"><i class="fa fa-long-arrow-right" aria-hidden="true">'+ obj.author +'</i></a>';
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
      divCourses.innerHTML = 'No courses are available.';
    }
  }

  function selectCourses(ele){
    var subcatId = parseInt($(ele).val());
    var catId = parseInt(document.getElementById('category').value);
    if(catId > 0 && subcatId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getOnlineCourseByCatIdBySubCatId')}}",
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

  $(".toggle").slideUp();
  $(".trigger").click(function(){
    $(this).next(".toggle").slideToggle("slow");
  });
  </script>
@stop