<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<div class="mu-header-area">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 ">
							<div class="mu-header-top-left">
								<div class="mu-top-email">
									<i class="fa fa-envelope"></i>
									<span>info@vchiptech.com</span>
								</div>
								<div class="mu-top-phone">
									<i class="fa fa-phone"></i>
									<span>(+91) 7722-078597 </span>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 ">
							<div class="mu-header-top-right">
								<ul class="mu-top-social-nav">
									<li><a href="#"><span class="fa fa-facebook"></span></a></li>
									<li><a href="#"><span class="fa fa-twitter"></span></a></li>
									<li><a href="#"><span class="fa fa-google-plus"></span></a></li>
									<li><a href="#"><span class="fa fa-linkedin"></span></a></li>
									<li><a href="#"><span class="fa fa-youtube"></span></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
				Menu <i class="fa fa-bars"></i>
			</button>
			<a class="navbar-brand" href="{{ asset('/')}}"><i class="fa fa-university"></i><span>Vchip</span></a>
		</div>
		<div class="collapse navbar-collapse navbar-right navbar-main-collapse">
			<ul class="nav navbar-nav">
				<li class="hidden">
					<a href="#page-top"></a>
				</li>
				<li>
					<a class="page-scroll" href="{{ asset('/home')}}">Home</a>
				</li>
				<li class="dropdown" id="Course">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Course<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#"> Online course :</a></li>
						<li class="divider"></li>
						<li><a href="{{ url('courses')}}">Courses</a></li>
						<li><a href="{{ url('liveCourse')}}">Live course</a></li>
						<li><a href="webinar.html">Webinar</a></li>
					</ul>
				</li>
				<li class="dropdown" id="Test_series">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Test series<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{ url('online-courses') }}"> Online course :</a></li>
						<li class="divider"></li>
						@if(count($testCategories) > 0)
						@foreach($testCategories as $testCategory)
						<li>
							<a href="{{ url('/showTest') }}/{{ $testCategory->id }}">{{ $testCategory->name }}</a>
						</li>
						@endforeach
						@endif
					</ul>
				</li>
				<li>
					<a class="page-scroll" href="{{url('vkits')}}">V-Kits</a>
				</li>
				<li class="dropdown" id="Gadget">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Gadget<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#">V-Learn with fun</a></li>
						<li><a href="#">V-tab</a></li>
						<li><a href="#">V-pendrive</a></li>
					</ul>
				</li>
				<li>
					<a class="page-scroll" href="{{url('documents')}}">Documents</a>
				</li>
				<li class="dropdown" id="Discussion">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Discussion<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{url('discussion')}}">Discussion forum</a></li>
						<li><a href="#">Live video discussion</a></li>
						<li><a href="{{url('blog')}}">Blog</a></li>
					</ul>
				</li>
				@if(Auth::user())
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">
						Welcome
						{{ Auth::user()->name }}
						<span class="caret"></span>
					</a>
					<ul role="menu" class="dropdown-menu">
						<li> <a href="{{ url('/')}}">My Account</a> </li>
						<li class="divider"></li>
						<li style="background: #e67e22; color:#fff">
							<a href="{{ url('/logout') }}"
							onclick="event.preventDefault();
							document.getElementById('logout-form').submit();">
							Logout
						</a>
						<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</li>
				</ul>
			</li>
			@endif
		</ul>
	</div>
</div>
</nav>