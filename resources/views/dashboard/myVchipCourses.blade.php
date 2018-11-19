@extends('dashboard.dashboard')
@section('dashboard_header')
  <link href="{{asset('css/sidemenuindex.css?ver=1.0')}}" rel="stylesheet"/>
  <link href="{{asset('css/v_courses.css?ver=1.0')}}" rel="stylesheet"/>
  <style type="text/css">
    .btn-primary{
      width: 120px;
    }
    .btn{
      border-radius: 2px !important;
    }
  </style>
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
      <a href="{{ url('college/'.Session::get('college_user_url').'/myVchipCourses')}}" class="btn btn-primary">Vchip Courses</a> &nbsp;
      <a href="{{ url('college/'.Session::get('college_user_url').'/myCollegeCourses')}}" class="btn btn-default">College Courses</a>&nbsp;
      <a class="btn btn-default" id="favourite" data-favourite="false" title="Favourite" onClick="myVchipFavouriteCourses(this);" style="border-radius: 2px;"> <i class="fa fa-star " aria-hidden="true"></i> </a>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-4 mrgn_10_btm">
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);">
          <option value="">Select Category</option>
          @if(count($categories) > 0)
            @foreach($categories as $category)
              <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
      <div class="col-sm-4">
        <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourses(this);">
          <option value="0">Select Sub Category</option>
        </select>
      </div>
    </div><br/>
    <div class="row" id="addCourses">
    	@if(count($courses) > 0)
        @foreach($courses as $course)
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="course-box">
              <a class="img-course-box" href="{{ url('college/'.Session::get('college_user_url').'/vchipCourseDetails')}}/{{$course->id}}" target="_blank">
                @if(!empty($course->image_path))
                  <img class="img-responsive " src="{{ asset($course->image_path) }}" alt="course">
                @else
                  <img class="img-responsive " src="{{ asset('images/default_course_image.jpg') }}" alt="course">
                @endif
              </a>
              <div class="topleft">@if( 1 == $course->certified )Certified @else Non Certified @endif</div>
              <div class="topright">{{($course->price > 0)? 'Paid' : 'Free' }}</div>
              <div class="course-box-content" >
                 <h4 class="course-box-title " title="{{$course->name}}" data-toggle="tooltip" data-placement="bottom"> <p class="block-with-text"><a href="{{ url('college/'.Session::get('college_user_url').'/vchipCourseDetails')}}/{{$course->id}}" target="_blank">{{$course->name}}</a></p></h4>
                 <div class="categoery">
                   <a  href="{{ url('college/'.Session::get('college_user_url').'/vchipCourseDetails')}}/{{$course->id}}" target="_blank"> {{$course->category}}</a>
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
                        <a href="{{ url('college/'.Session::get('college_user_url').'/vchipCourseDetails')}}/{{$course->id}}" class="btn btn-primary btn-default" target="_blank"> Start Course</a>
                      </div>
                  </div>
                </div>
                <div class="course-auther text-center">
                  @if(is_object(Auth::user()))
                    @if(in_array($course->id, $userPurchasedCourses))
                      <a class="btn btn-sm btn-primary pay-width">
                        @if($course->price > 0)
                          Paid
                        @else
                          Free
                        @endif
                      </a>
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
        @endforeach
      @else
        No courses are available.
      @endif
    </div>
  </div>
  <script type="text/javascript" src="{{ asset('js/read_info.js')}}"></script>
