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
          @if(is_object(Auth::guard('clientuser')->user()))
            <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User">
            @if(!empty(Auth::guard('clientuser')->user()->photo))
              <img src="{{ asset(Auth::guard('clientuser')->user()->photo) }}" class="img-circle user-profile" alt="user name" aria-haspopup="true" aria-expanded="true"/>&nbsp;
            @else
              <img src="{{ asset('images/user1.png') }}" class="img-circle user-profile" alt="user name" aria-haspopup="true" aria-expanded="true"/>&nbsp;
            @endif
            @if(Auth::guard('clientuser')->user()->userNotificationCount() > 0)
            <b style="color: red;">
              {{Auth::guard('clientuser')->user()->userNotificationCount()}}
            </b>
            @endif
            </a>
            <ul class="dropdown-menu user-dropdown ">
              <li>
                <a href="{{ url('profile')}}" title="Dashbord">
                  @if(!empty(Auth::guard('clientuser')->user()->photo))
                    <img src="{{ asset(Auth::guard('clientuser')->user()->photo) }}" class="img-circle user-profile1" alt="user name" aria-haspopup="true" aria-expanded="true"/>&nbsp;
                  @else
                    <img src="{{ asset('images/user1.png') }}" class="img-circle user-profile1" alt="user name" aria-haspopup="true" aria-expanded="true"/>&nbsp;
                  @endif
                  {{Auth::guard('clientuser')->user()->name}}
                </a>
              </li>
              <li role="separator" class="divider"></li>
              <li>
                <a href="{{ url('myNotifications')}}" data-toggle="tooltip" title="My Notifications"><i class="fa fa-star" aria-hidden="true"></i> My Notifications : <b style="color: red;">{{Auth::guard('clientuser')->user()->userNotificationCount()}}</b></a>
              </li>
              <li>
                <a href="{{ url('clientMessages')}}" data-toggle="tooltip" title="Admin Messages"><i class="fa fa-star" aria-hidden="true"></i> Admin Messages : <b style="color: red;">{{Auth::guard('clientuser')->user()->adminNotificationCount()}}</b></a>
              </li>
              <li role="separator" class="divider"></li>
              <li><a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                </form>
              </li>
            </ul>
            @else
              <a href="#" class="dropdown-toggle pull-right user_menu" data-toggle="dropdown" role="button" aria-expanded="false" title="User"><img src="{{ asset('images/user1.png') }}" class="img-circle user-profile" alt="user name" aria-haspopup="true" aria-expanded="true"/>
              </a>
              <ul class="dropdown-menu" role="menu">
                <div class="navbar-content">
                  <li title="Login">
                    <a href="{{ url('/')}}"><i class="fa fa-tachometer" aria-hidden="true" ></i> Login</a>
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
          <li class="" title="Online Course">
            <a href="{{ url('online-courses')}}"> Online Course </a>
          </li>
          <li class="dropdown" title="Online Tests">
            <a href="{{ url('online-tests')}}"> Online Tests </a>
            </a>
          </li>
        </ul>
   </div>
 </div>
</nav>