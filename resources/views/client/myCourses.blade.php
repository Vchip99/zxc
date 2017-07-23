@extends('client.dashboard')
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
      <div class="col-sm-4">
        <select id="category" class="form-control" name="category" onChange="selectSubcategory(this);">
          <option value="">Select Category ...</option>
          @if(count($categories) > 0)
            @foreach($categories as $category)
              <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
          @endif
        </select>
      </div>
      <div class="col-sm-4">
        <select id="subcategory" class="form-control" name="subcategory" onChange="selectCourses(this);">
          <option value="">Select Sub Category ...</option>
        </select>
      </div>
    </div>
    <div class="row " id="addCourses">
    	@if(count($courses) > 0)
        @foreach($courses as $course)
          <div class="col-lg-4 col-md-4 col-sm-6 mrgn_30_top_btm">
            <div class="v_courses mrgn_10_top_btm ">
              <a href="course-details.html">
                <img class="img-responsive " src="{{ asset('images/courses/course.jpg') }}" alt="course">
                <span class="topleft">Certified</span>
              </a>
              <div class="courses-text ">
                 <h4 class="v_h4_subtitle border_bottom h4_box"><a href="{{ url('courseDetails')}}/{{$course->id}}"">{{$course->name}}</a> </h4>
                 <span class="v_cost mrgn_20_top_btm">Free</span>
                 <span class="v_subcat mrgn_20_top_btm "><a>{{ $course->subCategory}}<i class="icon-speech-bubble"></i></a></span>
                  <ul class="vchip_categories list-inline">
                    <li>Number of Lessons :</li>
                    <li>Duration of Course :</li></br>
                    <li>Auther : {{$course->author}}</li>
                  </ul>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>