<script type="text/javascript">

  function getCourseSubCategories(id, userId){
    document.getElementById('subcategory').value = 0;
    document.getElementById('addCourses').innerHTML ='';

    if( 0 < id){
      $.ajax({
          method: "POST",
          url: "{{url('getCourseSubCategories')}}",
          data: {id:id}
      })
      .done(function( msg ) {
        select = document.getElementById('subcategory');
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '0';
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
    // var userId = parseInt(document.getElementById('user_id').value);
    getCourseSubCategories(id);
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
        var url = "{{ url('college/'.Session::get('college_user_url').'/vchipCourseDetails')}}/"+ obj.id;
        var anc = document.createElement('a');
        anc.className = 'img-course-box';
        anc.href = url;
        anc.setAttribute("target","_blank");
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

        var courseContent = '<h4 class="course-box-title" title="'+ obj.name +'" data-toggle="tooltip" data-placement="bottom"><p class="block-with-text"><a href="'+ url +'" target="_blank">'+ obj.name +'</a></p></h4>';
         courseContent += '<div class="categoery"><a href="'+ url +'" target="_blank">'+ obj.category +'</a></div><br/><p class="block-with-text">'+ obj.description+'<a type="button" class="show " data-show="'+ obj.id +'">Read More</a></p>';

        courseContent += '<div class="corse-detail" id="corse-detail-'+ obj.id +'"><div class="corse-detail-heder"><span class="card-title"><b>'+ obj.name +'</b></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close" data-close="'+ obj.id +'"><span aria-hidden="true">×</span></button></div></br/><p>'+ obj.description +'</p><div class="text-center corse-detail-footer" ><a href="'+ url +'" class="btn btn-primary btn-default" target="_blank"> Start Course</a></div></div>';

        thirdDiv.innerHTML = courseContent;
        secondDiv.appendChild(thirdDiv);

        var authorDiv = document.createElement('div');
        authorDiv.className = "course-auther text-center";
        if(msg['userPurchasedCourses'].length > 0 && true == msg['userPurchasedCourses'].indexOf(obj.id) > -1){
          if( obj.price > 0 ){
            authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Paid </a>';
          } else {
            authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Free </a>';
          }
        } else if( obj.price > 0 ){
          var purchaseCourseUrl = "{{ url('purchaseCourse')}}";
          var csrfField = '{{ csrf_field() }}';
          authorDiv.innerHTML = '<a data-course_id="'+obj.id+'" class="btn btn-sm btn-primary pay-width" style="cursor: pointer;" onClick="purchaseCourse(this);">Pay Price: '+obj.price+' Rs.</a>';
          authorDiv.innerHTML +='<form id="purchaseCourse_'+obj.id+'" method="POST" action="'+purchaseCourseUrl+'">'+csrfField+'<input type="hidden" name="course_id" value="'+obj.id+'"><input type="hidden" name="course_category_id" value="'+obj.course_category_id+'"><input type="hidden" name="course_sub_category_id" value="'+obj.course_sub_category_id+'"></form>';
        } else {
          authorDiv.innerHTML = '<a class="btn btn-sm btn-primary pay-width"> Free </a>';
        }

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
      var firstDiv = document.createElement('div');
      firstDiv.className = "col-lg-4 col-md-4 col-sm-6 col-xs-12";
      firstDiv.innerHTML = 'No Result !';
      divCourses.appendChild(firstDiv);
    }
  }

  function selectCourses(ele){
    var subcatId = parseInt($(ele).val());
    var catId = parseInt(document.getElementById('category').value);
    // var userId = parseInt(document.getElementById('user_id').value);
    if(catId > 0 && subcatId > 0){
      $.ajax({
        method: "POST",
        url: "{{url('getCourseByCatIdBySubCatId')}}",
        data: {catId:catId, subcatId:subcatId}
      })
      .done(function( msg ) {
        renderCourse(msg);
      });
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

  function myVchipFavouriteCourses(ele){
    if(false == $(ele).data('favourite')){
      $(ele).data('favourite',true);
      $(ele).prop('style','color: rgb(233, 30, 99);');
      $(ele).prop('title','All');
      $.ajax({
        method: "POST",
        url: "{{url('myVchipFavouriteCourses')}}"
      })
      .done(function( msg ) {
        renderCourse(msg);
      });
    } else {
      window.location.reload();
    }
  }
</script>
@stop