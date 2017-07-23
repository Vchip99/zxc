<nav class="navbar navbar-default navbar-fixed-top shrink" role="navigation">
    <div class="container">
      <div class="pull-left">
        <a class="navbar-brand pull-left" href="{{url('/')}}"><i class="fa fa-university"></i><span>V-edu</span></a>
      </div>
      <div class="navbar-header pull-right">
        <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <div class="pull-right dropdown " >
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User"><i class="fa fa-user" aria-hidden="true"></i><b class="caret"></b>
          </a>
          @if(Auth::user())
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li>
                <!-- @if(!empty(Auth::user()->subdomain))
                  <a href="{{Auth::user()->subdomain}}" target="_blank"><i class="fa fa-tachometer" aria-hidden="true"></i>Dashbord</a>
                @else -->
                  <a href="{{ url('profile')}}" data-toggle="tooltip" title="Dashbord"><i class="fa fa-tachometer" aria-hidden="true"></i>Dashbord</a>
                <!-- @endif -->
                </li>
                <!-- <li><a href=""><i class="fa fa-user" aria-hidden="true"></i>My Profile</a></li> -->
                <li><a href="{{ url('logout') }}" data-toggle="tooltip" title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
                  <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                </li>
              </div>
            </ul>
            @else
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                  <li>
                    <a href="{{ url('/')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Login</a>
                  </li>
                </div>
              </ul>
          @endif
          </div>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::user()))?Auth::user()->id: NULL }}">
        </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
          <li class="" data-toggle="tooltip" title="Home"><a href="{{ asset('/home')}}">HOME</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Course">Course <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li data-toggle="tooltip" title="Online Course"><a href="{{ url('courses')}}"> Online course </a></li>
                <li data-toggle="tooltip" title="Live Course"><a href="{{ url('liveCourse') }}"> Live course </a></li>
                <li data-toggle="tooltip" title="Webinar"><a href="{{ url('webinar')}}">Webinar</a></li>
              </div>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Test-Series">Test-Series <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li><a class="" href="{{ url('online-tests') }}" data-toggle="tooltip" title="Online Tests"> Online Tests </a></li>
                @if(count($testCategoriesWithQuestions) > 0)
                  @foreach($testCategoriesWithQuestions as $testCategory)
                    <li>
                      <a href="{{ url('/showTest') }}/{{ $testCategory->id }}" data-toggle="tooltip" title="{{ $testCategory->name }}">{{ $testCategory->name }}</a>
                    </li>
                  @endforeach
                @endif
             </div>
            </ul>
          </li>
          <li>
            <a class="page-scroll" href="{{url('vkits')}}" data-toggle="tooltip" title="V-Kits">V-Kits</a>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Solution">Solution <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li data-toggle="tooltip" title="V-Education"><a href="{{ url('vEducation')}}"> V-Education</a></li>
                <li data-toggle="tooltip" title="V-Connect"><a href="{{ url('vConnect')}}">V-Connect</a></li>
                <li data-toggle="tooltip" title="V-Pendrive"><a href="{{ url('vPendrive')}}">V-Pendrive</a></li>
                <li data-toggle="tooltip" title="V-Cloud"><a href="{{ url('vCloud')}}">V-Cloud</a></li>
              </div>
            </ul>
          </li>
          <li><a class="page-scroll" href="{{url('documents')}}" data-toggle="tooltip" title="Documents">Documents</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Discussion">Discussion <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li data-toggle="tooltip" title="Discussion forum"><a href="{{url('discussion')}}">Discussion forum</a></li>
                <li data-toggle="tooltip" title="Live video discussion"><a href="{{url('liveVideo')}}">Live video discussion</a></li>
                <li data-toggle="tooltip" title="Blog"><a href="{{url('blog')}}">Blog</a></li>
              </div>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="More">More <span class="caret"></span></a>
            <ul class="dropdown-menu " role="menu">
              <div class="navbar-content">
                <li data-toggle="tooltip" title="Career"><a href="{{url('career')}}">Career</a></li>
                <li data-toggle="tooltip" title="Our partners"><a href="{{url('ourpartner')}}">Our partners</a></li>
                <li data-toggle="tooltip" title="Contact Us"><a href="{{url('contactus')}}">Contact Us</a></li>
              </div>
            </ul>
          </li>
        </ul>
   </div>
 </div>
</nav>