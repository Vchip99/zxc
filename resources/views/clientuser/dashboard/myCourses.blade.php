@extends('clientuser.dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
@stop
@section('module_title')
  <section class="content-header">
    <h1> My Online Courses </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Online Courses</li>
      <li class="active">My Online Courses</li>
    </ol>
  </section>
@stop
@section('dashboard_content')
  <div class="container">
    <div class="row">
      <div class="col-sm-4 mrgn_10_btm">
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);" title="Category">
          <option value="">Select Category ...</option>
          @if(count($categories) > 0)
            @foreach($categories as $category)
              <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
      <div class="col-sm-4">
        <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourses(this);" title="Sub Category">
          <option value="">Select Sub Category ...</option>
        </select>
      </div>
    </div>
    <br/>
    <div class="row " id="addCourses">
    	@if(count($courses) > 0)
        @foreach($courses as $course)
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 slideanim">
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
        @endforeach
      @else
        No courses are registered.
      @endif
    </div>
  </div>
    <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
<script type="text/javascript">

  function getCourseSubCategories(id, userId){
    if( 0 < id && 0 < userId){
      $.ajax({
          method: "POST",
          url: "{{url('getOnlineSubCategories')}}",
          data: {id:id, userId:userId}
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
    var userId = parseInt(document.getElementById('user_id').value);
    getCourseSubCategories(id, userId);
  }

  function renderCourse(msg){
    divCourses = document.getElementById('addCourses');
    divCourses.innerHTML = '';
    if(undefined !== msg['courses'] && 0 < msg['courses'].length) {
      $.each(msg['courses'], function(idx, obj) {
          var firstDiv = document.createElement('div');
          firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-12";
          var secondDiv = document.createElement('div');
          secondDiv.className = "course-box";
          var url = "{{ url('courseDetails')}}/"+ obj.id;
          var anc = document.createElement('a');
          anc.className = 'img-course-box';
          anc.href = url;
          anc.title = obj.name;
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
           courseContent += '<div class="categoery" title="'+obj.category+'"><a  href="'+ url +'">'+ obj.category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

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
    }
  }

  function selectCourses(ele){
    var subcatId = parseInt($(ele).val());
    var catId = parseInt(document.getElementById('category').value);
    var userId = parseInt(document.getElementById('user_id').value);
    if(catId > 0 && subcatId > 0 && userId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getRegisteredOnlineCourseByCatIdBySubCatId')}}",
        data: {catId:catId, subcatId:subcatId, userId:userId}
      })
      .done(function( msg ) {
        renderCourse(msg);
      });
    }
  }
</script>
@stop