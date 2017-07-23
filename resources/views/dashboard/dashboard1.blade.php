@extends('layouts.master')
@section('header-css')
	@include('layouts.home-css')
	@yield('mytest_header')
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
          <img src="{{asset('images/study.jpg')}}" alt="Background" style="vertical-align:top; background-attachment:fixed" alt="vchip courses" />
        </figure>
      </div>
      <div class="vchip-background-content">
          <h2>Digital Education</h2>
        </div>
    </div>
  </section>
<section class="v_container" style="margin-bottom: 10px;">
	<div class="container-fluid">
		<div class="row content">
			<div class="col-sm-3 sidenav  v_bg_grey">
				<ul class="nav nav-pills nav-stacked">
					<li class="dropdown" id="onlineCourse"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Online Courses <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ url('myCourses')}}" ><span> My Online Courses </span></a></li>
							<li><a href="{{ url('myCertificate') }}" > My Certificate </a></li>
							<li><a href="{{ url('courses')}}" > More Courses </a></li>
						</ul>
					</li>
					<li class="dropdown" id="onlineTest"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Online Test <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="{{ url('myTest')}}" > My Test </a></li>
							<li><a href="{{ url('online-tests')}}"> More Test </a></li>
						</ul>
					</li>
					<li class="dropdown" id="liveCourse"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Live Courses <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="{{ url('myLiveCourses')}}" > My Live Courses </a></li>
							<li><a href="{{ url('liveCourse')}}" > More Courses </a></li>
						</ul>
					</li>
					<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> My Webinar <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="#" > My Webinar </a></li>
							<li><a href="{{ url('webinar') }}" > More Webinar </a></li>
						</ul>
					</li>
					<li class="dropdown" id="documents"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Documents <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="{{ url('myDocuments') }}" > Read Articles </a></li>
							<li><a href="{{ url('myFavouriteArticles') }}" > Favourite Articles </a></li>
							<li><a href="{{ url('documents') }}" > More Articles </a></li>
						</ul>
					</li>
					<li class="dropdown" id="vkits"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Vkit <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="{{ url('myVkits')}}" > Favourite Projects </a></li>
							<li><a href="{{ url('vkits') }}" > More Projects </a></li>
						</ul>
					</li>
					<li class="dropdown" id="discussion"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Discussion <span class="caret"></span></a>
						<ul class="dropdown-menu " role="menu">
							<li><a href="{{ url('myQuestions')}}" > My Questions </a></li>
							<li><a href="{{ url('myReplies') }}" > My Replies </a></li>
							<li><a href="{{ url('discussion') }}" > More Discussion </a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="col-sm-9">
				@yield('dashboard_content')
			</div>
		</div>
	</div>
</section>

@stop
@section('footer')
@include('footer.footer')
@stop