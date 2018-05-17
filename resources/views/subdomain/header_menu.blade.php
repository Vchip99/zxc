<nav class="navbar navbar-default navbar-fixed-top shrink" role="navigation">
    <div class="container">
      <div class="pull-left">
        <a class="navbar-brand pull-left" href="{{ url('/')}}"><i class="fa fa-university"></i><span>Vchip</span></a>
      </div>
      <div class="navbar-header pull-right">
        <button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <div class="pull-right dropdown " >
          <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i><b class="caret"></b>
          </a>
          @if(Auth::guard('client')->user())
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li>
                  <a href="{{ url('dashboard')}}"><i class="fa fa-tachometer" aria-hidden="true"></i>Dashbord</a>
                </li>
                <li><a href=""><i class="fa fa-user" aria-hidden="true"></i>My Profile</a></li>
                <li><a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
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
          <li class=""><a href="{{ asset('/home')}}">HOME</a></li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Course <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li><a href="{{ url('online-courses')}}"> Online course </a></li>
              </div>
            </ul>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Test-Series <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
              <div class="navbar-content">
                <li><a class="" href="{{ url('online-tests') }}"> Online Tests </a></li>

             </div>
            </ul>
          </li>

        </ul>
   </div>
 </div>
</nav>