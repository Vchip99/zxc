<nav class="navbar navbar-default navbar-fixed-top shrink" role="navigation">
    <div class="container">
      <div class="pull-left">
        <a class="navbar-brand pull-left" href="{{ url('/')}}"><i class="fa fa-university"></i></a>
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
          @if(Auth::guard('clientuser')->user())
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li>
                  <a href="{{ url('dashboard')}}" title="Dashbord"><i class="fa fa-tachometer" aria-hidden="true"></i>Dashbord</a>
                </li>
                <!-- <li><a href=""><i class="fa fa-user" aria-hidden="true"></i>My Profile</a></li> -->
                <li><a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
                  <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                </li>
              </div>
            </ul>
            @else
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                  <li title="Login">
                    <a href="{{ url('/')}}"><i class="fa fa-tachometer" aria-hidden="true" ></i>Login</a>
                  </li>
                </div>
              </ul>
          @endif
          </div>
          <input type="hidden" name="user_id" id="user_id" value="{{ (is_object(Auth::guard('clientuser')->user()))?Auth::guard('clientuser')->user()->id: NULL }}">
        </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
          <li class="" title="Main Site"><a href="{{ $subdomain->institute_url }}" target="_blank">Main Site</a></li>
          <li class="" title="HOME"><a href="{{ url('/')}}">HOME</a></li>
          @if(1 == $client->course_permission)
            <li class="" title="Online Course">
              <a href="{{ url('online-courses')}}"> Online Course </a>
            </li>
          @endif
          @if(1 == $client->test_permission)
            <li class="dropdown" title="Online Tests">
              <a href="{{ url('online-tests')}}"> Online Tests </a>
              </a>
            </li>
          @endif
        </ul>
   </div>
 </div>
</nav>